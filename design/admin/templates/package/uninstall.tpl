{* The uninstall step of a package (package/uninstall/<name>): what uninstalling removes from the site, before
   anything is done. Uninstall package (UninstallPackageButton) starts it; Skip uninstallation (SkipPackageButton)
   returns to the package's page. The same names as before. Guide: doc/guides/packages.md *}
{include uri='design:package/exp_style.tpl'}
<div id="package">

<form method="post" action={concat( 'package/uninstall/', $package.name )|ezurl}>

<div class="context-block exp-packages exp-standalone">

<div class="box-header"><div class="box-ml">
<div class="exp-title-row">
    <h1 class="context-title">{'Uninstall package'|i18n('design/admin/package')}</h1>
    <span class="exp-title-key">{$package.name|wash}</span>
    <span class="exp-version">{$package.version-number|wash}-{$package.release-number|wash}</span>
</div>
</div></div>

<div class="box-ml"><div class="box-mr"><div class="box-content">

    <p class="exp-meta">
        <a href={concat( 'package/view/full/', $package.name )|ezurl}>&laquo; {'Back to the package'|i18n('design/admin/package')}</a>
        &middot; <a href={'package/list'|ezurl}>{'Package list'|i18n('design/admin/package')}</a>
    </p>

    <p class="exp-intro">{'The package can be uninstalled from your system. Uninstalling the package will remove any installed files, content classes etc., depending on the package.
If you do not want to uninstall the package at this time, you can do so later on the view page for the package.
You can also remove the package without uninstalling it from the package list.'|i18n('design/admin/package')|break}</p>

    <div class="exp-feedback is-warn" role="note">{'Uninstalling removes what the items below created on this site, content included. It cannot be undone.'|i18n('design/admin/package')}</div>

    <div class="exp-panel">
    <h2 class="exp-h2">{'Uninstall items'|i18n('design/admin/package')} ({$uninstall_elements|count})</h2>
    {if $uninstall_elements|count|eq( 0 )}
    <p class="exp-muted">{'This package has nothing to uninstall.'|i18n('design/admin/package')}</p>
    {else}
    <ul class="exp-changelog">
    {section var=uninstall loop=$uninstall_elements}
        <li>{$uninstall.description|wash}</li>
    {/section}
    </ul>
    {/if}
    </div>

</div></div></div>

<div class="exp-bottombar">
    <div class="exp-actions">
        <button class="exp-btn exp-btn-danger" type="submit" name="UninstallPackageButton" value="1">{'Uninstall package'|i18n('design/admin/package')}</button>
        <button class="exp-btn" type="submit" name="SkipPackageButton" value="1">{'Skip uninstallation'|i18n('design/admin/package')}</button>
    </div>
</div>

</div>

</form>

</div>
