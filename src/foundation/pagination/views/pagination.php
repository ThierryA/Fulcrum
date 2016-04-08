<nav class="navigation pagination" role="navigation">
	<h2 class="screen-reader-text"><?php _e( 'Posts navigation', 'fulcrum' ); ?></h2>
	<div class="nav-links">
		<?php if ( $prev_uri ): ?>
		<a clas="prev page-numbers" href="<?php echo esc_url( $prev_uri ); ?>"><?php _e( 'Previous', 'fulcrum' ); ?></a>
		<?php endif; ?>

		<?php for ( $page_number = 1; $page_number <= $this->total_pages; $page_number ++ ) : ?>
		<a class="page-numbers<?php echo $this->get_page_class( $page_number ); ?>" href="<?php echo esc_url( $this->get_page_uri( $page_number ) ); ?>">
			<span class="screen-reader-text">Page </span><?php echo (int) $page_number; ?>
		</a>
		<?php endfor;

		if ( $next_uri ) : ?>
			<a class="next page-numbers" href="<?php echo esc_url( $next_uri ); ?>"><?php _e( 'Next', 'fulcrum' ); ?></a>
		<?php endif; ?>
	</div>
</nav>
