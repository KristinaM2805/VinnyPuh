
function changeMainImage(clickedImageSrc) {
    const mainImage = document.getElementById('mainImage');
    const clickedImage = event.target;
    
   
    const tempSrc = mainImage.src;
    mainImage.src = clickedImageSrc;
    clickedImage.src = tempSrc;
}

function selectWeight(weight) {
    document.querySelector('#weightDropdown .drop_btn').textContent = weight;
}

function selectTaste(taste) {
    document.querySelector('#tasteDropdown .drop_btn').textContent = taste;
}

function selectCandles(candles) {
    document.querySelector('#candlesDropdown .drop_btn').textContent = candles;
}

document.querySelector('.decor_btm').addEventListener('click', function() {
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.accept = 'image/*';
    
    fileInput.onchange = function(e) {
        const file = e.target.files[0];
        if (file) {
            
            alert('Изображение загружено: ' + file.name);
        
        }
    };
    
    fileInput.click();
});


function addToCart() {
    alert('Торт добавлен в корзину!');
    
    updateCartCounter();
}

function updateCartCounter() {
    console.log('Корзина обновлена');
}

document.addEventListener('DOMContentLoaded', function() {
    const formInputs = document.querySelectorAll('.order_page3 .form_group input[type="text"]');
    
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            const value = this.value;
            const textWidth = this.scrollWidth;
            this.style.textAlign = 'center';
        });
        
        input.addEventListener('input', function() {
            this.style.textAlign = 'center';
        });
    });
});