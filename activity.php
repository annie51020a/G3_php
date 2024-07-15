<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");

try {
    require_once("./connect_cid101g3.php");
    
    $returnData = [
        'code' => 200,
        'msg' => '',
        'data' => []
    ];

    // 獲取請求數據
    $input = json_decode(file_get_contents('php://input'), true);
    $act_id = isset($input['act_id']) ? $input['act_id'] : null;

    if ($act_id) {
        // 查詢特定活動
        $sql = "SELECT * FROM activity WHERE act_id = :act_id";
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':act_id', $act_id, PDO::PARAM_INT);
        $statement->execute();
        
        $activityData = $statement->fetch(PDO::FETCH_ASSOC);
        
        if ($activityData) {
            $returnData['data'] = $activityData;
            $returnData['msg'] = 'Success';
        } else {
            $returnData['code'] = 10002;
            $returnData['msg'] = "Activity not found";
        }
    } else {
        // 查詢所有活動
        $sql = "SELECT * FROM activity";
        $statement = $pdo->prepare($sql);
        $statement->execute();
        
        $activityData = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        $returnData['data']['list'] = $activityData;
        $returnData['msg'] = 'Success';
    }

} catch (PDOException $e) {
    $returnData['code'] = 10003;
    $returnData['msg'] = "Database error: " . $e->getMessage();
} catch (Exception $e) {
    $returnData['code'] = 10004;
    $returnData['msg'] = "General error: " . $e->getMessage();
}

echo json_encode($returnData);
?>
