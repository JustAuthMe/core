<?php require VIEWS . 'mail/_top.php'; ?>
<tr>
    <td height="30" align="center">
        <h1 style="text-transform: uppercase;font-size: 24px;">Confirmation d'adresse E-Mail</h1>
    </td>
</tr>
<tr>
    <td>
        <p align="justify" style="font-size:14px;line-height:28px">
            Bonjour,<br />
            Vous avez récemment souhaité modifier votre E-Mail associé à votre compte JustAuthMe.
            Afin de compléter le processus, nous vous invitons à valider votre adresse E-Mail en cliquant sur le
            bouton ci-dessous. Attention, ce lien n'est valide que 24h !
            Si le bouton ne s'affiche pas ou si vous rencontrez des difficultés pour l'utiliser, copiez
            simplement ce lien dans votre navigateur web :<br />
            <span style="color:blue"><?= $confirm_link ?></span>
        </p>
    </td>
</tr>
<tr>
    <td height="10"></td>
</tr>
<tr>
    <td align="center">
        <a href="<?= $confirm_link ?>" style="color:white;text-decoration:none;">
            <table width="200" bgcolor="#3598db" style="border-radius:20px">
                <tr color="white">
                    <td height="25" align="center" style="font-size: 11px;text-transform: uppercase; color: white;" color="white">Confirmer mon adresse E-Mail</td>
                </tr>
            </table>
        </a>
    </td>
</tr>
<?php require VIEWS . 'mail/_btm.php'; ?>
