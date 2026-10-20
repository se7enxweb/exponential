<?php
/**
 * exphelp/audience/<audience> (doc/eng-us/features/6.0/help-system.md, "Landing pages"): the landing page of one audience,
 * built from the blocks of exphelp.ini [LandingSettings] / [Landing_<audience>] (expHelpLanding). The commit log of
 * the developers' page (block commits) pages with the view parameter (offset); (merges)/1 shows merge commits too.
 * Template design:exphelp/audience.tpl.
 *
 * Template variables: help_landing (expHelpLanding::build(), names and summary translated), help_audiences (id => id,
 * name, icon, count), help_commits (expHelpCommitFeed::page() or false), help_commit_params (the view parameters of
 * the pager), help_support (self::support() or false), help_search_url, help_i18n_json.
 *
 * @copyright Copyright (C) 1998 - 2026 7x & Exponential Foundation. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Exphelp
{

class Audience extends \Exponential\Runnable\ModuleView
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        if ( !\expHelp::isEnabled() || !\expHelpLanding::isEnabled() )
            return $Module->handleError( \eZError::KERNEL_MODULE_NOT_FOUND, 'kernel' );

        $audience = isset( $Params['Audience'] ) && is_string( $Params['Audience'] ) ? strtolower( $Params['Audience'] ) : '';
        $index = \expHelpDocumentIndex::instance();
        $landing = $audience !== '' && preg_match( '/^[a-z0-9-]{1,64}$/', $audience ) ? ( new \expHelpLanding( $index ) )->build( $audience ) : null;
        if ( $landing === null )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        $landing = self::translate( $landing );

        $user = isset( $Params['UserParameters'] ) && is_array( $Params['UserParameters'] ) ? $Params['UserParameters'] : array();
        $param = function ( $name, $variable ) use ( $Params, $user ) {
            return isset( $Params[$variable] ) && is_scalar( $Params[$variable] ) ? (string)$Params[$variable]
                 : ( isset( $user[$name] ) && is_scalar( $user[$name] ) ? (string)$user[$name] : '' );
        };
        $offset = max( 0, (int)$param( 'offset', 'Offset' ) );
        $merges = $param( 'merges', 'Merges' ) === '1';

        $commits = false;
        $commitParams = array( 'offset' => $offset );
        if ( in_array( 'commits', $landing['blocks'], true ) )
        {
            $feed = new \expHelpCommitFeed();
            if ( $feed->options()['enabled'] )
                $commits = $feed->page( $offset, null, $merges );
            if ( $merges )
                $commitParams['merges'] = '1';
        }

        $tpl = \eZTemplate::factory();
        $tpl->setVariable( 'help_i18n_json', \expHelp::clientStringsJson() );
        $tpl->setVariable( 'help_landing', $landing );
        $tpl->setVariable( 'help_audiences', \expHelp::translatedNames( $index->audiences() ) );
        $tpl->setVariable( 'help_commits', $commits );
        $tpl->setVariable( 'help_commit_merges', $merges );
        $tpl->setVariable( 'help_commit_params', $commitParams );
        $tpl->setVariable( 'help_support', in_array( 'support', $landing['blocks'], true ) ? self::support() : false );
        $tpl->setVariable( 'help_search_url', \expHelp::url( 'search' ) );

        $Result = array();
        $Result['content'] = $tpl->fetch( 'design:exphelp/audience.tpl' );
        $Result['path'] = array( array( 'url' => 'exphelp/dashboard', 'text' => \ezpI18n::tr( 'kernel/exphelp', 'Help' ) ),
                                 array( 'url' => false, 'text' => $landing['name'] ) );
        return $Result;
    }

    /**
     * The names of the audience, its categories and related audiences, and its summary, in the user's language.
     *
     * @param array $landing
     * @return array
     */
    public static function translate( array $landing )
    {
        if ( !class_exists( 'ezpI18n' ) )
            return $landing;
        $c = 'kernel/exphelp';
        $landing['name'] = \ezpI18n::tr( $c, $landing['name'] );
        $landing['summary'] = $landing['summary'] !== '' ? \ezpI18n::tr( $c, $landing['summary'] ) : '';
        $landing['categories'] = array_values( \expHelp::translatedNames( $landing['categories'] ) );
        $landing['related'] = array_values( \expHelp::translatedNames( $landing['related'] ) );
        return $landing;
    }

    /**
     * The Support Exponential block: [SupportSettings] and one [SupportOrganization_<id>] group per organization,
     * with its name, its GitHub Sponsors address (https only), its own page in the help and what funding it pays for.
     * False when the block is off or no organization has an https address.
     *
     * @return array|false array( 'title', 'text', 'organizations' => list of array( id, name, url, page, mission,
     *                     funds (list) ), 'more' => page id or '' )
     */
    public static function support()
    {
        if ( \expHelp::setting( 'SupportSettings', 'Enabled', 'enabled' ) !== 'enabled' )
            return false;
        $tr = function ( $text ) {
            $text = trim( (string)$text );
            return $text !== '' && class_exists( 'ezpI18n' ) ? \ezpI18n::tr( 'kernel/exphelp', $text ) : $text;
        };
        $index = \expHelpDocumentIndex::instance();
        $page = function ( $id ) use ( $index ) {
            $id = trim( (string)$id );
            return $id !== '' && $index->document( $id ) !== null ? $id : '';
        };
        $ini = \expHelp::ini();
        $organizations = array();
        foreach ( (array)\expHelp::setting( 'SupportSettings', 'Organizations', array() ) as $id )
        {
            $group = 'SupportOrganization_' . $id;
            if ( !is_string( $id ) || !preg_match( '/^[a-z0-9-]{1,64}$/', $id ) || !$ini->hasGroup( $group ) )
                continue;
            $url = (string)\expHelp::setting( $group, 'URL', '' );
            if ( !preg_match( '#^https://[a-z0-9.-]+/[^\s"\'<>]*$#i', $url ) )
                continue;
            $funds = array();
            foreach ( (array)\expHelp::setting( $group, 'Funds', array() ) as $item )
                if ( is_string( $item ) && trim( $item ) !== '' )
                    $funds[] = $tr( $item );
            $organizations[] = array(
                'id'      => $id,
                'name'    => $tr( \expHelp::setting( $group, 'Name', $id ) ),
                'url'     => $url,
                'button'  => $tr( \expHelp::setting( $group, 'Button', '' ) ),
                'page'    => $page( \expHelp::setting( $group, 'Page', '' ) ),
                'mission' => $tr( \expHelp::setting( $group, 'Mission', '' ) ),
                'funds'   => $funds,
            );
        }
        if ( !$organizations )
            return false;
        return array(
            'title'         => $tr( \expHelp::setting( 'SupportSettings', 'Title', 'Support Exponential' ) ),
            'text'          => $tr( \expHelp::setting( 'SupportSettings', 'Text', '' ) ),
            'organizations' => $organizations,
            'more'          => $page( \expHelp::setting( 'SupportSettings', 'MorePage', '' ) ),
        );
    }
}

}
