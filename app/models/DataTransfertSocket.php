<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 26/12/2018
 * Time: 20:31
 */

namespace Model;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;

class DataTransfertSocket implements MessageComponentInterface {
    protected $clients;
    protected $sessions;

    public function __construct() {
        $this->clients = new \SplObjectStorage();
        $this->sessions = [];
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo 'Opened: ' . $conn->resourceId . "\n";
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo 'Closed: ' . $conn->resourceId . "\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo 'Fatal error from ' . $conn->resourceId . ' (connection will be closed): ' . $e->getMessage() . "\n";
        $conn->close();
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        if ($obj = json_decode($msg, true) === null) {
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
                $this->sessions[$obj['auth_id']] = [
                    'conn' => $from,
                    'expire' => \Utils::time() + 600 // 10 minutes
                ];
                break;

            case 'data':
                if (!isset($this->sessions[$obj['auth_id']])) {
                    echo 'Message error from ' . $from->resourceId . ': unknown Auth ID' . "\n";
                    return;
                }

                echo 'Message "data" received from ' . $from->resourceId . ': ' . $msg . "\n";
                ECHO 'Sending data...' . "\n";
                $this->sessions[$obj['auth_id']]['conn']->send(json_encode($obj['data']));
                echo 'Data sent to ' . $this->sessions[$obj['auth_id']]['conn']->resourceId . "\n";
                break;
        }
    }
}