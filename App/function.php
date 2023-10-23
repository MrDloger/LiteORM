<?
function d($value, $title = false){
    echo '<pre>' . PHP_EOL;
    $back_trace = debug_backtrace();
    echo 'line <b>'.$back_trace[0]['line'].'</b> in '.$back_trace[0]['file'].'</br>'.PHP_EOL;
    if ($title) echo $title . PHP_EOL;
    echo htmlspecialchars(print_r($value, 1));
    echo '</pre>'.PHP_EOL;
}
function dv($value, $title = false){
    echo '<pre>' . PHP_EOL;
    $back_trace = debug_backtrace();
    echo 'line <b>'.$back_trace[0]['line'].'</b> in '.$back_trace[0]['file'].'</br>'.PHP_EOL;
    if ($title) echo $title . PHP_EOL;
    var_dump($value);
    echo '</pre>'.PHP_EOL;
}

function dd($value, $title = false){
	echo '<pre>' . PHP_EOL;
    $back_trace = debug_backtrace();
    echo 'line <b>'.$back_trace[0]['line'].'</b> in '.$back_trace[0]['file'].'</br>'.PHP_EOL;
    if ($title) echo $title . PHP_EOL;
    echo htmlspecialchars(print_r($value, 1));
    echo '</pre>'.PHP_EOL;
	die();
}
function ddv($value, $title = false){
     echo '<pre>' . PHP_EOL;
    $back_trace = debug_backtrace();
    echo 'line <b>'.$back_trace[0]['line'].'</b> in '.$back_trace[0]['file'].'</br>'.PHP_EOL;
    if ($title) echo $title . PHP_EOL;
    var_dump($value);
    echo '</pre>'.PHP_EOL;
    die();
}


spl_autoload_register(function ($class_name) {
    include_once ROOT . DIRECTORY_SEPARATOR . $class_name . '.php';
});
function db():App\Core\DataBase\Db
{
    return App\Core\DataBase\Db::getInstance();
}
