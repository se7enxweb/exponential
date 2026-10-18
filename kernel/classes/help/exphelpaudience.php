<?php
/**
 * The audiences of a help page (workstream B): front matter, then [AudienceSettings] PathRules[], then heuristics,
 * then the type's default. Skeleton: B replaces the bodies.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expHelpAudience
{
    const EDITOR = 'editor';
    const ADMIN = 'admin';
    const DEVELOPER = 'developer';
    const USER = 'user';

    /** @return string[] */
    public static function all() { return array( self::EDITOR, self::ADMIN, self::DEVELOPER, self::USER ); }

    /**
     * @param string $path repo-relative path
     * @param array $frontMatter
     * @param string $type
     * @param string $text
     * @return string[]
     */
    public static function forPage( $path, array $frontMatter, $type = '', $text = '' ) { return array( self::DEVELOPER ); }

    /** @param string $audience @return string translated name */
    public static function label( $audience ) { return (string)$audience; }
}

?>
