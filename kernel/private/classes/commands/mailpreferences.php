<?php
/**
 * @description Show and change the e-mail preferences of a user or an address: categories, master switch, frequency, export, erase
 * @alias exp:mail:preferences
 *
 * File containing the exp:mail:preferences command.
 *
 * @copyright Copyright (C) 1998 - 2026 7x and the Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\Command\Kernel
{

class Mailpreferences extends \Exponential\Runnable\Command
{
    public function run()
    {
        $cli = $this->cli();
        $this->script(
            array(
                'description' => "E-mail preferences of one person\n" .
                                 "  categories                the categories (identifier, essential/optional, default, frequencies, double opt-in)\n" .
                                 "  show                      the person's master switch, categories, frequencies, suppression, pending confirmations\n" .
                                 "  set                       switch a category on or off (--category, --state=on|off)\n" .
                                 "  frequency                 the frequency of a category (--category, --frequency=immediate|daily|weekly)\n" .
                                 "  master                    the master switch \"all optional mail\" (--state=on|off)\n" .
                                 "  export                    everything stored about the person, JSON or CSV (--format, --output)\n" .
                                 "  erase                     remove the preferences, anonymise the consent log (--yes)\n" .
                                 "  link                      print the person's preference link (works without login)\n" .
                                 "Every change is written to the consent log with source admin (or --source=import for consent\n" .
                                 "confirmed elsewhere) and the wording of --wording. Addresses are hidden unless --addresses.\n" .
                                 "\n" .
                                 "./console exp:mail:preferences show --user=editor\n" .
                                 "./console exp:mail:preferences set --email=someone@example.com --category=newsletter --state=on --no-mail\n" .
                                 "./console exp:mail:preferences master --user=14 --state=off\n" .
                                 "./console exp:mail:preferences export --user=editor --format=csv --output=var/tmp/editor.csv",
                'use-session'    => false,
                'use-modules'    => true,
                'use-extensions' => true
            )
        );
        $options = $this->startup(
            '[user:][email:][category:][state:][frequency:][format:][output:][wording:][source:][no-mail][yes][json][addresses]', '',
            array( 'user'      => 'the person: a login or a user id',
                   'email'     => 'the person: an address (the account it belongs to, if any)',
                   'category'  => 'set, frequency: the category identifier',
                   'state'     => 'set, master: on or off',
                   'frequency' => 'frequency: immediate, daily or weekly',
                   'format'    => 'export: json (default) or csv',
                   'output'    => 'export: write to this file instead of the screen',
                   'wording'   => 'the text recorded in the consent log (default: "Changed on the console")',
                   'source'    => 'admin (default) or import: import switches a double opt-in category on at once',
                   'no-mail'   => 'set: do not send the confirmation mail of a double opt-in',
                   'yes'       => 'erase: really erase',
                   'json'      => 'show, categories: JSON',
                   'addresses' => 'show the address instead of hiding it' ) );
        $args = isset( $options['arguments'] ) ? array_values( $options['arguments'] ) : array();
        $action = $args ? strtolower( (string)$args[0] ) : 'show';
        $show = function ( $email ) use ( $options ) {
            return !empty( $options['addresses'] ) ? $email : \expMailPreferencesService::maskAddress( $email );
        };

        if ( $action === 'categories' )
        {
            $rows = array();
            foreach ( \expMailCategoryRegistry::instance()->all() as $id => $c )
                $rows[$id] = $c->toArray();
            if ( !empty( $options['json'] ) )
                $cli->output( json_encode( $rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) );
            else
                foreach ( $rows as $id => $c )
                    $cli->output( sprintf( '%-20s %-9s %-12s %-24s %s  %s', $id, $c['essential'] ? 'essential' : 'optional', $c['default_on'] ? 'default on' : 'default off',
                                           implode( ',', $c['frequencies'] ), $c['double_opt_in'] ? 'double opt-in' : '-', $c['name'] ) );
            $cli->output( 'PASS' );
            $this->shutdown( 0 );
            return;
        }

        $recipient = \expMailPreferencesService::recipientFor( isset( $options['user'] ) ? $options['user'] : null, isset( $options['email'] ) ? $options['email'] : null );
        if ( !$recipient )
        {
            $cli->error( 'FAIL: name the person with --user=<login or id> or --email=<address> (an existing user, a valid address)' );
            $this->shutdown( 1 );
            return;
        }
        $source = isset( $options['source'] ) && $options['source'] === 'import' ? 'import' : 'admin';
        $wording = isset( $options['wording'] ) && $options['wording'] !== '' ? (string)$options['wording'] : 'Changed on the console';
        $context = \expConsentContext::system( $wording, $source );
        if ( !empty( $options['no-mail'] ) )
            $context->sendConfirmation = false;
        $prefs = \expMailPreferences::forRecipient( $recipient );
        $who = $recipient->userId() > 0 ? 'user ' . $recipient->userId() . ' (' . $show( $recipient->email() ) . ')' : $show( $recipient->email() );

        try
        {
            switch ( $action )
            {
                case 'show':
                    $data = array( 'recipient' => $recipient->key(), 'user_id' => $recipient->userId(), 'email' => $show( $recipient->email() ),
                                   'master' => $prefs->masterOn() ? 'on' : 'off', 'suppressed' => $prefs->isSuppressed(), 'categories' => array() );
                    foreach ( $prefs->overview() as $id => $o )
                        $data['categories'][$id] = array( 'state' => $o['state'], 'frequency' => $o['frequency'], 'stored' => $o['stored'],
                                                          'essential' => $o['essential'], 'mail_goes' => $o['allows'] );
                    if ( !empty( $options['json'] ) )
                    {
                        $cli->output( json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) );
                        break;
                    }
                    $cli->output( 'Person            ' . $who );
                    $cli->output( 'All optional mail ' . $data['master'] . ( $data['suppressed'] ? '   (the address is suppressed)' : '' ) );
                    foreach ( $data['categories'] as $id => $c )
                        $cli->output( sprintf( '  %-20s %-8s %-10s %s%s', $id, $c['essential'] ? 'always' : $c['state'], $c['frequency'],
                                               $c['mail_goes'] ? 'mail goes' : 'no mail', $c['stored'] || $c['essential'] ? '' : '   (default)' ) );
                    break;
                case 'set':
                    $state = isset( $options['state'] ) ? strtolower( (string)$options['state'] ) : '';
                    if ( !in_array( $state, array( 'on', 'off' ), true ) || empty( $options['category'] ) )
                        throw new \InvalidArgumentException( 'set needs --category and --state=on|off' );
                    $r = $prefs->set( (string)$options['category'], $state === 'on', $context );
                    $cli->output( sprintf( '%s: %s is %s', $who, $options['category'], $r ) );
                    break;
                case 'frequency':
                    if ( empty( $options['category'] ) || empty( $options['frequency'] ) )
                        throw new \InvalidArgumentException( 'frequency needs --category and --frequency' );
                    $prefs->setFrequency( (string)$options['category'], (string)$options['frequency'], $context );
                    $cli->output( sprintf( '%s: %s is %s', $who, $options['category'], $prefs->frequency( (string)$options['category'] ) ) );
                    break;
                case 'master':
                    $state = isset( $options['state'] ) ? strtolower( (string)$options['state'] ) : '';
                    if ( !in_array( $state, array( 'on', 'off' ), true ) )
                        throw new \InvalidArgumentException( 'master needs --state=on|off' );
                    $prefs->setMaster( $state === 'on', $context );
                    $cli->output( sprintf( '%s: all optional mail %s', $who, $prefs->masterOn() ? 'on' : 'off' ) );
                    break;
                case 'export':
                    $data = $prefs->export();
                    $format = isset( $options['format'] ) && $options['format'] === 'csv' ? 'csv' : 'json';
                    $text = $format === 'csv' ? \expMailPreferences::exportToCsv( $data ) : json_encode( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
                    \expConsentLog::record( $recipient, '', 'export', '', $format, $context );
                    if ( !empty( $options['output'] ) )
                    {
                        if ( file_put_contents( (string)$options['output'], $text ) === false )
                            throw new \RuntimeException( 'cannot write ' . $options['output'] );
                        @chmod( (string)$options['output'], 0600 );
                        $cli->output( 'written to ' . $options['output'] . ' (' . strlen( $text ) . ' bytes, mode 0600)' );
                    }
                    else
                        $cli->output( $text );
                    break;
                case 'erase':
                    if ( empty( $options['yes'] ) )
                        throw new \InvalidArgumentException( 'erase removes the preferences and anonymises the consent log: add --yes' );
                    $r = $prefs->erase( $context );
                    $cli->output( sprintf( '%s: %d preferences and %d pending confirmations removed, %d consent log rows anonymised',
                                           $who, $r['preferences'], $r['pending'], $r['anonymised'] ) );
                    break;
                case 'link':
                    $cli->output( \expMailPreferencesService::manageURL( $recipient ) );
                    break;
                default:
                    throw new \InvalidArgumentException( "unknown action '$action' (categories, show, set, frequency, master, export, erase, link)" );
            }
        }
        catch ( \Throwable $e )
        {
            $cli->error( 'FAIL: ' . $e->getMessage() );
            $this->shutdown( 1 );
            return;
        }
        $cli->output( 'PASS' );
        $this->shutdown( 0 );
    }
}

}
