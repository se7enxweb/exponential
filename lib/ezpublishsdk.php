<?php
/**
 * File containing the eZPublishSDK class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @package lib
 */

/**
 * Former name of ExponentialSDK (lib/version.php), kept for compatibility.
 *
 * Extensions and site code that call eZPublishSDK::version(), read
 * eZPublishSDK::VERSION_MAJOR or test class_exists( 'eZPublishSDK' ) keep
 * working unchanged: every constant and static method is inherited from
 * ExponentialSDK and returns the same value.
 *
 * @deprecated since 6.0.15, use ExponentialSDK instead.
 */
class eZPublishSDK extends ExponentialSDK
{
}

?>
