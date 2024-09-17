<?php
    use app\core\Registry;
    // require_once dirname(__FILE__).'/Router.php';
    // require_once dirname(__FILE__).'/../controllers/HomeController.php';
    require_once dirname(__FILE__).'/Autoload.php';//autoload để nạp các file trước khi classname được gọi
    //use dùng để khai báo class
    // use app\core\Router;
    class App{
        
        // tạo thuoc tinh
        private $router;
        // public static $config;
        // private static $controller;
        // private static $action;
        //áp dụng registry trong design pattern để tự khởi tạo các phương thức
    
        function __construct($config){
            // tự động khởi tạo class đã đc gọi
            new Autoload($config['rootDir']);
            $this->router = new Router($config['basePath']);
            Registry::getIntance()->config = $config;
        }
        // public static function setConfig($config){
        //     //tu khoa statis dung self
        //     self::$config = $config;
        // }
        // public static function getConfig(){
        //     return self::$config;
        // }

        // public static function setController($controller){
        //     //tu khoa statis dung self
        //     self::$controller = $controller;
        // }
        // public static function getController(){
        //     return self::$controller;
        // }

        // public static function setAction($action){
        //     //tu khoa statis dung self
        //     self::$action = $action;
        // }
        // public static function getAction(){
        //     return self::$action;
        // }
        public function run(){
            // echo 'App running';
            $this->router->run();
        }
    }
?>