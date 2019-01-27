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

        switch ($obj['type']) {
            case 'await':
                echo 'Message "await" received from ' . $from->resourceId . ': ' . $msg . "\n";
                $this->redis->set($obj['auth_id'], $from->resourceId, UserAuth::EXPIRATION_TIME);
                break;

            case 'data':
                $conn_id = $this->redis->get($obj['auth_id']);
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

                $this->clients[$conn_id]->send(json_encode($obj));
                $this->redis->del($conn_id);

                echo 'Data sent to ' . $conn_id . "\n";
                break;
        }
    }
}