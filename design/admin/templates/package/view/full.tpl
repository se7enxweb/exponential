{* A package's page (package/view/full/<name>[/<repository>]), read-only.

   The package's name, version, type, install state and whether the installer takes it as a source; its actions
   (Compare, Install or Reinstall, Uninstall, Export to file, Download, Remove), each shown by its own policy; the
   details (repository, license, maintainers, documents, release and packaging, dependencies both ways, size and
   last change); what it carries (classes, content, extensions, settings files, other install items, files by
   kind); its changelog; and the contents browser of every file it holds. The card and what it carries come from
   the view (eZPackageCatalog: package_card, package_contents); without them (an older view) the page shows what
   the package itself says. The POST names are those of before (InstallButton, UninstallButton, ExportButton);
   Remove posts RemovePackageButton to the package list, which asks first. Guide: doc/guides/packages.md *}
{let package=fetch( package,item,
                    hash( package_name, $package_name,
                          repository_id, $repository_id ) )}
{include uri='design:package/exp_style.tpl'}
{* The labels of the kinds eZPackageFileBrowser tells files apart by, shared by the filter, the
   file list's type badges and the viewed file's header. *}
{def $kindLabels = hash( 'class', 'Content class'|i18n('design/admin/package'),
                         'object', 'Content object'|i18n('design/admin/package'),
                         'image', 'Image'|i18n('design/admin/package'),
                         'simplefile', 'File'|i18n('design/admin/package'),
                         'document', 'Document'|i18n('design/admin/package'),
                         'package', 'Package definition'|i18n('design/admin/package'),
                         'other', 'Other'|i18n('design/admin/package') )
     $kindShortLabels = hash( 'class', 'Class'|i18n('design/admin/package'),
                              'object', 'Object'|i18n('design/admin/package'),
                              'image', 'Image'|i18n('design/admin/package'),
                              'simplefile', 'File'|i18n('design/admin/package'),
                              'document', 'Document'|i18n('design/admin/package'),
                              'package', 'Package'|i18n('design/admin/package'),
                              'other', 'Other'|i18n('design/admin/package') )
     $card = first_set( $package_card, false() )
     $carries = first_set( $package_contents, false() )
     $repo = first_set( $package_repository, false() )
     $repo_suffix = ''
     $reason_labels = hash( 'vendor_repository', "It is in the setup wizard's repository, which the published packages are built from."|i18n( 'design/admin/package' ),
                            'site_package', 'It is a site package the setup wizard offers for new sites.'|i18n( 'design/admin/package' ),
                            'required_by_source', 'Another installer source requires it.'|i18n( 'design/admin/package' ) )}
{if and( $repo, $repo.id|ne( 'local' ) )}{set $repo_suffix = concat( '/', $repo.id )}{/if}

<div class="context-block exp-packages package-view-full">

<div id="package" class="viewfull">
    <div id="pn-{$package.name|wash}" class="pt-{$package.type|wash}">

<div class="box-header"><div class="box-ml">
<div class="exp-title-row">
    <h1 class="context-title">{$package.name|wash}</h1>
    <span class="exp-version" title="{'Version'|i18n('design/admin/package')}">{$package.version-number|wash}-{$package.release-number|wash}</span>
    <ul class="exp-badges">
        {if $package.type}<li class="exp-badge" title="{'Type'|i18n('design/admin/package')}">{$package.type|wash}</li>{/if}
        {if $package.install_type|eq( 'install' )}
            {if $package.is_installed}
            <li class="exp-badge is-ok">{'Installed'|i18n('design/admin/package')}</li>
            {else}
            <li class="exp-badge">{'Not installed'|i18n('design/admin/package')}</li>
            {/if}
        {else}
            <li class="exp-badge is-info">{'Imported'|i18n('design/admin/package')}</li>
        {/if}
        {if and( $card, $card.installer_source )}<li class="exp-badge is-info">{'Installer source'|i18n( 'design/admin/package' )}</li>{/if}
    </ul>
</div>
</div></div>

<div class="box-ml"><div class="box-mr"><div class="box-content">

