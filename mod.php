<!-- 出納帳システム
UTF-8
HTML5
PHP5.5
mysql

参考資料
- http://www.html5-memo.com/first-html5/html5-001/
- http://hyper-text.org/archives/2011/07/html5_mistakes.shtml
- http://www.htmq.com/html5/
- http://www.phpbook.jp/tutorial/mysql/

 -->

<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>出納帳</title>
<link rel="stylesheet" type="text/css" href="css/styles.css">
<script>
<!--
	// 入出金切り替え処理用に
	// マウスクリック時にPOSTする
	function swichInOut(){
		var s = document.forms['selectForm'];
		s.method = 'POST';
		s.action = 'accountbook.php';
		s.submit();
		return true;
	}
// -->
</script>
</head>
<body>
<?php 
// 入出金用 前処理
$flg_switch = 0;
if ($_POST) {
	$flg_switch = $_POST['switch'];
}

?>


<header>
<h1>出納帳</h1>
<form action="accountbook.php" method="GET">
<input type="month" name="selectMonth" value=
<?php 
	$thisYear  = date('Y');
	$thisMonth = date('m');
	if ($_POST) {
		$num1 = explode("-", $_GET['selectMonth']);
		$thisYear = $num1[0];
		$thisMonth= $num1[1];
	}
	echo '"',$thisYear, "-", $thisMonth, '"';
?>
 required >
<button type="submit" >移動</button>
</form>
</header>

<div role="application">
<form action="update.php" method="POST">
<?php 

// 対象レコード取得
$selectID = $_POST['isSelect'];

// DB接続
$pdo = new PDO("mysql:dbname=accountbook", "mishiro", "314159");
// 文字コード設定
$pdo->query("SET NAMES utf8");

$str = "select jounal_ID, date_format(jounal_DATE,'%Y-%m-%d') as jounal_DATE,
titles_name, jounal_ABST, jounal_AMOUNT, in_out_flg, J.titles_ID
from accountbook.titles as T, accountbook.journal as J
where J.titles_ID = T.titles_ID
and J.jounal_ID = $selectID;";
$st = $pdo->query($str);
$row = $st->fetch();

$row_id = htmlspecialchars($row['jounal_ID']);
$row_flg_inout = htmlspecialchars($row['in_out_flg']);
$row_title_ID = htmlspecialchars($row['titles_ID']);
$row_title = htmlspecialchars($row['titles_name']);
$row_amount = htmlspecialchars ( $row ['jounal_AMOUNT'] );
$row_abst = htmlspecialchars ( $row ['jounal_ABST']);
$row_date = htmlspecialchars ( $row ['jounal_DATE']);
$thisYear = date("Y", strtotime($row['jounal_DATE']));
$thisMonth = date("m", strtotime($row['jounal_DATE']));

// チェックボックス,日付,科目,摘要,入金,出金
echo '<table border="1">
		<tr>
		<td rowspan="2">
		<label><input type="radio" name="switch" value="0" disabled
		';
if($row_flg_inout != 1){echo "checked=\"checked\"";}
echo '> 入金</label><br>
		<label><input type="radio" name="switch" value="1" disabled
		';
if($row_flg_inout == 1){echo "checked=\"checked\"";}
echo '> 出金</label></td>
		<th>日付</th>
		<th>科目</th>
		<th>摘要</th>
		<th>金額</th>
		<td rowspan="2">
		<button type="submit" name="mod">修正</button><br>
		<button type="submit" name="del">削除</button></td>
		</tr>';
// jounalIDを保持する隠しフィールド
echo "<tr><td><input type=\"hidden\" name=\"selectID\" value=\"$selectID\">\n";
echo "<input type=\"date\" name=\"kamoku_date\" value=\"$row_date\" ";
echo "min=\"".$thisYear."-".$thisMonth."-01\"";
echo "max=\"".$thisYear."-".$thisMonth."-".date("t", mktime(0,0,0,$thisMonth,1,$thisYear))."\"";
echo "></td>\n";
// 科目選択
echo "<td><select name=\"kamoku_val\" class=\"kamoku\">$row_title";
$st = $pdo->query("SELECT * FROM titles WHERE in_out_flg = ".$row_flg_inout);
while ($row = $st->fetch()) {
	$flg_id = $row['titles_id'];
	$title = htmlspecialchars($row['titles_name']);
	if($flg_id == $row_title_ID){
		echo "<option value=\"".$flg_id."\" selected>$title</option>\n";
		$isfirst = 0;
	}else{
		echo "<option value=\"".$flg_id."\">$title</option>\n";
	}
}
echo "</select></td>\n";

