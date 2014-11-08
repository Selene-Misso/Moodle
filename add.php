<?php
// DB接続
$pdo = new PDO("mysql:dbname=accountbook", "mishiro", "314159");
// 文字コード設定
$pdo->query("SET NAMES utf8");

if(isset($_POST['add'])) {
	// 入力ボタン
	$sql = 'INSERT INTO journal
			( jounal_ID,titles_ID,jounal_DATE,jounal_ABST,jounal_AMOUNT)
			VALUES
			( null, :kamoku , :kamoku_dates , :kamoku_abst , :kamoku_amount )';
	
	$sth = $pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
	
	// クエリ実行
	$sth->execute(array(':kamoku' => $_POST["kamoku_val"],
			':kamoku_dates'  => $_POST["kamoku_date"],
			':kamoku_abst'   => $_POST["abstract"],
			':kamoku_amount' => $_POST["amount"]));
	
	print_r($sth->errorInfo());
	
	exit;
}