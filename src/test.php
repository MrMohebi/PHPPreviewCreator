<?php
//use MrMohebi\PHPPreviewCreator\VideoGif;

require_once "../vendor/autoload.php";
$videoGif = new \MrMohebi\PHPPreviewCreator\VideoGif("/../temp8585");
try {
    $videoGif->create('./testVideo.mp4', 'result_'.time().'.gif');
} catch (Exception $e) {
    print_r($e);
}


// php -S 127.0.0.1:8001