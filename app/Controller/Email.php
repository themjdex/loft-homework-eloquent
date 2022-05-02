<?php
namespace App\Controller;

use Base\AbstractController;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use App\Model\Email as EmailModel;


class Email extends AbstractController
{
    private $transport;
    private $mailer;
    private $message;

    public function __construct()
    {
        $this->transport = (new Swift_SmtpTransport('smtp.mail.ru', 465, 'ssl'))
            ->setUsername('restore-blog-pass@mail.ru')
            ->setPassword('kBpF8gs45n2V9Z8FtNJj')
        ;

        $this->mailer = new Swift_Mailer($this->transport);

        $this->message = (new Swift_Message('Восстановление пароля'))
            ->setFrom(['restore-blog-pass@mail.ru' => 'Service Blog']);
    }
    public function newPasswordAction()
    {
        include PROJECT_ROOT_DIR . '/app/View/User/password.phtml';
    }
    public function sendAction()
    {
        if (!isset($_POST['email'])) {
            $this->redirect('/user/register');
        }
        $email = $_POST['email'];
        $try = EmailModel::getUser($email);

        if (!isset($try)) {
            $this->view->assign('error', 'Проверьте правильность почты');
            exit();
        }

        $this->message->setTo([$email])->setBody('Ваш новый пароль: qwerty (ненастоящий)');
        $this->mailer->send($this->message);
        $this->redirect('/user/login');
    }
}
