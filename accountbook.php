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
<table border="1">
<tr>
<td rowspan="2">
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
</form></td>
<th>日付</th>
<th>科目</th>
<th>適用</th>
<th>金額</th>
<td rowspan="2">
<button type="submit" >入力</button><br>
<button type="submit" >削除</button></td>
</tr>
<tr>
<td><input type="date" name="date" 
min="<?php echo $thisYear."-".$thisMonth."-01" ?>" 
max="<?php echo $thisYear."-".$thisMonth."-".date("t", mktime(0,0,0,$thisMonth,1,$thisYear));?>">
</td>
<td><select name="inout">
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

</select></td>
<td><input type="text" name="abstract"></td>
<td><input type="number" name="amount"></td>
</tr>
</table>
</div>

<div role="main">
<table border="1">
<thead><tr>
<th><button type="submit" >修正</button></th>
<th>日付</th>
<th>科目</th>
<th>摘要</th>
<th>入金</th>
<th>出金</th>
</tr></thead>
<tbody>
<tr>
<td></td>
<td><time datetime="2014-11-01">11/01</time></td>
<td>前月繰越</td>
<td></td>
<td>10</td>
<td>0</td>
</tr>
<tr>
<td><input type="radio" name="selectRow" value="1"></td>
<td><time datetime="2014-11-03">11/03</time></td>
<td>現金</td>
<td>AA商店に支払</td>
<td>500</td>
<td></td>
</tr>
</tbody>
<tfoot><tr>
<td colspan="4"></td>
<td>510</td>
<td>0</td>
</tr></tfoot>
</table>
</div>
<footer>
<?php 
echo $thisMonth;
?>
<p>作成: 147-D8690 美代 苑生</p>
</footer>
</body>
</html>