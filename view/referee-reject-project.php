<?php

require_once '../database/config.php';

if (isset($_POST['data'])) {

    $data = htmlspecialchars($_POST['data']) ?? false;


    if (!$data) {
        $sonuc['hata'] = "DATA BOŞ!";
    } else {

        $selectedApply = $db->query("SELECT * FROM applies WHERE projectCode='$data'")->fetch(PDO::FETCH_ASSOC);
        $applyId = $selectedApply['idApply'];
        //$applyStatusTransection = $db->query("SELECT * FROM statutransections WHERE Applies_idApply='$applyId'")->fetch(PDO::FETCH_ASSOC);
        $transectionData = ['newIdStatus' => '2', 'currentRole' => "TTO Yetkilisi", 'nextRole' => 'Girişimci'];
        $updateTransection = $db->prepare("UPDATE statutransections SET Status_idStatus=:newIdStatus,currentRole=:currentRole,nextRole=:nextRole WHERE Applies_idApply='$applyId'")->execute($transectionData);
        if ($updateTransection) {
            $transitionData = ['fromSend' => 'Yetkili Hakem', 'toSend' => 'TTO Yetkilisi'];
            $updateTransition = $db->prepare("UPDATE transitions SET fromSend=:fromSend, toSend=:toSend WHERE Applies_idApply='$applyId'")->execute($transitionData);
            if ($updateTransition) {

                $actionQuery = $db->prepare('INSERT INTO actions SET name=?,projectCode=?,User_idUser=?,Roles_idRole=?');
                $actionSonuc = $actionQuery->execute([
                    "Başvuru reddedildi",
                    $data,
                    $selectedApply['Users_idUser'],
                    1,
                ]);
                if ($actionSonuc) {
                    $sonuc['deger'] = "Proje reddedilmiştir..";
                } else {
                    $hata = $actionQuery->errorInfo();
                    echo $hata[2];
                    exit;
                }
            } else {
                $sonuc['deger'] = "Onay sırasında bir hata ile karşılaşıldı";
            }
        } else {
            $sonuc['deger'] = "Onay sırasında bir hata ile karşılaşıldı";
        }
    }
} else {
    $sonuc['hata'] = "data yok";
}
echo json_encode($sonuc, JSON_UNESCAPED_UNICODE);
