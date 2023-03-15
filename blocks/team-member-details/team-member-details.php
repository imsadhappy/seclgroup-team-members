<?php
/**
 * Team Member Details Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during backend preview render.
 * @param   int $post_id The post ID the block is rendering content against.
 *          This is either the post ID currently being displayed inside a query loop,
 *          or the post ID of the post hosting this block.
 * @param   array $context The context provided to the block by the post or it's parent block.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! function_exists('get_field') ) {
    return;
}

$class_name = 'team-member-details-block';
$team_member = get_field('team_member');

if ( ! empty( $block['className'] ) )
    $class_name .= ' ' . $block['className'];

if ( ! empty( $block['align'] ) )
    $class_name .= ' align' . $block['align'];

?>
<div <?php echo empty( $block['anchor'] ) ? '' : 'id="'.esc_attr( $block['anchor'] ).'"' ?> class="<?php echo esc_attr( $class_name ); ?>">

    <?php if ( empty($team_member) ) {

        esc_html_e('Please select team member to output', 'seclgroup-team-members');

    } else {

        do_action( 'team_member_template', array(
            'id' => $team_member->ID,
            'title' => $team_member->post_title,
            'content' => $team_member->post_content,
            'image' => get_field('image', $team_member),
            'position' => get_field('position', $team_member),
            'phone' => get_field('phone', $team_member),
            'email' => get_field('email', $team_member)
        ) );

        $reviewed_posts = get_posts(array(
            'numberposts'   => 5,
            'post_type'     => 'any',
            'fields'        => 'ids',
            'meta_key'      => 'reviewer',
            'meta_value'    => $team_member->ID
        ));

        if ( ! empty($reviewed_posts) ) {

            ?><div class="team-member-reviewed-posts"><?php

            esc_html_e('Reviewed posts:', 'seclgroup-team-members');

            foreach ( $reviewed_posts as $reviewed_post_id )
                printf( '<a href="%s" class="team-member-reviewed-post">%s</a>',
                        get_permalink($reviewed_post_id),
                        get_the_title($reviewed_post_id) );
            ?></div><?php
        }

    } ?>

</div><!-- .team-member-details-block -->
