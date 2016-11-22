<?php

//
// Выводит список наставников в профиле пользователя
//
//

?>

<div id="mentors-dir-list">

<?php if ( bp_get_current_member_type() ) : ?>
	<p class="current-member-type"><?php bp_current_member_type_message() ?></p>
<?php endif; ?>

<?php if ( bp_has_members( $members_param ) ) : ?>

	<div id="pag-top" class="pagination">

		<div class="pag-count" id="member-dir-count-top">

			<?php bp_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-dir-pag-top">

			<?php bp_members_pagination_links(); ?>

		</div>

	</div>

	<ul id="members-list" class="item-list" aria-live="assertive" aria-relevant="all">

	<?php while ( bp_members() ) : bp_the_member(); ?>

		<li <?php bp_member_class(); ?> id="mentors-dir-list-user-<?php bp_member_user_id(); ?>">
			<div class="item-avatar">
				<a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar(); ?></a>
			</div>

			<div class="item">
				<div class="item-title">
					<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>
				</div>

				<div class="item-meta"><span class="activity" data-livestamp="<?php bp_core_iso8601_date( bp_get_member_last_active( array( 'relative' => false ) ) ); ?>"><?php bp_member_last_active(); ?></span></div>

			</div>

			<div class="action">

				<?php do_action( 'mentorship_members_actions' ); ?>

			</div>

			<div class="clear"></div>
		</li>

	<?php endwhile; ?>

	</ul>

	<?php bp_member_hidden_fields(); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="member-dir-count-bottom">

			<?php bp_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-dir-pag-bottom">

			<?php bp_members_pagination_links(); ?>

		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( "Sorry, no members were found.", 'buddypress' ); ?></p>
	</div>

<?php endif; ?>

</div>
