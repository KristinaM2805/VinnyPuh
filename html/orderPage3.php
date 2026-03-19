<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа</title>
    <link rel="stylesheet" href="/css/main.css">
</head>
<body class="order_page3">
    <div class="header-top">
                <ul class="nav">
                    <li><a href="/index.php" class="button">о нас</a></li>
                    <li><a href="tastePage.php" class="button">католог</a></li>
                    <li><a href="dessertType.php" class="button">сделать заказ</a></li>
                    <li><a href="cooperation.php" class="button">сотрудничество</a></li>
                </ul>
            </div>
    <div class="continer">
        <div class="form_grid">
            <div class="titel2">Оформление заказа</div>
            <form class="ordear_form" id="ordear_form">
                <div class="form_group">
                    <input type="text" id="full_name" name="full_name" placeholder="Введите ваше полное имя" required
                    minlength="2" maxlength="100" pattern="^[а-яА-ЯёЁa-zA-Z\s\-]+$" title="Только буквы, пробелы и дефисы">
                </div>
                <div class="form_group">
                    <input type="email" id="email" name="email" required placeholder="Введите ваш e-mail">
                </div>
                <div class="form_group">
                    <input type="tel" id="phone" name="phone" pattern="^[\+]?[0-9\s\-\(\)]{7,15}$" 
                    required placeholder="Введите ваш номер телефона" 
                    title="Пример: +375291234567 или 80291234567">
                </div>
                <div class="form_group" >
                    <input type="text" id="wishes" name="wishes" placeholder="Введите ваши пожелания">
                </div>
                <div class="form_group">
                    <select class="delivery" name="delivery_type" id="delivery_type">
                        <option value="delivery">Доставка</option>
                        <option value="pickup">Самовывоз</option>
                    </select>
                </div>
                <div class="form_group address-group" id="address_group" style="display: none;">
                    <input type="text" id="address" name="address" placeholder="Введите адрес доставки">
                </div>
                 <input type="hidden" name="order_items" id="order_items">
                 <input type="hidden" id="total_price" name="total_price">
                 <button type="submit" class="button3">Оформить заказ</button>
            </form>
            <div class="descr_order_grid">
               <button class="add-position-btn" id="addPositionBtn">
                   <span class="btn-icon">+</span>
                   Добавить новую позицию
               </button>
            </div>
        </div>
    </div>
    <script src="/js/order.js"></script>
</body>
</html>