<?php
/**
 * File containing the expUpgradeStepPdfExportFooter class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The footer wording of a PDF export: ezpdf_export.show_footer and footer_text.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepPdfExportFooter extends expUpgradeSchemaStep
{
    public function id()
    {
        return '6.0.15-pdf-export-footer';
    }

    public function description()
    {
        return 'The footer wording of a PDF export: ezpdf_export.show_footer and footer_text';
    }

    public function fields()
    {
        return array( 'ezpdf_export' => array( 'show_footer', 'footer_text' ) );
    }
}
