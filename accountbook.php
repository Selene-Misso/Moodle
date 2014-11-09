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
</head>
<body>


<header>
<h1>出納帳</h1>
<form action="accountbook.php" method="GET">
<input type="month" name="selectMonth" tabindex="1" size="5" value=
<?php 
	if (isset($_GET['selectMonth'])) {
		$num1 = explode("-", $_GET['selectMonth']);
		$thisYear = $num1[0];
		$thisMonth= $num1[1];
	}else{
		$thisYear  = date('Y');
		$thisMonth = date('m');
	}
	echo '"',$thisYear, "-", $thisMonth, '"';
?>
 required >
<button type="submit" tabindex="2">移動</button>
</form>
</header>

<div role="application">
<form name="addform" action="add.php" method="POST">
<table border="1" class="tbl">
<tr>
<td rowspan="2">
<label><input type="radio" name="switch" tabindex="3" value="0" onChange="selectIO(this)" checked="checked">
 入金</label><br>
<label><input type="radio" name="switch" tabindex="4" value="1" onChange="selectIO(this)">
 出金</label>
</td>
<th>日付</th>
<th>科目</th>
<th>摘要</th>
<th>金額</th>
<td rowspan="2">
<button type="submit" name="add" tabindex="9">追加</button><br>
<button type="submit" name="del" disabled tabindex="10">削除</button></td>
</tr>
<tr>
<td><input type="date" name="kamoku_date" required="required" tabindex="5"
min="<?php echo $thisYear."-".$thisMonth."-01" ?>" 
max="<?php echo $thisYear."-".$thisMonth."-".date("t", mktime(0,0,0,$thisMonth,1,$thisYear));?>">
</td>
<td><select name="kamoku_val" class="kamoku" tabindex="6">
<?php
// DB接続
$pdo = new PDO("mysql:dbname=accountbook", "mishiro", "314159");
// 文字コード設定
$pdo->query("SET NAMES utf8");

$sql = 'SELECT * FROM titles WHERE in_out_flg = 0;';
$sth = $pdo->query($sql);
while ($row = $sth->fetch()) {
	$flg_id = $row['titles_id'];
	$title = htmlspecialchars($row['titles_name']);
	echo "<option value=\"$flg_id\">$title</option>\n";
}
?>
</select></td>
<td><input type="text" name="abstract" class="tekiyou" required="required" tabindex="7"></td>
<td><input type="number" name="amount" min="1" class="valueRight" required="required" tabindex="8"></td>
</tr>
</table>
</form>
<script type="text/javascript">
<!--
/* 
 * 選択肢の配列の宣言
 */
// 表示文字列
var titles = new Array();
// 送信する値
var values = new Array();

// 挿入する値はPHPから生成
<?php 
for($in_out = 0; $in_out <= 1; $in_out++){
	$sql = 'SELECT * FROM titles WHERE in_out_flg = :io';
	$sth = $pdo->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
	$sth->execute(array(':io' => $in_out));
	
	$titlesArray="";
	$valuesArray="";
	while ($row = $sth->fetch()) {
		$flg_id = $row['titles_id'];
		$title = htmlspecialchars($row['titles_name']);
		$valuesArray = $valuesArray.$flg_id.",";
		$titlesArray = $titlesArray.'"'.$title.'"'.",";
	}
	echo "titles['$in_out'] = new Array(".substr($titlesArray,0,-1).");\n";
	echo "values['$in_out'] = new Array(".substr($valuesArray,0,-1).");\n";
}
?>

/* 
 * 入金出金を切り替える
 */
function selectIO(obj){
	// <Select>を動的生成
	createSelect(values[obj.value],titles[obj.value]);
}

/*
 * <select>の生成
 * @valueList OptionのValue値リスト
 * @textList  OptionのText値リスト
 */
function createSelect(valueList, textList){
	targetObj = addform.elements['kamoku_val'];
	targetObj.length = 0;
	
	for( var i=0; i < valueList.length; i++){
		creatOptions( targetObj, valueList[i], textList[i]);
	}
}

/*
 * <option>の生成
 * @targetObj 上位の<select>オブジェクト
 * @val valueの指定値
 * @str 表示文字列
 */
function creatOptions( targetObj, val, str ){
	targetObj.length++;
	targetObj.options[targetObj.length-1].value = val;
	targetObj.options[targetObj.length-1].text  = str;
}

//-->
</script>
</div>

<div role='main'>
<form action="mod.php" method="POST">
<table border="1" class="tbl">
<thead><tr>
<th><button type="submit" name="select_row" tabindex="12">選択</button></th>
<th>日付</th>
<th>科目</th>
<th>摘要</th>
<th>入金</th>
<th>出金</th>
</tr></thead>
<?php


// 前月繰越の表示
$str = "SELECT T.in_out_flg, sum(J.jounal_AMOUNT) as total
			FROM accountbook.titles as T, accountbook.journal as J
			WHERE J.titles_ID = T.titles_id and
			J.jounal_DATE < '$thisYear-$thisMonth-01'
			group by T.in_out_flg;";
$st = $pdo->query($str);
$last_total_in = 0;
$last_total_out = 0;
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
echo "<tr><td>&nbsp;</td><td class=\"valueCenter\">$thisMonth/01</td>";
echo "<td class=\"kamoku\">前月繰越</td><td class=\"tekiyou\">&nbsp;</td>";
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
			J.jounal_DATE between '$thisYear-$thisMonth-01' and '$thisYear-$thisMonth-".date("t", mktime(0,0,0,$thisMonth,1,$thisYear))."'
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
	echo "<td class=\"valueCenter\"><input type=\"radio\" name=\"isSelect\" value=\"$row_id\" required=\"required\"></td>";
	echo "<td class=\"valueCenter\">$row_date</td>";
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
				J.jounal_DATE between '$thisYear-$thisMonth-01' and '$thisYear-$thisMonth-".date("t", mktime(0,0,0,$thisMonth,1,$thisYear))."'
				group by T.in_out_flg;";
$st = $pdo->query($str);
$row_total_in = $last_total_in;
$row_total_out= $last_total_out;
while ($row = $st->fetch()) {
	$row_flg_inout = $row['in_out_flg'];
	$row_total = $row['total'];
	
	if($row_flg_inout == 0){
		$row_total_in += $row_total;
	}else{
		$row_total_out += $row_total;
	}
}
echo "<tr><td class=\"valueCenter\">
		<button type=\"submit\"
		formaction=\"accountbook.php?selectMonth=".$thisYear."-".$thisMonth."\">新規</button>
		</td>";
echo "<td colspan=3>&nbsp;</td>";
echo "<td class=\"valueRight\">".
		number_format($row_total_in).
		"</td><td class=\"valueRight\">".
		number_format($row_total_out).
		"</td></tr>";

echo "</tfoot>";

?>
</table>
</form>
</div>
<footer>
<p>作成: 147-D8690 美代 苑生</p>
</footer>
</body>
</html>