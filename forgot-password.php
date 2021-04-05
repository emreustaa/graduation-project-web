<?php

if (isset($_POST['submit'])) {
    echo $_POST['form-username'];
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
    <link rel="stylesheet" href="assets/css/forgot-password.css">

    <!-- Favicon and touch icons -->
    <link rel="shortcut icon" href="/bitirme/assets/images/tto.png">

</head>


<body>

    <div class="container">
        <div class="row">
        <a href="index.php"> <img  src="/bitirme/assets/images/logo.png" alt="Logo" class="kurumlogo" id="kurumlogo"></a>
            <div class="login-container col-lg-4 col-md-6 col-sm-8 col-xs-12">

              
                <div class="login-content">

                    <div class="login-body">
                        <form action="" method="post" class="login-form">

                            <div class="form-group">
                                <div class="pos-r">
                                    <input id="form-password" type="password" name="form-password" placeholder="Eski Şifre" class="form-password form-control">
                                    <i class="fa fa-lock" style="color: white;"></i>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="pos-r">
                                    <input id="form-password" type="password" name="form-password" placeholder="Yeni Şifre" class="form-password form-control">
                                    <i class="fa fa-lock" style="color: white;"></i>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="pos-r">
                                    <input id="form-password" type="password" name="form-password" placeholder="Yeni Şifre" class="form-password form-control">
                                    <i class="fa fa-lock" style="color: white;"></i>
                                </div>
                            </div>
                            <div class="form-group text-right">
                                <a href="#" class="bold"> Şifremi Unuttum</a>
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