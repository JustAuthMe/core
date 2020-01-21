<?php require VIEWS . 'mail/_top.php'; ?>
<tr>
    <td height="30" align="center">
        <h1 style="text-transform: uppercase;font-size: 24px;">Login passcode</h1>
    </td>
</tr>
<tr>
    <td>
        <p align="justify" style="font-size:14px;line-height:28px">
            Bonjour,<br />
            Vous avez récemment souhaité vous connecter à JustAuth.Me sur appareil mobile.
            Afin de compléter le processus de connexion, nous vous invitons à entrer le code ci-dessous
            dans votre application.
            Attention, ce code n'est valable que 10 minutes !
            Si le code ne s'affiche pas ou si vous rencontrez des difficultés pour l'utiliser,
            contactez le support à l'adresse suivante: <a href="mailto:support@justauth.me">support@justauth.me</a>
        </p>
    </td>
</tr>
<tr>
    <td height="10"></td>
</tr>
<tr>
    <td align="center">
        <h1><?= $passcode ?></h1>
    </td>
</tr>
<?php require VIEWS . 'mail/_btm.php'; ?>