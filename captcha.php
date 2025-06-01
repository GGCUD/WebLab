<?php
session_start();
header('Content-type: image/png');

$captcha = substr(md5(rand()), 0, 6);
$_SESSION['captcha'] = $captcha;

$image = imagecreatetruecolor(120, 40);
$bg = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 0, 0, 0);

imagefilledrectangle($image, 0, 0, 120, 40, $bg);
imagettftext($image, 20, 0, 10, 30, $text_color, 'arial.ttf', $captcha);

imagepng($image);
imagedestroy($image);
?>