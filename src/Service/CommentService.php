<?php
/* Serwis służący do obsługi komentarzy - dodawanie, wyświetlanie, usuwanie, edycja (wszystko w jednym)

I. Należy utworzyć encję Comments zawierającą następujące pola:
    1) comment <- treść komentarza, typ: text
    2) postedDate <- data dodania komentarza, typ: datetime
    3) author <- użytkownik, który napisał komentarz, typ: relation do encji User, typ relacji ManyToOne
    4) isPublic <- czy komentarz ma być widoczny publicznie, typ: boolean
    5) parent <- komentarz będący "rodzicem", typ: relation do encji Comments, typ relacji ManyToOne
    6) powiązanie np.: ze szkoleniem (system szkoleniowy) lub samochodem (mobiClassic) <- zależnie od miejsca
        wykorzystania można utworzyć np. pole o typie relation
        do wybranej encji np. Trainings, Cars, itp, typ relacji ManyToMany

II. CommentsController <- kontroler służący do obsługi systemu komentarzy
    1) commentsIndex <- wywołanie widoku komentarzy
    2) getComments <- dynamiczne pobieranie komentarzy
    3) addNewComment <- dynamiczne dodawanie komentarzy
    4) addNewCommentUnderParent <- dynamiczne odpowiedzi na komentarze
    5) deleteComment <- dynamiczne usuwanie komentarzy

III. templates/comments <- folder z widokami
    1) index.html.twig <- jedyny widok potrzebny do obsłużenia całego systemu komentarzy

IV. public/comments <- pliki zasobów, niezbędne do działania systemu komentarzy


V. Konfiguracja systemu:

    1) W CommentsService znajduje się funkcja getAuthorProfilePhoto() w której nalezy ustalić ścieżkę do plików ze zdjęciami
        profilowymi użytkowników oraz ścieżkę do pliku, który ma być używany w przypadku, gdy użytkownik nie posiada zdjęcia

    2) W CommentsService znajduje się funkcja getAuthorFullName() w której należy ustalić w jaki sposób ma być pobierana
        nazwa autora komentarza (nazwa jest wyświetlana nad komentarzem)

    3) W CommentsService znajduje się funkcja getCommentsByParent() w której należy ustalić czy podczas wyświetlania komentarzy
        ma być wyświetlana informacja kto komu odpowiedział np.: "user1 -> user2" Domyślnie jest to ustawione jako aktywne,
        aby usunąć w/w wyświetanie wystarczy zakomentować lub usunąć poniższy fragment kodu:

            if($parent)
            {
                $parentId = $parent->getId();
                $parentName = $this->getAuthorFullName($parent->getAuthor());
            }


*/

namespace App\Service;

use App\Entity\Core\Comments;
use App\Repository\Core\CommentsRepository;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Security;

