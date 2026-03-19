<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>"Винни Пух: сотрудничество"</title>
    <link rel ="stylesheet" href ="/css/main.css">
</head>
    <div class="header-top">
        <ul class="nav">
            <li><a href="/index.php" class="button">о нас</a></li>
            <li><a href="tastePage.php" class="button">католог</a></li>
            <li><a href="dessertType.php" class="button">сделать заказ</a></li>
            <li><a href="cooperation.php" class="button">сотрудничество</a></li>
        </ul>
     </div>
     <body>
        <div class="titel2">Сотрудничество</div>
        <div class="cooperation_grid">
            <div class="cooperation_text">
                <h3>Cотрудничество</h3>
                <p>Наша кондитерская всегда с огромным удовольствием принимает новые знакомства.
                 Если Вы владелец кафе, ресторана, точки «Кофе с собой», владелец банкетного зала, 
                 event-агентства, организатор торжеств, ведущий, декоратор и просто хороший человек и
                 Вам необходим надежный партнер по изготовлению тортов, сладких столов или просто десертов, то мы 
                 идеально подходим на его место! Приглашаем Вас к сотрудничеству по реализации нашей продукции в Ваших
                 заведениях или мероприятиях. Вы можете ознакомиться с ассортиментом, который мы предлагаем для
                 реализации в нашем коммерческом предложении.</p>
            </div>
            <div class="cooperation_text">
                <h3>Этапы сотрудничества:</h3>
                <ul>
                    <li>Оставьте заявку по форме ниже, и ожидайте связи с нашим специалистом;</li>
                    <li>Мы организовываем для вас персональную дегустацию с обсуждением условий, объемов, прайса и ассортимента;</li>
                    <li>Заключаем договор и Вы радуете гостей Вашего заведения/мероприятия вкусными десертами от надежного поставщика.</li>
                    <li>Первая поставка по 100 % предоплате. Отсрочка платежа 5 календарных дней. Наличный и безналичный расчет. Мы самостоятельно доставим продукцию на следующий день после заказа в согласованное время. Минимальная сумма заказа и доставка по Гродно при сумме заказа от 80 BYN.
                        Запишитесь на дегустацию и попробуйте наши сладости! Ждем Вас в гости!</li>
                </ul>
            </div>
            <h3>Заполните форму и получите предложение</h3>
<form class="cooprt_form" id="cooprt_form" method="POST" >
    <div class="form_group">
        <input type="text" id="full_name_c" name="full_name" 
               placeholder="Введите ваше полное имя" 
               required
               minlength="2"
               maxlength="100">
    </div>
    
    <div class="form_group">
        <input type="tel" id="phone_c" name="phone" 
               required 
               placeholder="Введите ваш номер телефона"
               pattern="^[\+]?[0-9\s\-\(\)]{7,20}$"
               title="Пример: +375291234567">
    </div>
    
    <div class="form_group">
        <input type="text" id="company" name="company" 
               placeholder="Название организации"
               maxlength="100">
    </div>
    
    <div class="form_group">
        <input id="message" name="message" 
                  placeholder="Текст сообщения"></input>
    </div>
    
    <button type="submit" class="button4">Получить предложение</button>
</form>
                <img class="img_cooprt" src="/image/виннипух2.png">
        </div>
        <script src="/js/cooperation_form.js"></script>
     </body>
<footer class="footer">
        <div class="footer_grid">
            <div class="footer_text">
                <p>Адрес: Коммунистическая 45А</p>
                <p>Часы работы: 9:00-22:00</p>
                <p>Контакты:</p>
                <p>email: vinnipux@gmail.com</p>
                <p>По вопросам сотрудничества: +375 44 799 9802 (Анна)</p>
                <p>По вопросам связанными с заказом: +375 44 477 8224</p>
                <p>Присоеденяйся к нам в соцсетях!</p>  
            </div>
            <div class="social_media">
                <a href="#!"><img src="/image/insta.png" class="social_media_img"></a>
                <a href="#!"><img src="/image/telega.png" class="social_media_img"></a>
                <a href="#!"><img src="/image/whtsap.png" class="social_media_img"></a>
                <a href="#!"><img src="/image/tiktok.png" class="social_media_img"></a>
                <a href="#!"><img src="/image/pinterest.png" class="social_media_img"></a>
            </div>
            <img class="mnogo_pchel" src="/image/mnogo_pchel.png" alt="mnogo_pchel">
        </div>
</footer>
</html>