{* The package list (package/list[/<repository>]).

   What packages are and where they live, the figures over every repository, one tile per repository, the
   directories that could not be read, a search with filters by repository, type and state and a sort, then one
   card per package: name, version, type, install state, whether the installer takes it as a source, summary,
   repository, maintainers, what it requires and what requires it, files and size, last change; View and Download.
   Ticked packages are removed through a confirmation (package/confirmremove.tpl) that says what goes.

   The same file is in design/admin and design/admin4. Everything comes from the view (kernel/private/classes/
   views/package/list.php, eZPackageCatalog); nothing is fetched here. Works without javascript; the script only
   adds "select all". The POST names are those of before. Guide: doc/guides/packages.md *}
{include uri='design:package/exp_style.tpl'}

{def $q = $package_query
     $repo_base = cond( $repository_id|ne( '' ), concat( '/package/list/', $repository_id ), '/package/list' )
     $state_labels = hash( 'installed', 'Installed'|i18n( 'design/admin/package/list' ),
                           'not_installed', 'Not installed'|i18n( 'design/admin/package/list' ),
                           'import', 'Imported, nothing to install'|i18n( 'design/admin/package/list' ),
                           'no_items', 'No install items'|i18n( 'design/admin/package/list' ),
                           'installer', 'Installer sources'|i18n( 'design/admin/package/list' ) )
     $state_badges = hash( 'installed', 'is-ok', 'not_installed', '', 'import', 'is-info', 'no_items', 'is-warn' )
     $sort_labels = hash( 'name', 'Name'|i18n( 'design/admin/package/list' ),
                          'changed', 'Last change'|i18n( 'design/admin/package/list' ),
                          'size', 'Size'|i18n( 'design/admin/package/list' ),
                          'version', 'Version'|i18n( 'design/admin/package/list' ),
                          'type', 'Type'|i18n( 'design/admin/package/list' ),
                          'repository', 'Repository'|i18n( 'design/admin/package/list' ),
                          'state', 'Install state'|i18n( 'design/admin/package/list' ) )
     $reason_labels = hash( 'vendor_repository', "In the setup wizard's repository"|i18n( 'design/admin/package/list' ),
                            'site_package', 'A site package the setup wizard offers'|i18n( 'design/admin/package/list' ),
                            'required_by_source', 'Required by another installer source'|i18n( 'design/admin/package/list' ) )
     $filtered = or( $q.search|ne( '' ), $q.type|ne( '' ), $q.state|ne( '' ) )
     $can_export = fetch( package, can_export )}

<div class="context-block exp-packages">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{if $repository_id|ne( '' )}{'Packages in %repository'|i18n( 'design/admin/package/list',, hash( '%repository', $repository_id ) )|wash}{else}{'Packages'|i18n( 'design/admin/package/list' )}{/if}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'A package carries content classes, content, extensions, settings or a whole site, to install here or to take to another installation. Packages are kept in repositories under %path: "local" holds the packages made or imported here, the others hold packages by vendor. The setup wizard installs new sites from these packages, and the published packages are built from them, so a package marked "Installer source" should be changed only on purpose.'|i18n( 'design/admin/package/list',, hash( '%path', $package_storage_path ) )|wash}</p>

{def $feedback = first_set( $package_feedback, false() )}
{if $feedback}
    {if eq( $feedback.type, 'removed' )}
<div class="exp-feedback is-ok" role="status">{if $feedback.names|count|gt( 0 )}{'Removed: %names.'|i18n( 'design/admin/package/list',, hash( '%names', $feedback.names|implode( ', ' ) ) )|wash}{else}{'Nothing was removed.'|i18n( 'design/admin/package/list' )}{/if}</div>
    {elseif eq( $feedback.type, 'cancelled' )}
<div class="exp-feedback is-info" role="status">{'Package removal was canceled.'|i18n( 'design/admin/package/list' )}</div>
    {elseif eq( $feedback.type, 'none_selected' )}
<div class="exp-feedback is-warn" role="alert">{'No package was selected. Tick the packages to remove first.'|i18n( 'design/admin/package/list' )}</div>
    {/if}
{/if}
{undef $feedback}

