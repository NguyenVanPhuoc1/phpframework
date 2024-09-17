//Hàm để kiểm tra và khởi tạo menu
document.addEventListener(
    "DOMContentLoaded", () => {
        new Mmenu( "#menu", {
            "offCanvas": {
                "position": "left"
            },
            "theme": "light"
        });
    }
);

function checkMenu(){
    var hamburgerElement = document.getElementById("hamburger");

    // Thêm class animate__tada để kích hoạt hiệu ứng
    hamburgerElement.classList.add("animate__tada");

    // Đặt timeout để xóa class sau khi hiệu ứng kết thúc
    setTimeout(function() {
        hamburgerElement.classList.remove("animate__tada");
    }, 1000); // Thời gian timeout, có thể điều chỉnh theo thời gian hiệu ứng
}

// slider



// scroll to top
const backToTopBtn = document.querySelector('.scrollToTop');
    window.addEventListener("scroll", () => {
        if (window.scrollY > 200) {
            backToTopBtn.classList.add("show");
        } else {
            backToTopBtn.classList.remove("show");
        }
    });

    backToTopBtn.addEventListener("click", () => {
        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
});
document.querySelector('.zalo').addEventListener('mouseover', function(){
    document.querySelector('.zalo').classList.add('animate__animated' , 'animate__rubberBand');

});

document.querySelector('.zalo').addEventListener('mouseout', function(){
    document.querySelector('.zalo').classList.remove('animate__animated' , 'animate__rubberBand');

});

// truyền id tin tức dưới dạng form ẩn
function submitFormNews(id) {
    var form = document.createElement('form');
    form.method = 'post';
    if(window.location.href.includes('tin-tuc')){
        form.action = window.location.href;
    }else{
        form.action = window.location.href + 'tin-tuc';

    }

    var slugInput = document.createElement('input');
    slugInput.type = 'hidden';
    slugInput.name = 'id';
    slugInput.value = id;

    form.appendChild(slugInput);

    document.body.appendChild(form);
    form.submit();
}

// tìm kiếm sản phẩm
function performSearch(formId){
    document.querySelector('#' + formId).submit();
}