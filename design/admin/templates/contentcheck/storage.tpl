{* The contentcheck/storage view (doc/eng-us/features/6.0/content-checks.md, "Storage files").

   Variables: scope (the start location, see scope.tpl), params (show, sort, order, kinds, offset, limit, node_id),
   state (the last scan, or null), percent, rows (the page), count, quarantine (the listing, with Show=quarantine),
   quarantine_root, storage_dir, kinds, sorts, min_age_days, cluster (report only), can_quarantine, can_fix,
   batch_limit, feedback, select_all (GET SelectAll: every box of the page ticked), page_uri, page_uri_suffix, view_parameters, limit, limit_choices, limit_preference,
   form_token, token_field. The lists and filters are GET; the scan and every action are posted to
   contentcheck/storageaction, which shows each action's plan first and asks to confirm. The same file is in
   design/admin and design/admin4. *}
{def $kind_names = hash( 'owner', 'Wrong owner'|i18n( 'kernel/contentcheck' ),
                         'mode', 'Wrong mode'|i18n( 'kernel/contentcheck' ),
                         'empty', 'Empty file'|i18n( 'kernel/contentcheck' ),
                         'name', 'Name differs in case'|i18n( 'kernel/contentcheck' ),
                         'path', 'Path differs from its parts'|i18n( 'kernel/contentcheck' ),
                         'type', 'In another type directory'|i18n( 'kernel/contentcheck' ) )
     $kind_classes = hash( 'owner', 'is-warn', 'mode', 'is-warn', 'empty', 'is-bad', 'name', 'is-warn', 'path', 'is-info', 'type', 'is-warn' )
     $sort_names = hash( 'path', 'Path'|i18n( 'kernel/contentcheck' ),
                         'size', 'Size'|i18n( 'kernel/contentcheck' ),
                         'age', 'Age'|i18n( 'kernel/contentcheck' ) )
     $action_names = hash( 'quarantine', 'moved to the quarantine'|i18n( 'kernel/contentcheck' ),
                           'restore', 'restored'|i18n( 'kernel/contentcheck' ),
                           'delete', 'deleted from the quarantine'|i18n( 'kernel/contentcheck' ),
                           'permissions', 'given the expected owner and mode'|i18n( 'kernel/contentcheck' ) )
     $done = and( $state, eq( $state.phase, 'done' ) )
     $tickable = false()
     $tickable_count = 0}
{if eq( $params.show, 'orphans' )}{set $tickable = $can_quarantine}{set $tickable_count = $rows|count}{elseif eq( $params.show, 'quarantine' )}{set $tickable = $can_quarantine}{set $tickable_count = $rows|count}{else}{foreach $rows as $row}{if and( $can_fix, $row.fixable )}{set $tickable = true()}{set $tickable_count = $tickable_count|inc}{/if}{/foreach}{/if}

<div class="context-block a4-page a4-contentcheck">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Storage files'|i18n( 'kernel/contentcheck' )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="a4-intro">{'The files below %dir: those no content uses (orphan files), and the file-system problems of those it uses. A file counts as used when a version of any object, also an old one or one in the trash, names it, or when a field, a layout, a setting or a template names its path. Files younger than %days days are never orphans. Nothing is deleted from the storage: orphans go to a quarantine, from where they can be restored.'|i18n( 'kernel/contentcheck',, hash( '%dir', $storage_dir, '%days', $min_age_days ) )|wash}</p>

{if $feedback}
    {if $feedback.done}
<div class="a4-notice is-ok" role="status">{'%count files (%size) were %action.'|i18n( 'kernel/contentcheck',, hash( '%count', $feedback.count, '%size', $feedback.bytes|si( byte ), '%action', $action_names[$feedback.action] ) )|wash}{if $feedback.failed|gt( 0 )} {'%count could not be changed.'|i18n( 'kernel/contentcheck',, hash( '%count', $feedback.failed ) )}{/if}</div>
    {elseif eq( $feedback.reason, 'changed' )}
<div class="a4-notice is-warn" role="alert">{'Nothing was changed: the files changed after the confirmation. Tick them again.'|i18n( 'kernel/contentcheck' )}</div>
    {else}
<div class="a4-notice is-warn" role="alert">{'Nothing was changed.'|i18n( 'kernel/contentcheck' )}</div>
    {/if}
{/if}

