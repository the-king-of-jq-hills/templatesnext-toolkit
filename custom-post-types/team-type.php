<?php

/* ************************************************ */
/*	Team Post Type Functions  */
/* ************************************************ */	    
	    
	
	add_action('init', 'tx_team_register');  
	  
	function tx_team_register() {  
	
	    $labels = array(
	        'name' => _x('Team', 'post type general name', "nx-admin"),
	        'singular_name' => _x('Team Member', 'post type singular name', "nx-admin"),
	        'add_new' => _x('Add New', 'team member', "nx-admin"),
	        'add_new_item' => __('Add New Team Member', "nx-admin"),
	        'edit_item' => __('Edit Team Member', "nx-admin"),
	        'new_item' => __('New Team Member', "nx-admin"),
	        'view_item' => __('View Team Member', "nx-admin"),
	        'search_items' => __('Search Team Members', "nx-admin"),
	        'not_found' =>  __('No team members have been added yet', "nx-admin"),
	        'not_found_in_trash' => __('Nothing found in Trash', "nx-admin"),
	        'parent_item_colon' => ''
	    );
	
	    $args = array(  
	        'labels' => $labels,  
	        'public' => true,  
	        'show_ui' => true,
	        'show_in_menu' => true,
	        'show_in_nav_menus' => false,
	        'rewrite' => false,
	        'supports' => array('title', 'editor', 'thumbnail'),
	        'has_archive' => true,
			'menu_icon' => 'dashicons-groups',				
	        'taxonomies' => array('team-category')
	       );  
	  
	    register_post_type( 'team' , $args );
		
	}  
	
	function tx_create_team_taxonomy() {
		
		$atts = array(
			"label" 						=> _x('Team Categories', 'category label', "nx-admin"), 
			"singular_label" 				=> _x('Team Category', 'category singular label', "nx-admin"), 
			'public'                        => true,
			'hierarchical'                  => true,
			'show_ui'                       => true,
			'show_in_nav_menus'             => false,
			'args'                          => array( 'orderby' => 'term_order' ),
			'rewrite'                       => false,
			'query_var'                     => true
		);
		
		register_taxonomy( 'team-category', 'team', $atts );		
		
	}
	add_action( 'init', 'tx_create_team_taxonomy', 0 );		
	
	
	add_filter('manage_edit-team_columns', 'tx_team_edit_columns');   
	  
	function tx_team_edit_columns($columns){  
	        $columns = array(  
	            "cb" => "<input type=\"checkbox\" />",  
	            "thumbnail" => "",
	            "title" => __("Team Member", "nx-admin"),
	            "description" => __("Description", "nx-admin"),
	            "team-category" => __("Categories", "nx-admin")
	        );  
	  
	        return $columns;  
	}
	
	// Replace title placeholder	
	function tx_change_title_text( $title ){
		 $screen = get_current_screen();
	 
		 if  ( 'team' == $screen->post_type ) {
			  $title = 'Enter Team Members Name';
		 }
	 
		 return $title;
	}
	 
	add_filter( 'enter_title_here', 'tx_change_title_text' );	