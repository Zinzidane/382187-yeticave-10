<div class="container">
    <section class="lots">
        <h2>Результаты поиска по запросу «<span><?=$search; ?></span>»</h2>
        <ul class="lots__list">
            <?php if (empty($lots)): ?>
            <p>Ничего не найдено по вашему запросу</p>
            <?php else: ?>
            <?php foreach ($lots as $lot): ?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="../<?=$lot['image']; ?>" width="350" height="260" alt="<?=$lot['title']; ?>">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?=$lot['category']; ?></span>
                    <h3 class="lot__title"><a class="text-link" href="lot.php?id=<?=$lot['id']; ?>"><?=$lot['title']; ?></a></h3>
                    <div class="lot__state">
                    <div class="lot__rate">
                        <span class="lot__amount">Стартовая цена</span>
                        <span class="lot__cost"><?=addCurrencyToPrice(formatPrice(getCurrentPrice($lot['initial_rate'], $lot['current_rate'])), 'rub', 'р'); ?></span>
                    </div>
                    <?php $finishing_classname = getDtRange($lot['date_close'])[0] < 24  ? "timer--finishing" : ""; ?>
                    <div class="lot__timer timer <?=$finishing_classname; ?>">
                        <?=implode(':', getDtRange($lot['date_close']));?>
                    </div>
                    </div>
                </div>
            </li>
            <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </section>
    <?php if ($pages_count > 1): ?>
    <ul class="pagination-list">
        <li class="pagination-item pagination-item-prev">
            <?php if ($cur_page > 1): ?>
            <a href="/search.php?search=<?=$search; ?>&page=<?=$cur_page - 1; ?>">Назад</a>
            <?php endif; ?>
        </li>
        <?php foreach ($pages as $page): ?>
        <li class="pagination-item <?php if ($page == $cur_page): ?>pagination-item-active<? endif ?>">
            <a href="/search.php?search=<?=$search; ?>&page=<?=$page; ?>"><?=$page; ?></a>
        </li>
        <?php endforeach; ?>
        <li class="pagination-item pagination-item-next">
            <?php if ($cur_page < $pages_count): ?>
            <a href="/search.php?search=<?=$search; ?>&page=<?=$cur_page + 1; ?>">Вперед</a>
            <?php endif; ?>
        </li>
    </ul>
    <?php endif; ?>
</div>
