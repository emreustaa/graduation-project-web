<?php

require_once '../database/config.php';

if (isset($_POST['data'])) {

    $data = htmlspecialchars($_POST['data']) ?? false;

    if (!$data) {
        $sonuc['hata'] = "DATA BOŞ!";
    } else {

        $selectedApply = $db->query("SELECT * FROM patents WHERE patentCode='$data'")->fetch(PDO::FETCH_ASSOC);
        $applyId = $selectedApply['idPatent'];
        //$applyStatusTransection = $db->query("SELECT * FROM statutransections WHERE Applies_idApply='$applyId'")->fetch(PDO::FETCH_ASSOC);
        $transectionData = ['newIdStatus' => '1', 'currentRole' => "Girişimci", 'nextRole' => 'Girişimci'];
        $updateTransition = $db->prepare("UPDATE patentstatus SET Status_idStatus=:newIdStatus,currentRole=:currentRole,nextRole=:nextRole WHERE Patents_idPatents='$applyId'")->execute($transectionData);
        if ($updateTransition) {

            $actionQuery = $db->prepare('INSERT INTO actions SET name=?,projectCode=?,User_idUser=?,Roles_idRole=?');
            $actionSonuc = $actionQuery->execute([
                "Patent başvurusu onaylanarak girişimciye gönderildi.",
                $data,
                $selectedApply['Users_idUser'],
                2,
            ]);
            if ($actionSonuc) {
                $sonuc['deger'] = "Proje başarı ile onaylandı.";
            } else {
                $hata = $actionQuery->errorInfo();
                echo $hata[2];
                exit;
            }
        } else {
            $sonuc['deger'] = "Projenin gönderimi esnasında bir hata ile karşılaşıldı.";
        }
    }
} else {
    $sonuc['hata'] = "data yok";
}
echo json_encode($sonuc, JSON_UNESCAPED_UNICODE);
