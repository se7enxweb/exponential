{* The confirmation of package removal (package/list, RemovePackageButton).

   Lists what goes: per package its directory, files and size, whether it is installed (its install record goes,
   what it installed stays), the packages that require it, and whether the installer takes it as a source - those
   need their own tick before Remove does anything. Selected values that cannot be removed are listed with the
   reason. Remove posts ConfirmRemovePackageButton with the same PackageSelection[] values; the view removes only
   what this page offered. Guide: doc/guides/packages.md *}

{def $plan = $remove_plan
     $reason_labels = hash( 'vendor_repository', "It is in the setup wizard's repository, which the published packages are built from."|i18n( 'design/admin/package/list' ),
                            'site_package', 'It is a site package the setup wizard offers for new sites.'|i18n( 'design/admin/package/list' ),
                            'required_by_source', 'Another installer source requires it.'|i18n( 'design/admin/package/list' ) )
     $refused_labels = hash( 'unsafe', 'not a package name'|i18n( 'design/admin/package/list' ),
                             'not_found', 'no such package (any more)'|i18n( 'design/admin/package/list' ),
                             'ambiguous', 'packages of that name are in more than one repository; remove it from its repository list'|i18n( 'design/admin/package/list' ),
                             'policy', 'you are not allowed to remove packages of its type'|i18n( 'design/admin/package/list' ),
                             'links', 'its directory contains links, and removing it could delete files elsewhere; remove it on the server'|i18n( 'design/admin/package/list' ) )}

<div class="context-block a4-page">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Remove packages?'|i18n( 'design/admin/package/list' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<form name="packageremove" method="post" action={cond( $repository_id|ne( '' ), concat( '/package/list/', $repository_id ), '/package/list' )|ezurl}>

{if eq( $remove_problem, 'installer_source' )}
<div class="a4-notice is-bad" role="alert">{'Nothing was removed: tick the box under the list to confirm that installer sources are to be removed too.'|i18n( 'design/admin/package/list' )}</div>
{elseif eq( $remove_problem, 'not_offered' )}
<div class="a4-notice is-bad" role="alert">{'Nothing was removed: the removal was not confirmed on this page. Check the list and confirm again.'|i18n( 'design/admin/package/list' )}</div>
{/if}

{if $plan.items|count|gt( 0 )}
<p class="a4-intro">{'The directories below are deleted from the package storage with every file in them. This cannot be undone: download a package first to keep a copy. Removing a package does not uninstall it: the classes, content and files it installed stay on the site; only the record that it is installed goes.'|i18n( 'design/admin/package/list' )}</p>

<ul class="a4-figures">
    <li class="a4-figure"><strong>{$plan.items|count}</strong><span>{'Packages'|i18n( 'design/admin/package/list' )}</span></li>
    <li class="a4-figure"><strong>{$plan.bytes|si( byte )}</strong><span>{'%count files'|i18n( 'design/admin/package/list',, hash( '%count', $plan.files ) )}</span></li>
    <li class="a4-figure"><strong>{$plan.installed}</strong><span>{'Installed'|i18n( 'design/admin/package/list' )}</span></li>
    <li class="a4-figure{if $plan.installer_sources|gt( 0 )} is-attention{/if}"><strong>{$plan.installer_sources}</strong><span>{'Installer sources'|i18n( 'design/admin/package/list' )}</span></li>
    <li class="a4-figure{if $plan.required_elsewhere|gt( 0 )} is-attention{/if}"><strong>{$plan.required_elsewhere}</strong><span>{'Required by packages that stay'|i18n( 'design/admin/package/list' )}</span></li>
</ul>

