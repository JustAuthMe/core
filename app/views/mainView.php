<?php

/** @var UserAuth $auth */

use Entity\UserAuth;

?>
<!doctype html>
<html lang="en">
	<head>
		<title><?= NAME . (isset($TITLE) ? ' - ' . $TITLE : '') ?></title>
		<meta charset="utf-8" />
        <link rel="stylesheet" href="https://static.justauth.me/medias/fonts/lato-v16-latin/lato-v16-latin.css">
		<link type="text/css" rel="stylesheet" href="<?php echo CSS.'bootstrap.min.css'; ?>" media="screen" />
		<link type="text/css" rel="stylesheet" href="<?php echo CSS.'style.css'; ?>" media="screen" />
		<meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1.0" />
		<meta name="format-detection" content="telephone=no" />
        <link rel="shortcut icon" href="<?= IMG ?>icon.png"/>
        <?= Request::get()->getArg(0) === 'auth' ? '<meta http-equiv="refresh" content="' . (\Model\UserAuth::EXPIRATION_TIME - 10) . '">' : '' ?>
	</head>

	<body>
        <div class="container-fluid" style="height: 100vh;">
            <div class="row">
                <div class="jam col-md-4 d-none d-lg-flex flex-column justify-content-center align-items-center">
                    <a href="https://justauth.me" target="_blank">
                        <img src="<?= ASSETS.'img/logo_typo.png'; ?>" alt="JustAuthMe Logo" style="height: 250px;">
                    </a>
                    <p class="baseline text-center mx-5 mb-5">
                        <?= Request::get()->getArg(0) === 'auth' ? L::auth_baseline_auth_desktop($auth->client_app->getDomain()) : L::auth_baseline_default; ?>
                    </p>
                    <div class="stores text-center">
                        <a href="https://apps.apple.com/<?= L::lang === 'fr' ? 'fr' : 'us' ?>/app/justauthme/id1506495629"><img class="mb-2" src="<?= ASSETS.'img/stores_badges/apple_'.L::lang.'.png'; ?>"></a>
                        <a href="https://play.google.com/store/apps/details?id=me.justauth.app.android"><img class="mb-2" src="<?= ASSETS.'img/stores_badges/google_'.L::lang.'.png'; ?>"></a>
                    </div>
                </div>
                <div class="col-md-12 col-lg-8 d-flex flex-column justify-content-start justify-content-lg-center align-items-center" style="height: 100vh;">
                    <a href="https://justauth.me" target="_blank" class="d-lg-none">
                        <img src="<?= ASSETS.'img/JustAuthMe_logo.svg'; ?>" style="height: 200px;" alt="JustAuthMe Logo" class="mb-3">
                    </a>
                    <?php require_once @$appView; ?>
                    <a href="#notice" class="btn btn-sm btn-outline-secondary d-lg-none mt-3"><?= L::auth_what; ?></a>
                </div>
                <div class="jam d-lg-none col-md-12 col-lg-8 d-flex flex-column justify-content-start justify-content-lg-center pt-5 pt-lg-0 align-items-center" id="notice">
                    <a href="https://justauth.me" target="_blank">
                        <img src="<?= ASSETS.'img/logo_typo.png'; ?>" alt="JustAuthMe Logo" style="height: 250px;">
                    </a>
                    <p class="baseline text-center mx-5 mb-5">
                        <?= Request::get()->getArg(0) === 'auth' ? L::auth_baseline_auth_mobile($auth->client_app->getDomain()) : L::auth_baseline_default; ?>
                    </p>
                    <div class="stores text-center">
                        <a href="https://apps.apple.com/<?= L::lang === 'fr' ? 'fr' : 'us' ?>/app/justauthme/id1506495629"><img class="mb-2" src="<?= ASSETS.'img/stores_badges/apple_'.L::lang.'.png'; ?>"></a>
                        <a href="https://play.google.com/store/apps/details?id=me.justauth.app.android"><img class="mb-2" src="<?= ASSETS.'img/stores_badges/google_'.L::lang.'.png'; ?>"></a>
                    </div>
                </div>
            </div>
        </div>
		<script type="text/javascript" src="<?php echo JS.'jquery.min.js'; ?>"></script>
		<script type="text/javascript" src="<?php echo JS.'bootstrap.min.js'; ?>"></script>
        <script type="text/javascript" src="<?php echo JS.'script.js'; ?>"></script>
	</body>
</html>