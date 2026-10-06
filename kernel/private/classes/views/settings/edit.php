<?php
/**
 * The code of kernel/settings/edit.php, moved into a class (#207 stage 1). The file kernel/settings/edit.php is one call to it.
 * Guide: doc/bc/6.0/cli_cronjob_view_abstractions.md
 *
 * Every name in the request is checked against the installation's own lists before anything is read or written
 * (expSettingsTarget): the INI file against the files the settings page lists, the siteaccess against
 * RelatedSiteAccessList, the place to write against siteaccess, override and the active extensions, the block and
 * the setting name against what eZINI reads back; read-only settings (site.ini [eZINISettings]
 * ReadonlySettingList) are refused. eZINI::save() refuses a value that would break out of its line or out of the
 * PHP comment of a .ini.append.php file. After a write the INI cache is cleared (expSettingsPage::afterWrite()),
 * so PHP-FPM and Velocity both read the change on their next request. User guide: doc/guides/settings-page.md
 */
/*
 * The original header of kernel/settings/edit.php:
 *
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 * @package kernel
 *
 */

namespace
{
if ( !function_exists( 'parseArrayToStr' ) ) {
function parseArrayToStr( $value, $separator )
{
    if ( !is_array( $value ) )
        return $value;

    $valueArray = array();

    foreach( $value as $param=>$key )
    {
        if ( !is_numeric( $param ) )
        {
            $valueArray[] = "[$param]=$key";
        }
        else
        {
            $valueArray[] = "=$key";
        }
    }

    $value = implode( $separator, $valueArray );
    return $value;
}
}
}

namespace Exponential\View\Kernel\Settings
{

class Edit extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        // the including function's variables ($Params, $Module, $cli, ...)
        foreach ( array_keys( $scope ) as $__name )
            if ( $__name !== 'this' && $__name !== 'scope' )
                ${$__name} = &$scope[$__name];
        unset( $__name );

        $settingTypeArray = array( 'array' => 'Array',
                                   'true/false' => 'True/False',
                                   'enable/disable' => 'Enabled/Disabled',
                                   'string' => 'String',
                                   'numeric' => 'Numeric' );

        $tpl = \eZTemplate::factory();
        $http = \eZHTTPTool::instance();
        $siteIni = \eZINI::instance();
        $siteAccessList = array_values( array_map( 'strval', (array)$siteIni->variable( 'SiteAccessSettings', 'RelatedSiteAccessList' ) ) );
        $extensions = \expSettingsPage::activeExtensions();
        $rule = \expSettingsSecretRule::fromIni( $siteIni );

        $post = function ( $name, $default ) use ( $http ) {
            return $http->hasPostVariable( $name ) ? $http->postVariable( $name ) : $default;
        };
        $iniFile = \expSettingsTarget::iniFile( $post( 'INIFile', $Params['INIFile'] ), \expSettingsPage::iniFileList() );
        $siteAccess = \expSettingsTarget::siteAccess( $post( 'SiteAccess', $Params['SiteAccess'] ), $siteAccessList );
        $block = $post( 'Block', $Params['Block'] );
        $block = is_string( $block ) ? trim( $block ) : '';
        $settingName = $post( 'SettingName', $Params['Setting'] );
        $settingName = is_string( $settingName ) ? trim( $settingName ) : '';
        $settingPlacement = $post( 'SettingPlacement', $Params['Placement'] );
        $settingPlacement = is_string( $settingPlacement ) && $settingPlacement !== '' ? trim( $settingPlacement ) : 'siteaccess';
        $settingType = $http->hasPostVariable( 'SettingType' ) ? trim( (string)$http->postVariable( 'SettingType' ) ) : null;
        if ( $settingType !== null && !isset( $settingTypeArray[$settingType] ) )
            $settingType = null;

        // Nothing here works without a file, a siteaccess and a block the installation knows
        if ( $iniFile === null || $siteAccess === null || !\expSettingsTarget::isValidBlock( $block )
             || ( $settingName !== '' && !\expSettingsTarget::isValidName( $settingName ) && !$http->hasPostVariable( 'WriteSetting' ) ) )
        {
            return $this->viewResult( null, $Module->handleError( \eZError::KERNEL_NOT_AVAILABLE, 'kernel' ) );
        }
        $viewURL = '/settings/view/' . $siteAccess . '/' . $iniFile;

