<?php defined('ABSPATH') || die;

class SEO_Lite_Admin {
	// Register the hooks for the options page
	public function __construct() {
		add_action('admin_enqueue_scripts',	[$this, 'seo_lite_admin_enqueue_scripts']);
		add_action('admin_menu', [$this, 'seo_lite_admin_menu']);
		add_action('admin_init', [$this, 'seo_lite_admin_init']);
	}

	public function seo_lite_admin_enqueue_scripts() {
		// Only load scripts.js if we are on the seo lite options page
		$current_screen = get_current_screen();
		if ($current_screen && property_exists($current_screen, 'id') && $current_screen->id == 'settings_page_seo-lite-options-page') {
			wp_enqueue_script('seo-lite-admin-core',  plugin_dir_url(__FILE__) . 'scripts.js', ['jquery'], false, true);
			wp_enqueue_style('seo-lite-admin-core',  plugin_dir_url(__FILE__) . 'styles.css');
		}
	}

	// Add menu item to admin area
	public function seo_lite_admin_menu() {
		add_options_page(
			'SEO Lite Settings',				// string $page_title
			'SEO Lite',							// string $menu_title
			'manage_options',					// string $capability
			'seo-lite-options-page',			// string $menu_slug
			[$this, 'print_options_page_html'],	// callable $callback (function to be called to output the page content)
			//null								// int $position
		);
	}

	// Register settings and sections so they can be printed later (admin_menu action)
	public function seo_lite_admin_init() {
		// Add section to form
		add_settings_section(
			'seo_lite_settings',		// string $section_id
			'Optional',					// string $section_title
			null,						// callable $callback (renders section description html)
			'seo-lite-options-page',	// string $page ($menu_slug of the page I show up on)
			[],							// array $args (before_section, after_section, section_class)
		);

		// Register setting
		register_setting(
			'seo_lite_option_group',	// string $option_group 
			'seo_lite_custom_tags',		// string $option_name
			array(						// array $args
				'type' => 'array',
				'description' => 'Additional tags for document head',
				'sanitize_callback' => [$this, 'sanatize_custom_tags'],
				//'show_in_rest' => false,
				//'default' => [] // Default value
			)
		);

		// Add the registered setting field to section
		add_settings_field(
			'seo_lite_custom_tags',					// string $option_name
			'Custom Tags',							// string $title
			[$this, 'print_custom_tags_html'], 		// callable $callback (renders field input html)
			'seo-lite-options-page',				// string $page ($menu_slug of the page I show up on)
			'seo_lite_settings'						// string $section_id that I appear under
		);
	}

	// Displays the admin page content
	public function print_options_page_html() {
		?>
		<div class="wrap">
			<h1>SEO Lite Settings</h1>
			<form method="post" action="options.php" id="seo-lite-form">
				<?php

				// Prints out all settings sections added to a particular settings page
				// $menu_slug (from add_options_page() call)
				do_settings_sections('seo-lite-options-page');

				// Outputs nonce, action, and option_page fields for a settings page.
				// $option_group from register_setting calls
				settings_fields('seo_lite_option_group');

				// Echoes a submit button, with provided text and appropriate class(es).
				submit_button(
					//'Save Changes',	// string $text (button text)
					//'primary',		// string $type (wp button style class)
					//'submit',			// string $name (input name attribute. If no id attribute is given in $other_attributes below, $name will be used as the button id)
					//true,				// bool $wrap (wrap button in p?)
					//null				// array|string $other_attributes (Other attributes that should be output with the button, mapping attributes to their values)
				);

				?>
			</form>
		</div>
		<?php
	}

	public function print_custom_tags_html() {
		// Get and decode the tags
		$seo_lite_custom_tags = json_decode(get_option('seo_lite_custom_tags' /* Option name */, '[]' /* Default value */), true /* decode json as assoc array? */);
		if (!is_array($seo_lite_custom_tags)) $seo_lite_custom_tags = [];

		?>
		<input type="hidden" name="seo_lite_custom_tags" id="seo_lite_custom_tags" value="">
		<table id="seo-lite-custom-tags-table">
			<thead class="desktop-only">
				<tr>
					<th>Property</th>
					<th>Content</th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($seo_lite_custom_tags as $property => $content): ?>
					<tr>
						<td>
							<label class="mobile-only">Property</label>
							<input name="property" type="text" value="<?php echo esc_attr($property); ?>">
						</td>
						<td>
							<label class="mobile-only">Content</label>
							<input name="content" type="text" value="<?php echo esc_attr($content); ?>">
						</td>
						<td>
							<span class="seo-lite-delete-row button button-primary">Delete</span>
						</td>
					</tr>
				<?php endforeach; ?>
				<?php if (empty($seo_lite_custom_tags)): /* Print empty row if there are no objectives */ ?>
					<tr>
						<td>
							<label class="mobile-only">Property</label>
							<input name="property" type="text" value="">
						</td>
						<td>
							<label class="mobile-only">Content</label>
							<input name="content" type="text" value="">
						</td>
						<td>
							<span class="seo-lite-delete-row button button-primary">Delete</span>
						</td>
					</tr>
				<?php endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="100%"><span class="seo-lite-add-row button button-primary">Add Tag</span></td>
				</tr>
			</tfoot>
		</table>
		<p>
			These meta tag property/content pairs will be added at the end of SEO Lite head tags.<br>
			<em>If there is a duplicate property, only the last occurrence will be saved.</em>
		</p>
		<?php
	}

	public function sanatize_custom_tags($custom_tags_json) {
		$custom_tags_json = json_decode($custom_tags_json, true /* decode json as assoc array? */);
		if (!is_array($custom_tags_json)) $custom_tags_json = [];

		foreach($custom_tags_json as $property => $content) {
			// Remove empty properties. We do not check for empty content because there are occasions where you may want them (ie, custom crawlers)
			if($property  == '') {
				unset($custom_tags_json[$property]);
			}
		}

		return wp_json_encode($custom_tags_json);
	}
}
