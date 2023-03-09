<?php

namespace App\Controller\Core;

use App\Entity\Core\UserData;
use App\Entity\Core\UserEmail;
use App\Entity\User;
use App\Form\Core\User\NewUserType;
use App\Form\Core\User\PasswordResetType;
use App\Form\Core\User\UserDataType;
use App\Form\Core\User\UserEmailType;
use App\Form\Core\User\UserType;
use App\Repository\Core\LogsRepository;
use App\Repository\Core\SettingsRepository;
use App\Repository\Core\UserDataRepository;
use App\Repository\Core\UserEmailRepository;
use App\Repository\UserRepository;
use App\Service\EmailService;
use App\Service\FilesService;
use App\Service\HelperService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\File;

class UserController extends AbstractController
{
    private UserRepository $userRepository;
    private UserDataRepository $userDataRepository;
    private FilesService $filesService;
    private LogsRepository $logs;
    private HelperService $helper;
    private EmailService $email;
    private SettingsRepository $settings;
    private KernelInterface $appKernel;

    public function __construct(
        UserRepository $userRepository,
        UserDataRepository $userDataRepository,
        FilesService $filesService,
        LogsRepository $logsRepository,
        HelperService $helperService,
        EmailService $emailService,
        SettingsRepository $settingsRepository,
        KernelInterface $appKernel
    ) {
        $this->userRepository = $userRepository;
        $this->userDataRepository = $userDataRepository;
        $this->filesService = $filesService;
        $this->logs = $logsRepository;
        $this->helper = $helperService;
        $this->email = $emailService;
        $this->settings = $settingsRepository;
        $this->appKernel = $appKernel;
    }

    #[Route('/uzytkownicy', name: 'user_list')]
    public function list(): Response
    {
        return $this->render('core/user/list.html.twig', [
            'buttons' => [
                [
                    'button_class' => 'btn-success',
                    'button_icon' => 'ri-add-circle-fill',
                    'button_link' => 'user_add',
                    'button_title' => 'Dodaj użytkownika',
                ],
                [
                    'button_class' => 'btn-secondary',
                    'button_icon' => 'ri-home-2-fill',
                    'button_link' => 'home',
                    'button_title' => '',
                ],
            ],
            'breadcrumb' => [
                ['none' => 'Administracja'],
                ['none' => 'Użytkownicy'],
            ],
        ]);
    }

    #[Route('/uzytkownicy/userJsonList', name: 'user_json_list')]
    public function jsonList(UserRepository $userRepository): Response
    {
        $collection = $userRepository->findAll();
        $data = [];
        foreach ($collection as $user) {
            $roles = '';
            foreach ($user->getRoles() as $role) {
                switch ($role) {
                    case 'ROLE_ALLOWED_TO_SWITCH':
                        $roles .= '<div class="badge bg-success  p-1 px-3 text-white me-2">Przełączanie użytkowników</div>';
                        break;
                    case 'ROLE_SUPER_ADMIN':
                        $roles .= '<div class="badge bg-danger p-1 px-3 text-white me-2">Super Admin</div>';
                        break;
                    case 'ROLE_ADMIN':
                        $roles .= '<div class="badge bg-danger p-1 px-3 text-white me-2">Admin</div>';
                        break;
                    case 'ROLE_USER':
                        $roles .= '<div class="badge bg-info p-1 px-3 text-white me-2">Użytkownik</div>';
                        break;
                }
            }

            $dat['id'] = $user->getId();
            $dat['email'] = $user->getEmail();
            $dat['roles'] = $roles;
            // $dat['dateRegistered'] = $user->getDateRegister()->format('d-m-Y H:i');
            if (null != $user->getDateRegister()) {
                $dat['dateRegistered'] = $user->getDateRegister()->format('d-m-Y H:i');
            } else {
                $dat['dateRegistered'] = 'Brak daty rejestracji';
            }

            $dat['actions'] = '';

            if ('true' == $user->isActived()) {
                $dat['status'] = '<div class="badge bg-success p-1 px-3 text-white">Aktywny</div>';
            } else {
                $dat['status'] = '<div class="badge bg-danger p-1 px-3 text-white">Zawieszony</div>';
            }

            if ($this->isGranted('ROLE_ALLOWED_TO_SWITCH')) {
                if (!$this->isGranted('ROLE_PREVIOUS_ADMIN')) {
                    $dat['actions'] .= '<a href="'.$this->generateUrl('home', ['_switch_user' => $user->getEmail()]).'" class="badge btn btn-primary  text-white me-2">Przełącz </a>';
                }
            }

            $dat['actions'] .= '<a href="'.$this->generateUrl('user_edit', ['id' => $user->getId()]).'" class="badge btn btn-primary  text-white">Edytuj</a>';

            array_push($data, $dat);
            // }
        }
        $jsonData = [
            'data' => $data,
        ];

        return new JsonResponse($jsonData);
    }

