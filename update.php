<?php
// DB接続
$pdo = new PDO("mysql:dbname=accountbook", "mishiro", "314159");
// 文字コード設定
$pdo->query("SET NAMES utf8");

if (isset($_POST['del'])){
	// 削除ボタン
	$sql = 'DELETE FROM journal WHERE jounal_ID = :id';
	$sth = $pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));

	// 削除実行
	$sth->execute(array(':id' => $_POST["selectID"]));

	echo "Del: ID=".$_POST["selectID"]."<br>";
	print_r($sth->errorInfo());
	exit;
}elseif (isset($_POST['mod'])){
	// 修正ボタン
	// 入力ボタン
	$sql = 'UPDATE journal SET
			titles_ID = :kamoku,
			jounal_DATE = :kamoku_dates,
			jounal_ABST = :kamoku_abst,
			jounal_AMOUNT = :kamoku_amount
			WHERE jounal_ID = :id';
	$sth = $pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
	
	// クエリ実行
	$sth->execute(array(':kamoku' => $_POST["kamoku_val"],
			':kamoku_dates'  => $_POST["kamoku_date"],
			':kamoku_abst'   => $_POST["abstract"],
			':kamoku_amount' => $_POST["amount"],
			':id'            => $_POST["selectID"]));
	
	echo "Update: ID=".$_POST["selectID"]."<br>";
	print_r($sth->errorInfo());
	
	exit;
}
?>