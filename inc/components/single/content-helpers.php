<?php

if (! function_exists('blocksy_has_post_nav')) {
	function blocksy_has_post_nav() {
		$post_options = blocksy_get_post_options();
		$prefix = blocksy_manager()->screen->get_prefix();

		$has_post_nav = blocksy_get_theme_mod(
			$prefix . '_has_post_nav',
			'no'
		) === 'yes';

		if (blocksy_is_page()) {
			$has_post_nav = false;
		}

		if (
			blocksy_default_akg(
				'disable_posts_navigation', $post_options, 'no'
			) === 'yes'
		) {
			$has_post_nav = false;
		}

		return $has_post_nav;
	}
}

if (! function_exists('blocksy_has_share_box')) {
	function blocksy_has_share_box() {
		$post_options = blocksy_get_post_options();
		$prefix = blocksy_manager()->screen->get_prefix();

		$has_share_box = blocksy_get_theme_mod(
			$prefix . '_has_share_box',
			'no'
		) === 'yes';

		if (
			blocksy_default_akg(
				'disable_share_box',
				$post_options,
				'no'
			) === 'yes'
		) {
			$has_share_box = false;
		}

		return apply_filters(
			'blocksy:single:has-share-box',
			$has_share_box
		);
	}
}

if (! function_exists('blocksy_has_author_box')) {
	function blocksy_has_author_box() {
		$post_options = blocksy_get_post_options();
		$prefix = blocksy_manager()->screen->get_prefix();

		$has_author_box = blocksy_get_theme_mod(
			$prefix . '_has_author_box',
			'no'
		) === 'yes';

		if (blocksy_is_page()) {
			$has_author_box = false;
		}

		if (
			blocksy_default_akg(
				'disable_author_box', $post_options, 'no'
			) === 'yes'
		) {
			$has_author_box = false;
		}

		$has_author_box = apply_filters(
			'blocksy:single:has-author-box',
			$has_author_box
		);

		return $has_author_box;
	}
}