    #[Route('/uzytkownicy/dodaj', name: 'user_add')]
    public function add(Request $request, UserPasswordHasherInterface $passwordEncoder): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        // Tworzymy podstatowy formularz
        $user = new User();
        $form = $this->createForm(NewUserType::class, $user);
        $form->handleRequest($request);

        // Jeżeli formularz wysłany
        if ($form->isSubmitted() and $form->isValid()) {
            $log = [];
            $log['name'] = 'Użytkownicy';
            $log['date'] = new \DateTime(); // Data rozpoczęcia zadania
            $log['value'] = $form->get('email')->getData().' - dodano użytkownika';

            $user->setActived(true);
            $user->setIsVerified(true);
            $user->setPassword(
                $passwordEncoder->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $this->userRepository->save($user, true);

            $userData = new UserData();
            $userData->setUser($user);
            $userData->setPhone($form->get('phone')->getData());
            $userData->setName($form->get('name')->getData());

            $this->userDataRepository->save($userData, true);

            // TODO Zapis użytkownika do bazy mailerlite
            // $mailerLite = new MailerLiteService();
            // $mailerLite->addContact(['email' => $user->getEmail(), 'name' => $userData->getName(), 'groupId' => '11111111']);

            // TODO Wysyłka sms z hasłem do użytkownika
            // $sms = new SmsMessage($form->get('phone')->getData(), $form->get('password')->getData());
            // $sentMessage = $texter->send($sms);

            // TODO Wysyłka email do użytkownika po założeniu konta
            $message = $this->settings->get('emailUserCreateContent');
            $message = str_replace('[imie]', $userData->getName(), $message);
            $message = str_replace('[email]', $user->getEmail(), $message);
            $message = str_replace('[haslo]', 'Hasło zostało przesłane SMS', $message);
            $message = str_replace('[adres_logowania]', $this->generateUrl('app_sign_in', [], false), $message);

            $attach1 = $this->settings->get('emailAttach1', true);
            if (null != $attach1) {
                $dirPDF = $this->appKernel->getProjectDir().'/public'.$attach1->getPath();
                $filename = $attach1->getName();
                $attach = $dirPDF.$filename;
            } else {
                $attach = null;
            }

            $this->email->sendEmail([
                'from' => $this->settings->get('emailAdmin'),
                'from_label' => $this->settings->get('emailLabelAdmin'),
                'to' => $user->getEmail(),
                'to_label' => '',
                'subject' => $this->settings->get('emailUserCreateSubject'),
                'message' => $message,
                'htmlTemplate' => '/_modules/email/empty.html.twig',
                'attach' => $attach,
            ]);

            $this->logs->add($log);
            $this->addFlash('success', 'Zmiany zostały zapisane');

            return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
        }

        return $this->render('core/user/add.html.twig', [
            'controller_name' => 'Użytkownik',
            'header_title' => 'Użytkownik',
            'buttons' => [
                [
                    'button_class' => 'btn-secondary',
                    'button_icon' => 'ri-file-list-3-fill',
                    'button_link' => 'user_list',
                    'button_title' => 'Lista użytkowników',
                ],
                [
                    'button_class' => 'btn-secondary',
                    'button_icon' => 'ri-home-2-fill',
                    'button_link' => 'home',
                    'button_title' => '',
                ],
            ],
            'form' => $form->createView(),
        ]);
    }

