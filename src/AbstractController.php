<?php
namespace Base;
use App\Controller\Blog;
use App\Model\User;

abstract class AbstractController
{
    /** @var View */
    protected $view;
    /** @var User */
    protected $user;


    /**
     * @param View $view
     */
    public function setView(View $view): void
    {
        $this->view = $view;
    }


    protected function redirect(string $url)
    {
        throw new RedirectException($url);
    }


    public function setUser(User $user): void
    {
        $this->user = $user;
    }
}