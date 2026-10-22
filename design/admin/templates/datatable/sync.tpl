{* The datatable/sync view: make, change, run or remove one synchronisation.
   Variables: sync (or null), form (name, direction, mode, class, content_class, subtree, map, keys, conflict, interval,
   state, options), problems, messages, classes (class, origin, connection), content_classes (identifier, name),
   node_name, meta_sources, runs (expKernelTransferRun). *}
<div class="a4-page a4-datatable" id="exp-datatable-sync">

<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <p class="a4-pagehead-pre"><a href={'datatable/syncs'|ezurl}>{'Synchronisations'|i18n( 'design/admin/datatable' )}</a></p>
        <h1 class="a4-pagehead-title context-title">{if $sync}{$sync.name|wash}{else}{'New synchronisation'|i18n( 'design/admin/datatable' )}{/if}</h1>
    </div>
</div>

{foreach $messages as $m}
<div class="a4-notice is-{if eq( $m.kind, 'ok' )}ok{elseif eq( $m.kind, 'warn' )}warn{else}bad{/if}" role="{if eq( $m.kind, 'bad' )}alert{else}status{/if}"><p>{$m.text|wash}</p></div>
{/foreach}
{if $problems|count}
<div class="a4-error-summary a4-notice is-bad" role="alert"><p class="a4-notice-title">{'The synchronisation was not saved'|i18n( 'design/admin/datatable' )}</p><ul>{foreach $problems as $p}<li>{$p|wash}</li>{/foreach}</ul></div>
{/if}

<form method="post" action={concat( 'datatable/sync/', cond( $sync, $sync.id, 'new' ) )|ezurl} class="a4-form">
<div class="a4-card a4-section">
<div class="a4-card-body a4-stack">
    <div class="a4-grid">
        <div class="a4-field">
            <label class="a4-label" for="sync-name">{'Name'|i18n( 'design/admin/datatable' )} <span class="a4-required-mark" aria-hidden="true">*</span></label>
            <input class="a4-input" id="sync-name" name="Sync_name" required="required" maxlength="255" value="{$form.name|wash}" />
        </div>
        <div class="a4-field">
            <label class="a4-label" for="sync-direction">{'Direction'|i18n( 'design/admin/datatable' )}</label>
            <select class="a4-input" id="sync-direction" name="Sync_direction">
                {foreach hash( 'content_to_table', 'Content to the table', 'table_to_content', 'The table to content', 'two_way', 'Both ways' ) as $k => $l}<option value="{$k}"{if eq( $k, $form.direction )} selected="selected"{/if}>{$l|i18n( 'design/admin/datatable' )}</option>{/foreach}
            </select>
        </div>
        <div class="a4-field">
            <label class="a4-label" for="sync-mode">{'Runs'|i18n( 'design/admin/datatable' )}</label>
            <select class="a4-input" id="sync-mode" name="Sync_mode" aria-describedby="sync-mode-help">
                {foreach hash( 'once', 'When started', 'scheduled', 'On a schedule', 'live', 'Live, and on a schedule' ) as $k => $l}<option value="{$k}"{if eq( $k, $form.mode )} selected="selected"{/if}>{$l|i18n( 'design/admin/datatable' )}</option>{/foreach}
            </select>
            <p class="a4-help" id="sync-mode-help">{'Live: every publish, move and removal of an object of the class below the node is written to the table at once.'|i18n( 'design/admin/datatable' )}</p>
        </div>
    </div>
    <div class="a4-grid">
        <div class="a4-field">
            <label class="a4-label" for="sync-class">{'Data table (persistent class)'|i18n( 'design/admin/datatable' )}</label>
            <select class="a4-input" id="sync-class" name="Sync_class">
                <option value="">-</option>
                {foreach $classes as $c}<option value="{$c.class|wash}"{if eq( $c.class, $form.class )} selected="selected"{/if}>{$c.class|wash} ({$c.origin|wash}{if $c.connection}, {$c.connection|wash}{/if})</option>{/foreach}
            </select>
        </div>
        <div class="a4-field">
            <label class="a4-label" for="sync-content-class">{'Content class'|i18n( 'design/admin/datatable' )}</label>
            <select class="a4-input" id="sync-content-class" name="Sync_content_class">
                <option value="">-</option>
                {foreach $content_classes as $c}<option value="{$c.identifier|wash}"{if eq( $c.identifier, $form.content_class )} selected="selected"{/if}>{$c.name|wash} ({$c.identifier|wash})</option>{/foreach}
            </select>
        </div>
        <div class="a4-field">
            <label class="a4-label" for="sync-subtree">{'Node'|i18n( 'design/admin/datatable' )}</label>
            <input class="a4-input is-auto" type="number" min="1" id="sync-subtree" name="Sync_subtree" value="{$form.subtree|wash}" aria-describedby="sync-subtree-help" />
            <p class="a4-help" id="sync-subtree-help">{if $node_name}{$node_name|wash}. {/if}{'The subtree read for the table, and the parent of objects made from rows.'|i18n( 'design/admin/datatable' )}</p>
        </div>
    </div>
    <div class="a4-field a4-field-wide">
        <label class="a4-label" for="sync-map">{'Map: one line per column, column = source'|i18n( 'design/admin/datatable' )}</label>
        <textarea class="a4-input" id="sync-map" name="Sync_map" rows="8" aria-describedby="sync-map-help">{$form.map|wash}</textarea>
        <p class="a4-help" id="sync-map-help">{'A source is an attribute identifier of the content class, or one of: %list. Only attributes are written to content.'|i18n( 'design/admin/datatable',, hash( '%list', $meta_sources|implode( ' ' ) ) )|wash}</p>
    </div>
    <div class="a4-grid">
        <div class="a4-field">
            <label class="a4-label" for="sync-keys">{'Key column(s)'|i18n( 'design/admin/datatable' )}</label>
            <input class="a4-input" id="sync-keys" name="Sync_keys" maxlength="255" value="{$form.keys|wash}" aria-describedby="sync-keys-help" />
            <p class="a4-help" id="sync-keys-help">{'The mapped column(s) a row is matched by, e.g. contentobject_id or remote_id.'|i18n( 'design/admin/datatable' )}</p>
        </div>
        <div class="a4-field">
            <label class="a4-label" for="sync-conflict">{'When both sides changed'|i18n( 'design/admin/datatable' )}</label>
            <select class="a4-input" id="sync-conflict" name="Sync_conflict">
                {foreach hash( 'newest', 'The newer change wins', 'content', 'Content wins', 'table', 'The table wins', 'ask', 'Ask: a conflict to resolve' ) as $k => $l}<option value="{$k}"{if eq( $k, $form.conflict )} selected="selected"{/if}>{$l|i18n( 'design/admin/datatable' )}</option>{/foreach}
            </select>
        </div>
        <div class="a4-field">
            <label class="a4-label" for="sync-interval">{'Every (seconds)'|i18n( 'design/admin/datatable' )}</label>
            <input class="a4-input is-auto" type="number" min="60" step="60" id="sync-interval" name="Sync_interval" value="{$form.interval|wash}" />
        </div>
    </div>
    <label class="a4-check"><input type="checkbox" name="Sync_remove_missing" value="1"{if and( is_set( $form.options.remove_missing ), $form.options.remove_missing )} checked="checked"{/if} /> {'Remove the row of an object that is removed or leaves the subtree'|i18n( 'design/admin/datatable' )}</label>
    <label class="a4-check"><input type="checkbox" name="Sync_state" value="disabled"{if eq( $form.state, 'disabled' )} checked="checked"{/if} /> {'Switched off (no scheduled or live runs)'|i18n( 'design/admin/datatable' )}</label>
