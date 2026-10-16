{* The first step of a package install (package/install/<name>): what the package will install, before anything
   is done. Install package (InstallPackageButton) starts it; Skip installation (SkipPackageButton) returns to the
   package's page. The same names as before. Guide: doc/guides/packages.md
   What the install meets here ($conflict_report, by remote id) and the option to install the content objects as
   drafts (AsDrafts, DraftsExisting); after an install as drafts $drafts_result lists the drafts.
   Format reference: doc/bc/6.0/package-json-format.md *}
<form method="post" action={concat( 'package/install/', $package.name )|ezurl}>

<div class="context-block a4-page a4-standalone">

<div class="box-header"><div class="box-ml">
<div class="a4-title-row">
    <h1 class="context-title">{if is_set( $background_job )}{'Install in the background'|i18n('design/admin/package')}{elseif is_set( $drafts_result )}{'Installed as drafts'|i18n('design/admin/package')}{else}{'Install package'|i18n('design/admin/package')}{/if}</h1>
    <span class="a4-muted">{$package.name|wash}</span>
    <span class="a4-kbd">{$package.version-number|wash}-{$package.release-number|wash}</span>
    {if $package.format|eq( 'json' )}<span class="a4-kbd">JSON</span>{/if}
</div>
</div></div>

<div class="box-ml"><div class="box-mr"><div class="box-content">

    <p class="a4-meta install-wizard-links">
        <a href={concat( 'package/view/full/', $package.name )|ezurl}>&laquo; {'Back to the package'|i18n('design/admin/package')}</a>
        &middot; <a href={'package/list'|ezurl}>{'Package list'|i18n('design/admin/package')}</a>
    </p>

{if is_set( $upload_validation )}
{def $uv_title=cond( first_set( $upload_validation.source, '' )|eq( 'upload' ), 'Validation on upload'|i18n('design/admin/package'), 'Validation'|i18n('design/admin/package') )}
{include uri='design:package/validation.tpl' validation=$upload_validation validation_title=$uv_title}
{undef $uv_title}
{/if}

