<h1>Lock your account</h1>
<p>You lost access to your phone?<br/>Lock your JustAuthMe account using this form.</p>

<form class="recovery" action="" method="post">
    <input type="email" id="email" placeholder="E-Mail address" required autofocus />
    <button type="submit">Next step</button>
</form>
<?php if (isset($error)): ?>
    <div class="error">
        <p>
            <img src="<?= IMG ?>icon_error.png" class="icon" alt="">
            Unknow E-Mail.
        </p>
    </div>
<?php endif ?>