<?php
class App
{

    private $__controller, $__action, $__params;

    public function __construct()
    {

        global $routes, $config;

        if (!empty($routes['default_controller'])) {
            $this->__controller = $routes['default_controller'];
        }
        $this->__action = 'index';
        $this->__params = [];
        $this->handleUrl();
    }

    function getUrl()
    {
        if (!empty($_GET['url'])) {
            $url = $_GET['url'];
        } else {
            $url = '/';
        }
        return $url;
    }

    public function handleUrl()
    {
        $url = $this->getUrl();
        $urlArr = array_filter(explode('/', $url));
        $urlArr = array_values($urlArr);

        if (isset($urlArr[0]) && ucfirst($urlArr[0]) == 'Admin') {
            $this->__action = 'Login';
        }
        // Xu ly controller
        if (!empty($urlArr[0])) {
            $this->__controller = ucfirst($urlArr[0]);
        } else {
            $this->__controller = ucfirst($this->__controller);
        }
        if (file_exists('./app/controllers/' . $this->__controller . '.php')) {
            require_once './app/controllers/' . $this->__controller . '.php';
            $this->__controller = new $this->__controller();
            unset($urlArr[0]);
            // Xu ly action
            if (!empty($urlArr[1])) {
                $this->__action = $urlArr[1];
                unset($urlArr[1]);
            }

            // Xu li params
            $this->__params = array_values($urlArr);

            // Kiem tra method co ton tai hay khong
            if (method_exists($this->__controller, $this->__action)) {
                call_user_func_array([$this->__controller, $this->__action], $this->__params);
            } else {
                $this->loadError();
            }
        } else {
            $this->loadError();
        }
    }

    public function loadError($name = '404')
    {
        require_once './app/errors/' . $name . '.php';
    }
}
