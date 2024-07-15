<?php 
	//--------------------開發階段
	header("Access-Control-Allow-Origin:*");	
	//---
	// $dbname = "cid101_g3";
	// $user = "root";
	// $password = "";

	//--------------------prod階段
	$dbname = "tibamefe_cid101g3";
	$user = "tibamefe_since2021";
	$password = "vwRBSb.j&K#E";

	$dsn = "mysql:host=localhost;port=3306;dbname=$dbname;charset=utf8";

	$options = array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_CASE=>PDO::CASE_LOWER);
	// PDO::ATTR_CASE=>PDO::CASE_LOWER 欄位名一律小寫
	
	//建立pdo物件
	$pdo = new PDO($dsn, $user, $password, $options);	
?>