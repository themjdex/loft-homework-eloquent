<?php
namespace Base;

class Route
{
    private $controllerName;
    private $actionName;
    private $processed = false;
    private $routes;

    private function process()
    {
        if (!$this->processed) {
            $parts = parse_url($_SERVER['REQUEST_URI']);
            $path = $parts['path'];
            if (($route = $this->routes[$path] ?? null) !== null) {
                $this->controllerName = $route[0];
                $this->actionName = $route[1];
            } else {
                $parts = explode('/', $path);
                $this->controllerName = '\\App\\Controller\\' . ucfirst(strtolower($parts[1]));
                $this->actionName = strtolower($parts[2] ?? 'Index');
            }
            $this->processed = true;
        }
    }
    public function addRoute($path, $controllerName, $actionName)
    {
        $this->routes[$path] = [
            $controllerName,
            $actionName
        ];
    }
    public function getControllerName(): string
    {
        if (!$this->processed) {
            $this->process();
        }
        return $this->controllerName;
    }
    public function getActionName(): string
    {
        if (!$this->processed) {
            $this->process();
        }
        return $this->actionName . 'Action';
    }
}