<?php
/**
 * Registering meta boxes
 *
 * All the definitions of meta boxes are listed below with comments.
 * Please read them CAREFULLY.
 *
 * You also should read the changelog to know what has been changed before updating.
 *
 * For more information, please visit:
 * @link http://www.deluxeblogtips.com/meta-box/
 */


add_filter( 'rwmb_meta_boxes', 'tx_register_meta_boxes' );

/**
 * Register meta boxes
 *
 * @return void
 */
function tx_register_meta_boxes( $meta_boxes )
{
	/**
	 * Prefix of meta keys (optional)
	 * Use underscore (_) at the beginning to make keys hidden
	 * Alt.: You also can make prefix empty to disable it
	 */
	// Better has an underscore as last sign
	$prefix = 'tx_';
	
	// 1st meta box
	$meta_boxes[] = array(
		// Meta box id, UNIQUE per meta box. Optional since 4.1.5
		'id' => 'itrans-slider',

		// Meta box title - Will appear at the drag and drop handle bar. Required.
		'title' => __( 'itrans Slide Meta', 'ispirit' ),

		// Post types, accept custom post types as well - DEFAULT is array('post'). Optional.
		'pages' => array( 'itrans-slider' ),

		// Where the meta box appear: normal (default), advanced, side. Optional.
		'context' => 'normal',

		// Order of meta box: high (default), low. Optional.
		'priority' => 'high',

		// Auto save: true, false (default). Optional.
		'autosave' => true,

		// List of meta fields
		'fields' => array(

			// name
			array(
				'name'  => __( 'Slide Button Text', 'nx-admin' ),
				'id'    => "{$prefix}slide_link_text",
				'type'  => 'text',
				'std'   => __( '', 'nx-admin' ),
				'desc' => __('Enter the slide link button text.', 'nx-admin'),
			),

			// designation
			array(
				'name'  => __( 'Slide Link URL', 'nx-admin' ),
				'id'    => "{$prefix}slide_link_url",
				'type'  => 'text',
				'std'   => __( '', 'nx-admin' ),
				'desc' => __('Enter the slide link url', 'nx-admin'),
			),
					
		)
	);	
	
	$meta_boxes[] = array(
		// Meta box id, UNIQUE per meta box. Optional since 4.1.5
		'id' => 'teammember',

		// Meta box title - Will appear at the drag and drop handle bar. Required.
		'title' => __( 'Team Member Details', 'ispirit' ),

		// Post types, accept custom post types as well - DEFAULT is array('post'). Optional.
		'pages' => array( 'team' ),

		// Where the meta box appear: normal (default), advanced, side. Optional.
		'context' => 'normal',

		// Order of meta box: high (default), low. Optional.
		'priority' => 'high',

		// Auto save: true, false (default). Optional.
		'autosave' => true,

		// List of meta fields
		'fields' => array(

			// Designation
			array(
				'name'  => __( 'Position/Designation', 'nx-admin' ),
				'id'    => "{$prefix}designation",
				'type'  => 'text',
				'std'   => __( '', 'nx-admin' ),
				'desc' => __('Enter the team member\'s position within the team.', 'nx-admin'),
			),

			// Email
			array(
				'name'  => __( 'Email Address', 'nx-admin' ),
				'id'    => "{$prefix}team_email",
				'type'  => 'text',
				'std'   => __( '', 'nx-admin' ),
				'desc' => __('Enter the team member\'s Email Id.', 'nx-admin'),
			),
			// Phone
			array(
				'name'  => __( 'Phone Number', 'nx-admin' ),
				'id'    => "{$prefix}team_phone",
				'type'  => 'text',
				'std'   => __( '', 'nx-admin' ),
				'desc' => __('Enter the team member\'s Phone Number.', 'nx-admin'),
			),
			// Twitter
			array(
				'name'  => __( 'Twitter', 'nx-admin' ),
				'id'    => "{$prefix}team_twitter",
				'type'  => 'text',
				'std'   => __( '', 'nx-admin' ),
				'desc' => __('Enter the team member\'s Twitter URL.', 'nx-admin'),
			),
			
			// Facebook
			array(
				'name'  => __( 'Facebook', 'nx-admin' ),
				'id'    => "{$prefix}team_facebook",
				'type'  => 'text',
				'std'   => __( '', 'nx-admin' ),
				'desc' => __('Enter the team member\'s Facebook URL.', 'nx-admin'),
			),
			// Google+
			array(
				'name'  => __( 'Google+', 'nx-admin' ),
				'id'    => "{$prefix}team_gplus",
				'type'  => 'text',
				'std'   => __( '', 'nx-admin' ),
				'desc' => __('Enter the team member\'s Google+ URL.', 'nx-admin'),
			),
			// Skype
			array(
				'name'  => __( 'Skype', 'nx-admin' ),
				'id'    => "{$prefix}team_skype",
				'type'  => 'text',
				'std'   => __( '', 'nx-admin' ),
				'desc' => __('Enter the team member\'s Skype user name.', 'nx-admin'),
			),
			// Skype
			array(
				'name'  => __( 'Linkedin', 'nx-admin' ),
				'id'    => "{$prefix}team_linkedin",
				'type'  => 'text',
				'std'   => __( '', 'nx-admin' ),
				'desc' => __('Enter the team member\'s Linkedin URL.', 'nx-admin'),
			),									
						
		)
	);	
	
	
	$meta_boxes[] = array(
		// Meta box id, UNIQUE per meta box. Optional since 4.1.5
		'id' => 'testimonialmeta',

		// Meta box title - Will appear at the drag and drop handle bar. Required.
		'title' => __( 'Testimonial Meta', 'ispirit' ),

		// Post types, accept custom post types as well - DEFAULT is array('post'). Optional.
		'pages' => array( 'testimonials' ),

		// Where the meta box appear: normal (default), advanced, side. Optional.
		'context' => 'normal',

		// Order of meta box: high (default), low. Optional.
		'priority' => 'high',

		// Auto save: true, false (default). Optional.
		'autosave' => true,

		// List of meta fields
		'fields' => array(

			// name
			array(
				'name'  => __( 'Testimonial Cite', 'nx-admin' ),
				'id'    => "{$prefix}testi_name",
				'type'  => 'text',
				'std'   => __( '', 'nx-admin' ),
				'desc' => __('Enter the testimonial cite name.', 'nx-admin'),
			),

			// designation
			array(
				'name'  => __( 'Testimonial Cite Designation/position', 'nx-admin' ),
				'id'    => "{$prefix}testi_desig",
				'type'  => 'text',
				'std'   => __( '', 'nx-admin' ),
				'desc' => __('Enter the cite designation or position', 'nx-admin'),
			),
			// company name
			array(
				'name'  => __( 'Company name', 'nx-admin' ),
				'id'    => "{$prefix}testi_company",
				'type'  => 'text',
				'std'   => __( '', 'nx-admin' ),
				'desc' => __('Enter the cite company name', 'nx-admin'),
			),

		
						
		)
	);		
	
	
	
	
	return $meta_boxes;
}



