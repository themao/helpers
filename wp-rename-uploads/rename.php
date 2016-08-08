<?php

include '../../wp-config.php';

if (!defined('DB_HOST') || !defined('DB_USER') || 
    !defined('DB_PASSWORD') || !defined('DB_NAME') || !isset($table_prefix)) {
    echo 'No config defined! Check ' . realpath('../wp-config.php');
    exit(1);
}

$db = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysql_select_db(DB_NAME);
mysql_query('set names utf8');
$q = "select * from {$table_prefix}postmeta where meta_key='_wp_attached_file' and post_id in (select post_id from {$table_prefix}posts where post_type='portfolio-post' and post_status='publish')";
$result = mysql_query($q);
$cnt=0;
while ($row=mysql_fetch_array($result)) {
    $file = $row['meta_value'];
    if (file_exists($file) && (strpos($file,'Cem')!==false||strpos($file,'Pola')!==false)) {
        $cnt++;
        $fileInfo = pathinfo($file);
        $image = $fileInfo['basename'];
        $encim = iconv("utf-8","ascii//TRANSLIT", $image);
        $enc = str_replace($image, $encim, $file);
        echo $enc, PHP_EOL;
        echo rename($file, $enc);
        echo mysql_query("update {$table_prefix}postmeta set meta_value='$enc' where meta_id={$row['meta_id']} limit 1"), PHP_EOL;
        $q2 = "select * from {$table_prefix}postmeta where meta_key='_wp_attachment_metadata' and post_id={$row['post_id']} and meta_value like '%$image%' and post_id in (select post_id from {$table_prefix}posts where post_type='portfolio-post' and post_status='publish')";
        $result2 = mysql_query($q2);
        while ($row2 = mysql_fetch_array($result2)) {
            $data = unserialize($row2['meta_value']);
            $data['file'] = iconv("utf-8","ascii//TRANSLIT", $data['file']);
            foreach ($data['sizes'] as $k => $v) {
                $oldFile = $v['file'];
                $data['sizes'][$k]['file'] = iconv("utf-8","ascii//TRANSLIT", $data['sizes'][$k]['file']);
                if (file_exists("{$fileInfo['dirname']}/{$oldFile}")) {
                    rename("{$fileInfo['dirname']}/{$oldFile}", "{$fileInfo['dirname']}/{$data['sizes'][$k]['file']}");
                }
            }
            mysql_query(sprintf("update {$table_prefix}postmeta set meta_value='%s' where meta_id='%s' limit 1", serialize($data), $row2['meta_id']));
        }
    }
}
echo "Processed: $cnt";
mysql_close($db);

