<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
//wp_list_categories('orderby=name');
/**/
function tx_fix_shortcodes($content){   
    $array = array (
        '<p>[' => '[', 
        ']</p>' => ']', 
        ']<br />' => ']'
    );

    $content = strtr($content, $array);
    return $content;
}
add_filter('the_content', 'tx_fix_shortcodes');

if ( ! function_exists( 'tx_folio_term' ) ) {
	function tx_folio_term( $taxonomy ) {
		global $post;

		$folio_categories = array();
		$folio_cats = "";
		$tax_seperator = "";

		if ( is_object_in_taxonomy( $post->post_type, $taxonomy ) ) {
			foreach ( (array) wp_get_post_terms( $post->ID, $taxonomy ) as $term ) {
				if ( empty( $term->name ) )
					continue;
				$folio_cats .= $tax_seperator.$term->name;
				$tax_seperator = ', ';
			}
		}

		return $folio_cats;
	}
}

if ( ! function_exists( 'tx_shortcodes_comma_delim_to_array' ) ) {
	function tx_shortcodes_comma_delim_to_array( $string ) {
		$a = explode( ',', $string );

		foreach ( $a as $key => $value ) {
			$value = trim( $value );

			if ( empty( $value ) )
				unset( $a[ $key ] );
			else
				$a[ $key ] = $value;
		}

		if ( empty( $a ) )
			return '';
		else
			return $a;
	}
}


//add_image_size( $name, $width, $height, $crop );


function tx_get_category_list_key_array($category_name) {
			
	$get_category = get_categories( array( 'taxonomy' => $category_name	));
	$category_list = array( 'all' => 'Select Category');
		
	foreach( $get_category as $category ){
		if (isset($category->slug)) {
			$category_list[$category->slug] = $category->cat_name;
		}
	}
	return $category_list;
}

/************************ Category list ***************************/
/******************************************************************/

function tx_get_category_list_el($category_name) {
			
	$get_category = get_categories( array( 'taxonomy' => $category_name	));
	$category_list = array( 'all' => 'All Category');
		
	foreach( $get_category as $category ){
		if (isset($category->slug)) {
			$category_list[$category->slug] = $category->cat_name;
		}
	}
	return $category_list;
}

 /*
 *  @author Matthew Ruddy (http://easinglider.com)
 *  @return array   An array containing the resized image URL, width, height and file type.
 */
