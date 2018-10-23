<?php
if (!$_GET['file']) {
    echo "please provide filename";
    exit;
}

header('Content-Type: image/jpeg');

include("config.inc.php");

$original_folder = $_GET['folder'];

// this allows to use the root level of the bucket
if ($original_folder == "root") {
    $original_folder = null;
}

$original_name = $_GET['file'];
$newsize = $_GET['size'] ? $_GET['size'] : 500;

$url = $original_folder ? $bucket_url . "/" . $original_folder . "/" . urlencode($original_name) : $bucket_url . "/" . urlencode($original_name)  ;
$original_extension = pathinfo($url, PATHINFO_EXTENSION);
$original_name_no_extension = pathinfo($url, PATHINFO_FILENAME);
$original_img = $cache_folder . "/" . $original_folder . "_" . $original_name;
$resized_file = $cache_folder . "/" . $original_folder . "_" . $original_name_no_extension . "_" . $newsize . ".". $original_extension;

// if file already exists in the cache we just output it
if (file_exists($resized_file)) {
    readfile($resized_file);
    exit;
}

// downloading the original file
file_put_contents($original_img, file_get_contents($url));

list($width, $height) = getimagesize($original_img);

// checking if portrait or landscape
if ($width >= $height) {
    $new_width = $newsize;
    $new_height = floor($height / ($width / $newsize));
}
else {
    $new_height = $newsize;
    $new_width = floor($width / ($height / $newsize));
}

$image_p = imagecreatetruecolor($new_width, $new_height);
$image = imagecreatefromjpeg($original_img);
imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

// output image to browser
imagejpeg($image_p, null, 100);
// save resized image to cache folder
imagejpeg($image_p, $resized_file, 100);

// delete original image from disk
unlink($original_img);
?>
