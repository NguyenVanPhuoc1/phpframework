<?php
    use app\core\QueryBuilder;
    use app\core\Registry;
    $config = Registry::getIntance()->config;
    $menu = QueryBuilder::table('tb_menu')
    ->leftJoin('category', 'tb_menu.id', '=', 'category.menu_id')
    ->select('tb_menu.id as menu_id', 'tb_menu.menu_name', 'category.menu_id as menu_id', 'category.cate_name', 'category.slug_cate')
    ->get();
    $organizedData = [];

    foreach ($menu as $result) {
        if($result['menu_id'] !== null){

            $menuId = $result['menu_id'];
            
            // Nếu menu_id chưa có trong $organizedData, thêm mới
            if (!isset($organizedData[$menuId])) {
                $organizedData[$menuId] = [
                    'menu_id' => $result['menu_id'],
                    'menu_name' => $result['menu_name'],
                    'categories' => []
                ];
            }
            
            // Nếu cate_name không phải là null, thêm vào danh sách categories
            if ($result['cate_name']) {
                $organizedData[$menuId]['categories'][] = [
                    'cate_name' => $result['cate_name'],
                    'slug_cate' => $result['slug_cate']
                ];
            }
        }
    }
    
    // Chuyển đổi kết quả thành mảng có chỉ số numeri
    $organizedData = array_values($organizedData);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>
        <?php echo isset($pageTitle) ? $pageTitle : "Page Null"; ?>
    </title>
    <link rel="icon" href="<?php echo "front/image/logo_web.png" ?>" type="image/x-icon">


    <!-- boostrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <!-- icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        crossorigin="anonymous" />

    <!-- menu.css -->
    <link rel="stylesheet" href="front/css/mmenu.css">
    <link rel="stylesheet" href="front/css/trangchu.css">
    <link rel="stylesheet" href="front/css/shop.css">
    <link rel="stylesheet" href="front/css/loader.css">
    <link rel="stylesheet" href="front/css/modal.css">
    <!-- pagination -->
    <!-- <link rel="stylesheet" href="../../../public/pagination.css"> -->
    <!-- animate css -->
    <link rel="stylesheet" type="text/css"
        href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script type="text/javascript"
        src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
    <!-- noUiSlider -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/14.6.4/nouislider.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/14.6.4/nouislider.min.js"></script>

    <!-- aos animation -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">

    <!-- magiczoom -->
    <link rel="stylesheet" href="front/css/magiczoomplus.css">
    <script src="front/js/magiczoomplus.js"></script>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-M574KV18V5"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'G-M574KV18V5');
    </script>
</head>

