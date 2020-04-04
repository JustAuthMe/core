<h1>Lock your account</h1>
<p>We just sent you a passcode at <strong><?= $email ?></strong>,<br/>Fill it in below to lock your account.</p>

<form class="recovery" action="" method="post">
    <input type="hidden" name="email" required value="<?= $email ?>" />
    <input type="tel" name="passcode" placeholder="Passcode" required autofocus />
    <button type="submit">Lock</button>
</form>
<?php if (isset($error)): ?>
    <div class="error">
        <img src="<?= IMG ?>icon_error.png" class="icon" alt="">
        <p class="text">
            <?= html_entity_decode($error) ?>
        </p>
    </div>
<?php endif ?>