<?php
namespace App\Controller;
use Base\AbstractController;
use App\Model\User as UserModel;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class User extends AbstractController
{
    private $_twig;
    private $data;

    public function loginAction()
    {
        $email = trim($_POST['email']);

        if ($email) {
            $password = $_POST['password'];
            $user = UserModel::getByEmail($email);
            if (!$user) {
                $this->view->assign('error', 'Неверный логин и/или пароль');
            }

            if ($user) {

                if ($user->getPassword($email) != UserModel::getPasswordHash($password)) {
                    $this->view->assign('error', 'Неверный логин и/или пароль');
                } else {
                    $_SESSION['id'] = $user->getId($email);
                    $this->redirect('/blog');
                }
            }
        }
        return $this->view->render('User/register.phtml', [
            'user' => UserModel::getById((int) $_GET['id'])
        ]);

    }
    function registerAction()
    {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $passwordRetry = trim($_POST['passwordRetry']);
        $success = true;

        if (isset($_POST['email'])) {
            if (!$email) {
                $this->view->assign('error', 'Почта должна быть указана');
                $success = false;
            }
            if (!$name) {
                $this->view->assign('error', 'Имя не может быть пустым');
                $success = false;
            }
            if (!$password) {
                $this->view->assign('error', 'Пароль не может быть пустым');
                $success = false;
            }
            if (!$passwordRetry) {
                $this->view->assign('error', 'Нужно ввести пароль повторно');
                $success = false;
            }

            if ($password != $passwordRetry) {
                $this->view->assign('error', 'Введенные пароли не совпадают');
                $success = false;
            }

            if (mb_strlen($password) <= 3) {
                $this->view->assign('error', 'Пароль должен быть не менее 4 символов');
                $success = false;
            }

            $user = UserModel::getByEmail($email);
            if ($user) {
                $this->view->assign('error', 'Такая почта уже занята');
                $success = false;
            }
            if ($success) {
                $user = (new UserModel())
                    ->setName($name)
                    ->setEmail($email)
                    ->setPassword(UserModel::getPasswordHash($password));

                $user->saveMe();

                $_SESSION['id'] = $user->getId();

                $this->setUser($user);

                $this ->redirect('/blog');
            }
        } else {
            $this->view->assign('error', 'Вы не указали почту при регистрации или пытаетесь войти без авторизации');
            die;
        }

        return $this->view->render('User/register.phtml', [
            'user' => UserModel::getById((int) $_GET['id'])
        ]);
    }

    public function forgotPasswordAction()
    {

    }

    /**
     * @return \Twig\Environment
     */
    public function getTwig()
    {
        if (!$this->_twig) {
            $path = './app/View/User' ;
            $loader = new FilesystemLoader($path);
            $this->_twig = new Environment($loader, array('cache' => $path . '/compil'));
        }
        return $this->_twig;
    }

    public function profileAction()
    {
        return $this->view->render('User/cabinet.phtml', [
            'user' => UserModel::getById((int) $_SESSION['id'])
        ]);
    }

    public function logoutAction()
    {
        session_destroy();
        $this->redirect('/user/login');
    }

    public function adminAction()
    {
        $allUsers = UserModel::getAllUsers()->toArray();
        return $this->view->render('User/admin.phtml', [
            'users' => $allUsers
        ]);
    }

    public function newAction()
    {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $password = trim($_POST['password']);
        $success = true;

        if (!isset($name) || !isset($email) || !isset($password)) {
            $this->view->assign('error', 'Нужно указать все данные');
            $success = false;
        }

        $user = UserModel::checkEmail($email);

        if ($user) {
            $this->view->assign('error', 'Такая почта уже занята');
            $success = false;
        }

        if ($success) {
            $user = (new UserModel())
                ->setName($name)
                ->setEmail($email)
                ->setPassword(UserModel::getPasswordHash($password));

            $user->saveMe();
            $this ->redirect('/user/admin');
        }
    }

    public function changeAction()
    {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $id = trim($_POST['id']);
        $role = $_POST['role'];
        $avatar = trim($_POST['avatar']);

        if (!isset($id) || !isset($name) || !isset($email) || !isset($role) || !isset($avatar)) {
            $this->view->assign('error', 'Нужно указать все данные');
        } else {
            $result = UserModel::updateUser((int) $id, $email, $name, $role, $avatar);
            if ($result) {
                ob_start();
                $this->adminAction();
                header('Location: http://week5-eloquent/user/admin');
                ob_end_flush();
            } else {
                $this->view->assign('error', 'Что-то пошло не так');
            }
        }
    }
}