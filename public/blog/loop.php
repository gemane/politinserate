<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<link href="/css/inserate.css" media="screen" rel="stylesheet" type="text/css" />
</head>
<body class="body" style="background-color: #FFF;">
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
</body>
</html>