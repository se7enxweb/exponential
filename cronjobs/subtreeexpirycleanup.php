<?php
/**
 * @description Clean up subtree expiry entries from the URL cache tables
 *
 * File containing the subtreeexpirycleanup.php cronjob
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 */

eZSubtreeCache::removeAllExpiryCacheFromDisk();

?>
