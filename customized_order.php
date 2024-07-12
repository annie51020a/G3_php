<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header("Access-Control-Allow-Headers: Content-Type, X-Requested-With");

try {
    require_once("./connect_cid101g3.php");
    
    $returnData = [
        'code' => 200,
        'msg' => '',
        'data' => []
    ];


    $sql2 = "SELECT * FROM customized_orders";
    $orders = $pdo->prepare($sql2);
    $orders->execute();
    $ordersData = $orders->fetchAll(PDO::FETCH_ASSOC);

    $returnData['data']['orders'] = $ordersData;
    $returnData['msg'] = 'Success';
    

} catch (PDOException $e) {
    $returnData['code'] = 10003;
    $returnData['msg'] = "Database error: " . $e->getMessage();
} catch (Exception $e) {
    $returnData['code'] = 10004;
    $returnData['msg'] = "General error: " . $e->getMessage();
}

echo json_encode($returnData);
?>
