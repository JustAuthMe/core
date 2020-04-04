<!doctype html>
<html lang="en">
	<head>
		<title><?= NAME . (isset($TITLE) ? ' - ' . $TITLE : '') ?></title>
		<meta charset="utf-8" />
		<link type="text/css" rel="stylesheet" href="<?php echo CSS.'style.css'; ?>" media="screen" />
		<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0" />
		<meta name="format-detection" content="telephone=no" />
        <link rel="shortcut icon" href="<?= IMG ?>icon.png"/>
        <?= Request::get()->getArg(0) === 'auth' ? '<meta http-equiv="refresh" content="' . (\Model\UserAuth::EXPIRATION_TIME - 2) . '">' : '' ?>
	</head>

	<body>
        <div class="container">
            <?php require_once @$appView; ?>

            <div class="footer">
                <img src="<?= WEBROOT ?>assets/img/logo.png" alt="JustAuthMe Logo" />
            </div>
        </div>
		<script type="text/javascript" src="<?php echo JS.'jquery.min.js'; ?>"></script>
		<script type="text/javascript" src="<?php echo JS.'script.js'; ?>"></script>
	</body>
</html>