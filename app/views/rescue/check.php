<h1>Lock your account</h1>
<p>You just received a passcode by E-Mail,<br/>Fill it in below to lock your account.</p>

<form class="recovery" action="" method="post">
    <input type="tel" id="passcode" placeholder="Passcode" required autofocus />
    <button type="submit">Lock</button>
</form>
<?php if (isset($error)): ?>
<div class="error">
    <p>
        <img src="<?= IMG ?>icon_error.png" class="icon" alt="">
        Invalid passcode.
    </p>
</div>
<?php endif ?>