if (! function_exists('blocksy_single_content')) {
function blocksy_single_content($content = null) {
	$post_options = blocksy_get_post_options();

	$prefix = blocksy_manager()->screen->get_prefix();

	$has_post_tags = blocksy_get_theme_mod(
		$prefix . '_has_post_tags',
		'no'
	) === 'yes';

	if (
		blocksy_default_akg(
			'disable_post_tags', $post_options, 'no'
		) === 'yes'
	) {
		$has_post_tags = false;
	}

	$featured_image_location = 'none';

	$page_title_source = blocksy_get_page_title_source();
	$featured_image_source = blocksy_get_featured_image_source();

	if ($page_title_source) {
		$actual_type = blocksy_akg_or_customizer(
			'hero_section',
			blocksy_get_page_title_source(),
			'type-1'
		);

		if ($actual_type !== 'type-2') {
			$featured_image_location = blocksy_get_theme_mod(
				$prefix . '_featured_image_location',
				'above'
			);
		} else {
			$featured_image_location = 'below';
		}
	} else {
		$featured_image_location = 'above';
	}

	$share_box_type = blocksy_get_theme_mod($prefix . '_share_box_type', 'type-1');

	$share_box1_location = blocksy_get_theme_mod($prefix . '_share_box1_location', [
		'top' => false,
		'bottom' => true,
	]);

	$share_box2_location = blocksy_get_theme_mod($prefix . '_share_box2_location', 'right');
	$share_box2_colors = blocksy_get_theme_mod($prefix . '_share_box2_colors', 'custom');

	$gutenberg_layout_class = "is-layout-constrained";

	if (blocksy_sidebar_position() !== 'none') {
		$gutenberg_layout_class = "is-layout-flow";
	}

	$content_class = 'entry-content ' . $gutenberg_layout_class;

	ob_start();

	?>

	<article
		id="post-<?php the_ID(); ?>"
		<?php post_class(); ?>>

		<?php
			do_action('blocksy:single:top');

			if ($featured_image_location === 'above') {
				echo blocksy_get_featured_image_output();
			}

			if (
				! is_singular([ 'product' ])
				&&
				apply_filters('blocksy:single:has-default-hero', true)
			) {
				/**
				 * Note to code reviewers: This line doesn't need to be escaped.
				 * Function blocksy_output_hero_section() used here escapes the value properly.
				 */
				echo blocksy_output_hero_section([
					'type' => 'type-1'
				]);
			}

			if ($featured_image_location === 'below') {
				echo blocksy_get_featured_image_output();
			}
		?>

		<?php if (
			$share_box1_location['top']
			&&
			blocksy_has_share_box()
		) { ?>
			<?php
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				/**
				 * Note to code reviewers: This line doesn't need to be escaped.
				 * Function blocksy_get_social_share_box() used here escapes the value properly.
				 */
				echo blocksy_get_social_share_box([
					'root_class' => 'is-width-constrained',
					'html_atts' => [ 'data-location' => 'top'],
					'links_wrapper_attr' => $share_box_type === 'type-2' ? [
						'data-color' => $share_box2_colors
					] : [],
					'type' => $share_box_type,
					'enable_shortcut' => true
				]);
			?>
		<?php } ?>

		<?php do_action('blocksy:single:content:top'); ?>

		<div class="<?php echo $content_class ?>">
			<?php

			if (! is_attachment()) {
				if (
					function_exists('blc_get_content_block_that_matches')
					&&
					blc_get_content_block_that_matches([
						'template_type' => 'single',
						'template_subtype' => 'content'
					])
				) {
					$content = blc_render_content_block(
						blc_get_content_block_that_matches([
							'template_type' => 'single',
							'template_subtype' => 'content'
						])
					);
				}

				if ($content) {
					echo $content;
				} else {
					the_content(
						blocksy_safe_sprintf(
							wp_kses(
								/* translators: 1: span open 2: Name of current post. Only visible to screen readers 3: span closing */
								__(
									'Continue reading%1$s "%2$s"%3$s',
									'blocksy'
								),
								array(
									'span' => array(
										'class' => array(),
									),
								)
							),
							'<span class="screen-reader-text">',
							get_the_title(),
							'</span>'
						)
					);
				}
			} else {
				?>
					<figure class="wp-block-image">
						<?php
							echo blocksy_media([
								'attachment_id' => get_the_ID(),
								'post_id' => get_the_ID(),
								'size' => 'full',
								'tag_name' => 'figure',
								'ratio' => 'original',
							]);							
						?>

						<figcaption class="wp-caption-text">
							<?php
								echo wp_kses_post(wp_get_attachment_caption(get_post_thumbnail_id()));
								
							?>
						</figcaption>
					</figure>
				<?php
					remove_filter('the_content', 'prepend_attachment');
					the_content(); 
					add_filter('the_content', 'prepend_attachment');
			}

			?>
		</div>

		<?php
			if (get_post_type() === 'post') {
				edit_post_link(
					blocksy_safe_sprintf(
						/* translators: 1: span opening 2: Post title 3: span closing. */
						__( 'Edit%1$s "%2$s"%3$s', 'blocksy' ),
						'<span class="screen-reader-text">',
						get_the_title(),
						'</span>'
					),
					'',
					'',
					null,
					'post-edit-link is-width-constrained'
				);
			}

			wp_link_pages(
				[
					'before' => '<div class="page-links is-width-constrained"><span class="post-pages-label">' . esc_html__( 'Pages', 'blocksy' ) . '</span>',
					'after'  => '</div>',
				]
			);

			do_action('blocksy:single:content:bottom');
		?>

		<?php if ($has_post_tags) { ?>
			<?php
				$class = 'entry-tags is-width-constrained';

				$class .= ' ' . blocksy_visibility_classes(blocksy_get_theme_mod(
					$prefix . '_post_tags_visibility',
					[
						'desktop' => true,
						'tablet' => true,
						'mobile' => true,
					]
				));

				$tax_to_check = blocksy_maybe_get_matching_taxonomy(
					get_post_type(),
					false
				);
				$taxonomies_choices = [];

				if ($tax_to_check) {
					$all_taxonomies = array_values(array_diff(
						get_object_taxonomies(get_post_type()),
						['post_format']
					));

					foreach ($all_taxonomies as $single_taxonomy) {
						$taxonomy_object = get_taxonomy($single_taxonomy);

						if (! $taxonomy_object->hierarchical) {
							$taxonomies_choices[] = $single_taxonomy;
						}
					}

					if (count($taxonomies_choices) > 1) {
						$post_tags_taxonomy = blocksy_get_theme_mod(
							$prefix . '_post_tags_taxonomy',
							$taxonomies_choices[0]
						);

						if (taxonomy_exists($post_tags_taxonomy)) {
							$tax_to_check = $post_tags_taxonomy;
						}
					}
				}

				$module_title_default = __('Tags', 'blocksy');

				if (count($taxonomies_choices) > 0) {
					$taxonomy_object = get_taxonomy($taxonomies_choices[0]);

					if ($taxonomy_object) {
						$module_title_default = $taxonomy_object->label;
					}
				}

				$module_title_output = '';
				$module_title = blocksy_get_theme_mod(
					$prefix . '_post_tags_title',
					$module_title_default
				);
				$module_wrapper = blocksy_get_theme_mod($prefix . '_post_tags_title_wrapper', 'span');

				if (!empty($module_title) || is_customize_preview()) {
					$module_title_output = blocksy_html_tag(
						$module_wrapper,
						[
							'class' => 'ct-module-title'
						],
						$module_title
					);
				}

				$deep_link_args = blocksy_generic_get_deep_link([
					'prefix' => $prefix,
					'suffix' => $prefix . '_has_post_tags',
					'shortcut' => 'border',
					'return' => 'array'
				]);

				/**
				 * Note to code reviewers: This line doesn't need to be escaped.
				 * Function blocksy_post_meta() used here escapes the value properly.
				 */
				if (
					$tax_to_check
					&&
					blocksy_get_categories_list([
						'taxonomy' => $tax_to_check
					])
					&&
					! is_wp_error(blocksy_get_categories_list([
						'taxonomy' => $tax_to_check
					]))
				) {
					echo blocksy_html_tag(
						'div',
						array_merge(
							[
								'class' => $class,
							],
							$deep_link_args
						),

						$module_title_output .

						blocksy_html_tag(
							'div',
							[
								'class' => 'entry-tags-items'
							],
							blocksy_get_categories_list([
								'taxonomy' => $tax_to_check,
								'before_each' => '<span>#</span> ',
								'has_term_class' => false
							])
						)
					);
				}
			?>
		<?php } ?>

		<?php if (
			$share_box1_location['bottom']
			&&
			blocksy_has_share_box()
		) { ?>
			<?php
				/**
				 * Note to code reviewers: This line doesn't need to be escaped.
				 * Function blocksy_get_social_share_box() used here escapes the value properly.
				 */
				echo blocksy_get_social_share_box([
					'root_class' => 'is-width-constrained',
					'html_atts' => ['data-location' => 'bottom'],
					'links_wrapper_attr' => $share_box_type === 'type-2' ? [
						'data-color' => $share_box2_colors
					] : [],
					'type' => $share_box_type,
					'enable_shortcut' => true
				]);
			?>
		<?php } ?>

		<?php

		if (blocksy_has_author_box()) {
			blocksy_author_box();
		}

		if (blocksy_has_post_nav()) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			/**
			 * Note to code reviewers: This line doesn't need to be escaped.
			 * Function blocksy_post_navigation() used here escapes the value properly.
			 */
			echo blocksy_post_navigation();
		}

		if (function_exists('blc_ext_newsletter_subscribe_form')) {
			if (get_post_type() === 'post') {
				/**
				 * Note to code reviewers: This line doesn't need to be escaped.
				 * Function blc_ext_newsletter_subscribe_form() used here escapes the value properly.
				 */
				echo blc_ext_newsletter_subscribe_form();
			}
		}

		blocksy_display_page_elements('contained');

		do_action('blocksy:single:bottom');

		?>

	</article>

	<?php

	return ob_get_clean();
}
}