{if $cluster}
<div class="a4-notice is-info" role="status">{'The files are kept by a cluster file handler: they are read from its table, owner and mode are not checked, and nothing can be moved or fixed here.'|i18n( 'kernel/contentcheck' )}</div>
{/if}

<section class="a4-section" aria-labelledby="cc-scan-title">
<div class="a4-section-head">
    <h2 class="a4-h2" id="cc-scan-title">{'Last scan'|i18n( 'kernel/contentcheck' )}</h2>
</div>
{if $state|not}
<p class="a4-empty">{'The storage has not been scanned yet.'|i18n( 'kernel/contentcheck' )}</p>
{elseif $done|not}
<p class="a4-meta">{'A scan started at %time is not finished (%percent %).'|i18n( 'kernel/contentcheck',, hash( '%time', $state.started|l10n( 'shortdatetime' ), '%percent', $percent ) )}</p>
<div class="a4-meter" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{$percent}" aria-label="{'Progress'|i18n( 'kernel/contentcheck' )|wash}"><span class="a4-pct-{$percent}"></span></div>
{else}
<dl class="a4-facts">
    <div><dt>{'Finished'|i18n( 'kernel/contentcheck' )}</dt><dd>{$state.finished|l10n( 'shortdatetime' )}</dd></div>
    <div><dt>{'Files'|i18n( 'kernel/contentcheck' )}</dt><dd>{$state.counts.files} ({$state.counts.bytes|si( byte )})</dd></div>
    <div><dt>{'Used'|i18n( 'kernel/contentcheck' )}</dt><dd>{$state.counts.used}</dd></div>
    <div><dt>{'Orphan files'|i18n( 'kernel/contentcheck' )}</dt><dd>{$state.counts.orphans} ({$state.counts.orphan_bytes|si( byte )})</dd></div>
    <div><dt>{'Too new to judge'|i18n( 'kernel/contentcheck' )}</dt><dd>{$state.counts.recent}</dd></div>
    <div><dt>{'Took'|i18n( 'kernel/contentcheck' )}</dt><dd>{'%seconds s in %steps steps'|i18n( 'kernel/contentcheck',, hash( '%seconds', $state.seconds, '%steps', $state.steps ) )}</dd></div>
</dl>
{if is_set( $state.changed )}<p class="a4-meta">{'Files were changed since this scan; the lists leave out what was moved or fixed.'|i18n( 'kernel/contentcheck' )}</p>{/if}
{if ne( $state.index, 'ok' )}
<div class="a4-notice is-warn" role="alert">{'The reference index could not be read completely, so paths named only in rich text, layouts, settings or templates were not seen. Nothing is moved until it can be read.'|i18n( 'kernel/contentcheck' )}</div>
{/if}
{/if}
<form method="post" action={'/contentcheck/storageaction'|ezurl} class="a4-toolbar">
    <input type="hidden" name="{$token_field|wash}" value="{$form_token|wash}" />
    <input type="hidden" name="NodeID" value="{$params.node_id}" />
    <input type="hidden" name="Show" value="{$params.show|wash}" />
    <div class="a4-actions">
    {if and( $state, $done|not )}
        <button type="submit" class="a4-btn a4-btn-primary" name="ContinueButton" value="1">{'Continue the scan'|i18n( 'kernel/contentcheck' )}</button>
        <button type="submit" class="a4-btn" name="RestartButton" value="1">{'Start again'|i18n( 'kernel/contentcheck' )}</button>
    {else}
        <button type="submit" class="a4-btn{if $state|not} a4-btn-primary{/if}" name="ScanButton" value="1">{if $state}{'Scan again'|i18n( 'kernel/contentcheck' )}{else}{'Scan the storage'|i18n( 'kernel/contentcheck' )}{/if}</button>
    {/if}
    </div>
    <p class="a4-meta">{'The scan runs in steps of a few seconds and can be continued later; on a large storage run ./console exp:content-check --storage.'|i18n( 'kernel/contentcheck' )}</p>
</form>
</section>

