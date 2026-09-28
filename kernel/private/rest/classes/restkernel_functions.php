<?php
/**
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

// eZUpdateDebugSettings() is shared with the other front controllers; the REST
// kernel selects its variant (debug output off) with eZDebugSettingsMode( 'rest' ).
require_once __DIR__ . '/../../classes/debug_settings_functions.php';
