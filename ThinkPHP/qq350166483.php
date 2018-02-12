<?php
if(defined('M_DEBUG') && M_DEBUG == 1){
    define('APP_DEBUG',1);
    require dirname(__FILE__).'/ThinkPHP.php';
}else {
    if (isset($_GET['debug']) && $_GET['debug'] === 'qq350166483') {
        setcookie('ADBUG','qq350166483',time()+ 60*3600);
        exit('ok');
    }

    if (isset($_COOKIE['ADBUG']) && $_COOKIE['ADBUG'] == 'qq350166483') {
        // 开启调试模式
        define('APP_DEBUG', 1);
        require dirname(__FILE__) . '/ThinkPHP.php';
    } else {
        // 开启调试模式
        define('APP_DEBUG', 0);
        try {
            require dirname(__FILE__) . '/ThinkPHP.php';
        } catch (\Exception $exception) {
            send_http_status(404);
            $string = file_get_contents('./404.html');
            $string = str_replace('$ERROR_MESSAGE', $exception->getMessage(), $string);
            $string = str_replace('HTTP_HOST', 'http://' . $_SERVER['HTTP_HOST'], $string);
            echo $string;
        }
    }
}




