<?php
/**
 * File containing the Exponential\View\Kernel\Mailpreferences\Admin class: the view mailpreferences/admin/<section>.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Mailpreferences
{

use Exponential\Service\MailPreferencesPage;

/**
 * The administrator's pages of the e-mail preferences (policy mailpreferences/administrate):
 *
 *  - admin/status                the state of the mail gate, the numbers, what looks wrong
 *  - admin/categories[/<id>]     the categories; create, rename, describe
 *  - admin/suppression           the suppression list: check, add, lift
 *  - admin/consent               the consent log with filters and the CSV export (mailpreferences/export)
 *  - admin/user[/<id>]           find a user; see and change their preferences on their request
 *
 * Paging: /(offset)/<n>. The consent filters are query string parameters, so a filtered list can be bookmarked and
 * exported as it is shown.
 */
class Admin extends Page
{
    const LIMIT = 50;

    protected function page()
    {
        if ( !self::canAdministrate() )
            return $this->module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
        switch ( (string)$this->param( 'Page', $this->param( 'Section', 'status' ) ) )
        {
            case 'categories':
                return $this->categories();
            case 'suppression':
                return $this->suppression();
            case 'consent':
                return $this->consent();
            case 'user':
                return $this->user();
            case 'status':
                return $this->status();
        }
        return $this->module->handleError( \eZError::KERNEL_MODULE_VIEW_NOT_FOUND, 'kernel' );
    }

    /** @return int the offset of a list (/(offset)/n) */
    protected function offset()
    {
        $offset = $this->param( 'Offset', null );
        if ( $offset === null && isset( $this->params['UserParameters']['offset'] ) )
            $offset = $this->params['UserParameters']['offset'];
        return max( 0, (int)$offset );
    }

    // ------------------------------------------------------------------ status

