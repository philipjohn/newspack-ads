<?php
/**
 * Newspack Google Ad Manager Block Management
 *
 * @package Newspack
 */

/**
 * Newspack Google Ad Manager Blocks Management
 */
class Newspack_GAM_Blocks {

	/**
	 * Initialize blocks
	 *
	 * @return void
	 */
	public static function init() {
		require_once NEWSPACK_GAM_ABSPATH . 'src/blocks/google-ad-manager/view.php';
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'enqueue_block_editor_assets' ) );
	}

	/**
	 * Enqueue block scripts and styles for editor.
	 */
	public static function enqueue_block_editor_assets() {
		$editor_script = Newspack_GAM::plugin_url( 'dist/editor.js' );
		$editor_style  = Newspack_GAM::plugin_url( 'dist/editor.css' );
		$dependencies  = self::dependencies_from_path( NEWSPACK_GAM_ABSPATH . 'dist/editor.deps.json' );
		wp_enqueue_script(
			'newspack-gam-editor',
			$editor_script,
			$dependencies,
			'0.1.0',
			true
		);
		wp_enqueue_style(
			'newspack-gam-editor',
			$editor_style,
			array(),
			'0.1.0'
		);
	}

	/**
	 * Parse generated .deps.json file and return array of dependencies to be enqueued.
	 *
	 * @param string $path Path to the generated dependencies file.
	 *
	 * @return array Array of dependencides.
	 */
	public static function dependencies_from_path( $path ) {
		$dependencies = file_exists( $path )
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			? json_decode( file_get_contents( $path ) )
			: array();
		$dependencies[] = 'wp-polyfill';
		return $dependencies;
	}

	/**
	 * Utility to assemble the class for a server-side rendered bloc
	 *
	 * @param string $type The block type.
	 * @param array  $attributes Block attributes.
	 *
	 * @return string Class list separated by spaces.
	 */
	public static function block_classes( $type, $attributes = array() ) {
		$align   = isset( $attributes['align'] ) ? $attributes['align'] : 'center';
		$classes = array(
			"wp-block-newspack-blocks-{$type}",
			"align{$align}",
		);
		if ( isset( $attributes['className'] ) ) {
			array_push( $classes, $attributes['className'] );
		}
		return implode( $classes, ' ' );
	}

	/**
	 * Enqueue view scripts and styles for a single block.
	 *
	 * @param string $type The block's type.
	 */
	public static function enqueue_view_assets( $type ) {
		$style_path  = Newspack_GAM::plugin_url( 'dist/{$type}/view' . ( is_rtl() ? '.rtl' : '' ) . '.css' );
		$script_path = Newspack_GAM::plugin_url( 'dist/{$type}/view.js' );
		if ( file_exists( NEWSPACK_GAM_ABSPATH . $style_path ) ) {
			wp_enqueue_style(
				"newspack-blocks-{$type}",
				plugins_url( $style_path, __FILE__ ),
				array(),
				NEWSPACK_GAM_VERSION
			);
		}
		if ( file_exists( NEWSPACK_GAM_ABSPATH . $script_path ) ) {
			$dependencies = self::dependencies_from_path( NEWSPACK_BLOCKS__PLUGIN_DIR . "dist/{$type}/view.deps.json" );
			wp_enqueue_script(
				"newspack-blocks-{$type}",
				plugins_url( $script_path, __FILE__ ),
				$dependencies,
				array(),
				NEWSPACK_GAM_VERSION
			);
		}
	}
}
Newspack_GAM_Blocks::init();