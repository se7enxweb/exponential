{* The uninstall step of a package (package/uninstall/<name>): what uninstalling removes from the site, before
   anything is done. Uninstall package (UninstallPackageButton) starts it; Skip uninstallation (SkipPackageButton)
   returns to the package's page. The same names as before. Guide: doc/guides/packages.md *}
<div id="package">

<form method="post" action={concat( 'package/uninstall/', $package.name )|ezurl}>

<div class="context-block a4-page a4-standalone">

<div class="box-header"><div class="box-ml">
<div class="a4-title-row">
    <h1 class="context-title">{'Uninstall package'|i18n('design/admin/package')}</h1>
    <span class="a4-muted">{$package.name|wash}</span>
    <span class="a4-kbd">{$package.version-number|wash}-{$package.release-number|wash}</span>
</div>
</div></div>

<div class="box-ml"><div class="box-mr"><div class="box-content">

    <p class="a4-meta">
        <a href={concat( 'package/view/full/', $package.name )|ezurl}>&laquo; {'Back to the package'|i18n('design/admin/package')}</a>
        &middot; <a href={'package/list'|ezurl}>{'Package list'|i18n('design/admin/package')}</a>
    </p>

    <p class="a4-intro">{'The package can be uninstalled from your system. Uninstalling the package will remove any installed files, content classes etc., depending on the package.
If you do not want to uninstall the package at this time, you can do so later on the view page for the package.
You can also remove the package without uninstalling it from the package list.'|i18n('design/admin/package')|break}</p>

    <div class="a4-notice is-warn" role="note">{'Uninstalling removes what the items below created on this site, content included. It cannot be undone.'|i18n('design/admin/package')}</div>

    <div class="a4-card">
    <h2 class="a4-h2">{'Uninstall items'|i18n('design/admin/package')} ({$uninstall_elements|count})</h2>
    {if $uninstall_elements|count|eq( 0 )}
    <p class="a4-muted">{'This package has nothing to uninstall.'|i18n('design/admin/package')}</p>
    {else}
    <ul class="a4-package-changelog">
    {section var=uninstall loop=$uninstall_elements}
        <li>{$uninstall.description|wash}</li>
    {/section}
    </ul>
    {/if}
    </div>

</div></div></div>

<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        <button class="a4-btn a4-btn-danger" type="submit" name="UninstallPackageButton" value="1">{'Uninstall package'|i18n('design/admin/package')}</button>
        <button class="a4-btn" type="submit" name="SkipPackageButton" value="1">{'Skip uninstallation'|i18n('design/admin/package')}</button>
    </div>
</div>

</div>

</form>

</div>
