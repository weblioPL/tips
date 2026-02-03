<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Tips_Database {

    private static $table_name;

    public static function init() {
        global $wpdb;
        self::$table_name = $wpdb->prefix . 'tips';
    }

    public static function get_table_name() {
        global $wpdb;
        return $wpdb->prefix . 'tips';
    }

    public static function activate() {
        global $wpdb;

        $table_name      = $wpdb->prefix . 'tips';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            created_at datetime NOT NULL,
            name varchar(255) NOT NULL,
            surname varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            amount decimal(10,2) NOT NULL,
            p1 text,
            p2 text,
            p3 text,
            PRIMARY KEY (id),
            KEY created_at (created_at),
            KEY email (email)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );

        update_option( 'tips_summary_db_version', TIPS_SUMMARY_VERSION );
    }

    public static function insert_tip( $data ) {
        global $wpdb;

        $table_name = self::get_table_name();

        $result = $wpdb->insert(
            $table_name,
            array(
                'created_at' => $data['created_at'],
                'name'       => $data['name'],
                'surname'    => $data['surname'],
                'email'      => $data['email'],
                'amount'     => $data['amount'],
                'p1'         => $data['p1'],
                'p2'         => $data['p2'],
                'p3'         => $data['p3'],
            ),
            array(
                '%s',
                '%s',
                '%s',
                '%s',
                '%f',
                '%s',
                '%s',
                '%s',
            )
        );

        return $result !== false ? $wpdb->insert_id : false;
    }

    public static function get_total_sum() {
        global $wpdb;

        $table_name = self::get_table_name();
        $sum        = $wpdb->get_var( "SELECT SUM(amount) FROM $table_name" );

        return $sum ? floatval( $sum ) : 0;
    }

    public static function get_donors_count() {
        global $wpdb;

        $table_name = self::get_table_name();
        $count      = $wpdb->get_var( "SELECT COUNT(DISTINCT email) FROM $table_name" );

        return $count ? intval( $count ) : 0;
    }

    public static function get_last_tips( $limit = 5 ) {
        global $wpdb;

        $table_name = self::get_table_name();
        $limit      = intval( $limit );

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT created_at, name, amount FROM $table_name ORDER BY created_at DESC LIMIT %d",
                $limit
            ),
            ARRAY_A
        );

        return $results ? $results : array();
    }

    public static function get_all_tips() {
        global $wpdb;

        $table_name = self::get_table_name();

        $results = $wpdb->get_results(
            "SELECT * FROM $table_name ORDER BY created_at DESC",
            ARRAY_A
        );

        return $results ? $results : array();
    }
}
