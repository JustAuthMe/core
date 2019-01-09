<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 26/12/2018
 * Time: 20:31
 */

namespace Model;

use PHPeter\Redis;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class DataTransfertSocket implements MessageComponentInterface {
    const AUTH_EXPIRATION_TIME = 600; // 10 minutes

    protected $clients;
    protected $redis;

    public function __construct() {
        $this->clients = [];
        $this->redis = new Redis();
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients[$conn->resourceId] = $conn;
        echo 'Opened: ' . $conn->resourceId . "\n";
    }

    public function onClose(ConnectionInterface $conn) {
        unset($this->clients[$conn->resourceId]);
        echo 'Closed: ' . $conn->resourceId . "\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo 'Fatal error from ' . $conn->resourceId . ' (connection will be closed): ' . $e->getMessage() . "\n";
        $conn->close();
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $obj = json_decode($msg, true);
        if ($obj === null) {
            echo 'Message error from ' . $from->resourceId . ': this is not JSON' . "\n";
            return;
        }

        if (!isset($obj['type'])) {
            echo 'Message error from ' . $from->resourceId . ': message type is missing' . "\n";
            return;
        }

        if (!isset($obj['auth_id'])) {
            echo 'Message error from ' . $from->resourceId . ': auth ID is missing' . "\n";
            return;
        }

        switch ($obj['type']) {
            case 'await':
                echo 'Message "await" received from ' . $from->resourceId . ': ' . $msg . "\n";
                $this->redis->set($obj['auth_id'], $from->resourceId, self::AUTH_EXPIRATION_TIME);
                break;

            case 'data':
                $conn_id = $this->redis->get($obj['auth_id']);
                if ($conn_id === false) {
                    echo 'Message error from ' . $from->resourceId . ': Unknown Auth ID (' . $obj['auth_id'] . ')' . "\n";
                    return;
                }

                echo 'Message "data" received from ' . $from->resourceId . ': ' . $msg . "\n";
                echo 'Sending data...' . "\n";

                $this->clients[$conn_id]->send(json_encode($obj));
                $this->redis->del($conn_id);

                echo 'Data sent to ' . $conn_id . "\n";
                break;
        }
    }
}