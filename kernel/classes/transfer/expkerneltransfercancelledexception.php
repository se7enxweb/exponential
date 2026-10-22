<?php
/**
 * File containing the expKernelTransferCancelledException class.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * Thrown by a transfer task (expKernelTransferTask) that stopped because its run was cancelled
 * (expKernelTransferContext::throwIfCancelled()); the run ends as cancelled, not failed.
 *
 * @since 6.0.15
 * @package kernel
 */
class expKernelTransferCancelledException extends RuntimeException
{
}
