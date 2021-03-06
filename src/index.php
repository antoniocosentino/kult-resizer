<?php
$env_debug_mode = getenv("DEBUG_MODE");

if ($env_debug_mode == 0){
    $debug_mode = false;
    ini_set('display_errors', 'Off');
}
else {
    $debug_mode = true;
}

if (!$_GET['file']) {
    echo "please provide filename";
    exit;
}

include("config.inc.php");

$original_folder = $_GET['folder'];

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
    if (!$debug_mode) {
        header('Content-Type: image/jpeg');
    }
    readfile($resized_file);
    exit;
}
// downloading the original file
$thedownload = file_get_contents($url);

if ($thedownload) {
    if ($debug_mode) {
        echo "<br />";
        echo "DOWNLOAD SUCCESSFUL";
    }
    header('Content-Type: image/jpeg');
}
else if (!$thedownload) {
    if ($debug_mode) {
        echo "<br />";
        echo "DOWNLOAD PROBLEM";
    }
    header("HTTP/1.0 404 Not Found");
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
    $width_contained = $sizearr[0];
    $height_contained = $sizearr[1];
}

// checking if portrait or landscape
if ($width >= $height) {
    $new_width = $contain === true ? $width_contained : $newsize;
    $new_height = floor($height / ($width / $new_width));

    if ($contain && $new_height > $height_contained){
        $new_height = $height_contained;
        $new_width = floor($width / ($height / $new_height));
    }
}
else {
    $new_height = $contain === true ? $height_contained : $newsize;
    $new_width = floor($width / ($height / $new_height));

    if ($contain && $new_width > $width_contained){
        $new_width = $width_contained;
        $new_height = floor($height / ($width / $new_width));
    }
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
