<?php
$time = microtime();
$time = explode(' ', $time);
$time = $time[1] + $time[0];
$start = $time;
?>
<style>
    .file-code-line .blob-line-code {
        background: #333;
    }

    .file-code-line .blob-line-nums {
        background: #292929;
        border-right: 3px solid #707070;
        color: #c0c0c0;
    }

    .file-data .line-numbers {
        font-size: 14px;
    }

    .highlight {
        background: rgb(38,41,40);
        color: #f8f8f2 ;
    }

    .highlight .bp {
        color: #f8f8f2 ;
    }

    .highlight .c {
        color: #75715e ;
    }

    .highlight .c1 {
        color: #75715e ;
    }

    .highlight .c1,pre .cm {
        color: rgb(117,113,94);
        font-style: normal;
    }

    .highlight .cm {
        color: #75715e ;
    }

    .highlight .cp {
        color: #75715e ;
    }

    .highlight .cs {
        color: #75715e ;
    }

    .highlight .err {
        background-color: #1e0010 ;
        color: #960050;
    }

    .highlight .ge {
        font-style: italic ;
    }

    .highlight .gs {
        font-weight: bold ;
    }

    .highlight .hll {
        background-color: #49483e ;
    }

    .highlight .il {
        color: #ae81ff ;
    }

    .highlight .k {
        color: #66d9ef ;
    }

    .highlight .k ,.highlight .nt,.highlight .kd {
        color: rgb(249,38,114);
        font-weight: normal;
    }

    .highlight .kc {
        color: #66d9ef ;
    }

    .highlight .kd {
        color: #66d9ef ;
    }

    .highlight .kn {
        color: #f92672 ;
    }

    .highlight .kp {
        color: #66d9ef ;
    }

    .highlight .kr {
        color: #66d9ef ;
    }

    .highlight .kt {
        color: #66d9ef ;
    }

    .highlight .l {
        color: #ae81ff ;
    }

    .highlight .ld {
        color: #e6db74 ;
    }

    .highlight .m {
        color: #ae81ff ;
    }

    .highlight .mf {
        color: #ae81ff ;
    }

    .highlight .mh {
        color: #ae81ff ;
    }

    .highlight .mi {
        color: #ae81ff ;
    }

    .highlight .mo {
        color: #ae81ff ;
    }

    .highlight .n {
        color: #f8f8f2 ;
    }

    .highlight .na {
        color: #a6e22e ;
    }

    .highlight .nb {
        color: #f8f8f2 ;
    }

    .highlight .nc {
        color: #a6e22e ;
    }

    .highlight .nd {
        color: #a6e22e ;
    }

    .highlight .ne {
        color: #a6e22e ;
    }

    .highlight .nf {
        color: #a6e22e ;
    }

    .highlight .ni {
        color: #f8f8f2 ;
    }

    .highlight .nl {
        color: #f8f8f2 ;
    }

    .highlight .nn {
        color: #f8f8f2 ;
    }

    .highlight .no {
        color: #66d9ef ;
    }

    .highlight .nt {
        color: #f92672 ;
    }

    .highlight .nv {
        color: #f8f8f2 ;
    }

    .highlight .nv,.highlight .na {
        color: rgb(166,226,46);
    }

    .highlight .nx {
        color: #a6e22e ;
    }

    .highlight .o {
        color: #f92672 ;
    }

    .highlight .ow {
        color: #f92672 ;
    }

    .highlight .p {
        color: #f8f8f2 ;
    }

    .highlight .py {
        color: #f8f8f2 ;
    }

    .highlight .s {
        color: #e6db74 ;
    }

    .highlight .s,.highlight .s2, .highlight .s1 {
        color: rgb(229,218,91)
    ;
    }

    .highlight .s1 {
        color: #e6db74 ;
    }

    .highlight .s2 {
        color: #e6db74 ;
    }

    .highlight .sb {
        color: #e6db74 ;
    }

    .highlight .sc {
        color: #e6db74 ;
    }

    .highlight .sd {
        color: #e6db74 ;
    }

    .highlight .se {
        color: #ae81ff ;
    }

    .highlight .sh {
        color: #e6db74 ;
    }

    .highlight .si {
        color: #e6db74 ;
    }

    .highlight .sr {
        color: #e6db74 ;
    }

    .highlight .ss {
        color: #e6db74 ;
    }

    .highlight .sx {
        color: #e6db74 ;
    }

    .highlight .vc {
        color: #f8f8f2 ;
    }

    .highlight .vg {
        color: #f8f8f2 ;
    }

    .highlight .vi {
        color: #f8f8f2 ;
    }

    .highlight .w {
        color: #f8f8f2 ;
    }

    .highlight pre {
        background: transparent;
        color: #f8f8f2 ;
    }

    .highlight pre span {
        font-weight: normal;
    }

    .markdown-body pre {
        border: 0;
        border-radius: 0;
        padding-left: 10px;
    }

    div.code-body.highlight,pre,.data.highlight {
        background: rgb(38,41,40);
        color: #ddd;
        font-family: 'Monaco',"Consolas";
        font-size: 16px;
    }

    .file-code {
        background: rgb(38,41,40);
    }

    .highlight .gd .diff-line-code,.highlight .gd .diff-line-num {
        background: #a00;
    }

    .highlight .gi .diff-line-code,.highlight .gi .diff-line-num {
        background: #006A74;
    }

    .highlight .gc .diff-line-code,.highlight .gc .diff-line-num {
        background: #948200;
    }

    .highlight .gi .x {
        background: #006A74;
        color: #fff;
    }

    .file-diff-line:hover .diff-line-code,.file-diff-line:hover .diff-line-num {
        background: #000;
    }

    pre {
        font-family: "Ayuthaya","Lucida Sans Typewriter","Menlo","Monospaced","Courier","Andale Mono";
        font-size: 14px;
        font-weight: normal;
        line-height: 1.5em;
    }

    pre code {
        font-size: 14px;
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

$ip = "ngsi.vm9it.com";