class CommentService
{
    protected AuthorizationCheckerInterface $authorizationChecker;
    public CommentsRepository $repository;
    protected Security $security;
    private ValidationService $validation;
    private Package $package;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        CommentsRepository $commentsRepository,
        Security $security,
        ValidationService $validationService
    ) {
        $this->authorizationChecker = $authorizationChecker;
        $this->repository = $commentsRepository;
        $this->security = $security;
        $this->validation = $validationService;
        $this->package = new Package(new EmptyVersionStrategy());
    }

    public function getAuthorProfilePhoto($author): string
    {
        $picture = $this->package->getUrl('/panel/assets/img/emptyProfile.png');
        if ($author->getUserData()) {
            if ($author->getUserData()->getPhoto()) {
                $picture = $author->getUserData()->getPhoto()->getPath().$author->getUserData()->getPhoto()->getName();
            }
        }

        return $picture;
    }

    public function getAuthorFullName($author): string
    {
        return $author->getEmail();
    }

    public function getCommentById($id): ?Comments
    {
        return $this->repository->findOneBy(['id' => $id, 'isPublic' => true]);
    }

    /**
     * @return Comments[]
     */
    public function getCommentsByParent($parentId = null, $entity = null, $nameEntity = null)
    {
        ++$GLOBALS['commentsCounter'];

        if (null != $entity and null != $nameEntity) {
            $parent = $this->repository->findOneBy(['id' => $parentId, 'isPublic' => true, $nameEntity => $entity]);
            $comments = $this->repository->findBy(['parent' => $parent, 'isPublic' => true, $nameEntity => $entity], ['id' => 'DESC']);
        } else {
            $parent = $this->repository->findOneBy(['id' => $parentId, 'isPublic' => true]);
            $comments = $this->repository->findBy(['parent' => $parent, 'isPublic' => true], ['id' => 'DESC']);
        }

        $childrensArray = [];
        foreach ($comments as $children) {
            $childrensSubArray = $this->getCommentsByParent($children, $entity, $nameEntity);

            $parentId = 0;
            $parentName = null;
            if ($parent) {
                $parentId = $parent->getId();
                $parentName = $this->getAuthorFullName($parent->getAuthor());
            }
            $childrensArray[] = [
                'comment_id' => $children->getId(),
                'parent_id' => $parentId,
                'in_reply_to' => $parentName,
               // "element_id" => "134",
                'created_by' => $children->getAuthor()->getId(),
                'fullname' => $this->getAuthorFullName($children->getAuthor()),
                'picture' => $this->getAuthorProfilePhoto($children->getAuthor()),
                'posted_date' => $children->getPostedDate()->format('d-m-Y H:i:s'),
                'text' => $children->getComment(),
                'attachments' => [],
                'childrens' => $childrensSubArray,
            ];
        }

        return $childrensArray;
    }

    public function getComments($entity = null, $entityName = null)
    {
        $GLOBALS['commentsCounter'] = 0;

        $commentsArray = $this->getCommentsByParent(null, $entity, $entityName);

        if ($this->security->isGranted('ROLE_USER')) {
            $is_logged_in = true;
            $is_add_allowed = true;
            $is_edit_allowed = true;
        } else {
            $is_logged_in = false;
            $is_add_allowed = false;
            $is_edit_allowed = false;
        }

        $named_array = [
            'results' => [
                'comments' => $commentsArray,
                'total_comment' => $GLOBALS['commentsCounter'],
                'user' => [
                    'user_id' => $this->security->getUser()->getId(),
                    'fullname' => $this->getAuthorFullName($this->security->getUser()),
                    'picture' => $this->getAuthorProfilePhoto($this->security->getUser()),
                    'is_logged_in' => $is_logged_in,
                    'is_add_allowed' => $is_add_allowed,
                    'is_edit_allowed' => $is_edit_allowed,
                ],
            ],
        ];

        return $named_array;
    }

    /**
     * @return array|false
     */
    public function addNewComment($entity = null, $nameEntity = null)
    {
        if (isset($_POST['text']) && $this->validation->sanitizeString($_POST['text'])) {
            $comment = new Comments();

            if (null != $entity and null != $nameEntity) {
                $comment->{'set'.$nameEntity}($entity);
            }

            $comment->setAuthor($this->security->getUser());
            $comment->setComment($this->validation->sanitizeString($_POST['text']));
            $comment->setPostedDate(new \DateTime());
            $comment->setIsPublic(true);
            if (isset($_POST['parent_id']) && $this->validation->sanitizeInteger($_POST['parent_id'])) {
                $comment->setParent($this->getCommentById($this->validation->sanitizeInteger($_POST['parent_id'])));
            }
            $this->repository->save($comment, true);

            $result = [];

            $result['success'] = true;
            $result['comment_id'] = $comment->getId();

            if ($comment->getParent()) {
                $result['in_reply_to'] = $comment->getParent()->getAuthor()->getEmail();
            } else {
                $result['in_reply_to'] = null;
            }
            // $result["element_id"] = "134";
            $result['created_by'] = $comment->getAuthor()->getId();
            $result['fullname'] = $this->getAuthorFullName($comment->getAuthor());
            $result['picture'] = $this->getAuthorProfilePhoto($this->security->getUser());
            $result['posted_date'] = $comment->getPostedDate()->format('d-m-Y H:i:s');
            $result['text'] = $comment->getComment();
            $result['attachments'] = [];
            $result['childrens'] = [];
            if ($comment->getParent()) {
                $result['parent_id'] = $comment->getParent()->getId();
            } else {
                $result['parent_id'] = 0;
            }

            return $result;
        } else {
            return false;
        }
    }

    /**
     * @return array|false
     */
    public function addNewCommentUnderParent($parentId)
    {
        if (isset($_POST['text']) && $this->validation->sanitizeString($_POST['text'])) {
            $comment = $this->getCommentById($parentId);
            $comment->setComment($this->validation->sanitizeString($_POST['text']));
            $this->repository->save($comment);
            $result = [];
            $result['success'] = true;
            $result['text'] = $comment->getComment();

            return $result;
        } else {
            return false;
        }
    }

    /**
     * @return false|int|null
     */
    public function deleteComment()
    {
        // usuwanie komentarza (komentarze może usuwać tylko ich autor)
        $comment = $this->getCommentById($this->validation->sanitizeInteger($_POST['id']));
        if ($comment->getAuthor() == $this->security->getUser()) {
            $comment->setIsPublic(false);
            $this->repository->save($comment);

            return $comment->getId();
        } else {
            return false;
        }
    }

    public function getCommentsService()
    {
        // return $this->repository->getCommentsService(); //$this->read->findOneBy(['user' => $this->security->getUser(), 'comment' => $comment]);
        return $this->repository->findBy([], ['id' => 'DESC']);
    }
}
