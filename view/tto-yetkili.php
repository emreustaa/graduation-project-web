<?php

require '../database/config.php';
session_start();
ob_start();


if (!isset($_SESSION['login'])) {
    header('Location:../empty.php');
} elseif (isset($_SESSION['time']) && time() > $_SESSION['time']) {
    session_destroy();
    header('Location:../session-ended.php');
}
if (isset($_GET['userId'])) {
    $loginUsers = $db->query('SELECT * FROM users WHERE idUser=' . $_GET['userId'] . '')->fetch(PDO::FETCH_ASSOC);
    $userRoles = $db->query("SELECT * FROM roles WHERE idRole=" . $loginUsers['Roles_idRole'])->fetch(PDO::FETCH_ASSOC);
    $totalUsers = $db->query("SELECT COUNT(idUser) toplamKullanici from users")->fetch(PDO::FETCH_ASSOC);
    $totalApply = $db->query("SELECT COUNT(idApply) toplamBasvuru from applies")->fetch(PDO::FETCH_ASSOC);
    $totalConfirm = $db->query("SELECT COUNT(idConfirms) toplamOnay from confirms")->fetch(PDO::FETCH_ASSOC);
    $totalAmount  = $db->query('SELECT SUM(confirmedAmount) toplamTutar FROM applies')->fetch(PDO::FETCH_ASSOC);
    $allSectors = $db->query('SELECT * FROM sectors')->fetchAll(PDO::FETCH_ASSOC);
    $allUsers = $db->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">


    <!-- Title Page-->
    <title>TTO YETKİLİ</title>

    <!-- Fontfaces CSS-->
    <link href="css/font-face.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">
    <link href="vendor/vector-map/jqvmap.min.css" rel="stylesheet" media="all">
    <link rel="shortcut icon" href="/bitirme/assets/images/tto.png">
    <!-- Main CSS-->
    <link href="css/theme.css" rel="stylesheet" media="all">

    <style>
        .container-image-add {
            position: relative;
            text-align: center;
            color: white;
        }

        .centered {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 60px;
        }
    </style>


</head>

<body class="animsition">
    <div class="page-wrapper">
        <!-- MENU SIDEBAR-->
        <aside class="menu-sidebar2">
            <div class="logo">
                <a href="#">
                    <img src="images/icon/logo-white.png" alt="Cool Admin" />
                </a>
            </div>
            <div class="menu-sidebar2__content js-scrollbar1">
                <div class="account2">
                    <div class="image img-cir img-120">
                        <div class="container-image-add">
                            <img src="images/icon/profile.png" alt="Fsmvü" />
                            <div class="centered"> <?php echo ($loginUsers['name']) ? strtoupper(substr($loginUsers['name'], 0, 1))  : '' ?></div>
                        </div>
                    </div>
                    <h4 class="name"><?php echo $loginUsers['name'] . ' ' .$loginUsers['surname'] ?></h4>
                    <h4><?php echo $userRoles['description'] ?></h4>
                    <br>
                    <a href="../index.php">Çıkış</a>
                </div>
                <nav class="navbar-sidebar2">
                    <ul class="list-unstyled navbar__list">
                        <li class="active has-sub">
                            <a class="js-arrow" href="tto-yetkili.php?userId=<?php echo $_GET['userId'] ?>">
                                <i class="fas fa-home"></i>Ana Sayfa
                            </a>

                        </li>

                        <li class="has-sub">
                            <a class="js-arrow" href="tto-yetkili-table.php?userId=<?php echo $_GET['userId'] ?>">
                                <i class="fas fa-trophy"></i>Proje
                                <span class="arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </span>
                            </a>

                        </li>
                        <li class="has-sub">
                            <a class="js-arrow" href="tto-yetkili-patent-table.php?userId=<?php echo $_GET['userId'] ?>">
                                <i class="fas fa-thumbtack"></i>Patent
                                <span class="arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>
        <!-- END MENU SIDEBAR-->

        <!-- PAGE CONTAINER-->
        <div class="page-container2">
            <!-- HEADER DESKTOP-->
            <header class="header-desktop2">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap2">
                            <div class="logo d-block d-lg-none">
                                <a href="#">
                                    <img src="images/icon/logo-white.png" alt="CoolAdmin" />
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </header>

            <!-- END HEADER DESKTOP-->

            <!-- BREADCRUMB-->
            <section class="au-breadcrumb m-t-75">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="au-breadcrumb-content">
                                    <div class="au-breadcrumb-left">
                                        <span class="au-breadcrumb-span">Buradasınız:</span>
                                        <ul class="list-unstyled list-inline au-breadcrumb__list">
                                            <li class="list-inline-item">
                                                <?php
                                                $bolunen =  explode("/", $_SERVER['PHP_SELF'], 3);
                                                echo ucfirst($bolunen[0]) . '/' . ucfirst($bolunen[1]) . '/'  . ucfirst($bolunen[2]);
                                                ?></li>
                                        </ul>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- END BREADCRUMB-->

            <!-- STATISTIC-->
            <section class="statistic">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6 col-lg-3">
                                <div class="statistic__item">
                                    <h2 class="number"><?php echo $totalUsers['toplamKullanici'] ?></h2>
                                    <span class="desc">Toplam Kullanıcı Sayısı</span>
                                    <div class="icon">
                                        <i class="zmdi zmdi-account-o"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="statistic__item">
                                    <h2 class="number"><?php echo $totalApply['toplamBasvuru'] ?></h2>
                                    <span class="desc">Toplam Proje Sayısı</span>
                                    <div class="icon">
                                        <i class="zmdi zmdi-file"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="statistic__item">
                                    <h2 class="number"><?php echo $totalConfirm['toplamOnay'] ?></h2>
                                    <span class="desc">Onaylanan Proje Sayısı</span>
                                    <div class="icon">
                                        <i class="zmdi zmdi-check"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="statistic__item">
                                    <h2 class="number"><?php echo '₺' . $totalAmount['toplamTutar'] ?></h2>
                                    <span class="desc">Toplam Verilen Destek</span>
                                    <div class="icon">
                                        <i class="fa fa-turkish-lira"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- END STATISTIC-->

            <section>
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-xl-4">
                                <!-- TASK PROGRESS-->
                                <div class="task-progress">
                                    <h3 class="title-3">Sektörlere göre başvurular</h3>
                                    <div class="au-skill-container">
                                        <?php foreach ($allSectors as $sector) : ?>
                                            <div class="au-progress">
                                                <span class="au-progress__title"><?php echo $sector['name'] ?></span>
                                                <br> <br>
                                                <div class="au-progress__bar">
                                                    <div class="au-progress__inner js-progressbar-simple" role="progressbar" data-transitiongoal="
                                                    <?php

                                                    $sorgu = $db->query("SELECT COUNT(idApply) as sayi FROM applies WHERE Sectors_idSectors=" .  $sector['idSectors'])->fetch(PDO::FETCH_ASSOC);
                                                    echo $sorgu['sayi'];
                                                    //print_r($sorgu);

                                                    ?>">
                                                        <span class="au-progress__value js-value"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <!-- END TASK PROGRESS-->
                            </div>

                            <div class="col-xl-6">
                                <!-- USER DATA-->
                                <div class="user-data m-b-40">
                                    <h3 class="title-3 m-b-30">
                                        <i class="zmdi zmdi-account-calendar"></i>Kayıtlı Kullanıcılar
                                    </h3>

                                    <div class="table-responsive table-data">
                                        <table class="table">
                                            <thead>
                                                <tr>

                                                    <td>İsim</td>
                                                    <td>Rol</td>
                                                    <td>Meslek</td>
                                                    <td></td>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                <?php foreach ($allUsers as $user) : ?>
                                                    <tr>

                                                        <td>
                                                            <div class="table-data__info">
                                                                <h6><?php echo $user['name'] ?></h6>
                                                                <span>
                                                                    <a href="#"><?php echo $user['mail'] ?></a>
                                                                </span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $role = $db->query("SELECT * FROM roles WHERE idRole=" . $user['Roles_idRole'])->fetch(PDO::FETCH_ASSOC);
                                                            if ($role['description'] == "Girişimci") {
                                                                $roleClass = "role user";
                                                                $roleName = $role['description'];
                                                            } else if ($role['description'] == "Yetkili Hakem") {
                                                                $roleClass = "role admin";
                                                                $roleName = $role['description'];
                                                            } else if ($role['description'] == "TTO Yetkilisi") {
                                                                $roleClass = "role member";
                                                                $roleName = $role['description'];
                                                            }
                                                            ?>
                                                            <span class="<?php echo $roleClass ?>"><?php echo $roleName ?></span>
                                                        </td>
                                                        <td>
                                                            <div class="table-data__info">
                                                                <h6><?php echo $user['task'] ?></h6>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="more">
                                                                <i class="zmdi zmdi-more"></i>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>


                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                                <!-- END USER DATA-->
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="copyright">
                                <p>© 2021 - Developed by Emre USTA - <a href="https://www.fsm.edu.tr">FSMVÜ</a>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- END PAGE CONTAINER-->
        </div>

    </div>

    <!-- Jquery JS-->
    <script src="vendor/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap JS-->
    <script src="vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <!-- Vendor JS       -->
    <script src="vendor/slick/slick.min.js">
    </script>
    <script src="vendor/wow/wow.min.js"></script>
    <script src="vendor/animsition/animsition.min.js"></script>
    <script src="vendor/bootstrap-progressbar/bootstrap-progressbar.min.js">
    </script>
    <script src="vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="vendor/counter-up/jquery.counterup.min.js">
    </script>
    <script src="vendor/circle-progress/circle-progress.min.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="vendor/chartjs/Chart.bundle.min.js"></script>
    <script src="vendor/select2/select2.min.js">
    </script>
    <script src="vendor/vector-map/jquery.vmap.js"></script>
    <script src="vendor/vector-map/jquery.vmap.min.js"></script>
    <script src="vendor/vector-map/jquery.vmap.sampledata.js"></script>
    <script src="vendor/vector-map/jquery.vmap.world.js"></script>

    <!-- Main JS-->
    <script src="js/main.js"></script>

</body>

</html>
<!-- end document-->