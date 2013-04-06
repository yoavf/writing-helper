<?php
	global $post;
	$on_a_page = FALSE;

	if ( isset( $_GET['post_type'] ) && 'page' == $_GET['post_type'] ) // new page
		$on_a_page = TRUE;
	else if ( 'page' == $post->post_type ) // editing a page
		$on_a_page = TRUE;
?>

<ul id="helpers"<?php if ( isset( $_GET['cap'] ) || isset( $_GET['requestfeedback'] ) ) echo ' style="display:none"'; ?>>
	<li class="copyapost">
		<div class="iconbox">
			<img src="/wp-content/mu-plugins/writing-helper/i/pencilhelper.png" alt="" />
		</div>

		<div class="helper-text">
			<a href="#copyapost"><?php ( $on_a_page ) ? _e( 'Copy a Page' ) : _e( 'Copy a Post' ); ?></a>
			<h4><?php ( $on_a_page ) ? _e( 'Copy a Page' ) : _e( 'Copy a Post' ); ?></h4>
			<p><?php ( $on_a_page ) ? _e( 'Use an existing page as a template.' ) : _e( 'Use an existing post as a template.' ); ?></p>
		</div>
		<div class="clear"></div>
	</li>
<?php if ( $show_feedback_button ): ?>
	<li class="requestfeedback">
		<div class="border-box">
			<div class="iconbox">
				<img src="/wp-content/mu-plugins/writing-helper/i/requestfeedback.png" alt="" />
			</div>

			<div class="helper-text">
				<a href="#requestfeedback"><?php _e( 'Request Feedback' ) ?></a>
				<h4><?php _e( 'Request Feedback' ) ?></h4>
				<p><?php _e( 'Get feedback on this draft before publishing.' ); ?></p>
			</div>
		</div>
	</li>
<?php endif; ?>
</ul>

<div id="requestfeedback" class="helper"<?php
	if ( isset( $_GET['requestfeedback'] ) )
		echo ' style="display:block"';
?>>
<div class="helper-header">
	<a href="" class="back"><?php _e( 'Back' ) ?><span></span></a>
	<h5><?php _e( 'Request Feedback' ) ?></h5>
</div>

<div class="inside helper-content">

<div id="invitetoshare">
	<p><strong><?php _e( 'Get feedback on this draft before publishing.' ); ?></strong></p>

	<p class="invitetext"><label for="invitelist"><?php _e( 'Enter email addresses of people you would like to get feedback from:' ) ?></label></p>
	<textarea id="invitelist" rows="2" placeholder="bob@example.org, sarah@example.org"></textarea>

	<input type="submit" id="add-request" value="Send Requests" class="button-secondary" />

	&nbsp; <a class="customize" href=""><?php _e( 'Customize the message' ) ?></a>
	<div id="modify-email" style="display: none;">
	<textarea class="customize" cols="80" rows="8">
<?php // Note: Keep in one string for easier i18n.
printf(
	__( "Hi,\n\nI started writing a new draft titled \"%s\" and would love to get your feedback. I plan on publishing it shortly.\n\nPlease leave your feedback here:\n%s\n\nTitle: %s\nBeginning: %s\n\nRead more: %s\n\nThanks,\n%s" ),
	'[title]',
	'[feedback-link]',
	'[title]',
	'[excerpt]',
	'[feedback-link]',
	$current_user->display_name
); ?>
	</textarea><br />
	<input type="submit" id="add-request-custom" value="<?php echo esc_attr( __( 'Send Requests' ) ); ?>" class="button-secondary" /> &nbsp;<a class="cancel" href="#"><?php _e( 'Cancel' ); ?></a>
	</div>

	<div id="df-share-link-p">
		<div id="df-getting-link" style="display:none"><img src="/wp-content/mu-plugins/writing-helper/i/ajax-loader.gif" alt="Loading" /> <?php _e( 'Getting a link...' ) ?></div>
		<a id="df-share-link" href="javascript:DraftGetLink(jQuery,<?php the_ID() ?>)"><?php _e( 'Get a share link without sending an email.' ) ?></a>
	</div>
</div>

<div id="add-request-sent" class="add-request-message" style="display:none">
<h4><?php _e( "Requests sent." ); ?></h4>
<p><?php _e( "When your friends read your draft and give feedback, you'll get an email and the feedback will appear below." ); ?></p>
<p><a href="#sendmore"><?php _e( "Send more requests." );?></a></p>
</div>
<?php
if ( $show_feedback_button && is_array( $requests ) && !empty( $requests ) ):
?>
  <table cellspacing="0" cellpadding="0" id="requests-list">
