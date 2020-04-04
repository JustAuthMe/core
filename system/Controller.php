<?php
abstract class Controller {
    public static function __callStatic($name, $arguments) {
        if (substr($name, 0, 4) == 'http') {
            $errCode = substr($name, 4, 3);
            $errMsg = preg_replace("#([A-Z])#", " $1", substr($name, 7));
            header("HTTP/1.1 $errCode$errMsg");
            if (Request::get()->getArg(0) != 'api' && file_exists(VIEWS . 'error/' . $errCode . '.php')) {
                self::renderView('error/' . $errCode);
                die;
            }
        }
    }

    public static function renderView(string $path, ?string $layout = 'mainView.php') {
        $file = VIEWS.$path.'.php';
        if (file_exists($file) ) {
            $appView = $file;
            $data = Request::get()->getArg(0) !== 'api' ? Utils::secure(Data::get()->getData()) : Data::get()->getData();
            extract($data);
            if (!is_null($layout) && file_exists(VIEWS . $layout)) {
                require_once VIEWS . $layout;
            }
            else {
                require_once $appView;
            }
        }
        else {
            self::http500InternalServerError();
        }
    }

    public static function renderApiError($message) {
        Logger::logError($message);
        Data::get()->add('status', 'error');
        Data::get()->add('message', $message);
        Controller::renderView('json/json', null);
        die;
    }

    public static function renderApiSuccess() {
        Data::get()->setData(array_merge(['status' => 'success'], Data::get()->getData()));
        Logger::logInfo($_GET['arg'] . ': ' . json_encode(Data::get()->getData()));
        Controller::renderView('json/json', null);
        die;
    }

    public static function sendNoCacheHeaders() {
        header('Cache-Control: no-store');
        header('Pragma: no-cache');
    }
}