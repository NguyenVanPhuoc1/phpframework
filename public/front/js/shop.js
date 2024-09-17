var slider = document.getElementById('slider');
const minValue = document.getElementById('min-value');
const maxValue = document.getElementById('max-value');

noUiSlider.create(slider, {
    start: [20, 600],
    connect: true,
    range: {
        'min': 1,
        'max': 999
    }
});

slider.noUiSlider.on('update', function (values) {
    const [min, max] = values;
    minValue.textContent = `${Math.ceil(min)}`;
    maxValue.textContent = `${Math.ceil(max)}`;
});


var sortBySelect = document.querySelector('.sortBy');
var categoryCheckboxes = document.querySelectorAll('input[type="checkbox"]');
var filler_price = document.querySelector('.btn-check-result');

// Biến để lưu trữ giá trị đã chọn
var selectedValues = {
    sortBy: null,
    categories: [],
    min_price: null,
    max_price: null,
};


// Hàm cập nhật URL
function updateURL() {
    localStorage.setItem('myData', JSON.stringify(selectedValues));

    var params = new URLSearchParams();

    if (selectedValues.sortBy !== null) {
        params.set('sortBy', selectedValues.sortBy);
    }

    if (selectedValues.categories.length > 0) {
        params.set('categories', selectedValues.categories.join(','));
    }
    if (selectedValues.min_price !== null && selectedValues.max_price !== null) {
        params.set('min_price', selectedValues.min_price);
        params.set('max_price', selectedValues.max_price);
    }

    var currentURL = window.location.href.split('?')[0];
    var newURL = currentURL + '?' + params.toString();

    window.history.replaceState({}, '', newURL);

    setTimeout(function () {
        document.querySelector('.lds-roller').style = `display: flex;`;
        window.location.href = newURL;
    }, 1500);
}


sortBySelect.addEventListener('change', function () {
    selectedValues.sortBy = sortBySelect.value;
    updateURL();
});

categoryCheckboxes.forEach(function (checkbox) {
    checkbox.addEventListener('change', function () {
        if (checkbox.checked) {
            selectedValues.categories.push(checkbox.value);
        } else {
            selectedValues.categories = selectedValues.categories.filter(function (value) {
                return value !== checkbox.value;
            });
        }

        updateURL();
    });
});

filler_price.addEventListener("click", function () {
    selectedValues.min_price = parseInt(minValue.textContent);
    selectedValues.max_price = parseInt(maxValue.textContent);
    updateURL();
});

// Sau khi đã lưu trữ, bạn có thể đọc dữ liệu từ localStorage
var storedData = localStorage.getItem('myData');

// Chuyển chuỗi JSON thành đối tượng
var retrievedData = JSON.parse(storedData);

// In ra dữ liệu đã lấy từ localStorage
console.log(retrievedData);

sortBySelect.value = retrievedData.sortBy ? retrievedData.sortBy : "moi-nhat";
categoryCheckboxes.forEach(function (checkbox) {
    // Kiểm tra xem giá trị của checkbox có trong mảng categories không
    var isChecked = retrievedData.categories.includes(checkbox.value);

    // Thiết lập trạng thái checked của checkbox dựa trên kiểm tra
    checkbox.checked = isChecked;
});
slider.noUiSlider.set([retrievedData.min_price, retrievedData.max_price]);



