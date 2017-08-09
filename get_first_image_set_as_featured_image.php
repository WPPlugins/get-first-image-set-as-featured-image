<?php
error_reporting(E_ALL ^ E_NOTICE);
/*
Plugin Name: Get First Image Set As Featured Image
Plugin URI: http://venugopalphp.wordpress.com/
Description: After installed this plugin automatically retrieve the first image from content and its set as featured image.
Author: Venugopal
Version: 1.0
Author URI: http://venugopalphp.wordpress.com/
*/

function ffim_set_featured_img($post_id){
/* Checking post id */
if(isset($post_id)){
	
/* Getting Current Post Content by using post id */	
$content_post = get_post($post_id);
$content = $content_post->post_content;

 /* Checking first image in current post content */	
$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches);
$filename = $matches [1][0];

/* $new_img_tag = "<img src='".$filename."' />"; */
/* echo  $filename.'--> File Name'; */

/* checking filename */
if(isset($filename)){

/* Removing .jpg Extension  from Name will getting the only of fils */
$namep =  basename($filename); 
$pathInfo = pathinfo($namep);
$img_title =  $pathInfo['filename'];

//echo '<center>'.$img_title.'</center>'; 
$get_full_img_name = "";

/* Checking if image name contain WxH(ex : venugopal-1024x453). If WXH is there we will  removing that */
if (strpos($img_title,'x') !== false)
{
	$arr_img_title = explode('-', $img_title);
	array_pop($arr_img_title);
	
	$get_full_img_name = implode("-", $arr_img_title);

	//echo '<center>'.$get_full_img_name.'</center>';
} 
else{
	
	$get_full_img_name = $img_title; 
	
}
    /* Getting thumb id  */
	global $wpdb;
	$url_check = "SELECT * FROM ".$wpdb->prefix."posts WHERE post_title = '$get_full_img_name' and post_type = 'attachment'";
	$url_check_thumid = $wpdb->get_var($url_check); 
     
	 /* Checking Image path in database  if image path is empty insert image path*/
	if(empty($url_check_thumid) && $url_check_thumid == ""){
		
		global $wpdb;
						 
		 // Create post object
		$my_post = array(
		'post_title'   => $img_title,
		'post_parent'   => $post_id,
		'guid' => $filename,
		'post_type' => 'attachment'
		);

		// Insert the post into the database
		$thumb_id = wp_insert_post( $my_post );
		 		 
	     //echo "insert".$thumb_id;
		if(!add_post_meta( $post_id, '_thumbnail_id', $thumb_id, true ))
		{
			update_post_meta($post_id, '_thumbnail_id', $thumb_id);
		}
		
		
	} else{
		/* If image path is there getting thumb id from database */
		$compare_thumid = $wpdb->get_row("SELECT * FROM ".$wpdb->prefix."posts WHERE post_title = '$get_full_img_name' and post_type = 'attachment'");
		
		//echo $compare_thumid->ID.'thumb_id';
		if(!add_post_meta( $post_id, '_thumbnail_id', $compare_thumid->ID, true ))
		{
			if(update_post_meta($post_id, '_thumbnail_id', $compare_thumid->ID))
			{
				//echo "Sucess";
			} else{
				//echo $compare_thumid->ID."fail";
			}
					
		}	
	}
}
}
}

// Getting Current Post ID
$post_id = intval($_REQUEST['post']);

// Calling function
ffim_set_featured_img($post_id);
?>