	<div id="primary" class="sidebar">
		<ul class="xoxo">
			
			<!-- Icons -->
			<li style="font-size: 70%;padding-top:20px;">
			    <div style="width: 50px; float: left;line-height:100%;margin-left: 40px; text-align: center;"><a style="text-decoration:none;" href="http://blog.politinserate.at/feed/" title="RSS-Abo der Blogbeiträge"><img style="border:none; padding-bottom:3px;" src="http://politinserate.at/images/rssfeedicon.jpg" height="35" width="35" alt="RSS-Abo starten" />&nbsp;RSS</a></div>

			    <div style="margin-left: 15px; width: 50px; float: left; line-height:100%; text-align: center;"><a style="text-decoration:none;" href="http://twitter.com/politinserate" title="@politinserate auf Twitter folgen"><img style="margin-left:3px; border:none; padding-bottom:3px;" src="http://politinserate.at/images/twitter.jpg" height="35" width="35" alt="@politinserate auf Twitter folgen" />Twitter</a></div>
			</li>

			<?php 
			  $file = realpath('/kunden/213870_4020/inserate/data/cache/expenses.txt');
			  $file_contents = file_get_contents($file);
			  $values = unserialize($file_contents);
			?>
			<!-- Count Uploads -->
			<li style="">
			  <div style="padding: 9px 0px 0px 10px;clear:both;">
			      <div style="padding: 10px; text-align:left;background-color:#FCFF98;border:1px solid black;color:#852806; -moz-border-radius: 7px; -webkit-border-radius: 7px; border-radius:7px; background: url(http://politinserate.at/images/frame_yellow.png) 0 0 repeat-x;">
				  <dl style="margin:0px;">
				      <dt>Neue Inserate</dt><dd style="text-align:right;"><?php echo $values['num_tagged_month'] ?></dd>
				      <dt>und Zahlungen</dt><dd style="text-align:right;">€ <?php echo number_format($values['expense_month'], 0, ',', '.') ?></dd>
				  </dl>
				  im <?php echo $values['last_month'] . ' ' . $values['last_year'] ?>.
			      </div>
			  </div>
			</li>

<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(1) ) : // begin primary sidebar widgets ?>

			<li id="ads">
				<?php sdstheme_sidebar_ads() ?>
			</li>

			<li id="categories">
				<div class="title"><?php _e( 'Categories', 'sandbox' ) ?></div>
				<ul>
<?php wp_list_categories('title_li=&show_count=0&hierarchical=1') ?> 

				</ul>
			</li>

			<li id="archives">
				<div class="title"><?php _e( 'Archives', 'sandbox' ) ?></div>
				<ul>
<?php wp_get_archives('type=monthly') ?>

				</ul>
			</li>
<?php endif; // end primary sidebar widgets  ?>
		</ul>
	</div><!-- #primary .sidebar -->

	<div id="secondary" class="sidebar" >
		<ul class="xoxo">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar(2) ) : // begin secondary sidebar widgets ?>

			<!-- Twitter -->
			<li style="font-size: 70%;">
			  <div style="padding: 10px 0px 0px 10px; clear:left;">
			  <script type="text/javascript" src="http://widgets.twimg.com/j/2/widget.js"></script>
			  <script type="text/javascript">
			  new TWTR.Widget({
			    version: 2,
			    type: 'profile',
			    rpp: 5,
			    interval: 6000,
			    width: 200,
			    height: 250,
			    theme: {
			      shell: {
				background: '#F58E0E',
				color: '#ffffff'
			      },
			      tweets: {
				background: '#FFFFFF',
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
			</li>

<?php wp_list_bookmarks('title_before=<div class="title">&title_after=</div>&show_images=1') ?>

			<li id="rss-links">
				<div class="title"><?php _e( 'RSS Feeds', 'sandbox' ) ?></div>
				<ul>
					<li><a href="<?php bloginfo('rss2_url') ?>" title="<?php printf( __( '%s latest posts', 'sandbox' ), wp_specialchars( get_bloginfo('name'), 1 ) ) ?>" rel="alternate" type="application/rss+xml"><?php _e( 'All posts', 'sandbox' ) ?></a></li>
					<li><a href="<?php bloginfo('comments_rss2_url') ?>" title="<?php printf( __( '%s latest comments', 'sandbox' ), wp_specialchars( get_bloginfo('name'), 1 ) ) ?>" rel="alternate" type="application/rss+xml"><?php _e( 'All comments', 'sandbox' ) ?></a></li>
				</ul>
			</li>

			<li id="meta">
				<div class="title"><?php _e( 'Meta', 'sandbox' ) ?></div>
				<ul>
					<?php wp_register() ?>

					<li><?php wp_loginout() ?></li>
					<?php wp_meta() ?>

				</ul>
			</li>
<?php endif; // end secondary sidebar widgets  ?>
		</ul>
	</div><!-- #secondary .sidebar -->
