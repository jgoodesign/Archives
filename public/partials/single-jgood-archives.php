<?php

// default archive page for single archive page

get_header();

?>

<section id="primary" class="content-area">
	<div id="main" class="site-main" role="main">
		<header class="page-header">
			<h1 class="page-title"><?php the_title(); ?></h1>
		</header><!-- .page-header -->
		<?php
		// grab archives through shortcode
		echo do_shortcode( '[jgoodarchives]' );
		?>
	</div><!-- .site-main -->
</section><!-- .content-area -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