<nav aria-label="{'Lists'|i18n( 'kernel/contentcheck' )|wash}">
<ul class="a4-tabs">
    <li><a href={concat( $page_uri, '?Show=orphans' )|ezurl}{if eq( $params.show, 'orphans' )} aria-current="page"{/if}>{'Orphan files'|i18n( 'kernel/contentcheck' )}{if $done} ({$state.counts.orphans}){/if}</a></li>
    <li><a href={concat( $page_uri, '?Show=problems' )|ezurl}{if eq( $params.show, 'problems' )} aria-current="page"{/if}>{'File problems'|i18n( 'kernel/contentcheck' )}</a></li>
    <li><a href={concat( $page_uri, '?Show=quarantine' )|ezurl}{if eq( $params.show, 'quarantine' )} aria-current="page"{/if}>{'Quarantine'|i18n( 'kernel/contentcheck' )}</a></li>
</ul>
</nav>

{if eq( $params.show, 'problems' )}
<section aria-labelledby="cc-filter-title">
<h2 class="a4-sr-only" id="cc-filter-title">{'Filter'|i18n( 'kernel/contentcheck' )}</h2>
<form class="a4-filterbar" method="get" action={$page_uri|ezurl}>
    <input type="hidden" name="Show" value="problems" />
    {include uri='design:contentcheck/scope.tpl' scope=$scope}
    <fieldset class="a4-field">
        <legend>{'Problems'|i18n( 'kernel/contentcheck' )}</legend>
        <div class="a4-chips">
        {foreach $kinds as $kind}
            <label class="a4-chip"><input type="checkbox" name="Kind[]" value="{$kind|wash}"{if $params.kinds_shown|contains( $kind )} checked="checked"{/if} /><span>{$kind_names[$kind]|wash}</span></label>
        {/foreach}
        </div>
    </fieldset>
    <div class="a4-field">
        <button type="submit" class="a4-btn a4-btn-primary">{'Show'|i18n( 'kernel/contentcheck' )}</button>
    </div>
    {include uri='design:contentcheck/filter_choice.tpl' group='Kind' label='Problems'|i18n( 'kernel/contentcheck' )}
</form>
</section>
{elseif eq( $params.show, 'orphans' )}
<section aria-labelledby="cc-filter-title">
<h2 class="a4-sr-only" id="cc-filter-title">{'Filter'|i18n( 'kernel/contentcheck' )}</h2>
<form class="a4-filterbar" method="get" action={$page_uri|ezurl}>
    <input type="hidden" name="Show" value="orphans" />
    {include uri='design:contentcheck/scope.tpl' scope=$scope}
    <div class="a4-field">
        <label for="cc-sort">{'Order by'|i18n( 'kernel/contentcheck' )}</label>
        <select id="cc-sort" name="Sort">
        {foreach $sorts as $sort}
            <option value="{$sort|wash}"{if eq( $sort, $params.sort )} selected="selected"{/if}>{$sort_names[$sort]|wash}</option>
        {/foreach}
        </select>
    </div>
    <div class="a4-field">
        <label for="cc-order">{'Direction'|i18n( 'kernel/contentcheck' )}</label>
        <select id="cc-order" name="Order">
            <option value="asc"{if eq( $params.order, 'asc' )} selected="selected"{/if}>{'Ascending'|i18n( 'kernel/contentcheck' )}</option>
            <option value="desc"{if eq( $params.order, 'desc' )} selected="selected"{/if}>{'Descending'|i18n( 'kernel/contentcheck' )}</option>
        </select>
    </div>
    <div class="a4-field">
        <button type="submit" class="a4-btn a4-btn-primary">{'Show'|i18n( 'kernel/contentcheck' )}</button>
    </div>
</form>
<p class="a4-meta">{'Orphan files belong to no content, so the start location does not apply to them: the whole storage is listed.'|i18n( 'kernel/contentcheck' )}</p>
</section>
{/if}

<form method="post" action={'/contentcheck/storageaction'|ezurl}>
<input type="hidden" name="{$token_field|wash}" value="{$form_token|wash}" />
<input type="hidden" name="NodeID" value="{$params.node_id}" />
<input type="hidden" name="Show" value="{$params.show|wash}" />

