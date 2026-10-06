{* The first step of a package install (package/install/<name>): what the package will install, before anything
   is done. Install package (InstallPackageButton) starts it; Skip installation (SkipPackageButton) returns to the
   package's page. The same names as before. Guide: doc/guides/packages.md *}
{include uri='design:package/exp_style.tpl'}
<form method="post" action={concat( 'package/install/', $package.name )|ezurl}>

<div class="context-block exp-packages exp-standalone">

<div class="box-header"><div class="box-ml">
<div class="exp-title-row">
    <h1 class="context-title">{'Install package'|i18n('design/admin/package')}</h1>
    <span class="exp-title-key">{$package.name|wash}</span>
    <span class="exp-version">{$package.version-number|wash}-{$package.release-number|wash}</span>
</div>
</div></div>

<div class="box-ml"><div class="box-mr"><div class="box-content">

    <p class="exp-meta install-wizard-links">
        <a href={concat( 'package/view/full/', $package.name )|ezurl}>&laquo; {'Back to the package'|i18n('design/admin/package')}</a>
        &middot; <a href={'package/list'|ezurl}>{'Package list'|i18n('design/admin/package')}</a>
    </p>

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

</div></div></div>

<div class="exp-bottombar">
    <p class="exp-meta">{'Each item is installed in turn; a step may ask how to handle a conflict.'|i18n('design/admin/package')}</p>
    <div class="exp-actions">
        <button class="exp-btn exp-btn-primary" type="submit" name="InstallPackageButton" value="1">{'Install package'|i18n('design/admin/package')}</button>
        <button class="exp-btn" type="submit" name="SkipPackageButton" value="1">{'Skip installation'|i18n('design/admin/package')}</button>
    </div>
</div>

</div>

</form>