    protected function status()
    {
        $notice = false;
        $http = $this->http;
        if ( self::isPost() && $http->hasPostVariable( 'StoreSenderDetailsButton' ) )
        {
            // the form token of ezformtoken guards this POST like every other; the view needs mailpreferences/administrate
            try
            {
                $ok = \expMailSenderDetails::save( (string)$http->postVariable( 'OrganisationName', '' ), (string)$http->postVariable( 'OrganisationAddress', '' ) );
                $notice = $ok ? array( 'type' => 'success', 'text' => MailPreferencesPage::trAdmin( 'The sender details were saved.' ) )
                              : array( 'type' => 'error', 'text' => MailPreferencesPage::trAdmin( 'The sender details could not be saved: the settings override is not writable.' ) );
            }
            catch ( \Throwable $e )
            {
                \eZDebug::writeError( $e->getMessage(), __METHOD__ );
                $notice = array( 'type' => 'error', 'text' => MailPreferencesPage::trAdmin( 'The sender details could not be saved: %error', array( '%error' => $e->getMessage() ) ) );
            }
        }
        $status = \expMailPreferencesService::status();
        $problems = array();
        foreach ( $status['problems'] as $problem )
            $problems[] = array( 'level' => $problem[0] === 'notice' ? 'info' : $problem[0], 'text' => \expMailPreferencesService::problemText( $problem ) );
        $t = function ( $text, $args = null ) { return MailPreferencesPage::trAdmin( $text, $args ); };
        $stats = array(
            array( 'label' => $t( 'People with stored preferences' ), 'value' => $status['recipients'], 'level' => '' ),
            array( 'label' => $t( 'Turned all optional e-mail off' ), 'value' => $status['master_off'], 'level' => '' ),
            array( 'label' => $t( 'Waiting for a confirmation' ), 'value' => $status['pending'], 'level' => '' ),
            array( 'label' => $t( 'Addresses on the suppression list' ), 'value' => $status['suppression']['total'], 'level' => '' ),
            array( 'label' => $t( 'Optional e-mails sent, last 24 hours' ), 'value' => $status['gate_24h']['sent'], 'level' => '' ),
            array( 'label' => $t( 'Blocked by a preference, last 24 hours' ), 'value' => $status['gate_24h']['blocked'], 'level' => '' ),
            array( 'label' => $t( 'Without a category, last 7 days' ), 'value' => $status['gate_7d']['uncategorised'], 'level' => $status['gate_7d']['uncategorised'] > 0 ? 'bad' : '' ),
            array( 'label' => $t( 'Errors, last 24 hours' ), 'value' => $status['gate_24h']['error'], 'level' => $status['gate_24h']['error'] > 0 ? 'bad' : '' ),
            array( 'label' => $t( 'Consent records' ), 'value' => $status['consent_log'], 'level' => '' ),
        );
        $none = $t( '(not set)' );
        $facts = array(
            array( 'label' => $t( 'Mail gate' ), 'value' => $status['gate'] === 'enabled' ? $t( 'On' ) : $t( 'Off' ) ),
            array( 'label' => $t( 'Organisation in the footer' ), 'value' => $status['footer']['organisation_name'] !== ''
                   ? $status['footer']['organisation_name'] . ( isset( $status['footer']['organisation_name_source'] ) && $status['footer']['organisation_name_source'] === 'site' ? ' (' . $t( 'the site name' ) . ')' : '' )
                   : $none ),
            array( 'label' => $t( 'Postal address in the footer' ), 'value' => $status['footer']['organisation_address'] !== '' ? $status['footer']['organisation_address'] : $none ),
            array( 'label' => $t( 'Links in e-mails point to' ), 'value' => $status['base_url'] ),
            array( 'label' => $t( 'Site secret of the links' ), 'value' => $status['secret'] ? $t( 'Generated' ) : $t( 'Not generated yet' ) ),
            array( 'label' => $t( 'Last e-mail through the gate' ), 'value' => $status['gate_7d']['last'] ? \eZLocale::instance()->formatShortDateTime( $status['gate_7d']['last'] ) : $none ),
        );
        if ( isset( $status['bounce'] ) && is_array( $status['bounce'] ) )
            $facts[] = array( 'label' => $t( 'Bounce mailbox, last read' ),
                              'value' => !empty( $status['bounce']['last_read'] ) ? \eZLocale::instance()->formatShortDateTime( (int)$status['bounce']['last_read'] ) : $none );
        $links = array( array( 'url' => 'mailpreferences/settings', 'text' => $t( 'My e-mail preferences' ) ),
                        array( 'url' => 'notification/status', 'text' => $t( 'Notification status' ) ),
                        array( 'url' => 'mailpreferences/request', 'text' => $t( 'The "send me a link" page' ) ) );
        return $this->render( 'admin/status.tpl', array( 'problems' => $problems, 'stats' => $stats, 'facts' => $facts, 'links' => $links, 'notice' => $notice,
                                                         'sender' => \expMailSenderDetails::get() + array( 'site_name' => \expMailSenderDetails::siteName() ) ),
                              $t( 'Status' ) );
    }

    // ------------------------------------------------------------------ categories

