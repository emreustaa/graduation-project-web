<?php

require_once '../database/config.php';
session_start();
ob_start();

date_default_timezone_set('Europe/Istanbul');
setlocale(LC_ALL, 'tr_TR', 'Turkish');

if (!isset($_SESSION['login'])) {
    header('Location:../empty.php');
} elseif (isset($_SESSION['time']) && time() > $_SESSION['time']) {
    session_destroy();
    header('Location:../session-ended.php');
}
if (isset($_GET['userId'])) {

    $allUsers = $db->query('SELECT * FROM users WHERE idUser=' . $_GET['userId'] . '')->fetch(PDO::FETCH_ASSOC);
    $categories  = $db->query('SELECT * FROM categories')->fetchAll(PDO::FETCH_ASSOC);
    $naceCodes = $db->query('SELECT * FROM naceCodes')->fetchAll(PDO::FETCH_ASSOC);
    $sectors = $db->query('SELECT * FROM sectors')->fetchAll(PDO::FETCH_ASSOC);
    $roles = $db->query('SELECT * FROM roles')->fetchAll(PDO::FETCH_ASSOC);
    $loginUsers = $db->query('SELECT * FROM users WHERE idUser=' . $_GET['userId'] . '')->fetch(PDO::FETCH_ASSOC);
    $userRoles = $db->query("SELECT * FROM roles WHERE idRole=" . $loginUsers['Roles_idRole'])->fetch(PDO::FETCH_ASSOC);
    if (isset($_POST['submit'])) {
        $projectName = isset($_POST['project-name-input']) ? $_POST['project-name-input'] : null;
        $projectCode = isset($_POST['project-code-input']) ? $_POST['project-code-input'] : null;
        $projectTutar = isset($_POST['talep-tutar-input']) ? $_POST['talep-tutar-input'] : null;
        $projectNaceCode = isset($_POST['selectSm']) ? $_POST['selectSm'] : null;
        $projectSectors = isset($_POST['sectors']) ? $_POST['sectors'] : null;
        $projectContent = isset($_POST['proje-icerik-input']) ? $_POST['proje-icerik-input'] : null;
        $startDate = isset($_POST['start-date-input']) ? $_POST['start-date-input'] : null;
        $categories = isset($_POST['categories']) ? $_POST['categories'] : null;
        $deadlineInput = isset($_POST['deadline-input']) ? $_POST['deadline-input'] : null;
        $projectGoal = isset($_POST['project-goal']) ? $_POST['project-goal'] : null;

        if (!$projectName) {
            $result['sonuc'] = 'Proje ismi girilmedi';
        } else if (!$projectCode) {
            $result['sonuc'] = 'Proje kodu girilmedi';
        } else if (!$projectTutar) {
            $result['sonuc'] = 'Talep edilen tutar girilmedi';
        } else if (!$projectNaceCode || $projectNaceCode == -1) {
            $result['sonuc'] = 'Proje nace kodu seçilmedi';
        } else if (!$projectContent) {
            $result['sonuc'] = 'Proje içeriği girilmedi';
        } else if (!$startDate) {
            $result['sonuc'] = 'Proje başlangıç tarihi girilmedi';
        } else if (!$categories || $categories == -1) {
            $result['sonuc'] = 'Proje kategorisi girilmedi';
        } else if (!$deadlineInput) {
            $result['sonuc'] = 'Proje süresi girilmedi';
        } else if (!$projectGoal) {
            $result['sonuc'] = 'Proje hedefi girilmedi';
        } else if (!$projectSectors || $projectSectors == -1) {
            $result['sonuc']  = 'Proje sektör bilgisi seçilmedi';
        } else {


            $sorgu = $db->prepare('INSERT INTO applies SET name=?, 
            goal=?, content=?, projectCode=?,  requestedAmount=?, Users_idUser=?, Users_Roles_idRole=?, 
            NaceCodes_idNace=?, Sectors_idSectors=?, Categories_idCategories=?');

            $insertSonuc = $sorgu->execute([
                $projectName,
                $projectGoal,
                $projectContent,
                $projectCode,
                $projectTutar,
                $_GET['userId'],
                $allUsers['Roles_idRole'],
                $projectNaceCode,
                $projectSectors,
                $categories,
            ]);

            if ($insertSonuc) {

                $result['durum'] = true;
            } else {

                $result['durum'] = false;
            }

            if ($result['durum'] == true) {
                $sorgu2 = $db->prepare('INSERT INTO transitions SET fromSend=?, toSend=?, startDate=?, deadline=?, Applies_idApply=?, Applies_Users_idUser=?, Applies_Users_Roles_idRole=?');
                $allUsers = $db->query('SELECT * FROM users WHERE idUser=' . $_GET['userId'] . '')->fetch(PDO::FETCH_ASSOC);
                $categories  = $db->query('SELECT * FROM categories WHERE idCategories=' . $_POST['categories'])->fetch(PDO::FETCH_ASSOC);
                $naceCodes = $db->query('SELECT * FROM naceCodes WHERE idNace=' . $_POST['selectSm'])->fetch(PDO::FETCH_ASSOC);
                $sectors = $db->query('SELECT * FROM sectors WHERE idSectors=' . $_POST['sectors'])->fetch(PDO::FETCH_ASSOC);
                $roles = $db->query('SELECT * FROM roles')->fetchAll(PDO::FETCH_ASSOC);
                $allApplies  = $db->query("SELECT * FROM applies WHERE projectCode='" . $projectCode . "'")->fetch(PDO::FETCH_ASSOC);
                //print_r($allApplies);
                $roleInfo = $db->query('SELECT * FROM roles WHERE idRole=' . $allUsers['Roles_idRole'])->fetch(PDO::FETCH_ASSOC);
                $tto = $db->query('SELECT * FROM roles WHERE idRole=2')->fetch(PDO::FETCH_ASSOC);


                $transitionSonuc = $sorgu2->execute([
                    $roleInfo['description'],
                    $tto['description'],
                    $_POST['start-date-input'],
                    $_POST['deadline-input'],
                    isset($allApplies['idApply']) ? $allApplies['idApply'] : null,
                    $_GET['userId'],
                    $allUsers['Roles_idRole']
                ]);

                if ($transitionSonuc) {
                    $allStatus = $db->query('SELECT * FROM status WHERE name="New"')->fetch(PDO::FETCH_ASSOC);
                    $statuTransection = $db->prepare('INSERT INTO statutransections SET currentRole=?, nextRole=?, Applies_idApply=?,Status_idStatus=?');
                    $transectionSonuc = $statuTransection->execute([
                        $roleInfo['description'],
                        $tto['description'],
                        isset($allApplies['idApply']) ? $allApplies['idApply'] : null,
                        $allStatus['idStatus']
                    ]);
                    if ($transectionSonuc) {
                        echo "<script>alert('Proje kaydı başarılı.');document.location='form.php?userId=" . $_GET['userId'] . "'</script>";
                    } else {
                        $hata = $statuTransection->errorInfo();
                        echo $hata[2];
                        exit;
                    }
                } else {
                    $hata = $sorgu2->errorInfo();
                    echo $hata[2];
                    exit;
                    //echo "<script>alert('Proje kaydı eklenmesi başarılı olmadı. " . $hata[2] . "');document.location='form.php?userId=" . $_GET['userId'] . "'</script>";
                    //echo 'transition hata';
                    //exit;
                }
            } else if ($result['durum'] == false) {
                $hata = $sorgu->errorInfo();
                echo $hata[2];
                exit;
            }
            //<?php echo isset($siteDirect) ? $siteDirect : 'preview.php'
            //echo isset($siteDirect) ? $siteDirect : 'null';
        }


        /*if (isset($result['sonuc'])) {
            echo "<script>alert('" . $result['sonuc'] . "');document.location='form.php?userId=" . $_GET['userId'] . "'</script>";
        }*/
    }
}



?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">

    <!-- Title Page-->
    <title>Yeni Başvuru</title>

    <!-- Fontfaces CSS-->
    <link href="css/font-face.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
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
    <script src="https://code.jquery.com/jquery.datepicker2.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery.min.js"></script>



    </script>
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

        #SelectLm option {
            font-size: 12px;
        }

        .main-content {
            margin-top: -20px;
            width: max-content;

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
                            <img src="images/icon/profile.png" alt="John Doe" />
                            <div class="centered"> <?php echo ($loginUsers['name']) ? strtoupper(substr($loginUsers['name'], 0, 1))  : '' ?></div>
                        </div>
                    </div>
                    <h4 class="name"><?php echo $loginUsers['name'] . ' ' . $loginUsers['surname'] ?></h4>
                    <h4><?php echo $userRoles['description'] ?></h4>
                    <br>
                    <a href="../index.php">Çıkış</a>
                </div>
                <nav class="navbar-sidebar2">
                    <ul class="list-unstyled navbar__list">
                        <li class="active has-sub">
                            <a class="js-arrow" href="index.php?userId=<?php echo $_GET['userId'] ?>">
                                <i class="fas fa-home"></i>Ana Sayfa
                            </a>

                        </li>

                        <li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-trophy"></i>Proje
                                <span class="arrow">
                                    <i class="fas fa-angle-down"></i>
                                </span>
                            </a>
                            <ul class="list-unstyled navbar__sub-list js-sub-list">
                                <li>
                                    <a href="table.php?userId=<?php echo $_GET['userId'] ?>">
                                        <i class="fas fa-table"></i>Başvurduklarım</a>
                                </li>
                                <li>
                                    <a href="form.php?userId=<?php echo $_GET['userId'] ?>">
                                        <i class="far fa-check-square"></i>Yeni Başvuru</a>
                                </li>

                            </ul>
                        </li>


                    </ul>
                </nav>
            </div>
        </aside>
        <!-- END MENU SIDEBAR-->
        <aside class="menu-sidebar2 js-right-sidebar d-block d-lg-none">
            <div class="logo">
                <a href="#">
                    <img src="images/icon/logo-white.png" alt="Cool Admin" />
                </a>
            </div>
            <div class="menu-sidebar2__content js-scrollbar1">
                <div class="account2">
                    <div class="image img-cir img-120">
                        <div class="container-image-add">
                            <img src="images/icon/profile.png" alt="John Doe" />
                            <div class="centered"> <?php echo ($loginUsers['name']) ? strtoupper(substr($loginUsers['name'], 0, 1))  : '' ?></div>
                        </div>
                    </div>
                    <h4 class="name"><?php echo $loginUsers['name'] . ' ' . $loginUsers['surname'] ?></h4>
                    <h4><?php echo $userRoles['description'] ?></h4>
                    <br>
                    <a href="../index.php">Çıkış</a>
                </div>
                <nav class="navbar-sidebar2">
                    <ul class="list-unstyled navbar__list">
                        <li class="active has-sub">
                            <a class="js-arrow" href="view/index.php?userId=<?php echo $_GET['userId'] ?>">
                                <i class="fas fa-home"></i>Ana Sayfa
                            </a>

                        </li>

                        <li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-trophy"></i>Proje
                                <span class="arrow">
                                    <i class="fas fa-angle-down"></i>
                                </span>
                            </a>
                            <ul class="list-unstyled navbar__sub-list js-sub-list">
                                <li>
                                    <a href="table.php?userId=<?php echo $_GET['userId'] ?>">
                                        <i class="fas fa-table"></i>Başvurduklarım</a>
                                </li>
                                <li>
                                    <a href="form.php?userId=<?php echo $_GET['userId'] ?>">
                                        <i class="far fa-check-square"></i>Yeni Başvuru</a>
                                </li>

                            </ul>
                        </li>


                    </ul>
                </nav>
            </div>
        </aside>
        <!-- PAGE CONTAINER-->
        <div class="page-container2">
            <!-- HEADER DESKTOP-->
            <header class="header-desktop2">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap2">
                            <div class="logo d-block d-lg-none">
                                <a href="#">
                                    <img src="images/icon/logo-white.png" alt="FSMVU" />
                                </a>
                            </div>
                            <div class="header-button2">
                                <div class="header-button-item js-item-menu">
                                    <i class="zmdi zmdi-search"></i>
                                    <div class="search-dropdown js-dropdown">
                                        <form action="">
                                            <input class="au-input au-input--full au-input--h65" type="text" placeholder="Search for datas &amp; reports..." />
                                            <span class="search-dropdown__icon">
                                                <i class="zmdi zmdi-search"></i>
                                            </span>
                                        </form>
                                    </div>
                                </div>

                                <div class="header-button-item mr-0 js-sidebar-btn">
                                    <i class="zmdi zmdi-menu"></i>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="card">
                                    <div class="card-header">
                                        <strong>Proje</strong> Bilgileri
                                    </div>
                                    <div class="card-body card-block">

                                        <form action="" id="data-form" method="post" enctype="multipart/form-data" class="form-horizontal" accept-charset="UTF-8">

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label class=" form-control-label">Başvuran</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <p class="form-control-static"><?php echo $allUsers['name'] . ' ' . $allUsers['surname'] ?></p>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="email-input" class="form-control-label">Proje Kodu</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input type="text" id="email-input" name="project-code-input" placeholder="Proje Kodu" class="form-control">
                                                    <small class="help-block form-text">Kısa bir kod belirleyiniz</small>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="text-input" class="form-control-label">Proje Adı</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input value="<?php echo isset($_GET['project-name-input']) ? $_GET['project-name-input'] : null ?>" type="text" id="text-input" name="project-name-input" placeholder="Proje Adı" class="form-control">
                                                    <small class="form-text text-muted">En az 3 karakter olmalı</small>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="text-input" class="form-control-label">Proje Hedefi</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input value="<?php echo isset($_GET['project-goal']) ? $_GET['project-goal'] : null ?>" type="text" id="text-input" name="project-goal" placeholder="Proje Hedefi" class="form-control">
                                                    <small class="form-text text-muted">Hedefinizden kısaca bahsediniz</small>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="password-input" class="form-control-label">Talep Edilen Destek Tutarı</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input type="text" id="password-input" name="talep-tutar-input" placeholder="Tutar" class="form-control">

                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="password-input" class=" orm-control-label">Verilen Destek Tutarı</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input type="text" id="password-input" name="destek-tutar-input" placeholder="0 ₺" class="form-control" disabled>

                                                </div>
                                            </div>

                                            <div st class="row form-group">
                                                <div class="col col-md-3">
                                                    <label class="form-control-label">Nace Kodları</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <select name="selectSm" id="SelectLm" class="form-control-sm form-control">
                                                        <option value="-1">--Nace Kodu Giriniz--</option>
                                                        <?php foreach ($naceCodes as $nace) : ?>
                                                            <option value="<?php echo $nace['idNace'] ?>"><?php echo substr_replace(substr_replace($nace['code'], '.', -2, 0), '.', -5, 0) . ' - ' . $nace['description'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="select" class=" form-control-label">Kategoriler</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <select name="categories" id="select" class="form-control">
                                                        <option value="-1">--Kategori Seçiniz--</option>
                                                        <?php foreach ($categories as $category) : ?>
                                                            <option value="<?php echo $category['idCategories'] ?>"><?php echo $category['title'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="select" class=" form-control-label">Sektörler</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <select name="sectors" id="select" class="form-control">
                                                        <option value="-1">--Sektör Seçiniz--</option>
                                                        <?php foreach ($sectors as $sector) : ?>
                                                            <option value="<?php echo $sector['idSectors'] ?>"><?php echo $sector['name'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="textarea-input" class="form-control-label">Proje İçeriği</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <textarea name="proje-icerik-input" id="textarea-input" rows="9" placeholder="Proje Detaylarını Giriniz" class="form-control"></textarea>
                                                    <small class="help-block form-text">1000 karakter giriniz</small>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="start-date-input" name="startDate" class="form-control-label">Başlangıç Tarihi</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input type="text" id="start-date-input" name="start-date-input" placeholder="Tarihi Yazınız" class="form-control">
                                                    <small class="help-block form-text">Planlanan Başlangıç Tarihi</small>
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="deadline-input" class="form-control-label">Proje Süresi(Ay)</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input type="text" id="deadline-input" name="deadline-input" placeholder="Süre" class="form-control">
                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="password-input" class="form-control-label">Bitiş Tarihi Tarihi</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input type="text" id="end-date-input" name="end-date-input" placeholder="-" class="form-control" disabled>

                                                </div>
                                            </div>

                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="file-input" class=" form-control-label">Ekler</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input type="file" id="file-input" name="file-input" class="form-control-file">
                                                </div>
                                            </div>
                                            <div class="row form-group">
                                                <div class="col col-md-3">
                                                    <label for="file-multiple-input" class=" form-control-label">Diğer Belgeler</label>
                                                </div>
                                                <div class="col-12 col-md-9">
                                                    <input type="file" id="file-multiple-input" name="file-multiple-input" multiple="" class="form-control-file">
                                                </div>
                                            </div>

                                            <input type="hidden" name="submit" value="1">
                                            <button type="submit" class="btn btn-primary btn-sm" style="float: right;">
                                                <i class="fa fa-floppy-o"></i> Kaydet
                                            </button>

                                        </form>

                                    </div>

                                </div>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="copyright">
                                    <p>© 2021 - Developed by Emre USTA - <a href="https://www.fsm.edu.tr">FSMVÜ</a>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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

        <!-- Main JS-->
        <script src="js/main.js"></script>

</body>

</html>
<!-- end document-->