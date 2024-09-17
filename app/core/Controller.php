<?php
namespace app\core;
use \App;
use app\core\AppException;
class Controller {
    private $layout = null;
    private $config ;

    public function __construct() {
        // Layout mặc định khi chạy project
        $this->config = Registry::getIntance()->config;
        $this->layout = $this->config['layout'];
    }
    public function getConfig(){
        return $this->config;
    }

    public function setLayout($layout) {
        $this->layout = $layout;
    }

    public function redirect($url, $isEnd = true, $responseCode = 302) {
        // Redirect đến URL mới
        header("Location: {$url}", true, $responseCode);
        if ($isEnd) {
            die();
        }
    }
    // view là đường dẫn trong folder
    public function render($view, $data = null) {
        $rootDir = $this->config['rootDir'];

        // Lấy nội dung của view
        $content = $this->getViewContent($view, $data);
        // Nếu layout được thiết lập
        if ($content) {
            echo $content;  
        } 
        else {
            $layoutPath = "{$rootDir}/app/views/{$this->layout}.php";
            if (file_exists($layoutPath)) {
                require $layoutPath;
            }
        }
    }

    // Truyền biến vào view, hỗ trợ cấu trúc folder tuỳ ý
    public function getViewContent($view, $data = null) {
        $rootDir = $this->config['rootDir'];

        // Truyền dữ liệu vào view
        if (is_array($data)) {
            extract($data, EXTR_PREFIX_SAME, "data");
        }
        // Chấp nhận đường dẫn view có thể có nhiều cấp folder, ví dụ: admin/user/adduser
        $viewPath = "{$rootDir}/app/views/{$view}.php";
        // var_dump(file_exists($viewPath));die();
        
        // Kiểm tra sự tồn tại của file view
        if (file_exists($viewPath)) {
            ob_start();
            require $viewPath;
            return ob_get_clean();
        } else {
            // Ném lỗi nếu không tìm thấy view
            throw new AppException("View không tồn tại: {$viewPath}");
        }
    }

    // Trường hợp không có controller, render một phần view
    public function renderPartial($view, $data = null) {
        $rootDir = $this->config['rootDir'];

        if (is_array($data)) {
            extract($data, EXTR_PREFIX_SAME, "data");
        }

        $viewPath = "{$rootDir}/app/views/{$view}.php";

        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            throw new AppException("Partial view không tồn tại: {$viewPath}");
        }
    }

}
?>