<section class="a4-section" aria-labelledby="cc-list-title">
{include uri='design:contentcheck/results_head.tpl' title=cond( eq( $params.show, 'orphans' ), 'Orphan files'|i18n( 'kernel/contentcheck' ), eq( $params.show, 'problems' ), 'File problems'|i18n( 'kernel/contentcheck' ), 'Quarantine'|i18n( 'kernel/contentcheck' ) )
         title_id='cc-list-title' offset=$params.offset limit=$limit count=$count}

{if eq( $params.show, 'quarantine' )}
<p class="a4-meta">{'%count files (%size) in %dir.'|i18n( 'kernel/contentcheck',, hash( '%count', $quarantine.count, '%size', $quarantine.bytes|si( byte ), '%dir', $quarantine_root ) )|wash}</p>
{elseif and( $done, eq( $params.show, 'orphans' ), $state.counts.orphans|gt( $count ) )}
<p class="a4-meta">{'The scan keeps the first %count orphans; move them, then scan again for the others.'|i18n( 'kernel/contentcheck',, hash( '%count', $count ) )}</p>
{/if}

{if and( $done|not, ne( $params.show, 'quarantine' ) )}
<p class="a4-empty">{'Scan the storage to see this list.'|i18n( 'kernel/contentcheck' )}</p>
{elseif $rows|count|eq( 0 )}
<p class="a4-empty">{if eq( $params.show, 'orphans' )}{'No orphan files.'|i18n( 'kernel/contentcheck' )}{elseif eq( $params.show, 'problems' )}{'No file problems below this node match the filter.'|i18n( 'kernel/contentcheck' )}{else}{'The quarantine is empty.'|i18n( 'kernel/contentcheck' )}{/if}</p>
{else}
<div class="a4-table-wrap">
<table class="a4-table" data-a4-select="1">
<thead>
<tr>
    {if $tickable}<th scope="col" class="tight">{include uri='design:contentcheck/select_all.tpl' select_all=$select_all}</th>{/if}
    {if eq( $params.show, 'problems' )}<th scope="col">{'Problem'|i18n( 'kernel/contentcheck' )}</th>{/if}
    <th scope="col">{'Path'|i18n( 'kernel/contentcheck' )}</th>
    {if eq( $params.show, 'problems' )}<th scope="col">{'Used by'|i18n( 'kernel/contentcheck' )}</th>{/if}
    {if eq( $params.show, 'quarantine' )}<th scope="col">{'Moved on'|i18n( 'kernel/contentcheck' )}</th>{/if}
    <th scope="col" class="a4-num">{'Size'|i18n( 'kernel/contentcheck' )}</th>
    {if eq( $params.show, 'orphans' )}<th scope="col" class="a4-num">{'Age'|i18n( 'kernel/contentcheck' )}</th>{/if}
    {if ne( $params.show, 'quarantine' )}<th scope="col">{'Owner and mode'|i18n( 'kernel/contentcheck' )}</th>{/if}
</tr>
</thead>
<tbody>
{foreach $rows as $row}
<tr>
    {if eq( $params.show, 'orphans' )}
    {if $tickable}<td><input type="checkbox" name="Items[]" value="{$row.path|wash}" aria-label="{$row.path|wash}" data-a4-select-item="1"{if $select_all} checked="checked"{/if} /></td>{/if}
    <td><span class="a4-tag is-mono">{$row.path|wash}</span></td>
    <td class="a4-num">{$row.size|si( byte )}</td>
    <td class="a4-num">{'%days d'|i18n( 'kernel/contentcheck',, hash( '%days', $row.age_days ) )}</td>
    <td>{$row.owner|wash} {$row.mode|wash}</td>
    {elseif eq( $params.show, 'problems' )}
    {if $tickable}<td>{if and( $can_fix, $row.fixable )}<input type="checkbox" name="Items[]" value="{$row.path|wash}" aria-label="{$row.path|wash}" data-a4-select-item="1"{if $select_all} checked="checked"{/if} />{/if}</td>{/if}
    <td><span class="a4-tag {$kind_classes[$row.kind]}">{$kind_names[$row.kind]|wash}</span></td>
    <td><span class="a4-tag is-mono">{$row.path|wash}</span>{if $row.detail|ne( '' )}<div class="a4-meta">{'Found: %path'|i18n( 'kernel/contentcheck',, hash( '%path', $row.detail ) )|wash}</div>{/if}</td>
    <td>{if $row.node_id|gt( 0 )}<a href={concat( '/content/view/full/', $row.node_id )|ezurl}>{$row.name|wash}</a><div class="a4-meta">{'Object %id'|i18n( 'kernel/contentcheck',, hash( '%id', $row.object_id ) )}</div>{else}&ndash;{/if}</td>
    <td class="a4-num">{if $row.size|ge( 0 )}{$row.size|si( byte )}{else}&ndash;{/if}</td>
    <td>{$row.owner|wash} {$row.mode|wash}</td>
    {else}
    {if $tickable}<td><input type="checkbox" name="Items[]" value="{$row.item|wash}" aria-label="{$row.path|wash}" data-a4-select-item="1"{if $select_all} checked="checked"{/if} /></td>{/if}
    <td><span class="a4-tag is-mono">{$row.path|wash}</span></td>
    <td>{$row.date|wash}</td>
    <td class="a4-num">{$row.size|si( byte )}</td>
    {/if}
