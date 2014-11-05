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
	<div id="プルダウン">
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
				<?php if($flg_switch != 1){echo "checked=\"checked\"";} ?>> 入金</label><br>
			<label><input type="radio" name="switch" value="1"
				onclick="return swichInOut();"
				<?php if($flg_switch == 1){echo "checked=\"checked\"";} ?>> 出金 </label>
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

	<div id="表を出力">
	<?php
	
	// DB接続
	$pdo = new PDO("mysql:dbname=accountbook", "mishiro", "314159");
	// 文字コード設定
	$pdo->query("SET NAMES utf8");
	
	// クエリ実行
	$str = "SELECT T.in_out_flg, sum(J.jounal_AMOUNT) as total
				FROM accountbook.titles as T, accountbook.journal as J
				WHERE J.titles_ID = T.titles_id and
				J.jounal_DATE <= '2014-10-31'
				group by T.in_out_flg;";
	$st = $pdo->query($str);
	
	// 処理
	while ($row = $st->fetch()) {
		$row_flg_inout =  htmlspecialchars($row['in_out_flg']);
		$row_total = htmlspecialchars($row['total']);
	
		if($row_flg_inout == 0){
			$last_total_in = $row_total;
		}else{
			$last_total_out = $row_total;
		}
	}
	echo "<table border=1>";
	echo "<tr><td>&nbsp;</td><td>11/01</td>";
	echo "<td>前月繰越</td><td>&nbsp;</td>";
	echo "<td>$last_total_in</td><td>$last_total_out</td></tr>";
	
	// クエリ実行
	$str = "select jounal_ID, date_format(jounal_DATE,'%m/%d') as jounal_DATE, 
				titles_name, jounal_ABST, jounal_AMOUNT, in_out_flg
				from accountbook.titles as T, accountbook.journal as J
				where J.titles_ID = T.titles_id and
				J.jounal_DATE between '2014-11-01' and '2014-11-30'
				order by jounal_DATE, jounal_ID asc;";
	$st = $pdo->query($str);
	// クエリ処理
	while ($row = $st->fetch()) {
		$row_id = htmlspecialchars($row['jounal_ID']);
		$row_flg_inout = htmlspecialchars($row['in_out_flg']);
		$row_title = htmlspecialchars($row['titles_name']);
		$row_amount = htmlspecialchars ( $row ['jounal_AMOUNT'] );
		$row_abst = htmlspecialchars ( $row ['jounal_ABST']);
		$row_date = htmlspecialchars ( $row ['jounal_DATE']);
		
		// チェックボックス,日付,科目,摘要,入金,出金
		echo "<tr>";
		echo "<td><input type=\"radio\" name=\"isSelect\" value=\"$row_id\"></td>";
		echo "<td>$row_date</td>";
		echo "<td>$row_title</td>";
		echo "<td>$row_abst</td>";
		if($row_flg_inout == 0){ // 入金時
			echo "<td>$row_amount</td>";
			echo "<td>&nbsp;</td>";
		}else{ // 出金
			echo "<td>&nbsp;</td>";
			echo "<td>$row_amount</td>";
		}
		echo "</tr>";
	}
	
	// クエリ実行
	$str = "SELECT T.in_out_flg, sum(J.jounal_AMOUNT) as total
					FROM accountbook.titles as T, accountbook.journal as J
					WHERE J.titles_ID = T.titles_id and
					J.jounal_DATE between '2014-11-01' and '2014-11-30'
					group by T.in_out_flg;";
	$st = $pdo->query($str);
	
	// 処理
	while ($row = $st->fetch()) {
		$row_flg_inout =  htmlspecialchars($row['in_out_flg']);
		$row_total = htmlspecialchars($row['total']);
		
		if($row_flg_inout == 0){
			$row_total_in = $row_total + $last_total_in;
		}else{
			$row_total_out = $row_total + $last_total_out;
		}
	}
	echo "<tr><td colspan=4>&nbsp;</td>";
	echo "<td>$row_total_in</td><td>$row_total_out</td></tr>";
	
	echo "</table>";
	
	?>
	</div>

	<div id="input">
	<?php 
	// DB接続
	$pdo = new PDO("mysql:dbname=accountbook", "mishiro", "314159");
	// 文字コード設定
	$pdo->query("SET NAMES utf8");
	
	// クエリ実行
	/*$str = "INSERT INTO `accountbook`.`titles` (`titles_id`,`in_out_flg`,`titles_name`)
				VALUES (null, '1', '商品');";
	$st = $pdo->query($str);
	
	if(!$st){
		mysql_error();
	}*/
	
	?>
	</div>
	
</body>

</html>