echo "<td><input type=\"text\" name=\"abstract\" class=\"tekiyou\" value=\"$row_abst\"></td>\n";
echo "<td><input type=\"number\" name=\"amount\" min=\"0\" class=\"valueRight\" value=\"$row_amount\"></td>\n";
echo "</tr>\n</table>";

?>
</form>
</div>

<div role="main">
<form action="mod.php" method="POST">
<table border="1">
<thead><tr>
<th><button type="submit" >選択</button></th>
<th>日付</th>
<th>科目</th>
<th>摘要</th>
<th>入金</th>
<th>出金</th>
</tr></thead>
<?php

// DB接続
$pdo = new PDO("mysql:dbname=accountbook", "mishiro", "314159");
// 文字コード設定
$pdo->query("SET NAMES utf8");

// 前月繰越の表示
$str = "SELECT T.in_out_flg, sum(J.jounal_AMOUNT) as total
			FROM accountbook.titles as T, accountbook.journal as J
			WHERE J.titles_ID = T.titles_id and
			J.jounal_DATE <= '2014-10-31'
			group by T.in_out_flg;";
$st = $pdo->query($str);
while ($row = $st->fetch()) {
	$row_flg_inout =  htmlspecialchars($row['in_out_flg']);
	$row_total = htmlspecialchars($row['total']);

	if($row_flg_inout == 0){
		$last_total_in = $row_total;
	}else{
		$last_total_out = $row_total;
	}
}
echo "<tbody>";
echo "<tr><td>&nbsp;</td><td>11/01</td>";
echo "<td>前月繰越</td><td>&nbsp;</td>";
echo "<td class=\"valueRight\">".
		number_format($last_total_in).
		"</td><td class=\"valueRight\">".
		number_format($last_total_out).
		"</td></tr>";

// 各行の生成
$str = "select jounal_ID, date_format(jounal_DATE,'%m/%d') as jounal_DATE, 
			titles_name, jounal_ABST, jounal_AMOUNT, in_out_flg
			from accountbook.titles as T, accountbook.journal as J
			where J.titles_ID = T.titles_id and
			J.jounal_DATE between '2014-11-01' and '2014-11-30'
			order by jounal_DATE, jounal_ID asc;";
$st = $pdo->query($str);
while ($row = $st->fetch()) {
	$row_id = htmlspecialchars($row['jounal_ID']);
	$row_flg_inout = htmlspecialchars($row['in_out_flg']);
	$row_title = htmlspecialchars($row['titles_name']);
	$row_amount = htmlspecialchars ( $row ['jounal_AMOUNT'] );
	$row_abst = htmlspecialchars ( $row ['jounal_ABST']);
	$row_date = htmlspecialchars ( $row ['jounal_DATE']);
	
	// チェックボックス,日付,科目,摘要,入金,出金
	echo "<tr>";
	echo "<td class=\"valueCenter\"><input type=\"radio\" name=\"isSelect\" value=\"$row_id\"";
	if($selectID == $row_id ) {echo 'checked="checked" ';}
	echo "></td>";
	echo "<td>$row_date</td>";
	echo "<td class=\"kamoku\">$row_title</td>";
	echo "<td class=\"tekiyou\">$row_abst</td>";
	if($row_flg_inout == 0){ // 入金時
		echo "<td class=\"valueRight\">".number_format($row_amount)."</td>";
		echo "<td>&nbsp;</td>";
	}else{ // 出金
		echo "<td>&nbsp;</td>";
		echo "<td class=\"valueRight\">".number_format($row_amount)."</td>";
	}
	echo "</tr>\n";
}
echo "</tbody><tfoot>\n";

// 合計表示
$str = "SELECT T.in_out_flg, sum(J.jounal_AMOUNT) as total
				FROM accountbook.titles as T, accountbook.journal as J
				WHERE J.titles_ID = T.titles_id and
				J.jounal_DATE between '2014-11-01' and '2014-11-30'
				group by T.in_out_flg;";
$st = $pdo->query($str);
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
echo "<td class=\"valueRight\">".
		number_format($row_total_in).
		"</td><td class=\"valueRight\">".
		number_format($row_total_out).
		"</td></tr>";

echo "</tfoot>";

?>
</table>
</div>
<footer>
<?php 
echo "ID=".$_POST['isSelect'];
?>
<p>作成: 147-D8690 美代 苑生</p>
</footer>
</body>
</html>