<?php

require_once '../database/config.php';

if (isset($_POST['data'])) {

    $data = htmlspecialchars($_POST['data']) ?? false;

    if (!$data) {
        $sonuc['hata'] = "DATA BOŞ!";
    } else {

        $selectedApply = $db->query("SELECT * FROM patents WHERE patentCode='$data'")->fetch(PDO::FETCH_ASSOC);
        $patentId = $selectedApply['idPatent'];
        $transectionData = ['newIdStatus' => '4', 'currentRole' => "TTO Yetkilisi", 'nextRole' => 'Yetkili Hakem'];
        $updateTransition = $db->prepare("UPDATE patentstatus SET Status_idStatus=:newIdStatus,currentRole=:currentRole,nextRole=:nextRole WHERE Patents_idPatents='$patentId'")->execute($transectionData);

        if ($updateTransition) {
            
            $actionQuery = $db->prepare('INSERT INTO actions SET name=?,projectCode=?,User_idUser=?,Roles_idRole=?');
            $actionSonuc = $actionQuery->execute([
                "Patent başvurusu TTO Yetkilisine gönderildi.",
                $data,
                $selectedApply['Users_idUser'],
                0,
            ]);
            if ($actionSonuc) {
                $sonuc['deger'] = "Patent başvurusu TTO Yetkilisine başarı ile gönderildi.";
            } else {
                $hata = $actionQuery->errorInfo();
                echo $hata[2];
                exit;
            }
            
            $sonuc['deger'] = "Patent başvurusu TTO Yetkilisine başarı ile gönderildi.";
        } else {
            $sonuc['deger'] = "Projenin gönderimi esnasında bir hata ile karşılaşıldı.";
        }
    }
} else {
    $sonuc['hata'] = "data yok";
}
echo json_encode($sonuc, JSON_UNESCAPED_UNICODE);
