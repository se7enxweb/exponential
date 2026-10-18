{* Setup > Log files (setup/logs; doc/features/6.0/log-viewer.md): one log file, followed live, searched and
   cross-referenced.

   Variables (kernel/private/classes/views/setup/logs.php): log_file, log_error, log_initial, log_search,
   log_related, log_searching, log_criteria, log_scope, log_rotations, log_levels, log_actions, log_can_unmask,
   log_unmasked, log_masking, log_client, log_token_field, log_token, log_done, log_done_ok, log_siteaccesses,
   log_max_download, log_groups, log_state_json, log_permalink (setup/logs/view/<key>... of this view, without done, ok
   and unmask), log_share_permalink (the same with the request and client address filters as the masks show them: the
   link to copy and share; log_permalink when not set), log_key, log_view_state.

   Every link of the page is a permalink by log key (expLogPermalink): setup/logs/view/<key>/(name)/value. The search
   form and the paging link go through setup/logs?key=<key>&..., which answers 303 with the permalink, so they work
   without JavaScript; the script keeps the address in step with the view (paused, wrapped, the focused entry).

   Without JavaScript the page shows the end of the file (or the results of the search) as a list, the search form,
   paging of the results and the actions. page/setup_logs.js follows the file through setup/logsfeed (short
   requests, the interval growing while nothing arrives), draws only the lines in view, finds, and opens one entry
   with the lines of its request, its debug output and the audit of that moment.

   The same file is in design/admin and design/admin4. Styles: stylesheets/modules/setup.css (.a4-setup-log*), no
   inline CSS. Every line arrives masked from the server. *}
{def $log_level_names = hash( 'error',   'Error'|i18n( 'design/admin/setup/logs' ),
                              'warning', 'Warning'|i18n( 'design/admin/setup/logs' ),
                              'notice',  'Notice'|i18n( 'design/admin/setup/logs' ),
                              'debug',   'Debug'|i18n( 'design/admin/setup/logs' ) )
     $log_group_names = hash( 'kernel',     'Kernel'|i18n( 'design/admin/setup/logs' ),
                              'site',       'Site'|i18n( 'design/admin/setup/logs' ),
                              'extensions', 'Extensions'|i18n( 'design/admin/setup/logs' ),
                              'webserver',  'Web server'|i18n( 'design/admin/setup/logs' ),
                              'archives',   'Rotated archives'|i18n( 'design/admin/setup/logs' ) )
     $log_entries = array()
     $log_q = ''
     $log_crit = ''
     $log_page_q = ''
     $log_base = 'setup/logs'
     $log_label = ''
     $log_has_file = is_set( $log_file.id )
     $log_can_templates = fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'setup' ) )
     $log_can_finder = fetch( 'user', 'has_access_to', hash( 'module', 'contentfinder', 'function', 'search' ) )
     $log_can_audit = fetch( 'user', 'has_access_to', hash( 'module', 'audit', 'function', 'read' ) )}
{if $log_searching}
    {if is_set( $log_search.entries )}{set $log_entries = $log_search.entries}{/if}
{elseif is_set( $log_initial.entries )}
    {set $log_entries = $log_initial.entries}
{/if}
{* The criteria of this page's search: for the feed ($log_q, by file id) and for the paging link ($log_page_q, by key:
   setup/logs?key=... answers 303 with the permalink). $log_base is the plain permalink of the log. *}
{if $log_criteria.q|ne( '' )}{set $log_crit = concat( $log_crit, '&q=', $log_criteria.q|rawurlencode )}{/if}
{if $log_criteria.regex}{set $log_crit = concat( $log_crit, '&regex=1' )}{/if}
{if $log_criteria.case}{set $log_crit = concat( $log_crit, '&case=1' )}{/if}
{if $log_criteria.levels|count}{set $log_crit = concat( $log_crit, '&levels=', $log_criteria.levels|implode( ',' ) )}{/if}
{if $log_criteria.from_text|ne( '' )}{set $log_crit = concat( $log_crit, '&from=', $log_criteria.from_text|rawurlencode )}{/if}
{if $log_criteria.to_text|ne( '' )}{set $log_crit = concat( $log_crit, '&to=', $log_criteria.to_text|rawurlencode )}{/if}
{if $log_criteria.siteaccess|ne( '' )}{set $log_crit = concat( $log_crit, '&siteaccess=', $log_criteria.siteaccess|rawurlencode )}{/if}
{if $log_criteria.uri|ne( '' )}{set $log_crit = concat( $log_crit, '&uri=', $log_criteria.uri|rawurlencode )}{/if}
{if $log_criteria.ip|ne( '' )}{set $log_crit = concat( $log_crit, '&ip=', $log_criteria.ip|rawurlencode )}{/if}
{if $log_criteria.reference|ne( '' )}{set $log_crit = concat( $log_crit, '&reference=', $log_criteria.reference|rawurlencode )}{/if}
{set $log_crit = concat( $log_crit, '&scope=', $log_scope|rawurlencode )}
{if $log_rotations}{set $log_crit = concat( $log_crit, '&rotations=1' )}{/if}
{if $log_unmasked}{set $log_crit = concat( $log_crit, '&unmask=1' )}{/if}
{if $log_has_file}
    {set $log_q = concat( 'file=', $log_file.id|rawurlencode, $log_crit )
         $log_page_q = concat( 'key=', $log_file.key|rawurlencode, cond( $log_file.rotation|ne( 0 ), concat( '&archive=', $log_file.rotation ), '' ), $log_crit )
         $log_base = concat( 'setup/logs/view/', $log_file.key, cond( $log_file.rotation|ne( 0 ), concat( '/(archive)/', $log_file.rotation ), '' ) )
         $log_label = cond( $log_file.label|ne( '' ), $log_file.label, $log_file.name )}
{/if}

