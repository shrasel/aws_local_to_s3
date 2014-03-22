<?php

require_once ('config.php');
require_once ('vendor/autoload.php');

use Aws\S3\S3Client;

class localToS3{

    public  $S3;

    public function __construct () {
        $this->S3 = S3Client::factory(array(
            'key' => AWS_KEY,
            'secret' => AWS_SECRET
        ));

    }

    public function dirToArray($directory, $recursive)
    {
        $array_items = array();
        if ($handle = opendir($directory)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    if (is_dir($directory . "/" . $file)) {
                        if ($recursive) {
                            $array_items = array_merge($array_items, $this->dirToArray($directory . "/" . $file, $recursive));
                        }
                        $file = $directory . "/" . $file;
                        $array_items[] = preg_replace("/\/\//si", "/", $file);
                    } else {
                        $file = $directory . "/" . $file;
                        $array_items[] = preg_replace("/\/\//si", "/", $file);
                    }
                }
            }
            closedir($handle);
        }
        return $array_items;
    }

    public function upload($dir)
    {

        $files = $this->dirToArray($dir, 1);

        foreach ($files as $ff) {

            // get the file extension
            $ext = pathinfo($ff, PATHINFO_EXTENSION);

            //check if its not a directory and file extension exists
            if (!is_dir($ff) && in_array($ext, array('jpg', 'png'))) {

                $s3_filename = basename($ff);

                $s3_path = IMAGE_PATH . $ff;

                $this->S3->putObject(array(
                    'Bucket' => BUCKET,
                    'Key' => $s3_filename,
                    'SourceFile' => $s3_path,
                    'ACL' => 'public-read'
                ));

                echo $s3_filename . " file uploaded <br>";

            }
        }
    }

}


$ltos = new localToS3;
$ltos->upload('photos');


?>