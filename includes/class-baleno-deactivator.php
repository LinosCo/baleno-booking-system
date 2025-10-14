<?php
/**
 * Fired during plugin deactivation
 */
class Baleno_Deactivator {

    public static function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }
}