    protected function categories()
    {
        $registry = \expMailCategoryRegistry::instance();
        $t = function ( $text, $args = null ) { return MailPreferencesPage::trAdmin( $text, $args ); };
        $notice = false;
        $form = false;
        $editID = (string)$this->param( 'ID', '' );
        $http = $this->http;

        if ( self::isPost() && ( $http->hasPostVariable( 'CreateCategoryButton' ) || $http->hasPostVariable( 'StoreCategoryButton' ) ) )
        {
            $identifier = \expMailCategory::cleanIdentifier( (string)$http->postVariable( 'Identifier', '' ) );
            $existing = $registry->get( $identifier );
            $creating = $http->hasPostVariable( 'CreateCategoryButton' );
            $name = trim( (string)$http->postVariable( 'Name', '' ) );
            $description = trim( (string)$http->postVariable( 'Description', '' ) );
            $form = array( 'identifier' => $identifier, 'name' => $name, 'description' => $description, 'essential' => false,
                           'frequencies' => array_values( array_intersect( \expMailCategory::FREQUENCIES, (array)$http->postVariable( 'Frequencies', array() ) ) ),
                           'double_opt_in' => $http->hasPostVariable( 'DoubleOptIn' ) );
            if ( !\expMailCategory::validIdentifier( $identifier ) || $identifier !== (string)$http->postVariable( 'Identifier', '' ) )
                $notice = array( 'type' => 'error', 'text' => $t( 'The identifier may only hold lower case letters, digits and underscores, and must start with a letter.' ) );
            else if ( $creating && $existing )
                $notice = array( 'type' => 'error', 'text' => $t( 'A category with this identifier exists already.' ) );
            else if ( !$creating && !$existing )
                $notice = array( 'type' => 'error', 'text' => $t( 'This category does not exist any more.' ) );
            else if ( $name === '' )
                $notice = array( 'type' => 'error', 'text' => $t( 'Please give the category a name.' ) );
            else
            {
                if ( $existing && $existing->source !== 'admin' )
                {
                    // a category of the settings or an extension: only its name and description change here
                    $values = array( 'name' => $name, 'description' => $description, 'essential' => $existing->essential,
                                     'defaultOn' => $existing->defaultOn, 'frequencies' => $existing->frequencies,
                                     'doubleOptIn' => $existing->doubleOptIn, 'handlerClass' => $existing->handlerClass );
                }
                else
                {
                    $values = array( 'name' => $name, 'description' => $description, 'essential' => false, 'defaultOn' => false,
                                     'frequencies' => $form['frequencies'], 'doubleOptIn' => $form['double_opt_in'], 'source' => 'admin',
                                     'handlerClass' => $existing ? $existing->handlerClass : '' );
                }
                if ( $registry->saveAdmin( new \expMailCategory( $identifier, $values ) ) )
                {
                    $notice = array( 'type' => 'success', 'text' => $creating ? $t( 'The category "%name" was created.', array( '%name' => $name ) )
                                                                              : $t( 'The category "%name" was saved.', array( '%name' => $name ) ) );
                    $form = false;
                    $editID = '';
                }
                else
                    $notice = array( 'type' => 'error', 'text' => $t( 'The category could not be saved.' ) );
            }
            if ( $form && !$creating )
                $editID = $identifier;
        }
        else if ( self::isPost() && ( $http->hasPostVariable( 'ResetCategoryButton' ) || $http->hasPostVariable( 'RemoveCategoryButton' ) ) )
        {
            $identifier = \expMailCategory::cleanIdentifier( (string)$http->postVariable( 'Identifier', '' ) );
            $existing = $registry->get( $identifier );
            if ( $existing && $registry->removeAdmin( $identifier ) )
                $notice = array( 'type' => 'success', 'text' => $existing->source === 'admin'
                    ? $t( 'The category "%name" was removed. The choices people made for it are kept in their records.', array( '%name' => MailPreferencesPage::categoryName( $existing ) ) )
                    : $t( 'The category "%name" uses the name and description of its definition again.', array( '%name' => $existing->identifier ) ) );
            $editID = '';
        }

        $rows = array();
        $edit = false;
        foreach ( $registry->all() as $id => $category )
        {
            $row = array( 'identifier' => $id, 'name' => MailPreferencesPage::categoryName( $category ),
                          'description' => MailPreferencesPage::categoryDescription( $category ), 'essential' => (bool)$category->essential,
                          'frequencies' => $category->frequencies, 'double_opt_in' => (bool)$category->doubleOptIn, 'source' => $category->source,
                          'editable' => true, 'removable' => $category->source === 'admin' );
            $rows[] = $row;
            if ( $editID !== '' && $id === $editID )
            {
                // the form edits the stored text, not its translation
                $edit = array_merge( $row, array( 'name' => $category->name, 'description' => $category->description ) );
                if ( $form )
                    $edit = array_merge( $edit, array( 'name' => $form['name'], 'description' => $form['description'],
                                                       'frequencies' => $form['frequencies'], 'double_opt_in' => $form['double_opt_in'] ) );
            }
        }
        return $this->render( 'admin/categories.tpl', array( 'categories' => $rows, 'edit' => $edit, 'form' => $edit ? false : $form,
                                                             'notice' => $notice, 'frequency_names' => MailPreferencesPage::frequencyNames() ),
                              $t( 'Categories' ) );
    }

