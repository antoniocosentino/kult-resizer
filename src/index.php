<?php
$env_debug_mode = getenv("DEBUG_MODE");

if ($env_debug_mode == 0){
    $debug_mode = false;
}
else {
    $debug_mode = true;
}

if (!$_GET['file']) {
    echo "please provide filename";
    exit;
}

if (!$debug_mode) {
    header('Content-Type: image/jpeg');
}

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
    if ($debug_mode) {
        echo "FILE EXISTS";
        exit;
    }
    readfile($resized_file);
    exit;
}
// downloading the original file
$thedownload = file_get_contents($url);

if ($thedownload && $debug_mode) {
    echo "<br />";
    echo "DOWNLOAD SUCCESSFUL";
}
else if (!$thedownload && $debug_mode) {
    echo "<br />";
    echo "DOWNLOAD PROBLEM";
}

// copying it in the cache folder
$thecopy = file_put_contents($original_img, $thedownload);

if ($thecopy && $debug_mode) {
    echo "<br />";
    echo "COPY SUCCESSFUL";
}
else if (!$thecopy && $debug_mode) {
    echo "<br />";
    echo "COPY PROBLEM";
}


list($width, $height) = getimagesize($original_img);

if (strpos($newsize, "x")) {
    $contain = true;
    $sizearr = explode("x", $newsize);
    $smaller_value = min($sizearr);
}

// checking if portrait or landscape
if ($width >= $height) {
    $new_width = $contain === true ? $smaller_value : $newsize;
    $new_height = floor($height / ($width / $new_width));
}
else {
    $new_height = $contain === true ? $smaller_value : $newsize;
    $new_width = floor($width / ($height / $new_height));
}

$image_p = imagecreatetruecolor($new_width, $new_height);

if ($debug_mode){
    echo "<br />";
    echo "IMAGE P: ";
    print_r($image_p);
}

$image = imagecreatefromjpeg($original_img);

if ($debug_mode){
    echo "<br />";
    echo "IMAGE: ";
    print_r($image);
}

$copy_resampled = imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

if ($copy_resampled && $debug_mode)
{
    echo "<br />";
    echo "Copy resampled done";
}

if (!$debug_mode) {
    // output image to browser
    imagejpeg($image_p, null, 100);
}
// save resized image to cache folder
$theresizedcopy = imagejpeg($image_p, $resized_file, 100);

if ($theresizedcopy && $debug_mode)
{
    echo "<br />";
    echo "Copy done";
}

// delete original image from disk
unlink($original_img);
?>