if ( isset( $wp_version ) && version_compare( $wp_version, '3.5' ) >= 0 ) {
	
	function tx_image_resize( $url, $width = NULL, $height = NULL, $crop = true, $retina = false ) {
		global $wpdb;
		if ( empty( $url ) )
			return new WP_Error( 'no_image_url', __( 'No image URL has been entered.','wta' ), $url );
		// Get default size from database
		$width = ( $width )  ? $width : get_option( 'thumbnail_size_w' );
		$height = ( $height ) ? $height : get_option( 'thumbnail_size_h' );
		  
		// Allow for different retina sizes
		$retina = $retina ? ( $retina === true ? 2 : $retina ) : 1;
		// Get the image file path
		$file_path = parse_url( $url );
		$file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path['path'];
		
		// Check for Multisite
		if ( is_multisite() ) {
			global $blog_id;
			$blog_details = get_blog_details( $blog_id );
			$file_path = str_replace( $blog_details->path . 'files/', '/wp-content/blogs.dir/'. $blog_id .'/files/', $file_path );
		}
		// Destination width and height variables
		$dest_width = $width * $retina;
		$dest_height = $height * $retina;
		// File name suffix (appended to original file name)
		$suffix = "{$dest_width}x{$dest_height}";
		// Some additional info about the image
		$info = pathinfo( $file_path );
		$dir = $info['dirname'];
		$ext = $info['extension'];
		$name = wp_basename( $file_path, ".$ext" );
	        if ( 'bmp' == $ext ) {
			return new WP_Error( 'bmp_mime_type', __( 'Image is BMP. Please use either JPG or PNG.','wta' ), $url );
		}
		// Suffix applied to filename
		$suffix = "{$dest_width}x{$dest_height}";
		// Get the destination file name
		$dest_file_name = "{$dir}/{$name}-{$suffix}.{$ext}";
		if ( !file_exists( $dest_file_name ) ) {
			
			/*
			 *  Bail if this image isn't in the Media Library.
			 *  We only want to resize Media Library images, so we can be sure they get deleted correctly when appropriate.
			 */
			$query = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE guid='%s'", $url );
			$get_attachment = $wpdb->get_results( $query );
			if ( !$get_attachment )
				return array( 'url' => $url, 'width' => $width, 'height' => $height );
			// Load Wordpress Image Editor
			$editor = wp_get_image_editor( $file_path );
			if ( is_wp_error( $editor ) )
				return array( 'url' => $url, 'width' => $width, 'height' => $height );
			// Get the original image size
			$size = $editor->get_size();
			$orig_width = $size['width'];
			$orig_height = $size['height'];
			$src_x = $src_y = 0;
			$src_w = $orig_width;
			$src_h = $orig_height;
			if ( $crop ) {
				$cmp_x = $orig_width / $dest_width;
				$cmp_y = $orig_height / $dest_height;
				// Calculate x or y coordinate, and width or height of source
				if ( $cmp_x > $cmp_y ) {
					$src_w = round( $orig_width / $cmp_x * $cmp_y );
					$src_x = round( ( $orig_width - ( $orig_width / $cmp_x * $cmp_y ) ) / 2 );
				}
				else if ( $cmp_y > $cmp_x ) {
					$src_h = round( $orig_height / $cmp_y * $cmp_x );
					$src_y = round( ( $orig_height - ( $orig_height / $cmp_y * $cmp_x ) ) / 2 );
				}
			}
			// Time to crop the image!
			$editor->crop( $src_x, $src_y, $src_w, $src_h, $dest_width, $dest_height );
			// Now let's save the image
			$saved = $editor->save( $dest_file_name );
			// Get resized image information
			$resized_url = str_replace( basename( $url ), basename( $saved['path'] ), $url );
			$resized_width = $saved['width'];
			$resized_height = $saved['height'];
			$resized_type = $saved['mime-type'];
			// Add the resized dimensions to original image metadata (so we can delete our resized images when the original image is delete from the Media Library)
			$metadata = wp_get_attachment_metadata( $get_attachment[0]->ID );
			if ( isset( $metadata['image_meta'] ) ) {
				$metadata['image_meta']['resized_images'][] = $resized_width .'x'. $resized_height;
				wp_update_attachment_metadata( $get_attachment[0]->ID, $metadata );
			}
			// Create the image array
			$image_array = array(
				'url' => $resized_url,
				'width' => $resized_width,
				'height' => $resized_height,
				'type' => $resized_type
			);
		}
		else {
			$image_array = array(
				'url' => str_replace( basename( $url ), basename( $dest_file_name ), $url ),
				'width' => $dest_width,
				'height' => $dest_height,
				'type' => $ext
			);
		}
		// Return image array
		return $image_array;
	}
}
else {
	function tx_image_resize( $url, $width = NULL, $height = NULL, $crop = true, $retina = false ) {
		global $wpdb;
		if ( empty( $url ) )
			return new WP_Error( 'no_image_url', __( 'No image URL has been entered.','wta' ), $url );
		// Bail if GD Library doesn't exist
		if ( !extension_loaded('gd') || !function_exists('gd_info') )
			return array( 'url' => $url, 'width' => $width, 'height' => $height );
		// Get default size from database
		$width = ( $width ) ? $width : get_option( 'thumbnail_size_w' );
		$height = ( $height ) ? $height : get_option( 'thumbnail_size_h' );
		// Allow for different retina sizes
		$retina = $retina ? ( $retina === true ? 2 : $retina ) : 1;
		// Destination width and height variables
		$dest_width = $width * $retina;
		$dest_height = $height * $retina;
		// Get image file path
		$file_path = parse_url( $url );
		$file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path['path'];
		
		// Check for Multisite
		if ( is_multisite() ) {
			global $blog_id;
			$blog_details = get_blog_details( $blog_id );
			$file_path = str_replace( $blog_details->path . 'files/', '/wp-content/blogs.dir/'. $blog_id .'/files/', $file_path );
		}
		// Some additional info about the image
		$info = pathinfo( $file_path );
		$dir = $info['dirname'];
		$ext = $info['extension'];
		$name = wp_basename( $file_path, ".$ext" );
	        if ( 'bmp' == $ext ) {
			return new WP_Error( 'bmp_mime_type', __( 'Image is BMP. Please use either JPG or PNG.','wta' ), $url );
		}
		// Suffix applied to filename
		$suffix = "{$dest_width}x{$dest_height}";
		// Get the destination file name
		$dest_file_name = "{$dir}/{$name}-{$suffix}.{$ext}";
		// No need to resize & create a new image if it already exists!
		if ( !file_exists( $dest_file_name ) ) {
		
			/*
			 *  Bail if this image isn't in the Media Library either.
			 *  We only want to resize Media Library images, so we can be sure they get deleted correctly when appropriate.
			 */
			$query = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE guid='%s'", $url );
			$get_attachment = $wpdb->get_results( $query );
			if ( !$get_attachment )
				return array( 'url' => $url, 'width' => $width, 'height' => $height );
			$image = wp_load_image( $file_path );
			if ( !is_resource( $image ) )
				return new WP_Error( 'error_loading_image_as_resource', $image, $file_path );
			// Get the current image dimensions and type
			$size = @getimagesize( $file_path );
			if ( !$size )
				return new WP_Error( 'file_path_getimagesize_failed', __( 'Failed to get $file_path information using "@getimagesize".','wta'), $file_path );
			list( $orig_width, $orig_height, $orig_type ) = $size;
			
			// Create new image
			$new_image = wp_imagecreatetruecolor( $dest_width, $dest_height );
			// Do some proportional cropping if enabled
			if ( $crop ) {
				$src_x = $src_y = 0;
				$src_w = $orig_width;
				$src_h = $orig_height;
				$cmp_x = $orig_width / $dest_width;
				$cmp_y = $orig_height / $dest_height;
				// Calculate x or y coordinate, and width or height of source
				if ( $cmp_x > $cmp_y ) {
					$src_w = round( $orig_width / $cmp_x * $cmp_y );
					$src_x = round( ( $orig_width - ( $orig_width / $cmp_x * $cmp_y ) ) / 2 );
				}
				else if ( $cmp_y > $cmp_x ) {
					$src_h = round( $orig_height / $cmp_y * $cmp_x );
					$src_y = round( ( $orig_height - ( $orig_height / $cmp_y * $cmp_x ) ) / 2 );
				}
				// Create the resampled image
				imagecopyresampled( $new_image, $image, 0, 0, $src_x, $src_y, $dest_width, $dest_height, $src_w, $src_h );
			}
			else
				imagecopyresampled( $new_image, $image, 0, 0, 0, 0, $dest_width, $dest_height, $orig_width, $orig_height );
			// Convert from full colors to index colors, like original PNG.
			if ( IMAGETYPE_PNG == $orig_type && function_exists('imageistruecolor') && !imageistruecolor( $image ) )
				imagetruecolortopalette( $new_image, false, imagecolorstotal( $image ) );
			// Remove the original image from memory (no longer needed)
			imagedestroy( $image );
			// Check the image is the correct file type
			if ( IMAGETYPE_GIF == $orig_type ) {
				if ( !imagegif( $new_image, $dest_file_name ) )
					return new WP_Error( 'resize_path_invalid', __( 'Resize path invalid (GIF)','wta' ) );
			}
			elseif ( IMAGETYPE_PNG == $orig_type ) {
				if ( !imagepng( $new_image, $dest_file_name ) )
					return new WP_Error( 'resize_path_invalid', __( 'Resize path invalid (PNG).','wta' ) );
			}
			else {
				// All other formats are converted to jpg
				if ( 'jpg' != $ext && 'jpeg' != $ext )
					$dest_file_name = "{$dir}/{$name}-{$suffix}.jpg";
				if ( !imagejpeg( $new_image, $dest_file_name, apply_filters( 'resize_jpeg_quality', 90 ) ) )
					return new WP_Error( 'resize_path_invalid', __( 'Resize path invalid (JPG).','wta' ) );
			}
			// Remove new image from memory (no longer needed as well)
			imagedestroy( $new_image );
			// Set correct file permissions
			$stat = stat( dirname( $dest_file_name ));
			$perms = $stat['mode'] & 0000666;
			@chmod( $dest_file_name, $perms );
			// Get some information about the resized image
			$new_size = @getimagesize( $dest_file_name );
			if ( !$new_size )
				return new WP_Error( 'resize_path_getimagesize_failed', __( 'Failed to get $dest_file_name (resized image) info via @getimagesize','wta' ), $dest_file_name );
			list( $resized_width, $resized_height, $resized_type ) = $new_size;
			// Get the new image URL
			$resized_url = str_replace( basename( $url ), basename( $dest_file_name ), $url );
			// Add the resized dimensions to original image metadata (so we can delete our resized images when the original image is delete from the Media Library)
			$metadata = wp_get_attachment_metadata( $get_attachment[0]->ID );
			if ( isset( $metadata['image_meta'] ) ) {
				$metadata['image_meta']['resized_images'][] = $resized_width .'x'. $resized_height;
				wp_update_attachment_metadata( $get_attachment[0]->ID, $metadata );
			}
			// Return array with resized image information
			$image_array = array(
				'url' => $resized_url,
				'width' => $resized_width,
				'height' => $resized_height,
				'type' => $resized_type
			);
		}
		else {
			$image_array = array(
				'url' => str_replace( basename( $url ), basename( $dest_file_name ), $url ),
				'width' => $dest_width,
				'height' => $dest_height,
				'type' => $ext
			);
		}
		return $image_array;
	}
}
/**
 *  Deletes the resized images when the original image is deleted from the Wordpress Media Library.
 *
 *  @author Matthew Ruddy
 */
