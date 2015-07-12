<?php

/* sintassi 
 * 

	save minimo/services/storage/?op=save&storage=storage&usermail=usermail&session=session
	upload 
	url
	rm
 
*/ 
function xmd5($key){
	return md5(strrev(md5($key)));
	}


$service="http://verticaldev.altervista.org/minimo/services/auth/index.php";
$usermail=$_GET['usermail'];
$session=$_GET['session'];
$scope=$_GET['scope'];
$response=file($service."?op=verify&scope=".$scope."&usermail=".$usermail."&session=".$session);

if($response==true){
	$basepath=substr( $_SERVER['PHP_SELF'], 0, strpos( $_SERVER['PHP_SELF'], "index.php" ));
	$storage=@$_GET['storage'];
	$storagedir=xmd5($storage);
	if(file_exists($storagedir)==false)mkdir($storagedir,0775);

	$op=@$_GET['op'];

	if ($op=='save'){
		echo "
		<form action='$basepath/index.php?op=upload&storage=$storage' method='post' enctype='multipart/form-data'>
		<input name='file' type='file' size='30%'>
		<input type='submit' >
		</form>
		
		";
	}

	if ($op=='upload'){
		if (move_uploaded_file($_FILES['file']['tmp_name'], $storagedir ."/". $_FILES['file']['name'])) {
			chmod( $storagedir ."/". $_FILES['file']['name'],0775);
			rename($storagedir ."/". $_FILES['file']['name'],$storagedir ."/". xmd5($_FILES['file']['name']));
		}
	}

	if ($op=='url'){
		$filename=xmd5($_GET['filename']);
		if(file_exists($storagedir."/".$filename)){
			echo "http://".$_SERVER['HTTP_HOST'].$basepath.$storagedir."/".$filename;
		}	
	}
}
?>
