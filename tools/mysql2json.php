<?php
function scaleImageFileToBlob($file, $max_width=0, $max_height=0) {

    $source_pic = $file;

    list($width, $height, $image_type) = getimagesize($file);
    $max_width = ($max_width==0?$width:$max_width);
    $max_height = ($max_height==0?$height:$max_height);

    switch ($image_type)
    {
        case 1: $src = imagecreatefromgif($file); break;
        case 2: $src = imagecreatefromjpeg($file);  break;
        case 3: $src = imagecreatefrompng($file); break;
        default: return '';  break;
    }
    if (!$src) {
        return '';
    }

    $x_ratio = $max_width / $width;
    $y_ratio = $max_height / $height;

    if( ($width <= $max_width) && ($height <= $max_height) ){
        $tn_width = $width;
        $tn_height = $height;
        }elseif (($x_ratio * $height) < $max_height){
            $tn_height = ceil($x_ratio * $height);
            $tn_width = $max_width;
        }else{
            $tn_width = ceil($y_ratio * $width);
            $tn_height = $max_height;
    }

    $tmp = imagecreatetruecolor($tn_width,$tn_height);

    // fill white color
    imagefill($tmp, 0, 0, imagecolorallocate($tmp, 255, 255, 255));

    imagecopyresampled($tmp,$src,0,0,0,0,$tn_width, $tn_height,$width,$height);

    ob_start();

    imagejpeg($tmp, NULL, 80); // medium quality

    $final_image = ob_get_contents();

    ob_end_clean();

    return array($final_image, $tn_width, $tn_height);
}

function begin(){
    mysql_query("BEGIN");
}

function commit(){
    mysql_query("COMMIT");
}

function rollback(){
    mysql_query("ROLLBACK");
}

$con = mysqli_connect("127.0.0.1","root","","miraiki");
if (mysqli_connect_errno())
{
	die("Failed to connect to MySQL: " . mysqli_connect_error());
}

$d = mysqli_query($con, "SELECT * FROM `assetsTree`;");
$pics = array();
while($r = mysqli_fetch_assoc($d)) {
    if ($r['tag']=='Pic') {continue;
        if (!in_array($r['album'], array('壁纸-h', '壁纸-w'))) {
            continue;
        }
        if (!isset($pics[$r['album']])) {
            @mkdir(__DIR__."/../source/images/{$r['tag']}", 0777, true);
            $album = array();
            $album['title'] = $r['album'];
            $album['path'] = "pics/".$r['album'].".html";
            $album['photos'] = array("/images/{$r['album']}.cover.jpg");
            $album['list'] = array();
            $album['thumbnailrate'] = 1;

            $pics[$r['album']] = $album;
        }

        $r['thumbnail'] = "/images/{$r['tag']}/{$r['md5']}.jpg";
        if (file_exists(__DIR__."/../source/{$r['thumbnail']}")) {
            list($width, $height) = getimagesize(__DIR__."/../source/{$r['thumbnail']}");
            $r['thumbnailrate'] = sprintf("%.2f", $width/$height);
        }
        $pics[$r['album']]['list'][] = $r;

        continue;
    }
    if ($r['type'] != 'dir') continue;
    if ($r['tag'] == 'Fanart') continue;
    if ($r['tag'] == 'Comic') continue;
    if ($r['tag'] == 'CV') continue;
    if ($r['tag'] == 'CV 3D') continue;
    if ($r['tag'] == 'CV Msc') continue;
    if ($r['tag'] == 'AV Msc') continue;

    $r['name'] = str_replace('\'', '`', $r['name']);
    $r['tag'] = str_replace('\'', '`', $r['tag']);
    $r['album'] = str_replace('\'', '`', $r['album']);

    $r['photos'] = array();
    $r['createTime'] = date("Y-m-d H:i:s", $r['createTime']);

    $r['thumbnail'] = "/images/{$r['tag']}/{$r['name']}.jpg";
    $r['thumbnailrate'] = 1;
    if (file_exists(__DIR__."/../source/{$r['thumbnail']}")) {
        list($width, $height) = getimagesize(__DIR__."/../source/{$r['thumbnail']}");
        $r['thumbnailrate'] = sprintf("%.2f", $width/$height);

        $r['thumbnailsmall'] = "/images/{$r['tag']}/{$r['name']}.small.jpg";
        if (!file_exists(__DIR__."/../source/{$r['thumbnailsmall']}")) {
            if ($r['thumbnailrate'] > 1.33)
                list($thumbnail) = scaleImageFileToBlob(__DIR__."/../source/{$r['thumbnail']}", 0, 200);
            else
                list($thumbnail) = scaleImageFileToBlob(__DIR__."/../source/{$r['thumbnail']}", 266, 0);
            file_put_contents(__DIR__."/../source/{$r['thumbnailsmall']}", $thumbnail);
        }
    }
    $name = addslashes($r['name']);
    $createTime = $r['createTime'];
    $updateTime = $r['updateTime'];
    $tag = addslashes($r['tag']);
    $album = addslashes($r['album']);
    $sizef = addslashes($r['sizef']);
    $md = <<< EOT
title: '{$name}'
date: '{$createTime}'
updated: '{$updateTime}'
categories: '{$tag}'
tags: '{$album}'
thumbnail: '/images/{$tag}/{$name}.small.jpg'
photos: '/images/{$tag}/{$name}.jpg'
type: picture
size: '{$sizef}'
thumbnailrate: {$r['thumbnailrate']}
---
<!--more-->
{$r['name']}
{$r['sizef']}
{$r['path']}

EOT;
    file_put_contents(__DIR__."/../source/_posts/{$r['name']}.md", $md);
}
file_put_contents(__DIR__."/../source/_data/pics.json", json_encode($pics));

mysqli_free_result($d);
mysqli_close($con);

// file_put_contents(__DIR__."/../source/_data/assets.json", json_encode($data));
// 266 * 200