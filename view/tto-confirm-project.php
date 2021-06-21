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
        $transectionData = ['newIdStatus' => '1', 'currentRole' => "Girişimci", 'nextRole' => 'Girişimci'];
        $updateTransection = $db->prepare("UPDATE statutransections SET Status_idStatus=:newIdStatus,currentRole=:currentRole,nextRole=:nextRole WHERE Applies_idApply='$applyId'")->execute($transectionData);
        if ($updateTransection) {
            $transitionData = ['fromSend' => 'TTO Yetkilisi', 'toSend' => 'Girişimci'];
            $updateTransition = $db->prepare("UPDATE transitions SET fromSend=:fromSend, toSend=:toSend WHERE Applies_idApply='$applyId'")->execute($transitionData);
            if ($updateTransition) {
                $confirmData = ['Applies_idApply' => $applyId, 'Applies_Users_idUser' => $selectedApply['Users_idUser'], 'Applies_Users_Roles_idRole' => $selectedApply['Users_Roles_idRole']];
                $confirmInsert = $db->prepare("INSERT INTO confirms (Applies_idApply,Applies_Users_idUser,Applies_Users_Roles_idRole) VALUES (:Applies_idApply,:Applies_Users_idUser,:Applies_Users_Roles_idRole)")->execute($confirmData);
                if ($confirmInsert) {
                    $actionQuery = $db->prepare('INSERT INTO actions SET name=?,projectCode=?,User_idUser=?,Roles_idRole=?');
                    $actionSonuc = $actionQuery->execute([
                        "Proje onaylandı ve girişimciye gönderildi",
                        $data,
                        $selectedApply['Users_idUser'],
                        2,
                    ]);
                    if ($actionSonuc) {
                        $sonuc['deger'] = "Proje onayı tamamlandı ve girişimciye gönderildi.";
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
        } else {
            $sonuc['deger'] = "Onay sırasında bir hata ile karşılaşıldı";
        }
    }
} else {
    $sonuc['hata'] = "data yok";
}
echo json_encode($sonuc, JSON_UNESCAPED_UNICODE);
