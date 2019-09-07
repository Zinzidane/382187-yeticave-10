<section class="rates container">
    <h2>Мои ставки</h2>
    <table class="rates__list">
    <?php foreach ($bets as $bet): ?>
        <?php $is_end = (strtotime($bet['date_close']) < strtotime('now')); ?>
        <?php $is_winner = $bet['winner_id'] == $_SESSION['user']['id']; ?>
        <?php $end_classname = $is_end && !$is_winner ? "rates__item--end" : ""; ?>
        <?php $winner_classname = $is_winner ? "rates__item--win" : "" ?>
        <tr class="rates__item <?=$end_classname; ?> <?=$winner_classname ?>">
            <td class="rates__info">
                <div class="rates__img">
                    <img src="<?=$bet['image']; ?>" width="54" height="40" alt="Сноуборд">
                </div>
                <h3 class="rates__title"><a href="lot.php?id=<?=$bet['id']; ?>"><?=$bet['title']; ?></a></h3>
            </td>
            <td class="rates__category">
                <?=$bet['category']; ?>
            </td>
            <td class="rates__timer">
                <?php $finishing_classname = get_dt_range($bet['date_close'])[0] < 24 && !$is_end && !$is_winner ? "timer--finishing" : ""; ?>
                <?php $end_classname = $is_end && !$is_winner ? "timer--end" : ""; ?>
                <?php $winner_classname = $is_winner ? "timer--win" : ""; ?>
                <div class="timer <?=$end_classname; ?><?=$winner_classname; ?><?=$finishing_classname; ?>"><?=get_bet_info($bet); ?></div>
            </td>
            <td class="rates__price">
            <?=$bet['rate']; ?> р
            </td>
            <td class="rates__time">
            <?=get_passed_time($bet['date_add']); ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </table>
</section>
