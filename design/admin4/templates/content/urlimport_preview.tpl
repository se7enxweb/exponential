{* Import a URL map, the preview (content/urlimport/(plan)/<token>[/(state)/<state>][/(offset)/<n>]): what each row
   of the file would do. The counts by state (each a filter, view parameter /(state)/), the defaults, the hosts this
   installation does not know (map each to a siteaccess and plan again), then the rows a page at a time with a tick
   for each row that can be imported (new, and changed when the entry there may be replaced). Dry run checks and
   counts without writing; Import asks "Verify it's you" and writes the ticked rows as one import. After the import
   the page shows its result. The plan as CSV or JSON: /(report)/csv|json.

   Variables (kernel/private/classes/views/content/urlimport.php): import_token, import_meta, import_options,
   import_counts, import_states, import_state, import_rows, import_row_count, import_limit, import_page_uri,
   import_unknown_hosts, import_siteaccesses, import_dry_run, import_result, import_error, import_error_text,
   view_parameters. The same file is in design/admin and design/admin4. Works without javascript. *}

{def $base = concat( 'content/urlimport/(plan)/', $import_token )
     $state_part = cond( $import_state|ne( 'all' ), concat( '/(state)/', $import_state ), '' )
     $c = $import_counts
     $done = $import_result|is_array}

<div class="context-block a4-page a4-url a4-content-urlmap">

<div class="box-header"><div class="box-ml">
<div class="a4-title-row">
<h1 class="context-title">{'Import a URL map'|i18n( 'design/admin/content/urlimport' )}</h1>
<span class="a4-meta">{'%file, %format, %rows rows'|i18n( 'design/admin/content/urlimport',, hash( '%file', $import_meta.source_name, '%format', $import_meta.format, '%rows', $c.rows ) )|wash}</span>
</div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

{include uri='design:content/urlmap_toolbar.tpl' current='import' all=true()}

{if $done}
<div class="a4-notice is-ok" role="status">
    <p class="a4-notice-title"><strong>{'Imported as import %id.'|i18n( 'design/admin/content/urlimport',, hash( '%id', $import_result.batch_id ) )|wash}</strong></p>
    <p>{'%created created, %updated replaced, %skipped skipped, %failed failed.'|i18n( 'design/admin/content/urlimport',, hash( '%created', $import_result.counts.created, '%updated', $import_result.counts.updated, '%skipped', $import_result.counts.skipped, '%failed', $import_result.counts.failed ) )|wash}</p>
    <p><a href={concat( 'content/urlimporthistory/', $import_result.batch_id )|ezurl}>{'See the import, or undo it'|i18n( 'design/admin/content/urlimport' )}</a> &middot; <a href={'content/urlimport'|ezurl}>{'Import another file'|i18n( 'design/admin/content/urlimport' )}</a></p>
</div>
{else}
<p class="a4-intro">{'Nothing has been written yet. Each row shows what the import would do with it. Ticked rows are imported; untick a row to leave it out. Rows that are not new can only be imported when their old address may be replaced.'|i18n( 'design/admin/content/urlimport' )}</p>
{/if}

{switch match=$import_error}
{case match='error-import'}<div class="a4-notice is-bad" role="alert"><p>{'The import was not done: %error'|i18n( 'design/admin/content/urlimport',, hash( '%error', $import_error_text ) )|wash}</p></div>{/case}
{case match='error-nothing'}<div class="a4-notice is-warn" role="alert"><p>{'No row is ticked for import.'|i18n( 'design/admin/content/urlimport' )}</p></div>{/case}
{case match='error-format'}<div class="a4-notice is-bad" role="alert"><p>{'The file cannot be read: %error'|i18n( 'design/admin/content/urlimport',, hash( '%error', $import_error_text ) )|wash}</p></div>{/case}
{case}{/case}
{/switch}

{if is_array( $import_dry_run )}
<div class="a4-notice is-info" role="status">
    <p class="a4-notice-title"><strong>{'Dry run: nothing was written.'|i18n( 'design/admin/content/urlimport' )}</strong></p>
    <p>{'The import would create %created, replace %updated and skip %skipped; %failed would fail.'|i18n( 'design/admin/content/urlimport',, hash( '%created', $import_dry_run.counts.created, '%updated', $import_dry_run.counts.updated, '%skipped', $import_dry_run.counts.skipped, '%failed', $import_dry_run.counts.failed ) )|wash}</p>
</div>
{/if}

<section aria-labelledby="urlimport-counts-title">
<h2 class="a4-sr-only" id="urlimport-counts-title">{'Overview'|i18n( 'design/admin/content/urlimport' )}</h2>
<ul class="a4-figures">
    <li class="a4-figure is-ok"><strong>{$c.create}</strong><span>{'To create'|i18n( 'design/admin/content/urlimport' )}</span></li>
    <li class="a4-figure"><strong>{$c.update}</strong><span>{'To replace'|i18n( 'design/admin/content/urlimport' )}</span></li>
    <li class="a4-figure"><strong>{$c.skip}</strong><span>{'To skip'|i18n( 'design/admin/content/urlimport' )}</span></li>
    {if sum( $c['conflict'], $c['broken-target'], $c['not-importable'] )|gt( 0 )}<li class="a4-figure is-bad"><strong>{sum( $c['conflict'], $c['broken-target'], $c['not-importable'] )}</strong><span>{'With a problem'|i18n( 'design/admin/content/urlimport' )}</span></li>{/if}
