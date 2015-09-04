<?php
/**
 * Created by PhpStorm.
 * User: lusinda
 * Date: 8/27/15
 * Time: 7:05 PM
 */
if ( ! defined( 'ABSPATH' ) ) exit;



function ecwd_admin_notices( $notices ) {

	$two_week_review_ignore = add_query_arg( array( 'ecwd_admin_notice_ignore' => 'two_week_review' ) );
	$two_week_review_temp = add_query_arg( array( 'ecwd_admin_notice_temp_ignore' => 'two_week_review', 'int' => 14 ) );
	$notices['two_week_review'] = array(
		'title' => __( 'Leave A Review?', 'ecwd' ),
		'msg' => __( 'We hope you\'ve enjoyed using Event Calendar WD! Would you consider leaving us a review on WordPress.org?', 'ecwd' ),
		'link' => '<li><span class="dashicons dashicons-external"></span><a href="http://wordpress.org/support/view/plugin-reviews/event-calendar-wd?filter=5" target="_blank">' . __( 'Sure! I\'d love to!', 'ecwd' ) . '</a></li>
					<li> <span class="dashicons dashicons-smiley"></span><a href="' . $two_week_review_ignore . '"> ' . __( 'I\'ve already left a review', 'ecwd' ) . '</a></li>
                    <li><span class="dashicons dashicons-calendar-alt"></span><a href="' . $two_week_review_temp . '">' . __( 'Maybe Later' ,'ecwd' ) . '</a></li>
                     <li><span class="dashicons dashicons-dismiss"></span><a href="' . $two_week_review_ignore . '">' . __( 'Never show again' ,'ecwd' ) . '</a></li>',

		'int' => 14
	);


	return $notices;
}

add_filter( 'ecwd_admin_notices', 'ecwd_admin_notices' );

