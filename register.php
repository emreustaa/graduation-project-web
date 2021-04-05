<?php

require 'model/user.php';
require 'database/config.php';

const girisimci = 'girisimci'; // -> 0
const rol = 'rol'; // -> -1
const yetkilihakem = 'yetkilihakem'; // -> 1
const ttoyetkilisi = 'ttoyetkilisi'; // -> 2


if (isset($_POST['submit'])) {

    $name = htmlspecialchars($_POST['name']) ?? null;
    $surname = htmlspecialchars($_POST['surname']) ?? null;
    $username = htmlspecialchars($_POST['username']) ?? null;
    $email = htmlspecialchars($_POST['email']) ?? null;
    $password = htmlspecialchars($_POST['password']) ?? null;
    $task = htmlspecialchars($_POST['task']) ?? null;
    $meslek = htmlspecialchars($_POST['meslek']) ?? 0;


    if (!$name) {
        echo 'İsim giriniz';
    } elseif (!$surname) {
        echo 'Soyad boş olamaz';
    } elseif (!$username) {
        echo 'Kullanıcı adı boş olamaz';
    } elseif (!$email) {
        echo 'E-Mail boş olamaz';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script type='text/javascript'>alert('Kayıt sırasında bir hata ile karşılaşıldı')</script>";
    } elseif (!$password) {
        echo 'Şifre boş olamaz';
    } elseif (!$task) {
        echo  'Görev boş olamaz';
    } else {

        if ($meslek == girisimci) {
            $Roles_idRole = 0;
        } elseif ($meslek == ttoyetkilisi) {
            $Roles_idRole = 2;
        } elseif ($meslek == yetkilihakem) {
            $Roles_idRole = 1;
        } else {
            $Roles_idRole = 3;
        }


        $sorgu = $db->prepare('INSERT INTO users SET name=:name, surname=:surname,username=:username,mail=:mail,password=:password,task=:task,Roles_idRole=:Roles_idRole');
        $insertSonuc = $sorgu->execute([
            'name' => $name,
            'surname' => $surname,
            'username' => $username,
            'mail' => $email,
            'password' => md5($password),
            'task' => $task,
            'Roles_idRole' => $Roles_idRole,
        ]);

        if ($insertSonuc) {
            echo "<script>alert('Kayıt Başarılı!');document.location='index.php'</script>";
            //header('Location: index.php');
        } else {

            echo "<script type='text/javascript'>alert('Kayıt sırasında bir hata ile karşılaşıldı')</script>";
        }
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/register.css">
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
                                    <input id="name" type="text" name="name" placeholder="Adı" class="form-username form-control" required>
                                    <i class="fa fa-user-o" style="color: white;"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="pos-r">
                                    <input id="surname" type="text" name="surname" placeholder="Soyadı" class="form-password form-control" required>
                                    <i class="fa fa-user-o" style="color: white;"></i>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="pos-r">
                                    <input required id="username" type="text" name="username" placeholder="Kullanıcı Adı" class="form-password form-control">
                                    <i class="fa fa-user-o" style="color: white;"></i>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="pos-r">
                                    <input required id="email" type="text" name="email" placeholder="E-Mail" class="form-password form-control">
                                    <i class="fa fa-envelope-o" style="color: white;"></i>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="pos-r">
                                    <input required id="password" type="password" name="password" placeholder="Şifre" class="form-password form-control">
                                    <i class="fa fa-lock" style="color: white;"></i>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="pos-r">
                                    <input required id="task" type="text" name="task" placeholder="Görev" class="form-password form-control">
                                    <i class="fa fa-tasks" style="color: white;"></i>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="pos-r">
                                    <select name="meslek" class="meslek" required>
                                        <option value="rol"> -- Rol seçiniz -- </option>
                                        <option value="girisimci">Girişimci</option>
                                        <option value="ttoyetkilisi"> TTO Yetkilisi</option>
                                        <option value="yetkilihakem"> Yetkili Hakem</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group text-right">
                                <a href="forgot-password.php" class="bold"> Şifremi Unuttum</a>
                            </div>
                            <input type="hidden" name="submit" value="1">
                            <button type="submit" class="btn btn-primary form-control" id="btnsave" name="btnsave"><strong>Kaydet</strong></button>


                        </form>
                    </div>
                    <!-- end  login-body -->
                </div>
                <!-- end  login-content -->
                <div class="login-footer text-center template">
                    <h5>Hesabın var mı?<a href="../bitirme/index.php" class="bold"> Giriş Yap </a> </h5>

                </div>
            </div>
            <!-- end  login-container -->

        </div>
    </div>
    <!-- end container -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>


</body>

</html>