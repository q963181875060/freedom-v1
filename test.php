<?php

$to = "291477321@qq.com";
$subject = "My subject";
$txt = "Hello world!";
$headers = "From: webmaster@example.com" . "\r\n" .
    "CC: somebodyelse@example.com";

$rt = mail($to,$subject,$txt,$headers);
$rt1 = 1;
?>