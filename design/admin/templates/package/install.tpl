{* The first step of a package install (package/install/<name>): what the package will install, before anything
   is done. Install package (InstallPackageButton) starts it; Skip installation (SkipPackageButton) returns to the
   package's page. The same names as before. Guide: doc/guides/packages.md
   What the install meets here ($conflict_report, by remote id) and the option to install the content objects as
   drafts (AsDrafts, DraftsExisting); after an install as drafts $drafts_result lists the drafts.
   Format reference: doc/bc/6.0/package-json-format.md *}
{include uri='design:package/exp_style.tpl'}
<form method="post" action={concat( 'package/install/', $package.name )|ezurl}>

<div class="context-block exp-packages exp-standalone">

<div class="box-header"><div class="box-ml">
<div class="exp-title-row">
    <h1 class="context-title">{if is_set( $drafts_result )}{'Installed as drafts'|i18n('design/admin/package')}{else}{'Install package'|i18n('design/admin/package')}{/if}</h1>
    <span class="exp-title-key">{$package.name|wash}</span>
    <span class="exp-version">{$package.version-number|wash}-{$package.release-number|wash}</span>
    {if $package.format|eq( 'json' )}<span class="exp-version">JSON</span>{/if}
</div>
</div></div>

<div class="box-ml"><div class="box-mr"><div class="box-content">

    <p class="exp-meta install-wizard-links">
        <a href={concat( 'package/view/full/', $package.name )|ezurl}>&laquo; {'Back to the package'|i18n('design/admin/package')}</a>
        &middot; <a href={'package/list'|ezurl}>{'Package list'|i18n('design/admin/package')}</a>
    </p>

