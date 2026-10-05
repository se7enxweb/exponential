<?php
/**
 * File containing the expNotificationMailCategoryHandler class.
 *
 * The categories "content" and "collaboration" of the e-mail preferences read the notification system's own data,
 * so nothing has to be migrated and the old notification pages keep working:
 *
 *  - content: a person with at least one subtree notification rule is "on" until they choose otherwise on the
 *    preference page; the general digest settings give the frequency (daily, weekly; a monthly digest reads as
 *    weekly, the nearest the preference page offers).
 *  - collaboration: a person with at least one collaboration notification rule is "on"; it is sent at once.
 *
 * A choice made on the preference page is stored by the preference system and wins over these readings. The rules
 * themselves are never changed or removed: switched off (the category or the master switch), they are dormant and
 * come back when the person switches the mail on again.
 *
 * The static helpers are what the notification handlers call before they do any work for a person (allowsUser(),
 * allowsAddress()) and to schedule an item at the frequency the person chose (storedFrequency()).
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

class expNotificationMailCategoryHandler implements expMailCategoryHandler
{
    const CONTENT = 'content';
    const COLLABORATION = 'collaboration';

    public function stateFor( expMailRecipient $recipient, expMailCategory $category )
    {
        if ( !$recipient->hasAccount() )
            return null;
        if ( $category->identifier === self::CONTENT )
            return eZSubtreeNotificationRule::fetchListCount( $recipient->userId() ) > 0 ? true : null;
        if ( $category->identifier === self::COLLABORATION )
            return count( (array)eZCollaborationNotificationRule::fetchList( $recipient->userId(), false ) ) > 0 ? true : null;
        return null;
    }

    public function frequencyFor( expMailRecipient $recipient, expMailCategory $category )
    {
        if ( !$recipient->hasAccount() || $category->identifier !== self::CONTENT )
            return null;
        $settings = eZGeneralDigestUserSettings::fetchByUserId( $recipient->userId() );
        if ( !$settings instanceof eZGeneralDigestUserSettings || (int)$settings->attribute( 'receive_digest' ) !== 1 )
            return 'immediate';
        switch ( (int)$settings->attribute( 'digest_type' ) )
        {
            case eZGeneralDigestUserSettings::TYPE_DAILY:
                return 'daily';
            case eZGeneralDigestUserSettings::TYPE_WEEKLY:
            case eZGeneralDigestUserSettings::TYPE_MONTHLY:
                return 'weekly';
        }
        return 'immediate';
    }

    /**
     * The old rules stay as they are ("map, keep the old pages"): a category switched off leaves them dormant.
     */
    public function changed( expMailRecipient $recipient, expMailCategory $category, $state, expConsentContext $context )
    {
    }

    // ------------------------------------------------------------------ helpers for the notification handlers

    /**
     * The category of the mail a notification handler sends.
     *
     * @param string $handlerID ezsubtree, ezgeneraldigest, ezcollaboration
     * @return string|null
     */
    public static function categoryForHandler( $handlerID )
    {
        switch ( (string)$handlerID )
        {
            case 'ezsubtree':
            case 'ezgeneraldigest':
                return self::CONTENT;
            case 'ezcollaboration':
                return self::COLLABORATION;
        }
        return null;
    }

    /** @return bool the preference system is installed (classes and tables) */
    public static function available()
    {
        // only a yes is kept: a persistent worker started before the database update learns of the tables later
        static $available = false;
        if ( !$available )
            $available = class_exists( 'expMailPreferences' ) && expMailPreferencesService::tableExists( 'expmail_preference' );
        return $available;
    }

    /**
     * May a mail of the category go to this account? The same answer the mail gate gives: with the gate switched
     * off only a suppressed address is refused.
     *
     * @param int $userID
     * @param string $category
     * @return bool
     */
    public static function allowsUser( $userID, $category )
    {
        if ( !self::available() )
            return true;
        try
        {
            $recipient = expMailRecipient::fromUserId( (int)$userID );
            return $recipient === null ? true : self::allowsRecipient( $recipient, $category );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'Mail preferences: ' . $e->getMessage(), __METHOD__ );
            return true; // the gate decides again when the mail is sent
        }
    }

    /**
     * allowsUser() for an address (the account it belongs to, if any).
     *
     * @param string $address
     * @param string $category
     * @return bool
     */
    public static function allowsAddress( $address, $category )
    {
        if ( !self::available() )
            return true;
        try
        {
            $recipient = expMailRecipient::fromAddress( $address );
            return $recipient === null ? true : self::allowsRecipient( $recipient, $category );
        }
        catch ( Throwable $e )
        {
            eZDebug::writeError( 'Mail preferences: ' . $e->getMessage(), __METHOD__ );
            return true;
        }
    }

    protected static function allowsRecipient( expMailRecipient $recipient, $category )
    {
        $decision = expMailPreferences::forRecipient( $recipient )->decision( $category );
        if ( $decision === 'allow' || $decision === 'unknown_category' )
            return true;
        if ( !expMailGate::enabled() && $decision !== 'suppressed' )
            return true;
        return false;
    }

    /**
     * The frequency the person chose on the preference page, when they chose one.
     *
     * @param int $userID
     * @param string $category
     * @return string immediate|daily|weekly, '' when nothing was chosen there (the old digest settings apply)
     */
    public static function storedFrequency( $userID, $category )
    {
        if ( !self::available() || (int)$userID <= 0 )
            return '';
        $rows = expMailPreferenceRow::fetchForKey( 'u:' . (int)$userID );
        if ( !isset( $rows[$category] ) )
            return '';
        $frequency = (string)$rows[$category]->attribute( 'frequency' );
        return in_array( $frequency, array( 'immediate', 'daily', 'weekly' ), true ) ? $frequency : '';
    }
}