        if ( $http->hasPostVariable( 'Cancel' ) )
            return $this->viewResult( null, $Module->redirectTo( $viewURL ) );

        $loaded = \expSettingsPage::chainFor( $iniFile, $siteAccess, false );
        $chain = $loaded['chain'];
        $setting = $settingName !== '' ? $chain->setting( $block, $settingName ) : null;
        $isSecret = $settingName !== '' && $rule->isSecretName( $settingName );

        $tpl->setVariable( 'validation_error', false );
        $tpl->setVariable( 'validation_error_type', false );
        $tpl->setVariable( 'validation_error_message', false );
        $tpl->setVariable( 'validation_field', false );

        if ( $http->hasPostVariable( 'WriteSetting' ) )
        {
            $valueToWrite = $http->hasPostVariable( 'Value' ) ? $http->postVariable( 'Value' ) : '';
            if ( $settingType === null )
                $settingType = 'string';
            // an array keeps its leading empty line (it empties the array before the elements)
            $valueToWrite = is_string( $valueToWrite ) ? ( $settingType === 'array' ? rtrim( $valueToWrite ) : trim( $valueToWrite ) ) : '';
            $path = \expSettingsTarget::writeDirectory( $settingPlacement, $siteAccess, $extensions );
            $error = self::writeError( $path, $iniFile, $block, $settingName, $settingType, $valueToWrite, $loaded['ini'] );

            // A secret's field starts empty: leaving it empty keeps the value that is there
            if ( $error === null && $isSecret && $valueToWrite === '' && $setting !== null )
            {
                \expSettingsPage::afterWrite( 'unchanged', $iniFile, $siteAccess, array( array( 'block' => $block, 'name' => $settingName, 'path' => '' ) ) );
                return $this->viewResult( null, $Module->redirectTo( $viewURL . '#' . \expSettingsPage::anchor( $block . '-' . $settingName ) ) );
            }
            if ( $error === null )
            {
                require_once 'kernel/settings/validation.php';
                // an array may be empty: "leave the first line empty" makes one
                $validationResult = $settingType === 'array'
                    ? validate( array( 'Name' => $settingName ), array( 'name' ), true )
                    : validate( array( 'Name' => $settingName, 'Value' => $valueToWrite ), array( 'name', $settingType ), true );
                if ( $validationResult['hasValidationError'] )
                    $error = array( $validationResult['type'], $validationResult['message'], $validationResult['fieldContainingError'] );
            }
            if ( $error === null )
            {
                $toWrite = $valueToWrite;
                if ( $settingType === 'array' )
                {
                    $parsed = \expSettingsTarget::parseArrayText( $valueToWrite );
                    if ( $parsed['invalid'] )
                        $error = array( 'not_array', 'A key cannot contain [ or ]', 'Value' );
                    $toWrite = $parsed['values'];
                }
            }
            if ( $error === null )
            {
                $ini = new \eZINI( $iniFile . '.append', $path, null, null, null, true, true );
                $ini->setVariable( $block, $settingName, $toWrite );
                if ( $ini->save() )
                {
                    \expSettingsPage::afterWrite( 'saved', $iniFile, $siteAccess,
                        array( array( 'block' => $block, 'name' => $settingName, 'path' => $path . '/' . $iniFile . '.append.php' ) ) );
                    return $this->viewResult( null, $Module->redirectTo( $viewURL . '#' . \expSettingsPage::anchor( $block . '-' . $settingName ) ) );
                }
                // eZINI::save() refuses a line break, a NUL byte or the end of a PHP comment, and fails on permissions
                $error = array( 'write_error', '', 'Value' );
                $tpl->setVariable( 'path', $path );
                $tpl->setVariable( 'filename', $iniFile . '.append.php' );
            }
            $tpl->setVariable( 'validation_error', true );
            $tpl->setVariable( 'validation_error_type', $error[0] );
            $tpl->setVariable( 'validation_error_message', $error[1] );
            $tpl->setVariable( 'validation_field', $error[2] );
            // the form is shown again with what was typed (never for a secret)
            $value = $isSecret ? '' : $valueToWrite;
        }
        else
        {
            $current = $setting !== null ? $setting['value'] : '';
            if ( $settingType === null )
                $settingType = $setting !== null ? $setting['type'] : 'string';
            // an array is shown as it is in effect, so saving it as shown must replace it, not add to it: the
            // empty first line empties the array in the file written before these elements
            $value = $isSecret ? '' : ( is_array( $current ) ? "\n" . parseArrayToStr( $current, "\n" ) : (string)$current );
        }