add_action( 'delete_attachment', 'matthewruddy_delete_resized_images' );
function matthewruddy_delete_resized_images( $post_id ) {
	// Get attachment image metadata
	$metadata = wp_get_attachment_metadata( $post_id );
	if ( !$metadata )
		return;
	// Do some bailing if we cannot continue
	if ( !isset( $metadata['file'] ) || !isset( $metadata['image_meta']['resized_images'] ) )
		return;
	$pathinfo = pathinfo( $metadata['file'] );
	$resized_images = $metadata['image_meta']['resized_images'];
	// Get Wordpress uploads directory (and bail if it doesn't exist)
	$wp_upload_dir = wp_upload_dir();
	$upload_dir = $wp_upload_dir['basedir'];
	if ( !is_dir( $upload_dir ) )
		return;
	// Delete the resized images
	foreach ( $resized_images as $dims ) {
		// Get the resized images filename
		$file = $upload_dir .'/'. $pathinfo['dirname'] .'/'. $pathinfo['filename'] .'-'. $dims .'.'. $pathinfo['extension'];
		// Delete the resized image
		@unlink( $file );
	}
}




/*-----------------------------------------------------------------------------------*/
/*	changing default Excerpt length 
/*-----------------------------------------------------------------------------------*/ 

if ( ! function_exists( 'tx_custom_excerpt' ) ) {
	function tx_custom_excerpt($limit) {
		$excerpt = explode(' ', get_the_excerpt(), $limit);
		if (count($excerpt)>=$limit) {
			array_pop($excerpt);
			$excerpt = implode(" ",$excerpt).' ...';
		} else {
			$excerpt = implode(" ",$excerpt);
		}
		//$excerpt = preg_replace('`[[^]]*]`','',$excerpt);
		$excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
		return $excerpt;
	}
}