<body>
    <!-- menu -->
    <nav class="menu" id="menu">
        <ul>
            
            <li>
                <span>Account</span>
                <?php if (isset($_SESSION['userLogin'])): ?>
                    <ul class=" border-0">
                        <li><a href="#">Cài Đặt</a></li>
                        <li><a href="<?php echo $config['basePath'] . '/gio-hang' ?>">Giỏ Hàng</a></li>
                        <li><a href="#">Đăng Xuất</a></li>
                    </ul>
                <?php else: ?>
                    <ul class=" border-0">
                        <li><a href="<?php echo $config['basePath'] . '/login' ?>">Đăng Nhập</a></li>
                        <li><a href="<?php echo $config['basePath'] . '/register' ?>">Đăng Kí</a></li>
                    </ul>
                <?php endif; ?>
            </li>
        </ul>
    </nav>

    <!-- page >> desktop -->
    <div class="page">
        <!-- header -->
        <nav class="header navbar-fixed-top">
            <div class="hearder-top">
                <div class="wrap-content d-flex align-items-center justify-content-between">
                    <p class="info-header slogan">
                        <marquee>Xin chào bạn đã đến trang web của tôi</marquee>
                    </p>
                    <p class="info-header address">
                        <i class="fa-solid fa-location-dot mx-2"></i> 53 Võ Văn Ngân
                    </p>
                    <p class="info-header address">
                        <i class="fa-solid fa-phone-volume mx-2"></i> <span class="text-danger fw-bold">HotLine:</span>
                        0123456789
                    </p>
                </div>
            </div>
            <?php if (strpos($_SERVER['REQUEST_URI'], 'login') === false && strpos($_SERVER['REQUEST_URI'], 'register') === false): ?>
                <!-- menu-responsive -->
                <div class="menu-res">
                    <div class="menu-res-bar">
                        <a class="animate__animated " id="hamburger" href="#menu" title="Menu"
                            onclick="checkMenu()"><span></span></a>
                        <div class="search">
                            <form action="<?php echo $config['basePath'] . '/tim-kiem' ?>" method="get" class="searchPro" id="form1">
                                <input type="text" name="searchProduct" id="keyword" placeholder="Nhập từ khóa cần tìm..."
                                    required>
                                <p id="searchIcon" onclick="performSearch('form1')">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                </p>
                            </form>
                        </div>
                        <div class="logo-mobile">
                            <img src="front/image/logo_web.png" alt="" srcset="">
                        </div>
                    </div>
                </div>
                <!-- header -main -->
                <div class="header-main">
                    <div class="wrap-content d-flex">
                        <div class="col-3">
                            <div class="header-main-logo">
                                <img src="front/image/logo_web.png" alt="Logo" srcset="">
                            </div>
                        </div>
                        <div class="col-9  blockquote mb-0">
                            <div class="row my-3 ">
                                <div class="col-6 ">
                                    <div class="search">
                                        <form action="<?php echo $config['basePath'] . '/tim-kiem' ?>" method="get" class="searchPro"
                                            id="form2">
                                            <input type="text" name="searchProduct" id="keyword"
                                                placeholder="Nhập từ khóa cần tìm..." required>
                                            <p id="searchIcon" onclick="performSearch('form2')">
                                                <i class="fa-solid fa-magnifying-glass"></i>
                                            </p>
                                        </form>
                                    </div>
                                </div>
                                <?php if (isset($_SESSION['userLogin'])): ?>
                                    <div class="col-2 text-center">
                                        <i class="fa-regular fa-heart mx-2 text-success"></i>My List
                                    </div>
                                <?php endif ?>
                                <div class="col-2 text-center">
                                    <li class="nav-item mr-3 dropdown">
                                        <a href="#"><i class="fa-solid fa-person-shelter mx-2 text-success"></i>Account</a>
                                        <?php if (isset($_SESSION['userLogin'])): ?>
                                            <ul class="dropdown-menu border-0">
                                                <li class="nav-item">
                                                    <a class="nav-link" href="#">
                                                        Cài Đặt
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link"
                                                        href="#">
                                                        Đăng Xuất
                                                    </a>
                                                </li>
                                            </ul>
                                        <?php else: ?>
                                            <ul class="dropdown-menu border-0">
                                                <li class="nav-item">
                                                    <a class="nav-link" href="<?php echo $config['basePath'] . '/login' ?>">
                                                        Đăng Nhập
                                                    </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a class="nav-link" href="<?php echo $config['basePath'] . '/register' ?>">
                                                        Đăng Kí
                                                    </a>
                                                </li>
                                            </ul>
                                        <?php endif; ?>
                                    </li>
                                </div>
                                <div class=" col-2 cart text-center">
                                    <a class="cart-fixed text-decoration-none text-success"
                                        href="<?php echo $config['basePath'] . '/gio-hang' ?>" title="Giỏ hàng">
                                        <i class="fa-solid fa-cart-shopping mx-2 "></i>
                                        <span class="count-cart">0</span>
                                        Cart
                                    </a>
                                </div>
                            </div>
                            <div class="row my-3">
                                <ul class="d-flex mb-2 mb-lg-0 justify-content-between">
                                    
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            <?php else: ?>
                <script>
                    $('.menu-res').css("display", "none");
                    $('.header-main').css("display", "none");
                </script>
            <?php endif ?>
        </nav>