{if $package.summary}<p class="exp-intro">{$package.summary|wash}</p>{/if}

{* Each button needs its own policy: Install, Reinstall and Uninstall package/install (the views they open check
   exactly that), Export to file and Download package/export, Remove package/remove. Only a package with install
   type "install" and at least one install item can be installed; for any other the page says why. *}
{def $pvf_installable=and( $package.install_type|eq( 'install' ), $package.install|count|gt( 0 ) )
     $pvf_can_install=$package.can_install
     $pvf_can_export=$package.can_export
     $pvf_can_remove=first_set( $package_can_remove, false() )}
<div class="exp-actionbar">
    <p class="exp-meta"><a href={cond( $repo_suffix|ne( '' ), concat( '/package/list', $repo_suffix ), '/package/list' )|ezurl}>&laquo; {'Back to the packages'|i18n( 'design/admin/package' )}</a></p>
    <div class="exp-actions">
    <form method="post" action={concat( 'package/view/full/', $package.name, $repo_suffix )|ezurl}>
        {if $package.install_type|eq( 'install' )}
            {* Compare (package/compare): a plain link, it only reads *}
            <a class="exp-btn" href={concat( 'package/compare/', $package.name )|ezurl} title="{"Compare the package's content with the site's content tree"|i18n( 'design/admin/package' )}">{'Compare'|i18n( 'design/admin/package' )}</a>
        {/if}
        {if and( $pvf_installable, $pvf_can_install )}
            {if $package.is_installed}
                <button class="exp-btn" type="submit" name="InstallButton" value="1">{'Reinstall'|i18n( 'design/admin/package')}</button>
            {else}
                <button class="exp-btn exp-btn-primary" type="submit" name="InstallButton" value="1">{'Install'|i18n( 'design/admin/package')}</button>
            {/if}
        {/if}
        {if $pvf_can_export}
            <a class="exp-btn" href={concat( 'package/export/', $package.name, $repo_suffix )|ezurl} title="{'Download the package as an .ezpkg file.'|i18n( 'design/admin/package' )}">{'Download'|i18n( 'design/admin/package' )}</a>
        {/if}
        {if and( $pvf_installable, $pvf_can_install, $package.is_installed )}
            <button class="exp-btn exp-btn-outline-danger" type="submit" name="UninstallButton" value="1">{'Uninstall'|i18n( 'design/admin/package')}</button>
        {/if}
    </form>
    {if and( $pvf_can_remove, $card, $card.links|eq( 0 ) )}
    <form method="post" action={cond( $repo_suffix|ne( '' ), concat( '/package/list', $repo_suffix ), '/package/list' )|ezurl}>
        <input type="hidden" name="PackageSelection[]" value="{concat( $package.name, '@', $card.repository_id )|wash}" />
        <button class="exp-btn exp-btn-outline-danger" type="submit" name="RemovePackageButton" value="1" title="{'Asks first and says what goes.'|i18n( 'design/admin/package' )}">{'Remove'|i18n( 'design/admin/package' )}</button>
    </form>
    {/if}
    </div>
</div>
{if $package.install_type|ne( 'install' )}
    <p class="exp-feedback is-info pvf-noinstall">{'There is nothing to install here: this package has the install type "%type". A site package is imported by the setup wizard together with the packages it requires, and is not installed on its own.'|i18n( 'design/admin/package',, hash( '%type', $package.install_type|wash ) )}</p>
{elseif $package.install|count|eq( 0 )}
    <p class="exp-feedback is-info pvf-noinstall">{'There is nothing to install here: this package has no install items.'|i18n( 'design/admin/package' )}</p>
{elseif $pvf_can_install|not}
    <p class="exp-feedback is-info pvf-noinstall">{'You are not allowed to install packages (package/install), so the Install button is not shown.'|i18n( 'design/admin/package' )}</p>
{/if}
{undef $pvf_installable $pvf_can_install $pvf_can_export $pvf_can_remove}

