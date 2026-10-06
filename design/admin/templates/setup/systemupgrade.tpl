{* The upgrade check (setup/systemupgrade).

   What is installed and what the checks compare it with (the version, the file manifest of Exponential and the
   manifests of the active extensions, the database engine), the two checks with their downloads, then the result of
   the check that ran: the file consistency check grouped by what was found, with a search and filters, or the
   database consistency check table by table with the SQL it would take. Every kind of finding says how to deal
   with it.

   Nothing on this page writes a file or changes the database. The buttons run a check that only reads, or send
   its report as a file; the SQL is text to read.

   The same file is in design/admin and design/admin4. The view gives file_report, file_groups, schema_report and
   upgrade_info; without them (an older view class) the page shows md5_result and upgrade_sql as before. Everything
   works without javascript; the script adds the search and the filters and says that a check is running.
   Guide: doc/guides/upgrade-check.md *}
{include uri='design:setup/systemupgrade_exp_style.tpl'}

{def $info = first_set( $upgrade_info, false() )
     $files = first_set( $file_report, false() )
     $schema = first_set( $schema_report, false() )
     $current = first_set( $upgrade_check, '' )
     $guide = 'https://github.com/se7enxweb/exponential/blob/main/doc/guides/upgrade-check.md'
     $state_labels = hash( 'modified', 'Modified'|i18n( 'design/admin/setup/systemupgrade' ),
                           'missing', 'Missing'|i18n( 'design/admin/setup/systemupgrade' ),
                           'unreadable', 'Unreadable'|i18n( 'design/admin/setup/systemupgrade' ),
                           'unlisted', 'Not listed'|i18n( 'design/admin/setup/systemupgrade' ),
                           'malformed', 'Malformed lines'|i18n( 'design/admin/setup/systemupgrade' ),
                           'unordered', 'Out of order'|i18n( 'design/admin/setup/systemupgrade' ) )
     $kind_labels = hash( 'missing_table', 'Missing table'|i18n( 'design/admin/setup/systemupgrade' ),
                          'extra_table', 'Table not in the schema'|i18n( 'design/admin/setup/systemupgrade' ),
                          'changed_table', 'Changed table'|i18n( 'design/admin/setup/systemupgrade' ),
                          'missing_field', 'Missing field'|i18n( 'design/admin/setup/systemupgrade' ),
                          'extra_field', 'Field not in the schema'|i18n( 'design/admin/setup/systemupgrade' ),
                          'changed_field', 'Changed field'|i18n( 'design/admin/setup/systemupgrade' ),
                          'missing_index', 'Missing index'|i18n( 'design/admin/setup/systemupgrade' ),
                          'extra_index', 'Index not in the schema'|i18n( 'design/admin/setup/systemupgrade' ),
                          'changed_index', 'Changed index'|i18n( 'design/admin/setup/systemupgrade' ) )}

