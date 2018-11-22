<?
$cache_folder = getenv("CACHE_FOLDER");
$bucket_url = getenv("BUCKET_URL");
?>
<pre>
Env variables test
Cache Folder: <?= $cache_folder ?>
Bucket Url:   <?= $bucket_url   ?>
</pre>