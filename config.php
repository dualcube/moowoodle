<?php

define( 'MOOWOODLE_PLUGIN_TOKEN', 'moowoodle' );
define( 'MOOWOODLE_PLUGIN_VERSION', '3.2.10' );
define( 'MOOWOODLE_PLUGIN_SERVER_URL', 'http://plugins.dualcube.com' );
define( 'MOOWOODLE_MOODLE_PLUGIN_URL', '/auth/moowoodle/login.php?data=' );
define( 'MOOWOODLE_REST_NAMESPACE', 'moowoodle/v1' );

define( 'MOOWOODLE_PRO_SHOP_URL', 'https://dualcube.com/product/moowoodle-pro/' );
define( 'MOOWOODLE_SETUP_URL', 'https://dualcube.com/docs/moowoodle-set-up-guide/' );
define( 'MOOWOODLE_SUPPORT_URL', 'https://wordpress.org/support/plugin/moowoodle/' );
define( 'MOOWOODLE_DUALCUBE_URL', 'http://dualcube.com' );

define( 'EMU2_I18N_DOMAIN', 'moowoodle' );
define( 'MOOWOODLE_LOGS_DIR', ( trailingslashit( wp_upload_dir(null, false)['basedir'] ) . 'mw-logs' ) );

define( 'MOOWOODLE_PLUGIN_URL', trailingslashit( plugins_url( '', __FILE__ ) ) );
define( 'MOOWOODLE_PRO_ADV', ! defined( 'MOOWOODLE_PRO_PLUGIN_TOKEN' ) );
define( 'MOOWOOLE_PRO_STICKER', MOOWOODLE_PRO_ADV ? '<span class="mw-pro-tag" style="font-size: 0.5rem; background: #e35047; padding: 0.125rem 0.5rem; color: #F9F8FB; font-weight: 700; line-height: 1.1; position: absolute; border-radius: 2rem 0; right: -0.75rem; top: 50%; transform: translateY(-50%)">Pro</span>' : '' );

define( 'MOOWOODLE_SCRIPT_DEBUG', true );