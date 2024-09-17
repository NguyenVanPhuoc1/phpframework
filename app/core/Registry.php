<?php 
    namespace app\core;

    class Registry{
        private static $intance;
        private $storage;

        //áp dụng singleton pattern giới hạn việc khởi tạo class
        private function __construct(){

        }    

        public static function getIntance(){
            if(!isset(self::$intance)){
                //khoi tao chinh no
                self::$intance = new self;
            }
            return self::$intance;
        }
        //khởi tạo các phương thức gọi tự động khi khởi tạo đối tượng
        public function __set($name, $value){
            if(!isset($this->storage[$name])){
                $this->storage[$name] = $value;
            }else{
                die("Can't not set {$value} to {$name} already exists");
            }
        }
        public function __get($name){
            if(isset($this->storage[$name])){
                return $this->storage[$name];
            }
            return null;
        }
    }

?>