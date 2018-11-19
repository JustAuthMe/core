<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 19/11/2018
 * Time: 22:22
 */

$username = \Model\User::generateUsername();
$user = new \Entity\User(
    0,
    $username
);
Persist::create($user);

Data::get()->add('user', $user);
Controller::renderApiSuccess();
