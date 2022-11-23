<?php
namespace OffbeatWP\Faker;

use OffbeatWP\Faker\Helpers\DummyHelper;
use OffbeatWP\Services\AbstractService;
use WP_REST_Server;

final class FakerService extends AbstractService {
    public function register()
    {
        $path = 'generate-dummy';
        $isAllowed = (current_user_can('manage_options') && WP_ENV === 'development');

        add_action('rest_api_init', function () use ($path, $isAllowed) {
            register_rest_route('offbeatwp', $path, [
                'methods' => WP_REST_Server::READABLE,
                'callback' => function () {
                    $amount = (int)($_GET['amount'] ?? 0);
                    $type = (string)($_GET['type'] ?? '');

                    if (!$amount || !$type) {
                        return "The 'amount' and 'type' params are required.";
                    }

                    DummyHelper::generatePosts($type, $amount);

                    wp_safe_redirect(get_site_url(null, 'wp-admin/edit.php?post_type=' . $type));
                    exit('Posts have been created');
                },
                'permission_callback' => fn() => $isAllowed
            ]);
        });

        add_action('manage_posts_extra_tablenav', static function (string $which) use ($path, $isAllowed) {
            if ($which === 'bottom' && $isAllowed) {
                $type = $_GET['post_type'] ?? 'post';
                $url = get_site_url(null, '/wp-json/offbeatwp/' . $path) . '?amount=10&type=' . $type;

                echo "&nbsp<div class='float-end' style='margin-left:1rem'>
                    <a href='$url' type='submit' class='button'>Generate 10 dummy posts</a>
                </div> ";
            }
        });
    }
}
