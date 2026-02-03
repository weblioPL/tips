<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Tips_Admin {

    public static function init() {
        add_action( 'admin_menu', array( __CLASS__, 'add_admin_menu' ) );
        add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_styles' ) );
    }

    public static function add_admin_menu() {
        add_menu_page(
            __( 'Tips', 'tips-summary' ),
            __( 'Tips', 'tips-summary' ),
            'manage_options',
            'tips-summary',
            array( __CLASS__, 'render_admin_page' ),
            'dashicons-heart',
            30
        );
    }

    public static function enqueue_styles( $hook ) {
        if ( 'toplevel_page_tips-summary' !== $hook ) {
            return;
        }

        wp_add_inline_style(
            'wp-admin',
            '
            .tips-admin-wrap { max-width: 1200px; }
            .tips-shortcodes { background: #fff; padding: 20px; margin-bottom: 20px; border: 1px solid #ccd0d4; }
            .tips-shortcodes h3 { margin-top: 0; }
            .tips-shortcodes code { background: #f0f0f1; padding: 4px 8px; display: inline-block; margin: 4px 0; }
            .tips-shortcodes ul { margin-left: 20px; }
            .tips-table-wrap { background: #fff; padding: 20px; border: 1px solid #ccd0d4; overflow-x: auto; }
            .tips-table { width: 100%; border-collapse: collapse; }
            .tips-table th, .tips-table td { padding: 10px; text-align: left; border-bottom: 1px solid #ccd0d4; }
            .tips-table th { background: #f0f0f1; font-weight: 600; }
            .tips-table tr:hover { background: #f9f9f9; }
            .tips-empty { padding: 40px; text-align: center; color: #666; }
            '
        );
    }

    public static function render_admin_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $tips = Tips_Database::get_all_tips();
        ?>
        <div class="wrap tips-admin-wrap">
            <h1><?php echo esc_html__( 'Tips', 'tips-summary' ); ?></h1>

            <div class="tips-shortcodes">
                <h3><?php echo esc_html__( 'Available Shortcodes', 'tips-summary' ); ?></h3>
                <ul>
                    <li>
                        <code>[tips_sum]</code>
                        <?php echo esc_html__( '- Displays the total sum of all tips', 'tips-summary' ); ?>
                    </li>
                    <li>
                        <code>[tips_last limit="5"]</code>
                        <?php echo esc_html__( '- Displays a list of last tips (limit: 3-10, default: 5)', 'tips-summary' ); ?>
                    </li>
                    <li>
                        <code>[tips_donors]</code>
                        <?php echo esc_html__( '- Displays the number of unique donors', 'tips-summary' ); ?>
                    </li>
                </ul>
            </div>

            <div class="tips-table-wrap">
                <h3><?php echo esc_html__( 'Tips List', 'tips-summary' ); ?></h3>

                <?php if ( empty( $tips ) ) : ?>
                    <div class="tips-empty">
                        <?php echo esc_html__( 'No tips recorded yet.', 'tips-summary' ); ?>
                    </div>
                <?php else : ?>
                    <table class="tips-table">
                        <thead>
                            <tr>
                                <th><?php echo esc_html__( 'Date', 'tips-summary' ); ?></th>
                                <th><?php echo esc_html__( 'Name', 'tips-summary' ); ?></th>
                                <th><?php echo esc_html__( 'Surname', 'tips-summary' ); ?></th>
                                <th><?php echo esc_html__( 'Email', 'tips-summary' ); ?></th>
                                <th><?php echo esc_html__( 'Amount', 'tips-summary' ); ?></th>
                                <th><?php echo esc_html__( 'P1', 'tips-summary' ); ?></th>
                                <th><?php echo esc_html__( 'P2', 'tips-summary' ); ?></th>
                                <th><?php echo esc_html__( 'P3', 'tips-summary' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ( $tips as $tip ) : ?>
                                <tr>
                                    <td><?php echo esc_html( date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $tip['created_at'] ) ) ); ?></td>
                                    <td><?php echo esc_html( $tip['name'] ); ?></td>
                                    <td><?php echo esc_html( $tip['surname'] ); ?></td>
                                    <td><?php echo esc_html( $tip['email'] ); ?></td>
                                    <td><?php echo esc_html( number_format( floatval( $tip['amount'] ), 2 ) ); ?></td>
                                    <td><?php echo esc_html( $tip['p1'] ); ?></td>
                                    <td><?php echo esc_html( $tip['p2'] ); ?></td>
                                    <td><?php echo esc_html( $tip['p3'] ); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
}
