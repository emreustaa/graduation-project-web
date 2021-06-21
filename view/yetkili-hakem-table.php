<?php

require_once '../database/config.php';

session_start();
ob_start();
setlocale(LC_ALL, 'tr_TR', 'Turkish');
date_default_timezone_set('Europe/Istanbul');

if (!isset($_SESSION['login'])) {
    header('Location:../empty.php');
} elseif (isset($_SESSION['time']) && time() > $_SESSION['time']) {
    session_destroy();
    header('Location:../session-ended.php');
}
if (isset($_GET['userId'])) {
    $loginUsers = $db->query('SELECT * FROM users WHERE idUser=' . $_GET['userId'] . '')->fetch(PDO::FETCH_ASSOC);
    $userRoles = $db->query("SELECT * FROM roles WHERE idRole=" . $loginUsers['Roles_idRole'])->fetch(PDO::FETCH_ASSOC);
    $userApplies = $db->query('SELECT * FROM applies INNER JOIN statutransections ON applies.idApply = statutransections.Applies_idApply WHERE (Status_idStatus!=4 AND Status_idStatus!=8 AND Status_idStatus!=7 AND Status_idStatus!=1)')->fetchAll(PDO::FETCH_ASSOC);
    //$statuTransections = $db->query('SELECT * FROM statuTransecitons WHERE Applies_idApply='.$_GET[''])

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="au theme template">
    <meta name="author" content="Hau Nguyen">
    <meta name="keywords" content="au theme template">
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <!-- Title Page-->
    <title>Başvurulan Projeler</title>

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
    <link rel="shortcut icon" href="/bitirme/assets/images/tto.png">
    <!-- Main CSS-->
    <link href="css/theme.css" rel="stylesheet" media="all">
    <link href="css/pop-up.css" rel="stylesheet" media="all">

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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(function() {
            $(".item").on('click', function(e) {
                var buttonValue = $(this).attr("value")

                var table = document.getElementsByTagName("table")[0];
                var tbody = table.getElementsByTagName("tbody")[0];
                tbody.onclick = function(e) {
                    e = e || window.event;
                    var data = [];
                    var target = e.srcElement || e.target;
                    while (target && target.nodeName !== "TR") {
                        target = target.parentNode;
                    }
                    if (target) {
                        var cells = target.getElementsByTagName("td");
                        for (var i = 0; i < cells.length; i++) {
                            data.push(cells[i].innerHTML);
                        }
                    }
                    if (buttonValue == "Onayla ve TTO Yetkilisine İlet") {
                        $.post("referee-confirm-project.php", {
                            data: data[0]
                        }).done(function(receiveData) {
                            alert(receiveData);
                            window.location.reload();
                        })
                    } else if (buttonValue == "Düzenleme Talep Et") {
                        $.post("referee-edit-request.php", {
                            data: data[0]
                        }).done(function(receiveData) {
                            alert(receiveData);
                            window.location.reload();
                        })

                    } else if (buttonValue == "Reddet") {
                        $.post("referee-reject-project.php", {
                            data: data[0]
                        }).done(function(receiveData) {
                            alert(receiveData);
                            window.location.reload();
                        })
                    } else if (buttonValue == "Görüntüle") {
                        //window.location.replace()
                        window.location.href = "referee-show-project.php?userId=<?php echo $_GET['userId'] ?>&projectCode=" + data[0]
                    }

                };


            });
        })
    </script>
</head>

<body class="animsition">

    <div class="page-wrapper">

        <!-- MENU SIDEBAR-->
        <aside class="menu-sidebar2">
            <div class="logo">
                <a href="../index.php">
                    <img src="images/icon/logo-white.png" alt="FSMVÜ" />
                </a>
            </div>
            <div class="menu-sidebar2__content js-scrollbar1">
                <div class="account2">
                    <div class="image img-cir img-120">
                        <div class="container-image-add">
                            <img src="images/icon/profile.png" alt="Profile" />
                            <div class="centered"> <?php echo ($loginUsers['name']) ? strtoupper(substr($loginUsers['name'], 0, 1))  : '' ?></div>
                        </div>
                    </div>

                    <h4 class="name"><?php echo $loginUsers['name']  . ' ' .  $loginUsers['surname'] ?></h4>
                    <h4><?php echo $userRoles['description'] ?></h4>
                    <br>
                    <a href="../index.php">Çıkış</a>
                </div>
                <nav class="navbar-sidebar2">
                    <ul class="list-unstyled navbar__list">
                        <li class="active has-sub">
                            <a href="tto-yetkili.php?userId=<?php echo $_GET['userId'] ?>">
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
                                    <a href="yetkili-hakem-table.php?userId=<?php echo $_GET['userId'] ?>">
                                        <i class="fas fa-clipboard-list"></i>Tüm Projeler</a>
                                </li>

                                <li>
                                    <a href="tto-system-projects.php?userId=<?php echo $_GET['userId'] ?>">
                                        <i class="fas fa-tasks"></i>Devam Eden Projeler</a>
                                </li>

                                <li>
                                    <a href="tto-all-confirms.php?userId=<?php echo $_GET['userId'] ?>">
                                        <i class="fas fa-check"></i>Onaylananlar</a>
                                </li>
                                <li>
                                    <a href="tto-new-project.php?userId=<?php echo $_GET['userId'] ?>">
                                        <i class="fas fa-bell"></i>Yeni Başvurular</a>
                                </li>

                                <li>
                                    <a href="tto-editable-project.php?userId=<?php echo $_GET['userId'] ?>">
                                        <i class="far fa-edit"></i>Düzenleme Sürecindekiler</a>
                                </li>

                            </ul>
                        </li>
                               </ul>
                </nav>
            </div>
        </aside>
        <!-- END MENU SIDEBAR-->

        <!-- PAGE CONTAINER-->
        <div class="page-container">
            <!-- HEADER DESKTOP-->
            <header class="header-desktop2">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="header-wrap2">
                            <div class="logo d-block d-lg-none">
                                <a href="#">
                                    <img src="images/icon/logo-white.png" alt="FSMVÜ" />
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            <!-- END HEADER DESKTOP-->

            <!-- MAIN CONTENT-->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">

                        <div class="row">
                            <div class="col-md-12">
                                <!-- DATA TABLE -->
                                <h3 class="title-5 m-b-35">Tüm Projeler</h3>

                                <div class="table-responsive table-responsive-data2">

                                    <table class="table table-data2" id="data-table">
                                        <thead>
                                            <tr>
                                                <th>PROJE KODU</th>
                                                <th>Proje Adı</th>
                                                <th>PROJE HEDEFİ</th>
                                                <th>BAŞVURU TARİHİ</th>
                                                <th>BAŞVURU DURUMU</th>
                                                <th>SEKTÖR</th>

                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody class="tbody">
                                            <?php foreach ($userApplies as $apply) : ?>
                                                <td ><?php echo $apply['projectCode'] ?></td>
                                                <td>
                                                    <span><?php echo $apply['name'] ?></span>
                                                </td>
                                                <td><?php echo $apply['goal'] ?></td>
                                                <td><?php echo strftime("%e %B %Y", strtotime($apply['date'])) ?></td>
                                                <td>
                                                    <?php

                                                    $projectTransection  = $db->query("SELECT * FROM statutransections WHERE Applies_idApply=" . $apply['idApply'])->fetch(PDO::FETCH_ASSOC);

                                                    $projectStatu = $db->query("SELECT * FROM status WHERE idStatus=" . $projectTransection['Status_idStatus'])->fetch(PDO::FETCH_ASSOC);

                                                    if ($projectStatu['name']  == "Reject") {
                                                        $statuName = "Reddedildi";
                                                        $className = "status--denied";
                                                    } else if ($projectStatu['name']  == "Update") {
                                                        $statuName = "Düzenleme Talebi";
                                                        $className = "status--waiting";
                                                    } else if ($projectStatu['name']  == "Confirm") {
                                                        $statuName = "Onaylandı";
                                                        $className = "status--process";
                                                    } else if ($projectStatu['name']  == "Review") {
                                                        $statuName = "Yeni Başvuru";
                                                        $className = "status--looking";
                                                    } else if ($projectStatu['name']  == "Entries") {
                                                        $statuName = "Proje Devam Etmektedir";
                                                        $className = "status--entry";
                                                    } else if ($projectStatu['name']  == "RefereeConfirm") {
                                                        $statuName = "Yetkili Hakem Tarafından Onaylandı";
                                                        $className = "status--looking";
                                                    }


                                                    ?>
                                                    <span class="<?php echo $className ?>"><?php echo $statuName ?></span>
                                                    <!-- 
                                                          <td>
                                                              <span class="status--denied">Denied</span>
                                                          </td>
                                                    -->
                                                </td>
                                                <td>
                                                    <?php
                                                    $applySector = $db->query("SELECT * FROM sectors WHERE idSectors=" . $apply['Sectors_idSectors'])->fetch(PDO::FETCH_ASSOC);

                                                    echo $applySector['name'];
                                                    ?></td>
                                                <td>

                                                    <div class="table-data-feature">
                                                        <?php
                                                        if ($projectStatu['name']  == "Reject") {

                                                            $disabled['confirmStatu'] = "FALSE";
                                                            $disabled['rejectStatu'] = "FALSE";
                                                            $disabled['updateStatu'] = "FALSE";
                                                            $disabled['showStatu'] = "TRUE";
                                                        } else if ($projectStatu['name']  == "Update") {

                                                            $disabled['confirmStatu'] = "FALSE";
                                                            $disabled['rejectStatu'] = "FALSE";
                                                            $disabled['updateStatu'] = "FALSE";
                                                            $disabled['showStatu'] = "TRUE";
                                                        } else if ($projectStatu['name']  == "Confirm") {

                                                            $disabled['confirmStatu'] = "FALSE";
                                                            $disabled['rejectStatu'] = "FALSE"; // TTO YETKİLİSİ İLE İLGİLİ OLAN
                                                            $disabled['updateStatu'] = "FALSE"; // UPDATE -> HAKEMLE İLGİLİ OLAN
                                                            $disabled['showStatu'] = "TRUE";
                                                        } else if ($projectStatu['name']  == "Review") {

                                                            $disabled['confirmStatu'] = "TRUE";
                                                            $disabled['rejectStatu'] = "TRUE";
                                                            $disabled['updateStatu'] = "TRUE";
                                                            $disabled['showStatu'] = "TRUE";
                                                        } else if ($projectStatu['name']  == "Entries") {

                                                            $disabled['confirmStatu'] = "FALSE";
                                                            $disabled['rejectStatu'] = "FALSE";
                                                            $disabled['updateStatu'] = "FALSE";
                                                            $disabled['showStatu'] = "TRUE";
                                                        } else if ($projectStatu['name']  == "RefereeConfirm") {

                                                            $disabled['confirmStatu'] = "FALSE";
                                                            $disabled['rejectStatu'] = "FALSE";
                                                            $disabled['updateStatu'] = "FALSE";
                                                            $disabled['showStatu'] = "TRUE";
                                                        }


                                                        ?>

                                                        <button style="visibility: <?php echo $disabled['confirmStatu'] == "TRUE"  ? "visible" : "collapse" ?>; display: <?php echo $disabled['confirmStatu'] == "TRUE"  ? "visible" : "none" ?>;" class="item" data-toggle="tooltip" id="confirm-entrepereneur-button" name="confirm-entrepereneur-button" data-placement="top" title="Onayla ve TTO Yetkilisine İlet" value="Onayla ve TTO Yetkilisine İlet">
                                                            <i class="zmdi zmdi-check"></i>
                                                        </button>

                                                        <button style="visibility: <?php echo $disabled['showStatu'] == "TRUE"  ? "visible" : "hidden" ?>; display: <?php echo $disabled['showStatu'] == "TRUE"  ? "visible" : "none" ?>;" class="item" data-toggle="tooltip" id="show-button" name="show-button" data-placement="top" title="Görüntüle" value="Görüntüle">
                                                            <i class="zmdi zmdi-eye"></i>
                                                        </button>

                                                        <button style="visibility: <?php echo $disabled['updateStatu'] == "TRUE"  ? "visible" : "hidden" ?>; display: <?php echo $disabled['updateStatu'] == "TRUE"  ? "visible" : "none" ?>;" class="item" data-toggle="tooltip" data-placement="top" id="edit-button" name="edit-button" title="Düzenleme Talep Et" value="Düzenleme Talep Et">
                                                            <i class="zmdi zmdi-edit"></i>
                                                        </button>

                                                        <button style="visibility: <?php echo $disabled['rejectStatu'] == "TRUE"  ? "visible" : "hidden" ?>; display: <?php echo $disabled['rejectStatu'] == "TRUE"  ? "visible" : "none" ?>;" class="item" data-toggle="tooltip" id="update-button" name="update-button" data-placement="top" title="Reddet" value="Reddet">
                                                            <i class="zmdi zmdi-close-circle"></i>
                                                        </button>

                                                    </div>

                                                </td>
                                                <tr class="spacer"></tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
                                    <script src="js/lisenme.js"></script>
                                    <script>
                                        jQuery('#data-table').ddTableFilter();
                                    </script>
                                </div>
                                <!-- END DATA TABLE -->
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