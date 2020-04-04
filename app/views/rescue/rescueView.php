<!DOCTYPE html>
<html lang="en">
    <head>
        <title>JustAuthMe - Verrouiller votre compte</title>
        <meta charset="UTF-8"/>
        <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0" />
        <link rel="shortcut icon" href="<?= IMG ?>icon.png"/>
        <link rel="stylesheet" href="<?= CSS ?>recovery.css"/>
    </head>
    <body>
    <div class="container">
        <div class="brand">
            <img src="<?= IMG ?>logo_inline_white.png" alt="">
        </div>
        <div class="description">
            <?php require_once @$appView; ?>
        </div>
        <div class="footer"></div>
    </div>
    </body>
</html>