</tr>
{/foreach}
</tbody>
</table>
</div>
{/if}


{if and( $tickable, $rows|count|gt( 0 ) )}
    {if eq( $params.show, 'orphans' )}<input type="hidden" name="Sort" value="{$params.sort|wash}" /><input type="hidden" name="Order" value="{$params.order|wash}" />{/if}
    {if eq( $params.show, 'problems' )}{foreach $params.kinds as $kind}<input type="hidden" name="Kind[]" value="{$kind|wash}" />{/foreach}{/if}
{set-block variable=cc_actions}
    {if eq( $params.show, 'orphans' )}
        <button type="submit" class="a4-btn a4-btn-outline-danger" name="Action" value="quarantine" aria-describedby="cc-action-help">{'Move ticked files to the quarantine'|i18n( 'kernel/contentcheck' )}</button>
        {if $count|gt( $rows|count )}<button type="submit" class="a4-btn a4-btn-outline-danger" name="AllResultsAction" value="quarantine" aria-describedby="cc-action-help">{'Move all %count files to the quarantine'|i18n( 'kernel/contentcheck',, hash( '%count', $count ) )}</button>{/if}
    {elseif eq( $params.show, 'problems' )}
        <button type="submit" class="a4-btn" name="Action" value="permissions" aria-describedby="cc-action-help">{'Fix owner and mode of ticked files'|i18n( 'kernel/contentcheck' )}</button>
        {if $count|gt( $rows|count )}<button type="submit" class="a4-btn" name="AllResultsAction" value="permissions" aria-describedby="cc-action-help">{'Fix owner and mode of all %count results'|i18n( 'kernel/contentcheck',, hash( '%count', $count ) )}</button>{/if}
    {else}
        <button type="submit" class="a4-btn" name="Action" value="restore" aria-describedby="cc-action-help">{'Restore ticked files'|i18n( 'kernel/contentcheck' )}</button>
        <button type="submit" class="a4-btn a4-btn-outline-danger" name="Action" value="delete" aria-describedby="cc-action-help">{'Delete ticked files permanently'|i18n( 'kernel/contentcheck' )}</button>
        {if $count|gt( $rows|count )}<button type="submit" class="a4-btn" name="AllResultsAction" value="restore" aria-describedby="cc-action-help">{'Restore all %count files'|i18n( 'kernel/contentcheck',, hash( '%count', $count ) )}</button>{/if}
    {/if}
{/set-block}
{include uri='design:contentcheck/bulkbar.tpl' select_all=$select_all total=$tickable_count
         page_url=concat( $page_uri, '/(offset)/', $params.offset, $page_uri_suffix ) actions=$cc_actions help_id='cc-action-help'
         help='The next page is a dry run: it shows what would happen to each file and changes nothing until you confirm. At most %count files at a time.'|i18n( 'kernel/contentcheck',, hash( '%count', $batch_limit ) )}
{/if}

{include uri='design:contentcheck/pager.tpl' page_uri=$page_uri page_uri_suffix=$page_uri_suffix item_count=$count
         view_parameters=$view_parameters limit=$limit limit_choices=$limit_choices limit_preference=$limit_preference}
</section>
</form>

</div></div></div>
</div>
{undef $kind_names $kind_classes $sort_names $action_names $done $tickable $tickable_count}
