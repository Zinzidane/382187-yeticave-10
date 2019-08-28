<?php $classname = (isset($errors)) ? "form--invalid" : ""; ?>
<form class="form form--add-lot container <?=$classname; ?>" enctype="multipart/form-data" action="add.php" method="post"> <!-- form--invalid -->
    <h2>Добавление лота</h2>
    <div class="form__container-two">
    <?php $classname = isset($errors['title']) ? "form__item--invalid" : ""; ?>
    <div class="form__item <?=$classname; ?>"> <!-- form__item--invalid -->
        <label for="lot-name">Наименование <sup>*</sup></label>
        <input id="lot-name" type="text" name="title" value="<?=getPostVal('title'); ?>" placeholder="Введите наименование лота">
        <span class="form__error"><?=$errors['title']; ?></span>

    </div>
    <?php $classname = isset($errors['category_id']) ? "form__item--invalid" : ""; ?>
    <div class="form__item <?=$classname; ?>">
        <label for="category">Категория <sup>*</sup></label>
        <select id="category" name="category_id">
        <option>Выберите категорию</option>
        <?php foreach ($categories as $category): ?>
            <option value="<?= $category['id'] ?>"
            <?php if ($category['id'] == $lot['category_id']): ?>selected<?php endif; ?>><?=$category['name'];?></option>
        <?php endforeach; ?>
        </select>
        <span class="form__error"><?=$errors['category_id']; ?></span>
    </div>
    </div>
    <?php $classname = isset($errors['description']) ? "form__item--invalid" : ""; ?>
    <div class="form__item form__item--wide <?=$classname; ?>">
        <label for="message">Описание <sup>*</sup></label>
        <textarea id="message" name="description" placeholder="Напишите описание лота"><?=getPostVal('description'); ?></textarea>
        <span class="form__error"><?=$errors['description']; ?></span>
    </div>
    <?php $classname = isset($errors['file']) ? "form__item--invalid" : ""; ?>
    <div class="form__item form__item--file <?=$classname; ?>">
    <label>Изображение <sup>*</sup></label>
    <div class="form__input-file">
        <input class="visually-hidden" type="file" id="lot_image" name="lot_image" value="">
        <label for="lot_image">
        Добавить
        </label>
        <span class="form__error"><?=$errors['file']; ?></span>
    </div>
    </div>
    <div class="form__container-three">
    <?php $classname = isset($errors['initial_rate']) ? "form__item--invalid" : ""; ?>
    <div class="form__item form__item--small <?=$classname; ?>">
        <label for="lot-rate">Начальная цена <sup>*</sup></label>
        <input id="lot-rate" type="text" name="initial_rate" value="<?=getPostVal('initial_rate'); ?>" placeholder="0">
        <span class="form__error"><?=$errors['initial_rate']; ?></span>
    </div>
    <?php $classname = isset($errors['rate_step']) ? "form__item--invalid" : ""; ?>
    <div class="form__item form__item--small <?=$classname; ?>">
        <label for="lot-step">Шаг ставки <sup>*</sup></label>
        <input id="lot-step" type="text" name="rate_step" value="<?=getPostVal('rate_step'); ?>" placeholder="0">
        <span class="form__error"><?=$errors['rate_step']; ?></span>
    </div>
    <?php $classname = isset($errors['date_close']) ? "form__item--invalid" : ""; ?>
    <div class="form__item <?=$classname; ?>">
        <label for="lot-date">Дата окончания торгов <sup>*</sup></label>
        <input class="form__input-date" id="lot-date" type="text"  value="<?=getPostVal('date_close'); ?>" name="date_close" placeholder="Введите дату в формате ГГГГ-ММ-ДД">
        <span class="form__error"><?=$errors['date_close']; ?></span>
    </div>
    </div>
    <?php if (isset($errors)): ?>
        <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
     <?php endif; ?>
    <button type="submit" class="button">Добавить лот</button>
</form>
