<?php require VIEWS . 'mail/_top.php'; ?>
<tr>
    <td height="30" align="center">
        <h1 style="text-transform: uppercase;font-size: 24px;"><?= $subject ?></h1>
    </td>
</tr>
<tr>
    <td>
        <p align="justify" style="font-size:14px;line-height:22px">
            <?= nl2br($body) ?>
        </p>
    </td>
</tr>
<?php if (isset($call_to_action)): ?>
    <tr>
        <td height="10"></td>
    </tr>
    <tr>
        <td align="center">
            <a href="<?= $call_to_action->link ?>" style="color:white;text-decoration:none;">
                <table width="200" bgcolor="#3598db" style="border-radius:20px">
                    <tr color="white">
                        <td height="25" align="center" style="font-size: 11px;text-transform: uppercase; color: white;" color="white"><?= $call_to_action->title ?></td>
                    </tr>
                </table>
            </a>
        </td>
    </tr>
<?php endif ?>
<?php require VIEWS . 'mail/_btm.php'; ?>
