<?php

error_reporting(E_ALL);
include 'safemysql.php';
include 'networks/parser.php';
include 'networks/deadline.php';
include 'networks/variety.php';
include 'networks/tvline.php';
error_reporting(E_ERROR);

$action = $_POST['action'];
$url = $_POST['url'];
$site = $_POST['site'];
$next = $_POST['next'];


if (!is_null($url) and ! is_null($site)) {
    $obj = new $site;
    $obj->url = $url['href'];
    $arr = array('status' => 1, 'url' => $url);
    foreach ($url as $key => $v) {
        if ($key != 'href' and $key != 'site') {
            $done = $obj->get_article($v, $url['href']);
            if (!$done) {
                $arr = array('status' => 0);
                break;
            }
            sleep(5);
        }
    }

    echo json_encode($arr);
    exit;
}


if (!is_null($action) and ! is_null($site)) {
    if ($action == 'go') {
        $obj = new $site;
        $obj->go($site, $next);
    }
}



//$tvline = new tvline;
//$tvline->start();


//$deadline = new deadline;
//$deadline->start();

//$variety = new variety;
//$variety->start();


