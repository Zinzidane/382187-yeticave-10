<?php $classname = (isset($errors)) ? "form--invalid" : ""; ?>
<form class="form container <?=$classname; ?>" action="signup.php" method="post" autocomplete="off">
    <h2>Регистрация нового аккаунта</h2>
    <?php $classname = isset($errors['email']) ? "form__item--invalid" : ""; ?>
    <div class="form__item <?=$classname; ?>">
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" value="<?=get_post_val('email'); ?>" placeholder="Введите e-mail">
        <span class="form__error"><?=$errors['email']; ?></span>
    </div>
    <?php $classname = isset($errors['password']) ? "form__item--invalid" : ""; ?>
    <div class="form__item <?=$classname; ?>">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" value="<?=get_post_val('password'); ?>" placeholder="Введите пароль">
        <span class="form__error"><?=$errors['password']; ?></span>
    </div>
    <?php $classname = isset($errors['name']) ? "form__item--invalid" : ""; ?>
    <div class="form__item <?=$classname; ?>">
        <label for="name">Имя <sup>*</sup></label>
        <input id="name" type="text" name="name" value="<?=get_post_val('name'); ?>" placeholder="Введите имя">
        <span class="form__error"><?=$errors['name']; ?></span>
    </div>
    <?php $classname = isset($errors['message']) ? "form__item--invalid" : ""; ?>
    <div class="form__item <?=$classname; ?>">
        <label for="message">Контактные данные <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите как с вами связаться"><?=get_post_val('message'); ?></textarea>
        <span class="form__error"><?=$errors['message']; ?></span>
    </div>
    <?php if (isset($errors)): ?>
    <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
    <?php endif; ?>
    <button type="submit" class="button">Зарегистрироваться</button>
    <a class="text-link" href="#">Уже есть аккаунт</a>
</form>
