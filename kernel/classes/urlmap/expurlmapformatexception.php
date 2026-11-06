<?php
/**
 * File containing the expUrlMapFormatException class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * A URL map file that cannot be read or written as a whole: an unknown format, JSON that does not parse, a spreadsheet
 * that is not one, a missing PHP extension. A single bad row is not an exception; it is a row with problems.
 */
class expUrlMapFormatException extends RuntimeException
{
}
