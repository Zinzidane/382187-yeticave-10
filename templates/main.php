<main class="container">
    <section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>
        <ul class="promo__list">
            <!--заполните этот список из массива категорий-->
            <?php foreach ($categories as $category): ?>
            <li class="promo__item promo__item--<?=$category['symbol_code']; ?>">
                <a class="promo__link" href="pages/all-lots.html"><?=htmlspecialchars($category['name']); ?></a>
            </li>
            <?php endforeach; ?>
        </ul>
    </section>
    <section class="lots">
        <div class="lots__header">
            <h2>Открытые лоты</h2>
        </div>
        <ul class="lots__list">
            <!--заполните этот список из массива с товарами-->
            <?php foreach ($lots as $lot): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?=htmlspecialchars($lot['image']); ?>" width="350" height="260" alt="">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?=htmlspecialchars($lot['category']); ?></span>
                    <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?= $lot['id']; ?>"><?=htmlspecialchars($lot['title']); ?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost"><?=add_currency_to_price(format_price(htmlspecialchars($lot['initial_rate'])), 'rub', 'р'); ?></span>
                        </div>
                        <?php if (get_dt_range($lot['date_close'])[0] < 1): ?>
                        <div class="lot__timer timer--finishing">
                            <?=implode(':', get_dt_range($lot['date_close']));?>
                        </div>
                        <?php else: ?>
                        <div class="lot__timer">
                            <?=implode(':', get_dt_range($lot['date_close']));?>
                        </div>
                        <?php endif;?>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
        </ul>
    </section>
</main>
