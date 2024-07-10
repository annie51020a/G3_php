<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

try {
    require_once("./connect_cid101g3.php");
    
    $returnData = [
        'code' => 200,
        'msg' => '',
        'data' => []
    ];

    
    // 執行第一個 SQL 查詢
    $sql1 = "SELECT tpl_img FROM template";
    $template = $pdo->prepare($sql1);
    $template->execute();
    $templateData = $template->fetchAll(PDO::FETCH_ASSOC);

    // 執行第二個 SQL 查詢
    $sql2 = "SELECT icon_img FROM icon";
    $icon = $pdo->prepare($sql2);
    $icon->execute();
    $iconData = $icon->fetchAll(PDO::FETCH_ASSOC);

    // 合併兩個結果
    $returnData['data']['template'] = $templateData;
    $returnData['data']['icon'] = $iconData;
    $returnData['msg'] = 'Success';
    

} catch (PDOException $e) {
    $returnData['code'] = 10003;
    $returnData['msg'] = "Database error: " . $e->getMessage();
} catch (Exception $e) {
    $returnData['code'] = 10004;
    $returnData['msg'] = "General error: " . $e->getMessage();
}

echo json_encode($returnData);
?>
