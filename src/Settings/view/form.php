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
$spinner = void_tag($this->getPageSlug() . '_spinner', 'img', [
    'class' => 'loading-gif',
    'src'   => \includes_url() . 'images/spinner.gif',
    'alt'   => 'spinner',
    'style' => 'display: none',
]);
\do_action('italystrap_before_settings_page', $this);
?>
<div id="tabs" class="wrap">
    <h1><?php
    echo \esc_html(\get_admin_page_title());
    ?></h1>
    <div id="post-body">
        <div class="postbox-container">
            <?php
            do_action('italystrap_before_settings_form', $this);
            ?>
            <?php
            open_tag_e($this->getPageSlug() . 'form', 'form', [
                'method'    => 'post',
                'action'    => 'options.php',
                'id'        => $this->getPageSlug(),
            ]);
            ?>
                <?php
                $this->createNavTab();
/**
 * Output nonce, action, and option_page fields for a settings page.
 */
                \settings_fields($this->getPageSlug());
/**
 * Output settings sections and fields
 */
                $this->doSettingsSections($this->getPageSlug());
/**
 * Output a submit button
 */
                \submit_button();
                echo $spinner;
                ?>
                <div id="saveResult"></div>
            <?php
            close_tag_e($this->getPageSlug() . 'form');
            ?>
            <?php
            \do_action('italystrap_after_settings_form', $this);
            ?>
        </div>
    </div>
</div>
<div class="clear"></div>
<?php
do_action('italystrap_after_settings_page', $this);
return;