</ul>
</section>

<dl class="a4-facts">
    <div><dt>{'Siteaccess of bare paths'|i18n( 'design/admin/content/urlimport' )}</dt><dd>{$import_options.siteaccess|wash}</dd></div>
    <div><dt>{'Language'|i18n( 'design/admin/content/urlimport' )}</dt><dd>{if $import_options.language|ne( '' )}{$import_options.language|wash}{else}{'The language of the siteaccess'|i18n( 'design/admin/content/urlimport' )}{/if}</dd></div>
    <div><dt>{'When an old address has an entry already'|i18n( 'design/admin/content/urlimport' )}</dt><dd>{if eq( $import_options.on_conflict, 'overwrite' )}{'Replace the entry'|i18n( 'design/admin/content/urlimport' )}{else}{'Keep the entry (skip the row)'|i18n( 'design/admin/content/urlimport' )}{/if}</dd></div>
    <div><dt>{'Kept until'|i18n( 'design/admin/content/urlimport' )}</dt><dd>{$import_meta.expires|l10n( shortdatetime )}</dd></div>
</dl>

<form method="post" action={concat( $base, $state_part, cond( $view_parameters.offset|gt( 0 ), concat( '/(offset)/', $view_parameters.offset ), '' ) )|ezurl}>

{if and( $done|not, or( $import_unknown_hosts|count|gt( 0 ), $c.changed|gt( 0 ) ) )}
<section class="a4-card a4-section" aria-labelledby="urlimport-replan-title">
<div class="a4-card-head">
    <h2 class="a4-h2" id="urlimport-replan-title">{'Plan again'|i18n( 'design/admin/content/urlimport' )}</h2>
    <p>{'Change what decides the rows and plan the file again. Ticks you changed by hand are reset.'|i18n( 'design/admin/content/urlimport' )}</p>
</div>
<div class="a4-form a4-card-body">
    {if $import_unknown_hosts|count|gt( 0 )}
    <p class="a4-help">{'Rows of these hosts are not imported, because this installation does not answer for them. Choose the siteaccess each old host stands for.'|i18n( 'design/admin/content/urlimport' )}</p>
    {foreach $import_unknown_hosts as $index => $host}
    <div class="a4-field">
        <label for="urlimport-host-{$index}">{$host|wash}</label>
        <select id="urlimport-host-{$index}" name="HostMap[{$host|wash}]">
            <option value="">{'Not imported'|i18n( 'design/admin/content/urlimport' )}</option>
            {foreach $import_siteaccesses as $siteaccess}<option value="{$siteaccess|wash}">{$siteaccess|wash}</option>{/foreach}
        </select>
    </div>
    {/foreach}
    {/if}
    <fieldset class="a4-field">
        <legend>{'When an old address has an entry already'|i18n( 'design/admin/content/urlimport' )}</legend>
        <label class="a4-check"><input type="radio" name="OnConflict" value="skip"{if ne( $import_options.on_conflict, 'overwrite' )} checked="checked"{/if} /> <span>{'Keep the entry (skip the row)'|i18n( 'design/admin/content/urlimport' )}</span></label>
        <label class="a4-check"><input type="radio" name="OnConflict" value="overwrite"{if eq( $import_options.on_conflict, 'overwrite' )} checked="checked"{/if} /> <span>{'Replace the entry'|i18n( 'design/admin/content/urlimport' )}</span></label>
    </fieldset>
    <div class="a4-actions">
        <button type="submit" class="a4-btn" name="ReplanButton" value="1">{'Plan again'|i18n( 'design/admin/content/urlimport' )}</button>
    </div>
</div>
</section>
{/if}

<section class="a4-section" aria-labelledby="urlimport-rows-title">
<div class="a4-section-head">
    <h2 class="a4-h2" id="urlimport-rows-title">{'Rows'|i18n( 'design/admin/content/urlimport' )}</h2>
    {if $import_row_count|gt( 0 )}<span class="a4-meta">{'%from to %to of %count'|i18n( 'design/admin/content/urlimport',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $import_limit ), $import_row_count ), '%count', $import_row_count ) )|wash}</span>{/if}
</div>

