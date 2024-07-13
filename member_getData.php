<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type: application/json');

try {
    // 引入資料庫連線
    require_once("./connect_cid101g3.php");

    // 預設返回的數據結構
    $returnData = [
        'code' => 200,
        'msg' => '',
        'data' => []
    ];

    // 確認是 POST 請求
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // 解析 JSON 資料
        $postData = json_decode(file_get_contents("php://input"), true);

        // 從 POST 請求中獲取需要更新的會員資料
        $mem_name = $postData['mem_name'];
        $mem_tel = $postData['mem_tel'];
        $mem_gender = $postData['mem_gender'];
        $mem_birth = $postData['mem_birth'];
        $mem_addr = $postData['mem_addr'];
        $mem_carrier = $postData['mem_carrier'];
        $mem_company = $postData['mem_company'];

        // 執行 SQL 更新語句
        $sql = "UPDATE member SET 
                mem_name = :mem_name,
                mem_tel = :mem_tel,
                mem_gender = :mem_gender,
                mem_birth = :mem_birth,
                mem_addr = :mem_addr,
                mem_carrier = :mem_carrier,
                mem_company = :mem_company
                WHERE mem_id = :mem_id";

        $statement = $pdo->prepare($sql);
        $statement->bindParam(':mem_name', $mem_name, PDO::PARAM_STR);
        $statement->bindParam(':mem_tel', $mem_tel, PDO::PARAM_STR);
        $statement->bindParam(':mem_gender', $mem_gender, PDO::PARAM_STR);
        $statement->bindParam(':mem_birth', $mem_birth, PDO::PARAM_STR);
        $statement->bindParam(':mem_addr', $mem_addr, PDO::PARAM_STR);
        $statement->bindParam(':mem_carrier', $mem_carrier, PDO::PARAM_STR);
        $statement->bindParam(':mem_company', $mem_company, PDO::PARAM_STR);
        // 假設 mem_id 是從前端傳來的
        $mem_id = $postData['mem_id'];
        $statement->bindParam(':mem_id', $mem_id, PDO::PARAM_INT);

        if ($statement->execute()) {
            $returnData['msg'] = '會員資料更新成功';
        } else {
            $returnData['code'] = 10005;
            $returnData['msg'] = '會員資料更新失敗';
        }
    } else {
        $returnData['code'] = 10001;
        $returnData['msg'] = '僅接受 POST 請求';
    }

} catch (PDOException $e) {
    $returnData['code'] = 10003;
    $returnData['msg'] = "資料庫錯誤: " . $e->getMessage();
} catch (Exception $e) {
    $returnData['code'] = 10004;
    $returnData['msg'] = "一般錯誤: " . $e->getMessage();
}

echo json_encode($returnData);
?>
