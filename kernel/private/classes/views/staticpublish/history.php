<?php
/**
 * The staticpublish/history view (/staticpublish/history/<target>): the publishes and rollbacks of a target
 * (exp_static_run), the tags static/<target>/<n> and the commits of its branch, with links to the commit on the host
 * when the remote is on GitHub. Rollback (staticpublish/publish, "Verify it's you", for experts: a tick and the tag's
 * name typed again) starts a rollback in the background (expStaticJob: exp:static:rollback --apply): a new commit with
 * the files of the earlier publish, tagged and pushed like a publish; history is never rewritten and no tag moves.
 * Policy staticpublish/read.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Staticpublish
{

class History extends Page
{
    public function run( array $scope )
    {
        $Params = $scope['Params'];
        $Module = $Params['Module'];
        $target = self::target( $Params, 'read' );
        if ( $target === null )
            return $Module->handleError( \eZError::KERNEL_NOT_FOUND, 'kernel' );
        $messages = array();
        $publisher = \expStaticAdmin::publisher( $target );
        $repo = $publisher->repository();
        $tags = array();
        $commits = array();
        try
        {
            $tags = $repo->tagList();
            $commits = $repo->commits( 30 );
        }
        catch ( \Throwable $e )
        {
            $messages[] = array( 'kind' => 'bad', 'text' => $e->getMessage() );
        }
        $publisher->credentials()->cleanup();
        $chosen = '';

        if ( self::isPost() && isset( $_POST['RollbackButton'] ) )
        {
            if ( !\expStaticAdmin::canDo( 'publish', $target->id() ) )
                return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
            $chosen = isset( $_POST['RollbackTag'] ) ? trim( (string)$_POST['RollbackTag'] ) : '';
            $typed = isset( $_POST['RollbackTyped'] ) ? trim( (string)$_POST['RollbackTyped'] ) : '';
            $names = array_column( $tags, 'name' );
            if ( !\expStaticTarget::enabled() )
                $messages[] = array( 'kind' => 'bad', 'text' => self::t( 'Static publishing is switched off. Switch it on in Setup > Static publishing first.' ) );
            else if ( !in_array( $chosen, $names, true ) )
                $messages[] = array( 'kind' => 'bad', 'text' => self::t( 'Choose the publish to go back to.' ) );
            else if ( $tags && $tags[0]['name'] === $chosen )
                $messages[] = array( 'kind' => 'bad', 'text' => self::t( '%tag is the publish the site has now.', array( '%tag' => $chosen ) ) );
            else if ( empty( $_POST['RollbackExpert'] ) || $typed !== $chosen )
                $messages[] = array( 'kind' => 'bad', 'text' => self::t( 'A rollback is for experts: tick "I know what this changes" and type the name of the tag again.' ) );
            else
            {
                $started = \expStaticJob::start( 'rollback', $target->id(), array( 'to' => $chosen ), self::login(),
                                                 array( 'work_dir' => $publisher->workDirectory() ) );
                if ( $started['ok'] )
                    return $Module->redirectTo( '/staticpublish/log/' . $started['id'] );
                $messages[] = array( 'kind' => 'bad', 'text' => self::refusal( $started ) );
            }
        }

        $runs = array();
        try
        {
            foreach ( \expStaticStatus::runs( \eZDB::instance(), $target->id(), 50 ) as $run )
                $runs[] = \expStaticAdmin::runView( $run, $target->id() );
        }
        catch ( \Throwable $e )
        {
        }
        return self::page( 'history.tpl', array(
            'github' => \expStaticAdmin::github( $target, true ),
            'target' => \expStaticAdmin::overview( $target ),
            'messages' => $messages,
            'runs' => $runs,
            'tags' => $tags,
            'commits' => $commits,
            'commit_url' => self::commitUrl( (string)$target->setting( 'Remote' ) ),
            'chosen' => $chosen,
            'running' => \expStaticJob::running( $publisher->workDirectory(), $target->id() ),
            'view' => 'history',
        ), self::path( self::t( 'History' ), $target ) );
    }

    /**
     * The address of a commit on the host, for a remote on GitHub ('' for another host). Pure.
     *
     * @param string $remote
     * @return string https://github.com/<owner>/<repo>/commit/ (the commit id goes after it), or ''
     */
    public static function commitUrl( $remote )
    {
        return \expStaticAdmin::commitUrl( $remote );
    }
}

}
?>