<div class="context-block a4-page a4-setup-logs{if $log_searching} is-searching{/if}" id="a4-setup-logs">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{if $log_has_file}{'Log file %name'|i18n( 'design/admin/setup/logs',, hash( '%name', $log_label ) )|wash}{else}{'Log files'|i18n( 'design/admin/setup/logs' )}{/if}</h1>
<div class="header-mainline"></div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">
{* The buttons of the log viewer's pages, then this page's intro (setup/logs_nav.tpl): the same place on every page *}
{include uri='design:setup/logs_nav.tpl' log_nav_current='logs'
         log_nav_intro='Follows a log file as it is written, searches it and its rotations, and links the lines of one request. Nothing on this page changes a file unless an action below says so.'|i18n( 'design/admin/setup/logs' )}

{if $log_done|ne( '' )}
<div class="a4-notice {if $log_done_ok}is-ok{else}is-bad{/if}" role="{if $log_done_ok}status{else}alert{/if}">
{if $log_done_ok}
    {switch match=$log_done}
    {case match='rotate'}{'The file was rotated. The next line written starts a new file.'|i18n( 'design/admin/setup/logs' )}{/case}
    {case match='truncate'}{'The file was emptied.'|i18n( 'design/admin/setup/logs' )}{/case}
    {case}{'The archived rotation was removed.'|i18n( 'design/admin/setup/logs' )}{/case}
    {/switch}
{else}
    {'The action could not be carried out. The web server may not change the file or its directory.'|i18n( 'design/admin/setup/logs' )}
{/if}
</div>
{/if}

{if $log_error|eq( 'not_allowed' )}
<div class="a4-notice is-bad" role="alert">{'The file asked for is not a log file this viewer may open (settings/logviewer.ini [LogViewerSettings] Directories[] and FilePatterns[]). The default file is shown instead.'|i18n( 'design/admin/setup/logs' )}</div>
{/if}

{if $log_criteria.invalid|count}
<div class="a4-notice is-bad" role="alert">
    <p class="a4-notice-title">{'The search could not be run:'|i18n( 'design/admin/setup/logs' )}</p>
    <ul>
    {foreach $log_criteria.invalid as $log_field => $log_why}
        <li>{switch match=$log_why}
            {case match='too_long'}{'The search text is too long.'|i18n( 'design/admin/setup/logs' )}{/case}
            {case match='not_a_time'}{'"%field" is not a time.'|i18n( 'design/admin/setup/logs',, hash( '%field', $log_field ) )|wash}{/case}
            {case match='not_an_address'}{'The IP address may only hold digits, letters a to f, dots and colons.'|i18n( 'design/admin/setup/logs' )}{/case}
            {case match='invalid_regex'}{'The regular expression is not valid.'|i18n( 'design/admin/setup/logs' )}{/case}
            {case match='not_a_reference'}{'An error reference looks like ERR-0123456789.'|i18n( 'design/admin/setup/logs' )}{/case}
            {case}{$log_why|wash}{/case}
        {/switch}</li>
    {/foreach}
    </ul>
</div>
{/if}

{if $log_has_file|not}
<div class="a4-notice is-info" role="status">{'There is no log file this viewer may open yet.'|i18n( 'design/admin/setup/logs' )}</div>
{else}

