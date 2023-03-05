<?php

namespace App\Controller\Core;

use App\Entity\Core\Files\Files;
use App\Entity\Core\Settings;
use App\Repository\Core\SettingsRepository;
use App\Service\FilesService;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;

class SettingsController extends AbstractController
{
    private SettingsRepository $setting;
    private FilesService $files;

    public function __construct(SettingsRepository $settingsRepository, FilesService $filesService)
    {
        $this->setting = $settingsRepository;
        $this->files = $filesService;
    }

    #[Route('/ustawienia', name: 'settings')]
    public function settings(Request $request): Response
    {
        $form = $this->createFormBuilder()->getForm();
        $fields = [
            [
                'label' => 'Po rejestracji',
                'name' => 'serviceType',
                'value' => 'close',
                'type' => 'choice2',
                'choices' => [
                    'można się odrazu zalogować' => 'open',
                    'konto akceptuje administrator systemu' => 'close',
                    'można się zalogować po zatwierdzeniu adresem email' => 'confirmOpen',
                    'można się zalogować po zatwierdzeniu adresem email oraz akceptacji admina' => 'confirmClose',
                ],
            ],
            [
                'label' => 'Tytuł strony',
                'name' => 'pageTitle',
                'value' => '',
                'type' => 'text',
            ],
            [
                'label' => 'Logotyp',
                'name' => 'logo',
                'value' => '',
                'type' => 'file',
                'help' => '300px x 300px',
            ],
            [
                'label' => 'Favicon',
                'name' => 'favicon',
                'value' => '',
                'type' => 'file',
                'help' => '32px x 32px',
            ],
            [
                'label' => 'Zgoda 1',
                'name' => 'accept1',
                'value' => '',
                'type' => 'text',
            ],

            [
                'label' => 'Email - Założenie konta w systemie - Temat',
                'name' => 'emailUserCreateSubject',
                'value' => 'Konto zostało założone',
                'type' => 'text',
                'help' => '',
            ],
            [
                'label' => 'Email - Założenie konta w systemie - Treść',
                'name' => 'emailUserCreateContent',
                'value' => '[imie] [email] [haslo] [adres_logowania]',
                'type' => 'textarea',
                'help' => 'Dostępne pola: [imie] [email] [haslo] [adres_logowania]',
            ],

            [
                'label' => 'Stopka Email',
                'name' => 'emailFooter',
                'value' => 'Programigo',
                'type' => 'ckeditor',
            ],

            [
                'label' => 'Zgoda na założenie konta',
                'name' => 'agreeAccount',
                'value' => 'Wyrażam zgodę na założenie konta w systemie.',
                'type' => 'text',
            ],

            [
                'label' => 'Zgoda na informacje handlowe',
                'name' => 'agreeTerms',
                'value' => 'Wyrażam zgodę na otrzymywanie drogą elektroniczną na wskazany przeze mnie adres e-mail informacji handlowej w rozumieniu art.10 ust.1 ustawy z dnia 18 lipca 2002 roku o świadczeniu usług drogą elektroniczną (Dz.U. 2002 Nr 144 poz. 1204 ze zmianami).',
                'type' => 'text',
            ],
        ];

        $result = $this->setSetting($request, $form, $fields);
        if (true == $result['send']) {
            return $this->redirectToRoute('settings');
        }

        return $this->render('core/settings/index.html.twig', [
            'controller_name' => 'Ustawienia',
            'header_title' => 'Ustawienia',
            'fields' => $fields,
            'breadcrumb' => [
                ['none' => 'Ustawienia'],
            ],
            'form' => $result['form']->createView(),
        ]);
    }

