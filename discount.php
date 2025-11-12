<?php

/*
Plugin name: Email based discount
Description: Provides discount codes based on email domains.
Version: 0.1
Author: Vilunki
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once plugin_dir_path(__FILE__) . 'admin/admin-page.php';
register_activation_hook(__FILE__, function() {
    if ( ! get_option( 'discount_data' ) ) {
        update_option( 'discount_data', [] );
    }
});

add_action( 'admin_menu', function() {
    add_menu_page(
        'Alennukset',
        'Alennukset',
        'manage_options',
        'alennukset',
        'admin_page_render',
        'dashicons-tag',
        56
    );
});

add_action( 'woocommerce_cart_calculate_fees', function( $cart ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
    if ( ! is_user_logged_in() ) return;

    $user = wp_get_current_user();
    $email = $user->user_email;
    $domain = substr( strrchr( $email, "@" ), 1 );
    $discounts = get_option( 'discount_data', [] );

    foreach ( $discounts as $row ) {
        if ( stripos( $email, $row['email'] ) !== false || stripos( $domain, $row['email'] ) !== false ) {
            $discount = floatval( $row['discount'] );
            $cart->add_fee( 'Alennus: ' . $row['name'], -$cart->get_subtotal() * ($discount / 100), false );
            WC()->customer->update_meta_data( 'customer_type', $row['type'] );
            WC()->customer->update_meta_data( 'payment_terms', $row['terms'] );
            return;
        }
    }
});
