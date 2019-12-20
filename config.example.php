<?php
/**
 * Rename this file "config.dist.php"
 */

const PROD_ENV = false;
const PROD_HOST = 'localhost';
const LOGGING = true;

const DATA_TRANSFERT_KEY = 'some random shit';
const JAM_CONSOLE_API_KEY = 'some OTHER random shit (DIFFERENT from above)';

const DB_HOST = 'do host';
const DB_NAME = 'justauthme';
const DB_USER = 'db user';
const DB_PASS = 'db pass';

const REDIS_HOST = '127.0.0.1';
const REDIS_PORT = 6379;
const REDIS_PASS = 'redis pass (must be loooooooooooooooong';

const WEBSOCKET_HOST = 'localhost';
const WEBSOCKET_PORT = 1337;
const WEBSOCKET_PATH = '';
const WEBSOCKET_SOCKET_LOCAL = 'ws://' . WEBSOCKET_HOST . ':' . WEBSOCKET_PORT;
const WEBSOCKET_SOCKET_REMOTE = 'ws://' . PROD_HOST . (
    WEBSOCKET_PATH !== '' ?
        WEBSOCKET_PATH :
        ':' . WEBSOCKET_PORT
    );