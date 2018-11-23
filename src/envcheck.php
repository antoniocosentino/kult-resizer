<?
$cache_folder = getenv("CACHE_FOLDER");
$bucket_url = getenv("BUCKET_URL");
$debug_mode = getenv("DEBUG_MODE");
?>
<h3>Env variables test</h3>
Cache Folder: <?= $cache_folder ?>
<br />
Bucket Url: <?= $bucket_url   ?>
<br />
Debug Mode: <?= $debug_mode ?>
</pre>
