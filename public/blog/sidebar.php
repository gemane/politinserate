<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="/css/inserate.css" media="screen" rel="stylesheet" type="text/css" />
</head>
<body class="body" style="background-color: #FFF;">
<?php include('wp-blog-header.php'); ?>
<div id="sidebar">
<ul>
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : endif;?>
</ul>
</div>
</body>
</html>