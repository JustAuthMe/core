<h1>Lock your account</h1>
<p>
    You lost access to your phone?<br />
    Lock your JustAuthMe account using this form.
    <br /><br />
    <small class="wildcard">Your account will still exists and you'll be able to recover it later</small>
</p>

<form class="recovery" action="<?= WEBROOT ?>rescue/challenge" method="post">
    <input type="email" name="email" placeholder="E-Mail address" required autofocus />
    <button type="submit">Next step</button>
</form>
<?php if (isset($error)): ?>
    <div class="error">
        <img src="<?= IMG ?>icon_error.png" class="icon" alt="">
        <p class="text">
            <?= html_entity_decode($error) ?>
        </p>
    </div>
<?php endif ?>