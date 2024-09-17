<?php
    //dinh nghĩa controller nằm trong không gian tên dễ phân biệt + autoload
    //quy tac dat theo phan cap danh muc
    namespace app\controllers;
    use app\core\Controller;
    use app\core\QueryBuilder;
    use \App;

    class HomeController extends Controller {
        function __construct(){
            // echo 'Home Controller';
            parent::__construct();
        }
        public function index(){
            // var_dump($config);die();
            $builder = QueryBuilder::table('user')
            ->where('user.email','=','phuoc@gmail.com')
            ->update([
                'id' => 1,
                'name' => 'Phuoc',
            ]);
            echo '<pre>';
            print_r($builder);
            $this->render('frontend/index',[
                'ten' => 'phuoc',
                'male' => 'nam',
                'header' => $this->renderPartial('frontend/layout/header'),
            ]);
        }
    }
?>