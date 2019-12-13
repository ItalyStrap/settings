<?php
/**
 * Template to display the settings page.
 *
 * @package ItalyStrap\Settings
 */
declare(strict_types=1);

$spinner = \ItalyStrap\HTML\void_tag( $this->options_group . '_spinner', 'img', [
	\ItalyStrap\HTML\Attributes::CLASS_NAME	=> 'loading-gif',
	'src'	=> \includes_url() . 'images/spinner.gif',
	'alt'	=> 'spinner',
	'style'	=> 'display: none',
] );

?>
<?php \do_action( 'italystrap_before_settings_page', $this ); ?>
<div id="tabs" class="wrap">
	<div id="post-body">
		<div class="postbox-container">
			<?php do_action( 'italystrap_before_settings_form', $this ); ?>
			<form action="options.php" id="<?php echo \esc_attr( $this->options_group ) ?>" method="post">
				<?php
				$this->createNavTab();
				/**
				 * Output nonce, action, and option_page fields for a settings page.
				 */
				\settings_fields( $this->options_group );

				/**
				 * Output settings sections and fields
				 */
				$this->doSettingsSections( $this->options_group );

				/**
				 * Output a submit button
				 */
				\submit_button();
				echo $spinner;
				?>
				<div id="saveResult"></div>
			</form>
			<?php \do_action( 'italystrap_after_settings_form', $this ); ?>
		</div>
	</div>
</div>
<div class="clear"></div>
<?php
/**
 * https://www.wpoptimus.com/434/save-plugin-theme-setting-options-ajax-wordpress/
 */
?>
<script type="text/javascript">
	jQuery( document ).ready( function($) {
		var spinner = $( '.loading-gif' );
		$('#<?php echo \esc_attr( $this->options_group ) ?>').submit(function() {
			$( '.saveResult' ).empty();
			spinner.fadeIn();
			$(this).ajaxSubmit({
				success: function(){
					$('#saveResult').html("<div id='saveMessage' class='successModal'></div>");
					$('#saveMessage').append("<div class=\"updated\"><p><?php echo \htmlentities(__('Settings Saved Successfully', 'wp'), ENT_QUOTES); ?></p></div>").show();
					spinner.fadeOut();
				},
				error: function( data ) {
					console.log(data);
				},
				timeout: 5000
			});
			setTimeout("$('#saveMessage').hide('slow');", 5000);
			return false;
		});
	});
</script>
<?php do_action( 'italystrap_after_settings_page', $this ); ?>
