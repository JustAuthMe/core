<h1><?= L::rescue_title ?></h1>
<p><?= L::rescue_step2_text($email) ?></p>
<form class="recovery" action="" method="post">
    <input type="hidden" name="email" required value="<?= $email ?>" />
    <input type="tel" name="passcode" placeholder="<?= L::rescue_step2_placeholder ?>" required autofocus />
    <button type="submit"><?= L::rescue_lock ?></button>
</form>
<?php if (isset($error)): ?>
    <div class="error">
        <img src="<?= IMG ?>icon_error.png" class="icon" alt="">
        <p class="text">
            <?= html_entity_decode($error) ?>
        </p>
    </div>
<?php endif ?>