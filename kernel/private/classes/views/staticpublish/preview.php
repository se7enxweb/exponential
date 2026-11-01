<?php
/**
 * The staticpublish/preview view (/staticpublish/preview/<target>): the build in the target's folder that is not
 * published yet, before Publish:
 *
 *   - the diff against the last publish (the manifest of the last commit): every page and file added, changed or
 *     removed, with its size (expStaticAdmin::diffDetails())
 *   - the report of the last build (exp-static/1): pages refused, files missing, the link check, warnings
 *   - the limits check: the build against the host's limits (GitHub Pages: 1 GB a site, 100 MB a file)
 *   - a local preview: the folder served by staticpublish/serve behind a signed link of an hour
 *
 * Publish (staticpublish/publish, "Verify it's you") starts the publish in the background (expStaticJob:
 * exp:static:publish: commit, tag static/<target>/<n>, push, never forced) and opens its page. Discard
 * (staticpublish/build) throws the build away: the folder goes back to the last publish. Policy staticpublish/read.
 *
 * @copyright Copyright (C) 1998 - 2026 Exponential Foundation and 7x. All rights reserved.
 * @license GNU General Public License v2.0 (or any later version)
 * @package kernel
 */

namespace Exponential\View\Kernel\Staticpublish
{

class Preview extends Page
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
        $workDir = $publisher->workDirectory();
        $running = \expStaticJob::running( $workDir, $target->id() );

        if ( self::isPost() && isset( $_POST['PublishButton'] ) )
        {
            if ( !\expStaticAdmin::canDo( 'publish', $target->id() ) )
                return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
            if ( !\expStaticTarget::enabled() )
                $messages[] = array( 'kind' => 'bad', 'text' => self::t( 'Static publishing is switched off. Switch it on in Setup > Static publishing first.' ) );
            else if ( empty( $_POST['ConfirmPublish'] ) )
                $messages[] = array( 'kind' => 'bad', 'text' => self::t( 'Tick "I have checked the changes" first.' ) );
            else
            {
                $message = isset( $_POST['PublishMessage'] ) ? (string)$_POST['PublishMessage'] : '';
                $started = \expStaticJob::start( 'publish', $target->id(), array( 'message' => $message ), self::login(), array( 'work_dir' => $workDir ) );
                if ( $started['ok'] )
                    return $Module->redirectTo( '/staticpublish/log/' . $started['id'] );
                $messages[] = array( 'kind' => 'bad', 'text' => self::refusal( $started ) );
            }
        }
        else if ( self::isPost() && isset( $_POST['DiscardButton'] ) )
        {
            if ( !\expStaticAdmin::canDo( 'build', $target->id() ) )
                return $Module->handleError( \eZError::KERNEL_ACCESS_DENIED, 'kernel' );
            $problem = '';
            $lock = $running ? null : \expStaticLock::acquire( $workDir, $target->id(), 'discard', $problem );
            if ( $lock === null )
                $messages[] = array( 'kind' => 'bad', 'text' => $running ? self::t( 'A job of this target is still going on. Wait for it to end.' ) : $problem );
            else
            {
                $result = array();
                $ok = $publisher->repository()->discard( $result );
                $lock->release();
                $publisher->credentials()->cleanup();
                $messages[] = $ok ? array( 'kind' => 'ok', 'text' => self::t( 'The build was thrown away: the folder holds the last publish again.' ) )
                                  : array( 'kind' => 'bad', 'text' => self::t( 'The build could not be thrown away: %problem', array( '%problem' => implode( '; ', $result['problems'] ) ) ) );
            }
        }

        $folder = rtrim( $publisher->checkoutDirectory() . '/' . $target->folder(), '/' );
        $built = \expStaticManifest::load( $folder );
        $committed = null;
        try
        {
            $committed = $publisher->repository()->committedManifest();
        }
        catch ( \Throwable $e )
        {
        }
        $diff = \expStaticAdmin::diffDetails( $committed, $built );
        $changes = $diff['counts']['added'] + $diff['counts']['changed'] + $diff['counts']['removed'];
        $last = \expStaticJob::lastBuild( $workDir, $target->id() );
        $userId = (int)\eZUser::currentUser()->attribute( 'contentobject_id' );

        return self::page( 'preview.tpl', array(
            'github' => \expStaticAdmin::github( $target, true ),
            'target' => \expStaticAdmin::overview( $target ),
            'messages' => $messages,
            'enabled' => \expStaticTarget::enabled(),
            'has_build' => $built !== null,
            'published' => $committed !== null,
            'changes' => $changes,
            'diff' => $diff,
            'limits' => \expStaticAdmin::limits( $built, \expStaticAdmin::siteLimit(), \expStaticAdmin::fileLimit() ),
            'build_job' => $last,
            'report' => $last && is_array( $last['report'] ) ? $last['report'] : null,
            'running' => $running,
            'preview_url' => $built !== null ? \expStaticAdmin::previewPath( $target->id(), $userId ) : '',
            'github_site' => \expStaticAdmin::GITHUB_SITE_BYTES,
            'github_file' => \expStaticAdmin::GITHUB_FILE_BYTES,
            'view' => 'preview',
        ), self::path( self::t( 'Preview' ), $target ) );
    }
}

}
?>
