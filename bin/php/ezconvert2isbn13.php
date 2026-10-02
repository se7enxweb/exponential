#!/usr/bin/env php
<?php
/**
 * File containing the ezconvert2isbn13.php script.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/*!
  \brief Converts ISBN-10 numbers to ISBN-13.

  The script should be run by command line with example:

  php bin/php/ezconvert2isbn13.php --all-classes

  Depending on the parameter, the script will search through contentobjects and convert
  ezisbn values in content attributes from ISBN-10 to ISBN-13. The script will also set the hyphen on the correct
  place as well. You should set the class attribute to ISBN-13 in the contentclass before running
  this script or add the flag --force as a parameter when you're running the script.

  When --force is used, the is ISBN-13 will also be updated to ISBN-13 at the
  contentclass level.

  Example:
  --class-id=2 Will Go through all ezisbn attributes in the class with id 2 and convert everyone which is a
               ISBN-10 value.
  --attribute-id=12 will check if this is an ISBN datatype and convert all ISBN-13 values in the
                    attribute with id 12.
  --all-classes Does not have any argument, and converts all contentobject attributes that is set to ISBN-13.

  --force or -f will work in addition to all the options above and set the class attribute to ISBN-13, even if it was
                ISBN-10 before.
*/

set_time_limit( 0 );

require_once 'autoload.php';

// The code is in kernel/private/classes/commands/ezconvert2isbn13.php (#207); this file is the entry point.
\Exponential\Command\Kernel\Ezconvert2isbn13::main( __FILE__ );