    // ------------------------------------------------------------------ suppression

    protected function suppression()
    {
        $t = function ( $text, $args = null ) { return MailPreferencesPage::trAdmin( $text, $args ); };
        $http = $this->http;
        $notice = false;
        $check = false;
        $reasons = MailPreferencesPage::reasonNames();
        unset( $reasons['bridge'] );
        $access = \eZUser::currentUser()->hasAccessTo( 'mailpreferences', 'export' );
        $canExport = $access['accessWord'] !== 'no';
        if ( isset( $_GET['export'] ) && $_GET['export'] === 'csv' )
        {
            if ( !$canExport )
                return $this->module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
            $csv = "\xEF\xBB\xBF" . \expMailSuppression::exportCsv();
            MailPreferencesPage::sendResponse( $csv, 'text/csv; charset=utf-8', 200, 'suppression-list-' . gmdate( 'Y-m-d' ) . '.csv' );
        }
        if ( self::isPost() && $http->hasPostVariable( 'AddButton' ) )
        {
            $email = trim( (string)$http->postVariable( 'AddEmail', '' ) );
            $reason = (string)$http->postVariable( 'Reason', 'admin' );
            if ( !\eZMail::validate( $email ) )
                $notice = array( 'type' => 'error', 'text' => $t( 'Please enter a complete e-mail address.' ) );
            else if ( !isset( $reasons[$reason] ) )
                $notice = array( 'type' => 'error', 'text' => $t( 'Please choose a reason.' ) );
            else
            {
                \expMailSuppression::add( $email, $reason, trim( (string)$http->postVariable( 'Note', '' ) ) );
                $recipient = \expMailRecipient::fromAddress( $email );
                if ( $recipient )
                    \expConsentLog::record( $recipient, '', 'suppress', '', $reason,
                                            \expConsentContext::fromRequest( 'admin', $t( 'Add to the list' ) . ': ' . $reasons[$reason] ) );
                $notice = array( 'type' => 'success', 'text' => $t( 'The address was added to the suppression list.' ) );
            }
        }
        else if ( self::isPost() && $http->hasPostVariable( 'LiftButton' ) )
        {
            $value = $http->hasPostVariable( 'LiftHash' ) ? (string)$http->postVariable( 'LiftHash' ) : trim( (string)$http->postVariable( 'LiftEmail', '' ) );
            $byHash = \expMailSuppression::isHash( $value );
            if ( $value !== '' && \expMailSuppression::lift( $value ) )
            {
                $recipient = $byHash ? \expMailRecipient::fromKey( 'a:' . $value ) : \expMailRecipient::fromAddress( $value );
                if ( $recipient )
                    \expConsentLog::record( $recipient, '', 'unsuppress', '', '', \expConsentContext::fromRequest( 'admin', $t( 'Lift the block for this address' ) ) );
                $notice = array( 'type' => 'success', 'text' => $t( 'The block was lifted. Optional e-mail goes to the address again where the person turned it on.' ) );
            }
            else
                $notice = array( 'type' => 'warning', 'text' => $t( 'This address is not on the list.' ) );
        }
        else if ( self::isPost() && $http->hasPostVariable( 'CheckButton' ) )
        {
            $email = trim( (string)$http->postVariable( 'CheckEmail', '' ) );
            if ( !\eZMail::validate( $email ) )
                $notice = array( 'type' => 'error', 'text' => $t( 'Please enter a complete e-mail address.' ) );
            else
            {
                $row = \expMailSuppression::fetchByHash( \expMailSuppression::hash( $email ) );
                $check = array( 'email' => $email, 'suppressed' => $row !== null,
                                'row' => $row ? array( 'reason' => (string)$row->attribute( 'reason' ), 'created' => (int)$row->attribute( 'created' ) ) : false );
            }
        }

        $offset = $this->offset();
        $total = \expMailSuppression::countList();
        $rows = array();
        foreach ( \expMailSuppression::fetchList( $offset, self::LIMIT ) as $row )
            $rows[] = array( 'hash' => (string)$row->attribute( 'email_hash' ), 'reason' => (string)$row->attribute( 'reason' ),
                             'note' => (string)$row->attribute( 'note' ), 'created' => (int)$row->attribute( 'created' ) );
        return $this->render( 'admin/suppression.tpl', array( 'rows' => $rows, 'total' => $total, 'offset' => $offset, 'limit' => self::LIMIT,
                                                              'reason_names' => MailPreferencesPage::reasonNames(), 'add_reasons' => $reasons,
                                                              'check' => $check, 'notice' => $notice, 'page_uri' => 'mailpreferences/admin/suppression',
                                                              'export_uri' => $canExport && $total > 0 ? 'mailpreferences/admin/suppression?export=csv' : false ),
                              $t( 'Suppression list' ) );
    }

