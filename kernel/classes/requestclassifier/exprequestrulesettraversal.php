<?php
/**
 * File containing the expRequestRuleSetTraversal class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Category "traversal": a path that tries to climb out of the web root (../, ..\), encoded (%2e%2e, any case) or
 * double-encoded (%252e), and null bytes (%00). Matched against the raw request URI (path and query, as sent) and
 * then against it decoded once.
 *
 * @package kernel
 */
class expRequestRuleSetTraversal extends expRequestPatternRuleSet
{
    public function category()
    {
        return 'traversal';
    }

    protected function builtInPatterns()
    {
        return array(
            'double-encoded' => '%252e(?:%252e|\.)|\.%252e|%25(?:2f|5c)(?:\.|%252e)|\.\.%25(?:2f|5c)|%2500',
            'null-byte' => '%00|\x00',
            'encoded-dot-dot' => '%2e(?:%2e|\.)|\.%2e|\.\.%(?:2f|5c)|%(?:2f|5c)\.\.',
            'dot-dot' => '(?:^|[/\\\\=?&])\.\.(?:[/\\\\]|$)',
        );
    }

    protected function subjects( expRequestContext $context )
    {
        $decoded = $context->decodedPath();
        $query = $context->decodedQuery();
        return array( $context->uri, $decoded . ( $query !== '' ? '?' . $query : '' ) );
    }

    protected function excludeSubject( expRequestContext $context )
    {
        return '';
    }
}