<section aria-labelledby="package-overview-title">
<h2 class="exp-sr" id="package-overview-title">{'Overview'|i18n( 'design/admin/package/list' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure"><strong>{$package_totals.packages}</strong><span>{'Packages'|i18n( 'design/admin/package/list' )}</span></li>
    <li class="exp-figure"><strong>{$package_totals.installed}</strong><span>{'Installed'|i18n( 'design/admin/package/list' )}</span></li>
    <li class="exp-figure"><strong>{$package_totals.not_installed}</strong><span>{'Not installed'|i18n( 'design/admin/package/list' )}</span></li>
    <li class="exp-figure"><strong>{$package_totals.import}</strong><span>{'Imported only'|i18n( 'design/admin/package/list' )}</span></li>
    <li class="exp-figure"><strong>{$package_totals.installer}</strong><span>{'Installer sources'|i18n( 'design/admin/package/list' )}</span></li>
    <li class="exp-figure"><strong>{$package_totals.bytes|si( byte )}</strong><span>{'%count files'|i18n( 'design/admin/package/list',, hash( '%count', $package_totals.files ) )}</span></li>
    <li class="exp-figure{if $package_totals.problems|gt( 0 )} is-attention{/if}"><strong>{$package_totals.problems}</strong><span>{'Unreadable'|i18n( 'design/admin/package/list' )}</span></li>
</ul>
</section>

<section class="exp-section" aria-labelledby="package-repos-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="package-repos-title">{'Repositories'|i18n( 'design/admin/package/list' )}</h2>
    <p>{'Each repository is a directory of the package storage. Choose one to list only its packages.'|i18n( 'design/admin/package/list' )}</p>
</div>
<ul class="exp-repos">
    <li class="exp-repo{if $repository_id|eq( '' )} is-current{/if}"><a href={'/package/list'|ezurl}{if $repository_id|eq( '' )} aria-current="page"{/if}>
        <span class="exp-repo-name">{'All repositories'|i18n( 'design/admin/package/list' )}</span>
        <span class="exp-repo-count">{$package_totals.packages} <span>{'packages in %count repositories'|i18n( 'design/admin/package/list',, hash( '%count', $package_totals.repositories ) )}</span></span>
    </a></li>
{foreach $package_repositories as $repo}
    <li class="exp-repo{if $repository_id|eq( $repo.id )} is-current{/if}"><a href={concat( '/package/list/', $repo.id )|ezurl}{if $repository_id|eq( $repo.id )} aria-current="page"{/if}>
        <span class="exp-repo-name"><code>{$repo.id|wash}</code>
            {if $repo.is_vendor}<span class="exp-badge is-info">{"Setup wizard's repository"|i18n( 'design/admin/package/list' )}</span>{/if}
            {if eq( $repo.type, 'local' )}<span class="exp-badge">{'Made or imported here'|i18n( 'design/admin/package/list' )}</span>{/if}
        </span>
        <span class="exp-repo-count">{$repo.counts.packages} <span>{if $repo.counts.packages|eq( 1 )}{'package'|i18n( 'design/admin/package/list' )}{else}{'packages'|i18n( 'design/admin/package/list' )}{/if}, {$repo.counts.bytes|si( byte )}</span></span>
        <ul class="exp-repo-lines">
            <li>{'%installed installed, %not not installed, %import imported only'|i18n( 'design/admin/package/list',, hash( '%installed', $repo.counts.installed, '%not', sum( $repo.counts.not_installed, $repo.counts.no_items ), '%import', $repo.counts.import ) )}</li>
            {if $repo.counts.installer|gt( 0 )}<li>{'%count installer sources'|i18n( 'design/admin/package/list',, hash( '%count', $repo.counts.installer ) )}</li>{/if}
            {if $repo.counts.orphans|gt( 0 )}<li>{'%count directories without a package definition (not listed)'|i18n( 'design/admin/package/list',, hash( '%count', $repo.counts.orphans ) )}</li>{/if}
            {if $repo.counts.problems|gt( 0 )}<li class="is-warn">{'%count unreadable'|i18n( 'design/admin/package/list',, hash( '%count', $repo.counts.problems ) )}</li>{/if}
        </ul>
    </a></li>
{/foreach}
</ul>
</section>

{if $package_problems|count|gt( 0 )}
<div class="exp-feedback is-warn" role="status">
    <p><strong>{'These directories have a package definition that cannot be read, and are left out of the list:'|i18n( 'design/admin/package/list' )}</strong></p>
    <ul>
    {foreach $package_problems as $problem}
        <li><code>{concat( $package_storage_path, '/', $problem.repository, '/', $problem.directory )|wash}</code>:
            {if eq( $problem.reason, 'definition' )}{'package.xml is not a well formed package definition, or names no package.'|i18n( 'design/admin/package/list' )}
            {elseif eq( $problem.reason, 'mismatch' )}{'package.xml names another package than its directory.'|i18n( 'design/admin/package/list' )}
            {else}{'the directory name is not a valid package name.'|i18n( 'design/admin/package/list' )}{/if}</li>
    {/foreach}
    </ul>
</div>
{/if}

<section aria-labelledby="package-find-title">
<h2 class="exp-sr" id="package-find-title">{'Find packages'|i18n( 'design/admin/package/list' )}</h2>
<form class="exp-toolbar" method="get" action={'/package/list'|ezurl}>
    <input type="hidden" name="PackageFilter" value="1" />
    <div class="exp-field">
        <label for="package-search">{'Search'|i18n( 'design/admin/package/list' )}</label>
        <input type="search" id="package-search" name="SearchText" value="{$q.search|wash}" maxlength="100" aria-describedby="package-search-help" />
        <span class="exp-help" id="package-search-help">{'Name, summary, type, vendor, version, maintainer or a required package.'|i18n( 'design/admin/package/list' )}</span>
    </div>
    <div class="exp-field">
        <label for="package-repository">{'Repository'|i18n( 'design/admin/package/list' )}</label>
        <select id="package-repository" name="Repository">
            <option value="">{'All repositories'|i18n( 'design/admin/package/list' )}</option>
            {foreach $package_repositories as $repo}
            <option value="{$repo.id|wash}"{if $repository_id|eq( $repo.id )} selected="selected"{/if}>{$repo.id|wash} ({$repo.counts.packages})</option>
            {/foreach}
        </select>
    </div>
    <div class="exp-field">
        <label for="package-type">{'Type'|i18n( 'design/admin/package/list' )}</label>
        <select id="package-type" name="Type">
            <option value="">{'Any type'|i18n( 'design/admin/package/list' )}</option>
            {foreach $package_types as $type}
            <option value="{$type|wash}"{if $q.type|eq( $type )} selected="selected"{/if}>{$type|wash}</option>
            {/foreach}
        </select>
    </div>
    <div class="exp-field">
        <label for="package-state">{'State'|i18n( 'design/admin/package/list' )}</label>
        <select id="package-state" name="State">
            <option value="">{'Any state'|i18n( 'design/admin/package/list' )}</option>
            {foreach $package_states as $state}
            <option value="{$state|wash}"{if $q.state|eq( $state )} selected="selected"{/if}>{$state_labels[$state]|wash}</option>
            {/foreach}
        </select>
    </div>
    <div class="exp-field">
        <label for="package-sort">{'Sort by'|i18n( 'design/admin/package/list' )}</label>
        <select id="package-sort" name="Sort">
            {foreach $package_sort_keys as $key}
            <option value="{$key|wash}"{if $q.sort|eq( $key )} selected="selected"{/if}>{$sort_labels[$key]|wash}</option>
            {/foreach}
        </select>
    </div>
    <div class="exp-field">
        <div class="exp-actions">
            <button type="submit" class="exp-btn exp-btn-primary">{'Apply'|i18n( 'design/admin/package/list' )}</button>
            {if $filtered}<a class="exp-btn" href={$package_clear_uri|ezurl}>{'Clear'|i18n( 'design/admin/package/list' )}</a>{/if}
        </div>
    </div>
</form>
</section>

<form name="packagelist" method="post" action={concat( $repo_base, cond( $view_parameters.offset|gt( 0 ), concat( '/offset/', $view_parameters.offset ), '' ) )|ezurl}>

<section class="exp-section" aria-labelledby="package-list-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="package-list-title">{if $filtered}{'Matching packages'|i18n( 'design/admin/package/list' )}{elseif $repository_id|ne( '' )}{'Packages in %repository'|i18n( 'design/admin/package/list',, hash( '%repository', $repository_id ) )|wash}{else}{'All packages'|i18n( 'design/admin/package/list' )}{/if}</h2>
    <span class="exp-meta">{if $package_count|gt( 0 )}{'%from to %to of %count'|i18n( 'design/admin/package/list',, hash( '%from', $package_pager.from, '%to', $package_pager.to, '%count', $package_count ) )}{/if}{if $q.dir|eq( 'desc' )} &middot; {'%sort, descending'|i18n( 'design/admin/package/list',, hash( '%sort', $sort_labels[$q.sort] ) )|wash}{else} &middot; {'%sort, ascending'|i18n( 'design/admin/package/list',, hash( '%sort', $sort_labels[$q.sort] ) )|wash}{/if}
        &middot; <a href={$package_reverse_uri|ezurl}>{'Reverse'|i18n( 'design/admin/package/list' )}</a></span>
    {if and( $package_can_remove, $package_cards|count|gt( 0 ) )}
    <label class="exp-meta exp-js-only" hidden><input type="checkbox" id="package-select-all" /> {'Select all on this page'|i18n( 'design/admin/package/list' )}</label>
    {/if}
</div>

{if $package_cards|count|eq( 0 )}
<p class="exp-empty">{if $package_all_count|eq( 0 )}{'There are no packages yet. Import one with Import new package, or make one with Create new package.'|i18n( 'design/admin/package/list' )}{else}{'No package matches. Clear the search or choose another repository, type or state.'|i18n( 'design/admin/package/list' )}{/if}</p>
{else}
<ul class="exp-pkgs" id="package-list">
{foreach $package_cards as $card}
    {def $card_id = concat( 'package-', $card.name, '-', $card.repository_id )
         $view_uri = concat( '/package/view/full/', $card.name, cond( $card.repository_id|ne( 'local' ), concat( '/', $card.repository_id ), '' ) )}
<li class="exp-pkg{if $card.installer_source} is-source{/if}" id="{$card_id|wash}">
    <div class="exp-pkg-head">
        <div class="exp-pkg-title">
            {if $package_can_remove}
            <label class="exp-select" title="{'Select the package for removal.'|i18n( 'design/admin/package/list' )}">
                <input type="checkbox" name="PackageSelection[]" value="{concat( $card.name, '@', $card.repository_id )|wash}"{if $card.links|gt( 0 )} disabled="disabled"{/if} aria-label="{'Select %name for removal'|i18n( 'design/admin/package/list',, hash( '%name', $card.name ) )|wash}" />
            </label>
            {/if}
            <h3 id="{$card_id|wash}-title"><a href={$view_uri|ezurl}>{$card.name|wash}</a></h3>
            {if $card.version|ne( '' )}<span class="exp-version" title="{'Version'|i18n( 'design/admin/package/list' )}">{$card.version|wash}</span>{/if}
            <ul class="exp-badges">
                {if $card.type|ne( '' )}<li class="exp-badge">{$card.type|wash}</li>{/if}
                <li class="exp-badge {$state_badges[$card.state]}">{$state_labels[$card.state]|wash}</li>
                {if $card.installer_source}<li class="exp-badge is-info" title="{foreach $card.installer_reasons as $reason}{$reason_labels[$reason]|wash}{delimiter}; {/delimiter}{/foreach}">{'Installer source'|i18n( 'design/admin/package/list' )}</li>{/if}
                {if $card.links|gt( 0 )}<li class="exp-badge is-warn">{'Contains links'|i18n( 'design/admin/package/list' )}</li>{/if}
            </ul>
        </div>
        <div class="exp-actions">
            <a class="exp-btn exp-btn-small" href={$view_uri|ezurl} aria-describedby="{$card_id|wash}-title">{'View'|i18n( 'design/admin/package/list' )}</a>
            {if $can_export}
            <a class="exp-btn exp-btn-small" href={concat( '/package/export/', $card.name, '/', $card.repository_id )|ezurl} aria-describedby="{$card_id|wash}-title" title="{'Download the package as an .ezpkg file.'|i18n( 'design/admin/package/list' )}">{'Download'|i18n( 'design/admin/package/list' )}</a>
            {/if}
        </div>
    </div>
    {if $card.summary|ne( '' )}<p class="exp-pkg-summary">{$card.summary|wash}</p>{/if}
    <dl class="exp-facts">
        <div>
            <dt>{'Repository'|i18n( 'design/admin/package/list' )}</dt>
            <dd><a href={concat( '/package/list/', $card.repository_id )|ezurl}><code>{$card.repository_id|wash}</code></a>{if $card.vendor|ne( '' )} &middot; {$card.vendor|wash}{/if}</dd>
        </div>
        <div>
            <dt>{'Maintainers'|i18n( 'design/admin/package/list' )}</dt>
            <dd>{if $card.maintainers|count|eq( 0 )}{'None named'|i18n( 'design/admin/package/list' )}{else}{foreach $card.maintainers as $maintainer}{$maintainer.name|wash}{if $maintainer.role|ne( '' )} ({$maintainer.role|wash}){/if}{delimiter}, {/delimiter}{/foreach}{/if}</dd>
        </div>
        <div>
            <dt>{'Requires'|i18n( 'design/admin/package/list' )}</dt>
            <dd>{if $card.requires|count|eq( 0 )}{'Nothing'|i18n( 'design/admin/package/list' )}{else}<ul class="exp-deps">{foreach $card.requires as $require}<li{if $require.present|not} class="is-missing" title="{'Not in any repository'|i18n( 'design/admin/package/list' )}"{/if}>{$require.name|wash}{if $require.present|not} ({'missing'|i18n( 'design/admin/package/list' )}){/if}</li>{/foreach}</ul>{/if}</dd>
        </div>
        <div>
            <dt>{'Required by'|i18n( 'design/admin/package/list' )}</dt>
            <dd>{if $card.required_by|count|eq( 0 )}{'No package'|i18n( 'design/admin/package/list' )}{else}{$card.required_by|implode( ', ' )|wash}{/if}</dd>
        </div>
        <div>
            <dt>{'Size'|i18n( 'design/admin/package/list' )}</dt>
            <dd>{$card.bytes|si( byte )}, {'%count files'|i18n( 'design/admin/package/list',, hash( '%count', $card.files ) )}</dd>
        </div>
        <div>
            <dt title="{'The newest file of the package directory'|i18n( 'design/admin/package/list' )}">{'Last change'|i18n( 'design/admin/package/list' )}</dt>
            <dd>{if $card.changed}{$card.changed|l10n( shortdatetime )}{else}{'Unknown'|i18n( 'design/admin/package/list' )}{/if}{if $card.packaging_timestamp|gt( 0 )}<br /><span class="exp-muted">{'Packaged %date'|i18n( 'design/admin/package/list',, hash( '%date', $card.packaging_timestamp|l10n( shortdate ) ) )}</span>{/if}</dd>
        </div>
    </dl>
</li>
    {undef $card_id $view_uri}
{/foreach}
</ul>
{/if}

<div class="exp-listfoot">
    {* The sizes come from admininterface.ini [PaginationSettings]; the preference stores the position in that list. *}
    <p class="exp-sizes">
        <span>{'Per page'|i18n( 'design/admin/package/list' )}:</span>
    {foreach $limit_choices as $limit_index => $limit_option}
        {if eq( $limit_index|inc, $limit_choice )}
        <span class="current" aria-current="true">{$limit_option}</span>
        {else}
        <a href={concat( '/user/preferences/set/admin_package_list_limit/', $limit_index|inc )|ezurl} title="{'Show %count packages per page.'|i18n( 'design/admin/package/list',, hash( '%count', $limit_option ) )}">{$limit_option}</a>
        {/if}
    {/foreach}
    </p>
    {if $package_pager.pages|gt( 1 )}
    <nav aria-label="{'Pages'|i18n( 'design/admin/package/list' )}">
    <ul class="exp-pages">
        <li>{if $package_pager.prev}<a href={$package_pager.prev|ezurl} rel="prev">{'Previous'|i18n( 'design/admin/package/list' )}</a>{else}<span class="is-disabled">{'Previous'|i18n( 'design/admin/package/list' )}</span>{/if}</li>
        {if $package_pager.gap_start}<li><a href={$package_pager.first|ezurl}>1</a></li><li><span class="is-disabled">&hellip;</span></li>{/if}
        {foreach $package_pager.items as $item}
        <li>{if $item.current}<span aria-current="page">{$item.number}</span>{else}<a href={$item.uri|ezurl}>{$item.number}</a>{/if}</li>
        {/foreach}
        {if $package_pager.gap_end}<li><span class="is-disabled">&hellip;</span></li><li><a href={$package_pager.last|ezurl}>{$package_pager.pages}</a></li>{/if}
        <li>{if $package_pager.next}<a href={$package_pager.next|ezurl} rel="next">{'Next'|i18n( 'design/admin/package/list' )}</a>{else}<span class="is-disabled">{'Next'|i18n( 'design/admin/package/list' )}</span>{/if}</li>
    </ul>
    </nav>
    {/if}
</div>
</section>

<div class="exp-bottombar">
    <p class="exp-meta">{if $package_can_remove}{'Remove selected asks first and says what goes. Removing a package deletes its files from the repository; what it installed stays on the site.'|i18n( 'design/admin/package/list' )}{else}{'You are not allowed to remove packages (package/remove).'|i18n( 'design/admin/package/list' )}{/if}</p>
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-outline-danger" name="RemovePackageButton" value="1"{if or( $package_can_remove|not, $package_cards|count|eq( 0 ) )} disabled="disabled"{/if}>{'Remove selected'|i18n( 'design/admin/package/list' )}</button>
        <button type="submit" class="exp-btn" name="InstallPackageButton" value="1"{if $package_can_import|not} disabled="disabled" title="{'You are not allowed to import packages (package/import).'|i18n( 'design/admin/package/list' )}"{/if}>{'Import new package'|i18n( 'design/admin/package/list' )}</button>
        <button type="submit" class="exp-btn exp-btn-primary" name="CreatePackageButton" value="1"{if $package_can_create|not} disabled="disabled" title="{'You are not allowed to create packages (package/create).'|i18n( 'design/admin/package/list' )}"{/if}>{'Create new package'|i18n( 'design/admin/package/list' )}</button>
    </div>
</div>

</form>

</div></div></div>

</div>

{literal}
<script>
(function () {
    var all = document.getElementById( 'package-select-all' );
    var list = document.getElementById( 'package-list' );
    if ( !all || !list ) return;
    var boxes = list.querySelectorAll( 'input[name="PackageSelection[]"]:not([disabled])' );
    if ( !boxes.length ) return;
    all.parentNode.hidden = false;
    function sync() {
        var n = 0;
        for ( var i = 0; i < boxes.length; i++ ) { if ( boxes[i].checked ) n++; boxes[i].closest( '.exp-pkg' ).classList.toggle( 'is-selected', boxes[i].checked ); }
        all.checked = n === boxes.length; all.indeterminate = n > 0 && n < boxes.length;
    }
    all.addEventListener( 'change', function () { for ( var i = 0; i < boxes.length; i++ ) boxes[i].checked = all.checked; sync(); } );
    for ( var i = 0; i < boxes.length; i++ ) boxes[i].addEventListener( 'change', sync );
    sync();
})();
</script>
{/literal}
{undef $q $repo_base $state_labels $state_badges $sort_labels $reason_labels $filtered $can_export}
