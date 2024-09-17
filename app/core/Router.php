<?php 
    use app\core\Registry;
    use app\core\Request;
    class Router{
        private static $routers = [];
        private $basePath;

        function __construct($basePath){
            $this->basePath = $basePath;
        }
        // lay duong dan url
        private function getRequestURL(){
            //App:: dùng để gọi phương thức tính static va muon dung global namespace thì dùng dấu \
            // $url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
            $url = $_SERVER['REQUEST_URI'] ?? '/';// Sử dụng toán tử null coalescing
            $url = str_replace($this->basePath, '', $url);
            $url = $url === '' || empty($url) ? '/' : $url;//thay'/' hien thi trang chu       
            return $url;
        }
        ///pthuc get router
        private function getRequestMethod(){
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            return $method;
        }

        private static function addRouter($method,$url,$action){
            self::$routers[] = [$method,$url, $action];
        }
        //call with get method
        public static function get($url, $action){
            self::addRouter('GET',$url, $action);
        }
        //call with post method
        public static function post($url, $action){
            self::addRouter('POST',$url, $action);
        }
        //call with ...method 
        public static function any($url, $action){
            self::addRouter('GET|POST',$url, $action);
        }

        public function map(){
            //$requestURL là url trên giao diện, $url: là url khai báo trong App
            $checkRoute = false;
            $requestURL = $this->getRequestURL();
            $requestMethod = $this->getRequestMethod();
             // Tách phần query string ra khỏi $requestURL
            $urlParts = parse_url($requestURL);
            $requestPath = $urlParts['path']; // Lấy phần path của URL, bỏ qua query string
            
            // lấy tat ca router
            $routers = self::$routers;
            $params = [];
            foreach($routers as $route){
                list($method,$url,$action) = $route;
                // ham kiem tra vi tri cua chuoi
                if(strpos($method,$requestMethod ) === false){
                    continue;
                };
                // var_dump(strpos($url,'{') === false);die();
                if($url === '*'){
                    $checkRoute = true;
                }elseif(strpos($url,'{') === false){
                    // print_r('không tồn tại {' . $requestURL);die();
                
                    //kiem tra params {} trong url
                    if(strcmp(strtolower($url), strtolower($requestPath)) === 0){
                        //kiem tra action co phai là function hay k
                        $checkRoute = true;
                    }
                    else{
                        continue;
                    }
                }elseif(strpos($url,'}') === false){
                    continue;
                }else{
                    //truong hop co {}
                    $routeParams = explode('/', $url);
                    $requestParams = explode('/', $requestPath);//url tren trang
                    if(end($requestParams) !== ''){

                        if(count($routeParams) !== count($requestParams)){
                            continue;
                        }
                        //lấy từng params
                        foreach($routeParams as $k => $rp) {
                            // Dùng regex kiểm tra xem chuỗi có bắt đầu bằng { và kết thúc bằng }
                            if(preg_match('/^{\w+}$/', $rp)) {
                                // Nếu khớp, lưu giá trị tương ứng từ $requestParams vào $params
                                $params[] = $requestParams[$k];
                            }
                        }
                        // echo '<pre>';
                        // print_r($params); 
                        // echo '</pre>';
                        $checkRoute = true;
                    }
                    

                }
                //kiem tra dương dan k ton tai
                if($checkRoute === true){
                    $request = new Request();
                    if(is_callable($action)){
                        // $action();//$action là 1 closure
                        // print_r($params);die();
                        call_user_func_array($action, array_merge([$request], $params));
                    }elseif(is_string($action)){
                        // $action();//$action là 1 controller
                        $this->compieRoute($action,array_merge([$request], $params));
                    }
                    return;
                }else{
                    continue;
                }
            }
            return;
        }
        private function getParam($routeParams, $requestParams) {
            $params = []; // Khởi tạo mảng $params để lưu trữ các tham số
        
            // Duyệt qua từng tham số trong $routeParams
            foreach($routeParams as $k => $rp) {
                // Dùng regex kiểm tra xem chuỗi có bắt đầu bằng { và kết thúc bằng }
                if(preg_match('/^{\w+}$/', $rp)) {
                    // Nếu khớp, lưu giá trị tương ứng từ $requestParams vào $params
                    $params[] = $requestParams[$k];
                }
            }
            // Trả về mảng chứa các tham số đã lấy từ URL
            return $params;
        }
        
        private function compieRoute($action,$params){
            if(count(explode('@', $action)) != 2){
                die('Router error');
            }
            $className = explode('@', $action)[0];
            $methodName = explode('@', $action)[1];

            $classNamespace = "app\\controllers\\{$className}";
            //kiem tra class da duoc dinh nghia(khoi tao) hay chưa
            if(class_exists($classNamespace)){
                // echo 'class exist';
                Registry::getIntance()->controller = $className;
                $object = new $classNamespace;//tao class controller
                if(method_exists($classNamespace, $methodName)){
                    //goi ham trong object class $classNamespace da dươc tao
                    Registry::getIntance()->action = $methodName;
                    call_user_func_array([$object, $methodName], $params);
                }else{
                    die("Method {$methodName} not found");
                }
            }else{
                die("Class {$classNamespace} not found");
            }
        }

        public function run(){
            // echo 'Router running';
            $this->map();
        }
    }
?>