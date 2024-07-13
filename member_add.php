<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

function generateUniqueTel($pdo) {
    do {
        // 生成一個隨機的 10 位數字
        $tel = '1' . str_pad(mt_rand(0, 999999999), 9, '0', STR_PAD_LEFT);
        
        // 檢查該電話號碼是否已存在
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM member WHERE mem_tel = :tel");
        $stmt->execute([':tel' => $tel]);
        $count = $stmt->fetchColumn();
    } while ($count > 0);
    
    return $tel;
}

try {
    require_once("./connect_cid101g3.php");
    
    $returnData = [
        'code' => 200,
        'msg' => '',
        'data' => []
    ];

    // 處理 OPTIONS 請求
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        exit(0);
    }

    // 獲取 JSON 輸入
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON input");
    }

    // 獲取並驗證數據
    $email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $data['password'] ?? '';
    $gender = filter_var($data['gender'] ?? '', FILTER_SANITIZE_STRING);
    $birth = filter_var($data['birth'] ?? '', FILTER_SANITIZE_STRING);

    if (!$email || empty($password) || empty($gender) || empty($birth)) {
        throw new Exception("有東西沒填寫完喔");
    }

    // 密碼哈希
    // $password = password_hash($password, PASSWORD_BCRYPT);

    // 從電子郵件中提取 "@" 前的字串作為會員名稱
    $mem_name = substr($email, 0, strpos($email, '@'));

    // 設置其他欄位的默認值
    $create_date = date('Y-m-d H:i:s');
    $tel = generateUniqueTel($pdo);  // 請注意：這應該是一個有效的電話號碼
    $mem_pic = '';
    $mem_addr = '';
    $mem_carrier = '';
    $mem_company = '';
    $mem_status = 0;
    // $mem_googleid = null;
    $mem_card = '';
    $mem_card_date = null;

    // 準備 SQL 語句
    // $sql = "INSERT INTO member (mem_name, mem_email, mem_psw, mem_tel, mem_gender, mem_birth, mem_create, mem_pic, mem_addr, mem_carrier, mem_company, mem_status, mem_googleid, mem_card, mem_card_date) 
    //         VALUES (:mem_name, :email, :password, :mem_tel, :gender, :birth, :create_date, :mem_pic, :mem_addr, :mem_carrier, :mem_company, :mem_status, :mem_googleid, :mem_card, :mem_card_date)";
        // 準備 SQL 語句
    $sql = "INSERT INTO member (mem_name, mem_email, mem_psw, mem_tel, mem_gender, mem_birth, mem_create, mem_pic, mem_addr, mem_carrier, mem_company, mem_status, mem_card, mem_card_date) 
    VALUES (:mem_name, :email, :password, :mem_tel, :gender, :birth, :create_date, :mem_pic, :mem_addr, :mem_carrier, :mem_company, :mem_status,  :mem_card, :mem_card_date)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':mem_name' => $mem_name,
        ':email' => $email,
        ':password' => $password,
        ':mem_tel' => $tel,
        ':gender' => $gender,
        ':birth' => $birth,
        ':create_date' => $create_date,
        ':mem_pic' => $mem_pic,
        ':mem_addr' => $mem_addr,
        ':mem_carrier' => $mem_carrier,
        ':mem_company' => $mem_company,
        ':mem_status' => $mem_status,
        // ':mem_googleid' => $mem_googleid,
        ':mem_card' => $mem_card,
        ':mem_card_date' => $mem_card_date
    ]);

    $returnData['msg'] = '註冊成功';
    $returnData['data']['member'] = [
        'mem_name' => $mem_name,
        'mem_email' => $email,
        'mem_gender' => $gender,
        'mem_birth' => $birth
    ];

} catch (PDOException $e) {
    $returnData['code'] = 10003;
    $returnData['msg'] = "Database error: " . $e->getMessage();
} catch (Exception $e) {
    $returnData['code'] = 10004;
    $returnData['msg'] = "General error: " . $e->getMessage();
}

echo json_encode($returnData);