<?php
/**
 * File containing the expNamePatternResolverInterface interface.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 * Portions derived from LoveStack (mugoweb/ezpublish-legacy, commit 95d86f5990), Copyright (C) Mugo Web, GPL v2
 */

/**
 * A PHP class that makes the name of a content object, or the name its URL alias is made from, instead of the
 * attribute tokens of the class's name pattern.
 *
 * A content class uses it when its object name pattern (or URL alias name pattern) starts with the class name in
 * braces, for example "{myNameResolver}" or "{myNameResolver} <title>". The class must be listed in
 * content.ini [NamePatternSettings] ResolverClasses[] and implement this interface; any other name in braces is
 * ignored, logged, and the pattern is resolved as usual. Classes are never made from a name that is not listed.
 *
 * The resolver is constructed with one argument, the rest of the pattern after "{ClassName}" (may be empty).
 * It is made for each name, so it should keep no state between calls.
 *
 * @package kernel
 */
interface expNamePatternResolverInterface
{
    /**
     * Returns the name of $object.
     *
     * @param eZContentObject $object
     * @param int|false $version the version to read, false for the current one
     * @param string|false $translation the language code to read, false for the default
     * @param int|false $length the longest name the caller can store (the kernel shortens a longer one again)
     * @param string $sequence the text the kernel ends a shortened name with
     * @return string
     */
    public function resolveNamePattern( eZContentObject $object, $version = false, $translation = false,
                                        $length = false, $sequence = '' );
}
