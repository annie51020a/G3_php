<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

try {
    require_once("./connect_cid101g3.php");
    
    $returnData = [
        'code' => 200,
        'msg' => '',
        'data' => null
    ];

    // 檢查是否有 emp_id 參數
    if (isset($_GET['act_id'])) {
        $act_id = $_GET['act_id'];
        
        // 執行 SQL，只獲取特定 act_id 的數據
        $sql = "SELECT * FROM activity WHERE act_id = :act_id";
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':act_id', $act_id, PDO::PARAM_INT);
        $statement->execute();
        
        // 獲取結果
        $actData = $statement->fetch(PDO::FETCH_ASSOC);
        
        if ($actData) {
            $returnData['data'] = $actData;
            $returnData['msg'] = 'Success';
        } else {
            $returnData['code'] = 404;
            $returnData['msg'] = 'prod not found';
        }
    } else {
        $returnData['code'] = 400;
        $returnData['msg'] = 'Missing prod_id parameter';
    }

} catch (PDOException $e) {
    $returnData['code'] = 500;
    $returnData['msg'] = "Database error: " . $e->getMessage();
} catch (Exception $e) {
    $returnData['code'] = 500;
    $returnData['msg'] = "General error: " . $e->getMessage();
}

echo json_encode($returnData);
?>