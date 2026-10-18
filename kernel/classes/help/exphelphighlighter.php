<?php
/**
 * Server-side syntax highlighting for the help's code blocks (workstream A): escaped code wrapped in
 * <span class="a4-help-tok-<kind>"> (keyword, string, comment, number, variable, tag, attr, ...). Skeleton.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpHighlighter
{
    /**
     * @param string $code
     * @param string $language php, ini, tpl, bash, js, css, json, sql, xml, yaml, ... ('' = plain)
     * @return string HTML, always escaped
     */
    public static function highlight( $code, $language = '' ) { return htmlspecialchars( (string)$code, ENT_QUOTES, 'UTF-8' ); }
}

?>
