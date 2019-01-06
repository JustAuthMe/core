<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 02/01/2019
 * Time: 23:23
 */
require_once 'vendor/autoload.php';
require_once 'app/models/DataTransfertSocket.php';

$server = \Ratchet\Server\IoServer::factory(
    new \Ratchet\Http\HttpServer(
        new \Ratchet\WebSocket\WsServer(
            new \Model\DataTransfertSocket()
        )
    ),
    1337
);

$server->run();