<?php if ( 1 == $screen_layout_columns ): ?>
    <thead>
      <tr>
	  <th class="name"><?php _e( 'Email' ); ?></th>
	  <th class="feedback"><?php _e( 'Feedback' ); ?></th>
      </tr>
    </thead>
<?php endif; ?>
    <tbody>
<?php
	$i = 0;

	foreach( $requests as $email => $data ):
		$feedbacks = $df->get_user_feedbacks( $post_id, $email );
		$requested_on = $df->time_to_date( $data['time'] );
		$secret_url = $df->generate_secret_link( $post_id, $data['key'] );
		$revoke_display = $data['revoked']? 'none' : 'inline';
		$unrevoke_display = $data['revoked']? 'inline' : 'none';
?>
	<tr>
		<td>
			<?php if ( is_email( $email ) ) : ?>
			<?php echo get_avatar( $email, 24 ); ?>
    		<?php echo esc_html( $email ); ?>
			<?php endif; ?>
			<div class="links">
				<?php echo esc_html( $requested_on  ); ?> |
				<a href="<?php echo esc_attr( $secret_url );  ?>" title="<?php esc_attr__( 'The secret link this person received in order to see and give feedback on your draft' ); ?>" target="_blank"><?php _e( 'Link' ); ?></a> |
				<a href="javascript:DraftRevokeAccess(jQuery, <?php echo esc_js( $post_id ); ?>, '<?php echo esc_js( $email ) ?>', '#revoke-<?php echo $i ?>')" id="revoke-<?php echo $i++; ?>">
					<span class="revoke" style="display: <?php echo $revoke_display; ?>"><?php _e( 'Revoke Access' ); ?></span>
					<span class="unrevoke" style="display: <?php echo $unrevoke_display; ?>"><?php _e( 'Give Back Access' ); ?></span>
				</a>
			</div>
<?php	if ( 1 == $screen_layout_columns ): ?>
		</td>
		<td>
<?php	endif;

		if ( is_array( $feedbacks ) && !empty( $feedbacks ) ):
			$multi_items_css = ( count( $feedbacks ) > 1 ) ? 'multiple' : '';
?>
			<ol class="feedbacks-list <?php echo esc_attr( $multi_items_css ) ?>">
<?php
				foreach ( $feedbacks as $feedback ):
					$feedback_content = wpautop( esc_html( $feedback['content'] ) );
					$feedback_content_truncated = ( mb_strlen( $feedback_content ) > 70 ) ? wp_html_excerpt( $feedback_content, 70 ) . '&hellip;' : $feedback_content;
					$feedback_date_info = esc_attr( sprintf( __( 'Submitted on %s' ), $df->time_to_date( $feedback['time'] ) ) );
?>
					<li>
						<?php if ( $feedback_content_truncated != $feedback_content ) : ?>
							<a href="#" title="<?php echo esc_attr( $feedback_date_info ); ?>" class="truncated">[+] <?php echo $feedback_content_truncated; ?></a>
							<div title="<?php echo esc_attr( $feedback_date_info ); ?>" class="full"><?php echo $feedback_content; ?><a href="#"><?php _e( '[-] Collapse' ); ?></a></div>
						<?php else: ?>
							<?php echo $feedback_content; ?>
						<?php endif; ?>
					</li>
<?php
				endforeach;
?>
			</ol>
<?php
		endif;
?>
      </td>
    </tr>
<?php
	endforeach;
?>
	</tbody>
  </table>
<?php
endif;
?>
</div>
</div>

