<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$product_id = isset( $_GET['product_id'] ) ? intval( $_GET['product_id'] ) : 0;
$product = $product_id ? wc_get_product( $product_id ) : null;

$title = $product ? $product->get_name() : '';
$price = $product ? $product->get_regular_price() : '';
$quantity = $product ? $product->get_stock_quantity() : '';
$description = $product ? $product->get_description() : '';
$image_id = $product ? $product->get_image_id() : '';
?>

<form method="post" enctype="multipart/form-data">
    <?php wp_nonce_field( 'add_product', 'add_product_nonce' ); ?>

    <?php if ( $product_id ) : ?>
        <input type="hidden" name="product_id" value="<?php echo esc_attr( $product_id ); ?>">
    <?php endif; ?>

    <p>
        <label for="product_title"><?php _e( 'Product Title', 'product-manager' ); ?></label>
        <input type="text" name="product_title" id="product_title" value="<?php echo esc_attr( $title ); ?>" required>
    </p>

    <p>
        <label for="product_price"><?php _e( 'Product Price', 'product-manager' ); ?></label>
        <input type="number" name="product_price" id="product_price" value="<?php echo esc_attr( $price ); ?>" required>
    </p>

    <p>
        <label for="product_quantity"><?php _e( 'Product Quantity', 'product-manager' ); ?></label>
        <input type="number" name="product_quantity" id="product_quantity" value="<?php echo esc_attr( $quantity ); ?>" required>
    </p>

    <p>
        <label for="product_description"><?php _e( 'Product Description', 'product-manager' ); ?></label>
        <?php wp_editor( $description, 'product_description' ); ?>
    </p>

    <p>
        <label for="product_image"><?php _e( 'Product Image', 'product-manager' ); ?></label>
        <input type="text" name="product_image_url" id="product_image_url" value="<?php echo esc_url( wp_get_attachment_url( $image_id ) ); ?>" readonly />
        <button type="button" id="upload_image_button"><?php _e( 'Upload Image', 'product-manager' ); ?></button>
        <?php if ( $image_id ) : ?>
            <img src="<?php echo wp_get_attachment_url( $image_id ); ?>" alt="<?php _e( 'Current Product Image', 'product-manager' ); ?>" style="max-width: 100px; margin-top: 10px;">
        <?php endif; ?>
    </p>

    <p>
        <button type="submit"><?php echo $product_id ? __( 'Update Product', 'product-manager' ) : __( 'Save Product', 'product-manager' ); ?></button>
    </p>
</form>

<script type="text/javascript">
    jQuery(document).ready(function($) {
        var media_uploader;

        $('#upload_image_button').click(function(e) {
            e.preventDefault();

            if (media_uploader) {
                media_uploader.open();
                return;
            }

            media_uploader = wp.media.frames.file_frame = wp.media({
                title: '<?php _e( 'Select Product Image', 'product-manager' ); ?>',
                button: {
                    text: '<?php _e( 'Use this image', 'product-manager' ); ?>'
                },
                multiple: false,
                library: {
                    author: <?php echo get_current_user_id(); ?>
                }
            });

            media_uploader.on('select', function() {
                var attachment = media_uploader.state().get('selection').first().toJSON();
                $('#product_image_url').val(attachment.url);
            });

            media_uploader.open();
        });
    });
</script>












