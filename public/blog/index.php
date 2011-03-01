<?php

    // Define path to application directory
    /*defined('APPLICATION_PATH')
        || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));*/
    
    define('APPLICATION_PATH', '/kunden/213870_4020/inserate_test/application');
    
    // Ensure library/ is on include_path
    set_include_path(implode(PATH_SEPARATOR, array(
        realpath(APPLICATION_PATH . '/../library'),
        get_include_path(),
    )));
    
    setlocale(LC_MONETARY, 'de_AT');
    
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Polit-Inserate.at - Blog</title>

    <meta name="robots" content="index, follow" />
    <meta name="description" content="Polit-Inserate: Wir zahlen, sie werben." lang="de" />
    <meta name="keywords" content="Politik Regierung Partei Inserate Werbung Steuergeld" lang="de" />
    <link href="/css/inserate.css" media="screen" rel="stylesheet" type="text/css" />
    <link href="/css/style.css" media="screen" rel="stylesheet" type="text/css" />

    <link href="/favicon.ico" rel="icon" type="image/x-icon" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" /> 

    <script language="javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js" type="text/javascript"></script>
</head>
<body class="body tundra">
<a href="http://github.com/gemane/politinserate"><img style="position: absolute; top: 0; left: 0; border: 0;" src="https://assets1.github.com/img/c641758e06304bc53ae7f633269018169e7e5851?repo=&url=http%3A%2F%2Fs3.amazonaws.com%2Fgithub%2Fribbons%2Fforkme_left_white_ffffff.png&path=" alt="Fork me on GitHub"></a>
<div class="layout_position layout_size" style="padding:0px;margin-bottom:15px;font-size:15px;">
<div class="links"><a href="/links">Partner</a> | <a href="/blog">Blog</a> | <a href="/faq">Hilfe</a></div>

<!--<div class="login"><a href="/login">Login</a> | <a href="/user/register">Registrieren</a></div>-->
</div>
<div class="layout_position layout_size layout_design" style="background-color: #FCFF98;border:1px solid black;padding-bottom:10px;margin-bottom:0px;">
<h1 id="header"><a href="/"><img src="/images/logo.png" width="217" height="52" alt="Logo:Polit-Inserate.at, Wir zahlen, sie werben." style="border:none;" /></a></h1>

<ul id="navigation" style="position:absolute; right:1em;">
    <li><a href="/eingabe/foto">Neues Inserat</a></li>
    <li><a href="/stream/tagged" onmouseover="showit(0)" onmouseout="resetit(event)">Inserate</a></li>
    <li><a href="/statistiken/parteien" onmouseover="showit(1)" onmouseout="resetit(event)">Statistiken</a></li>
    <li><a href="/tarife">Anzeigentarife</a></li>
</ul>
<div id="describe" onmouseover="clear_delayhide()" onmouseout="resetit(event)"></div>
</div>
<?php 
$expenses_file = realpath(APPLICATION_PATH . '/../data/cache/expenses.txt');
$expense = file_get_contents($expenses_file);
?>
<div class="layout_position layout_size layout_design" style="background-color:#646567;border:1px solid black;padding-top:5px;padding-bottom:5px;margin-bottom:0px;">
<span style="color:white;text-align:center;">Gesamtausgaben für Inseratschaltungen im System: € <?php echo number_format($expense, 2, ',', '.') ?> (seit Mitte 2010)</span>
</div>

<div class="layout_position layout_size layout_design" >
<div style="width:780px;float:left;">

<!--  -->
<?php include('wp-blog-header.php'); ?>

    <!--<div id="content" class="narrowcolumn" role="main">-->
<?php query_posts('showposts = 3'); ?>

