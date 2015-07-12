<?php
 $page=@$_GET['page'];
 if ($page=="")$page="main.php";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//IT" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
	<title>MiNiMo</title>
	<meta name="robots" content="" >
	<meta name="generator" content="" >
	<meta name="keywords" content="minimo php" >
	<meta name="description" content="" >
	<meta name="MSSmartTagsPreventParsing" content="true" >
	<meta http-equiv="distribution" content="global" >
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" >
	<meta http-equiv="Resource-Type" content="document" >
	<link rel="stylesheet" type="text/css" href="theme.css" media="all" />	
</head>
<body id='body'>
	<!-- layout --------------------------------------------------------------->
	<div id='div-title' ><?php include "pages/title.php"; ?></div>
	<div id='div-sidebar'><?php include "pages/sidebar.php"; ?></div>
	<div id='div-textpage'><?php include "pages/$page"; ?></div>
	<div id='div-footer'><?php include "pages/footer.php"; ?></div>
	<!------------------------------------------------------------------------->
</body>
</html>
