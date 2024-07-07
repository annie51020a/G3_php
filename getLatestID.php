<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Content-Type: application/json');

try {
    require_once("./connect_cid101g3.php");

    $returnData = [
        'code' => 200,
        'msg' => '',
        'data' => null
    ];

    $sql = "SELECT MAX(emp_id) AS latest_id FROM employee";
    $statement = $pdo->query($sql);
    $result = $statement->fetch(PDO::FETCH_ASSOC);

    if ($result && isset($result['latest_id'])) {
        $returnData['data'] = [
            'latest_id' => (int)$result['latest_id'],
        ];
    } else {
        $returnData['code'] = 404;
        $returnData['msg'] = 'No ID found';
    }

} catch (PDOException $e) {
    $returnData['code'] = 500;
    $returnData['msg'] = "Database error: " . $e->getMessage();
} catch (Exception $e) {
    $returnData['code'] = 500;
    $returnData['msg'] = "General error: " . $e->getMessage();
}

echo json_encode($returnData);
?>
