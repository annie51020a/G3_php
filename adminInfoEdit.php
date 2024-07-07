<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: X-Requested-With, Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

try {
    require_once("./connect_cid101g3.php");

    $returnData = [
        'code' => 200,
        'msg' => '',
        'data' => null
    ];

    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['emp_id'], $input['emp_account'], $input['emp_name'], $input['emp_password'])) {
        $emp_id = $input['emp_id'];
        $emp_account = $input['emp_account'];
        $emp_name = $input['emp_name'];
        $emp_password = $input['emp_password'];

        // 检查是否有相同的 emp_account
        $checkSql = "SELECT emp_id FROM employee WHERE emp_account = :emp_account AND emp_id != :emp_id";
        $checkStatement = $pdo->prepare($checkSql);
        $checkStatement->bindParam(':emp_account', $emp_account);
        $checkStatement->bindParam(':emp_id', $emp_id);
        $checkStatement->execute();
        
        if ($checkStatement->rowCount() > 0) {
            $returnData['code'] = 409;
            $returnData['msg'] = '帳號已存在';
        } else {
            // 更新数据库
            $updateSql = "UPDATE employee SET emp_account = :emp_account, emp_name = :emp_name, emp_password = :emp_password WHERE emp_id = :emp_id";
            $updateStatement = $pdo->prepare($updateSql);
            $updateStatement->bindParam(':emp_account', $emp_account);
            $updateStatement->bindParam(':emp_name', $emp_name);
            $updateStatement->bindParam(':emp_password', $emp_password);
            $updateStatement->bindParam(':emp_id', $emp_id);
            
            if ($updateStatement->execute()) {
                $returnData['msg'] = '更新成功';
            } else {
                $returnData['code'] = 500;
                $returnData['msg'] = '更新失敗';
            }
        }
    } else {
        $returnData['code'] = 400;
        $returnData['msg'] = '缺少必要的參數';
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
