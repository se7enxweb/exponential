<?php
/**
 * File containing the expMigrationStepLanguage class.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

/**
 * The step type "language": adds, renames, ensures and removes languages of content.
 *
 *   - type: language
 *     mode: ensure                     create, update (rename), ensure (add when missing), delete
 *     locale: ger-DE
 *     name: Deutsch
 *
 * A language is added with eZContentLanguage::addLanguage(), as the administration adds one (the locale must be one
 * the installation knows, share/locale/<locale>.ini), and removed only while no object and no class has a
 * translation in it (eZContentLanguage::removeThis()). Which languages a siteaccess shows is a setting
 * (SiteLanguageList), not part of this step. A create undoes itself (the language is removed again while unused).
 *
 * @since 6.0.15
 * @package kernel
 */
class expMigrationStepLanguage extends expMigrationStepStructureBase implements expMigrationPartlyRevertibleStepType
{
    /** @return string */
    public function name()
    {
        return 'language';
    }

    /** @return array */
    public function schema()
    {
        return array(
            'description' => 'Adds, renames, ensures or removes a language of content',
            'modes' => array( 'create', 'update', 'ensure', 'delete' ),
            'keys' => array(
                'locale' => array( 'type' => 'string', 'modes' => array( 'create', 'ensure' ),
                                   'description' => 'the locale of the language (ger-DE); ensure finds the language by it' ),
                'language' => array( 'type' => 'identifier', 'kind' => 'language', 'modes' => array( 'update', 'delete' ),
                                     'description' => 'the language to rename or remove: its locale or id' ),
                'name' => array( 'type' => 'string', 'modes' => array( 'create', 'update', 'ensure' ),
                                 'description' => 'the name of the language (default: the name of the locale)' ),
            ) + self::missingKey( array( 'update', 'delete' ), 'the language' ),
            'values' => array(
                'language_id' => 'the id of the language',
                'locale' => 'its locale',
                'name' => 'its name',
                'created' => 'true when the step added the language',
                'changed' => 'true when the step changed it (added, renamed or removed)',
            ),
            'example' => "- type: language\n  mode: ensure\n  locale: ger-DE\n  name: Deutsch\n",
        );
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function validate( array $step )
    {
        $mode = isset( $step['mode'] ) ? $step['mode'] : null;
        $errors = array();
        if ( in_array( $mode, array( 'create', 'ensure' ), true ) )
        {
            if ( !isset( $step['locale'] ) )
                $errors[] = "locale is missing (mode $mode)";
            else if ( ( $problem = expMigrationIdentifier::problem( 'language', $step['locale'] ) ) !== null || is_int( $step['locale'] ) )
                $errors[] = 'locale: ' . expMigrationStepSchema::show( $step['locale'] ) . ' is not a locale (ger-DE)';
        }
        if ( in_array( $mode, array( 'update', 'delete' ), true ) && !isset( $step['language'] ) )
            $errors[] = "language is missing (mode $mode)";
        if ( $mode === 'update' && !isset( $step['name'] ) )
            $errors[] = 'nothing to update: name is missing';
        return $errors;
    }

    /**
     * @param array $step
     * @return string[]
     */
    public function caches( array $step )
    {
        return array( 'content_language', 'content' );
    }

    /**
     * @param array $step
     * @return bool
     */
    public function revertible( array $step )
    {
        return isset( $step['mode'] ) && $step['mode'] === 'create';
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function plan( array $step, $context )
    {
        $name = isset( $step['name'] ) ? ' "' . $step['name'] . '"' : '';
        switch ( $step['mode'] )
        {
            case 'create':
                return array( 'add the language ' . $step['locale'] . $name );
            case 'ensure':
                return array( 'ensure the language ' . $step['locale'] . $name . ': add it when missing' );
            case 'update':
                return array( 'rename the language ' . expMigrationStepSchema::show( $step['language'] ) . ' to' . $name );
            case 'delete':
                return array( 'remove the language ' . expMigrationStepSchema::show( $step['language'] ) . ' (only when no object or class has a translation in it)' );
        }
        return parent::plan( $step, $context );
    }

    /**
     * @param array $step
     * @param expMigrationContext $context
     * @return array
     */
    public function apply( array $step, $context )
    {
        $mode = $step['mode'];
        if ( $mode === 'create' || $mode === 'ensure' )
        {
            $language = eZContentLanguage::fetchByLocale( (string)$step['locale'] );
            if ( $language instanceof eZContentLanguage && $mode === 'create' )
                throw new RuntimeException( 'the language ' . $step['locale'] . ' is there already (mode ensure accepts it)' );
            if ( $language instanceof eZContentLanguage )
                return isset( $step['name'] ) ? $this->rename( $language, (string)$step['name'] )
                                              : self::result( array( 'the language ' . $step['locale'] . ' is there' ), self::values( $language ) + array( 'created' => false, 'changed' => false ) );
            $language = eZContentLanguage::addLanguage( (string)$step['locale'], isset( $step['name'] ) ? (string)$step['name'] : null );
            if ( !$language instanceof eZContentLanguage )
                throw new RuntimeException( 'the language ' . $step['locale'] . ' could not be added (a locale this installation does not know, share/locale/'
                                            . $step['locale'] . '.ini, or no room for another language)' );
            self::expire();
            return self::result( array( 'added the language ' . $step['locale'] . ' "' . $language->attribute( 'name' ) . '" (' . $language->attribute( 'id' ) . ')' ),
                                 self::values( $language ) + array( 'created' => true, 'changed' => true ) );
        }
        $id = expMigrationIdentifier::find( 'language', $step['language'] );
        $language = $id !== null ? eZContentLanguage::fetch( $id ) : null;
        if ( !$language instanceof eZContentLanguage )
            return self::missingResult( $step, 'the language ' . expMigrationStepSchema::show( $step['language'] ), array( 'language_id' => null, 'locale' => null, 'name' => null ) );
        return $mode === 'update' ? $this->rename( $language, (string)$step['name'] ) : $this->delete( $language );
    }

    /**
     * Removes the language a create added, while unused.
     *
     * @param array $step
     * @param expMigrationContext $context
     * @return string[]
     */
    public function revert( array $step, $context )
    {
        if ( !$this->revertible( $step ) )
            throw new RuntimeException( 'only a create can be undone by itself; give the migration a down:' );
        $language = eZContentLanguage::fetchByLocale( (string)$step['locale'] );
        if ( !$language instanceof eZContentLanguage )
            return array( 'the language ' . $step['locale'] . ' is not there: nothing to undo' );
        $result = $this->delete( $language );
        return $result['lines'];
    }

    /**
     * @param eZContentLanguage $language
     * @param string $name
     * @return array
     */
    protected function rename( eZContentLanguage $language, $name )
    {
        $old = (string)$language->attribute( 'name' );
        if ( $old === $name )
            return self::result( array( 'the language ' . $language->attribute( 'locale' ) . " is named \"$name\"" ), self::values( $language ) + array( 'created' => false, 'changed' => false ) );
        $language->setAttribute( 'name', $name );
        $language->store();
        self::expire();
        return self::result( array( 'renamed the language ' . $language->attribute( 'locale' ) . " from \"$old\" to \"$name\"" ), self::values( $language ) + array( 'created' => false, 'changed' => true ) );
    }

    /**
     * @param eZContentLanguage $language
     * @return array
     */
    protected function delete( eZContentLanguage $language )
    {
        $values = self::values( $language );
        $db = eZDB::instance();
        $db->begin();
        try
        {
            $removed = $language->removeThis();
        }
        catch ( Throwable $e )
        {
            // nothing of a transaction that failed half way is committed by a later commit()
            if ( $db->transactionCounter() > 0 )
                $db->rollback();
            throw $e;
        }
        $db->commit();
        if ( !$removed )
            throw new RuntimeException( 'the language ' . $values['locale'] . ' is in use (an object or a class has a translation in it): it is not removed' );
        self::expire();
        return self::result( array( 'removed the language ' . $values['locale'] . ' (' . $values['language_id'] . ')' ), $values + array( 'created' => false, 'changed' => true ) );
    }

    /** The languages are read again (the list is cached in memory and in the cache). */
    protected static function expire()
    {
        unset( $GLOBALS['eZContentLanguageList'], $GLOBALS['eZContentLanguageMask'] );
        eZContentLanguage::expireCache();
    }

    /**
     * @param eZContentLanguage $language
     * @return array
     */
    protected static function values( eZContentLanguage $language )
    {
        return array( 'language_id' => (int)$language->attribute( 'id' ), 'locale' => (string)$language->attribute( 'locale' ),
                      'name' => (string)$language->attribute( 'name' ) );
    }
}
