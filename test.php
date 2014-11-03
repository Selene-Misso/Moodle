<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>Test Page</title>
<script type="text/javascript">
<!--

	// 入出金切り替え処理用に
	// マウスクリック時にPOSTする
	function swichInOut(){
		var s = document.forms['selectForm'];
		s.method = 'POST';
		s.action = 'test.php';
		s.submit();
		return true;
	}
// --></script>
</head>
<body>
<div>
<?php 
// 入出金処理
$flg_switch = 0;
if ($_POST) {
	$flg_switch = $_POST['switch'];
}
?>

<form action="accountbook.php" method="POST" name="selectForm">
<label><input type="radio" name="switch" value="0"
 onclick="return swichInOut();"
 <?php if($flg_switch != 1){echo "checked=\"checked\"";} ?>>
 入金</label><br>
<label><input type="radio" name="switch" value="1"
 onclick="return swichInOut();"
 <?php if($flg_switch == 1){echo "checked=\"checked\"";} ?>>
 出金
</label>
</form>


<select name="inout">
<?php

// DB接続
$pdo = new PDO("mysql:dbname=accountbook", "mishiro", "314159");
// 文字コード設定
$pdo->query("SET NAMES utf8");

// クエリ実行
$st = $pdo->query("SELECT * FROM titles WHERE in_out_flg = ".$flg_switch);

// クエリ処理
$isfirst = 1;
while ($row = $st->fetch()) {
	$flg_inout = htmlspecialchars($row['in_out_flg']);
	$title = htmlspecialchars($row['titles_name'], ENT_HTML5, 'UTF-8');
	if($isfirst == 1){
		echo "<option value=\"".$flg_inout."\" selected>$title</option>";
		$isfirst = 0;
	}else{
		echo "<option value=\"".$flg_inout."\">$title</option>";
	}
}

?>

</select>
<hr>
</div>

<?php

// DB接続
$pdo = new PDO("mysql:dbname=accountbook", "mishiro", "314159");
// 文字コード設定
$pdo->query("SET NAMES utf8");

// クエリ実行
$str = "select jounal_ID, date_format(jounal_DATE,'%m/%d'), titles_name, jounal_ABST, jounal_AMOUNT, in_out_flg
from accountbook.titles as T, accountbook.journal as J
where J.titles_ID = T.titles_id and
J.jounal_DATE between '2014-11-01' and '2014-11-30'
order by jounal_DATE, jounal_ID asc;";
$st = $pdo->query($str);

// クエリ処理
$isfirst = 1;
echo "<table border=1>";
while ($row = $st->fetch()) {
	$flg_inout = htmlspecialchars($row['in_out_flg']);
	$amount = htmlspecialchars($row['jounal_AMOUNT']);
	$title = htmlspecialchars($row['jounal_ABST'], ENT_HTML5, 'UTF-8');
	echo "<tr><td>$flg_inout</td><td>$amount</td><td>$title</td></tr>";
}
echo "</table>";

?>

</body>

</html>