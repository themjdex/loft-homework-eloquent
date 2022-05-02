<?php
namespace Base;
class View
{
    private $templatePath = '';
    private $data = []; // ['posts' => []]

    public function __construct()
    {
        $this->templatePath = PROJECT_ROOT_DIR . DIRECTORY_SEPARATOR . 'app/View';
    }

    public function assign(string $name, $value)
    {
        $this->data['name'] = $value;
        $this->render('User/error.phtml', ['name' => $name, 'value' => $value]);

    }
    public function render(string $tpl, $data = []): string
    {
        extract($data);
        ob_start();
        include $this->templatePath . DIRECTORY_SEPARATOR . $tpl;
        return ob_end_flush();
    }

    public function __get($varName)
    {
        return $this->data[$varName] ?? null;
    }


}
