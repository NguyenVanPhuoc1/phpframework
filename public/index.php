<?php 
    require_once dirname(__FILE__).'/../app/core/App.php';
    $config = require_once dirname(__FILE__).'/../config/main.php';
    // App::setConfig($config);
    // echo '<pre>'; print_r(App::getConfig());
    $app = new App($config);
    $app->run();
?>
