<?php
/* sintassi auth.php?op=login&scope=prova&dest=index.html
 file di salvataggio dati= usermail,passwordmd5,session
 cookie=usermail,session
*/

// API gestione elenco utenti su file

function xmd5($key){
	return md5(strrev(md5($key)));
	}
//-------------------------------------------------------------------------------

function user_search($scope,$usermail){
	$nomefile=xmd5($scope);
	$utenti=@file("$nomefile");
	if($utenti){
		foreach($utenti as $utente){
			$datiutente=explode(",",$utente);
			if($datiutente[0]==$usermail)return true;
		}
	}
	return false;
}
//-------------------------------------------------------------------------------

function user_verifysession($scope,$usermail,$session){
	$nomefile=xmd5($scope);
	$utenti=@file("$nomefile");
	if($utenti){ 
		foreach($utenti as $utente){
			$datiutente=explode(",",$utente);
			if($datiutente[0]==$usermail && trim($datiutente[2])==trim($session))return true;
		}
	}
	return false;
}
//-------------------------------------------------------------------------------

function user_updatesession($scope,$usermail,$oldsession){
	$newsession="";
	$nomefile=xmd5($scope);
	$utenti=@file("$nomefile");
	$fp=fopen($nomefile,"wb");
	for($i=0;$i<count($utenti);$i++){
		$datiutente=explode(",",$utenti[$i]);
		if($datiutente[0]==$usermail && $datiutente[2]==$oldsession){
			$newsession=time()."-".rand(1000,9999);
			$datiutente[2]=$newsession;
			$utenti[$i]=implode(",",$datiutente);
		}
		if ($utenti[$i]!="\n")fwrite($fp,$utenti[$i]."\n");
	}
	fclose($fp);
	return $newsession;
}
//-------------------------------------------------------------------------------

function user_verifypassword($scope,$usermail,$password){
	$nomefile=xmd5($scope);
	$utenti=@file("$nomefile");
	if($utenti){
		foreach($utenti as $utente){
			$datiutente=explode(",",$utente);
			if($datiutente[0]==$usermail && $datiutente[1]==$password)return $datiutente[2];
		}
	}
	return false; 
	
}
//-------------------------------------------------------------------------------

function user_setpassword($scope,$usermail,$oldpassword,$newpassword){
	$nomefile=xmd5($scope);
	$utenti=@file("$nomefile");
	$fp=fopen($nomefile,"wb");
	for($i=0;$i<count($utenti);$i++){
		$datiutente=explode(",",$utenti[$i]);
		if($datiutente[0]==$usermail && $datiutente[1]==$oldpassword){
			$datiutente[1]=$newpassword;
			$utenti[$i]=implode(",",$datiutente);
		}
		if ($utenti[$i]!="")fwrite($fp,$utenti[$i]."\n");
	}
	fclose($fp);
}
//-------------------------------------------------------------------------------

function user_create($scope,$usermail,$password){
	if (user_search($scope,$usermail)==false){
		$nomefile=xmd5($scope);
		$session=time()."-".rand(1000,9999);
		$fp=fopen($nomefile,"a");
		fwrite($fp,"$usermail,$password,$session");
		fclose($fp);
	}
	return $session;
}
//-------------------------------------------------------------------------------

function user_delete($scope,$usermail,$password){
	$nomefile=xmd5($scope);
	$utenti=@file("$nomefile");
	$fp=fopen($nomefile,"wb");
	for($i=0;$i<count($utenti);$i++){
		$datiutente=explode(",",$utenti[$i]);
		if($datiutente[0]==$usermail && $datiutente[2]==$session){
			unset($utenti[$i]);
		}
		if ($utenti[$i]!="")fwrite($fp,$utenti[$i]."\n");
	}
	fclose($fp);
}
//-------------------------------------------------------------------------------
//-------------------------------------------------------------------------------


$op=@$_GET['op'];
$scope=@$_GET['scope'];
$dest=@$_GET['dest'];

//va usato con chiamata ajax
if ($op=='verify'){
	header('Access-Control-Allow-Origin: *');
	$cookie=@$_COOKIE['auth'];
	if ($cookie){
		$dati=explode("||",$cookie);
		$usermail=$dati[0];
		$session=$dati[1];
		echo "".user_verifysession($scope,$usermail,$session);
	}
	else{ 
		$usermail=$_GET['usermail'];
		$session=$_GET['session'];
		echo "".user_verifysession($scope,$usermail,$session);	
	}

}

if ($op=='login'){
	$cookie=@$_COOKIE['auth'];
	if ($cookie){
		$dati=explode("||",$cookie);
		$usermail=$dati[0];
		$session=$dati[1];
		$newsession=user_updatesession($scope,$usermail,$session);
		header("Location:$dest?usermail=$usermail&session=$newsession");
	}
	else{
		$usermail=@$_POST['usermail'];
		if ($usermail!=""){
			$password=xmd5($_POST['password']);
			if($session=user_verifypassword($scope,$usermail,$password)){
				$newsession=user_updatesession($scope,$usermail,$session);
				//setcookie("auth",$usermail."||".$newsession,time()+60*60*24*30,"/");
				header("Location:$dest?op=session&usermail=$usermail&session=$newsession");
			}
		}
		echo"
			<div style='position:absolute;
						top:50%;left:50%;
						border:1px solid #000000;
						padding:25px;
						width:400px;height:250px;
						margin-left:-200px;
						margin-top: -125px;'>
			<h2>Login</h2><hr><br>
			<form action='index.php?op=login&scope=$scope&dest=$dest' method='post'>
			usermail <input type='text' name='usermail'><br><br>
			password<input type='password' name='password'><br><br>
			<input type='submit' style='button blue' value='login'>
			<a href='index.php?op=registration&scope=$scope&dest=$dest'>registra</a>
			</form>
			</div>
			";					
	}		
}

if ($op=='logout'){
	//setcookie("auth",null, time()-3600,"/");	
	header("Location:$dest?op=logout");
}

if ($op=='create'){
	$usermail=$_POST['usermail'];
	$password=xmd5($_POST['password']);
	if (user_search($scope,$usermail)==false){
		$newsession=user_create($scope,$usermail,$password);
		//setcookie("auth",$usermail."||".$newsession,time()+60*60*24*30,"/");
		header("Location:$dest?op=session&usermail=$usermail&session=$newsession");
	}
	else{
		$op='registration';
	}
		
}

if ($op=='registration'){
	echo"
	<div style='position:absolute;
						top:50%;left:50%;
						border:1px solid #000000;
						padding:25px;
						width:450px;height:250px;
						margin-left:-225px;
						margin-top: -125px;'>
	<h2>registration</h2><hr><br>
	<form action='index.php?op=create&scope=$scope&dest=$dest' method='post'>
	usermail <input type='text' name='usermail'><br><br>
	password<input type='password' name='password'> retype<input type='password' name='repassword'><br><br>
	<input type='submit' value='register' onclick=\"if (password.value!=repassword.value){ alert('le 2 password non coincidono');return false;}return true;\" >
	</form></div>";
}
?>
