<?php

use Entity\Customer;
use Model\Customer as CustomerModel;
use Model\User;

if (!Utils::isJamInternal()) {
    Controller::http401Unauthorized();
    Controller::renderApiError('Authentication failed');
}

if (POST) {
    $redis = new \PHPeter\Redis();

    if (!isset($_POST['email'], $_POST['ip']) || $_POST['email'] === '' && $_POST['ip'] === '') {
        Controller::http400BadRequest();
        Controller::renderApiError('Email and ip are required');
    }

    $cache_key = CustomerModel::SUBSCRIPTION_CACHE_PREFIX . Utils::slugifyIp(Utils::truncateIPV6($_POST['ip'], 4));
    $attempts = (int) $redis->get($cache_key);
    if ($attempts > 0) {
        Controller::http429TooManyRequests();
        Controller::renderApiError('Please wait at least 10 seconds between each subscription');
    }

    $redis->set($cache_key, 1, CustomerModel::SUBSCRIPTION_CACHE_COOLDOWN);

    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        Controller::http400BadRequest();
        Controller::renderApiError('Please provide a valid email address');
    }

    if (Persist::exists('Customer', 'email', strtolower($_POST['email']))) {
        Controller::http409Conflict();
        Controller::renderApiError('This email already exists');
    }

    $customer = new Customer(
        0,
        strtolower($_POST['email']),
        Utils::time(),
        $_POST['ip']
    );
    Persist::create($customer);
    Persist::deleteBy('EmailBlacklist', 'email', User::hashEmail($_POST['email']));

    Controller::renderApiSuccess();
}

Controller::http405MethodNotAllowed();
Controller::renderApiError('Only POST requests are allowed');