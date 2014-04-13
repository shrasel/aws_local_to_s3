<?php
require_once ('vendor/autoload.php');
require_once ('config.php');
require_once ('lib.php');

use Aws\S3\S3Client;

$ltos = new localToS3;

$ltos->upload('photos');

?>