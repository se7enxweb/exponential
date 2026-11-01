{* The staticpublish/build view: Build starts a full build in the background; the job going on, or the last build, with
   its progress (followed live) and report. Variables: target (expStaticAdmin::overview()), messages, enabled, view, and
   those of parts/job.tpl (job, job_running, progress, meter, progress_offset, lines, report, job_errors). *}
<div class="a4-page a4-setup-publish" id="exp-staticpublish-build">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <h1 class="a4-pagehead-title context-title">{'Build %name'|i18n( 'design/admin/staticpublish',, hash( '%name', $target.name ) )|wash}</h1>
        <p class="a4-pagehead-sub">{'A build fetches every page of %site as a visitor who is not signed in sees it, rewrites the links, copies the files and writes the result to the target\'s folder. Nothing is published until you check the build and press Publish.'|i18n( 'design/admin/staticpublish',, hash( '%site', $target.siteaccess ) )|wash}</p>
    </div>
    <div class="a4-pagehead-actions"><a class="a4-btn" href={'exphelp/viewer/help/static-publishing/build-preview-and-publish.md'|ezurl}>{'Help'|i18n( 'design/admin/staticpublish' )}</a></div>
</div>

{include uri='design:staticpublish/parts/tabs.tpl' target=$target view=$view}

{foreach $messages as $m}<div class="a4-notice is-bad" role="alert"><p>{$m.text|wash}</p></div>{/foreach}
{if $enabled|not}<div class="a4-notice is-warn" role="status"><p>{'Static publishing is switched off. Switch it on in Setup > Static publishing first.'|i18n( 'design/admin/staticpublish' )} <a href={'staticpublish/dashboard'|ezurl}>{'Static publishing'|i18n( 'design/admin/staticpublish' )}</a></p></div>{/if}
{foreach $target.errors as $error}<div class="a4-notice is-bad" role="alert"><p>{$error|wash}</p></div>{/foreach}

<section class="a4-card a4-section" aria-labelledby="sp-build-start">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sp-build-start">{'Build now'|i18n( 'design/admin/staticpublish' )}</h2>
        <p class="a4-meta">{'It runs in the background as %user; you can close this page.'|i18n( 'design/admin/staticpublish',, hash( '%user', fetch( 'user', 'current_user' ).login ) )|wash}</p></div>
    <div class="a4-card-body">
        {if $target.pending_build}<p class="a4-warn-text">{'The folder holds a build that is not published yet; a new build replaces it.'|i18n( 'design/admin/staticpublish' )} <a href={concat( 'staticpublish/preview/', $target.id )|ezurl}>{'Check it first'|i18n( 'design/admin/staticpublish' )}</a></p>{/if}
    </div>
    <div class="a4-card-foot a4-actions">
        <form method="post" action={concat( 'staticpublish/build/', $target.id )|ezurl}>
            <button type="submit" class="a4-btn a4-btn-primary" name="BuildButton" value="1"{if or( $job_running, $enabled|not, $target.errors|count )} disabled="disabled"{/if}>{'Build'|i18n( 'design/admin/staticpublish' )}</button>
        </form>
        {if and( $job, $job.ended, eq( $job.exit, 0 ) )}<a class="a4-btn" href={concat( 'staticpublish/preview/', $target.id )|ezurl}>{'Preview the build'|i18n( 'design/admin/staticpublish' )}</a>{/if}
    </div>
</section>

{if $job}
{include uri='design:staticpublish/parts/job.tpl' job=$job job_running=$job_running progress=$progress meter=$meter progress_offset=$progress_offset lines=$lines report=$report job_errors=$job_errors}
{else}
<div class="a4-empty a4-section"><p>{'This target has not been built here yet.'|i18n( 'design/admin/staticpublish' )}</p></div>
{/if}

</div>
{if $job_running}{ezscript( array( 'page/staticpublish_job.js' ) )}{/if}