<div style="text-align:left; font-size:90%; width:90%; margin-left:5%;">

 <!-- Start the Loop. -->
 <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

 <!-- The following tests if the current post is in category 3. -->
 <!-- If it is, the div box is given the CSS class "post-cat-three". -->
 <!-- Otherwise, the div box will be given the CSS class "post". -->
 <?php if ( in_category('3') ) { ?>
           <div class="post-cat-three">
 <?php } else { ?>
           <div class="post">
 <?php } ?>

 <!-- Display the Title as a link to the Post's permalink. -->
 <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>

 <!-- Display the date (November 16th, 2009 format) and a link to other posts by this posts author. -->
 <small><?php the_time('F jS, Y') ?> by <?php the_author_posts_link() ?></small>

 <!-- Display the Post's Content in a div box. -->
 <div class="entry">
   <?php the_content(); ?>
 </div>

 <!-- Display a comma separated list of the Post's Categories. -->
 <p class="postmetadata">Posted in <?php the_category(', '); ?></p>
 </div> <!-- closes the first div box -->

 <!-- Stop The Loop (but note the "else:" - see next line). -->
 <?php endwhile; else: ?>

 <!-- The very first "if" tested to see if there were any Posts to -->
 <!-- display.  This "else" part tells what do if there weren't any. -->
 <p>Sorry, no posts matched your criteria.</p>

 <!-- REALLY stop The Loop. -->
 <?php endif; ?>
</div>

    <!--</div>-->

<!--  -->

</div>

<!-- Sidebar start -->
<div style="width:210px;float:right; border-left: 1px dashed #191919;">

<div style="font-size: 70%; clear:both; ">
<div style="margin-left: 40px; width: 50px; float: left;line-height:100%; border:none;"><a style="text-decoration:none;" href="http://blog.politinserate.at/feed/" title="RSS-Abo der Blogbeiträge"><img style="border:none;" src="/images/rssfeedicon.jpg" height="35" width="35" alt="RSS-Abo starten" />&nbsp;RSS</a></div>

<div style="margin-left: 15px; width: 50px; float: left; line-height:100%;"><a style="text-decoration:none;" href="http://twitter.com/politinserate" title="@politinserate auf Twitter folgen"><img style="margin-left:3px; border:none;" src="/images/twitter.jpg" height="35" width="35" alt="@politinserate auf Twitter folgen" />Twitter</a></div>
</div>

<div id="sidebar">
<ul>
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar() ) : endif;?>
</ul>
</div>

<div style="padding: 10px 10px 10px 10px; clear:left; ">
<script src="http://widgets.twimg.com/j/2/widget.js"></script>
<script type="text/javascript">
new TWTR.Widget({
  version: 2,
  type: 'profile',
  rpp: 5,
  interval: 6000,
  width: 185,
  height: 250,
  theme: {
    shell: {
      background: '#F58E0E',
      color: '#ffffff'
    },
    tweets: {
      background: '#FCFF98',
      color: '#444444',
      links: '#852806'
    }
  },
  features: {
    scrollbar: true,
    loop: false,
    live: true,
    hashtags: true,
    timestamp: true,
    avatars: true,
    toptweets: true,
    behavior: 'all'
  }
}).render().setUser('politinserate').start();
</script>
</div>

<div><script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script><fb:like-box href="https://www.facebook.com/pages/Polit-Inserateat/141426775920579" width="185" show_faces="true" stream="false" header="true"></fb:like-box></div>

</div>
<div style="clear:both;"></div>
<!-- Sidebar end -->

</div>
<div id="footer">
    <span style="">
        © <?php echo date('Y', time()) ?> Gerold Neuwirt | <a href="/datenschutz">Datenschutz</a> | <a href="/agb">AGBs</a> | <a href="/impressum">Impressum</a> | <a href="/kontakt">Kontakt</a>
    </span>
</div>
<script type="text/javascript" src="/js/intern/tabmousover.js"></script>

<!--
https://politinserate.uservoice.com/admin/dashboard
-->
<script type="text/javascript">
var uservoiceOptions = {
  /* required */
  key: 'politinserate',
  host: 'politinserate.uservoice.com', 
  forum: '102641',
  showTab: true,  
  /* optional */
  alignment: 'left',
  background_color:'#f00', 
  text_color: 'white',
  hover_color: '#06C',
  lang: 'en'
};

function _loadUserVoice() {
  var s = document.createElement('script');
  s.setAttribute('type', 'text/javascript');
  s.setAttribute('src', ("https:" == document.location.protocol ? "https://" : "http://") + "cdn.uservoice.com/javascripts/widgets/tab.js");
  document.getElementsByTagName('head')[0].appendChild(s);
}
_loadSuper = window.onload;
window.onload = (typeof window.onload != 'function') ? _loadUserVoice : function() { _loadSuper(); _loadUserVoice(); };
</script>

</body>
</html>
