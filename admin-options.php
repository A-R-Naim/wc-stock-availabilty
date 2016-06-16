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
	$order_result = '<h3>'. esc_html__( 'Total no. of order: ', 'text-domain' ) . $total_order . '</h3>';
	foreach ( $all_orders as $order ) {
		$i++;
		$order_id = $order->order_id;
		$details  = new WC_Order($order_id);

		$order_result .= '<h4>'. esc_html__( 'Order no. ', 'text-domain') . $i . '<h4>';

		$order_result .= '<h5>'. esc_html__( ' Order Details', 'text-domain') . '</h5>';
		$order_result .= esc_html__( 'Name: ', 'text-domain' ) . $details->billing_first_name .' ' . $details->billing_last_name . '<br/>';
		$order_result .= esc_html( 'Email: ', 'text-domain' ) . $details->billing_email . '<br/>';

		$order_result .= esc_html__( 'Quantity: ', 'text-domain' );
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
			$status = esc_html__( 'Pending Payment', 'text-domain' );
		} 
		elseif ( $order_status === 'wc-processing' ) {
			$status = esc_html__( 'Processing', 'text-domain' );
		}
		elseif ( $order_status === 'wc-on-hold' ) {
			$status = esc_html__( 'On Hold', 'text-domain' );
		}
		elseif ( $order_status === 'wc-completed' ) {
			$status = esc_html__( 'Completed', 'text-domain' );
		}
		elseif ( $order_status === 'wc-cancelled' ) {
			$status = esc_html__( 'Cancelled', 'text-domain' );
		}
		elseif ( $order_status === 'wc-refunded' ) {
			$status = esc_html__( 'Refunded', 'text-domain' );
		}
		elseif ( $order_status === 'wc-failed' ) {
			$status = esc_html__( 'Failed', 'text-domain' );
		}	
		$order_result .= esc_html__( 'Order status: ', 'text-domain' ) . $status . '<br>';
		$order_result .= esc_html__( 'Order Date: ', 'text-domain' ) . $details->order_date;;
		
		$order_result .= '<hr>';
	}

	return $order_result;
}

/**
 * Dispaly preorder status
 */
function woo_pm_preorder_admin(){ ?>
	<h1><?php echo esc_html__( 'Preorder Status', 'text-domain' ); ?></h1>
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
			$modal = '<button id="open-modal'.$post->ID.'" value=' . esc_attr__( 'Click Here', 'text-domain' ) . '>
					<div id="modal-content'.$post->ID.'" title=' . esc_attr__( 'Order Details', 'text-domain' ) . ' style="display:none;">'
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
		        <td class="column-columnname">' . $modal . esc_html__( 'Click Here', 'text-domain' ) . '</td>
		    </tr>';
		} );

		echo '<table class="widefat fixed" cellspacing="0">
		        <thead>
		            <tr>
		                <th class="manage-column column-columnname">' . esc_html__( 'Product', 'text-domain' ) . '</th>
		                <th class="manage-column column-columnname">' . esc_html__( 'Preorder Limit', 'text-domain' ) . '</th>
		                <th class="manage-column column-columnname">' . esc_html__( 'Units Ordered', 'text-domain' ) . '</th>
		                <th class="manage-column column-columnname">' . esc_html__( 'Limit Completion', 'text-domain' ) . '</th>
		                <th class="manage-column column-columnname">' . esc_html__( 'Order Details', 'text-domain' ) . '</th>
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
	add_menu_page( esc_html__( 'Woo pre-order mailer', 'text-domain' ) , esc_html__( 'Preorder Settings', 'text-domain' ) , 'administrator', 'woo_pm_preorder', 'woo_pm_preorder_admin', 'dashicons-admin-generic');
}
add_action('admin_menu', 'woo_pm_admin_menu');
