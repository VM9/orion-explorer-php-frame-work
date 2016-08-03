<?php
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;
?>
<style>
    pre {
        display:block;
        font:normal 12px/22px Monaco,Monospace !important;
        color:#CFDBEC;
        background-color:#272821;
        background-image:-webkit-repeating-linear-gradient(top, #444 0px, #444 22px, #272821 22px, #272821 44px);
        background-image:-moz-repeating-linear-gradient(top, #444 0px, #444 22px, #272821 22px, #272821 44px);
        background-image:-ms-repeating-linear-gradient(top, #444 0px, #444 22px, #272821 22px, #272821 44px);
        background-image:-o-repeating-linear-gradient(top, #444 0px, #444 22px, #272821 22px, #272821 44px);
        background-image:repeating-linear-gradient(top, #444 0px, #444 22px, #272821 22px, #272821 44px);
        padding:0em 1em;
        overflow:auto;
    }
    code{
        display:block;
        font:normal 12px/22px Monaco,Monospace !important;
        background-color:#676360;
        background-image:-webkit-repeating-linear-gradient(top, #272821 0px, #272821 22px, #272822 22px, #272822 44px);
        background-image:-moz-repeating-linear-gradient(top, #272821 0px, #272821 22px, #272822 22px, #272822 44px);
        background-image:-ms-repeating-linear-gradient(top, #272821 0px, #272821 22px, #272822 22px, #272822 44px);
        background-image:-o-repeating-linear-gradient(top, #272821 0px, #272821 22px, #272822 22px, #272822 44px);
        background-image:repeating-linear-gradient(top, #272821 0px, #272821 22px, #272822 22px, #272822 44px);
        padding:0em 1em;
        overflow:auto;
    }
</style>
<a href="../example">Back</a>
<?php
//Autoloader compatible with psr-0  for example scripts
$base =  getcwd() . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR;
set_include_path("{$base}src" . PATH_SEPARATOR . "{$base}lib" . PATH_SEPARATOR . get_include_path());

//https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-0.md
//PSR-0 auto loader
spl_autoload_register(function ($class) use ($base) {
    if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
        $classname = str_replace('\\', '/', $class . '.php');
    } else {
        $classname = $class . '.php'; //str_replace('_', DIRECTORY_SEPARATOR, $class . '.php');
    }

    $filepath = "{$base}%folder%" . DIRECTORY_SEPARATOR .
            $classname;

    $is_module = file_exists(str_replace("%folder%", "src", $filepath));
    $is_lib = file_exists(str_replace("%folder%", "lib", $filepath));

    if ($is_module || $is_lib) {
        require_once $classname;
    } else {
//        die("Class Not Found {$classname} in {$base}");
    }
});

//Some functions to help samples


function getRandLat() {
    return rand(-83, 83) + (mt_rand() / 10000000000);
}

function getRandLng() {
    return rand(-178, 179) + (mt_rand() / 10000000000);
}