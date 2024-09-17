window.addEventListener('load', function() {
    const slider = document.querySelector('.slider');
    const sliderWrapper = document.querySelector('.slider-wrapper');
    const sliderItems = document.querySelectorAll('.slider-item');
    const prevBtn = document.querySelector('.slider-prev');
    const nextBtn = document.querySelector('.slider-next');
    const sliderLength = sliderItems.length;
    let positionX = 0;
    let index = 0;
    let sliderItemWidth = window.innerWidth;
    let autoSlideInterval;
    let resizing = false;

    function updateSliderItemWidth() {
        sliderItemWidth = window.innerWidth;
        positionX = -sliderItemWidth * index;
        sliderWrapper.style.transform = `translateX(${positionX}px)`;
    }

    function changeSlide(direction) {
        const currentSlide = sliderItems[index];
        const animateClasses = [
            'animate__fadeInDown', 'animate__backInUp', 'animate__rollIn', 'animate__backInRight',
            'animate__zoomInUp', 'animate__backInLeft', 'animate__rotateInDownLeft',
            'animate__backInDown', 'animate__zoomInDown', 'animate__fadeInUp', 'animate__zoomIn'
        ];

        currentSlide.classList.remove(...animateClasses);
        // sliderItems.forEach(item => item.style.display = 'none');

        if (direction === 1) {
            index++;
            if (index >= sliderLength) {
                index = 0;
            }
        } else if (direction === -1) {
            index--;
            if (index < 0) {
                index = sliderLength - 1;
            }
        }

        positionX = -sliderItemWidth * index;
        const randomAnimateClass = animateClasses[Math.floor(Math.random() * animateClasses.length)];

        sliderItems[index].classList.add('animate__animated', randomAnimateClass);
        sliderItems[index].style.display = 'block';
        sliderWrapper.style.transform = `translateX(${positionX}px)`;
    }

    function startAutoSlide() {
        autoSlideInterval = setInterval(function() {
            changeSlide(1);
        }, 4000);
    }

    function stopAutoSlide() {
        clearInterval(autoSlideInterval);
    }

    // Cập nhật kích thước slider ngay lập tức khi thay đổi kích thước cửa sổ
    window.addEventListener('resize', function() {
        if (resizing) return;

        resizing = true;
        requestAnimationFrame(function() {
            updateSliderItemWidth();
            resizing = false;
        });
    });

    // Dừng tự động chuyển slide khi người dùng tương tác
    slider.addEventListener('mousedown', stopAutoSlide);
    slider.addEventListener('mouseup', startAutoSlide);
    slider.addEventListener('mouseleave', startAutoSlide);

    nextBtn.addEventListener('click', function() {
        changeSlide(1);
    });

    prevBtn.addEventListener('click', function() {
        changeSlide(-1);
    });

    // Bắt đầu hiệu ứng tự chạy khi trang web được tải
    startAutoSlide();

    // Khởi tạo khi trang được tải
    updateSliderItemWidth();
});


// list
$('.news-list').slick({
    slidesToShow: 2,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 2000,
    arrows: false, // Ẩn cả hai nút điều hướng
    responsive: [
    {
        breakpoint: 576, // Kích thước màn hình dưới 768px (điện thoại di động)
        settings: {
          slidesToShow: 1 // Hiển thị 1 slide trên mobile
        }
    },
    {
        breakpoint: 1000, // Kích thước màn hình dưới 768px (điện thoại di động)
        settings: {
          slidesToShow: 2 // Hiển thị 1 slide trên mobile
        }
    }
      // Các điều kiện điều chỉnh khác cho các kích thước màn hình khác
    ]
});


// lấy sản phẩm
//lúc web vừa load thì mặc định là sản phẩm mới
document.addEventListener('DOMContentLoaded', function() {
    var defaultStatus = "new";
    getProducts(defaultStatus);
});

$('.list_sanpham a').on('click', function() {
    // Lấy data-id của thẻ <a> được click
    var status = $(this).data('id');
    // alert(status);
    getProducts(status);
    
    // Hủy active cho tất cả các thẻ <a>
    $('.list_sanpham a').removeClass('active');
    
    // Active cho thẻ <a> được click
    $(this).addClass('active');
});

function getProducts(status) {
    // Sử dụng AJAX để gửi yêu cầu đến models
    $.ajax({
        type: "POST",
        url: "../../controllers/Home.php", // Đường dẫn đến file PHP xử lý
        data: { action: "getProductByStatus", status: status },
        success: function(response) {
            var products = JSON.parse(response.trim());
            // console.log(products);
            // Lấy phần tử container
            var productContainer = $('.product-list-js');
            var formatter = new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND',
                currencyDisplay: 'symbol'
            });
            // Xóa nội dung hiện tại trong container (nếu cần)
            productContainer.empty();
            // Thêm HTML của sản phẩm vào container
            $.each(products, function(index, product) {
                console.log(product);
                if (product.file_name != '') {
                    var imagePath = product.file_name; // Lấy đường dẫn ảnh đầu tiên
                    // var productUrl = "{{ route('products.show', ['id' => ':productId']) }}";
                    // productUrl = productUrl.replace(':productId', product.id);
                    var productHtml = `
                        <div class="col-lg-3 col-md-6 col-6 product-item my-3">
                            <div class="box-product">
                                <p class="pic-product">
                                    <a href=" ${product.slug_name}" class="text-decoration-none scale-img">
                                        <img src="../../../public/front/image/${imagePath}" alt="">
                                    </a>
                                </p>
                                <h3>
                                    <a href=" ${product.slug_name}" class="text-decoration-none name-product">
                                    ${product.name}
                                    </a>
                                </h3>
                                ${product.price > 0 ?
                                `<p class="price-product">
                                    <span class="price-new">${formatter.format(product.price * (100 - parseInt(product.discount_percent)) / 100)}</span>
                                    <span class="price-old">${formatter.format(product.price)}</span>
                                    <span class="price-per">-${product.discount_percent}%</span>
                                </p>` : `
                                <p class="price-product">
                                    <span class="price-new">
                                        Liên Hệ 
                                    </span>
                                    <span class="price-per">New</span>
                                </p>
                                `}
                                <span  class="cart-add addcart transition d-flex align-items-center justify-content-center" data-product-id="${product.product_id}">
                                    <img class="px-2" src="../../../public/front/image/icon-giohang.png" alt="Thêm vào Giỏ Hàng" srcset="">Thêm
                                    Vào Giỏ Hàng
                                </span>
                            </div>
                        </div>
                    `;
                } 
                productContainer.append(productHtml);
            });
            if (products.length > 8) {
                var seeMoreHtml = `
                    <a class="btn btn-see-more" href="#" >
                        Xem tất cả SP+
                    </a>
                `;
                productContainer.after(seeMoreHtml);
            }
            // Xử lý phản hồi từ server (nếu cần)
        },
        error: function(error) {
            console.error(error);
        }
    });
}

