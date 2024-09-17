<?php
    namespace app\core;
    class Request
    {
        // Lấy tất cả query string (tương tự $_GET trong PHP)
        protected $query;

        // Lấy tất cả dữ liệu từ POST (tương tự $_POST trong PHP)
        protected $request;

        // Lấy tất cả các tham số trong URL (ví dụ: từ route)
        protected $params;

        public function __construct() {
            $this->query = $_GET;
            $this->request = $_POST;
            $this->params = []; // Khởi tạo mảng params, bạn sẽ xử lý từ router sau
        }

        /**
         * Lấy dữ liệu từ query string hoặc POST theo tên.
         * Nếu không có giá trị, trả về giá trị mặc định (null hoặc do người dùng quy định).
         */
        public function input($key, $default = null) {
            if (isset($this->query[$key])) {
                return $this->query[$key];
            }
            if (isset($this->request[$key])) {
                return $this->request[$key];
            }
            return $default;
        }

        /**
         * Gán params từ router vào request
         */
        public function setParams($params) {
            $this->params = $params;
        }

        /**
         * Lấy dữ liệu từ params được truyền từ router (các phần tử động của URL)
         */
        public function param($key, $default = null) {
            if (isset($this->params[$key])) {
                return $this->params[$key];
            }
            return $default;
        }
    }

?>
