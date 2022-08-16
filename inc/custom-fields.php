<?php

function add_custom_fields() {

    global $product, $post;
    
    echo '<div class="options_group">'; // Группировка полей.
    
    woocommerce_wp_text_input( array(
    'id'                => '_text_field',
    'label'             => __( 'Date', 'woocommerce' ),
    'placeholder'       => 'Date',
    'desc_tip'          => 'true',
    'type'			 => 'date',
    'custom_attributes' => array( 'required' => 'required' ),
    'description'       => __( 'Pick date', 'woocommerce' ),
    ) );
    woocommerce_wp_select(
        [
            'id'      => '_select',
            'label'   => 'Product type',
            'options' => [
                'rare'   => __( 'rare', 'woocommerce' ),
                'unusual'   => __('unusual', 'woocommerce' ),
                'frequent' => __( 'frequent', 'woocommerce' ),
            ],
        ]
    );
    echo '</div>'; // Группировка полей.

    
    }
    
    add_action( 'woocommerce_product_options_general_product_data', 'add_custom_fields' );
    
    add_action( 'woocommerce_process_product_meta',
    function( $post_id ) {
        $product = wc_get_product( $post_id );  
        $date = $_POST['_text_field'];
        // update_post_meta( $post_id, '_text_field', $date );
        $select = $_POST['_select'];
        update_post_meta( $post_id, '_select', $select );
        $product->save();
    } );
    
?>