{if and( $card, $card.installer_source )}
<div class="exp-feedback is-info" role="note">
    <p><strong>{'The installer takes this package as a source.'|i18n( 'design/admin/package' )}</strong></p>
    <ul>{foreach $card.installer_reasons as $reason}<li>{$reason_labels[$reason]|wash}</li>{/foreach}</ul>
    <p>{'Change it only on purpose: new installations and published packages are built from it.'|i18n( 'design/admin/package' )}</p>
</div>
{/if}

<section class="exp-section" aria-labelledby="package-details-title">
<div class="exp-section-head"><h2 class="exp-h2" id="package-details-title">{'Details'|i18n( 'design/admin/package' )}</h2></div>
<div class="exp-panel">
<div class="exp-head-row">
<dl class="exp-facts" style="flex: 1 1 520px">
    <div><dt>{'Repository'|i18n( 'design/admin/package' )}</dt>
        <dd>{if $repo}<a href={concat( '/package/list/', $repo.id )|ezurl}><code>{$repo.id|wash}</code></a>{/if}{if $package.vendor} &middot; {$package.vendor|wash}{/if}</dd></div>
    <div><dt>{'State'|i18n('design/admin/package')}</dt><dd>{if $package.state}{$package.state|wash}{else}&ndash;{/if}</dd></div>
    <div><dt>{'License'|i18n('design/admin/package')}</dt>
        {* A configured license (package.ini [LicenseSettings]) shows its name, a link and its identifier; an older or
           unknown stored value is shown as it is. *}
        {def $licenceInfo=$package.licence-info}
        <dd>{if $licenceInfo}{if $licenceInfo.url}<a href="{$licenceInfo.url|wash}" target="_blank" rel="noopener noreferrer">{$licenceInfo.name|wash}</a>{else}{$licenceInfo.name|wash}{/if}{if $licenceInfo.known} <code>{$licenceInfo.identifier|wash}</code>{if $licenceInfo.stored|ne( $licenceInfo.identifier )} <span class="exp-muted">({'stored as %licence'|i18n('design/admin/package',,hash( '%licence', $licenceInfo.stored ))|wash})</span>{/if}{/if}{else}&ndash;{/if}</dd>
        {undef $licenceInfo}</div>
    <div><dt>{'Maintainers'|i18n('design/admin/package')}</dt>
        <dd>{section var=maintainer loop=$package.maintainers}<a href="mailto:{$maintainer.item.email|wash}" title="{'Send email to the maintainer'|i18n('design/admin/package')}">{$maintainer.item.name|wash}</a>{if $maintainer.item.role} ({$maintainer.item.role|wash}){/if}{delimiter}, {/delimiter}{section-else}&ndash;{/section}</dd></div>
    <div><dt>{'Documents'|i18n('design/admin/package')}</dt>
        <dd>{section var=document loop=$package.documents}{let document_path=$package|ezpackage( documentpath, $document.name )}{if $document_path}<a href={$document_path|ezroot}>{/if}{$document.name|wash}{if $document_path}</a>{/if}{/let}{delimiter}, {/delimiter}{section-else}&ndash;{/section}
            {if $package.file-count|gt( 0 )} &middot; <a href={concat( "package/view/files/", $package.name, $repo_suffix )|ezurl}>{'File list'|i18n('design/admin/package')}</a>{/if}</dd></div>
    <div><dt>{'Released'|i18n( 'design/admin/package' )}</dt><dd>{if $package.release-timestamp}{$package.release-timestamp|l10n( shortdatetime )}{else}&ndash;{/if}</dd></div>
    <div><dt>{'Packaged'|i18n( 'design/admin/package' )}</dt><dd>{if $package.packaging-timestamp}{$package.packaging-timestamp|l10n( shortdatetime )}{if $package.packaging-host} &middot; {$package.packaging-host|wash}{/if}{else}&ndash;{/if}</dd></div>
    <div><dt>{'For Exponential'|i18n( 'design/admin/package' )}</dt><dd>{if $package.ezpublish-version}{$package.ezpublish-version|wash}{else}&ndash;{/if}</dd></div>
    {if $card}
    <div><dt>{'Requires'|i18n( 'design/admin/package' )}</dt>
        <dd>{if $card.requires|count|eq( 0 )}{'Nothing'|i18n( 'design/admin/package' )}{else}<ul class="exp-deps">{foreach $card.requires as $require}<li{if $require.present|not} class="is-missing"{/if}>{if $require.present}<a href={concat( '/package/view/full/', $require.name )|ezurl}>{$require.name|wash}</a>{else}{$require.name|wash} ({'missing'|i18n( 'design/admin/package' )}){/if}{if $require.min_version|ne( '' )} <span class="exp-muted">&ge; {$require.min_version|wash}</span>{/if}</li>{/foreach}</ul>{/if}</dd></div>
    <div><dt>{'Required by'|i18n( 'design/admin/package' )}</dt>
        <dd>{if $card.required_by|count|eq( 0 )}{'No package'|i18n( 'design/admin/package' )}{else}<ul class="exp-deps">{foreach $card.required_by as $requirer}<li><a href={concat( '/package/view/full/', $requirer )|ezurl}>{$requirer|wash}</a></li>{/foreach}</ul>{/if}</dd></div>
    <div><dt>{'Size'|i18n( 'design/admin/package' )}</dt><dd>{$card.bytes|si( byte )}, {'%count files'|i18n( 'design/admin/package',, hash( '%count', $card.files ) )}</dd></div>
    <div><dt title="{'The newest file of the package directory'|i18n( 'design/admin/package' )}">{'Last change'|i18n( 'design/admin/package' )}</dt><dd>{if $card.changed}{$card.changed|l10n( shortdatetime )}{else}&ndash;{/if}</dd></div>
    {/if}
