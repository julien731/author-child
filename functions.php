<?php
/**
 * @package Author Child Theme
 */

add_action( 'wp_enqueue_scripts', 'author_child_assets' );
/**
 * Load the parent and child theme styles
 */
function author_child_assets() {

	// Parent theme styles
	wp_enqueue_style( 'author-style', get_template_directory_uri(). '/style.css' );

	// Child theme styles
	wp_enqueue_style( 'author-child-style', get_stylesheet_directory_uri(). '/style.css' );

	// Prism Style
	wp_register_style( 'author-child-prism', get_stylesheet_directory_uri() . '/assets/vendors/prism/prism.css', null, '1.0', 'all' );
	wp_enqueue_style( 'author-child-prism' );

	// Prism Script
	wp_register_script( 'author-child-prism', get_stylesheet_directory_uri() . '/assets/vendors/prism/prism.js', array( 'jquery' ), '1.0', true );
	wp_enqueue_script( 'author-child-prism' );

	// Fitvids
	wp_enqueue_script( 'author-fitvids-js', get_template_directory_uri() . '/includes/js/fitvid/jquery.fitvids.js', array( 'jquery' ), '1.0.3', true );

	// Custom Script
	wp_register_script( 'author-child-custom', get_stylesheet_directory_uri() . '/assets/js/custom.js', array( 'jquery' ), '1.0', true );
	wp_enqueue_script( 'author-child-custom' );

}

add_action( 'wp_enqueue_scripts', 'author_child_remove_assets', 50 );
/**
 * Remove unwanted parent theme assets.
 *
 * @since 1.0.0
 * @return void
 */
function author_child_remove_assets() {

	// Remove theme custom script
	wp_dequeue_script( 'author-custom-js' );

	// Remove Flexslider
	wp_dequeue_script( 'author-flexslider-js' );
	wp_dequeue_style( 'author-flexslider-css' );

}

/**
 * Remove wp_trim_excerpt from the excerpt in order to generate an improved version
 *
 * @since 1.0
 */
remove_filter( 'get_the_excerpt', 'wp_trim_excerpt' );

add_filter( 'get_the_excerpt', 'author_child_custom_excerpt' );
/**
 * Customize the excerpt
 *
 * This function re-uses the code from wp_trim_excerpt() but does not apply wp_trim_words() which strips all tags.
 *
 * We want the excerpt to be longer than just the default 55 characters, and to display the formatted content.
 *
 * @since 1.0
 *
 * @param string $excerpt The post excerpt
 *
 * @return string
 */
function author_child_custom_excerpt( $excerpt ) {

	$raw_excerpt = $excerpt;

	if ( '' == $excerpt ) {

		$excerpt = get_the_content( '' );
		$excerpt = strip_shortcodes( $excerpt );
		$excerpt = apply_filters( 'the_content', $excerpt );
		$excerpt = str_replace( ']]>', ']]&gt;', $excerpt );

		// Set the excerpt word count and only break after sentence is complete.
		$excerpt_word_count = 200;
		$excerpt_length     = apply_filters( 'excerpt_length', $excerpt_word_count );
		$tokens             = array();
		$excerptOutput      = '';
		$count              = 0;

		// Divide the string into tokens; HTML tags, or words, followed by any whitespace
		preg_match_all( '/(<[^>]+>|[^<>\s]+)\s*/u', $excerpt, $tokens );

		foreach ( $tokens[0] as $token ) {

			if ( $count >= $excerpt_length && preg_match( '/[\,\;\?\.\!]\s*$/uS', $token ) ) {
				// Limit reached, continue until , ; ? . or ! occur at the end
				$excerptOutput .= trim( $token ) . ' [&hellip;]';
				break;
			}

			// Add words to complete sentence
			$count ++;

			// Append what's left of the token
			$excerptOutput .= $token;
		}

		$excerpt = trim( force_balance_tags( $excerptOutput ) );

		// Add the read more link if the post is longer that the excerpt length.
		if ( $excerpt_length < count( $tokens[0] ) ) {
			$excerpt_more  = ' <a href="' . esc_url( get_permalink() ) . '">' . sprintf( __( 'Continue reading &rarr;', 'thereader' ), get_the_title() ) . '</a>';
			$excerpt .= $excerpt_more;
		}

	}

	return apply_filters( 'wpse_custom_wp_trim_excerpt', $excerpt, $raw_excerpt );

}

add_filter( 'the_content', 'author_child_code_highlighting' );
/**
 * Ensure that the code contained in <pre> tags is semantically correct
 *
 * HTML5 recommends that code fragments withing <pre> tags should also be wrapped into <code> tags. Prism, the syntax
 * highlighter used in the theme, follows this recommendation and only triggers if the code if compliant.
 *
 * For that reason, we make sure that all <pre> tags also have the proper <code> wrapper.
 *
 * @since 1.0
 *
 * @param string $content Post content
 *
 * @see   https://www.w3.org/TR/html5/text-level-semantics.html#the-code-element
 *
 * @return string
 */
function author_child_code_highlighting( $content ) {

	if ( empty( $content ) ) {
		return $content;
	}

	// Prepare a default class to be used on the code tag.
	$class_default = apply_filters( 'author_child_code_highlight_code_tag_class', 'language-none' );

	// Load the content in a DOMDocument object.
	$document = new DOMDocument();

	// Set the encoding to avoid weird characters issue on saving the HTML.
	$document->encoding = 'utf-8';

	// Load the post content while making sure the content is UTF-8.
	$document->loadHTML( utf8_decode( $content ), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD );

	// Get all the pre elements from the content.
	$pres = $document->getElementsByTagName( 'pre' );

	foreach ( $pres as $key => $pre ) {

		// Get the <pre> class if any.
		$class = $pre->getAttribute( 'class' );

		// Make sure we have a valid class to apply to the code tag.
		if ( empty( $class ) || 'language-' !== substr( $class, 0, 9 ) ) {
			$class = $class_default;
		}

		$origin = $pre->nodeValue;

		if ( false === strpos( $origin, '<code' ) ) {

			// Create a new, empty pre element that will be used to replace the old one (the one without the code block).
			$new_pre = $document->createElement( 'pre', '' );

			// Create a new code element with the original pre's contents.
			$new_code = $document->createElement( 'code', $origin );

			// Create the class attribute to the code tag.
			$attr = $document->createAttribute( 'class' );

			// Set the class name for the code tag.
			$attr->value = $class;

			// Add the attribute to the new code element.
			$new_code->appendChild( $attr );

			// Now we add the code element to the new pre element.
			$new_pre->appendChild( $new_code );

			// Finally, we replace the original pre element by the new, HTML5 compliant one.
			$pre->parentNode->replaceChild( $new_pre, $pre );

		}

	}

	// Time to get the brand new HTML content from the DOMDocument.
	$content = $document->saveHTML();

	return $content;

}

/**
 * Remove the emoticons script.
 *
 * @since 1,0
 */
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );