<?php

use Entity\Customer;
use Entity\EmailBlacklist;
use Model\Email;
use Model\User;

if (!isset($_GET['email'], $_GET['key'])) {
    Controller::http404NotFound();
}

if (User::hashInfo(strtolower($_GET['email']) . UNSUBSCRIBE_SALT) !== $_GET['key']) {
    Controller::http403Forbidden();
}

$email_exists = Persist::exists('Customer', 'email', $_GET['email']);
$is_lang_change = Request::get()->getArg(1) !== '';

if ($email_exists) {
    if ($is_lang_change) {
        $wanted_lang = Request::get()->getArg(1);
        $new_lang = in_array($wanted_lang, Email::ACCEPTED_LANGUAGES) ? $wanted_lang : Email::DEFAULT_LANGUAGE;

        /** @var Customer $cutomer */
        $cutomer = Persist::readBy('Customer', 'email', $_GET['email']);
        $cutomer->setLang(Request::get()->getArg(1));
        Persist::update($cutomer);

        Data::get()->add('TITLE', L::unsubscribe_lang_change_title);
        Data::get()->add('new_lang', $new_lang);
        Controller::renderView('unsubscribe/lang_change');
        die;
    }

    Persist::deleteBy('Customer', 'email', $_GET['email']);
}

$email_blacklist = new EmailBlacklist(
    0,
    User::hashEmail($_GET['email'])
);
Persist::create($email_blacklist);

Data::get()->add('TITLE', L::unsubscribe_title);
Data::get()->add('email', $_GET['email']);
Controller::renderView('unsubscribe/unsubscribe');
