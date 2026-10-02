<?php
/**
 * File containing the global functions for content/treemenu
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

// eZUpdateDebugSettings() is shared with the other front controllers; the tree
// menu kernel selects its variant (leave eZDebug alone) with eZDebugSettingsMode( 'none' ).
require_once __DIR__ . '/debug_settings_functions.php';
