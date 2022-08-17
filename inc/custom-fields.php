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
  
    

// image field
add_action( 'add_meta_boxes', 'listing_image_add_metabox' );
function listing_image_add_metabox () {
	add_meta_box( 'listingimagediv', __( 'Listing Image', 'text-domain' ), 'listing_image_metabox', 'product', 'side', 'low');
}

function listing_image_metabox ( $post ) {
	global $content_width, $_wp_additional_image_sizes;

	$image_id = get_post_meta( $post->ID, '_listing_image_id', true );

	$old_content_width = $content_width;
	$content_width = 254;

	if ( $image_id && get_post( $image_id ) ) {

		if ( ! isset( $_wp_additional_image_sizes['post-thumbnail'] ) ) {
			$thumbnail_html = wp_get_attachment_image( $image_id, array( $content_width, $content_width ) );
		} else {
			$thumbnail_html = wp_get_attachment_image( $image_id, 'post-thumbnail' );
		}

		if ( ! empty( $thumbnail_html ) ) {
			$content = $thumbnail_html;
			$content .= '<p class="hide-if-no-js"><a href="javascript:;" id="remove_listing_image_button" >' . esc_html__( 'Remove listing image', 'text-domain' ) . '</a></p>';
			$content .= '<input type="hidden" id="upload_listing_image" name="_listing_cover_image" value="' . esc_attr( $image_id ) . '" />';
		}

		$content_width = $old_content_width;
	} else {

		$content = '<img src="" style="width:' . esc_attr( $content_width ) . 'px;height:auto;border:0;display:none;" />';
		$content .= '<p class="hide-if-no-js"><a title="' . esc_attr__( 'Set listing image', 'text-domain' ) . '" href="javascript:;" id="upload_listing_image_button" id="set-listing-image" data-uploader_title="' . esc_attr__( 'Choose an image', 'text-domain' ) . '" data-uploader_button_text="' . esc_attr__( 'Set listing image', 'text-domain' ) . '">' . esc_html__( 'Set listing image', 'text-domain' ) . '</a></p>';
		$content .= '<input type="hidden" id="upload_listing_image" name="_listing_cover_image" value="" />';

	}

	echo $content;
}

add_action( 'save_post', 'listing_image_save', 10, 1 );
function listing_image_save ( $post_id ) {
	if( isset( $_POST['_listing_cover_image'] ) ) {
		$image_id = (int) $_POST['_listing_cover_image'];
		update_post_meta( $post_id, '_listing_image_id', $image_id );
	}
}
add_action( 'admin_print_scripts', function() {
    // I'm using NOWDOC notation to allow line breaks and unescaped quotation marks.
    echo <<<'EOT'
<script type="text/javascript">
jQuery(document).ready(function($) {

	// Uploading files
	var file_frame;

	jQuery.fn.upload_listing_image = function( button ) {
		var button_id = button.attr('id');
		var field_id = button_id.replace( '_button', '' );

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
		  file_frame.open();
		  return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
		  title: jQuery( this ).data( 'uploader_title' ),
		  button: {
		    text: jQuery( this ).data( 'uploader_button_text' ),
		  },
		  multiple: false
		});

		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
		  var attachment = file_frame.state().get('selection').first().toJSON();
		  jQuery("#"+field_id).val(attachment.id);
		  jQuery("#listingimagediv img").attr('src',attachment.url);
		  jQuery( '#listingimagediv img' ).show();
		  jQuery( '#' + button_id ).attr( 'id', 'remove_listing_image_button' );
		  jQuery( '#remove_listing_image_button' ).text( 'Remove listing image' );
		});

		// Finally, open the modal
		file_frame.open();
	};

	jQuery('#listingimagediv').on( 'click', '#upload_listing_image_button', function( event ) {
		event.preventDefault();
		jQuery.fn.upload_listing_image( jQuery(this) );
	});

	jQuery('#listingimagediv').on( 'click', '#remove_listing_image_button', function( event ) {
		event.preventDefault();
		jQuery( '#upload_listing_image' ).val( '' );
		jQuery( '#listingimagediv img' ).attr( 'src', '' );
		jQuery( '#listingimagediv img' ).hide();
		jQuery( this ).attr( 'id', 'upload_listing_image_button' );
		jQuery( '#upload_listing_image_button' ).text( 'Set listing image' );
	});

});
</script>
EOT;
}, PHP_INT_MAX );
?>