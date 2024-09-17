// Thêm SP Vào Giỏ Hàng
$(document).ready(function () {
    var cartItems = JSON.parse(localStorage.getItem('cartItems')) || {};
    // Xử lý sự kiện khi người dùng rời khỏi trang
    $(window).on('beforeunload', function () {
        // Lưu dữ liệu vào localStorage trước khi người dùng rời khỏi trang
        localStorage.setItem('cartItems', JSON.stringify(cartItems));
        // localStorage.clear();
    });
    $('.count-cart').html(Object.keys(cartItems).length);
    // cập nhật thông tin sản phẩm trong giỏ hàng
    var formatter = new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
        currencyDisplay: 'symbol'
    });
    // xử lí dữ liệu trang giỏ hàng
    if (window.location.href.includes('gio-hang')) {
        if(Object.keys(cartItems).length > 0){
            var productContainer = $('.procart-list');
            var total = 0;
            // Xóa nội dung hiện tại trong container (nếu cần)
            productContainer.empty();
            // Thêm HTML của sản phẩm vào container
            showProductCart(productContainer, cartItems, formatter, total);
            $('.total-price').html(formatter.format(total));
            $('.delete-product-item').on('click', function () {
                if (confirm("Bạn có muốn xóa sản phẩm này không?")) {
                    // Lấy phần tử đã được click
                    var clickedElement = $(event.target);
    
                    // Lấy hàng sản phẩm chứa phần tử đã click
                    var productRow = clickedElement.closest('tr');
                    var productKey = productRow.data('product-key');
    
                    if (cartItems[productKey]) {
                        // xóa
                        delete cartItems[productKey];
                        productRow.remove(); // Sử dụng phương thức remove() để xóa phần tử hàng
                        console.log('Đã xóa thành công phần tử với khóa: ' + productKey);
                        if(Object.keys(cartItems).length === 0){
                            $('.form-cart').empty();
                            var html = `<div class="nocart text-center">
                                            <img src="../../../public/front/image/no_cart.png" alt="" srcset="">
                                            <span class="mt-10 d-block text-align-center title-nocart">Giỏ hàng trống</span>
                                        </div>`;
                            $('.form-cart').html(html);
                        }
                    }
                    updateTotalPrice(cartItems, formatter);
                }
            });
            updatePriceProduct(productContainer, cartItems, formatter);
            updateTotalPrice(cartItems, formatter);
        }else{
            $('.form-cart').empty();
            var html = `<div class="nocart text-center">
                            <img src="../../../public/front/image/no_cart.png" alt="" srcset="">
                            <span class="mt-10 d-block text-align-center title-nocart">Giỏ hàng trống</span>
                        </div>`;
            $('.form-cart').html(html);
        }
    }

    // Thêm sản phẩm vào giỏ hàng và tạo giỏ hàng
    $('.addcart').click(function () {
        var product_id = $(this).attr('data-product-id');
        var qty_pro = $('.quantity-pro-detail .qty-pro').length ? $('.quantity-pro-detail .qty-pro').val() : 1;
        console.log(product_id);
        console.log(qty_pro);

        // Kiểm tra xem sản phẩm đã được thêm vào giỏ hàng chưa
        $.ajax({
            type: 'POST',
            url: '../../controllers/Home.php ',
            data: { action: "addToCart", product_id: product_id },
            // dataType: 'json',
            success: function (response) {
                // Xử lý phản hồi từ server (nếu cần)
                var productData = JSON.parse(response);
                console.log(productData[0].name);
                var keys = Object.keys(cartItems);
                // $('.count-cart').html(keys.length);
                // $('.procarrt-list').html(keys.length);

                var newCartItemIndex = -1; // Khởi tạo biến chỉ số mới

                for (var i = 0; i < keys.length; i++) {
                    var key = keys[i];
                    if (cartItems[key].id == parseInt(product_id)) {
                        // Nếu tìm thấy sản phẩm, cập nhật số lượng
                        cartItems[key].quantity += parseInt(qty_pro);
                        newCartItemIndex = i; // Đặt chỉ số mới khi tìm thấy sản phẩm
                        // break; // Thoát khỏi vòng lặp khi đã xử lý xong
                    }
                }

                // Nếu sản phẩm không tồn tại, thêm mới vào giỏ hàng
                if (newCartItemIndex === -1) {
                    cartItems[keys.length] = {
                        'id': parseInt(product_id),
                        'product_name': productData[0].name,
                        'file_name': productData[0].file_name,
                        'quantity': parseInt(qty_pro),
                        'price': parseFloat(productData[0].price),
                        'discount_percent': productData[0].discount_percent,
                    };
                }

                // Hiển thị thông tin giỏ hàng
                console.log(JSON.stringify(cartItems));
                $('.count-cart').html(Object.keys(cartItems).length);
            },
            error: function (error) {
                // Xử lý lỗi nếu có
                console.error("Không Gửi được");
            }
        });
    });
    // xóa sp trong cart
});
// Hàm Cập Nhật Số lƯợng sản phẩm


