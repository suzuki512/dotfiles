<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>掲示板</title>
		<link rel="stylesheet" type="text/css" href="style.css">
	</head>
	<body>
	
<?php
	session_start();
	require_once("./database.php");
	//ページ　タイプ設定
	$page = empty($_GET["page"])? 1:$_GET["page"]; 
	$next = $page+1;//前のページ番号
	$prev = $page-1;//次のページ番号
	$type = empty($_GET["type"])? 50:$_GET["type"]; 
 //1件目　　
			$sql = sprintf("SELECT user_id FROM board WHERE id ='1'");
			$query = mysql_query($sql,$link);
			$row = mysql_fetch_object($query);
			$firstid=$row->user_id;
			
			$sql = sprintf("SELECT name FROM user WHERE id =%d",$firstid);
			$query = mysql_query($sql,$link);
			$row = mysql_fetch_object($query);
		    $firstname=$row->name;
					
			$sql = sprintf("SELECT date,body FROM board WHERE id ='1'");
			$query = mysql_query($sql,$link);
			$row = mysql_fetch_object($query);
			$firstbody=$row->body;
			$firstdate=$row->date;
//１件目終了
	

//boardテーブルのデータ数を数える
	$sql = sprintf("SELECT COUNT(*) as NUM FROM board");
	$query = mysql_query($sql,$link);
	$row = mysql_fetch_assoc($query);
	$number=$row['NUM'];

switch($type){//最新50件
		case 50:
			
			$limit = ($number-50)/100+1;
			$index=$number-(50+100*($page-1));
			
				if($index>=0){
					$sql = sprintf("SELECT*FROM board ORDER BY id ASC LIMIT {$index},100");
					$query = mysql_query($sql,$link);
				}
				if($index<0){//100件ないとき
					$index2=(100+$index);
					$sql = sprintf("SELECT*FROM board ORDER BY id ASC LIMIT 1,{$index2}");
					$query = mysql_query($sql,$link);
				}			
						break;
	
		case 100:	//100件ずつ							
				$limit = $number/100;
				$index=($page-1)*100;//1が被るのを防ぐ
				if($page==1){ 
					$index2=$index+1;
					$sql = sprintf("SELECT*FROM board ORDER BY id ASC LIMIT {$index2},99");
					$query = mysql_query($sql,$link);
				}
				else{
						$sql = sprintf("SELECT*FROM board ORDER BY id ASC LIMIT {$index},100");
						$query = mysql_query($sql,$link);
				}
						
						break;
		case 1://全件表示
				$sql = sprintf("SELECT*FROM board ORDER BY id ASC LIMIT 1,$number");
				$query =mysql_query($sql,$link);
					break;
}	

				while($row = mysql_fetch_object($query)){		
						
						$id[]=$row->id;
						$name[]=$row->user_id;
						$user_ids[]=$row->user_id;//ソート用
						$date[]=$row->date;
						$body[]=$row->body;
				}
			
	$user_ids=array_unique($user_ids); //user_idsのダブりを削る
	sort($user_ids,SORT_NUMERIC);//ソート
	$com='\',\'';
 	$imp=implode($user_ids,$com);//結合
	
	$sql = sprintf("SELECT id,name FROM user WHERE id IN ('$imp')");//$within[id]=>name
	$query = mysql_query($sql,$link);
		while($row=mysql_fetch_object($query)){
			$userid=$row->id;
			$within[$userid]=$row->name;
		}

	foreach($name as $key =>$val){//$username[]=>名前
		$username[$key]=$within[$val];
	}
?>
<div class="clear"><!--ヘッダー-->
<br><a href="?page=1&type=1">全部</a>
	<a href="?page=1&type=100">1-</a>
    <a href="?page=1&type=50">最新50</a></br>
 
<div class="part">
<div class="id"><?php echo "1:名前";?></div>
<div class="name"><?php echo $firstname;?></div>
<div class="date"><?php echo $firstdate;?></div>
<div class="body"><?php 
					$photo='<img src="http://localhost/img001.gif"width=50 height=50>';
					$search=array("\n",'img');
					$replace=array("<br>","$photo");
					$res = str_replace($search,$replace,$firstbody);
					echo  $res; ?></div>
</div>

<?php $i=0; $count=count($id);  while($i<$count):?>
<div class="part">
<div class="id"><?php echo $id[$i].":名前";?></div>
<div class="name"><?php echo $username[$i];?></div>
<div class="date"><?php echo $date[$i];?></div>
<div class="body"><?php  
					$search=array("\n",'img');
					$replace=array("<br>","$photo");
					$res = str_replace($search,$replace,$body[$i]);
					echo  $res; 
					$i++;
				   ?></div>
</div>
<?php endwhile;?>
<div class="clear">
		<form action="http://localhost/insert.php"method="post">
		<?php echo $_SESSION['name']."さん";?><br/>
		<textarea name="body" id="body" rows="10" cols="40"></textarea>
		<input type ="submit" value ="書き込む"/>
</div>
</form>
<div class="clear">
 <?php 	
	switch($type){
		case 50:
			if($page<$limit) {//最後のページ以外で「前の100件」を表示
			        print'<br><a href="?page='.$next.'&type=50"> &laquo;前の100件</a>';
		    }
			if($page!=1){//最初のページ以外で「後の100件」を表示
		        	print' <a href="?page='.$prev.'&type=50">後の100件&raquo;</a>';
		    
			}
					break;
			
		case 100:
			if($page!=1){//最初のページ以外で「前の100件」を表示
		        	print'<a href="?page='.$prev.'&type=100">&laquo;前の100件</a>';
		   
			}	
			if($page<$limit) {//最後のページ以外で「後の100件」を表示
			        print' <a href="?page='.$next.'&type=100">後の100件&raquo;</a>';
		    }
					break;
}
?>
<div class="clear">
	<br><a href="?page=1&type=1">全部</a>
	    <a href="?page=1&type=100">1-</a>
        <a href="?page=1&type=50">最新50</a></br>		
<a href= "index.php">ログアウト</a>
</div>
</body>
</html>