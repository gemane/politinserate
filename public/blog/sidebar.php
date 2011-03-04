
<?php include('wp-blog-header.php'); ?>
<div class="sidebar">
<ul>
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(1) ) : endif;?>
</ul>
</div>