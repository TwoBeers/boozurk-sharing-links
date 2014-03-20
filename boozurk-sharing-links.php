<?php
/*
Plugin Name: Boozurk Sharing Links
Plugin URI: 
Description: sharing links for Boozurk theme
Version: 0.1
Author: TwoBeers Crew
Author Email:
License:

  Copyright 2014 TwoBeers Crew

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as 
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
  
*/

class BoozurkSocialPlugin {

	/*--------------------------------------------*
	 * Constants
	 *--------------------------------------------*/
	const name = 'Boozurk Sharing Links';
	const slug = 'boozurk_sharing_links';
	
	/**
	 * Constructor
	 */
	function __construct() {
		//Hook up to the init action
		add_action( 'init', array( &$this, 'init_boozurk_sharing_links' ) );
	}

	/**
	 * Runs when the plugin is activated
	 */  
	function install_boozurk_sharing_links() {
		// do not generate any output here
	}

	/**
	 * Runs when the plugin is initialized
	 */
	function init_boozurk_sharing_links() {
		// Setup localization
		load_plugin_textdomain( self::slug, false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
		// Load JavaScript and stylesheets
		$this->register_scripts_and_styles();

		/*
		 * TODO: Define custom functionality for your plugin here
		 *
		 * For more information: 
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( 'boozurk_hook_post_extrainfo'	, array( $this, 'extrainfo' ), 10, 1 );
		add_action( 'wp_footer'						, array( $this, 'add_scripts' ) );
		add_filter( 'boozurk_options_array'			, array( $this, 'extra_options_array' ), 10, 1 );
		add_filter( 'boozurk_options_hierarchy'		, array( $this, 'extra_options_hierarchy' ), 10, 1 );
	}
  
	/**
	 * Registers and enqueues stylesheets for the administration panel and the
	 * public facing site.
	 */
	private function register_scripts_and_styles() {
		if ( is_admin() ) {

		} else {
			$this->load_file( self::slug . '-script', '/js/bsl.js', true, array( 'type' => boozurk_get_opt( 'boozurk_plusone' ) ) );
			$this->load_file( self::slug . '-style', '/css/bsl.css' );
		} // end if/else
	} // end register_scripts_and_styles
	
	/**
	 * Helper function for registering and enqueueing scripts and styles.
	 *
	 * @name	The 	ID to register with WordPress
	 * @file_path		The path to the actual file
	 * @is_script		Optional argument for if the incoming file_path is a JavaScript source file.
	 */
	private function load_file( $name, $file_path, $is_script = false, $localize_data = false ) {

		$url = plugins_url($file_path, __FILE__);
		$file = plugin_dir_path(__FILE__) . $file_path;

		if( file_exists( $file ) ) {
			if( $is_script ) {
				wp_register_script( $name, $url, array('jquery') ); //depends on jquery
				wp_enqueue_script( $name );
				if ( $localize_data )
					wp_localize_script( $name, self::slug . '_script_data', $localize_data );
			} else {
				wp_register_style( $name, $url );
				wp_enqueue_style( $name );
			} // end if
		} // end if

	} // end load_file

	function extra_options_array( $coa ) {

		$coa['boozurk_plusone'] = array(
			'type'			=> 'sel',
			'default'		=> 'none',
			'description'	=> 'sharing buttons',
			'info'			=> '',
			'options'		=> array( 'addthis', 'googleplus', 'googleplus_official', 'facebook', 'twitter', 'none' ),
			'options_l10n'	=> array( 'AddThis', 'Google+', 'Google+ (official button)', 'Facebook', 'Twitter', 'none' ),
			'req'			=> '',
		);

		return $coa;

	}

	function extra_options_hierarchy( $hierarchy ) {

		//Infinite Scroll
		if ( ! class_exists( 'Jetpack' ) || ! Jetpack::is_module_active( 'infinite-scroll' ) ) {

			$hierarchy['features']['sub']['other']['sub'][] = 'boozurk_plusone';

		}

		return $hierarchy;

	}

	function add_scripts() {

		if ( boozurk_is_printpreview() ) return;

		$share_type = boozurk_get_opt( 'boozurk_plusone' );

		if ( $share_type === 'googleplus_official' ) {

?>
	<script type="text/javascript" src="//apis.google.com/js/plusone.js">
		{parsetags: 'explicit'}
	</script>
<?php

		}

		if ( $share_type === 'addthis' ) {

?>
	<script type="text/javascript">
		var addthis_config =
		{
			ui_offset_top: 35,
			ui_offset_left:40,
			ui_delay:500
		}
	</script>
	<script type="text/javascript" src="//s7.addthis.com/js/250/addthis_widget.js"></script>
<?php

		}

	}

	function extrainfo( $args = '' ) {
		global $post;

		$share_type = boozurk_get_opt( 'boozurk_plusone' );

?>
		<?php if ( !post_password_required() && $args['share'] ) { ?>
			<?php if ( $share_type === 'addthis' ) { ?>
				<!-- AddThis Button BEGIN -->
				<div class="addthis_toolbox addthis_16x16_style">
					<a class="addthis_button_compact" href="javascript:void(0)" addthis:url="<?php echo esc_attr( get_permalink() ); ?>" addthis:title="<?php echo esc_attr( get_the_title() ); ?>"><i class="icon icon-plus"></i></a>
				</div>
				<!-- AddThis Button END -->
			<?php } ?>

			<?php if ( ( $share_type === 1 ) || ( $share_type === 'googleplus' ) ) { ?>
				<a class="btn share-with-plusone pmb_comm" title="<?php echo esc_attr( sprintf( __( 'recommend this with %s', 'boozurk_sharing_links' ), 'Google+' ) ); ?>" href="http://plus.google.com/share?url=<?php echo rawurlencode( get_permalink() ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
					<i class="icon icon-google-plus"></i>
				</a>
			<?php } ?>

			<?php if ( $share_type === 'googleplus_official' ) { ?>
				<div class="bz-plusone-wrap"><div class="g-plusone" data-annotation="none" data-href="<?php echo esc_url( get_permalink() ); ?>"></div></div>
			<?php } ?>

			<?php if ( $share_type === 'facebook' ) { ?>
				<a class="btn share-with-facebook pmb_comm" title="<?php echo esc_attr( sprintf( __( 'recommend this with %s', 'boozurk_sharing_links' ), 'Facebook' ) ); ?>" href="http://www.facebook.com/sharer.php?u=<?php echo rawurlencode( get_permalink() ); ?>&t=<?php echo rawurlencode( get_the_title() ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
					<i class="icon icon-facebook"></i>
				</a>
			<?php } ?>

			<?php if ( $share_type === 'twitter' ) { ?>
				<a class="btn share-with-twitter pmb_comm" title="<?php echo esc_attr( sprintf( __( 'recommend this with %s', 'boozurk_sharing_links' ), 'Twitter' ) ); ?>" href="https://twitter.com/intent/tweet?original_referer=<?php echo rawurlencode( get_permalink() ); ?>&text=<?php echo rawurlencode( get_the_title() ); ?>&url=<?php echo rawurlencode( get_permalink() ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
					<i class="icon icon-twitter"></i>
				</a>
			<?php } ?>

		<?php } ?>
<?php

	}

} // end class
new BoozurkSocialPlugin();

?>