{if is_set( $background_job )}
    {if $background_job}
    <div class="a4-notice is-ok" role="status" data-background-job="{$background_job.id|wash}">{'The install runs in the background: %description. The package page and the job page show its progress; nothing waits for this page.'|i18n('design/admin/package',, hash( '%description', $background_job.description ))}</div>
    <p><a class="a4-btn a4-btn-primary" href={concat( 'package/view/full/', $package.name )|ezurl}>{'Progress on the package page'|i18n('design/admin/package')}</a>
       <a class="a4-btn" href={concat( 'content/job/', $background_job.id )|ezurl}>{'Job page'|i18n('design/admin/package')}</a></p>
    {else}
    <div class="a4-notice is-bad" role="alert">{'The background install could not be started: %error'|i18n('design/admin/package',, hash( '%error', $background_error|wash ))}</div>
    {/if}
{elseif is_set( $drafts_result )}

    <div class="a4-notice is-ok" role="status">{'%count drafts were written. Nothing was published: review each draft and publish it, or remove it.'|i18n('design/admin/package',, hash( '%count', $drafts_result.drafts|count ))}</div>
    {if $drafts_result.drafts|count}
    <div class="a4-table-wrap">
    <table class="a4-table">
        <tr><th>{'Draft'|i18n('design/admin/package')}</th><th>{'Remote ID'|i18n('design/admin/package')}</th><th>{'What'|i18n('design/admin/package')}</th></tr>
        {foreach $drafts_result.drafts as $draft}
        <tr>
            <td>{if $draft.version}<a href={concat( 'content/versionview/', $draft.object_id, '/', $draft.version )|ezurl}>{$draft.name|wash}</a>{else}{$draft.name|wash}{/if}</td>
            <td><code>{$draft.remote_id|wash}</code></td>
            <td>{switch match=$draft.action}
                {case match='new_draft'}{'New object, draft'|i18n('design/admin/package')}{/case}
                {case match='draft_of_existing'}{'New draft of an existing object'|i18n('design/admin/package')}{/case}
                {case match='conflict'}<strong>{'Conflict'|i18n('design/admin/package')}</strong>: {'an object of another class has this remote ID; left alone'|i18n('design/admin/package')}{/case}
                {case}{'Existing object left alone'|i18n('design/admin/package')}{/case}
                {/switch}</td>
        </tr>
        {/foreach}
    </table>
    </div>
    {/if}
    {if $drafts_result.waiting|count}
    <div class="a4-notice is-warn" role="note">
        <p>{'%count drafts wait for a parent that is a draft of this package too. Publish the parent, then install the package as drafts again to give them their place.'|i18n('design/admin/package',, hash( '%count', $drafts_result.waiting|count ))}</p>
    </div>
    {/if}
    {if $drafts_result.skipped|count}
    <div class="a4-card">
    <h2 class="a4-h2">{'Not installed'|i18n('design/admin/package')} ({$drafts_result.skipped|count})</h2>
    <p class="a4-muted">{'As drafts only content objects are written. These items change the site at once and were left out; install the package normally to install them.'|i18n('design/admin/package')}</p>
    <ul class="a4-package-changelog">
    {foreach $drafts_result.skipped as $item}<li>{$item.type|wash} {$item.name|wash}</li>{/foreach}
    </ul>
    </div>
    {/if}
{else}
    <p class="a4-intro">{'The package can be installed on your system. Installing the package will copy files, create content classes etc., depending on the package.
If you do not want to install the package at this time, you can do so later on the view page for the package.'|i18n('design/admin/package')|break}</p>

    {if $package.is_installed}
    <div class="a4-notice is-warn" role="note">{'This package is installed already. Installing it again repeats every item below; content it creates is created again.'|i18n('design/admin/package')}</div>
    {/if}

    <div class="a4-card">
    <h2 class="a4-h2">{'Install items'|i18n('design/admin/package')} ({$install_elements|count})</h2>
    {if $install_elements|count|eq( 0 )}
    <p class="a4-muted">{'This package has no install items.'|i18n('design/admin/package')}</p>
    {else}
    <ul class="a4-package-changelog">
    {section var=install loop=$install_elements}
        <li>{$install.description|wash}</li>
    {/section}
    </ul>
    {/if}
    </div>

    {if and( is_set( $conflict_report ), $conflict_report.object_total|gt( 0 ) )}
    <div class="a4-card">
    <h2 class="a4-h2">{'What the install meets here'|i18n('design/admin/package')}</h2>
    <p class="a4-meta">{'%total content objects, by remote ID: %create new, %update existing (updated, or a new draft), %conflict conflicts, %error without their class.'|i18n('design/admin/package',, hash( '%total', $conflict_report.object_total, '%create', $conflict_report.counts.create, '%update', $conflict_report.counts.update, '%conflict', $conflict_report.counts.conflict, '%error', $conflict_report.counts.error ))}</p>
    {if is_set( $conflict_report.parents )}
    <p class="a4-meta" data-report="parents">{'Locations: %top at the top (below the node you choose next), %installed below a node that is here, %in_package below a node of the package, %missing below a node that is neither (they wait or are left out).'|i18n('design/admin/package',, hash( '%top', $conflict_report.parents.top, '%installed', $conflict_report.parents.installed, '%in_package', $conflict_report.parents.in_package, '%missing', $conflict_report.parents.missing ))}</p>
    {/if}
    {* every object of the package (the first 50; the counts above cover all): what the install does with it, and
       where its main location goes *}
    <div class="a4-table-wrap">
    <table class="a4-table" data-report="objects">
        <tr><th>{'Remote ID'|i18n('design/admin/package')}</th><th>{'Name'|i18n('design/admin/package')}</th><th>{'Class'|i18n('design/admin/package')}</th><th>{'Here'|i18n('design/admin/package')}</th><th>{'Parent'|i18n('design/admin/package')}</th></tr>
        {foreach $conflict_report.objects as $row max 50}
        <tr data-action="{$row.action|wash}">
            <td><code>{$row.remote_id|wash}</code></td>
            <td>{$row.name|wash}</td>
            <td>{$row.class_identifier|wash}{if $row.class|eq( 'missing' )} &ndash; {'missing'|i18n('design/admin/package')}{/if}</td>
            <td>{switch match=$row.action}
                {case match='create'}{'New'|i18n('design/admin/package')}{/case}
                {case match='update'}{if first_set( $row.node_id, false() )}<a href={concat( 'content/view/full/', $row.node_id )|ezurl}>{'Exists'|i18n('design/admin/package')}</a>{else}{'Exists'|i18n('design/admin/package')}{/if}{/case}
                {case match='conflict'}<strong>{'Conflict'|i18n('design/admin/package')}</strong>: {if $row.existing_class|ne( $row.class_identifier )}{'the remote ID is an object of the class %class'|i18n('design/admin/package',, hash( '%class', $row.existing_class ))|wash}{else}{'a location\'s node remote ID belongs to another object'|i18n('design/admin/package')}{/if}{/case}
                {case}<strong>{'Error'|i18n('design/admin/package')}</strong>: {'the class is neither installed nor in the package'|i18n('design/admin/package')}{/case}
                {/switch}</td>
            <td>{switch match=first_set( $row.parent, '' )}
                {case match='top'}{'At the top'|i18n('design/admin/package')}{/case}
                {case match='installed'}{'A node here'|i18n('design/admin/package')}{/case}
                {case match='in_package'}{'A node of the package'|i18n('design/admin/package')}{/case}
                {case match='missing'}<strong>{'Missing'|i18n('design/admin/package')}</strong>{/case}
                {case}&ndash;{/case}
                {/switch}</td>
        </tr>
        {/foreach}
    </table>
    </div>
    </div>
    {/if}

    <div class="a4-card">
    <h2 class="a4-h2">{'Install as drafts'|i18n('design/admin/package')}</h2>
    <p><label><input type="checkbox" name="AsDrafts" value="1" /> {'Install the content objects as drafts'|i18n('design/admin/package')}</label></p>
    <p class="a4-muted">{'Nothing is published and no published object is changed: each draft is reviewed and published by a person. Classes, settings, design files and extensions are not installed.'|i18n('design/admin/package')}</p>
    <p class="a4-meta"><label>{'An object that exists already'|i18n('design/admin/package')}:
        <select name="DraftsExisting">
            <option value="update">{'gets a new draft'|i18n('design/admin/package')}</option>
            <option value="skip">{'is left alone'|i18n('design/admin/package')}</option>
        </select></label></p>
    </div>

    {if first_set( $background_available, false() )}
    <div class="a4-card">
    <h2 class="a4-h2">{'Install in the background'|i18n('design/admin/package')}</h2>
    <p><label><input type="checkbox" name="Background" value="1"{if first_set( $background_suggested, false() )} checked="checked"{/if} /> {'Install in the background'|i18n('design/admin/package')}</label></p>
    <p class="a4-muted">{'The next steps ask the same questions; then a background job installs the package, as you, and this page does not wait for it. The package page shows its progress. Suggested for a package with more than %limit content objects.'|i18n('design/admin/package',, hash( '%limit', ezini( 'ContentJobSettings', 'SynchronousLimit', 'content.ini' ) ))}</p>
    </div>
    {/if}
{/if}

</div></div></div>

<div class="a4-toolbar is-bottom">
{if is_set( $background_job )}
    <div class="a4-actions">
        <a class="a4-btn" href={concat( 'package/view/full/', $package.name )|ezurl}>{'Back to the package'|i18n('design/admin/package')}</a>
    </div>
{elseif is_set( $drafts_result )}
    <div class="a4-actions">
        <a class="a4-btn a4-btn-primary" href={'content/draft'|ezurl}>{'My drafts'|i18n('design/admin/package')}</a>
        <a class="a4-btn" href={concat( 'package/view/full/', $package.name )|ezurl}>{'Back to the package'|i18n('design/admin/package')}</a>
    </div>
{else}
    <p class="a4-meta">{'Each item is installed in turn; a step may ask how to handle a conflict.'|i18n('design/admin/package')}</p>
    <div class="a4-actions">
        <button class="a4-btn a4-btn-primary" type="submit" name="InstallPackageButton" value="1">{'Install package'|i18n('design/admin/package')}</button>
        <button class="a4-btn" type="submit" name="SkipPackageButton" value="1">{'Skip installation'|i18n('design/admin/package')}</button>
    </div>
{/if}
</div>

</div>

</form>
