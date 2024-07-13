<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With, Content-Type");

try {
    require_once("./connect_cid101g3.php");
    
    $returnData = [
        'code' => 200,
        'msg' => '',
        'data' => []
    ];

    // 獲取POST數據
    $input = json_decode(file_get_contents('php://input'), true);
    $account = $input['account'];
    $password = $input['password'];

    // 執行 SQL 查詢用戶
    $sql = "SELECT * FROM member WHERE mem_email = :account AND mem_psw = :password";
    $statement = $pdo->prepare($sql);
    $statement->bindParam(':account', $account);
    $statement->bindParam(':password', $password);
    $statement->execute();
    
    // 獲取查詢結果
    $userData = $statement->fetch(PDO::FETCH_ASSOC);
    
    if ($userData) {
        $returnData['data']['user'] = $userData;
        $returnData['msg'] = '登入成功';
    } else {
        $returnData['code'] = 401;
        $returnData['msg'] = '帳號或密碼錯誤';
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
