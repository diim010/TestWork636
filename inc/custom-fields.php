<?php
if(is_admin()) {
    wp_enqueue_script('jquery-ui-datepicker');
    wp_enqueue_style('jquery-ui-custom', get_template_directory_uri().'/css/jquery-ui-custom.css');
    wp_enqueue_script('custom-js', get_template_directory_uri().'/js/custom-js.js');
}
add_action('admin_head','add_custom_scripts');
function add_custom_scripts() {
    global $custom_meta_fields, $post;
     
    $output = '<script type="text/javascript">
                jQuery(function() {';
                 
    foreach ($custom_meta_fields as $field) { // loop through the fields looking for certain types
        if($field['type'] == 'date')
            $output .= 'jQuery(".datepicker").datepicker();';
    }
     
    $output .= '});
        </script&gt';
         
    echo $output;
}
function add_custom_meta_box() {
    add_meta_box(
        'custom_meta_box', // $id
        'Custom Meta Box', // $title 
        'show_custom_meta_box', // $callback
        'product', // $page
        'normal', // $context
        'high'); // $priority
}
add_action('add_meta_boxes', 'add_custom_meta_box');
// Field Array
$prefix = 'custom_';
$custom_meta_fields = array( 
    array(
        'label' => 'Date',
        'desc'  => 'Start selling date',
        'id'    => $prefix.'date',
        'type'  => 'date'
    ),
    array(
        'name'  => 'Image',
        'desc'  => 'Featured image',
        'id'    => $prefix.'image',
        'type'  => 'image'
    ),
    array(
        'label'=> 'Select Box',
        'desc'  => 'Product type',
        'id'    => $prefix.'select',
        'type'  => 'select',
        'options' => array (
            'one' => array (
                'label' => 'rare',
                'value' => 'rare'
            ),
            'two' => array (
                'label' => 'frequent',
                'value' => 'frequent'
            ),
            'three' => array (
                'label' => 'unusual',
                'value' => 'unusual'
            )
        )
    )
);

// The Callback
function show_custom_meta_box() {
    global $custom_meta_fields, $post;
    // Use nonce for verification
    echo '<input type="hidden" name="custom_meta_box_nonce" value="'.wp_create_nonce(basename(__FILE__)).'" />';
         
        // Begin the field table and loop
        echo '<table class="form-table">';
        foreach ($custom_meta_fields as $field) {
            // get value of this field if it exists for this post
            $meta = get_post_meta($post->ID, $field['id'], true);
            // begin a table row with
            echo '<tr>
                    <th><label for="'.$field['id'].'">'.$field['label'].'</label></th>
                    <td>';
                    switch($field['type']) {
                        // select
                        case 'select':
                            echo '<select name="'.$field['id'].'" id="'.$field['id'].'">';
                            foreach ($field['options'] as $option) {
                                echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="'.$option['value'].'">'.$option['label'].'</option>';
                            }
                            echo '</select><br /><span class="description">'.$field['desc'].'</span>';
                        break;
                        // date
                        case 'date':
                            echo '<input type="date" class="datepicker" name="'.$field['id'].'" id="'.$field['id'].'" value="'.$meta.'" size="30" />
                                    <br /><span class="description">'.$field['desc'].'</span>';
                        break;
                        // image
                        case 'image':
                            echo    '</td><td>';
                            $image = get_template_directory_uri().'/images/image.png';  
                            echo '<span class="custom_default_image" style="display:none">'.$image.'</span>';
                            if ($meta) { $image = wp_get_attachment_image_src($meta, 'medium'); $image = $image[0]; }               
                            echo    '<input name="'.$field['id'].'" type="hidden" class="custom_upload_image" value="'.$meta.'" />
                                        <img src="'.$image.'" class="custom_preview_image" alt="" /><br />
                                            <input class="custom_upload_image_button button" type="button" value="Choose Image" />
                                            <small> <a href="#" class="custom_clear_image_button">Remove Image</a></small>
                                            <br clear="all" /><span class="description">'.$field['desc'].'</span>';
                        break;
                    } //end switch
            echo '</td></tr>';
        } // end foreach
        echo '</table>'; // end table
    }
    // Save the Data
function save_custom_meta($post_id) {
    global $custom_meta_fields;
     
    // verify nonce
    if (!wp_verify_nonce($_POST['custom_meta_box_nonce'], basename(__FILE__))) 
        return $post_id;
    // check autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        return $post_id;
    // check permissions
    if ('product' == $_POST['post_type']) {
        if (!current_user_can('edit_page', $post_id))
            return $post_id;
        } elseif (!current_user_can('edit_post', $post_id)) {
            return $post_id;
    }
     
    // loop through fields and save the data
    foreach ($custom_meta_fields as $field) {
        $old = get_post_meta($post_id, $field['id'], true);
        $new = $_POST[$field['id']];
        if ($new && $new != $old) {
            update_post_meta($post_id, $field['id'], $new);
        } elseif ('' == $new && $old) {
            delete_post_meta($post_id, $field['id'], $old);
        }
    } // end foreach
}
add_action('save_post', 'save_custom_meta');

?>