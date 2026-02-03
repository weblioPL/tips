<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Tips_Shortcodes {

    public static function init() {
        add_shortcode( 'tips_sum', array( __CLASS__, 'shortcode_sum' ) );
        add_shortcode( 'tips_last', array( __CLASS__, 'shortcode_last' ) );
        add_shortcode( 'tips_donors', array( __CLASS__, 'shortcode_donors' ) );
    }

    public static function shortcode_sum( $atts ) {
        $sum = Tips_Database::get_total_sum();
        return '<span class="tips-sum">' . esc_html( number_format( $sum, 2 ) ) . '</span>';
    }

    public static function shortcode_donors( $atts ) {
        $count = Tips_Database::get_donors_count();
        return '<span class="tips-donors">' . esc_html( $count ) . '</span>';
    }

    public static function shortcode_last( $atts ) {
        $atts = shortcode_atts(
            array(
                'limit' => 5,
            ),
            $atts,
            'tips_last'
        );

        $limit = intval( $atts['limit'] );

        if ( $limit < 3 ) {
            $limit = 3;
        }

        if ( $limit > 10 ) {
            $limit = 10;
        }

        $tips = Tips_Database::get_last_tips( $limit );

        if ( empty( $tips ) ) {
            return '<div class="tips-last-empty"></div>';
        }

        $output = '<ul class="tips-last-list">';

        foreach ( $tips as $tip ) {
            $date   = date_i18n( get_option( 'date_format' ), strtotime( $tip['created_at'] ) );
            $name   = esc_html( $tip['name'] );
            $amount = esc_html( number_format( floatval( $tip['amount'] ), 2 ) );

            $output .= sprintf(
                '<li class="tips-last-item"><span class="tips-date">%s</span> <span class="tips-name">%s</span> <span class="tips-amount">%s</span></li>',
                $date,
                $name,
                $amount
            );
        }

        $output .= '</ul>';

        return $output;
    }
}
