<?php
/**
 * Team Member Card Template.
 *
 * @param WP_Post $team_member Post object.
 */

?>

<div class="team-member">

<?php if ( ! empty($image) ) : ?>

    <div class="team-member-image"><?php
        echo wp_get_attachment_image($image, 'large')
    ?></div>

<?php endif;

if ( ! empty($title) && ! empty($id) ) : ?>

    <div class="team-member-title"><?php
        echo apply_filters('the_title', $title, $id)
    ?></div>

<?php endif;

if ( ! empty($position) ) : ?>

    <div class="team-member-position"><?php
        echo $position
    ?></div>

<?php endif;

if ( ! empty($phone) ) : ?>

    <a href="tel:<?php esc_html_e($phone) ?>" target="_blank" rel="nofollow" class="team-member-phone"><?php
        esc_html_e($phone)
    ?></a>

<?php endif;

if ( ! empty($email) ) : ?>

    <a href="mailto:<?php esc_html_e($email) ?>" target="_blank" rel="nofollow" class="team-member-email"><?php
        esc_html_e($email)
    ?></a>

<?php endif;

if ( ! empty($content) && ! empty($id) ) : ?>

    <div class="team-member-content"><?php
        echo apply_filters('the_content', $content, $id)
    ?></div>

<?php endif ?>

</div><!-- .team-member -->