</div>
<div class="a4-card-foot a4-actions">
    <button type="submit" class="a4-btn a4-btn-primary" name="SaveButton" value="1">{'Save'|i18n( 'design/admin/datatable' )}</button>
    <button type="submit" class="a4-btn" name="SaveRunButton" value="1">{'Save and run now'|i18n( 'design/admin/datatable' )}</button>
    <button type="submit" class="a4-btn" name="ProposeButton" value="1" formnovalidate="formnovalidate">{'Propose the map'|i18n( 'design/admin/datatable' )}</button>
    <button type="submit" class="a4-btn a4-btn-ghost" name="CancelButton" value="1" formnovalidate="formnovalidate">{'Back to the list'|i18n( 'design/admin/datatable' )}</button>
</div>
</div>

{if $sync}
<details class="a4-confirm">
    <summary class="a4-btn a4-btn-outline-danger">{'Remove this synchronisation'|i18n( 'design/admin/datatable' )}</summary>
    <div class="a4-fold-body is-warn">
        <p>{'The rows and the content stay; only the pairing and the open conflicts go.'|i18n( 'design/admin/datatable' )}</p>
        <label class="a4-check"><input type="checkbox" name="ConfirmRemove" value="1" /> {'Yes, remove it'|i18n( 'design/admin/datatable' )}</label>
        <button type="submit" class="a4-btn a4-btn-danger" name="RemoveButton" value="1" formnovalidate="formnovalidate">{'Remove'|i18n( 'design/admin/datatable' )}</button>
    </div>
</details>
{/if}
</form>

{if $runs|count}
<section class="a4-card is-flush a4-section" aria-labelledby="sync-runs">
    <div class="a4-card-head"><h2 class="a4-h3 a4-card-title" id="sync-runs">{'The last runs'|i18n( 'design/admin/datatable' )}</h2></div>
    <div class="a4-table-wrap" role="region" aria-labelledby="sync-runs" tabindex="0">
    <table class="a4-table is-compact">
        <thead><tr><th scope="col">{'Started'|i18n( 'design/admin/datatable' )}</th><th scope="col">{'State'|i18n( 'design/admin/datatable' )}</th><th scope="col">{'Engine'|i18n( 'design/admin/datatable' )}</th><th scope="col" class="a4-num">{'Done'|i18n( 'design/admin/datatable' )}</th><th scope="col" class="a4-num">{'Failed'|i18n( 'design/admin/datatable' )}</th><th scope="col">{'Error'|i18n( 'design/admin/datatable' )}</th></tr></thead>
        <tbody>
        {foreach $runs as $r}<tr><td>{if $r.started}{$r.started|l10n( 'shortdatetime' )}{else}{$r.created|l10n( 'shortdatetime' )}{/if}</td><td><span class="a4-badge{if eq( $r.state, 'done' )} is-ok{elseif eq( $r.state, 'failed' )} is-bad{/if}">{$r.state|wash}</span></td><td>{$r.engine|wash}</td><td class="a4-num">{$r.rows_done}</td><td class="a4-num">{$r.rows_failed}</td><td>{$r.error|wash}</td></tr>{/foreach}
        </tbody>
    </table>
    </div>
</section>
{/if}

</div>
