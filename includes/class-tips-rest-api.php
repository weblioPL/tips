<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Tips_REST_API {

    public static function init() {
        add_action( 'rest_api_init', array( __CLASS__, 'register_routes' ) );
    }

    public static function register_routes() {
        register_rest_route(
            'tips/v1',
            '/webhook',
            array(
                'methods'             => 'POST',
                'callback'            => array( __CLASS__, 'handle_webhook' ),
                'permission_callback' => array( __CLASS__, 'permission_callback' ),
            )
        );
    }

    public static function permission_callback( WP_REST_Request $request ) {
        return true;
    }

    public static function handle_webhook( WP_REST_Request $request ) {
        $body = $request->get_json_params();

        if ( empty( $body ) ) {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'message' => 'Invalid JSON payload',
                ),
                400
            );
        }

        $validation = self::validate_payload( $body );

        if ( is_wp_error( $validation ) ) {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'message' => $validation->get_error_message(),
                ),
                400
            );
        }

        $data = self::sanitize_payload( $body );

        $insert_id = Tips_Database::insert_tip( $data );

        if ( false === $insert_id ) {
            return new WP_REST_Response(
                array(
                    'success' => false,
                    'message' => 'Failed to insert tip',
                ),
                400
            );
        }

        return new WP_REST_Response(
            array(
                'success' => true,
                'id'      => $insert_id,
            ),
            200
        );
    }

    private static function validate_payload( $body ) {
        $required_fields = array( 'Createdat', 'Amount', 'Name', 'Surname', 'Email' );

        foreach ( $required_fields as $field ) {
            if ( ! isset( $body[ $field ] ) || ( empty( $body[ $field ] ) && $body[ $field ] !== 0 ) ) {
                return new WP_Error(
                    'missing_field',
                    sprintf( 'Missing required field: %s', $field )
                );
            }
        }

        if ( ! is_numeric( $body['Amount'] ) || floatval( $body['Amount'] ) < 0 ) {
            return new WP_Error(
                'invalid_amount',
                'Amount must be a positive number'
            );
        }

        if ( ! is_email( $body['Email'] ) ) {
            return new WP_Error(
                'invalid_email',
                'Invalid email address'
            );
        }

        $created_at = strtotime( $body['Createdat'] );
        if ( false === $created_at ) {
            return new WP_Error(
                'invalid_date',
                'Invalid date format for Createdat'
            );
        }

        return true;
    }

    private static function sanitize_payload( $body ) {
        $created_at = strtotime( $body['Createdat'] );

        return array(
            'created_at' => gmdate( 'Y-m-d H:i:s', $created_at ),
            'name'       => sanitize_text_field( $body['Name'] ),
            'surname'    => sanitize_text_field( $body['Surname'] ),
            'email'      => sanitize_email( $body['Email'] ),
            'amount'     => floatval( $body['Amount'] ),
            'p1'         => isset( $body['P1'] ) ? sanitize_textarea_field( $body['P1'] ) : null,
            'p2'         => isset( $body['P2'] ) ? sanitize_textarea_field( $body['P2'] ) : null,
            'p3'         => isset( $body['P3'] ) ? sanitize_textarea_field( $body['P3'] ) : null,
        );
    }
}
