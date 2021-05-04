<?php

require_once '../database/config.php';

if (isset($_POST['data'])) {

    $data = $_POST['data'] ?? false;

    if (!$data) {
        $sonuc['hata'] = "DATA BOŞ!";
    } else {
        $sonuc['deger'] = $_POST['data'];
    }
} else {
    $sonuc['hata'] = "data yok";
}
echo json_encode($sonuc, JSON_UNESCAPED_UNICODE);