<div class="context-block exp-upgrade">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'System upgrade check'|i18n( 'design/admin/setup' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="exp-intro">
    <p>{'Before upgrading Exponential to a newer version, it is important to check that the current installation is ready for upgrading.'|i18n( 'design/admin/setup' )}</p>
    <p>{'Remember to make a backup of the Exponential directory and the database before you upgrade.'|i18n( 'design/admin/setup' )}</p>
    <p>{'Both checks only read: they change no file and no table.'|i18n( 'design/admin/setup/systemupgrade' )}</p>
</div>

{* ---- The result of the file consistency check --------------------------------------------------------------- *}
{if $files}
    {if eq( $files.status, 'failed' )}
<div class="exp-feedback is-bad" role="alert">
    <div class="exp-feedback-head"><h2 class="exp-h2">{$failure_reason|wash}</h2><span class="exp-time">{'%seconds s'|i18n( 'design/admin/setup/systemupgrade',, hash( '%seconds', $files.seconds|l10n( 'number' ) ) )}</span></div>
    <p>{'Without the file list of the release the check cannot compare anything. The guide says where to get it.'|i18n( 'design/admin/setup/systemupgrade' )} <a href="{concat( $guide, '#when-the-check-cannot-run' )}">{'Guide: upgrade check'|i18n( 'design/admin/setup/systemupgrade' )}</a></p>
</div>
    {elseif eq( $files.status, 'ok' )}
<div class="exp-feedback is-ok" role="status">
    <div class="exp-feedback-head"><h2 class="exp-h2">{'File consistency check OK.'|i18n( 'design/admin/setup' )}</h2><span class="exp-time">{'%seconds s'|i18n( 'design/admin/setup/systemupgrade',, hash( '%seconds', $files.seconds|l10n( 'number' ) ) )}</span></div>
    <p>{'All %count listed files match their checksums.'|i18n( 'design/admin/setup/systemupgrade',, hash( '%count', $files.checked ) )}{if $files.notes|gt( 0 )} {'%count notes below do not stop an upgrade.'|i18n( 'design/admin/setup/systemupgrade',, hash( '%count', $files.notes ) )}{/if}</p>
</div>
    {else}
<div class="exp-feedback is-warn" role="alert">
    <div class="exp-feedback-head"><h2 class="exp-h2">{'Warning: it is not safe to upgrade without checking the modifications done to the following files'|i18n( 'design/admin/setup' )}</h2><span class="exp-time">{'%seconds s'|i18n( 'design/admin/setup/systemupgrade',, hash( '%seconds', $files.seconds|l10n( 'number' ) ) )}</span></div>
    <p>{'%problems of %count listed files differ from the release: %modified modified, %missing missing, %unreadable unreadable.'|i18n( 'design/admin/setup/systemupgrade',, hash( '%problems', $files.problems, '%count', $files.checked, '%modified', $files.counts.modified, '%missing', $files.counts.missing, '%unreadable', $files.counts.unreadable ) )}</p>
</div>
    {/if}
{elseif $md5_result}
    {* An older view: the flat list *}
    {if $md5_result|eq( 'ok' )}
<div class="exp-feedback is-ok" role="status"><h2 class="exp-h2">{'File consistency check OK.'|i18n( 'design/admin/setup' )}</h2></div>
    {elseif $md5_result|eq( 'failed' )}
<div class="exp-feedback is-bad" role="alert"><h2 class="exp-h2">{$failure_reason|wash}</h2></div>
    {else}
<div class="exp-feedback is-warn" role="alert">
    <h2 class="exp-h2">{'Warning: it is not safe to upgrade without checking the modifications done to the following files'|i18n( 'design/admin/setup' )}:</h2>
    <ul>{foreach $md5_result as $md5_path}<li><code>{$md5_path|wash}</code></li>{/foreach}</ul>
</div>
    {/if}
{/if}

{* ---- The result of the database consistency check ----------------------------------------------------------- *}
{if $schema}
    {if eq( $schema.status, 'failed' )}
<div class="exp-feedback is-bad" role="alert">
    <div class="exp-feedback-head"><h2 class="exp-h2">{'The database schema could not be read.'|i18n( 'design/admin/setup/systemupgrade' )}</h2><span class="exp-time">{'%seconds s'|i18n( 'design/admin/setup/systemupgrade',, hash( '%seconds', $schema.seconds|l10n( 'number' ) ) )}</span></div>
    <p>{'There is no schema handler for the database engine %engine, or the database did not answer. dbschema.ini [SchemaSettings] names the handlers.'|i18n( 'design/admin/setup/systemupgrade',, hash( '%engine', $schema.engine|wash ) )}</p>
</div>
    {elseif eq( $schema.status, 'ok' )}
<div class="exp-feedback is-ok" role="status">
    <div class="exp-feedback-head"><h2 class="exp-h2">{'Database check OK.'|i18n( 'design/admin/setup' )}</h2><span class="exp-time">{'%seconds s'|i18n( 'design/admin/setup/systemupgrade',, hash( '%seconds', $schema.seconds|l10n( 'number' ) ) )}</span></div>
    <p>{if $schema.relational}{'The database matches the schema of Exponential and the active extensions (%files schema files).'|i18n( 'design/admin/setup/systemupgrade',, hash( '%files', $schema.schema_files|count ) )}{else}{'Every collection Exponential needs is there.'|i18n( 'design/admin/setup/systemupgrade' )}{/if}
       {if $schema.counts.noise|gt( 0 )}{'%count tables differ only in how the %engine engine names their types; nothing needs to change.'|i18n( 'design/admin/setup/systemupgrade',, hash( '%count', $schema.counts.noise, '%engine', $schema.engine|wash ) )}{/if}</p>
</div>
    {else}
<div class="exp-feedback is-warn" role="alert">
    <div class="exp-feedback-head"><h2 class="exp-h2">{'The database is not consistent with the distribution database.'|i18n( 'design/admin/setup' )}</h2><span class="exp-time">{'%seconds s'|i18n( 'design/admin/setup/systemupgrade',, hash( '%seconds', $schema.seconds|l10n( 'number' ) ) )}</span></div>
    {if $schema.relational}
    <p>{'%count tables differ: %missing missing, %extra not in the schema, %changed changed. The SQL below would bring the database in line; read it before you run any of it, after a backup.'|i18n( 'design/admin/setup/systemupgrade',, hash( '%count', $schema.counts.tables, '%missing', $schema.counts.missing_table, '%extra', $schema.counts.extra_table, '%changed', sub( $schema.counts.changed_table, $schema.counts.noise ) ) )}</p>
    {else}
    <p>{'%count collections are missing.'|i18n( 'design/admin/setup/systemupgrade',, hash( '%count', $schema.counts.missing_table ) )}</p>
    {/if}
</div>
    {/if}
{elseif $upgrade_sql}
    {* An older view: the SQL as one block *}
    {if $upgrade_sql|eq( 'ok' )}
<div class="exp-feedback is-ok" role="status"><h2 class="exp-h2">{'Database check OK.'|i18n( 'design/admin/setup' )}</h2></div>
    {else}
<div class="exp-feedback is-warn" role="alert">
    <h2 class="exp-h2">{'The database is not consistent with the distribution database.'|i18n( 'design/admin/setup' )}</h2>
    {if $upgrade_sql|ne( 'mongo' )}<p>{'To synchronize your database with the distribution setup, run the following SQL commands'|i18n( 'design/admin/setup' )}:</p><pre class="exp-sql">{$upgrade_sql|wash}</pre>{/if}
</div>
    {/if}
{/if}

<form method="post" action={'/setup/systemupgrade/'|ezurl} id="upgrade-form">

{* ---- What is installed ---------------------------------------------------------------------------------------- *}
{if $info}
<section aria-labelledby="upgrade-overview-title">
<h2 class="exp-sr" id="upgrade-overview-title">{'Overview'|i18n( 'design/admin/setup/systemupgrade' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure is-text"><strong>{$info.version|wash}</strong><span>{'Exponential version'|i18n( 'design/admin/setup/systemupgrade' )}</span></li>
    <li class="exp-figure{if $info.manifest.exists|not} is-attention{/if}">
        <strong>{if $info.manifest.exists}{$info.manifest.entries}{else}&mdash;{/if}</strong>
        <span>{if $info.manifest.exists}{'Files in share/filelist.md5'|i18n( 'design/admin/setup/systemupgrade' )}{else}{'share/filelist.md5 is missing'|i18n( 'design/admin/setup/systemupgrade' )}{/if}</span>
    </li>
    <li class="exp-figure is-text">
        <strong>{if $info.manifest.committed|gt( 0 )}{$info.manifest.committed|l10n( shortdate )}{elseif $info.manifest.mtime|gt( 0 )}{$info.manifest.mtime|l10n( shortdate )}{else}&mdash;{/if}</strong>
        <span>{if $info.manifest.committed|gt( 0 )}{'Manifest last committed'|i18n( 'design/admin/setup/systemupgrade' )}{else}{'Manifest written'|i18n( 'design/admin/setup/systemupgrade' )}{/if}</span>
    </li>
    <li class="exp-figure"><strong>{$info.with_manifest}</strong><span>{'of %count active extensions carry a manifest of their own'|i18n( 'design/admin/setup/systemupgrade',, hash( '%count', $info.extension_count ) )}</span></li>
    <li class="exp-figure is-text"><strong>{$info.engine|wash}</strong><span>{'Database engine'|i18n( 'design/admin/setup/systemupgrade' )}</span></li>
</ul>
</section>
{/if}

{* ---- The two checks ------------------------------------------------------------------------------------------- *}
<section aria-labelledby="upgrade-checks-title">
<h2 class="exp-sr" id="upgrade-checks-title">{'Checks'|i18n( 'design/admin/setup/systemupgrade' )}</h2>
<div class="exp-checks">
    <div class="exp-check{if eq( $current, 'files' )} is-current{/if}">
        <div class="exp-check-head">
            <h3>{'File consistency check'|i18n( 'design/admin/setup' )}</h3>
            {if $files}
            <ul class="exp-badges">
                {if eq( $files.status, 'ok' )}<li class="exp-badge is-ok">{'OK'|i18n( 'design/admin/setup/systemupgrade' )}</li>
                {elseif eq( $files.status, 'failed' )}<li class="exp-badge is-bad">{'Could not run'|i18n( 'design/admin/setup/systemupgrade' )}</li>
                {else}<li class="exp-badge is-warn">{'%count to look at'|i18n( 'design/admin/setup/systemupgrade',, hash( '%count', $files.problems ) )}</li>{/if}
            </ul>
            {/if}
        </div>
        <p>{'The file consistency tool checks if you have altered any of the files that came with the current installation. Altered files may be replaced by new versions that contain bugfixes, new features, etc. Make sure that you backup and then merge your changes into the new versions of the files.'|i18n( 'design/admin/setup' )}</p>
        <p class="exp-meta">{'Compares every file listed in share/filelist.md5, and in the share/filelist.md5 of each active extension that has one, with its checksum. The same check from a shell: %command'|i18n( 'design/admin/setup/systemupgrade',, hash( '%command', '<code>php bin/php/checkmanifest.php --all --extensions</code>' ) )}</p>
        <div class="exp-actions">
            <button type="submit" class="exp-btn exp-btn-primary exp-run" name="MD5CheckButton" value="{'Check file consistency'|i18n( 'design/admin/setup' )}">{'Check file consistency'|i18n( 'design/admin/setup' )}</button>
            <button type="submit" class="exp-btn exp-btn-small" name="DownloadFileReportButton" value="csv">{'Download CSV'|i18n( 'design/admin/setup/systemupgrade' )}</button>
            <button type="submit" class="exp-btn exp-btn-small" name="DownloadFileReportButton" value="txt">{'Download text'|i18n( 'design/admin/setup/systemupgrade' )}</button>
        </div>
    </div>
    <div class="exp-check{if eq( $current, 'database' )} is-current{/if}">
        <div class="exp-check-head">
            <h3>{'Database consistency check'|i18n( 'design/admin/setup' )}</h3>
            {if $schema}
            <ul class="exp-badges">
                {if eq( $schema.status, 'ok' )}<li class="exp-badge is-ok">{'OK'|i18n( 'design/admin/setup/systemupgrade' )}</li>
                {elseif eq( $schema.status, 'failed' )}<li class="exp-badge is-bad">{'Could not run'|i18n( 'design/admin/setup/systemupgrade' )}</li>
                {else}<li class="exp-badge is-warn">{'%count to look at'|i18n( 'design/admin/setup/systemupgrade',, hash( '%count', $schema.counts.tables ) )}</li>{/if}
            </ul>
            {/if}
        </div>
        <p>{'Compares the tables, fields and indexes of the database with the schema that ships with Exponential and the active extensions, and shows the SQL that would bring the database in line. Nothing is run: read the SQL, make a backup, then run what you agree with yourself.'|i18n( 'design/admin/setup/systemupgrade' )}</p>
        <p class="exp-meta">{'The upgrade checking tools require a lot of system resources. They may take some time to run.'|i18n( 'design/admin/setup' )}</p>
        <div class="exp-actions">
            <button type="submit" class="exp-btn exp-btn-primary exp-run" name="DBCheckButton" value="{'Check database consistency'|i18n( 'design/admin/setup' )}">{'Check database consistency'|i18n( 'design/admin/setup' )}</button>
            <button type="submit" class="exp-btn exp-btn-small" name="DownloadSchemaReportButton" value="sql">{'Download SQL'|i18n( 'design/admin/setup/systemupgrade' )}</button>
        </div>
    </div>
</div>
<p class="exp-meta" id="upgrade-busy" role="status" aria-live="polite" hidden>{'Checking. This takes a few seconds; the page reloads with the result.'|i18n( 'design/admin/setup/systemupgrade' )}</p>
</section>

{* ---- File consistency: what was found ------------------------------------------------------------------------- *}
{if and( $files, ne( $files.status, 'failed' ) )}
<section class="exp-section" aria-labelledby="upgrade-files-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="upgrade-files-title">{'Files'|i18n( 'design/admin/setup/systemupgrade' )}</h2>
    <span class="exp-meta">{'%count files checked in %seconds s'|i18n( 'design/admin/setup/systemupgrade',, hash( '%count', $files.checked, '%seconds', $files.seconds|l10n( 'number' ) ) )}</span>
</div>

<ul class="exp-figures">
    <li class="exp-figure is-good"><strong>{$files.counts.matching}</strong><span>{'Matching'|i18n( 'design/admin/setup/systemupgrade' )}</span></li>
    <li class="exp-figure{if $files.counts.modified|gt( 0 )} is-attention{/if}"><strong>{$files.counts.modified}</strong><span>{$state_labels.modified|wash}</span></li>
    <li class="exp-figure{if $files.counts.missing|gt( 0 )} is-attention{/if}"><strong>{$files.counts.missing}</strong><span>{$state_labels.missing|wash}</span></li>
    {if $files.counts.unreadable|gt( 0 )}<li class="exp-figure is-attention"><strong>{$files.counts.unreadable}</strong><span>{$state_labels.unreadable|wash}</span></li>{/if}
    <li class="exp-figure{if $files.counts.unlisted|gt( 0 )} is-note{/if}"><strong>{$files.counts.unlisted}</strong><span>{'Not listed (notes)'|i18n( 'design/admin/setup/systemupgrade' )}</span></li>
    {if sum( $files.counts.malformed, $files.counts.unordered )|gt( 0 )}<li class="exp-figure is-note"><strong>{sum( $files.counts.malformed, $files.counts.unordered )}</strong><span>{'Manifest lines to tidy (notes)'|i18n( 'design/admin/setup/systemupgrade' )}</span></li>{/if}
</ul>

{* The manifests that were read *}
<div class="exp-panel">
    <div class="exp-panel-head"><h3>{'Manifests read'|i18n( 'design/admin/setup/systemupgrade' )}</h3></div>
    <div class="exp-table-wrap" style="margin-top: 10px">
    <table class="exp-table">
        <thead><tr>
            <th scope="col">{'Manifest'|i18n( 'design/admin/setup/systemupgrade' )}</th>
            <th scope="col" class="exp-num">{'Files'|i18n( 'design/admin/setup/systemupgrade' )}</th>
            <th scope="col" class="exp-num">{'To look at'|i18n( 'design/admin/setup/systemupgrade' )}</th>
            <th scope="col">{'Written'|i18n( 'design/admin/setup/systemupgrade' )}</th>
            <th scope="col">{'Not listed files'|i18n( 'design/admin/setup/systemupgrade' )}</th>
        </tr></thead>
        <tbody>
        {foreach $files.manifests as $manifest}
        <tr>
            <td><strong>{$manifest.label|wash}</strong>{if is_set( $manifest.header.version )} <span class="exp-badge">{'version %version'|i18n( 'design/admin/setup/systemupgrade',, hash( '%version', $manifest.header.version|wash ) )}</span>{/if}<br /><code class="exp-muted">{$manifest.file|wash}</code>
                {if and( is_set( $manifest.header.files_count ), ne( $manifest.header.files_count, $manifest.entries ) )}<br /><span class="exp-badge is-warn">{'Its header says %header files, it lists %count'|i18n( 'design/admin/setup/systemupgrade',, hash( '%header', $manifest.header.files_count|wash, '%count', $manifest.entries ) )}</span>{/if}
                {if $manifest.readable|not}<br /><span class="exp-badge is-bad">{'Cannot be read'|i18n( 'design/admin/setup/systemupgrade' )}</span>{/if}</td>
            <td class="exp-num">{$manifest.entries}</td>
            <td class="exp-num">{if $manifest.problems|gt( 0 )}<span class="exp-badge is-warn">{$manifest.problems}</span>{else}<span class="exp-badge is-ok">0</span>{/if}</td>
            <td>{if $manifest.mtime|gt( 0 )}{$manifest.mtime|l10n( shortdatetime )}{else}&mdash;{/if}</td>
            <td>{if $manifest.unlisted_checked}{'Checked against git'|i18n( 'design/admin/setup/systemupgrade' )}{else}<span class="exp-muted">{'Not checked: no git checkout'|i18n( 'design/admin/setup/systemupgrade' )}</span>{/if}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
</div>

{if $file_groups|count|gt( 0 )}
{* The search and the filters; shown by the script *}
<div class="exp-toolbar exp-js-only" hidden>
    <div class="exp-field">
        <label for="upgrade-search">{'Find a file'|i18n( 'design/admin/setup/systemupgrade' )}</label>
        <input type="search" id="upgrade-search" autocomplete="off" spellcheck="false" aria-controls="upgrade-findings" aria-describedby="upgrade-filter-count" />
    </div>
    {if $files.areas|count|gt( 1 )}
    <div class="exp-field">
        <label for="upgrade-area">{'Where'|i18n( 'design/admin/setup/systemupgrade' )}</label>
        <select id="upgrade-area" aria-controls="upgrade-findings">
            <option value="">{'Everywhere'|i18n( 'design/admin/setup/systemupgrade' )}</option>
            {foreach $files.areas as $area}
            <option value="{$area.name|wash}">{if eq( $area.name, 'kernel' )}{'Exponential (outside extension/)'|i18n( 'design/admin/setup/systemupgrade' )}{else}{$area.name|wash}{/if} ({$area.count})</option>
            {/foreach}
        </select>
    </div>
    {/if}
    <fieldset class="exp-field exp-field-wide">
        <legend>{'Show'|i18n( 'design/admin/setup/systemupgrade' )}</legend>
        <div class="exp-chips">
            <label class="exp-chip"><input type="radio" name="UpgradeFilterState" value="" checked="checked" /><span>{'All'|i18n( 'design/admin/setup/systemupgrade' )}</span></label>
            {foreach $file_groups as $group}
            <label class="exp-chip"><input type="radio" name="UpgradeFilterState" value="{$group.state|wash}" /><span>{$state_labels[$group.state]|wash} ({$group.count})</span></label>
            {/foreach}
        </div>
    </fieldset>
    <p class="exp-filter-count" id="upgrade-filter-count" aria-live="polite"></p>
</div>

<div id="upgrade-findings">
{foreach $file_groups as $group}
<details class="exp-fold exp-state-group {if $group.problem}is-problem{else}is-note{/if}" data-group="{$group.state|wash}"{if $group.problem} open="open"{/if}>
    <summary>
        <h3>{$state_labels[$group.state]|wash}</h3>
        <span class="exp-badge {if $group.problem}is-warn{else}is-info{/if}">{$group.count}</span>
        {if $group.problem|not}<span class="exp-meta">{'A note: it does not stop an upgrade'|i18n( 'design/admin/setup/systemupgrade' )}</span>{/if}
    </summary>
    <div class="exp-fold-body">
        <div class="exp-fix">
            <strong>{'What it means and what to do'|i18n( 'design/admin/setup/systemupgrade' )}</strong>
            {switch match=$group.state}
            {case match='modified'}
            <p>{'The file differs from the one the release shipped. If you changed it on purpose, move the change into an override, a design or an extension of your own, because an upgrade replaces this file, and merge it into the new version. If nobody changed it on purpose, restore it from the release.'|i18n( 'design/admin/setup/systemupgrade' )}</p>
            <p>{'For a maintainer who changed it in the source: refresh its line with %command and commit the manifest with the change.'|i18n( 'design/admin/setup/systemupgrade',, hash( '%command', '<code>php bin/php/checkmanifest.php --fix</code>' ) )}</p>
            {/case}
            {case match='missing'}
            <p>{'The file is listed but not there. Copy it back from the release this installation runs. A maintainer who removed it on purpose drops its line with %command.'|i18n( 'design/admin/setup/systemupgrade',, hash( '%command', '<code>php bin/php/checkmanifest.php --fix</code>' ) )}</p>
            {/case}
            {case match='unreadable'}
            <p>{'The file is there but could not be read: a directory where a file belongs, or permissions that keep the web server out. Check its owner and mode.'|i18n( 'design/admin/setup/systemupgrade' )}</p>
            {/case}
            {case match='unlisted'}
            <p>{'Git tracks the file but the manifest does not list it, so this check cannot tell whether it changed. Nothing to do on an installed site; a maintainer adds it with %command in the release that adds the file.'|i18n( 'design/admin/setup/systemupgrade',, hash( '%command', '<code>php bin/php/checkmanifest.php --fix</code>' ) )}</p>
            {/case}
            {case match='malformed'}
            <p>{'A line of the manifest is not a checksum, two spaces and a path inside the installation, or it lists a file a second time. The line is skipped. Take the manifest from the release, or write it again with %command.'|i18n( 'design/admin/setup/systemupgrade',, hash( '%command', '<code>bash bin/shell/generatefilelist.sh</code>' ) )}</p>
            {/case}
            {case match='unordered'}
            <p>{'The manifest is kept in sorted order so that its changes are easy to review. A line out of order is only a note; %command writes it sorted.'|i18n( 'design/admin/setup/systemupgrade',, hash( '%command', '<code>bash bin/shell/generatefilelist.sh</code>' ) )}</p>
            {/case}
            {case}{/case}
            {/switch}
        </div>
        {if lt( $group.shown, $group.count )}
        <p class="exp-meta">{'The first %shown of %count are listed here; the download has them all.'|i18n( 'design/admin/setup/systemupgrade',, hash( '%shown', $group.shown, '%count', $group.count ) )}</p>
        {/if}
        {if $group.shown|gt( 0 )}
        <div class="exp-table-wrap">
        <table class="exp-table">
            <thead><tr>
                <th scope="col">{if eq( $group.state, 'malformed' )}{'Manifest'|i18n( 'design/admin/setup/systemupgrade' )}{else}{'File'|i18n( 'design/admin/setup/systemupgrade' )}{/if}</th>
                <th scope="col">{'Manifest'|i18n( 'design/admin/setup/systemupgrade' )}</th>
                <th scope="col">{'Details'|i18n( 'design/admin/setup/systemupgrade' )}</th>
            </tr></thead>
            <tbody>
            {foreach $group.items as $item}
            <tr data-file="1" data-state="{$item.state|wash}" data-area="{$item.area|wash}" data-search="{concat( $item.path, ' ', $item.text )|downcase|wash}">
                <td class="exp-path">{$item.path|wash}</td>
                <td>{$item.manifest|wash}{if $item.line|gt( 0 )}<br /><span class="exp-meta">{'line %line'|i18n( 'design/admin/setup/systemupgrade',, hash( '%line', $item.line ) )}</span>{/if}</td>
                <td class="exp-sums">
                    {switch match=$item.state}
                    {case match='modified'}<span class="exp-sum"><span class="exp-sum-label">{'listed'|i18n( 'design/admin/setup/systemupgrade' )}</span> <span title="{$item.expected|wash}">{$item.expected|extract_left( 10 )|wash}&hellip;</span></span> <span class="exp-sum"><span class="exp-sum-label">{'now'|i18n( 'design/admin/setup/systemupgrade' )}</span> <span title="{$item.actual|wash}">{$item.actual|extract_left( 10 )|wash}&hellip;</span></span>{/case}
                    {case match='unordered'}{'after %path'|i18n( 'design/admin/setup/systemupgrade',, hash( '%path', $item.detail|wash ) )}{/case}
                    {case match='malformed'}{if eq( $item.detail, 'duplicate' )}{'lists a file a second time'|i18n( 'design/admin/setup/systemupgrade' )}{elseif eq( $item.detail, 'unsafe_path' )}{'a path outside the installation'|i18n( 'design/admin/setup/systemupgrade' )}{else}{'not a checksum and a path'|i18n( 'design/admin/setup/systemupgrade' )}{/if}<br /><code>{$item.text|shorten( 120 )|wash}</code>{/case}
                    {case match='missing'}<span class="exp-sum"><span class="exp-sum-label">{'listed'|i18n( 'design/admin/setup/systemupgrade' )}</span> <span title="{$item.expected|wash}">{$item.expected|extract_left( 10 )|wash}&hellip;</span></span>{/case}
                    {case}&mdash;{/case}
                    {/switch}
                </td>
            </tr>
            {/foreach}
            </tbody>
        </table>
        </div>
        {/if}
    </div>
</details>
{/foreach}
<p class="exp-empty exp-no-match" id="upgrade-no-match" hidden>{'No finding matches the search and the filters.'|i18n( 'design/admin/setup/systemupgrade' )}</p>
</div>
{/if}
</section>
{/if}

{* ---- Database consistency: what was found --------------------------------------------------------------------- *}
{if and( $schema, ne( $schema.status, 'failed' ) )}
<section class="exp-section" aria-labelledby="upgrade-db-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="upgrade-db-title">{'Database'|i18n( 'design/admin/setup/systemupgrade' )}</h2>
    <span class="exp-meta">{'%engine, checked in %seconds s'|i18n( 'design/admin/setup/systemupgrade',, hash( '%engine', $schema.engine|wash, '%seconds', $schema.seconds|l10n( 'number' ) ) )}</span>
</div>

{if $schema.relational}
<ul class="exp-figures">
    <li class="exp-figure{if $schema.counts.tables|gt( 0 )} is-attention{else} is-good{/if}"><strong>{$schema.counts.tables}</strong><span>{'Tables with SQL to review'|i18n( 'design/admin/setup/systemupgrade' )}</span></li>
    <li class="exp-figure{if $schema.counts.missing_table|gt( 0 )} is-attention{/if}"><strong>{$schema.counts.missing_table}</strong><span>{'Missing tables'|i18n( 'design/admin/setup/systemupgrade' )}</span></li>
    <li class="exp-figure{if $schema.counts.extra_table|gt( 0 )} is-note{/if}"><strong>{$schema.counts.extra_table}</strong><span>{'Tables not in the schema'|i18n( 'design/admin/setup/systemupgrade' )}</span></li>
    <li class="exp-figure"><strong>{sub( $schema.counts.changed_table, $schema.counts.noise )}</strong><span>{'Changed tables'|i18n( 'design/admin/setup/systemupgrade' )}</span></li>
    <li class="exp-figure"><strong>{$schema.counts.noise}</strong><span>{'Engine notes'|i18n( 'design/admin/setup/systemupgrade' )}</span></li>
</ul>

{if $schema.schema_files|count|gt( 0 )}
<details class="exp-fold">
    <summary><h3>{'Schema files compared'|i18n( 'design/admin/setup/systemupgrade' )}</h3><span class="exp-badge">{$schema.schema_files|count}</span></summary>
    <div class="exp-fold-body">
        <ul class="exp-ext-list">{foreach $schema.schema_files as $schema_file}<li title="{$schema_file.file|wash}">{$schema_file.name|wash} ({$schema_file.tables})</li>{/foreach}</ul>
    </div>
</details>
{/if}

{foreach $schema.tables as $table}
{if $table.noise}{continue}{/if}
<details class="exp-fold {if $table.noise}{elseif or( $table.destructive, eq( $table.kind, 'extra_table' ) )}is-note{else}is-problem{/if}"{if $table.noise|not} open="open"{/if}>
    <summary>
        <h3><code>{$table.name|wash}</code></h3>
        <ul class="exp-badges">
            {if $table.noise}<li class="exp-badge is-info">{'Engine note'|i18n( 'design/admin/setup/systemupgrade' )}</li>
            {else}<li class="exp-badge {if eq( $table.kind, 'missing_table' )}is-bad{elseif eq( $table.kind, 'extra_table' )}is-warn{else}is-warn{/if}">{$kind_labels[$table.kind]|wash}</li>{/if}
            {if $table.source|ne( '' )}<li class="exp-badge">{$table.source|wash}</li>{/if}
            {if $table.destructive}<li class="exp-badge is-bad">{'Removes something'|i18n( 'design/admin/setup/systemupgrade' )}</li>{/if}
        </ul>
    </summary>
    <div class="exp-fold-body">
        <div class="exp-fix">
            <strong>{'What it means and what to do'|i18n( 'design/admin/setup/systemupgrade' )}</strong>
            {if $table.noise}
            <p>{'The %engine schema handler writes no SQL for this: the engine names these types in its own words (on SQLite, text for longtext and an integer key it does not read as auto_increment). Nothing needs to change.'|i18n( 'design/admin/setup/systemupgrade',, hash( '%engine', $schema.engine|wash ) )}</p>
            {elseif eq( $table.kind, 'missing_table' )}
            <p>{'The table ships with Exponential or an active extension, and the database does not have it. Run the CREATE statement after a backup; the feature that uses the table fails until it is there.'|i18n( 'design/admin/setup/systemupgrade' )}</p>
            {elseif eq( $table.kind, 'extra_table' )}
            <p>{'No shipped schema names this table: usually it belongs to an extension that is not active, or to an extension of your own without a schema file. Leave it, unless you know its data is no longer needed; the DROP statement is shown only to be complete.'|i18n( 'design/admin/setup/systemupgrade' )}</p>
            {else}
            <p>{'Fields or indexes differ from the shipped definition. Read each statement and run the ones you agree with, after a backup. A statement that removes a field or an index may remove data of your own.'|i18n( 'design/admin/setup/systemupgrade' )}</p>
            {/if}
        </div>
        {if $table.items|count|gt( 0 )}
        <div class="exp-table-wrap">
        <table class="exp-table">
            <thead><tr><th scope="col">{'What'|i18n( 'design/admin/setup/systemupgrade' )}</th><th scope="col">{'Name'|i18n( 'design/admin/setup/systemupgrade' )}</th><th scope="col">{'Shipped'|i18n( 'design/admin/setup/systemupgrade' )}</th><th scope="col">{'In the database'|i18n( 'design/admin/setup/systemupgrade' )}</th></tr></thead>
            <tbody>
            {foreach $table.items as $table_item}
            <tr>
                <td>{$kind_labels[$table_item.kind]|wash}{if $table_item.detail|ne( '' )}<br /><span class="exp-meta">{$table_item.detail|wash}</span>{/if}</td>
                <td><code>{$table_item.name|wash}</code></td>
                <td class="exp-sums">{if $table_item.expected|ne( '' )}{$table_item.expected|wash}{else}&mdash;{/if}</td>
                <td class="exp-sums">{if $table_item.actual|ne( '' )}{$table_item.actual|wash}{else}&mdash;{/if}</td>
            </tr>
            {/foreach}
            </tbody>
        </table>
        </div>
        {/if}
        {if $table.sql|ne( '' )}
        <div class="exp-sql-head"><span class="exp-meta">{'SQL for %engine (not run)'|i18n( 'design/admin/setup/systemupgrade',, hash( '%engine', $schema.engine|wash ) )}</span></div>
        <pre class="exp-sql" tabindex="0">{$table.sql|wash}</pre>
        {/if}
    </div>
</details>
{/foreach}

{if $schema.counts.noise|gt( 0 )}
<details class="exp-fold" id="upgrade-engine-notes">
    <summary><h3>{'Engine notes'|i18n( 'design/admin/setup/systemupgrade' )}</h3><span class="exp-badge is-info">{$schema.counts.noise}</span><span class="exp-meta">{'Nothing to change'|i18n( 'design/admin/setup/systemupgrade' )}</span></summary>
    <div class="exp-fold-body">
        <div class="exp-fix">
            <strong>{'What it means and what to do'|i18n( 'design/admin/setup/systemupgrade' )}</strong>
            <p>{'The %engine schema handler writes no SQL for these: the engine names these types in its own words (on SQLite, text for longtext and an integer key it does not read as auto_increment). Nothing needs to change.'|i18n( 'design/admin/setup/systemupgrade',, hash( '%engine', $schema.engine|wash ) )}</p>
        </div>
        <div class="exp-table-wrap">
        <table class="exp-table">
            <thead><tr><th scope="col">{'Table'|i18n( 'design/admin/setup/systemupgrade' )}</th><th scope="col">{'Name'|i18n( 'design/admin/setup/systemupgrade' )}</th><th scope="col">{'Shipped'|i18n( 'design/admin/setup/systemupgrade' )}</th><th scope="col">{'In the database'|i18n( 'design/admin/setup/systemupgrade' )}</th></tr></thead>
            <tbody>
            {foreach $schema.tables as $table}{if $table.noise|not}{continue}{/if}
            {foreach $table.items as $table_item}
            <tr>
                <td><code>{$table.name|wash}</code>{if $table.source|ne( '' )}<br /><span class="exp-meta">{$table.source|wash}</span>{/if}</td>
                <td><code>{$table_item.name|wash}</code>{if $table_item.detail|ne( '' )}<br /><span class="exp-meta">{$table_item.detail|wash}</span>{/if}</td>
                <td class="exp-sums">{if $table_item.expected|ne( '' )}{$table_item.expected|wash}{else}&mdash;{/if}</td>
                <td class="exp-sums">{if $table_item.actual|ne( '' )}{$table_item.actual|wash}{else}&mdash;{/if}</td>
            </tr>
            {/foreach}
            {/foreach}
            </tbody>
        </table>
        </div>
    </div>
</details>
{/if}

{if $schema.sql|ne( '' )}
<details class="exp-fold">
    <summary><h3>{'All statements'|i18n( 'design/admin/setup/systemupgrade' )}</h3><span class="exp-meta">{'To synchronize your database with the distribution setup, run the following SQL commands'|i18n( 'design/admin/setup' )}</span></summary>
    <div class="exp-fold-body"><pre class="exp-sql" tabindex="0">{$schema.sql|wash}</pre></div>
</details>
{/if}

{else}
{* A document store (MongoDB): collections, not tables *}
    {if is_set( $mongo_grouped_list )}
<div class="exp-panel">
    <div class="exp-panel-head"><h3>{'%count collections are missing.'|i18n( 'design/admin/setup/systemupgrade',, hash( '%count', $mongo_missing_count ) )}</h3></div>
    <div class="exp-fix" style="margin-top: 10px"><p>{'MongoDB creates a collection on its first write, so a missing collection is often harmless until its feature is used. Create them up front with the mongosh command below.'|i18n( 'design/admin/setup/systemupgrade' )}</p></div>
    {foreach $mongo_grouped_list as $mongo_group}
    <p style="margin-top: 10px"><strong>{$mongo_group.name|wash}</strong> <span class="exp-badge">{$mongo_group.count}</span></p>
    <ul class="exp-ext-list">{foreach $mongo_group.collections as $mongo_collection}<li>{$mongo_collection|wash}</li>{/foreach}</ul>
    {/foreach}
    {if $mongo_extra|count|gt( 0 )}
    <p style="margin-top: 10px"><strong>{'Collections not in the schema'|i18n( 'design/admin/setup/systemupgrade' )}</strong> <span class="exp-badge">{$mongo_extra|count}</span></p>
    <ul class="exp-ext-list">{foreach $mongo_extra as $mongo_collection}<li>{$mongo_collection|wash}</li>{/foreach}</ul>
    {/if}
    {if $mongo_create_cmd|ne( '' )}<pre class="exp-sql" style="margin-top: 12px" tabindex="0">{$mongo_create_cmd|wash}</pre>{/if}
</div>
    {/if}
{/if}
</section>
{/if}

{* ---- What is checked: the manifests --------------------------------------------------------------------------- *}
{if $info}
<details class="exp-fold" id="upgrade-manifests">
    <summary><h3>{'What the file check reads'|i18n( 'design/admin/setup/systemupgrade' )}</h3><span class="exp-meta">{'%own extensions with a manifest of their own, %root in the manifest of Exponential, %none without one'|i18n( 'design/admin/setup/systemupgrade',, hash( '%own', $info.with_manifest, '%root', $info.in_root_manifest, '%none', $info.without ) )}</span></summary>
    <div class="exp-fold-body">
        <p>{'share/filelist.md5 lists every file of the Exponential release with its checksum, the extensions shipped inside it included. An extension released on its own may carry a share/filelist.md5 of its own, refreshed in each of its releases. An extension with neither is not checked.'|i18n( 'design/admin/setup/systemupgrade' )}</p>
        <dl class="exp-facts">
            <div><dt>{'Manifest'|i18n( 'design/admin/setup/systemupgrade' )}</dt><dd><code>{$info.manifest.file|wash}</code></dd></div>
            <div><dt>{'Files listed'|i18n( 'design/admin/setup/systemupgrade' )}</dt><dd>{$info.manifest.entries}{if $info.manifest.malformed|gt( 0 )} <span class="exp-badge is-warn">{'%count malformed lines'|i18n( 'design/admin/setup/systemupgrade',, hash( '%count', $info.manifest.malformed ) )}</span>{/if}</dd></div>
            <div><dt>{'Written'|i18n( 'design/admin/setup/systemupgrade' )}</dt><dd>{if $info.manifest.mtime|gt( 0 )}{$info.manifest.mtime|l10n( shortdatetime )}{else}&mdash;{/if}</dd></div>
            <div><dt>{'Last committed'|i18n( 'design/admin/setup/systemupgrade' )}</dt><dd>{if $info.manifest.committed|gt( 0 )}{$info.manifest.committed|l10n( shortdatetime )}{else}{'not a git checkout'|i18n( 'design/admin/setup/systemupgrade' )}{/if}</dd></div>
        </dl>
        <div class="exp-table-wrap">
        <table class="exp-table">
            <thead><tr>
                <th scope="col">{'Active extension'|i18n( 'design/admin/setup/systemupgrade' )}</th>
                <th scope="col">{'Checked by'|i18n( 'design/admin/setup/systemupgrade' )}</th>
                <th scope="col" class="exp-num">{'Files'|i18n( 'design/admin/setup/systemupgrade' )}</th>
                <th scope="col">{'Version'|i18n( 'design/admin/setup/systemupgrade' )}</th>
            </tr></thead>
            <tbody>
            {foreach $info.extensions as $extension}
            {if or( $extension.manifest, $extension.in_root|gt( 0 ) )}
            <tr>
                <td><code>{$extension.name|wash}</code></td>
                <td>{if $extension.manifest}<span class="exp-badge is-ok">{'Its own manifest'|i18n( 'design/admin/setup/systemupgrade' )}</span>{else}<span class="exp-badge is-info">{'The manifest of Exponential'|i18n( 'design/admin/setup/systemupgrade' )}</span>{/if}</td>
                <td class="exp-num">{if $extension.manifest}{$extension.entries}{else}{$extension.in_root}{/if}</td>
                <td>{if $extension.manifest}
                        {if $extension.manifest_version|ne( '' )}{'manifest %version'|i18n( 'design/admin/setup/systemupgrade',, hash( '%version', $extension.manifest_version|wash ) )}{/if}
                        {if $extension.version|ne( '' )}<br /><span class="exp-meta">{'extension %version'|i18n( 'design/admin/setup/systemupgrade',, hash( '%version', $extension.version|wash ) )}</span>{/if}
                        {if and( $extension.manifest_version|ne( '' ), $extension.version|ne( '' ), ne( $extension.manifest_version, $extension.version ) )}<br /><span class="exp-badge is-warn">{'The versions differ'|i18n( 'design/admin/setup/systemupgrade' )}</span>{/if}
                        {if and( $extension.manifest_files_count|gt( 0 ), ne( $extension.manifest_files_count, $extension.entries ) )}<br /><span class="exp-badge is-warn">{'Its header says %header files'|i18n( 'design/admin/setup/systemupgrade',, hash( '%header', $extension.manifest_files_count ) )}</span>{/if}
                    {else}{'as Exponential'|i18n( 'design/admin/setup/systemupgrade' )}{/if}</td>
            </tr>
            {/if}
            {/foreach}
            </tbody>
        </table>
        </div>
        {if $info.without|gt( 0 )}
        <p class="exp-meta">{'Not checked (no manifest):'|i18n( 'design/admin/setup/systemupgrade' )}</p>
        <ul class="exp-ext-list">{foreach $info.extensions as $extension}{if and( $extension.manifest|not, $extension.in_root|eq( 0 ) )}<li>{$extension.name|wash}</li>{/if}{/foreach}</ul>
        {/if}
    </div>
</details>
{/if}

<p class="exp-docs">{'How each finding is read and fixed, and how the check runs from the command line:'|i18n( 'design/admin/setup/systemupgrade' )}
    <a href="{$guide}">{'Guide: upgrade check'|i18n( 'design/admin/setup/systemupgrade' )}</a> &middot;
    <a href="https://github.com/se7enxweb/exponential/blob/main/doc/features/6.0/file-consistency-check.md">{'The file manifest'|i18n( 'design/admin/setup/systemupgrade' )}</a> &middot;
    <a href="https://github.com/se7enxweb/exponential/blob/main/doc/guides/upgrading.md">{'Upgrading'|i18n( 'design/admin/setup/systemupgrade' )}</a></p>

</form>

</div></div></div>

</div>

<script type="text/javascript">
var expUpgradeText = {ldelim}
    shown: '{'%shown of %count findings shown'|i18n( 'design/admin/setup/systemupgrade' )|wash( javascript )}',
    allShown: '{'%count findings'|i18n( 'design/admin/setup/systemupgrade' )|wash( javascript )}',
    busy: '{'Checking...'|i18n( 'design/admin/setup/systemupgrade' )|wash( javascript )}'
{rdelim};
{literal}
(function () {
    var root = document.querySelector( '.exp-upgrade' );
    if ( !root ) return;
    function each( selector, fn ) {
        var nodes = root.querySelectorAll( selector ), i;
        for ( i = 0; i < nodes.length; i++ ) fn( nodes[i], i );
    }
    function tr( name, values ) {
        var s = expUpgradeText[name], key;
        for ( key in values || {} ) s = s.split( '%' + key ).join( values[key] );
        return s;
    }
    each( '.exp-js-only', function ( el ) { el.hidden = false; } );

    // A check takes a few seconds: say so, and keep it from being pressed twice. The downloads stay on this page.
    var form = document.getElementById( 'upgrade-form' );
    if ( form ) {
        form.addEventListener( 'submit', function ( event ) {
            var button = event.submitter;
            if ( !button || !button.classList.contains( 'exp-run' ) ) return;
            var busy = document.getElementById( 'upgrade-busy' );
            if ( busy ) busy.hidden = false;
            button.classList.add( 'is-busy' );
            button.setAttribute( 'aria-busy', 'true' );
            button.textContent = expUpgradeText.busy;
            // a disabled button is not submitted: carry its name in a hidden field instead
            var carry = document.createElement( 'input' );
            carry.type = 'hidden'; carry.name = button.name; carry.value = button.value;
            form.appendChild( carry );
            each( '.exp-run', function ( b ) { b.disabled = true; } );
        } );
    }

    var findings = document.getElementById( 'upgrade-findings' );
    if ( !findings ) return;
    var searchEl = document.getElementById( 'upgrade-search' );
    var areaEl = document.getElementById( 'upgrade-area' );
    var countEl = document.getElementById( 'upgrade-filter-count' );
    var noMatchEl = document.getElementById( 'upgrade-no-match' );
    var total = findings.querySelectorAll( 'tr[data-file]' ).length;

    function apply() {
        var term = searchEl ? searchEl.value.trim().toLowerCase() : '';
        var area = areaEl ? areaEl.value : '';
        var picked = root.querySelector( 'input[name="UpgradeFilterState"]:checked' );
        var state = picked ? picked.value : '';
        var shown = 0;
        each( '#upgrade-findings details[data-group]', function ( group ) {
            var groupShown = 0;
            var rows = group.querySelectorAll( 'tr[data-file]' ), i;
            var stateOk = state === '' || group.getAttribute( 'data-group' ) === state;
            for ( i = 0; i < rows.length; i++ ) {
                var row = rows[i];
                var ok = stateOk && ( area === '' || row.getAttribute( 'data-area' ) === area ) &&
                         ( term === '' || row.getAttribute( 'data-search' ).indexOf( term ) !== -1 );
                row.hidden = !ok;
                if ( ok ) groupShown++;
            }
            group.hidden = !stateOk || ( rows.length > 0 && groupShown === 0 && ( term !== '' || area !== '' ) );
            if ( ( term !== '' || area !== '' || state !== '' ) && groupShown > 0 ) group.open = true;
            shown += groupShown;
        } );
        var filtered = term !== '' || area !== '' || state !== '';
        if ( countEl ) countEl.textContent = filtered ? tr( 'shown', { shown: shown, count: total } ) : tr( 'allShown', { count: total } );
        if ( noMatchEl ) noMatchEl.hidden = !( filtered && shown === 0 );
    }
    if ( searchEl ) searchEl.addEventListener( 'input', apply );
    if ( areaEl ) areaEl.addEventListener( 'change', apply );
    each( 'input[name="UpgradeFilterState"]', function ( r ) { r.addEventListener( 'change', apply ); } );
    apply();
})();
{/literal}
</script>
