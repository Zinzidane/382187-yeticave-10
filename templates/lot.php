<?php
    $minimal_bet = get_minimal_bet($lot['initial_rate'], $lot['rate_step'], $lot['current_rate']);
?>
<section class="lot-item container">
    <h2><?=$lot['title']; ?></h2>
    <div class="lot-item__content">
    <div class="lot-item__left">
        <div class="lot-item__image">
        <img src="<?=$lot['image']; ?>" width="730" height="548" alt=<?=$lot['title']; ?>>
        </div>
        <p class="lot-item__category">Категория: <span><?=$lot['category']; ?></span></p>
        <p class="lot-item__description"><?=$lot['description']; ?></p>
    </div>
    <div class="lot-item__right">
        <div class="lot-item__state">
        <div class="<?=get_dt_range($lot['date_close'])[0] < 1 ? 'lot__timer timer--finishing' : 'lot__timer'?>">
            <?=implode(':', get_dt_range($lot['date_close']));?>
        </div>
        <div class="lot-item__cost-state">
            <div class="lot-item__rate">
            <span class="lot-item__amount">Текущая цена</span>
            <span class="lot-item__cost"><?=add_currency_to_price(format_price(get_current_price($lot['initial_rate'], $lot['current_rate'])), 'rub', 'р'); ?></span>
            </div>
            <div class="lot-item__min-cost">
            Мин. ставка <span><?=add_currency_to_price($minimal_bet, 'rub', 'р'); ?></span>
            </div>
        </div>
        <?php if ($is_auth && $lot['user_id'] != get_user_id()): ?>
        <?php $classname = (isset($errors)) ? "form--invalid" : ""; ?>
        <form class="lot-item__form <?=$classname; ?>" action="lot.php?id=<?=$_GET['id'];?>" method="post" autocomplete="off">
            <?php $classname = isset($errors['cost']) ? "form__item--invalid" : ""; ?>
            <p class="lot-item__form-item form__item <?=$classname; ?>">
                <label for="cost">Ваша ставка</label>
                <input id="cost" type="text" name="cost" value="<?=get_post_val('cost'); ?>" placeholder="<?=$minimal_bet; ?>">
                <span class="form__error"><?=$errors['cost']; ?></span>
            </p>
            <button type="submit" class="button">Сделать ставку</button>
        </form>
        <?php endif;?>
        </div>
        <div class="history">
        <h3>История ставок (<span><?=$lot['bets_number']; ?></span>)</h3>
        <table class="history__list">
            <?php foreach ($bets as $bet): ?>
            <tr class="history__item">
            <td class="history__name"><?=$bet['user']; ?></td>
            <td class="history__price"><?=add_currency_to_price(format_price(htmlspecialchars($bet['rate'])), 'rub', 'р'); ?></td>
            <td class="history__time"><?=get_passed_time($bet['date_add']); ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        </div>
    </div>
    </div>
</section>