    // ------------------------------------------------------------------ consent log

    protected function consent()
    {
        $t = function ( $text, $args = null ) { return MailPreferencesPage::trAdmin( $text, $args ); };
        $get = function ( $name ) { return isset( $_GET[$name] ) && is_string( $_GET[$name] ) ? trim( $_GET[$name] ) : ''; };
        $filters = array( 'person' => $get( 'person' ), 'category' => $get( 'category' ), 'source' => $get( 'source' ),
                          'from' => $get( 'from' ), 'to' => $get( 'to' ) );
        $query = array();
        $sql = array();
        if ( $filters['person'] !== '' )
        {
            if ( ctype_digit( $filters['person'] ) )
                $sql['user_id'] = (int)$filters['person'];
            else
                $sql['email'] = $filters['person'];
        }
        foreach ( array( 'category', 'source' ) as $f )
            if ( $filters[$f] !== '' )
                $sql[$f] = $filters[$f];
        foreach ( array( 'from' => '00:00:00', 'to' => '23:59:59' ) as $f => $time )
            if ( preg_match( '/^\d{4}-\d{2}-\d{2}$/', $filters[$f] ) && ( $ts = strtotime( $filters[$f] . ' ' . $time ) ) !== false )
                $sql[$f] = $ts;
            else
                $filters[$f] = '';
        foreach ( $filters as $k => $v )
            if ( $v !== '' )
                $query[$k] = $v;
        $access = \eZUser::currentUser()->hasAccessTo( 'mailpreferences', 'export' );
        $canExport = $access['accessWord'] !== 'no';

        if ( $get( 'export' ) === 'csv' )
        {
            if ( !$canExport )
                return $this->module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
            $csv = "\xEF\xBB\xBF" . \expConsentLog::exportCsv( $sql );
            MailPreferencesPage::sendResponse( $csv, 'text/csv; charset=utf-8', 200, 'consent-log-' . gmdate( 'Y-m-d' ) . '.csv' );
        }

        $offset = $this->offset();
        $total = \expConsentLog::countList( $sql );
        $rows = array();
        $names = array();
        $name = function ( $userID ) use ( &$names ) {
            if ( !isset( $names[$userID] ) )
            {
                $object = \eZContentObject::fetch( $userID );
                $names[$userID] = $object ? (string)$object->attribute( 'name' ) : '#' . $userID;
            }
            return $names[$userID];
        };
        foreach ( \expConsentLog::fetchList( $sql, $offset, self::LIMIT ) as $log )
        {
            $row = MailPreferencesPage::logRow( $log );
            if ( $row['anonymised'] )
                $row['who'] = $t( 'Anonymised' );
            else if ( $row['user_id'] > 0 )
                $row['who'] = $name( $row['user_id'] );
            else
                $row['who'] = $row['email'] !== '' ? $row['email'] : $t( 'An address (only its hash is kept)' );
            $row['actor'] = $row['actor_user_id'] > 0 && $row['actor_user_id'] !== $row['user_id'] ? $name( $row['actor_user_id'] ) : '';
            $rows[] = $row;
        }
        $categoryNames = array();
        foreach ( \expMailCategoryRegistry::instance()->all() as $id => $category )
            $categoryNames[$id] = MailPreferencesPage::categoryName( $category );
        $queryString = $query ? '?' . http_build_query( $query ) : '';
        return $this->render( 'admin/consent.tpl', array(
            'rows' => $rows, 'total' => $total, 'offset' => $offset, 'limit' => self::LIMIT, 'filters' => $filters,
            'category_names' => $categoryNames, 'source_names' => MailPreferencesPage::sourceNames(),
            'export_uri' => $canExport ? 'mailpreferences/admin/consent?' . http_build_query( $query + array( 'export' => 'csv' ) ) : false,
            'query' => $queryString, 'page_uri' => 'mailpreferences/admin/consent', 'notice' => false ), $t( 'Consent log' ) );
    }

