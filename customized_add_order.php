<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// 啟用錯誤報告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 設置響應頭為 JSON
header('Content-Type: application/json');

// 引入數據庫配置文件
require_once("./connect_cid101g3.php");

// 定義 logError 函數
function logError($message) {
    error_log($message); // 這將錯誤記錄到 PHP 的錯誤日誌中
}

try {

    // // 檢查主鍵列是否已設置為自增
    // $checkAutoIncrementQuery = "SELECT EXTRA FROM INFORMATION_SCHEMA.COLUMNS 
    //                             WHERE TABLE_NAME = 'customized_orders' AND COLUMN_NAME = 'id'";
    // $stmt = $pdo->query($checkAutoIncrementQuery);
    // $row = $stmt->fetch(PDO::FETCH_ASSOC);
    // if (strpos($row['EXTRA'], 'auto_increment') === false) {
    //     // 如果主鍵列沒有設置為自增，則修改表結構
    //     $alterTableQuery = "ALTER TABLE customized_orders MODIFY COLUMN id INT NOT NULL AUTO_INCREMENT";
    //     $pdo->exec($alterTableQuery);
    //     logError("Table structure updated to set id as AUTO_INCREMENT");
    // }

    // 開始事務
    $pdo->beginTransaction();

    // 從請求中讀取 JSON 輸入數據
    $input = file_get_contents('php://input');
    logError("Received input: " . $input);
    $data = json_decode($input, true);

    // 檢查是否存在必要的數據
    if (
        isset($data["mem_id"]) && isset($data["ord_name"]) && isset($data["ord_tel"]) && 
        isset($data["ord_date"]) && isset($data["customized_pic"]) && isset($data["ord_sum"]) && 
        isset($data["promo_state"]) && isset($data["ord_pay"]) && isset($data["cus_order_state"]) &&
        isset($data["receiver_name"]) && isset($data["receiver_tel"]) && isset($data["receiver_address"]) &&
        isset($data["prefer_time"])
    ) {
        $sql = "INSERT INTO customized_orders (
            mem_id, ord_name, ord_tel, ord_mail, ord_date, customized_pic, ord_sum, promo_state, 
            ord_pay, cus_order_state, ord_note, receiver_name, receiver_tel, receiver_address, 
            receiver_mail, prefer_time, invoice_num, uniform_num
        ) VALUES (
            :mem_id, :ord_name, :ord_tel, :ord_mail, :ord_date, :customized_pic, :ord_sum, :promo_state, 
            :ord_pay, :cus_order_state, :ord_note, :receiver_name, :receiver_tel, :receiver_address, 
            :receiver_mail, :prefer_time, :invoice_num, :uniform_num
        )";
        
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindValue(":mem_id", $data["mem_id"], PDO::PARAM_INT);
        $stmt->bindValue(":ord_name", $data["ord_name"], PDO::PARAM_STR);
        $stmt->bindValue(":ord_tel", $data["ord_tel"], PDO::PARAM_STR);
        $stmt->bindValue(":ord_mail", $data["ord_mail"], PDO::PARAM_STR);
        $stmt->bindValue(":ord_date", $data["ord_date"], PDO::PARAM_STR);
        $stmt->bindValue(":customized_pic", $data["customized_pic"], PDO::PARAM_STR);
        $stmt->bindValue(":ord_sum", $data["ord_sum"], PDO::PARAM_INT);
        $stmt->bindValue(":promo_state", $data["promo_state"], PDO::PARAM_INT);
        $stmt->bindValue(":ord_pay", $data["ord_pay"], PDO::PARAM_INT);
        $stmt->bindValue(":cus_order_state", $data["cus_order_state"], PDO::PARAM_INT);
        $stmt->bindValue(":ord_note", $data["ord_note"], PDO::PARAM_STR);
        $stmt->bindValue(":receiver_name", $data["receiver_name"], PDO::PARAM_STR);
        $stmt->bindValue(":receiver_tel", $data["receiver_tel"], PDO::PARAM_STR);
        $stmt->bindValue(":receiver_address", $data["receiver_address"], PDO::PARAM_STR);
        $stmt->bindValue(":receiver_mail", $data["receiver_mail"], PDO::PARAM_STR);
        $stmt->bindValue(":prefer_time", $data["prefer_time"], PDO::PARAM_INT);
        $stmt->bindValue(":invoice_num", $data["invoice_num"], PDO::PARAM_STR);
        $stmt->bindValue(":uniform_num", $data["uniform_num"], PDO::PARAM_STR);

        $result = $stmt->execute();
        logError("Insert result: " . ($result ? "success" : "fail"));

        // 提交事務
        $pdo->commit();

        echo json_encode([
            "error" => false, 
            "msg" => "訂單創建成功"
        ]);
    } else {
        throw new Exception("缺少必要的數據");
    }
} catch (PDOException $e) {
    // 回滾事務
    $pdo->rollBack();
    logError("PDO Exception: " . $e->getMessage());
    header('Content-Type: application/json'); // 確保錯誤響應也是 JSON
    echo json_encode(['error' => true, 'msg' => '數據庫錯誤: ' . $e->getMessage()]);
} catch (Exception $e) {
    // 回滾事務
    $pdo->rollBack();
    logError("Exception: " . $e->getMessage());
    header('Content-Type: application/json'); // 確保錯誤響應也是 JSON
    echo json_encode(['error' => true, 'msg' => $e->getMessage()]);
}
?>