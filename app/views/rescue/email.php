<h1><?= L::rescue_title ?></h1>
<p><?= L::rescue_step1_text ?></p>
<form class="recovery" action="<?= WEBROOT ?>rescue/challenge" method="post">
    <input type="email" name="email" placeholder="<?= L::rescue_step1_placeholder ?>" required autofocus />
    <button type="submit"><?= L::rescue_next_step ?></button>
</form>
<?php if (isset($error)): ?>
    <div class="error">
        <img src="<?= IMG ?>icon_error.png" class="icon" alt="">
        <p class="text">
            <?= html_entity_decode($error) ?>
        </p>
    </div>
<?php endif ?>