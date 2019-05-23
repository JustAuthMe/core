<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 23/05/2019
 * Time: 21:47
 */

class Logger {
    const LOG_FILE_INFO = ROOT . 'log/info.log';
    const LOG_FILE_WARNING = ROOT . 'log/warning.log';
    const LOG_FILE_ERROR = ROOT . 'log/error.log';

    private static function log($message, $filename = self::LOG_FILE_INFO) {
        if (in_array($filename, [
            self::LOG_FILE_INFO,
            self::LOG_FILE_WARNING,
            self::LOG_FILE_ERROR
        ])) {
            $bt = debug_backtrace();
            $caller = array_shift($bt);
            $data = '------------------------------------------' . "\n" .
                '[' . date('d/m/Y H:i:s') . '][' . $_SERVER['REMOTE_ADDR'] . ']' . "\n" .
                '[in ' . $caller['file'] . ' line' . $caller['line'] . ']' . "\n" .
                $message . "\n\n";
            file_put_contents($filename, $data, FILE_APPEND);
        } else {
            self::logError('Wrong log file');
        }
    }

    public static function logInfo($message) {
        self::log($message, self::LOG_FILE_INFO);
    }

    public static function logWarning($message) {
        self::log($message, self::LOG_FILE_WARNING);
    }

    public static function logError($message) {
        self::log($message, self::LOG_FILE_ERROR);
    }
}