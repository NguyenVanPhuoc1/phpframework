<?php
    use app\core\AppException;
    class Autoload{
        //khi dùng autoload thi cac class khac can khoi tao namespace 
        private $rootDir;
        function __construct($rootDir){
            $this->rootDir = $rootDir;
            //dang ki khoi tao file
            //trước khi khởi tạo class thì hàm autoload sẽ chạy
            spl_autoload_register([$this, 'autoload']);
            $this->autoLoadFile();
        }
        
        private function autoload($class){
            // echo $class;
            //lay phan tu cuoi trong mang
            // $rootPath = App::getConfig()['rootDir'];
            $part = explode('\\', $class);
            $className = end($part);
            $partName = str_replace($className,'',$class);

            $filePath = "{$this->rootDir}\\{$partName}{$className}.php";
            // $filePath = "{$rootPath}\\{$class}.php";

            // echo $filePath;
            if(file_exists($filePath)){
                require_once $filePath;
            }else{
                throw new AppException("{$class} does not exsits");
            }
        }
        // tu dong load file Autoload
        private function autoLoadFile(){
            foreach($this->defaultFileLoad() as $file){
                require_once "{$this->rootDir}/{$file}";
            }
        }

        private function defaultFileLoad(){
            return[
                'app/core/Router.php',
                'app/routers.php',
            ];
        }
    }
?>