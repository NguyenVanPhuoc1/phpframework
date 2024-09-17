<?php
    use app\core\Controller;
    use app\core\QueryBuilder;
    use app\core\Request;


    // Router::get('/',function(){
    //     $ct = new Controller;
    //     $ct->render('frontend/index',['header' => $ct->render('frontend/layout/header')]);
    //     // echo '<pre> ';
    //     // print_r($ct->render('layout/main'));die();
    //     $builder = QueryBuilder::table('user')->select('cot1','cot2')->distinct()
    //     //->join('bang1','abc.id','=','bang1.abc_id')->leftJoin('bang2','abc.id','=','bang2.abc_id')
    //     //->where('cot1' , '=' , 20)->where('cot2' , '<' , 30)
    //     ->groupBy('cot1','cot2')->orderBy('cot1','ASC')->orderBy('cot2','DESC')
    //     ->limit(10)->offset(5)->get();
    //     echo '<pre>';
    //     print_r($builder);
    // });
    
    Router::get('/{slug}',function(Request $request,$slug){
        print_r($slug);
        // print_r($request -> input('abc','Phuoc'));

        $ct = new Controller;
        $ct->render('admin/user/adduser', ['username' => 'JohnDoe']);
    });

    Router::get('/','HomeController@index');

    Router::any('*',function(){
        echo '404 not found';
    });
?>