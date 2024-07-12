<?php
require_once("./connect_cid101g3.php");

// 從 POST 請求中取得需要更新的會員資料
$mem_name = $_POST['mem_name'];
$mem_tel = $_POST['mem_tel'];
$mem_gender = $_POST['mem_gender'];
$mem_birth = $_POST['mem_birth'];
$mem_addr = $_POST['mem_addr'];
$mem_carrier = $_POST['mem_carrier'];
$mem_company = $_POST['mem_company'];

// 使用 SQL 更新語句來更新會員資料
$sql = "UPDATE members SET 
        mem_name = '$mem_name', 
        mem_tel = '$mem_tel', 
        mem_gender = '$mem_gender', 
        mem_birth = '$mem_birth', 
        mem_addr = '$mem_addr', 
        mem_carrier = '$mem_carrier', 
        mem_company = '$mem_company' 
        WHERE mem_email = '$mem_email'";

// 執行 SQL 更新語句
if ($conn->query($sql) === TRUE) {
    echo "會員資料更新成功";
} else {
    echo "更新失敗：" . $conn->error;
}

// 關閉資料庫連線
$conn->close();
?>