</dl>
{let thumbnail_list=$package.thumbnail-list}
{if $thumbnail_list}
<img class="exp-thumb" src={concat( $package|ezpackage( fileitempath, $thumbnail_list[0] ) )|ezroot} alt="{$thumbnail_list[0].name|wash}" />
{/if}
{/let}
</div>
{* Escaped: the description comes from the package's own package.xml, and packages can be uploaded *}
{if $package.description}<p class="exp-description">{$package.description|wash|nl2br}</p>{/if}
</div>
</section>

{if $carries}
<section class="exp-section" aria-labelledby="package-carries-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="package-carries-title">{'What it carries'|i18n( 'design/admin/package' )}</h2>
    <p>{'From the package definition and its files. Nothing here changes the site; Compare shows how it differs from what the site has.'|i18n( 'design/admin/package' )}</p>
</div>
<div class="exp-carries">
    <div class="exp-panel">
        <h3>{'Content classes'|i18n( 'design/admin/package' )} ({$carries.classes|count})</h3>
        {if $carries.classes|count|eq( 0 )}<p class="exp-muted">{'None'|i18n( 'design/admin/package' )}</p>{else}<p>{foreach $carries.classes as $class}<span class="exp-fn">{$class|wash}</span>{/foreach}</p>{/if}
    </div>
    <div class="exp-panel">
        <h3>{'Content'|i18n( 'design/admin/package' )}</h3>
        {if and( $carries.object_items|eq( 0 ), $carries.object_files|eq( 0 ) )}<p class="exp-muted">{'None'|i18n( 'design/admin/package' )}</p>
        {else}<p>{'%items content install item(s), %files object file(s)'|i18n( 'design/admin/package',, hash( '%items', $carries.object_items, '%files', $carries.object_files ) )}</p>{/if}
    </div>
    <div class="exp-panel">
        <h3>{'Extensions'|i18n( 'design/admin/package' )} ({$carries.extensions|count})</h3>
        {if $carries.extensions|count|eq( 0 )}<p class="exp-muted">{'None'|i18n( 'design/admin/package' )}</p>{else}<p>{foreach $carries.extensions as $extension}<span class="exp-fn">{$extension|wash}</span>{/foreach}</p>{/if}
    </div>
    <div class="exp-panel">
        <h3>{'Settings files'|i18n( 'design/admin/package' )} ({$carries.settings|count})</h3>
        {if $carries.settings|count|eq( 0 )}<p class="exp-muted">{'None'|i18n( 'design/admin/package' )}</p>{else}<p>{foreach $carries.settings as $settings}<span class="exp-fn">{$settings|wash}</span>{/foreach}</p>{/if}
        {if $carries.other_items|count|gt( 0 )}
        <h3>{'Other install items'|i18n( 'design/admin/package' )}</h3>
        <p>{foreach $carries.other_items as $type => $count}<span class="exp-fn">{$type|wash} &times; {$count}</span>{/foreach}</p>
        {/if}
    </div>
</div>
{if $carries.kinds|count|gt( 0 )}
<div class="exp-table-wrap">
<table class="exp-table">
    <caption class="exp-sr">{'Files by kind'|i18n( 'design/admin/package' )}</caption>
    <thead><tr><th scope="col">{'Kind'|i18n( 'design/admin/package' )}</th><th scope="col" class="exp-num">{'Files'|i18n( 'design/admin/package' )}</th><th scope="col" class="exp-num">{'Size'|i18n( 'design/admin/package' )}</th></tr></thead>
    <tbody>
    {foreach $carries.kinds as $kind => $info}
    <tr><td>{cond( is_set( $kindLabels[$kind] ), $kindLabels[$kind], $kind )|wash}</td><td class="exp-num">{$info.files}</td><td class="exp-num">{$info.bytes|si( byte )}</td></tr>
    {/foreach}
    </tbody>
</table>
</div>
{/if}
</section>
{/if}

{if $package.changelog}
<section class="exp-section" aria-labelledby="package-changelog-title">
<div class="exp-section-head"><h2 class="exp-h2" id="package-changelog-title">{'Changelog'|i18n('design/admin/package')}</h2></div>
<div class="exp-panel">
<ul class="exp-changelog">
{section var=log loop=$package.changelog}
    <li><span class="exp-meta">{$log.item.timestamp|l10n( shortdatetime )} &middot; <a href="mailto:{$log.item.email|wash}" title="{'Send email to the maintainer'|i18n('design/admin/package')}">{$log.item.person|wash}</a></span>
        <ul>{section var=change loop=$log.item.changes}<li>{$change.item|wash}</li>{/section}</ul></li>
{/section}
</ul>
</div>
</section>
{/if}

    {if $ContentsBrowser}
    {* The package contents browser: every file the package carries, paginated and filtered
       (kernel/package/view.php, eZPackageFileBrowser - no dependency on any extension). Its own
       form for the type/search/per-page filter (independent of the export/install one above);
       pagination and "View" are plain links to the same view with its state in view parameters,
       (type)/(search)/(limit)/(offset)/(file), each path built by kernel/package/view.php
       (url_first/url_prev/... , and url_view per file). The form still submits GET fields; the
       view answers them with one redirect to the view-parameter address. Nothing here writes. *}
    {def $browseBaseURL = concat( 'package/view/full/', $package.name, $repo_suffix )|ezurl
         $viewedIndex = -1}
    {if $ContentsBrowser.viewed_file}{set $viewedIndex = $ContentsBrowser.viewed_file.index}{/if}
    <div class="pvf-browser">
        <div class="pvf-browser-head">
            <h2>{'Package contents'|i18n('design/admin/package')}</h2>
            <span class="pvf-count">{'%shown of %total files'|i18n('design/admin/package',,hash('%shown', $ContentsBrowser.total_filtered, '%total', $ContentsBrowser.total_all))}</span>
        </div>

        <form class="pvf-filter" method="get" action={$browseBaseURL}>
            <div class="pvf-field">
                <label for="browse-type">{'Type'|i18n('design/admin/package')}</label>
                <select id="browse-type" name="BrowseType">
                    <option value="">{'Any type'|i18n('design/admin/package')}</option>
                    {foreach $kindLabels as $kind => $kindLabel}
                    <option value="{$kind|wash}"{if $ContentsBrowser.type_filter|eq( $kind )} selected{/if}>{$kindLabel|wash}</option>
                    {/foreach}
                </select>
            </div>
            <div class="pvf-field pvf-field-search">
                <label for="browse-search">{'Search path/name'|i18n('design/admin/package')}</label>
                <input id="browse-search" type="search" name="BrowseSearch" value="{$ContentsBrowser.search|wash}" />
            </div>
            <div class="pvf-field">
                <label for="browse-limit">{'Per page'|i18n('design/admin/package')}</label>
                <select id="browse-limit" name="BrowseLimit">
                    {foreach array( '25', '50', '100', '250', '1000', 'all' ) as $limitChoice}
                    <option value="{$limitChoice|wash}"{if $limitChoice|eq( $ContentsBrowser.limit )} selected{/if}>{cond( $limitChoice|eq('all'), 'All'|i18n('design/admin/package'), $limitChoice|wash )}</option>
                    {/foreach}
                </select>
            </div>
            <div class="pvf-field pvf-field-submit">
                <input class="defaultbutton" type="submit" name="BrowseApply" value="{'Apply'|i18n('design/admin/package')}" />
                {if or( $ContentsBrowser.type_filter, $ContentsBrowser.search )}
                <a class="pvf-reset" href={$browseBaseURL}>{'Clear filters'|i18n('design/admin/package')}</a>
                {/if}
            </div>
        </form>

        {if $ContentsBrowser.viewed_file}
        {def $viewed = $ContentsBrowser.viewed_file}
        <div class="pvf-viewer">
            <div class="pvf-viewer-head">
                <div class="pvf-viewer-title">
                    <code class="pvf-path">{$viewed.path|wash}</code>
                    <span class="pvf-viewer-meta">
                        <span class="pvf-kind pvf-kind-{$viewed.kind|wash}">{cond( is_set( $kindLabels[$viewed.kind] ), $kindLabels[$viewed.kind], $viewed.kind )|wash}</span>
                        <span class="pvf-size" title="{$viewed.size|wash} B">{$viewed.size|si( byte )}</span>
                    </span>
                </div>
                <div class="pvf-viewer-actions">
                    <a class="pvf-link-button" href={concat( 'package/viewfile/', $package.name, '/', $viewed.index, $repo_suffix )|ezurl} target="_blank" rel="noopener">{'Download'|i18n('design/admin/package')}</a>
                    <a class="pvf-link-button" href={$ContentsBrowser.url_close|ezurl}>{'Close'|i18n('design/admin/package')}</a>
                </div>
            </div>
            <div class="pvf-viewer-body">
            {if $ContentsBrowser.viewed_object}
                <table class="list pvf-object">
                    <tr><th scope="row">{'Name'|i18n('design/admin/package')}</th><td>{$ContentsBrowser.viewed_object.name|wash}</td></tr>
                    <tr><th scope="row">{'Class'|i18n('design/admin/package')}</th><td><code>{$ContentsBrowser.viewed_object.class_identifier|wash}</code></td></tr>
                    <tr><th scope="row">{'Remote ID'|i18n('design/admin/package')}</th><td><code>{$ContentsBrowser.viewed_object.remote_id|wash}</code></td></tr>
                </table>
                {foreach $ContentsBrowser.viewed_object.translations as $language => $attributes}
                <h3 class="pvf-language">{$language|wash}</h3>
                <div class="pvf-scroll">
                <table class="list pvf-attributes">
                    <thead><tr><th>{'Attribute'|i18n('design/admin/package')}</th><th>{'Datatype'|i18n('design/admin/package')}</th><th>{'Value'|i18n('design/admin/package')}</th></tr></thead>
                    <tbody>
                    {foreach $attributes as $identifier => $attribute sequence array( 'bglight', 'bgdark' ) as $rowStyle}
                    <tr class="{$rowStyle}"><td><code>{$identifier|wash}</code></td><td>{$attribute.type|wash}</td><td class="pvf-value">{$attribute.text|wash}</td></tr>
                    {/foreach}
                    </tbody>
                </table>
                </div>
                {/foreach}
                {if $ContentsBrowser.viewed_object.more_objects|gt(0)}
                <p class="pvf-note">{'+ %count more object(s) in the same file'|i18n('design/admin/package',,hash('%count', $ContentsBrowser.viewed_object.more_objects))}</p>
                {/if}
            {elseif $viewed.kind|eq('image')}
                <div class="pvf-image"><img src={concat( 'package/viewfile/', $package.name, '/', $viewed.index, $repo_suffix )|ezurl} alt="{$viewed.path|wash}" /></div>
            {else}
                <pre class="pvf-code">{$ContentsBrowser.viewed_content|wash}</pre>
                {if and( is_set( $viewed.truncated ), $viewed.truncated )}<p class="pvf-note">{'Only the first megabyte is shown here; Download gives the whole file.'|i18n( 'design/admin/package' )}</p>{/if}
            {/if}
            </div>
        </div>
        {undef $viewed}
        {/if}

        {if $ContentsBrowser.pages|gt(1)}{include uri='design:package/view/contents_pager.tpl' browser=$ContentsBrowser position='top'}{/if}

        {if $ContentsBrowser.files|count|gt(0)}
        <div class="pvf-scroll">
        <table class="list pvf-files">
            <thead><tr>
                <th class="pvf-col-path">{'Path'|i18n('design/admin/package')}</th>
                <th class="pvf-col-type">{'Type'|i18n('design/admin/package')}</th>
                <th class="pvf-col-size">{'Size'|i18n('design/admin/package')}</th>
                <th class="pvf-col-actions"><span class="pvf-hidden">{'Actions'|i18n('design/admin/package')}</span></th>
            </tr></thead>
            <tbody>
            {foreach $ContentsBrowser.files as $file sequence array( 'bglight', 'bgdark' ) as $rowStyle}
            <tr class="{$rowStyle}{if $file.index|eq( $viewedIndex )} pvf-current{/if}">
                {def $pathParts = $file.path|explode( '/' )}
                <td class="pvf-col-path"><code>{if $pathParts|count|gt(1)}<span class="pvf-dir">{$pathParts|extract_left( $pathParts|count|dec )|implode( '/' )|wash}/</span>{/if}<span class="pvf-name">{$pathParts|extract_right( 1 )|implode( '' )|wash}</span></code></td>
                {undef $pathParts}
                <td class="pvf-col-type"><span class="pvf-kind pvf-kind-{$file.kind|wash}" title="{cond( is_set( $kindLabels[$file.kind] ), $kindLabels[$file.kind], $file.kind )|wash}">{cond( is_set( $kindShortLabels[$file.kind] ), $kindShortLabels[$file.kind], $file.kind )|wash}</span></td>
                <td class="pvf-col-size" title="{$file.size|wash} B">{$file.size|si( byte )}</td>
                <td class="pvf-col-actions">
                    {if $file.kind|ne('other')}
                    {if $file.index|eq( $viewedIndex )}
                    <a class="pvf-link-button pvf-active" href={$ContentsBrowser.url_close|ezurl} aria-current="true">{'Close'|i18n('design/admin/package')}</a>
                    {else}
                    <a class="pvf-link-button" href={$file.url_view|ezurl}>{'View'|i18n('design/admin/package')}</a>
                    {/if}
                    {/if}
                    <a class="pvf-link-button" href={concat( 'package/viewfile/', $package.name, '/', $file.index, $repo_suffix )|ezurl} target="_blank" rel="noopener">{'Download'|i18n('design/admin/package')}</a>
                </td>
            </tr>
            {/foreach}
            </tbody>
        </table>
        </div>

        {include uri='design:package/view/contents_pager.tpl' browser=$ContentsBrowser position='bottom'}
        {else}
        <div class="pvf-empty-state">
            <p>{if $ContentsBrowser.total_all|eq(0)}{'This package has no files.'|i18n('design/admin/package')}{else}{'No file matches these filters.'|i18n('design/admin/package')}{/if}</p>
            {if or( $ContentsBrowser.type_filter, $ContentsBrowser.search )}<p><a class="pvf-link-button" href={$browseBaseURL}>{'Clear filters'|i18n('design/admin/package')}</a></p>{/if}
        </div>
        {/if}
    </div>
    {undef $browseBaseURL $viewedIndex}
    {/if}

</div></div></div>

    </div>
</div>

</div>
{undef $kindLabels $kindShortLabels $card $carries $repo $repo_suffix $reason_labels}

{/let}
