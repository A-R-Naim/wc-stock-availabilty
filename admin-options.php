<?php 

function woo_pm_admin_settings_page(){ ?>
	<div class="wrap">
	<h1><?php esc_html_e( 'Settings', 'text-domain'); ?></h1>

	<form method="post" action="options.php">
	    <?php settings_fields( 'woo_pm_settings_group' ); ?>
	    <?php do_settings_sections( 'woo_pm_settings_group' ); ?>
	    <h3><?php esc_html_e( 'Pre-order status settings') ?></h3>
	    <table class="form-table">
	        <tr valign="top">
		        <th scope="row"><?php esc_html_e( 'Status bar color', 'text-domain' ); ?></th>
		        <td>
		        	<input type="text" name="woo_pm_status_bar" value="<?php echo esc_attr( get_option('woo_pm_status_bar') ); ?>" />
		        </td>
	        </tr>
	        <tr valign="top">
		        <th scope="row"><?php esc_html_e( 'Status background color', 'text-domain' ); ?></th>
		        <td>
		        	<input type="text" name="woo_pm_status_bg" value="<?php echo esc_attr( get_option('woo_pm_status_bg') ); ?>" />
		        </td>
	        </tr>
	        <tr valign="top">
		        <th scope="row"><?php esc_html_e( 'Status text color', 'text-domain' ); ?></th>
		        <td>
		        	<input type="text" name="woo_pm_status_text" value="<?php echo esc_attr( get_option('woo_pm_status_text') ); ?>" />
		        </td>
	        </tr>
	        <tr valign="top">
		        <th scope="row"><?php esc_html_e( 'Disable status on single product', 'text-domain' ); ?></th>
		        <td>
		        	<select name="woo_pm_disable_on_single">
		        		<?php $selected  = get_option('woo_pm_disable_on_single'); ?>
					  <option value="yes" <?php echo ( $selected == 'yes' ) ? 'selected' : '' ; ?>>Yes</option>
					  <option value="no" <?php echo ( $selected == 'no' ) ? 'selected' : '' ; ?>>No</option>
					</select>
		        </td>
	        </tr>
	        <tr valign="top">
		        <th scope="row"><?php esc_html_e( 'Mail subject', 'text-domain' ); ?></th>
		        <td>
		        	<input type="text" name="woo_pm_mail_subject" value="<?php echo esc_attr( get_option('woo_pm_mail_subject') ); ?>" />
		        </td>
	        </tr>
	        <tr valign="top">
		        <th scope="row"><?php esc_html_e( 'Mail content', 'text-domain' ); ?></th>
		        <td>
		        	<textarea name="woo_pm_mail_content"><?php echo esc_attr( get_option('woo_pm_mail_content') ); ?></textarea>
		        </td>
	        </tr>
	    </table>
	    <?php submit_button(); ?>
	</form>
	</div>
<?php }