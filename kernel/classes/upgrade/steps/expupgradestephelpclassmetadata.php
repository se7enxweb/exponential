<?php
/**
 * File containing the expUpgradeStepHelpClassMetaData class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The help classes (help_manual ... help_glossary_entry) as every other content class: the Meta Data attribute
 * (metadata, xrowmetadata, category meta, GPL-2.0-or-later, license and Open Graph image chosen per object) and no
 * title field below 255 characters, through expHelpContentMetaData as ./console exp:help:content install does for
 * a new installation. Not applicable without help classes. The attribute is part of the step only when xrowmetadata
 * is active and can be limited to these classes (1.4.24 or later); the title fields always are.
 *
 * @since 6.0.15
 * @package kernel
 */
class expUpgradeStepHelpClassMetaData extends expUpgradeScriptStep
{
    public function id()
    {
        return '6.0.15-help-class-metadata';
    }

    public function description()
    {
        return 'The help classes get the Meta Data attribute (xrowmetadata) and title fields of at least 255 characters';
    }

    protected function changes( expUpgradeContext $context )
    {
        $state = expHelpContentMetaData::state();
        $exists = false;
        foreach ( $state as $entry )
            $exists = $exists || !empty( $entry['exists'] );
        if ( !$exists )
        {
            $this->notApplicable = true;
            $this->reason = 'no help classes (exp:help:content install has not run)';
            return null;
        }
        list( $provider, $reason ) = expHelpContentMetaData::provider();
        return expHelpContentMetaData::changes( $state, $provider !== null );
    }

    protected function presentDetail()
    {
        list( $provider, $reason ) = expHelpContentMetaData::provider();
        return $provider !== null ? 'in place' : 'title fields in place; Meta Data attribute not part of the step: ' . $reason;
    }

    protected function write( expUpgradeContext $context )
    {
        return expHelpContentMetaData::apply();
    }
}
