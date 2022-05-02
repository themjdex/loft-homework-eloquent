<?php
namespace App\Controller;

use App\Model\post;
use Base\AbstractController;
use Intervention\Image\ImageManager;
use Intervention\Image\Image;
use function Symfony\Component\Translation\t;


class Blog extends AbstractController
{
    public $messages;
    public $posts;
    public $avatar;
    public $userRole = USER_ROLE;

    function indexAction()
    {
        if (!$this->user) {
            $this->redirect('/user/register');
        }

        if (!isset($_POST['message'])) {
            $userId = $_SESSION['id'];
            $message = new post('', $userId);
            $this->messages = $message->showLastMessages();
            $this->userRole = $message->checkUserRole($userId);
            $this->avatar = $message->getAvatar();
            $this->avatar = json_decode($this->avatar, true);

            return $this->view->render('Blog/index.phtml', [
                'posts' => $this->messages, 'role' => $this->userRole, 'avatar' => $this->avatar
            ]);
        } else {
            header('Location: http://week5-eloquent/blog');
            $userMessage = $_POST['message'];
            $userId = $_SESSION['id'];
            if (!$userMessage) {
                $this->view->assign('error', 'Нужно ввести текст');
                exit();
            }
            $message = new post($userMessage, $userId);
            $message->saveMessage();
            $this->messages = $message->showLastMessages();
            $this->avatar = $message->getAvatar()->toArray();

            if (!empty($_FILES['userfile']['tmp_name'])) {
                $postId = $message->getIdSavedImage()['id'];
                $fileContent = file_get_contents($_FILES['userfile']['tmp_name']);
                file_put_contents('./images/' . $postId . '.png', $fileContent);
                $this->resize($postId);

            } else {
                $this->view->assign('error', 'Нет сообщения');
                exit();
            }

            return $this->view->render('Blog/index.phtml', [
                'posts' => $this->messages, 'role' => $this->userRole, 'avatar' => $this->avatar
            ]);

        }
    }

    public function resize($postId)
    {
        $_imagePath = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'images/';
        $data = file_get_contents($_imagePath . $postId . '.png');
        $image = (new \Intervention\Image\ImageManager)->make($data)->resize(200, null, function ($image) {
            $image->aspectRatio();
        });
        self::watermark($image);
        $image->save('images/' . $postId . '.png');
    }
    public static function watermark(Image $image)
    {
        $image->text(
            "Блог\nобо\nвсем",
            20,
            15,
            function ($font) {
                $font->file(PROJECT_ROOT_DIR . '/fonts/arial.ttf')->size('24');
                $font->color(array(0, 0, 0, 0.5));
                $font->align('left');
                $font->valign('top');
            });
    }
    public function mypostAction()
    {
        $userId = $_SESSION['id'];
        $message = new post('', $userId);
        $json = $message->getLastMessagesById($userId);
        if (!$json) {
            return $this->view->render('Blog/index.phtml', [
                'posts' => 'Сообщений нет'
            ]);
        } else {
            $this->posts = json_decode($json, true);
            return $this->view->render('Blog/index.phtml', [
                'posts' => $this->posts, 'role' => $this->userRole
            ]);
        }
    }

    public function deleteAction()
    {
        header('Location: http://week5-eloquent/blog');
        $userId = $_SESSION['id'];
        $message = new post('', $userId);
        $postId = (int) $_POST['postId'];
        $message->deleteMessage($postId);
        $this->messages = $message->showLastMessages();

        return $this->view->render('Blog/index.phtml', [
            'posts' => $this->messages, 'role' => ADMIN_ROLE
        ]);
    }
}