<div id="copyapost" class="helper"<?php if ( isset( $_GET['cap'] ) ) echo ' style="display:block"'; ?>>
	<div class="helper-header" id="cap">
		<a href="" class="back"><?php _e( 'Back' ) ?><span></span></a>
		<h5><?php ( $on_a_page ) ? _e( 'Copy a Page' ) : _e( 'Copy a Post' ); ?></h5>
	</div>

	<div class="inside helper-content">
		<p>
			<strong><?php ( $on_a_page ) ? _e( "Use an existing page as a template." ) : _e( "Use an existing post as a template." ); ?></strong>
			<?php
				if ( $on_a_page ) :
					_e( "Pick a page and we'll copy the title and content. Recent pages are listed below. Search by title to find older pages. You can mark any page to keep it at the top." );
				else :
					_e( "Pick a post and we'll copy the title, content, tags and categories. Recent posts are listed below. Search by title to find older posts. You can mark any post to keep it at the top." );
				endif;
			?>
		</p>

		<div class="search-posts">
			<?php $search_text = ( $on_a_page ) ? __( 'Search for pages by title' ) : __( 'Search for posts by title' ); ?>
			<input type="search" name="search" id="search-posts" value="<?php echo $search_text ?>" onfocus="if ( this.value == '<?php echo $search_text ?>' ) this.value = '';" onblur="if ( this.value == '' ) this.value = '<?php echo $search_text ?>';" />
		</div>

		<div class="confirm-copy" style="display: none;">
			<p class="confirm"><?php ( $on_a_page ) ? _e( 'Replace the current page with the selected page?' ) : _e( 'Replace the current post with the selected post?' ); ?> &nbsp;<input type="button" class="button-secondary" value="<?php _e( 'Cancel' ) ?>" id="cancel-copy" /> <input type="button" class="button-primary" value="<?php _e( 'Confirm Copy' ) ?>" id="confirm-copy" /></p>
			<p class="copying"><img src="/wp-content/mu-plugins/writing-helper/i/ajax-loader.gif" alt="Loading" />  <?php ( $on_a_page ) ? _e( 'Copying Page...' ) : _e( 'Copying Post...' ); ?></p>
		</div>

		<div class="copy-posts">
			<?php $tmp_post = $post; ?>
			<?php $sticky_posts = get_option( 'copy_a_post_sticky_posts' ); ?>
			<?php if ( !empty( $sticky_posts ) ) : ?>
			<ul id="s-posts">
				<?php
					$stickies_args = array(
										'posts_per_page'		=> 3,
										'ignore_sticky_posts'	=> 1,
										'post__in'				=> (array) $sticky_posts,
									);

					if ( $on_a_page )
						$stickies_args['post_type'] = 'page';

					$stickies = new WP_Query( $stickies_args );
				?>
				<?php while( $stickies->have_posts() ) : $stickies->the_post(); ?>
					<li>
						<input type="button" value="<?php _e( 'Copy' ) ?>" class="button-secondary" id="cp-<?php the_ID() ?>" /> &nbsp;
						<span class="title"><?php the_title() ?></span>
						<span class="excerpt"><?php echo strip_tags( get_the_excerpt() ) ?></span>
					</li>
				<?php endwhile; ?>
			</ul>
			<?php endif; ?>

			<ul id="l-posts">
				<?php
					$latest_posts_args = array(
											'posts_per_page'	=> 20,
											'posts__not_in'		=> (array) $sticky_posts,
											'post_status'		=> 'any',
										);

					if ( $on_a_page )
						$latest_posts_args['post_type'] = 'page';

					$latest_posts = new WP_Query( $latest_posts_args );
				?>
				<?php while( $latest_posts->have_posts() ) : $latest_posts->the_post(); ?>
					<li>
						<input type="button" value="<?php _e( 'Copy' ) ?>" class="button-secondary" id="cp-<?php the_ID() ?>" /> &nbsp;
						<span class="title"><?php the_title() ?></span>
						<span class="excerpt"><?php echo strip_tags( get_the_excerpt() ) ?></span>
					</li>
				<?php endwhile; ?>
				<?php wp_reset_query(); $post = $tmp_post; ?>
			</ul>
			<div class="loading"><img src="/wp-content/mu-plugins/writing-helper/i/ajax-loader.gif" alt="Loading" /> <?php _e( 'Searching...' ) ?></div>
		</div>

	</div>

	<span class="explain" style="display: none;"><?php _e( 'Stick to Top' ) ?></span>

	<?php if ( isset( $_GET['cap'] ) ) : ?>
		<script type="text/javascript">
			jQuery( function() {
				jQuery( 'li#menu-posts li, li#menu-posts li a' ).removeClass( 'current' );
				jQuery( 'li#menu-posts li a[href="post-new.php?cap#cap"]' ).addClass('current').parent('li').addClass('current');
				jQuery.post( ajaxurl, { 'action': 'helper_record_stat', 'stat': 'menu_click' } );
			});
		</script>
	<?php endif; ?>
</div>

<div class="clear"></div>