function updatePriceProduct(productContainer, cartItems, formatter) {
    productContainer.on('click', '.quantity-minus-pro-detail, .quantity-plus-pro-detail', function (event) {
        // Lấy phần tử đã được click
        var clickedElement = $(event.target);

        // Lấy hàng sản phẩm chứa phần tử đã click
        var productRow = clickedElement.closest('tr');
        var qty = productRow.find('.quantity-pro-detail .qty-pro');
        // lấy key sản phẩm trong cartItems
        var productKey = productRow.data('product-key');
        var productPrice = cartItems[productKey].discount_percent ? (cartItems[productKey].price * (100 - cartItems[productKey].discount_percent) / 100) : cartItems[productKey].price;
        console.log('key : ' + productKey + 'price' + productPrice);
        // Xác định xem phần tử đã click là nút trừ hay nút cộng
        if (clickedElement.hasClass('quantity-minus-pro-detail')) {
            if (qty.val() > 1) {
                var currentValue = parseInt(qty.val());
                qty.val(currentValue - 1);
            } else {
                qty.val(1);
            }
        } else if (clickedElement.hasClass('quantity-plus-pro-detail')) {
            var currentValue = parseInt(qty.val());
            qty.val(currentValue + 1);
            // Xử lý khi nút cộng được click
        }
        productRow.find('.pro-price').html(formatter.format(parseInt(qty.val()) * productPrice));
        cartItems[productKey].quantity = parseInt(qty.val());
        localStorage.setItem('cartItems', JSON.stringify(cartItems));
        updateTotalPrice(cartItems, formatter);

    });
}
// hàm tính tổng tiền
function updateTotalPrice(cartItems, formatter) {
    var totalPrice = 0;
    // Lặp qua tất cả sản phẩm và tính tổng số tiền
    Object.keys(cartItems).forEach(function (productKey) {
        var productPrice = cartItems[productKey].discount_percent ? (cartItems[productKey].price * (100 - cartItems[productKey].discount_percent) / 100) : cartItems[productKey].price;
        var priceItem = cartItems[productKey].quantity * productPrice;
        totalPrice += priceItem || 0;
    });

    // Hiển thị tổng số tiền trong HTML hoặc làm theo yêu cầu của bạn
    $('.total-price').html(formatter.format(totalPrice));
}


// hiển thị sản phẩm giỏ hàng
function showProductCart(productContainer, cartItems, formatter, total) {
    for (var i = 0; i < Object.keys(cartItems).length; i++) {
        var key = Object.keys(cartItems)[i];
        var priceItem = cartItems[key].discount_percent ? (cartItems[key].price * (100 - parseInt(cartItems[key].discount_percent)) / 100) * cartItems[key].quantity : cartItems[key].price * cartItems[key].quantity;
        console.log('Product ID:', cartItems[key].id);
        console.log('Product Name:', cartItems[key].product_name);
        console.log('Product file:', cartItems[key].file_name);
        console.log('Product quantity:', cartItems[key].quantity);
        console.log('Product Price:', cartItems[key].price);
        if (cartItems[key].file_name != '') {
            var imagePath = cartItems[key].file_name; // Lấy đường dẫn ảnh đầu tiên
            // var productUrl = "{{ route('products.show', ['id' => ':productId']) }}";
            // productUrl = productUrl.replace(':productId', product.id);
            var productHtml = `
                <tr data-product-key=${key}>
                    <td class="p-4">
                        <div class="media align-items-center ">
                            <img src="../../../public/front/image/${imagePath}"
                                class="d-block ui-w-40 ui-bordered mr-4 border" alt="" style="width:85px; height:85px;">
                        </div>
                        <div class="delete-product ">
                            <a class="text-pro delete-product-item ">
                                <i class="fa-regular fa-circle-xmark mr-3"></i> Xóa
                            </a>
                        </div>
                    </td>
                    <td class="text-pro font-weight-bold align-middle p-4">${cartItems[key].product_name}</td>
                    <td class="align-middle p-4 m-0-auto">
                        <div class="quantity-pro-detail">
                            <span class="quantity-minus-pro-detail">-</span>
                            <input type="number" class="qty-pro" id="qty-pro" min="1" value="${cartItems[key].quantity}" readonly="">
                            <span class="quantity-plus-pro-detail">+</span>
                        </div>
                    </td>
                    <td class="text-pro pro-price text-danger font-weight-semibold align-middle p-4">${formatter.format(priceItem)}</td>
                </tr>
                `;
            total += priceItem;
        }
        productContainer.append(productHtml);
    }
    // updateTotalPrice(cartItems, formatter);

}
