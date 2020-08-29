<?php
/**
 * Template to display the settings page.
 *
 * @package ItalyStrap\Settings
 */
declare(strict_types=1);

use function ItalyStrap\HTML\close_tag_e;
use function ItalyStrap\HTML\open_tag_e;
use function ItalyStrap\HTML\void_tag;

$this->assertHasSections();

$spinner = void_tag( $this->getPageSlug() . '_spinner', 'img', [
	'class'	=> 'loading-gif',
	'src'	=> \includes_url() . 'images/spinner.gif',
	'alt'	=> 'spinner',
	'style'	=> 'display: none',
] );

?>
<?php \do_action( 'italystrap_before_settings_page', $this ); ?>
<div id="tabs" class="wrap">
	<h1><?php echo \esc_html( \get_admin_page_title() ); ?></h1>
	<div id="post-body">
		<div class="postbox-container">
			<?php do_action( 'italystrap_before_settings_form', $this ); ?>
			<?php open_tag_e( $this->getPageSlug() . 'form', 'form', [
				'method'	=> 'post',
				'action'	=> 'options.php',
				'id'		=> $this->getPageSlug(),
			] ); ?>
				<?php
				$this->createNavTab();
				/**
				 * Output nonce, action, and option_page fields for a settings page.
				 */
				\settings_fields( $this->getPageSlug() );

				/**
				 * Output settings sections and fields
				 */
				$this->doSettingsSections( $this->getPageSlug() );

				/**
				 * Output a submit button
				 */
				\submit_button();
				echo $spinner;
				?>
				<div id="saveResult"></div>
			<?php close_tag_e( $this->getPageSlug() . 'form' ); ?>
			<?php \do_action( 'italystrap_after_settings_form', $this ); ?>
		</div>
	</div>
</div>
<div class="clear"></div>
<?php do_action( 'italystrap_after_settings_page', $this ); ?>
<?php
return;

/**
 * https://www.wpoptimus.com/434/save-plugin-theme-setting-options-ajax-wordpress/
 */

$updated = \sprintf(
	'<div class="updated"><p>%s</p></div>',
	\htmlentities( \__('Settings Saved Successfully', 'wp'), ENT_QUOTES )
);

?>
<script type="text/javascript">
	jQuery( document ).ready( function($) {
		var spinner = $( '.loading-gif' );
		$('#<?php echo \esc_attr( $this->getPageSlug() ) ?>').submit(function() {
			$( '.saveResult' ).empty();
			spinner.fadeIn();
			$(this).ajaxSubmit({
				success: function(){
					$('#saveResult').html("<div id='saveMessage' class='successModal'></div>");
					$('#saveMessage').append('<?php echo $updated; ?>').show();
					spinner.fadeOut();
				},
				error: function( data ) {
					console.log(data);
				},
				timeout: 5000
			});
			setTimeout(() => {
				$('#saveMessage').hide('slow');
			}, 5000);
			return false;
		});
	});
</script>