{* The file: a compact chooser for narrow windows, where the left menu is folded away. Without JavaScript it sends the
   key (setup/logs?key=..., answered 303 with the permalink); the script opens the option's permalink at once. *}
<form method="get" action={'setup/logs'|ezurl} class="a4-setup-log-picker" id="log-picker-form">
    <label for="log-picker">{'File'|i18n( 'design/admin/setup/logs' )}</label>
    <select id="log-picker" name="file">
    {foreach $log_groups as $log_group => $log_list}
        {if $log_list|count}
        <optgroup label="{$log_group_names[ $log_group ]|wash}">
        {foreach $log_list as $log_item}
            <option value="{$log_item.id|wash}" data-href={concat( 'setup/logs/view/', $log_item.key, cond( $log_item.rotation|ne( 0 ), concat( '/(archive)/', $log_item.rotation ), '' ), cond( $log_unmasked, '/(unmask)/1', '' ) )|ezurl}{if $log_item.id|eq( $log_file.id )} selected="selected"{/if}>{if $log_item.labeled}{$log_item.label|wash} ({$log_item.name|wash}){else}{$log_item.id|wash}{/if}</option>
        {/foreach}
        </optgroup>
        {/if}
    {/foreach}
    </select>
    {if $log_unmasked}<input type="hidden" name="unmask" value="1" />{/if}
    <div class="a4-setup-log-picker-actions"><button type="submit" class="a4-btn a4-btn-primary">{'Open'|i18n( 'design/admin/setup/logs' )}</button></div>
</form>

{* The permalink of this view (never with the unmasked choice): selectable without JavaScript, copied with the button *}
{if is_set( $log_share_permalink )}{def $log_share_link = $log_share_permalink}{else}{def $log_share_link = $log_permalink}{/if}
<div class="a4-row a4-setup-log-permalink">
    <label for="log-permalink">{'Permalink'|i18n( 'design/admin/setup/logs' )}</label>
    <input type="text" id="log-permalink" class="a4-input" readonly="readonly" value={$log_share_link|ezurl( 'double', 'full' )} aria-describedby="log-permalink-help" />
    <div class="a4-setup-log-picker-actions"><button type="button" class="a4-btn a4-btn-primary" id="log-copy-messages" disabled="disabled" aria-describedby="log-copy-messages-help"><span class="a4-icon a4-bi-files" aria-hidden="true"></span>{'Copy messages'|i18n( 'design/admin/setup/logs' )}</button><button type="button" class="a4-btn a4-btn-primary" id="log-copy-link" disabled="disabled"><span class="a4-icon a4-bi-link-45deg" aria-hidden="true"></span>{'Copy permalink'|i18n( 'design/admin/setup/logs' )}</button></div>
    <span class="a4-help" id="log-copy-messages-help">{'Copy messages copies the lines shown as plain text: every kept line while following, the lines in view while paused. They are masked as shown.'|i18n( 'design/admin/setup/logs' )}</span>
    <span class="a4-help" id="log-permalink-help">{'A link to this log and this view (filters, search, paused or following, the focused line), to bookmark or share. It never shows the lines unmasked.'|i18n( 'design/admin/setup/logs' )}</span>
</div>

<ul class="a4-setup-inline a4-setup-log-facts">
    <li><code>{if $log_file.configured}{$log_file.name|wash}{else}{$log_file.id|wash}{/if}</code></li>
    <li><code>{$log_file.key|wash}</code></li>
    <li>{$log_group_names[ $log_file.group ]|wash}</li>
    <li id="log-size">{$log_file.size|si( byte )}</li>
    <li>{'Changed %time'|i18n( 'design/admin/setup/logs',, hash( '%time', $log_file.mtime|l10n( 'shortdatetime' ) ) )|wash}</li>
    {if $log_file.gz}<li>{'Compressed, read only'|i18n( 'design/admin/setup/logs' )}</li>{elseif $log_file.archive}<li>{'Archived rotation, not followed'|i18n( 'design/admin/setup/logs' )}</li>{/if}
    {if $log_masking}<li>{'E-mail addresses, IP addresses and secrets are masked'|i18n( 'design/admin/setup/logs' )}</li>{elseif $log_unmasked}<li class="a4-warn-text">{'Shown unmasked'|i18n( 'design/admin/setup/logs' )}</li>{/if}
</ul>

{* ---- the controls of the view; the script enables the buttons (without it they would do nothing) ---- *}
<section class="a4-toolbar a4-setup-log-toolbar" aria-labelledby="log-toolbar-title">
    <h2 class="a4-sr-only" id="log-toolbar-title">{'Controls'|i18n( 'design/admin/setup/logs' )}</h2>
    <div class="a4-row">
        <span class="a4-status{if and( $log_searching|not, $log_file.archive|not )} is-running{/if}" id="log-status">
            <span class="a4-dot" aria-hidden="true"></span>
            <span id="log-status-text">{if $log_searching}{'Search results'|i18n( 'design/admin/setup/logs' )}{elseif $log_file.archive}{'Archive'|i18n( 'design/admin/setup/logs' )}{else}{'Following'|i18n( 'design/admin/setup/logs' )}{/if}</span>
        </span>
        <button type="button" class="a4-btn a4-btn-small" id="log-pause" aria-pressed="false" disabled="disabled"{if or( $log_searching, $log_file.archive )} hidden{/if}>
            <span class="a4-icon a4-bi-pause-fill" aria-hidden="true"></span><span id="log-pause-label">{'Pause'|i18n( 'design/admin/setup/logs' )}</span>
        </button>
        <button type="button" class="a4-btn a4-btn-small" id="log-newest" disabled="disabled">
            <span class="a4-icon a4-bi-arrow-bar-down" aria-hidden="true"></span>{'Newest'|i18n( 'design/admin/setup/logs' )}
        </button>
        <label class="a4-check a4-setup-log-switch"><input type="checkbox" id="log-follow" checked="checked" disabled="disabled" /><span>{'Follow'|i18n( 'design/admin/setup/logs' )}</span></label>
        <label class="a4-check a4-setup-log-switch"><input type="checkbox" id="log-wrap" disabled="disabled"{if $log_wrap} checked="checked"{/if} /><span>{'Wrap lines'|i18n( 'design/admin/setup/logs' )}</span></label>
        {if $log_can_unmask}
        <a class="a4-btn a4-btn-small a4-btn-ghost" id="log-unmask" data-unmasked="{if $log_unmasked}1{else}0{/if}"
           href={concat( $log_permalink, cond( $log_unmasked, '', '/(unmask)/1' ) )|ezurl}>
            <span class="a4-icon {if $log_unmasked}a4-bi-eye-slash{else}a4-bi-eye{/if}" aria-hidden="true"></span>{if $log_unmasked}{'Mask again'|i18n( 'design/admin/setup/logs' )}{else}{'Show unmasked'|i18n( 'design/admin/setup/logs' )}{/if}
        </a>
        {/if}
    </div>
    <div class="a4-row a4-setup-log-find" role="search" aria-label="{'Find in the loaded lines'|i18n( 'design/admin/setup/logs' )|wash}">
        <label for="log-find" class="a4-sr-only">{'Find in the loaded lines'|i18n( 'design/admin/setup/logs' )}</label>
        <input type="search" id="log-find" class="a4-input" placeholder="{'Find in view'|i18n( 'design/admin/setup/logs' )|wash}" disabled="disabled" aria-describedby="log-find-count" />
        <button type="button" class="a4-btn a4-btn-small a4-btn-icon" id="log-find-prev" disabled="disabled" title="{'Previous match (Shift+Enter)'|i18n( 'design/admin/setup/logs' )|wash}"><span class="a4-icon a4-bi-arrow-up" aria-hidden="true"></span><span class="a4-sr-only">{'Previous match'|i18n( 'design/admin/setup/logs' )}</span></button>
        <button type="button" class="a4-btn a4-btn-small a4-btn-icon" id="log-find-next" disabled="disabled" title="{'Next match (Enter)'|i18n( 'design/admin/setup/logs' )|wash}"><span class="a4-icon a4-bi-arrow-down" aria-hidden="true"></span><span class="a4-sr-only">{'Next match'|i18n( 'design/admin/setup/logs' )}</span></button>
        <span class="a4-meta" id="log-find-count"></span>
    </div>
    <p class="a4-meta a4-setup-log-keys" id="log-keys">{'Keys in the list: Space pauses or resumes, End jumps to the newest line, Up and Down move, Enter opens a line, n and N find the next and previous match.'|i18n( 'design/admin/setup/logs' )}</p>
</section>

{* ---- the search ---- *}
<details class="a4-fold a4-setup-log-search"{if $log_searching} open{/if}>
<summary>{'Search'|i18n( 'design/admin/setup/logs' )}</summary>
<div class="a4-fold-body">
<form method="get" action={'setup/logs'|ezurl} class="a4-filterbar a4-setup-log-searchform" role="search" aria-label="{'Search the log files'|i18n( 'design/admin/setup/logs' )|wash}">
    <input type="hidden" name="key" value="{$log_file.key|wash}" />
    {if $log_file.rotation|ne( 0 )}<input type="hidden" name="rotation" value="{$log_file.rotation|wash}" />{/if}
    {if $log_unmasked}<input type="hidden" name="unmask" value="1" />{/if}
    <div class="a4-field a4-field-wide">
        <label for="log-q">{'Text'|i18n( 'design/admin/setup/logs' )}</label>
        <input type="search" id="log-q" name="q" value="{$log_criteria.q|wash}" maxlength="1000" aria-describedby="log-q-help" />
        <span class="a4-help" id="log-q-help">{'Literal text, or a regular expression (without delimiters) when Regular expression is ticked. A search stops after a few seconds and offers to go on.'|i18n( 'design/admin/setup/logs' )}</span>
    </div>
    <div class="a4-field">
        <label class="a4-check"><input type="checkbox" name="regex" value="1"{if $log_criteria.regex} checked="checked"{/if} /><span>{'Regular expression'|i18n( 'design/admin/setup/logs' )}</span></label>
        <label class="a4-check"><input type="checkbox" name="case" value="1"{if $log_criteria.case} checked="checked"{/if} /><span>{'Match case'|i18n( 'design/admin/setup/logs' )}</span></label>
    </div>
    <fieldset class="a4-field">
        <legend>{'Levels'|i18n( 'design/admin/setup/logs' )}</legend>
        <div class="a4-row">
        {foreach $log_levels as $log_level}
            <label class="a4-check"><input type="checkbox" name="levels[]" value="{$log_level|wash}"{if $log_criteria.levels|contains( $log_level )} checked="checked"{/if} /><span class="a4-setup-log-level is-{$log_level|wash}">{$log_level_names[ $log_level ]|wash}</span></label>
        {/foreach}
        </div>
    </fieldset>
    <div class="a4-field">
        <label for="log-from">{'From'|i18n( 'design/admin/setup/logs' )}</label>
        <input type="datetime-local" id="log-from" name="from" step="1" value="{$log_criteria.from_text|wash}" />
    </div>
    <div class="a4-field">
        <label for="log-to">{'To'|i18n( 'design/admin/setup/logs' )}</label>
        <input type="datetime-local" id="log-to" name="to" step="1" value="{$log_criteria.to_text|wash}" />
    </div>
    <div class="a4-field">
        <label for="log-sa">{'Siteaccess'|i18n( 'design/admin/setup/logs' )}</label>
        <input type="text" id="log-sa" name="siteaccess" list="log-sa-list" value="{$log_criteria.siteaccess|wash}" maxlength="64" />
        <datalist id="log-sa-list">{foreach $log_siteaccesses as $log_sa}<option value="{$log_sa|wash}"></option>{/foreach}</datalist>
    </div>
    <div class="a4-field">
        <label for="log-uri">{'Request address contains'|i18n( 'design/admin/setup/logs' )}</label>
        <input type="text" id="log-uri" name="uri" value="{$log_criteria.uri|wash}" maxlength="300" />
    </div>
    <div class="a4-field">
        <label for="log-ip">{'Client IP starts with'|i18n( 'design/admin/setup/logs' )}</label>
        <input type="text" id="log-ip" name="ip" value="{$log_criteria.ip|wash}" maxlength="64" inputmode="decimal" autocomplete="off" />
    </div>
    <div class="a4-field">
        <label for="log-ref">{'Error reference'|i18n( 'design/admin/setup/logs' )}</label>
        <input type="text" id="log-ref" name="reference" value="{$log_criteria.reference|wash}" maxlength="20" placeholder="ERR-0123456789" autocomplete="off" aria-describedby="log-ref-help" />
        <span class="a4-help" id="log-ref-help">{'The reference an error page showed: finds its line in every kernel log and its rotations, and the lines of its request.'|i18n( 'design/admin/setup/logs' )}</span>
    </div>
    <fieldset class="a4-field">
        <legend>{'Search in'|i18n( 'design/admin/setup/logs' )}</legend>
        <label class="a4-check"><input type="radio" name="scope" value="file"{if $log_scope|eq( 'file' )} checked="checked"{/if} /><span>{'This file'|i18n( 'design/admin/setup/logs' )}</span></label>
        <label class="a4-check"><input type="radio" name="scope" value="kernel"{if $log_scope|eq( 'kernel' )} checked="checked"{/if} /><span>{'All kernel logs'|i18n( 'design/admin/setup/logs' )}</span></label>
        <label class="a4-check"><input type="radio" name="scope" value="all"{if $log_scope|eq( 'all' )} checked="checked"{/if} /><span>{'All log files'|i18n( 'design/admin/setup/logs' )}</span></label>
        <label class="a4-check"><input type="checkbox" name="rotations" value="1"{if $log_rotations} checked="checked"{/if} /><span>{'Include rotated files'|i18n( 'design/admin/setup/logs' )}</span></label>
    </fieldset>
    <div class="a4-field a4-field-wide a4-row">
        <button type="submit" class="a4-btn a4-btn-primary"><span class="a4-icon a4-bi-search" aria-hidden="true"></span>{'Search'|i18n( 'design/admin/setup/logs' )}</button>
        {if $log_searching}<a class="a4-btn" href={concat( $log_base, cond( $log_unmasked, '/(unmask)/1', '' ) )|ezurl}>{'Back to following'|i18n( 'design/admin/setup/logs' )}</a>{/if}
    </div>
</form>
</div>
</details>

{if $log_searching}
<p class="a4-meta" id="log-search-summary" role="status">
{if is_set( $log_search.entries )}
    {'%count entries found'|i18n( 'design/admin/setup/logs',, hash( '%count', $log_search.entries|count ) )|wash}
    {switch match=$log_search.stopped}
    {case match='time'}{'(the search stopped after its time limit; more may follow)'|i18n( 'design/admin/setup/logs' )}{/case}
    {case match='bytes'}{'(the search stopped after reading its share; more may follow)'|i18n( 'design/admin/setup/logs' )}{/case}
    {case match='results'}{'(the first page of results)'|i18n( 'design/admin/setup/logs' )}{/case}
    {case match='regex_limit'}{'(the regular expression needed too much work on a line and was stopped; make it more specific)'|i18n( 'design/admin/setup/logs' )}{/case}
    {case}{/case}
    {/switch}
{/if}
</p>
{/if}

{if $log_related}
<section class="a4-card a4-setup-log-related" aria-labelledby="log-related-title">
    <h2 class="a4-card-title" id="log-related-title">{'The request of %reference'|i18n( 'design/admin/setup/logs',, hash( '%reference', $log_criteria.reference ) )|wash}</h2>
    {if and( is_set( $log_related.entries ), $log_related.entries|count )}
    <ol class="a4-setup-log-plain">
    {foreach $log_related.entries as $log_e}
        <li class="a4-setup-log-row is-{$log_e.lv|wash}"><span class="a4-setup-log-time">{$log_e.ts|wash}</span> <span class="a4-setup-log-level is-{$log_e.lv|wash}">{$log_level_names[ $log_e.lv ]|wash}</span> <span class="a4-setup-log-file">{$log_e.f|wash}</span> <span class="a4-setup-log-msg">{foreach $log_e.p as $log_part}{$log_part[0]|wash}{/foreach}</span></li>
    {/foreach}
    </ol>
    {else}
    <p class="a4-meta">{'No other line of this request was found.'|i18n( 'design/admin/setup/logs' )}</p>
    {/if}
    {if and( $log_can_audit, is_set( $log_related.audit.from ) )}
    <p><a href={concat( 'audit/console/(from)/', $log_related.audit.from, '/(to)/', $log_related.audit.to )|ezurl}>{'The audit of this moment'|i18n( 'design/admin/setup/logs' )}</a></p>
    {/if}
</section>
{/if}

{* ---- the lines ---- *}
<div class="a4-setup-log-view{if $log_searching} is-results{/if}{if $log_wrap} is-wrapped{/if}" id="log-view" tabindex="0" role="region" aria-labelledby="log-view-title" aria-describedby="log-keys">
    <h2 class="a4-sr-only" id="log-view-title">{if $log_searching}{'Search results'|i18n( 'design/admin/setup/logs' )}{else}{'Lines of %name'|i18n( 'design/admin/setup/logs',, hash( '%name', $log_file.name ) )|wash}{/if}</h2>
    {if $log_entries|count}
    <ol class="a4-setup-log-plain" id="log-list">
    {foreach $log_entries as $log_e}
        <li class="a4-setup-log-row is-{$log_e.lv|wash}" data-offset="{$log_e.o|wash}">
            <span class="a4-setup-log-time">{$log_e.ts|wash}</span>
            <span class="a4-setup-log-level is-{$log_e.lv|wash}">{$log_level_names[ $log_e.lv ]|wash}</span>
            {if is_set( $log_e.f )}<span class="a4-setup-log-file">{$log_e.f|wash}</span>{/if}
            {if $log_e.src|ne( '' )}<span class="a4-setup-log-src">{$log_e.src|wash}</span>{/if}
            {if or( $log_e.sa|ne( '' ), $log_e.ip|ne( '' ), $log_e.u|ne( '' ) )}<span class="a4-setup-log-req">{$log_e.sa|wash} {$log_e.ip|wash} {$log_e.u|wash}</span>{/if}
            <span class="a4-setup-log-msg">{foreach $log_e.p as $log_part}{$log_part[0]|wash}{/foreach}</span>
        </li>
    {/foreach}
    </ol>
    {else}
    <p class="a4-empty" id="log-empty">{if $log_searching}{'Nothing matches the search.'|i18n( 'design/admin/setup/logs' )}{else}{'The file is empty.'|i18n( 'design/admin/setup/logs' )}{/if}</p>
    {/if}
</div>

{if and( $log_searching, is_set( $log_search.cursor ), $log_search.cursor|ne( '' ) )}
<p class="a4-setup-log-more"><a class="a4-btn" id="log-more" href={concat( 'setup/logs?', $log_page_q, '&cursor=', $log_search.cursor|rawurlencode )|ezurl}>{'More results'|i18n( 'design/admin/setup/logs' )}</a></p>
{/if}

{* ---- one entry, opened by the script ---- *}
<section class="a4-card a4-setup-log-detail" id="log-detail" aria-labelledby="log-detail-title" tabindex="-1" hidden>
    <div class="a4-card-head">
        <h2 class="a4-card-title" id="log-detail-title">{'Entry'|i18n( 'design/admin/setup/logs' )}</h2>
        <div class="a4-card-actions"><button type="button" class="a4-btn a4-btn-small" id="log-detail-close">{'Close'|i18n( 'design/admin/setup/logs' )}</button></div>
    </div>
    <div class="a4-card-body">
        <dl class="a4-facts is-rows a4-setup-log-detail-facts" id="log-detail-facts"></dl>
        <pre class="a4-setup-log-detail-text" id="log-detail-text"></pre>
        <div class="a4-row" id="log-detail-actions"></div>
        <div id="log-detail-related" aria-live="polite"></div>
    </div>
</section>

{* ---- actions ---- *}
{if or( $log_actions.download, $log_actions.rotate, $log_actions.truncate, $log_actions.delete )}
<section class="a4-card a4-setup-log-actions" aria-labelledby="log-actions-title">
    <h2 class="a4-card-title" id="log-actions-title">{'Actions'|i18n( 'design/admin/setup/logs' )}</h2>
    {if $log_actions.download}
    <form method="post" action={'setup/logsaction'|ezurl} class="a4-row a4-setup-log-download">
        <input type="hidden" name="{$log_token_field|wash}" value="{$log_token|wash}" />
        <input type="hidden" name="File" value="{$log_file.id|wash}" />
        {if $log_unmasked}<input type="hidden" name="Unmask" value="1" />{/if}
        <div class="a4-field">
            <label for="log-dl-from">{'From byte'|i18n( 'design/admin/setup/logs' )}</label>
            <input type="number" id="log-dl-from" name="From" min="0" step="1" inputmode="numeric" />
        </div>
        <div class="a4-field">
            <label for="log-dl-to">{'To byte'|i18n( 'design/admin/setup/logs' )}</label>
            <input type="number" id="log-dl-to" name="To" min="0" step="1" inputmode="numeric" />
        </div>
        <button type="submit" class="a4-btn a4-btn-primary" name="DownloadButton" value="1"><span class="a4-icon a4-bi-download" aria-hidden="true"></span>{'Download (gzip)'|i18n( 'design/admin/setup/logs' )}</button>
        <p class="a4-help">{'Empty fields: the last %size of the file. Masked as shown here; recorded in the audit.'|i18n( 'design/admin/setup/logs',, hash( '%size', $log_max_download|si( byte ) ) )|wash}</p>
    </form>
    {/if}
    {if and( $log_file.configured|not, or( and( $log_actions.rotate, $log_file.archive|not ), and( $log_actions.truncate, $log_file.archive|not ), and( $log_actions.delete, $log_file.archive ) ) )}
    <form method="post" action={'setup/logsaction'|ezurl} class="a4-row a4-setup-log-manage">
        <input type="hidden" name="{$log_token_field|wash}" value="{$log_token|wash}" />
        <input type="hidden" name="File" value="{$log_file.id|wash}" />
        {if and( $log_actions.rotate, $log_file.archive|not )}<button type="submit" class="a4-btn a4-btn-primary" name="RotateButton" value="1"><span class="a4-icon a4-bi-arrow-clockwise" aria-hidden="true"></span>{'Rotate now'|i18n( 'design/admin/setup/logs' )}</button>{/if}
        {if and( $log_actions.truncate, $log_file.archive|not )}<button type="submit" class="a4-btn a4-btn-outline-danger" name="TruncateButton" value="1"><span class="a4-icon a4-bi-x-circle-fill" aria-hidden="true"></span>{'Empty the file'|i18n( 'design/admin/setup/logs' )}</button>{/if}
        {if and( $log_actions.delete, $log_file.archive )}<button type="submit" class="a4-btn a4-btn-outline-danger" name="DeleteButton" value="1"><span class="a4-icon a4-bi-trash" aria-hidden="true"></span>{'Remove this rotation'|i18n( 'design/admin/setup/logs' )}</button>{/if}
        <p class="a4-help">{'Each of these asks for a confirmation first and is recorded in the audit.'|i18n( 'design/admin/setup/logs' )}</p>
    </form>
    {/if}
</section>
{/if}

{/if}

{* Announcements of new lines, at most one every few seconds *}
<p class="a4-sr-only" id="log-live" role="status" aria-live="polite" aria-atomic="true"></p>

{* The values the script needs, read from here by page/setup_logs.js; the data it starts from follows as JSON *}
<div id="log-script" hidden
     data-feed-url={'setup/logsfeed'|ezurl}
     data-pref-url={'user/preferences/set_and_exit'|ezurl}
     data-token-field="{$log_token_field|wash}"
     data-token="{$log_token|wash}"
     data-page-url={'setup/logs'|ezurl}
     data-node-url={'content/view/full'|ezurl}
     data-class-url={'class/view'|ezurl}
     data-finder-url={if $log_can_finder}{'contentfinder/search'|ezurl}{else}""{/if}
     data-template-url={if $log_can_templates}{'setup/templateview'|ezurl}{else}""{/if}
     data-audit-url={if $log_can_audit}{'audit/console'|ezurl}{else}""{/if}
     data-search-query="{$log_q|wash}"
     data-permalink-url={$log_permalink|ezurl}
     data-share-url={$log_share_link|ezurl}
     data-base-url={$log_base|ezurl}
     data-text-copied="{'Link copied'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-lines-copied="{'%count lines copied'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-lines-copied-one="{'1 line copied'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-lines-copy-failed="{'The lines could not be copied. Select them in the list and copy them with the keyboard.'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-copy-failed="{'The link could not be copied; it is selected in the field, copy it with the keyboard.'|i18n( 'design/admin/setup/logs' )|wash}"
     data-can-unmask="{if $log_can_unmask}1{else}0{/if}"
     data-text-pause="{'Pause'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-resume="{'Resume'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-following="{'Following'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-paused="{'Paused'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-archive="{'Archive'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-results="{'Search results'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-waiting="{'Waiting %seconds s (too many requests)'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-offline="{'No answer from the server; trying again'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-gone="{'The file can no longer be read'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-new-lines="{'%count new lines'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-new-line="{'1 new line'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-rotated="{'The file was rotated or emptied; following the new one.'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-match="{'Match %index of %count'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-no-match="{'No match'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-kept="{'%count lines kept (the oldest are dropped from the page, never from the file)'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-entry="{'Entry at %time'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-time="{'Time'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-level="{'Level'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-source="{'Source'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-siteaccess="{'Siteaccess'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-client="{'Client'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-request="{'Request'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-file="{'File'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-offset="{'Byte offset'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-related="{'Lines of the same request'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-related-none="{'No other line of this request was found.'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-related-loading="{'Looking for the lines of this request…'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-debug="{'Debug output of this request'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-debug-none="{'No debug output of this request was logged.'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-audit="{'The audit of this moment'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-reference="{'Find %reference everywhere'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-open-request="{'Open the request'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-cut="{'(cut: the entry is longer than the viewer keeps)'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-more-loading="{'Loading more results…'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-more-done="{'No more results.'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-failed="{'The request failed: %error'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-level-error="{'Error'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-level-warning="{'Warning'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-level-notice="{'Notice'|i18n( 'design/admin/setup/logs' )|wash}"
     data-text-level-debug="{'Debug'|i18n( 'design/admin/setup/logs' )|wash}"></div>
{set-block variable=$log_state_block}{$log_state_json}{/set-block}
<script type="application/json" id="log-state">{$log_state_block|explode( '<' )|implode( '\u003c' )|explode( '>' )|implode( '\u003e' )}</script>
{ezscript( array( 'page/setup_logs_copy.js', 'page/setup_logs.js' ) )}

</div></div></div>
</div>
{undef $log_level_names $log_group_names $log_entries $log_q $log_crit $log_page_q $log_base $log_label $log_has_file $log_can_templates $log_can_finder $log_can_audit $log_share_link}