<h2 class="a4-h2">{'What goes'|i18n( 'design/admin/package/list' )}</h2>
<ul class="a4-package-goes">
{foreach $plan.items as $item}
<li class="a4-card{if $item.installer_source} is-info{/if}">
    <input type="hidden" name="PackageSelection[]" value="{$item.value|wash}" />
    <div class="a4-card-title">
        <h3 class="a4-h3">{$item.name|wash}</h3>
        {if $item.version|ne( '' )}<span class="a4-kbd">{$item.version|wash}</span>{/if}
        <ul class="a4-badges">
            {if $item.type|ne( '' )}<li class="a4-badge">{$item.type|wash}</li>{/if}
            {if $item.installed}<li class="a4-badge is-ok">{'Installed'|i18n( 'design/admin/package/list' )}</li>{/if}
            {if $item.installer_source}<li class="a4-badge is-bad">{'Installer source'|i18n( 'design/admin/package/list' )}</li>{/if}
        </ul>
    </div>
    {if $item.summary|ne( '' )}<p class="a4-package-pkg-summary">{$item.summary|wash}</p>{/if}
    <dl class="a4-facts">
        <div><dt>{'Directory'|i18n( 'design/admin/package/list' )}</dt><dd><code>{$item.path|wash}</code></dd></div>
        <div><dt>{'Size'|i18n( 'design/admin/package/list' )}</dt><dd>{$item.bytes|si( byte )}, {'%count files'|i18n( 'design/admin/package/list',, hash( '%count', $item.files ) )}</dd></div>
        <div><dt>{'Required by'|i18n( 'design/admin/package/list' )}</dt><dd>{if $item.required_by|count|eq( 0 )}{'No package'|i18n( 'design/admin/package/list' )}{else}{$item.required_by|implode( ', ' )|wash}{/if}</dd></div>
    </dl>
    {if or( $item.installer_source, $item.installed, $item.required_by|count|gt( 0 ) )}
    <ul class="">
        {foreach $item.installer_reasons as $reason}<li><strong>{'Installer source:'|i18n( 'design/admin/package/list' )}</strong> {$reason_labels[$reason]|wash} {'A new installation or a published package built without it will lack it.'|i18n( 'design/admin/package/list' )}</li>{/foreach}
        {if $item.installed}<li>{'It is installed here. What it installed stays; the site will no longer know which package it came from, and it cannot be uninstalled from here afterwards.'|i18n( 'design/admin/package/list' )}</li>{/if}
        {if $item.required_by|count|gt( 0 )}<li>{'%names require it and will miss it when they are installed.'|i18n( 'design/admin/package/list',, hash( '%names', $item.required_by|implode( ', ' ) ) )|wash}</li>{/if}
    </ul>
    {/if}
</li>
{/foreach}
</ul>
{/if}

{if $plan.refused|count|gt( 0 )}
<div class="a4-notice is-warn" role="status">
    <p><strong>{'These cannot be removed and stay:'|i18n( 'design/admin/package/list' )}</strong></p>
    <ul>
    {foreach $plan.refused as $refused}
        <li><code>{cond( $refused.name|ne( '' ), $refused.name, $refused.value )|wash}</code>: {$refused_labels[$refused.reason]|wash}</li>
    {/foreach}
    </ul>
</div>
{/if}

<div class="a4-toolbar is-bottom">
    {if $plan.items|count|gt( 0 )}
    {if $plan.installer_sources|gt( 0 )}
    <label class="a4-package-confirm-tick"><input type="checkbox" name="ConfirmInstallerSourceRemoval" value="1" /> {'I understand that %count installer source(s) will be deleted, and that new installations and published packages depend on them.'|i18n( 'design/admin/package/list',, hash( '%count', $plan.installer_sources ) )}</label>
    {/if}
    <div class="a4-actions">
        <button type="submit" class="a4-btn a4-btn-danger" name="ConfirmRemovePackageButton" value="1">{'Remove %count package(s)'|i18n( 'design/admin/package/list',, hash( '%count', $plan.items|count ) )}</button>
        <button type="submit" class="a4-btn" name="CancelRemovePackageButton" value="1">{'Cancel'|i18n( 'design/admin/package/list' )}</button>
    </div>
    {else}
    <p class="a4-meta">{'Nothing selected can be removed.'|i18n( 'design/admin/package/list' )}</p>
    <div class="a4-actions">
        <button type="submit" class="a4-btn a4-btn-primary" name="CancelRemovePackageButton" value="1">{'Back to the packages'|i18n( 'design/admin/package/list' )}</button>
    </div>
    {/if}
</div>

</form>

</div></div></div>

</div>
{undef $plan $reason_labels $refused_labels}