<div class="a4-filterbar">
    <div class="a4-field a4-field-wide">
        <span id="urlimport-state-label"><strong>{'Show'|i18n( 'design/admin/content/urlimport' )}</strong></span>
        <ul class="a4-tabs" aria-labelledby="urlimport-state-label">
            <li>{if eq( $import_state, 'all' )}<span class="current" aria-current="true">{'All'|i18n( 'design/admin/content/urlimport' )} ({$c.rows})</span>{else}<a href={$base|ezurl}>{'All'|i18n( 'design/admin/content/urlimport' )} <span class="a4-muted">{$c.rows}</span></a>{/if}</li>
        {foreach $import_states as $state}
            {if or( $c[$state]|gt( 0 ), eq( $state, $import_state ) )}
            <li>{if eq( $state, $import_state )}<span class="current" aria-current="true">{include uri='design:content/urlmap_state.tpl' state=$state label_only=true()} ({$c[$state]})</span>{else}<a href={concat( $base, '/(state)/', $state )|ezurl}>{include uri='design:content/urlmap_state.tpl' state=$state label_only=true()} <span class="a4-muted">{$c[$state]}</span></a>{/if}</li>
            {/if}
        {/foreach}
        </ul>
    </div>
</div>

{if $import_rows|count|eq( 0 )}
<p class="a4-empty">{'No row has this state.'|i18n( 'design/admin/content/urlimport' )} <a href={$base|ezurl}>{'Show all'|i18n( 'design/admin/content/urlimport' )}</a></p>
{else}
<div class="a4-table-wrap">
<table class="a4-table a4-content-urlmap-table">
<caption class="a4-sr-only">{'The rows of the file and what the import would do with each'|i18n( 'design/admin/content/urlimport' )}</caption>
<thead><tr>
    <th scope="col">{'Import'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col" class="a4-num">{'Line'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col">{'Old address'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col">{'New address'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col">{'Kind'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col">{'State'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col">{'Notes'|i18n( 'design/admin/content/urlimport' )}</th>
</tr></thead>
<tbody>
{foreach $import_rows as $row}
<tr>
    <td>{if and( $row.selectable, $done|not )}<input type="hidden" name="RowIndexList[]" value="{$row.index}" /><input type="checkbox" name="ImportRowList[]" value="{$row.index}"{if eq( $row.decision, 'import' )} checked="checked"{/if} aria-label="{'Import line %line'|i18n( 'design/admin/content/urlimport',, hash( '%line', $row.line ) )|wash}" />{elseif eq( $row.action, 'none' )}<span class="a4-muted">{'No'|i18n( 'design/admin/content/urlimport' )}</span>{else}{'Yes'|i18n( 'design/admin/content/urlimport' )}{/if}</td>
    <td class="a4-num">{$row.line}</td>
    <td class="a4-content-urlmap-addr"><code>{$row.old_url|wash}</code>{if $row.siteaccess|ne( '' )}<br /><span class="a4-meta">{cond( eq( $row.siteaccess, '*' ), 'Every siteaccess'|i18n( 'design/admin/content/urlimport' ), $row.siteaccess )|wash}{if $row.language|ne( '' )}, {$row.language|wash}{/if}</span>{/if}</td>
    <td class="a4-content-urlmap-addr"><code>{$row.new_url|wash}</code></td>
    <td>{include uri='design:content/urlmap_kind.tpl' kind=$row.kind status=$row.status}</td>
    <td>{include uri='design:content/urlmap_state.tpl' state=$row.state}</td>
    <td>{if $row.messages|count}<ul class="a4-content-urlmap-notes">{foreach $row.messages as $message}<li>{$message|wash}</li>{/foreach}</ul>{/if}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
{/if}

<div class="a4-listfoot">
    <div class="a4-pager">
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri=$import_page_uri
             item_count=$import_row_count
             view_parameters=$view_parameters
             item_limit=$import_limit}
    </div>
</div>
</section>

<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
    {if $done|not}
        <button type="submit" class="a4-btn a4-btn-primary" name="ImportButton" value="1">{'Import'|i18n( 'design/admin/content/urlimport' )}</button>
        <button type="submit" class="a4-btn" name="DryRunButton" value="1">{'Dry run'|i18n( 'design/admin/content/urlimport' )}</button>
        <button type="submit" class="a4-btn" name="SaveDecisionsButton" value="1">{'Keep the ticks'|i18n( 'design/admin/content/urlimport' )}</button>
    {/if}
        <a class="a4-btn" href={concat( $base, '/(report)/csv' )|ezurl}>{'Plan as CSV'|i18n( 'design/admin/content/urlimport' )}</a>
        <a class="a4-btn" href={concat( $base, '/(report)/json' )|ezurl}>{'Plan as JSON'|i18n( 'design/admin/content/urlimport' )}</a>
    {if $done|not}
        <button type="submit" class="a4-btn a4-btn-outline-danger" name="CancelImportButton" value="1">{'Throw away'|i18n( 'design/admin/content/urlimport' )}</button>
    {/if}
    </div>
    {if $done|not}<p class="a4-meta">{'Import writes the ticked rows of every page as one import, which can be undone from the import history; you confirm who you are first. Dry run checks every row against the site again and counts what the import would do, without writing.'|i18n( 'design/admin/content/urlimport' )}</p>{/if}
</div>

</form>

</div></div></div>
</div>
{undef $base $state_part $c $done}
