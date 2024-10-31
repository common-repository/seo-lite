<?php defined('ABSPATH') || die;

/*
 	Plugin Name: SEO Lite
 	Plugin URI: https://github.com/apedestrian/SEO-Lite
 	Description: Adds all of the basic Open Graph meta tags to the site head.
 	Version: 2.1.1
 	Author: aPEDESTRIAN
 	Author URI: https://github.com/apedestrian
 	License: GPL-2.0+
 	License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

// Only load admin pages on backend
if (is_admin()) {
	require_once plugin_dir_path(__FILE__) . 'admin/class-seo-lite-admin.php';
	$plugin_admin = new SEO_Lite_Admin();
}

add_action('wp_head', function () {
	?>
	<!------------ <seo-lite> ------------>
	<meta property="og:locale" content="<?php echo esc_attr(get_locale()); ?>">
	<meta property="og:site_name" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
	<meta property="og:title" content="<?php echo esc_attr(wp_get_document_title()); ?>">
	<?php // Front page
	if (is_front_page()): ?>
		<?php if ($description = get_bloginfo('description')): ?>
			<meta property="og:description" content="<?php echo esc_attr((strlen($description) > 200 ? substr($description, 0, 197) . '...' : $description)); ?>">
		<?php endif; ?>
		<meta property="og:url" content="<?php echo esc_url(home_url()); ?>">
		<meta property="og:type" content="website">
		<?php if ($attachment_id = get_option('site_icon')): $attachment_meta = wp_get_attachment_metadata($attachment_id); ?>
			<meta property="og:image" content="<?php echo esc_url(wp_get_attachment_url($attachment_id)); ?>">
			<meta property="og:image:type" content="<?php echo esc_attr(get_post_mime_type($attachment_id)); ?>">
			<meta property="og:image:width" content="<?php echo esc_attr($attachment_meta['width']); ?>">
			<meta property="og:image:height" content="<?php echo esc_attr($attachment_meta['height']); ?>">
			<meta property="og:image:alt" content="<?php echo esc_attr(get_bloginfo('name')); ?>">
		<?php endif; ?>
	<?php // Single Post and Page
	elseif (is_single() || is_page()): ?>
		<?php if ($description = get_the_excerpt()): ?>
			<meta property="og:description" content="<?php echo esc_attr((strlen($description) > 200 ? substr($description, 0, 197) . '...' : $description)); ?>">
		<?php endif; ?>
		<meta property="og:url" content="<?php echo esc_url(get_the_permalink()); ?>">
		<meta property="og:type" content="article">
		<meta property="article:published_time" content="<?php echo esc_attr(get_the_date('c')); ?>">
		<meta property="article:modified_time" content="<?php echo esc_attr(get_the_modified_date('c')); ?>">
		<?php if ($attachment_id = get_post_thumbnail_id()): $attachment_meta = wp_get_attachment_metadata($attachment_id); ?>
			<meta property="og:image" content="<?php echo esc_url(wp_get_attachment_url($attachment_id)); ?>">
			<meta property="og:image:type" content="<?php echo esc_attr(get_post_mime_type($attachment_id)); ?>">
			<meta property="og:image:width" content="<?php echo esc_attr($attachment_meta['width']); ?>">
			<meta property="og:image:height" content="<?php echo esc_attr($attachment_meta['height']); ?>">
			<?php if ($attachment_alt == get_post_meta($attachment_id, '_wp_attachment_image_alt', true)): ?>
				<meta property="og:image:alt" content="<?php echo esc_attr($attachment_alt); ?>">
			<?php endif; ?>
		<?php endif; ?>
	<?php // Everything Else
	else: global $wp; ?>
		<?php if (is_archive()): ?>
			<?php if ($description = get_the_archive_description()): ?>
				<meta property="og:description" content="<?php echo esc_attr((strlen($description) > 200 ? substr($description, 0, 197) . '...' : $description)); ?>">
			<?php endif; ?>
		<?php endif; ?>
		<meta property="og:url" content="<?php echo esc_url(get_option('permalink_structure') == '' ? add_query_arg($wp->query_vars, home_url()) : home_url($wp->request)); ?>">
		<meta property="og:type" content="website">
	<?php endif; ?>
	<?php foreach (json_decode(get_option('seo_lite_custom_tags', '[]')) as $property => $content): ?>
		<meta property="<?php echo esc_attr($property); ?>" content="<?php echo esc_attr($content); ?>">
	<?php endforeach; ?>
	<!------------ </seo-lite> ----------->
	<?php
});