    public function setSetting($request, $form, array $args = []): array
    {
        $db = [];

        if (!is_array($args)) {
            throw new NotFoundHttpException('Błędna struktura tablicy');
        }
        foreach ($args as $item) {
            if (!isset($item['name'])) {
                throw new NotFoundHttpException('Uzupełnij nazwę ustawienia');
            }
            if (!isset($item['value'])) {
                throw new NotFoundHttpException('Uzupełnij wartość ustawienia');
            }

            $db[$item['name']] = $this->setting->findOneBy(['name' => $item['name']]);
            if ($db[$item['name']]) {
                $return['values'][$item['name']] = $db[$item['name']]->getValue();
                $defaultValue = $return['values'][$item['name']];
            } else {
                $db[$item['name']] = new Settings();
                $db[$item['name']]->setName($item['name']);
                $db[$item['name']]->setValue($item['value']);
                $this->setting->save($db[$item['name']], true);

                $return['values'][$item['name']] = $db[$item['name']]->getValue();
                $defaultValue = $item['value'];
            }

            if ('text' == $item['type']) {
                $form->add($item['name'], TextType::class, ['data' => $defaultValue, 'label_attr' => ['class' => 'form-label'], 'label' => $item['label'], 'help' => @$item['help'], 'attr' => ['class' => 'form-control form-control-sm']]);
            } elseif ('color' == $item['type']) {
                $form->add($item['name'], ColorType::class, ['data' => $defaultValue, 'label_attr' => ['class' => 'form-label'], 'label' => $item['label'],  'help' => @$item['help'], 'attr' => ['class' => 'form-control form-control-color', 'value' => '#00FF00']]);
            } elseif ('choice' == $item['type']) {
                $form->add($item['name'], ChoiceType::class, [
                    'data' => $defaultValue,
                    'label' => $item['label'],
                    'help' => @$item['help'],
                    'label_attr' => ['class' => 'control-label ', 'style' => 'margin-top:7px;'],
                    'choices' => $item['choices'],
                    'expanded' => true,
                    'multiple' => false,
                    'attr' => ['class' => 'form-control form-control-sm'],
                ]);
            } elseif ('choice2' == $item['type']) {
                $form->add($item['name'], ChoiceType::class, [
                    'data' => $defaultValue,
                    'empty_data' => $defaultValue,
                    'label' => $item['label'],
                    'help' => @$item['help'],
                    'label_attr' => ['class' => 'control-label', 'style' => 'margin-top:7px;'],
                    'choices' => $item['choices'],
                    'expanded' => false,
                    'multiple' => false,
                    'attr' => ['class' => 'form-control form-control-sm'],
                ]);
            } elseif ('textarea' == $item['type']) {
                $form->add($item['name'], TextareaType::class, ['data' => $defaultValue, 'label_attr' => ['class' => 'form-label'], 'label' => $item['label'], 'help' => @$item['help'], 'attr' => ['class' => 'form-control  form-control-sm ckeditor_'.$item['name']]]);
            } elseif ('ckeditor' == $item['type']) {
                $form->add($item['name'], CKEditorType::class, ['config' => [
                    'uiColor' => '#ffffff',
                    // ...
                ], 'data' => $defaultValue, 'label_attr' => ['class' => 'form-label'], 'label' => $item['label'], 'help' => @$item['help'], 'attr' => ['class' => 'form-control  form-control-sm ckeditor_'.$item['name']]]);
            } elseif ('file' == $item['type']) {
                $form->add($item['name'], FileType::class, [
                    'label_attr' => ['class' => 'form-label'],
                    'label' => $item['label'],
                    'attr' => ['class' => 'file', 'id' => 'upload_file'],
                    'mapped' => false,
                    'help' => @$item['help'],
                    'required' => false,
                    'constraints' => [
                        new File([
                            'maxSize' => '5000k',
                            'mimeTypes' => [
                            ],
                            'mimeTypesMessage' => 'Proszę wgrać plik',
                        ]),
                    ],
                ]);
            }
        }
        $form->handleRequest($request);
        $return['send'] = false;
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            foreach ($request->files->all() as $files) {
                foreach ($files as $key => $_file) {
                    if (is_object($_file)) {
                        $file = $this->files->uploadFile($_file);
                      
                        $db[$key]->setFile($file);
                        $this->setting->save($db[$key], true);
                    }
                }
            }
            foreach ($data as $key => $item) {
                $db[$key]->setValue($item);
                $this->setting->save($db[$key], true);
            }

            $this->addFlash('success', 'Zmiany zostały zapisane');
            $return['send'] = true;
        }
        $return['form'] = $form;

        return $return;
    }

    #[Route('/setting/removefile/{id}', name: 'removeFileSetting', methods: ['GET', 'POST'])]
    public function removeSettingFile(Files $files, Request $request): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        $file = $this->setting->findOneBy(['file' => $files]);
        if ($file) {
            $file->setFile(null);
            $this->setting->save($file, true);
        }

        $this->files->deleteFileById($files);

        return $this->redirect($request->server->get('HTTP_REFERER'));
    }
}
