<?php

require_once 'database/config.php';

session_start();
ob_start();

if (isset($_POST['submit'])) {
    $username =  htmlspecialchars($_POST['form-username']);
    $password =  htmlspecialchars($_POST['form-password']);
    $loginStatus = false;
    $loginUserId = 0;
    $loginUserName = 0;

    $users = $db->query('SELECT * FROM users')->fetchAll(PDO::FETCH_ASSOC);


    if (!$username || !$password) {
        echo "<script>alert('Kullanıcı Adı ya da Şifre Boş');document.location='index.php?sayfa=login'</script>";
        exit;
    }
    foreach ($users as $user) {
        //echo md5($password) . '<br>';

        if ($username == $user['username'] && md5($password) ==  $user['password']) {
            $loginStatus  = true;
            $loginUserId = $user['idUser'];
            $loginUserName = $user['username'];
            $loginName =  $user['name'];
            $loginRole = $user['Roles_idRole'];
        } else {
            //echo 'hatalı';
        }
        /*echo $user['name'] . '<br>';

        echo $user['surname'];*/
    }


    if ($loginStatus == true) {
        $userRoles = $db->query("SELECT * FROM roles WHERE idRole=" . $loginRole)->fetch(PDO::FETCH_ASSOC);
        if ($userRoles['description'] == "Girişimci") {
            header('Location:view/index.php?userId=' . $loginUserId . '');
            $_SESSION['time']  = time() + 1000;
            $_SESSION['kullanici_adi']  = $username;
            $_SESSION['login'] = true;
        } else if ($userRoles['description'] == "Yetkili Hakem") {
            header('Location:view/yetkili-hakem.php?userId=' . $loginUserId . '');
            $_SESSION['time']  = time() + 1000;
            $_SESSION['kullanici_adi']  = $username;
            $_SESSION['login'] = true;
        } else if ($userRoles['description'] == "TTO Yetkilisi") {
            header('Location:view/tto-yetkili.php?userId=' . $loginUserId . '');
            $_SESSION['time']  = time() + 1000;
            $_SESSION['kullanici_adi']  = $username;
            $_SESSION['login'] = true;
        } else {
            echo "<script>alert('Yetkiniz bulunmamaktadır!');document.location='index.php'</script>";
        }
    } else {
        echo "<script>alert('Kullanıcı Adı ya da Şifre Yanlış');document.location='index.php'</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FSMVÜ - Teknoloji Transfer Ofisi</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/login.css">

    <!-- Favicon and touch icons -->
    <link rel="shortcut icon" href="/bitirme/assets/images/tto.png">

</head>


<body>

    <div class="container">
        <div class="row">
            <a href="index.php"> <img src="/bitirme/assets/images/logo.png" alt="Logo" class="logo" id="kurumlogo"></a>
            <div class="login-container col-lg-4 col-md-6 col-sm-8 col-xs-12">

                <div class="login-title text-center">
                    <h2><span>FSMVÜ <strong>Teknoloji Transfer Ofisi</strong></span></h2>
                </div>
                <div class="login-content">

                    <div class="login-body">
                        <form action="" method="post" class="login-form">
                            <div class="form-group ">
                                <div class="pos-r">
                                    <input id="form-username" type="text" name="form-username" placeholder="Kullanıcı Adı" class="form-username form-control">
                                    <i class="fa fa-user" style="color: white;"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="pos-r">
                                    <input id="form-password" type="password" name="form-password" placeholder="Şifre" class="form-password form-control">
                                    <i class="fa fa-lock" style="color: white;"></i>
                                </div>
                            </div>
                            <div class="form-group text-right">
                                <a href="forgot-password.php" class="bold"> Şifremi Unuttum</a>
                            </div>
                            <input type="hidden" name="submit" value="1">
                            <button type="submit" class="btn btn-primary form-control"><strong>Giriş</strong></button>


                        </form>
                    </div>
                    <!-- end  login-body -->
                </div>
                <!-- end  login-content -->
                <div class="login-footer text-center template">
                    <h5>Hesabın yok mu?<a href="register.php" class="bold"> Kayıt Ol </a> </h5>

                </div>
            </div>
            <!-- end  login-container -->

        </div>
    </div>
    <!-- end container -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>

</html>