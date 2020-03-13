<?php
//var_dump($_POST);
//最初に変数を定義しておかないとエラーになる
$err_msg1 = "";
$err_msg2 = "";
$message = "";
//isset関数は存在チェック　NULLまたはデータなしの場合にfalse
$name = (isset($_POST["name"]) == true) ?$_POST["name"]: "";
//trim 文字列の先頭および末尾にあるホワイトスペースを取り除く
$comment = (isset($_POST["comment"]) == true) ? trim($_POST["comment"]) : "";
//投稿がある場合のみ処理を行う
if(isset($_POST["send"]) == true ){
  if($name == ""){
    $err_msg1 = "ニックネームを入力してください";
  }
  if($comment == ""){
  $err_msg2 ="コメントを入力してください";
  }
  if($err_msg1 == "" && $err_msg2 == ""){
    //fopen:ファイルやURLを開く関数
    //a:書き出し用のみでオープンします。ファイルポインタをファイルの終端に置きます。 ファイルが存在しない場合には、作成を試みます。
    $fp = fopen("data.txt", "a");
    //ファイルを共有ロック
    if(flock($fp, LOCK_SH)){
      //fwrite：ファイルからデータを書き込む関数
      //int fwrite(resource handle, string string)
      //handle  対象となるファイルのハンドル
      //string  書き込む文字列
      fwrite($fp, $name."\t".$comment."\n");
      // \t：タブ, \n：改行
      $message = "書き込みに成功しました。";
    }else{
      echo "ロック失敗";
    }
    fclose($fp);
  }
}
//読み込みのみでオープンします。ファイルポインタをファイルの先頭に置きます。
$fp = fopen("data.txt", "r");

$dataArr = array();
//fgets：ファイルポインタの位置から1行ずつ読み取る関数
//string fgets ( resource $handle [, int $length ] )
//$lengthを指定しない場合、は改行かEOFまで読み込む
$count = -1;
while($res = fgets($fp)){
  //$dataArr配列のインデックスをカウント
  //explode(第一引数,第二引数、第三引数)：文字列を分割する関数
  //第一引数	区切り文字（例としてスペースやカンマなど）
  //第二引数	入力文字
  $tmp = explode("\t",$res);
  if(isset($tmp[1])){
    $arr = array(
      "name"=>$tmp[0],
      "comment"=>$tmp[1],
    );
    $dataArr[] = $arr;
    $count += 1;
    $beforeName = $arr["name"];
    $beforeComment = $arr["comment"];
  }else{
    $dataArr[$count]["comment"] .= $tmp[0];
  };

}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<link rel="stylesheet" type="text/css" href="css/style.css" />
<html lang="ja">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <title>掲示板</title>
  </head>
  <body>
    <?php echo $message; ?>
    <form method="post" action="">
<br />
      <span>ニックネーム：　　　　　　　　　　<input class="text" type="text" name="name" value= "<?php echo $name; ?>" placeholder="例：ごんちゃん" maxlength="10"</span>
      <span class="err_msg"><?php echo $err_msg1; ?></span><br />
<br />
      エンジニアで良かったと思う事は？：<textarea class="text" name="comment" rows="4" cols="40" placeholder="例：１０時出社だから、早起きが苦手でも大丈夫" maxlength="100"><?php echo $comment; ?></textarea>
      <span class="err_msg"><?php echo $err_msg2; ?></span><br />
<br />
            <input class="btn" type="submit" name="send" value="投稿"  />
    </form>
    <dl>
      <?php foreach($dataArr as $data):?>
      <p><span><?php echo htmlspecialchars($data["name"]); ?></span>：<span><?php echo htmlspecialchars($data["comment"])  ; ?></span></p>
    <?php endforeach;?>
    </dl>
  </body>
</html>
