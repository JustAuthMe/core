<?php

if(file_exists(__DIR__ . '/config.dist.php')){
    require_once __DIR__ . '/config.dist.php';
}else{
    require_once __DIR__ . '/config.dev.php';
}

const WEBSOCKET_SOCKET_LOCAL = 'ws://' . WEBSOCKET_LOCAL_HOST . ':' . WEBSOCKET_PORT;
const WEBSOCKET_SOCKET_REMOTE = WEBSOCKET_PROTOCOL . '://' . WEBSOCKET_REMOTE_HOST . (
    WEBSOCKET_PATH !== '' ?
        WEBSOCKET_PATH :
        ':' . WEBSOCKET_PORT
    );

const RELEASE_NAME = DEPLOYED_REF . '-' . DEPLOYED_COMMIT;