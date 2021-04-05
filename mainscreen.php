<?php

session_start();
ob_start();

/*
echo $_SESSION['kullanici_adi'] . '<br>';
echo $_SESSION['login'];
echo $_SESSION['time'];*/
if (!isset($_SESSION['login'])) {
    header('Location:index.php?sayfa=empty');
} elseif (isset($_SESSION['time']) && time() > $_SESSION['time']) {
    session_destroy();
    header('Location:index.php?sayfa=session-ended');
} else {
    if (isset($_POST['submit'])) {
        header('Location:index.php?sayfa=logout');
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <form action="" method="post">
        <input type="hidden" name="submit" value="1">
        <button type="submit" name="btnCikis"> Buton</button>
    </form>
</body>

</html>