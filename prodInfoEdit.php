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

    if (isset($input['prod_id'], $input['prod_category'], $input['prod_name'], $input['prod_price'], $input['prod_desc'])) {
        $prod_id = $input['prod_id'];
        $prod_category = $input['prod_category'];
        $prod_name = $input['prod_name'];
        $prod_price = $input['prod_price'];
        $prod_desc = $input['prod_desc'];

        // 更新数据库
        $updateSql = "UPDATE prod SET prod_category = :prod_category, prod_name = :prod_name, prod_price = :prod_price, prod_desc = :prod_desc WHERE prod_id = :prod_id";
        $updateStatement = $pdo->prepare($updateSql);
        $updateStatement->bindParam(':prod_category', $prod_category);
        $updateStatement->bindParam(':prod_name', $prod_name);
        $updateStatement->bindParam(':prod_price', $prod_price);
        $updateStatement->bindParam(':prod_id', $prod_id);
        $updateStatement->bindParam(':prod_desc', $prod_desc);

        if ($updateStatement->execute()) {
            $returnData['msg'] = '更新成功';
        } else {
            $returnData['code'] = 500;
            $returnData['msg'] = '更新失敗';
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