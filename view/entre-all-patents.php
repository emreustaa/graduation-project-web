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
    $userPatents = $db->query('SELECT * FROM patents WHERE Users_idUser=' . $_GET['userId'])->fetchAll(PDO::FETCH_ASSOC);
    $userRoles = $db->query("SELECT * FROM roles WHERE idRole=" . $loginUsers['Roles_idRole'])->fetch(PDO::FETCH_ASSOC);
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
    <title>Patent Başvurularım</title>

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
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.5.0/jquery.min.js"></script>


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
                    if (buttonValue == "Hakeme Gönder") {
                        $.post("entre-patent-send-to-tto.php", {
                            data: data[0]
                        }).done(function(receiveData) {
                            alert(receiveData)
                            window.location.reload();
                        })
                    } else if (buttonValue == "Görüntüle") {
                        window.location.href = "entre-show-patent.php?userId=<?php echo $_GET['userId'] ?>&patentCode=" + data[0]

                    } else if (buttonValue == "Projeyi Başlat") {

                        $.post("entre-start-patent.php", {
                            data: data[0]
                        }).done(function(receiveData) {
                            alert(receiveData)
                            window.location.reload();
                        })
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
                                    <a href="form.php?userId=<?php echo $_GET['userId'] ?>">
                                        <i class="fas fa-paper-plane"></i>Yeni Başvuru</a>
                                </li>
                                <li>
                                    <a href="table.php?userId=<?php echo $_GET['userId'] ?>">
                                        <i class="fas fa-clipboard-list"></i>Başvurduklarım</a>
                                </li>

                                <li>
                                    <a href="entre-continue-projects.php?userId=<?php echo $_GET['userId'] ?>">
                                        <i class="fas fa-tasks"></i>Devam Eden Projeler</a>
                                </li>

                                <li>
                                    <a href="entre-confirms-projects.php?userId=<?php echo $_GET['userId'] ?>">
                                        <i class="fas fa-check"></i>Onaylananlar</a>
                                </li>
                                <li>
                                    <a href="entre-confirm-request-project.php?userId=<?php echo $_GET['userId'] ?>">
                                        <i class="fas fa-bell"></i>Onay Bekleyenler</a>
                                </li>

                                <li>
                                    <a href="entre-editable-project.php?userId=<?php echo $_GET['userId'] ?>">
                                        <i class="far fa-edit"></i>Düzenleme Sürecindekiler</a>
                                </li>

                                <li>
                                    <a href="entre-saved-project.php?userId=<?php echo $_GET['userId'] ?>">
                                        <i class="far fa-save"></i>Taslak Olarak Kaydedilenler</a>
                                </li>

                            </ul>
                        </li>
                        <li class="has-sub">
                            <a class="js-arrow" href="#">
                                <i class="fas fa-thumbtack"></i>Patent
                                <span class="arrow">
                                    <i class="fas fa-arrow-right"></i>
                                </span>
                            </a>

                            <ul class="list-unstyled navbar__sub-list js-sub-list">
                                <li>
                                    <a href="entre-patent-form.php?userId=<?php echo $_GET['userId'] ?>">
                                        <i class="fas fa-paper-plane"></i>Yeni Başvuru</a>
                                </li>
                            </ul>

                            <ul class="list-unstyled navbar__sub-list js-sub-list">
                                <li>
                                    <a href="entre-all-patents.php?userId=<?php echo $_GET['userId'] ?>">
                                        <i class="fas fa-clipboard-list"></i>Başvurularım</a>
                                </li>

                            </ul>


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
                                    <img src="images/icon/logo-white.png" alt="FSMVU" />
                                </a>
                            </div>

                        </div>
                    </div>
                </div>
            </header>

            <!-- MAIN CONTENT-->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-12">

                                <!-- DATA TABLE -->
                                <h3 class="title-5 m-b-35">Patent Başvurularım</h3>

                                <div class="table-responsive table-responsive-data2">
                                    <table id="dataTable" class="table table-data2">
                                        <thead>
                                            <tr>

                                                <th>Patent Kodu</th>
                                                <th>Patent Adı</th>
                                                <th>Patent Hedefi</th>
                                                <th>Başvuru Tarihi</th>
                                                <th>Başvuru Durumu </th>
                                                <th>Sektör</th>

                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php foreach ($userPatents as $apply) : ?>

                                                <td><?php echo $apply['patentCode'] ?></td>

                                                <td>
                                                    <span><?php echo $apply['name'] ?></span>
                                                </td>
                                                <td class="desc"><?php echo $apply['goal'] ?></td>
                                                <td><?php echo strftime("%e %B %Y", strtotime($apply['date'])) ?></td>
                                                <td>
                                                    <?php

                                                    $status  = $db->query("SELECT * FROM patentstatus WHERE Patents_idPatents=" . $apply['idPatent'])->fetch(PDO::FETCH_ASSOC);


                                                    $patentStatus = $db->query("SELECT * FROM status WHERE idStatus=" . $status['Status_idStatus'])->fetch(PDO::FETCH_ASSOC);


                                                    if ($patentStatus['name']  == "New") {
                                                        $statuName = "Onay Bekleniyor";
                                                        $className = "status--looking";
                                                    } else if ($patentStatus['name']  == "Reject") {
                                                        $statuName = "Reddedildi";
                                                        $className = "status--denied";
                                                    } else if ($patentStatus['name']  == "Confirm") {
                                                        $statuName = "Onaylandı";
                                                        $className = "status--process";
                                                    } else if ($patentStatus['name']  == "Review") {
                                                        $statuName = "Onay Bekleniyor";
                                                        $className = "status--looking";
                                                    } else if ($patentStatus['name']  == "Entries") {
                                                        $statuName = "Patent Süreci Devam Etmektedir";
                                                        $className = "status--entry";
                                                    } else if ($patentStatus['name']  == "Deleted") {
                                                        $statuName = "Başvuru Talebi Onaylanmadı";
                                                        $className = "status--deleted";
                                                    } else if ($patentStatus['name']  == "Saved") {
                                                        $statuName = "Taslak Olarak Kaydedildi";
                                                        $className = "status--saved";
                                                    }
                                                    ?>
                                                    <span class="<?php echo $className ?>"><?php echo $statuName ?></span>

                                                </td>
                                                <td>
                                                    <?php
                                                    $applySector = $db->query("SELECT * FROM sectors WHERE idSectors=" . $apply['Sectors_idSectors'])->fetch(PDO::FETCH_ASSOC);

                                                    echo $applySector['name'];
                                                    ?></td>
                                                <td>
                                                    <div class="table-data-feature">
                                                        <?php if ($patentStatus['name']  == "New") {
                                                            $disabled['sendStatu'] = "FALSE";
                                                            $disabled['editSendStatu'] = "FALSE";
                                                            $disabled['editStatu'] = "FALSE";
                                                            $disabled['showStatu'] = "TRUE";
                                                            $disabled['startStatu'] = "FALSE";
                                                        } else if ($patentStatus['name']  == "Reject") {
                                                            $disabled['sendStatu'] = "FALSE";
                                                            $disabled['editSendStatu'] = "FALSE";
                                                            $disabled['editStatu'] = "FALSE";
                                                            $disabled['showStatu'] = "TRUE";
                                                            $disabled['startStatu'] = "FALSE";
                                                        } else if ($patentStatus['name']  == "Update") {
                                                            $disabled['sendStatu'] = "FALSE";
                                                            $disabled['editSendStatu'] = "TRUE";
                                                            $disabled['editStatu'] = "FALSE";
                                                            $disabled['showStatu'] = "FALSE";
                                                            $disabled['startStatu'] = "FALSE";
                                                        } else if ($patentStatus['name']  == "Confirm") {
                                                            $disabled['sendStatu'] = "FALSE";
                                                            $disabled['showStatu'] = "TRUE";
                                                            $disabled['startStatu'] = "TRUE";
                                                        } else if ($patentStatus['name']  == "Review") {
                                                            $disabled['sendStatu'] = "FALSE";
                                                            $disabled['showStatu'] = "TRUE";
                                                            $disabled['startStatu'] = "FALSE";
                                                        } else if ($patentStatus['name']  == "Entries") {
                                                            $disabled['sendStatu'] = "FALSE";
                                                            $disabled['showStatu'] = "TRUE";
                                                            $disabled['startStatu'] = "FALSE";
                                                        } else if ($patentStatus['name']  == "Deleted") {
                                                            $disabled['sendStatu'] = "FALSE";
                                                            $disabled['showStatu'] = "FALSE";
                                                            $disabled['startStatu'] = "FALSE";
                                                        } else if ($patentStatus['name']  == "Saved") {
                                                            $disabled['sendStatu'] = "TRUE";
                                                            $disabled['showStatu'] = "TRUE";
                                                            $disabled['startStatu'] = "FALSE";
                                                        } else if ($patentStatus['name']  == "RefereeConfirm") {
                                                            $disabled['sendStatu'] = "FALSE";
                                                            $disabled['showStatu'] = "TRUE";
                                                            $disabled['startStatu'] = "FALSE";
                                                        }

                                                        ?>
                                                        <button style="visibility: <?php echo $disabled['sendStatu'] == "TRUE"  ? "visible" : "hidden" ?>; display: <?php echo $disabled['sendStatu'] == "TRUE"  ? "visible" : "none" ?>;" class="item" data-toggle="tooltip" id="send-button" name="send-button" data-placement="top" title="Hakeme Gönder" value="Hakeme Gönder">
                                                            <i class="zmdi zmdi-account"></i>
                                                        </button>
                                                        <button style="visibility: <?php echo $disabled['showStatu'] == "TRUE"  ? "visible" : "hidden" ?>; display: <?php echo $disabled['showStatu'] == "TRUE"  ? "visible" : "none" ?>;" class="item" data-toggle="tooltip" id="show-button" name="show-button" data-placement="top" title="Görüntüle" value="Görüntüle">
                                                            <i class="zmdi zmdi-eye"></i>
                                                        </button>


                                                        <button style="visibility: <?php echo $disabled['startStatu'] == "TRUE"  ? "visible" : "hidden" ?>; display: <?php echo $disabled['startStatu'] == "TRUE"  ? "visible" : "none" ?>;" class="item" data-toggle="tooltip" data-placement="top" id="start-button" name="start-button" title="Projeyi Başlat" value="Projeyi Başlat">
                                                            <i class="zmdi zmdi-check"></i>
                                                        </button>
                                                </td>
                                                <tr class="spacer"></tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                    <script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
                                    <script src="js/lisenme.js"></script>
                                    <script>
                                        jQuery('#dataTable').ddTableFilter();
                                        /*const searchInput = document.getElementById('search');
                                        const rows = document.querySelectorAll('tbody tr');

                                        searchInput.addEventListener('keyup', function(event) {
                                            const q = event.target.value;
                                            rows.forEach(row => {
                                                row.querySelector('td').textContent.toLowerCase().startsWith(q) ? row.style.display = "" : row.style.display = 'none';
                                            });
                                        })*/
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