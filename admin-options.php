<?php 

function wc_stock_availability_settings_page(){ ?>
	<div class="wrap">
	<h2>Settings</h2>

	<form method="post" action="options.php">
	    <?php settings_fields( 'wc_stock_availability_settings_group' ); ?>
	    <?php do_settings_sections( 'wc_stock_availability_settings_group' ); ?>
	    <table class="form-table">
	        <tr valign="top">
	        <th scope="row">Name</th>
	        <td><input type="text" name="wc_sa_name" value="<?php echo esc_attr( get_option('wc_sa_name') ); ?>" /></td>
	        </tr>
	    </table>
	    <?php submit_button(); ?>
	</form>
	</div>
<?php }