{if is_set( $drafts_result )}
    <div class="exp-feedback is-ok" role="status">{'%count drafts were written. Nothing was published: review each draft and publish it, or remove it.'|i18n('design/admin/package',, hash( '%count', $drafts_result.drafts|count ))}</div>
    {if $drafts_result.drafts|count}
    <div class="exp-table-wrap">
    <table class="exp-table">
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
    <div class="exp-feedback is-warn" role="note">
        <p>{'%count drafts wait for a parent that is a draft of this package too. Publish the parent, then install the package as drafts again to give them their place.'|i18n('design/admin/package',, hash( '%count', $drafts_result.waiting|count ))}</p>
    </div>
    {/if}
    {if $drafts_result.skipped|count}
    <div class="exp-panel">
    <h2 class="exp-h2">{'Not installed'|i18n('design/admin/package')} ({$drafts_result.skipped|count})</h2>
    <p class="exp-muted">{'As drafts only content objects are written. These items change the site at once and were left out; install the package normally to install them.'|i18n('design/admin/package')}</p>
    <ul class="exp-changelog">
    {foreach $drafts_result.skipped as $item}<li>{$item.type|wash} {$item.name|wash}</li>{/foreach}
    </ul>
    </div>
    {/if}
{else}
    <p class="exp-intro">{'The package can be installed on your system. Installing the package will copy files, create content classes etc., depending on the package.
If you do not want to install the package at this time, you can do so later on the view page for the package.'|i18n('design/admin/package')|break}</p>

    {if $package.is_installed}
    <div class="exp-feedback is-warn" role="note">{'This package is installed already. Installing it again repeats every item below; content it creates is created again.'|i18n('design/admin/package')}</div>
    {/if}

    <div class="exp-panel">
    <h2 class="exp-h2">{'Install items'|i18n('design/admin/package')} ({$install_elements|count})</h2>
    {if $install_elements|count|eq( 0 )}
    <p class="exp-muted">{'This package has no install items.'|i18n('design/admin/package')}</p>
    {else}
    <ul class="exp-changelog">
    {section var=install loop=$install_elements}
        <li>{$install.description|wash}</li>
    {/section}
    </ul>
    {/if}
    </div>

    {if and( is_set( $conflict_report ), $conflict_report.object_total|gt( 0 ) )}
    <div class="exp-panel">
    <h2 class="exp-h2">{'What the install meets here'|i18n('design/admin/package')}</h2>
    <p class="exp-meta">{'%total content objects, by remote ID: %create new, %update existing (updated, or a new draft), %conflict conflicts, %error without their class.'|i18n('design/admin/package',, hash( '%total', $conflict_report.object_total, '%create', $conflict_report.counts.create, '%update', $conflict_report.counts.update, '%conflict', $conflict_report.counts.conflict, '%error', $conflict_report.counts.error ))}</p>
    {def $report_rows=array()}
    {foreach $conflict_report.objects as $row}{if $row.action|ne( 'create' )}{set $report_rows=$report_rows|append( $row )}{/if}{/foreach}
    {if $report_rows|count}
    <div class="exp-table-wrap">
    <table class="exp-table">
        <tr><th>{'Remote ID'|i18n('design/admin/package')}</th><th>{'Name'|i18n('design/admin/package')}</th><th>{'Class'|i18n('design/admin/package')}</th><th>{'Here'|i18n('design/admin/package')}</th></tr>
        {foreach $report_rows as $row max 50}
        <tr>
            <td><code>{$row.remote_id|wash}</code></td>
            <td>{$row.name|wash}</td>
            <td>{$row.class_identifier|wash}{if $row.class|eq( 'missing' )} &ndash; {'missing'|i18n('design/admin/package')}{/if}</td>
            <td>{switch match=$row.action}
                {case match='update'}{if $row.object_id}<a href={concat( 'content/view/full/', $row.object_id )|ezurl}>{'Exists'|i18n('design/admin/package')}</a>{/if}{/case}
                {case match='conflict'}<strong>{'Conflict'|i18n('design/admin/package')}</strong>: {if $row.existing_class|ne( $row.class_identifier )}{'the remote ID is an object of the class %class'|i18n('design/admin/package',, hash( '%class', $row.existing_class ))|wash}{else}{'a location\'s node remote ID belongs to another object'|i18n('design/admin/package')}{/if}{/case}
                {case}<strong>{'Error'|i18n('design/admin/package')}</strong>: {'the class is neither installed nor in the package'|i18n('design/admin/package')}{/case}
                {/switch}</td>
        </tr>
        {/foreach}
    </table>
    </div>
    {/if}
    {undef $report_rows}
    </div>
    {/if}

    <div class="exp-panel">
    <h2 class="exp-h2">{'Install as drafts'|i18n('design/admin/package')}</h2>
    <p><label><input type="checkbox" name="AsDrafts" value="1" /> {'Install the content objects as drafts'|i18n('design/admin/package')}</label></p>
    <p class="exp-muted">{'Nothing is published and no published object is changed: each draft is reviewed and published by a person. Classes, settings, design files and extensions are not installed.'|i18n('design/admin/package')}</p>
    <p class="exp-meta"><label>{'An object that exists already'|i18n('design/admin/package')}:
        <select name="DraftsExisting">
            <option value="update">{'gets a new draft'|i18n('design/admin/package')}</option>
            <option value="skip">{'is left alone'|i18n('design/admin/package')}</option>
        </select></label></p>
    </div>
{/if}

</div></div></div>

<div class="exp-bottombar">
{if is_set( $drafts_result )}
    <div class="exp-actions">
        <a class="exp-btn exp-btn-primary" href={'content/draft'|ezurl}>{'My drafts'|i18n('design/admin/package')}</a>
        <a class="exp-btn" href={concat( 'package/view/full/', $package.name )|ezurl}>{'Back to the package'|i18n('design/admin/package')}</a>
    </div>
{else}
    <p class="exp-meta">{'Each item is installed in turn; a step may ask how to handle a conflict.'|i18n('design/admin/package')}</p>
    <div class="exp-actions">
        <button class="exp-btn exp-btn-primary" type="submit" name="InstallPackageButton" value="1">{'Install package'|i18n('design/admin/package')}</button>
        <button class="exp-btn" type="submit" name="SkipPackageButton" value="1">{'Skip installation'|i18n('design/admin/package')}</button>
    </div>
{/if}
</div>

</div>

</form>
