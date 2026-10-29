<?php
/**
 * @description Convert the Smarty templates (.stpl) of a design to Exponential templates (.tpl); --dry-run, --file, --force, --override-ini
 * @alias exp:template:convert:stpl-to-tpl
 *
 * File containing the exp:template:convert:stpl-to-tpl command (bin/php/exptemplateconvertstpltotpl.php): the general
 * command exp:template:convert with --from-engine=stpl --to-engine=tpl. The work is done by expTemplateDesignConverter
 * (lib/templatebridge/classes/) and expSmartyReverseConverter (lib/smarty/classes/); guide:
 * doc/eng-us/guides/template-conversion.md
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Exptemplateconvertstpltotpl extends \Exponential\Runnable\Command
{
    /** The engines of this command. */
    const FROM_ENGINE = 'stpl';
    const TO_ENGINE = 'tpl';

    public function run()
    {
        $this->script( array( 'description' => "Convert Smarty templates (.stpl) to Exponential templates (.tpl)\n" .
                                               "Every *.stpl below --from becomes the *.tpl at the same place below --to, converted by\n" .
                                               "expSmartyReverseConverter: what the tpl -> stpl conversion writes, and common hand-written Smarty.\n" .
                                               "What has no exact .tpl form is kept as a {* SMARTY ... *} comment in the .tpl and reported as a NOTE.\n" .
                                               "--override-ini writes the -s siteaccess's override rules with MatchFile pointing at the converted .tpl.\n" .
                                               "The same as exp:template:convert --from-engine=stpl --to-engine=tpl.\n" .
                                               "\n" .
                                               "./console exp:template:convert:stpl-to-tpl --from=extension/<ext>/design/<design> --to=var/tmp/<design>\n" .
                                               "./console exp:template:convert:stpl-to-tpl --from=extension/<ext>/design/<design> --dry-run\n" .
                                               "./console exp:template:convert:stpl-to-tpl --file=design/<design>/templates/<name>.stpl\n" .
                                               "./console exp:template:convert:stpl-to-tpl -s <siteaccess> --from=<design> --to=<design> --override-ini=<file>\n" .
                                               "\n" .
                                               "Every written file ends with a marker comment ({* exp:template:convert stpl->tpl md5:... from ... *});\n" .
                                               "a target that has no marker, or was changed after it was written, is skipped (SKIP) unless --force\n" .
                                               "is given. The result is checked before it is written: a .tpl must parse. Prints WRITE or REPLACE for\n" .
                                               "every file written, NOTE for every construct that could not be converted exactly, ERROR for a\n" .
                                               "template that could not be converted, then PASS or FAIL with the counts.\n" .
                                               "Exit code: 0 PASS; 1 FAIL (a template could not be read, converted, checked or written); 2 wrong options.",
                              'use-session' => false,
                              'use-modules' => true,
                              'use-extensions' => true ) );
        $options = $this->startup( '[from:][to:][file:][override-ini:][dry-run][force]', '', array(
            'from' => 'design directory with the .stpl templates (every .stpl below it is converted)',
            'to' => 'design directory for the .tpl templates (default: the --from directory, next to the sources)',
            'file' => 'convert this one .stpl only (below --from, or a file of its own: then --to is the target file)',
            'override-ini' => 'override.ini.append.php to write for the --to design (the -s siteaccess\'s override rules, MatchFile pointing at the converted .tpl)',
            'dry-run' => 'show what would be written; nothing is written',
            'force' => 'also replace a .tpl that was changed or written by hand (no marker, or not matching it)',
        ) );
        $this->shutdown( \expTemplateDesignConverter::runCommand( $options, $this->cli(), self::FROM_ENGINE, self::TO_ENGINE ) );
    }
}

}