    // ------------------------------------------------------------------ a user

    protected function user()
    {
        $t = function ( $text, $args = null ) { return MailPreferencesPage::trAdmin( $text, $args ); };
        $id = (int)$this->param( 'ID', 0 );
        if ( $id <= 0 )
        {
            $q = isset( $_GET['q'] ) && is_string( $_GET['q'] ) ? trim( $_GET['q'] ) : '';
            return $this->render( 'admin/user.tpl', array( 'query' => $q, 'users' => $q !== '' ? self::findUsers( $q ) : array(), 'notice' => false ),
                                  $t( 'A user' ) );
        }
        $user = \eZUser::fetch( $id );
        if ( !$user )
            return $this->render( 'admin/user.tpl', array( 'query' => '', 'users' => array(),
                                                           'notice' => array( 'type' => 'error', 'text' => $t( 'No user found.' ) ) ), $t( 'A user' ) );
        $prefs = \expMailPreferences::forRecipient( \expMailRecipient::fromUser( $user ) );
        $base = 'mailpreferences/admin/user/' . $id;
        $format = isset( $_GET['export'] ) && in_array( $_GET['export'], array( 'json', 'csv' ), true ) ? $_GET['export'] : '';
        if ( $format !== '' )
        {
            $access = \eZUser::currentUser()->hasAccessTo( 'mailpreferences', 'export' );
            if ( $access['accessWord'] === 'no' )
                return $this->module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
            MailPreferencesPage::sendExport( $prefs, $format, 'admin' );
        }
        $notices = MailPreferencesPage::handlePost( $prefs, $this->http, 'admin' );
        $variables = MailPreferencesPage::templateVariables( $prefs, 'admin', $base,
                                                             array( 'json' => $base . '?export=json', 'csv' => $base . '?export=csv' ), $notices );
        $object = $user->attribute( 'contentobject' );
        $variables['user_name'] = $object ? (string)$object->attribute( 'name' ) : (string)$user->attribute( 'login' );
        $variables['can_administrate'] = true;
        $variables['notification_settings'] = false;
        return $this->render( 'settings.tpl', $variables, $variables['user_name'] );
    }

    /**
     * @param string $q a user ID, an e-mail address, or part of a login, name or address
     * @return array[] hash( id, name, login, email ), at most 25
     */
    protected static function findUsers( $q )
    {
        $db = \eZDB::instance();
        if ( ctype_digit( $q ) )
            $where = 'u.contentobject_id = ' . (int)$q;
        else
        {
            $like = $db->escapeString( str_replace( array( '%', '_' ), '', mb_strtolower( $q ) ) );
            $where = "LOWER( u.login ) LIKE '%$like%' OR LOWER( u.email ) LIKE '%$like%' OR LOWER( o.name ) LIKE '%$like%'";
        }
        $rows = $db->arrayQuery( "SELECT u.contentobject_id AS id, u.login, u.email, o.name FROM ezuser u, ezcontentobject o
                                  WHERE o.id = u.contentobject_id AND o.status = 1 AND ( $where ) ORDER BY o.name", array( 'limit' => 25 ) );
        $out = array();
        foreach ( (array)$rows as $row )
            $out[] = array( 'id' => (int)$row['id'], 'name' => (string)$row['name'], 'login' => (string)$row['login'], 'email' => (string)$row['email'] );
        return $out;
    }
}

}
