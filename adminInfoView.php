<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: X-Requested-With");
header('Content-Type: application/json');

require_once 'connect_cid101g3.php';

$emp_id = $_GET['emp_id'];

try {
    $sql = "SELECT emp_id, emp_name, emp_account, emp_password FROM employee WHERE emp_id = :emp_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':emp_id', $emp_id, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $member_info = $stmt->fetch(PDO::FETCH_ASSOC);
        $returnData = [
            'code' => 200,
            'msg' => 'Success',
            'data' => $member_info
        ];
    } else {
        $returnData = [
            'code' => 404,
            'msg' => '未找到該員工',
            'data' => null
        ];
    }
} catch (PDOException $e) {
    $returnData = [
        'code' => 500,
        'msg' => "資料庫錯誤：" . $e->getMessage(),
        'data' => null
    ];
} catch (Exception $e) {
    $returnData = [
        'code' => 500,
        'msg' => "通用錯誤：" . $e->getMessage(),
        'data' => null
    ];
}

echo json_encode($returnData);

$pdo = null;
?>
