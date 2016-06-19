<?php
/**
 * The default post content template
 */
?>

<article id="post-<?php the_ID(); ?>" class="<?php echo implode(' ', get_post_class( $jgood_archives_args['post_class'] ) ); ?>">
	<?php if($jgood_archives_args['item_title'] == "true"){ ?>
		<header class="entry-header">
			<?php
				if ( is_single() ) :
					the_title( '<h1 class="entry-title">', '</h1>' );
				else :
					the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' );
				endif;
			?>
		</header><!-- .entry-header -->
	<?php }	?>

	<div class="entry-content">
		<p class="post-meta">
			<?php 
			if($jgood_archives_args['meta_author'] == "true"){ 
				?>by <?php the_author_link();	
			}
			if($jgood_archives_args['meta_date'] == "true"){ 
				if($jgood_archives_args['meta_author'] == "true"){
					?> | <?php
				}
				the_time($jgood_archives_args['meta_date_format']);
			}
			if($jgood_archives_args['meta_category'] == "true"){ 
				if($jgood_archives_args['meta_date'] == "true" || $jgood_archives_args['meta_author'] == "true"){
					?> | <?php
				}
				the_category( ' ' ); 
			}
			?>
		</p>
		<?php 
			if($jgood_archives_args['excerpt'] == "true"){
				$content = get_the_content();
				$contentDisplay = wp_trim_words( $content, $jgood_archives_args['excerpt_length'] );
				echo $contentDisplay;
			}else if($jgood_archives_args['full_content'] == "true"){
				echo get_the_content();
			}
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php edit_post_link( __( 'Edit', 'jgood_archives' ), '<span class="edit-link">', '</span>' ); ?>
	</footer><!-- .entry-footer -->

</article><!-- #post-## -->
