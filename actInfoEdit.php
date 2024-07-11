<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

try {
    require_once ("./connect_cid101g3.php");

    $returnData = [
        'code' => 200,
        'msg' => '',
        'data' => null
    ];

    $input = json_decode(file_get_contents('php://input'), true);

    // 輸出接收到的JSON數據以供調試
    error_log(print_r($input, true));

    $required_params = [
        'act_id', 'act_cate', 'act_name', 'act_price',
        'start_date', 'end_date', 'act_date',
        'mem_num', 'mem_limit', 'sess_time'
    ];

    $missing_params = [];
    foreach ($required_params as $param) {
        if (!isset($input[$param])) {
            $missing_params[] = $param;
        }
    }

    if (!empty($missing_params)) {
        $returnData['code'] = 400;
        $returnData['msg'] = '缺少必要的參數: ' . implode(', ', $missing_params);
    } else {
        $act_id = $input['act_id'];
        $act_cate = $input['act_cate'];
        $act_name = $input['act_name'];
        $act_price = $input['act_price'];
        $start_date = $input['start_date'];
        $end_date = $input['end_date'];
        $act_date = $input['act_date'];
        $mem_num = $input['mem_num'];
        $mem_limit = $input['mem_limit'];
        $sess_time = $input['sess_time'];

        // 更新数据库
        $updateSql = "UPDATE activity SET act_cate = :act_cate, act_name = :act_name, act_price = :act_price, start_date = :start_date, end_date = :end_date, act_date = :act_date, mem_num = :mem_num, mem_limit = :mem_limit, sess_time = :sess_time WHERE act_id = :act_id";
        $updateStatement = $pdo->prepare($updateSql);
        $updateStatement->bindParam(':act_cate', $act_cate);
        $updateStatement->bindParam(':act_name', $act_name);
        $updateStatement->bindParam(':act_price', $act_price);
        $updateStatement->bindParam(':act_id', $act_id);
        $updateStatement->bindParam(':start_date', $start_date);
        $updateStatement->bindParam(':end_date', $end_date);
        $updateStatement->bindParam(':act_date', $act_date);
        $updateStatement->bindParam(':mem_num', $mem_num);
        $updateStatement->bindParam(':mem_limit', $mem_limit);
        $updateStatement->bindParam(':sess_time', $sess_time);

        if ($updateStatement->execute()) {
            $returnData['msg'] = '更新成功';
        } else {
            $returnData['code'] = 500;
            $returnData['msg'] = '更新失敗';
        }
    }
} catch (PDOException $e) {
    $returnData['code'] = 500;
    $returnData['msg'] = "資料庫錯誤: " . $e->getMessage();
} catch (Exception $e) {
    $returnData['code'] = 500;
    $returnData['msg'] = "一般錯誤: " . $e->getMessage();
}

echo json_encode($returnData);
?>
