<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes() ?>>
<head profile="http://gmpg.org/xfn/11">
	<title><?php sdstheme_title() ?></title>
	<meta http-equiv="content-type" content="<?php bloginfo('html_type') ?>; charset=<?php bloginfo('charset') ?>" />
<?php sdstheme_meta() ?>
        <link href="http://politinserate.at/favicon.ico" rel="icon" type="image/x-icon" />
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url') ?>" />
	<link href="http://blog.politinserate.at/wp-content/themes/seo-basics/inserate.css" type="text/css" rel="stylesheet" />
<?php wp_head() // For plugins ?>
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('rss2_url') ?>" title="<?php printf( __( '%s latest posts', 'sandbox' ), wp_specialchars( get_bloginfo('name'), 1 ) ) ?>" />
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('comments_rss2_url') ?>" title="<?php printf( __( '%s latest comments', 'sandbox' ), wp_specialchars( get_bloginfo('name'), 1 ) ) ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url') ?>" />
	<script type="text/javascript" src="<?php bloginfo('template_directory') ?>/sfhover.js"></script>
	<script language="javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', 'UA-18459406-1']);
        _gaq.push(['_trackPageview']);
        
        (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();
    </script>
</head>

<body class="<?php sandbox_body_class() ?>" style="margin:0px;">
<a href="http://github.com/gemane/politinserate"><img style="position: absolute; top: 0; left: 0; border: 0;" src="https://assets1.github.com/img/c641758e06304bc53ae7f633269018169e7e5851?repo=&url=http%3A%2F%2Fs3.amazonaws.com%2Fgithub%2Fribbons%2Fforkme_left_white_ffffff.png&path=" alt="Fork me on GitHub"></a>
<div class="layout_position layout_size" style="padding:0px;margin-bottom:15px;font-size:15px;">
<div class="links">&nbsp;</div>

<!--<div class="login"><a href="/login">Login</a> | <a href="/user/register">Registrieren</a></div>-->
</div>
<div id="wrapper" class="hfeed">

  <div class="layout_position layout_size layout_design" style="background-color: #FCFF98;border:0px solid black;padding-bottom:10px;margin-bottom:0px;background: url(http://politinserate.at/images/frame_yellow.png) 0 0 repeat-x;">
    <h1 id="header" style="padding-bottom:9px;"><a href="http://politinserate.at/"><img src="http://blog.politinserate.at/wp-content/themes/seo-basics/logo.png" width="217" height="52" alt="Logo:Polit-Inserate.at, Wir zahlen, sie werben." style="border:none;" /></a></h1>

    <ul id="main_navigation" style="position:absolute; right:1em;">
	<li><a href="http://politinserate.at/eingabe/foto">Neues Inserat</a></li>
	<li><a href="http://politinserate.at/stream/tagged" onmouseover="showit(0)" onmouseout="resetit(event)">Inserate</a></li>
	<li><a href="http://politinserate.at/statistiken/parteien" onmouseover="showit(1)" onmouseout="resetit(event)">Statistiken</a></li>
	<li><a href="http://politinserate.at/tarife">Anzeigentarife</a></li>
	<li><a href="http://blog.politinserate.at" style="text-decoration: underline;">Blog</a></li>
	<li><a href="http://politinserate.at/faq">FAQ</a></li>
    </ul>
  <div id="describe" onmouseover="clear_delayhide()" onmouseout="resetit(event)"></div>
</div><!--  #header -->

<?php 
$file = realpath('/kunden/213870_4020/inserate/data/cache/expenses.txt');
$file_contents = file_get_contents($file);
$values = unserialize($file_contents);
$expense = $values['expense'];
?>
<div class="layout_position layout_size layout_design" style="background-color:#646567;border-width: 2px 0px; border-style: solid; border-color: black;padding:5px 21px;margin-bottom:0px;width:998px;background: url(http://politinserate.at/images/frame_grey.png) 0 0 repeat-x;">
  <span style="color:white;text-align:center;">Gesamtausgaben für Inseratschaltungen im System: € <?php echo number_format($expense, 0, ',', '.') ?> (seit Mitte 2010)</span>
</div><!-- #access -->
