<?php
namespace App\Modules\Web\Home\Controllers;

class HomeController{
    function index() {

        $viewPath = $this->viewPath('home/index.php');
        ob_start();
        require_once $viewPath;
        return ob_get_clean();
    }

    private function viewPath($path){
        $viewPath = realpath(__DIR__ . '/../../../../views/'. $path);
        return $viewPath;
    }
}


