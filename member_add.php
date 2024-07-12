<?php
header('Content-Type: application/json');
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

try {
    // 獲取 POST 資料並清理
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $gender = filter_var($_POST['gender'] ?? '', FILTER_SANITIZE_STRING);
    $birth = filter_var($_POST['birth'] ?? '', FILTER_SANITIZE_STRING);

    if (!$email || empty($password) || empty($gender) || empty($birth)) {
        throw new Exception("所有必填字段都必須提供有效值");
    }

    // 密碼哈希
    $password = password_hash($password, PASSWORD_BCRYPT);

    // 從電子郵件中提取 "@" 前的字串作為會員名稱
    $mem_name = substr($email, 0, strpos($email, '@'));

    // 設置創建日期
    $create_date = date('Y-m-d H:i:s');

    // 設置其他欄位的默認值
    $mem_tel = '123456789';
    $mem_pic = ''; // 可以設置一個默認的圖片路徑
    $mem_addr = '';
    $mem_carrier = '';
    $mem_company = '';
    $mem_status = 0; // 默認沒有優惠券
    $mem_googleid = null;
    $mem_card = '';
    $mem_card_date = null;

    // 準備 SQL 語句
    $sql = "INSERT INTO member (mem_name, mem_email, mem_psw, mem_tel, mem_gender, mem_birth, mem_create, mem_pic, mem_addr, mem_carrier, mem_company, mem_status, mem_googleid, mem_card, mem_card_date) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("預處理語句準備失敗: " . $conn->error);
    }

    $stmt->bind_param("sssssssssssisss", 
        $mem_name, $email, $password, $mem_tel, $gender, $birth, $create_date,
        $mem_pic, $mem_addr, $mem_carrier, $mem_company, $mem_status,
        $mem_googleid, $mem_card, $mem_card_date
    );

    if (!$stmt->execute()) {
        throw new Exception("執行查詢失敗: " . $stmt->error);
    }

    $response = ['code' => 200, 'msg' => '註冊成功'];

    // 關閉語句和連接
    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    $response = ['code' => 500, 'msg' => '發生錯誤: ' . $e->getMessage()];
} finally {
    // 清理任何意外輸出
    $output = ob_get_clean();
    if (!empty($output)) {
        error_log("Unexpected output: " . $output);
    }
    
    // 輸出 JSON 響應
    echo json_encode($response);
}
?>