<?php
/**
 * Team Members List Block Template.
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

$class_name = 'team-members-list-block';
$team_members = get_field('team_members');
$columns = get_field('columns');

if ( ! empty( $columns ) )
    $class_name .= ' team-members-list-block-columns--' . $columns;

if ( ! empty( $block['className'] ) )
    $class_name .= ' ' . $block['className'];

if ( ! empty( $block['align'] ) )
    $class_name .= ' align' . $block['align'];

?>
<div <?php echo empty( $block['anchor'] ) ? '' : 'id="'.esc_attr( $block['anchor'] ).'"' ?> class="<?php echo esc_attr( $class_name ) ?>">

    <?php if ( empty($team_members) ) {

        esc_html_e('Please select team members to output', 'seclgroup-team-members');

    } else {

        foreach ( $team_members as $team_member ) {
            do_action( 'team_member_template', array(
                'id' => $team_member->ID,
                'title' => $team_member->post_title,
                //'content' => $team_member->post_content,
                'image' => get_field('image', $team_member),
                'position' => boolval( get_field('display_positions') ) ? get_field('position', $team_member) : '',
                'phone' => get_field('phone', $team_member),
                'email' => get_field('email', $team_member)
            ) );
        }
    } ?>

</div><!-- .team-members-list-block -->