        // Every file of the chain that sets it, in load order, values as the page may show them
        $steps = $setting !== null ? \expSettingsPage::row( $setting, $rule, array( 'file' => $iniFile ) )['steps'] : array();
        $values = self::locationValues( $setting, $rule, $extensions );

        $tpl->setVariable( 'setting_name', $settingName );
        $tpl->setVariable( 'current_siteaccess', $siteAccess );
        $tpl->setVariable( 'setting_type_array', $settingTypeArray );
        $tpl->setVariable( 'setting_type', $settingType );
        $tpl->setVariable( 'ini_file', $iniFile );
        $tpl->setVariable( 'block', $block );
        $tpl->setVariable( 'value', $value );
        $tpl->setVariable( 'values', $values );
        $tpl->setVariable( 'placement', $settingPlacement );
        // added with the redesign
        $tpl->setVariable( 'is_secret', $isSecret );
        $tpl->setVariable( 'secret_state', $isSecret && $setting !== null ? $rule->maskValue( $setting['value'] )['state'] : '' );
        $tpl->setVariable( 'chain_steps', $steps );
        $tpl->setVariable( 'exists', $setting !== null );
        $tpl->setVariable( 'extensions', $extensions );
        $tpl->setVariable( 'restart', \expSettingsPage::restartNeeded( $iniFile, $block, $settingName, \expSettingsPage::restartList() ) );
        $tpl->setVariable( 'view_url', $viewURL );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:settings/edit.tpl' );
        $Result['path'] = array( array( 'text' => \ezpI18n::tr( 'settings/edit', 'Settings' ),
                                        'url' => false ),
                                 array( 'text' => \ezpI18n::tr( 'settings/edit', 'Edit' ),
                                        'url' => false ) );

        return $this->viewResult( $Result, null );
    }

    /**
     * Why a write cannot be made, or null: the place to write, the setting name, a read-only setting.
     *
     * @return array|null type, message, field
     */
    protected static function writeError( $path, $iniFile, $block, $settingName, $settingType, $value, \eZINI $ini )
    {
        if ( $path === null )
            return array( 'not_valid_placement', 'Choose where to save the setting', 'SettingPlacement' );
        if ( $settingName === '' )
            return array( 'empty', 'Please specify a value', 'Name' );
        if ( !\expSettingsTarget::isValidName( $settingName ) )
            return array( 'not_valid_name', 'Name contains illegal characters', 'Name' );
        // eZINI::isSettingReadOnly() answers true when the setting is NOT read only
        if ( !$ini->isSettingReadOnly( $iniFile, $block, $settingName ) || !$ini->isSettingReadOnly( $iniFile, $block ) )
            return array( 'read_only', 'This setting is read only', 'Name' );
        return null;
    }

    /**
     * The template variable "values" as it has always been (default, siteaccess, override, extensions => name),
     * each what that one place sets, masked as the rule says, as text (array elements one per line).
     *
     * @return array
     */
    protected static function locationValues( $setting, \expSettingsSecretRule $rule, array $extensions )
    {
        $values = array( 'default' => false, 'siteaccess' => false, 'override' => false, 'extensions' => array() );
        foreach ( $extensions as $extension )
            $values['extensions'][$extension] = false;
        if ( $setting === null )
            return $values;
        foreach ( $setting['steps'] as $step )
        {
            $shown = $rule->displayValue( $setting['name'], $step['value'] );
            $text = is_array( $shown ) ? parseArrayToStr( $shown, "\n" ) : (string)$shown;
            $kind = $step['placement']['kind'];
            if ( $kind === 'extension' )
                $values['extensions'][$step['placement']['extension']] = $text;
            else if ( isset( $values[$kind] ) )
                $values[$kind] = $text;
        }
        return $values;
    }
}

}
