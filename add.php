<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>出納帳</title>
<link rel="stylesheet" type="text/css" href="css/styles.css">
</head>
<body>
<header>
<h1>出納帳</h1>
</header>
<div role='main'>

<?php
// DB接続
$pdo = new PDO("mysql:dbname=accountbook", "mishiro", "314159");
// 文字コード設定
$pdo->query("SET NAMES utf8");

// レコード表示
echo '<table border="1">
		<tr>
		<td rowspan="2">
		<label><input type="radio" name="switch" value="0" disabled
		';
if($_POST["switch"] != 1){echo "checked=\"checked\"";}
echo '> 入金</label><br>
		<label><input type="radio" name="switch" value="1" disabled
		';
if($_POST["switch"] == 1){echo "checked=\"checked\"";}
echo '> 出金</label></td>
		<th>日付</th>
		<th>科目</th>
		<th>摘要</th>
		<th>金額</th>
		</tr>
';
echo "<tr>";
echo "<td>".$_POST["kamoku_date"]."</td>\n";
$sql = 'SELECT titles_name FROM titles WHERE titles_ID = :id';
$sth = $pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
$sth->execute(array(':id' => $_POST["kamoku_val"]));
$row = $sth->fetch();
echo "<td class=\"kamoku\">".$row['titles_name']."</td>\n";
echo "<td class=\"tekiyou\">".$_POST["abstract"]."</td>\n";
echo "<td class=\"valueRight\" >".$_POST["amount"]."</td>\n";
echo "</tr>\n</table>";


if(isset($_POST['add'])) {
	// 入力ボタン
	$sql = 'INSERT INTO journal
			( jounal_ID,titles_ID,jounal_DATE,jounal_ABST,jounal_AMOUNT)
			VALUES
			( null, :kamoku , :kamoku_dates , :kamoku_abst , :kamoku_amount )';
	
	$sth = $pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
	
	// クエリ実行
	$sth->execute(array(
			':kamoku'       => $_POST["kamoku_val"],
			':kamoku_dates'  => $_POST["kamoku_date"],
			':kamoku_abst'   => $_POST["abstract"],
			':kamoku_amount' => $_POST["amount"]));
	
	echo "<p>";
	if($sth->errorCode() == '00000'){
		echo "上記レコードを追加しました．";
	}else{
		echo "レコード追加に失敗しました．<br>";
		print_r($sth->errorCode());
	}
	echo "</p>";
		
}

?>

<form action="accountbook.php">
<button type="submit" name="back">戻る</button>
</form>
</div>
</body>
</html>