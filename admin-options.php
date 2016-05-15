<?php 

function woo_pm_admin_settings_page(){ ?>
	<div class="wrap">
	<h2><?php esc_html_e( 'Settings', 'text-domain'); ?></h2>

	<form method="post" action="options.php">
	    <?php settings_fields( 'wc_stock_availability_settings_group' ); ?>
	    <?php do_settings_sections( 'wc_stock_availability_settings_group' ); ?>
	    <?php esc_html_e( 'Pre-order status styling') ?>
	    <table class="form-table">
	        <tr valign="top">
	        <th scope="row"><?php esc_html_e( 'Name', 'text-domain' ); ?></th>
	        <td><input type="text" name="woo_pm_" value="<?php echo esc_attr( get_option('wc_sa_name') ); ?>" /></td>
	        </tr>
	    </table>
	    <?php submit_button(); ?>
	</form>
	</div>
<?php }