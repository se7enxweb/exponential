<?php
/**
 * File containing the expMigrationSiteInstaller class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * eZSiteInstaller for the step types of YAML migrations: its helpers (addClassAttributes, removeClassAttribute,
 * addPoliciesForRole, createContentSection ...) without the settings and steps of a site package, and with the
 * errors a helper reports kept, so a step can name them in its exception.
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationSiteInstaller extends eZSiteInstaller
{
    /** @var string[] the errors the helpers reported */
    public $errors = array();

    function initSettings( $parameters )
    {
        $this->Settings = is_array( $parameters ) ? $parameters : array();
    }

    function initSteps()
    {
        $this->Steps = array();
    }

    function reportError( $text, $caption, $errCode = eZSiteInstaller::ERR_ABORT )
    {
        $this->errors[] = $caption . ': ' . ( is_scalar( $text ) ? (string)$text : json_encode( $text ) );
        parent::reportError( $text, $caption, $errCode );
    }
}
