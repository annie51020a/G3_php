<?php
header("Access-Control-Allow-Origin: https://tibamef2e.com");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    // Handle preflight requests
    http_response_code(204);
    exit(0);
}

try {
    require_once("./connect_cid101g3.php");
    
    $returnData = [
        'code' => 200,
        'msg' => '',
        'data' => []
    ];

    // 執行 SQL
    $sql = "SELECT * FROM member ORDER BY mem_id";
    $statement = $pdo->prepare($sql);
    $statement->execute();
    
    // 獲取所有結果
    $memData = $statement->fetchAll(PDO::FETCH_ASSOC);
    
    $returnData['data']['list'] = $memData;
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
