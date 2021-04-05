<?php


session_start();

ob_start();

session_destroy();

echo $_SESSION['login'];
echo 'YETKİNİZ YOK';
