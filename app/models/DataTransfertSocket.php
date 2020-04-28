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
    const CACHE_KEY_PREFIX = 'ws_auth_';

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
            $error = 'This is not JSON';
        } else if (!isset($obj['type'])) {
            $error = 'Message type is missing';
        } else if (!isset($obj['auth_id'])) {
            $error = 'Auth ID is missing';
        }

        if (isset($error)) {
            $from->send(json_encode(['error' => $error]));
            echo 'Message error from ' . $from->resourceId . ': ' . $error . "\n";
            return;
        }

        $cacheKey = self::CACHE_KEY_PREFIX . $obj['auth_id'];

        switch ($obj['type']) {
            case 'await':
                echo 'Message "await" received from ' . $from->resourceId . ': ' . $msg . "\n";
                $this->redis->set($cacheKey, $from->resourceId, UserAuth::EXPIRATION_TIME);
                break;

            case 'data':
                $conn_id = $this->redis->get($cacheKey);
                if ($conn_id === false) {
                    $error_data = 'Unknown Auth ID (' . $obj['auth_id'] . ')';
                } else if (!UserAuth::checkDataSign($obj['data'], $obj['sign'])) {
                    $error_data = 'Unverified data transfert';
                }

                if (isset($error_data)) {
                    $from->send(json_encode(['error' => $error_data]));
                    echo 'Message error from ' . $from->resourceId . ': ' . $error_data . "\n";
                    return;
                }

                echo 'Message "data" received from ' . $from->resourceId . ': ' . $msg . "\n";
                echo 'Sending data...' . "\n";

                unset($obj['sign']);
                if (isset($this->clients[$conn_id])) {
                    $this->clients[$conn_id]->send(json_encode($obj));
                    echo 'Data sent to ' . $conn_id . "\n";
                } else {
                    echo 'Client ' . $conn_id . ' disconnected before receiving data.' . "\n";
                }

                $this->redis->del($cacheKey);

                break;
        }
    }
}