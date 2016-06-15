<?php 

/**
 * Get order details by product ID
 * @param  int $product_id 
 */
function woo_pm_get_order_details( $product_id ){
	global $wpdb;
	$i           = 0;
	$table_name  = $wpdb->prefix . 'wc_ordered_products';
	$all_orders  = $wpdb->get_results("SELECT order_id FROM $table_name WHERE product_id = $product_id" );
	$total_order = count( $all_orders );
	$order_result = '<h3>Total no. of order: ' . $total_order . '</h3>';
	foreach ( $all_orders as $order ) {
		$i++;
		$order_id = $order->order_id;
		$details  = new WC_Order($order_id);

		$order_result .= '<h4>Order no. ' . $i . '<h4>';

		$order_result .= '<h5> Order Details </h5>';
		$order_result .= 'Name: ' . $details->billing_first_name .' ' . $details->billing_last_name . '<br/>';
		$order_result .= 'Email: ' . $details->billing_email . '<br/>';

		$order_result .= 'Quantity: ';
		$items = $details->get_items();
		foreach ($items as $item ) {
			$item_id = $item['product_id'];
			if ( $product_id == $item_id ) {
				$order_result .= $product_qty = $item['qty'] . '<br/>';
			}
		}
		$order_status = $details->post_status;
		$status = '';
		if ( $order_status === 'wc-pending' ) {
			$status = 'Pending Payment';
		} elseif ( $order_status === 'wc-pending' ) {
			$status = '';
		}
		elseif ( $order_status === 'wc-processing' ) {
			$status = 'Processing';
		}
		elseif ( $order_status === 'wc-on-hold' ) {
			$status = 'On Hold';
		}
		elseif ( $order_status === 'wc-completed' ) {
			$status = 'Completed';
		}
		elseif ( $order_status === 'wc-cancelled' ) {
			$status = 'Cancelled';
		}
		elseif ( $order_status === 'wc-refunded' ) {
			$status = 'Refunded';
		}
		elseif ( $order_status === 'wc-failed' ) {
			$status = 'Failed';
		}	
		$order_result .= 'Order status: ' . $status . '<br>';
		$order_result .= 'Order Date: ' . $details->order_date;;
		
		$order_result .= '<hr>';
	}

	return $order_result;
}

/**
 * Dispaly preorder status
 */
function woo_pm_preorder_admin(){ ?>
	<h1><?php echo 'Preorder Status'; ?></h1>
	<?php 
		$args = array(
		    'post_type'      => 'product',
		    'posts_per_page' => -1,
		    'orderby'        => 'meta_value_num',
		    'order'          => 'DESC',
		    'meta_query'     => array(
		    'relation'       => 'AND',
		        array(
		            'key' => 'total_sales',
		        ),
		        array(
		            'key' => '_preorder_limit',
		        ),
		    )
		);

		$output = array_reduce( get_posts( $args ), function( $result, $post ) { ?>
			<script>
				jQuery(function($) {
				    var $info = $("#modal-content<?php echo $post->ID;  ?>");
				    $info.dialog({                   
				        'dialogClass'   : 'wp-dialog',           
				        'modal'         : true,
				        'autoOpen'      : false, 
				        'closeOnEscape' : true,      
				        'buttons'       : {
				            "Close": function() {
				                $(this).dialog('close');
				            }
				        }
				    });
				    $("#open-modal<?php echo $post->ID; ?>").click(function(event) {
				        event.preventDefault();
				        $info.dialog('open');
				    });
				}); 
			</script>
		<?php 
			$modal = '<button id="open-modal'.$post->ID.'" value="Click Here">
					<div id="modal-content'.$post->ID.'" title="Order Details" style="display:none;">'
					. woo_pm_get_order_details( $post->ID ) .
					'</div>';
		    $preorder_limit = get_post_meta( $post->ID, '_preorder_limit', true ) ;
		    $total_sales    = get_post_meta( $post->ID, 'total_sales', true ) ;
		    $percentage     = ( $total_sales / $preorder_limit ) * 100 ;
		    $title          = $post->post_title;
		    return $result .= 
		    '<tr>
		        <td class="column-columnname">' . '<a href ="'. get_permalink( $post->ID ) .'">' . $post->post_title . '</a>' . '</td>
		        <td class="column-columnname">' . get_post_meta( $post->ID, '_preorder_limit', true ) . '</td>
		        <td class="column-columnname">' . get_post_meta( $post->ID, 'total_sales', true ) . '</td>
		        <td class="column-columnname">' . $percentage . '% </td>
		        <td class="column-columnname">' . $modal . 'Click Here </td>
		    </tr>';
		} );

		echo '<table class="widefat fixed" cellspacing="0">
		        <thead>
		            <tr>
		                <th class="manage-column column-columnname">' . __( 'Product', 'text-domain' ) . '</th>
		                <th class="manage-column column-columnname">' . __( 'Preorder Limit', 'text-domain' ) . '</th>
		                <th class="manage-column column-columnname">' . __( 'Units Ordered', 'text-domain' ) . '</th>
		                <th class="manage-column column-columnname">' . __( 'Limit Completion', 'text-domain' ) . '</th>
		                <th class="manage-column column-columnname">' . __( 'Order Details', 'text-domain' ) . '</th>
		            </tr>
		        </thead>' 
		        . $output 
		    . '</table>';

	 ?>
<?php }

/**
 * Dispaly plugin's settings
 */
function woo_pm_preorder() {
	// settings will comes here
}
add_action( 'admin_init', 'woo_pm_preorder' );

/**
 * Dispaly plugin admin menu
 */
function woo_pm_admin_menu() {
	add_menu_page('Woo pre-order mailer', 'Preorder Settings', 'administrator', 'woo_pm_preorder', 'woo_pm_preorder_admin', 'dashicons-admin-generic');
}
add_action('admin_menu', 'woo_pm_admin_menu');