function tx_slider_cat_list () {
	
	$post_type = 'itrans-slider';
	$tax = 'itrans-slider-category';
	
	$return = "";
	$cat_list = "";
	
	$terms = array( 'all' => 'All' );
	
	/**/
	$terms = get_terms('itrans-slider-category');
	if ( !empty( $terms ) && !is_wp_error( $terms ) )
	{
			foreach ( $terms as $term ) 
			{
				$cat_list .= '<option value="'.$term->slug.'">' . $term->name . '</option>';	
			}
	}
	
	if (!empty($cat_list)) {
		return $cat_list;
	} else {
		return $return;
	}
}


function tx_load_jqvariables(){
	
	$tx_slider_cat_list = tx_slider_cat_list();
	$tx_slider_cat_list = array( 'tx_slider_cat_list' => $tx_slider_cat_list );
	
	wp_localize_script( 'tx-main', 'tx_t1', $tx_slider_cat_list );
	
}
add_action( 'admin_enqueue_scripts', 'tx_load_jqvariables' ); 

/* Add SiteOrigin PageBuilder Widget Filter Tab */
function tx_add_widget_tabs($tabs) {
    $tabs[] = array(
        'title' => __('TemplatesNext Toolkit', 'tx'),
        'filter' => array(
            'groups' => array('tx')
        )
    );

    return $tabs;
}
add_filter('siteorigin_panels_widget_dialog_tabs', 'tx_add_widget_tabs', 20);


/* Get list of contact form 7 */
function tx_contactform7_list () {
        
	$options = array();

    if (function_exists('wpcf7')) {
        $wpcf7_form_list = get_posts(array(
           'post_type' => 'wpcf7_contact_form',
           'showposts' => 999,
       ));
       $options[0] = esc_html__('Select a Form', 'tx');
       if (!empty($wpcf7_form_list) && !is_wp_error($wpcf7_form_list)) {
           foreach ($wpcf7_form_list as $post) {
               $options[$post->ID] = $post->post_title;
           }
       } else {
           $options[0] = esc_html__('Create a Form First', 'tx');
       }
    }
    return $options;
}