    #[Route('/moj-profil', name: 'user_profil')]
    public function profil(Request $request, UserDataRepository $userDataRepository, UserPasswordHasherInterface $passwordEncoder, UserEmailRepository $userEmailRepository): Response
    {
        return $this->edit( $this->getUser(), $request,$userDataRepository, $passwordEncoder, $userEmailRepository);
    }

    #[Route('/uzytkownicy/edycja/{id}', name: 'user_edit')]
    public function edit(User $user, Request $request, UserDataRepository $userDataRepository, UserPasswordHasherInterface $passwordEncoder, UserEmailRepository $userEmailRepository): Response
    {
        $oldPass = '';
        $data = '';

        if ($user === $this->getUser() or $this->isGranted('ROLE_SUPER_ADMIN')) {
            $userData = $userDataRepository->findOneBy(['user' => $user]);

            if (!$userData) {
                $userData = new UserData();
                $userData->setUser($user);
            }

            $defaultData = [];
            if ($user->getUserData()) {
                $defaultData['name'] = $user->getUserData()->getName();
                $defaultData['lastName'] = $user->getUserData()->getLastName();
            }

            $defaultData['email'] = $user->getEmail();
            $defaultData['isVerified'] = $user->isVerified();
            $defaultData['roles'] = $user->getRoles();

            $defaultData2 = [];
            $formPhoto = $this->createFormBuilder($defaultData2)
                ->add('file', FileType::class, [
                    'label_attr' => ['class' => 'form-label'],
                    'label' => false,
                    'attr' => ['class' => 'file', 'id' => 'upload_file'],
                    'mapped' => false,
                    'required' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '10000k',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/jpg',
                                'image/png',
                            ],
                            'mimeTypesMessage' => 'Proszę wgrać plik',
                        ]),
                    ],
                ])
                ->getForm();
            $formPhoto->handleRequest($request);

            if ($formPhoto->isSubmitted()) {
                if ($formPhoto->isValid()) {
                    $log = [];
                    $log['name'] = 'Użytkownicy';
                    $log['date'] = new \DateTime(); // Data rozpoczęcia zadania
                    $log['value'] = $user->getEmail().' - dodano zdjęcie';

                    // $data = ['stat' => 'null'];
                    $file = $formPhoto->get('file')->getData();
                    $result = $this->filesService->upload($file);
                    $file = $this->filesService->getFile($result);
                    // $userData->setPhoto($file);
                    // $this->userDataRepository->save($userData, true);
                    $data = [
                        'id' => $file->getId(),
                        'name' => $file->getName(),
                        'path' => $file->getPath(),
                        'info' => 'test',
                    ];
                    $this->logs->add($log);
                } else {
                    $errors = ''; // $this->getErrorsFromForm($form);
                    $data = [
                        'type' => 'validation_error',
                        'title' => 'ERROR',
                        'errors' => $errors,
                    ];
                }

                return new JsonResponse($data);
            }

            if ($request->request->get('image')) {
                $return = $this->filesService->uploadImageFromBase64(
                    image: $request->request->get('image'),
                    name: $user->getId().'-'.uniqid().'.jpg',
                    path: '/uploads/profile/'
                );
                $userData->setPhoto($return);

                $userDataRepository->save($userData, true);

                $data = [
                    'id' => $return->getId(),
                    'name' => $return->getName(),
                    'path' => $return->getPath(),
                    'user' => $request->get('id'),
                ];

                return new JsonResponse($data);
            }

            // Tworze formularz z podstawowymi danymi
            $form = $this->createForm(UserType::class, $user);

            // Tworze formularz z rozszerzonymi danymi
            $formData = $this->createForm(UserDataType::class, $userData);
            $formData->handleRequest($request);
            if ($formData->isSubmitted() && $formData->isValid()) {
                $log = [];
                $log['name'] = 'Użytkownicy';
                $log['date'] = new \DateTime(); // Data rozpoczęcia zadania
                $log['value'] = $form->get('email')->getData().' - edycja użytkownika';
                $this->userDataRepository->save($userData, true);
                $this->addFlash('success', 'Zmiany zapisano');

                $this->logs->add($log);

                return $this->redirect($request->server->get('HTTP_REFERER'));
            }

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $log = [];
                $log['name'] = 'Użytkownicy';
                $log['date'] = new \DateTime(); // Data rozpoczęcia zadania
                $log['value'] = $form->get('email')->getData().' - edycja użytkownika';

                $this->userRepository->save($user, true);

                $this->logs->add($log);

                $this->addFlash('success', 'Zmiany zapisano');

                return $this->redirect($request->server->get('HTTP_REFERER'));
            }



            // $user = $this->getUser();
            $formPassword = $this->createForm(PasswordResetType::class);
            $formPassword->handleRequest($request);

            if ($formPassword->isSubmitted()) {
                if ($formPassword->get('password')->getData()) {
                    // Sprawdzam czy stare hasło jest prawidłowe, jeżeli tak, daje możliwwość zmiany hasła
                    if (true === $passwordEncoder->isPasswordValid($user, $formPassword->get('oldPassword')->getData())) {
                        $log = [];
                        $log['name'] = 'Użytkownicy';
                        $log['date'] = new \DateTime(); // Data rozpoczęcia zadania
                        $log['value'] = $user->getEmail().' - zmiana hasła';

                        $user->setPassword(
                            $passwordEncoder->hashPassword(
                                $user,
                                $formPassword->get('password')->getData()
                            )
                        );

                        $this->userRepository->save($user, true);

                        $this->addFlash('success', 'Hasło zostało zmienione.');
                        $this->logs->add($log);
                    } else {
                        $this->addFlash('danger', 'Wpisz poprawnie obecne hasło');
                    }
                } else {
                    $this->addFlash('danger', 'Podane hasła muszą być identyczne');
                }

                return $this->redirect($request->server->get('HTTP_REFERER'));
            }

            // Ustawienia email użytkownika
            $userEmail = $user->getUserEmail();
            if ($userEmail) {
                $oldPass = $userEmail->getSenderPass();
                if (null === $userEmail) {
                    $userEmail = new UserEmail();
                }
            }

            $formUserEmail = $this->createForm(UserEmailType::class, $userEmail);
            $formUserEmail->handleRequest($request);
            if ($formUserEmail->isSubmitted()) {
                $userEmail->setUser($user);

                if (null == $userEmail->getSenderPass()) {
                    $userEmail->setSenderPass($oldPass);
                } else {
                    $userEmail->setSenderPass($this->helper->encode($formUserEmail->get('senderPass')->getData()));
                }

                $userEmailRepository->save($userEmail, true);
            }

            return $this->render('core/user/edit.html.twig', [
                'controller_name' => 'Użytkownik',
                'header_title' => 'Użytkownik',
                'buttons' => [
                    [
                        'button_class' => 'btn-secondary',
                        'button_icon' => 'ri-file-list-3-fill',
                        'button_link' => 'user_list',
                        'button_title' => 'Lista użytkowników',
                    ],
                    [
                        'button_class' => 'btn-secondary',
                        'button_icon' => 'ri-home-2-fill',
                        'button_link' => 'home',
                        'button_title' => '',
                    ],
                ],
                'user' => $user,
                'form' => $form->createView(),
                'formData' => $formData->createView(),
                'formPhoto' => $formPhoto->createView(),
                'formPassword' => $formPassword->createView(),
                'formUserEmail' => $formUserEmail->createView(),
            ]);
        } else {
            echo 'no i coś nie tak';
            // throw new NotFoundHttpException('Nieprawidłowy adres');
            return $this->redirectToRoute('home');
        }
    }

    #[Route('/deletePhoto/{id}', name: 'user_deletePhoto')]
    public function deletePhoto(Request $request, UserData $id){
        //if ($request->isXmlHttpRequest()) {

            $userData = $id;

            if (null !== $request->request->get('idFiles')) {
                // delete photo

                if ($userData->getPhoto()->getId() == $request->request->get('idFiles')) {
                    $log = [];
                    $log['name'] = 'Użytkownicy';
                    $log['date'] = new \DateTime(); // Data rozpoczęcia zadania
                    $log['value'] = $userData->getUser()->getId().' - usunięcie zdjęcia profilowego';


                    $userData->setPhoto(null);
                    $this->userDataRepository->save($userData, true);

                    $this->filesService->deleteFileById($request->request->get('idFiles'));

                    $this->logs->add($log);

                    return new JsonResponse('success');
                }
            }
        return new JsonResponse('success');

        //}
    }


    /*#[Route('/zmiana-hasla', name: 'user_changePassword')]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordEncoder)
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $formPassword = $this->createForm(PasswordResetType::class);
        $formPassword->handleRequest($request);

        if ($formPassword->isSubmitted()) {
            if ($formPassword->get('plainPassword')->getData()) {
                // Sprawdzam czy stare hasło jest prawidłowe, jeżeli tak, daje możliwwość zmiany hasła
                if (true === $passwordEncoder->isPasswordValid($this->getUser(), $formPassword->get('oldPassword')->getData())) {
                    $log = [];
                    $log['name'] = 'Użytkownicy';
                    $log['date'] = new \DateTime(); // Data rozpoczęcia zadania

                    $log['value'] = $this->getUser()->getEmail() . ' - zmiana hasła';

                    $encodedPassword = $passwordEncoder->encodePassword(
                        $this->getUser(),
                        $formPassword->get('plainPassword')->getData()
                    );

                    $this->getUser()->setPassword($encodedPassword);
                    $this->userRepository->save();
                    $this->getDoctrine()->getManager()->flush();

                    $this->addFlash('success', 'Hasło zostało zmienione.');

                    $this->logs->add($log);

                    $user = $this->getUser()->setUserPass(true);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($user);
                    $entityManager->flush();

                    return $this->redirectToRoute('home');
                } else {
                    $this->addFlash('danger', 'Wpisz poprawnie obecne hasło');

                    return $this->redirect($request->server->get('HTTP_REFERER'));
                }
            } else {
                $this->addFlash('danger', 'Podane hasła muszą być identyczne');

                return $this->redirect($request->server->get('HTTP_REFERER'));
            }
        }

        return $this->render('core/user/changePassword.html.twig', [
            'formPassword' => $formPassword->createView(),
        ]);
    }*/

    #[Route('/uzytkownicy/userDelete/{id}', name: 'user_delete', methods: ['DELETE', 'POST'])]
    public function delete(Request $request, User $user): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            if (1 !== $user->getId() and !in_array('ROLE_SUPER_ADMIN', $user->getRoles()) and 'admin@appcode.eu' !== $user->getEmail()) {
                // TODO Przy usuwaniu użytkownika dodaj elementy, które chesz usunać
                /*foreach ($user->getComments() as $item) {
                    $user->removeComment($item);
                }*/

                $this->userRepository->save($user, true);
                $this->userRepository->remove($user, true);

                $this->addFlash('success', 'Użytkownik został usunięty');
            } else {
                $this->addFlash('danger', 'Nie można usunąć tego użytkownika');
            }
        }

        // return new JsonResponse();
        return $this->redirectToRoute('user_list');
    }

    #[Route('/cropImage', name: 'user_cropImage')]
    public function cropImage(Request $request)
    {
        if ($request->request->get('image')) {
            $path = '/uploads/profile/';
            $name = $this->getUser()->getId().uniqid().'.jpg';
            $return = $this->filesService->uploadImageFromBase64($request->request->get('image'), $path, $name, 'jpg');

            return $return;
        }

        return $this->render('core/user/cropImage.html.twig', [
        ]);
    }
}
