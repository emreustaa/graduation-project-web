<?php

require_once '../database/config.php';

if (isset($_POST['data'])) {

    $data = htmlspecialchars($_POST['data']) ?? false;

    if (!$data) {
        $sonuc['hata'] = "DATA BOŞ!";
    } else {

        $selectedApply = $db->query("SELECT * FROM applies WHERE projectCode='$data'")->fetch(PDO::FETCH_ASSOC);
        $applyId = $selectedApply['idApply'];
        $transectionData = ['newIdStatus' => '4', 'currentRole' => "TTO Yetkilisi", 'nextRole' => 'Yetkili Hakem'];
        $updateTransection = $db->prepare("UPDATE statutransections SET Status_idStatus=:newIdStatus,currentRole=:currentRole,nextRole=:nextRole WHERE Applies_idApply='$applyId'")->execute($transectionData);
        if ($updateTransection) {
            $transitionData = ['fromSend' => 'Girişimci', 'toSend' => 'TTO Yetkilisi'];
            $updateTransition = $db->prepare("UPDATE transitions SET fromSend=:fromSend, toSend=:toSend WHERE Applies_idApply='$applyId'")->execute($transitionData);
            if ($updateTransition) {
                $actionQuery = $db->prepare('INSERT INTO actions SET name=?,projectCode=?,User_idUser=?,Roles_idRole=?');
                $actionSonuc = $actionQuery->execute([
                    "TTO Yetkilisine Gönderildi",
                    $data,
                    $selectedApply['Users_idUser'],
                    0,
                ]);
                if ($actionSonuc) {
                    $sonuc['deger'] = "Proje TTO Yetkilisine başarı ile gönderildi.";
                } else {
                    $sonuc['deger'] = "Projenin gönderimi esnasında bir hata ile karşılaşıldı.";
                }
            } else {
                $sonuc['deger'] = "Projenin gönderimi esnasında bir hata ile karşılaşıldı.";
            }
        } else {
            $sonuc['deger'] = "Projenin gönderimi esnasında bir hata ile karşılaşıldı.";
        }
    }
} else {
    $sonuc['hata'] = "data yok";
}
echo json_encode($sonuc, JSON_UNESCAPED_UNICODE);
