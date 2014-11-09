<!DOCTYPE html>
<html lang="ja">
<head>
<meta charset="UTF-8">
<title>Test Page2</title>
</head>
<body>

<form name="form1" method="get" action="test2.php" onsubmit="gettext(this)">

<label><input type="radio" name="switch" value="0" onChange="selectIO(this)" checked="checked"> 入金</label><br>
<label><input type="radio" name="switch" value="1" onChange="selectIO(this)"> 出金 </label>

<select name="kamoku_val" class="kamoku">
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
	echo "<option value=\"$flg_id\">$title</option>";
}
?>
</select>


<input type="submit" value="Go"></input> 

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
	targetObj = form1.elements['kamoku_val'];
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



</body>
</html>