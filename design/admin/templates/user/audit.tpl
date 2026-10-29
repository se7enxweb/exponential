{* The user audit log (user/audit): the security events about user accounts and user groups (table expuser_audit),
   newest first: when, the event and its result, the account or group it was about, who acted, where (siteaccess,
   host, address) and the fields before and after. A GET form narrows the list (user: an id or a login, the account
   or the user who acted; action; result; from and to), the pager pages it, and Export as CSV (a POST) sends the
   whole filtered list. Needs the policy user/audit. The same file is in design/admin and design/admin4.
   Guide: doc/eng-us/features/6.0/user-audit.md *}

{def $uri = 'user/audit'
     $form_uri = concat( $uri, $audit_query )
     $result_levels = hash( 'success', 'is-ok', 'refused', 'is-bad', 'failed', 'is-warn' )
     $result_names = hash( 'success', 'Done'|i18n( 'design/admin/user/audit' ),
                           'refused', 'Refused'|i18n( 'design/admin/user/audit' ),
                           'failed', 'Failed'|i18n( 'design/admin/user/audit' ) )}

<div class="context-block a4-page">
<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'User audit log'|i18n( 'design/admin/user/audit' )}</h1>
</div></div>
<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="a4-intro">{'Who changed, created, enabled, disabled, moved or removed which account, who signed in or was refused, and which roles were assigned: one line per event, with the fields before and after. Passwords and secrets are never recorded.'|i18n( 'design/admin/user/audit' )}</p>

{if $audit_supported|not}
<div class="a4-notice is-warn" role="status">{'This page needs an SQL database; it is not available on MongoDB. The audit files keep every event.'|i18n( 'design/admin/user/audit' )}</div>

{elseif $audit_installed|not}
<div class="a4-notice is-warn" role="status">{'The table of the user audit log is missing. Create it with php update/common/scripts/6.0/createuseraudittable.php; until then the audit files keep every event.'|i18n( 'design/admin/user/audit' )}</div>

{else}
{if $audit_enabled|not}
<div class="a4-notice is-info" role="status">{'Recording is switched off in audit.ini ([UserAuditSettings] UserAudit); the list shows what was recorded before.'|i18n( 'design/admin/user/audit' )}</div>
{/if}

<section aria-labelledby="audit-find-title">
<h2 class="a4-sr-only" id="audit-find-title">{'Narrow the list'|i18n( 'design/admin/user/audit' )}</h2>
<form class="a4-filterbar" method="get" action={$uri|ezurl} role="search">
    <div class="a4-field">
        <label for="audit-user">{'User'|i18n( 'design/admin/user/audit' )}</label>
        <input type="search" id="audit-user" name="user" value="{$audit_filters.user|wash}" autocomplete="off" maxlength="150" aria-describedby="audit-user-help" />
        <span class="a4-help" id="audit-user-help">{'A login or a user id: the account the event was about or the user who acted.'|i18n( 'design/admin/user/audit' )}</span>
    </div>
    <div class="a4-field">
        <label for="audit-action">{'Action'|i18n( 'design/admin/user/audit' )}</label>
        <select id="audit-action" name="action">
            <option value="">{'Every action'|i18n( 'design/admin/user/audit' )}</option>
            {foreach $audit_event_names as $name}
            <option value="{$name|wash}"{if eq( $name, $audit_filters.action )} selected="selected"{/if}>{$name|wash}</option>
            {/foreach}
            {if and( $audit_filters.action|ne( '' ), $audit_event_names|contains( $audit_filters.action )|not )}
            <option value="{$audit_filters.action|wash}" selected="selected">{$audit_filters.action|wash}.*</option>
            {/if}
        </select>
    </div>
    <div class="a4-field">
        <label for="audit-result">{'Result'|i18n( 'design/admin/user/audit' )}</label>
        <select id="audit-result" name="result">
            <option value="">{'Any result'|i18n( 'design/admin/user/audit' )}</option>
            {foreach $result_names as $key => $text}<option value="{$key}"{if eq( $key, $audit_filters.result )} selected="selected"{/if}>{$text|wash}</option>{/foreach}
        </select>
    </div>
    <div class="a4-field">
        <label for="audit-from">{'From'|i18n( 'design/admin/user/audit' )}</label>
        <input type="date" id="audit-from" name="from" value="{$audit_filters.from|wash}" />
    </div>
    <div class="a4-field">
        <label for="audit-to">{'To'|i18n( 'design/admin/user/audit' )}</label>
        <input type="date" id="audit-to" name="to" value="{$audit_filters.to|wash}" />
    </div>
    <div class="a4-field">
        <label for="audit-limit">{'Per page'|i18n( 'design/admin/user/audit' )}</label>
        <select id="audit-limit" name="limit">
            {foreach array( 25, 50, 100 ) as $size}<option value="{$size}"{if eq( $size, $audit_filters.limit )} selected="selected"{/if}>{$size}</option>{/foreach}
        </select>
    </div>
    <div class="a4-field">
        <div class="a4-actions">
            <button type="submit" class="a4-btn a4-btn-primary">{'Show'|i18n( 'design/admin/user/audit' )}</button>
            {if $audit_query|ne( '' )}<a class="a4-btn" href={$uri|ezurl}>{'Clear'|i18n( 'design/admin/user/audit' )}</a>{/if}
        </div>
    </div>
</form>
</section>

<form name="useraudit" method="post" action={$form_uri|ezurl}>
<section class="a4-section" aria-labelledby="audit-list-title">
<div class="a4-section-head">
    <h2 class="a4-h2" id="audit-list-title">{'Events'|i18n( 'design/admin/user/audit' )} ({$audit_count})</h2>
    {if $audit_count|gt( 0 )}
    <span class="a4-meta">{'%from to %to of %count'|i18n( 'design/admin/user/audit',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $audit_filters.limit ), $audit_count ), '%count', $audit_count ) )}</span>
    {/if}
</div>

{if $audit_rows}
<div class="a4-table-wrap">
<table class="a4-table" id="audit-list">
<caption class="a4-sr-only">{'Events'|i18n( 'design/admin/user/audit' )}</caption>
<thead><tr>
    <th scope="col">{'When'|i18n( 'design/admin/user/audit' )}</th>
    <th scope="col">{'Action'|i18n( 'design/admin/user/audit' )}</th>
    <th scope="col">{'Account'|i18n( 'design/admin/user/audit' )}</th>
    <th scope="col">{'By'|i18n( 'design/admin/user/audit' )}</th>
    <th scope="col">{'Where'|i18n( 'design/admin/user/audit' )}</th>
    <th scope="col">{'Before'|i18n( 'design/admin/user/audit' )}</th>
    <th scope="col">{'After'|i18n( 'design/admin/user/audit' )}</th>
</tr></thead>
<tbody>
{foreach $audit_rows as $row}
<tr>
    <td class="a4-nowrap">{$row.created|l10n( 'shortdatetime' )}</td>
    <td><code>{$row.event_name|wash}</code><br /><span class="a4-badge {first_set( $result_levels[$row.result], 'is-info' )}">{first_set( $result_names[$row.result], $row.result )|wash}</span>{if $row.reason|ne( '' )} <span class="a4-muted">{$row.reason|wash}</span>{/if}</td>
    <td>{if $row.target_id|gt( 0 )}<a href={concat( $uri, '?user=', $row.target_id )|ezurl} title="{'Every event of this account'|i18n( 'design/admin/user/audit' )}">{if $row.target_login|ne( '' )}{$row.target_login|wash}{else}#{$row.target_id}{/if}</a>{elseif $row.target_login|ne( '' )}{$row.target_login|wash}{else}<span class="a4-muted">{'unknown'|i18n( 'design/admin/user/audit' )}</span>{/if}{if eq( $row.target_type, 'group' )} <span class="a4-badge is-info">{'group'|i18n( 'design/admin/user/audit' )}</span>{/if}</td>
    <td>{if $row.actor_id|gt( 0 )}<a href={concat( $uri, '?user=', $row.actor_id )|ezurl}>{if $row.actor_login|ne( '' )}{$row.actor_login|wash}{else}#{$row.actor_id}{/if}</a>{else}<span class="a4-muted">{'system'|i18n( 'design/admin/user/audit' )}</span>{/if}</td>
    <td>{if $row.siteaccess|ne( '' )}{$row.siteaccess|wash}{/if}{if $row.host|ne( '' )}<br /><span class="a4-muted">{$row.host|wash}</span>{/if}{if $row.client_ip|ne( '' )}<br /><span class="a4-muted">{$row.client_ip|wash}</span>{/if}{if $row.module_view|ne( '' )}<br /><code>{$row.module_view|wash}</code>{/if}</td>
    <td>{if $row.before}<ul>{foreach $row.before as $field => $value}<li><code>{$field|wash}</code>: {$value|wash}</li>{/foreach}</ul>{/if}</td>
    <td>{if $row.after}<ul>{foreach $row.after as $field => $value}<li><code>{$field|wash}</code>: {$value|wash}</li>{/foreach}</ul>{/if}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
{else}
<p class="a4-empty">{'No event matches.'|i18n( 'design/admin/user/audit' )}</p>
{/if}

<div class="a4-listfoot">
    <div class="a4-pager">
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri=concat( '/', $uri )
             page_uri_suffix=$audit_query
             item_count=$audit_count
             view_parameters=$view_parameters
             item_limit=$audit_filters.limit}
    </div>
</div>
</section>

<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        <button class="a4-btn" type="submit" name="ExportButton" value="1"{if $audit_count|eq( 0 )} disabled="disabled"{/if}>{'Export the list as CSV (%count)'|i18n( 'design/admin/user/audit',, hash( '%count', $audit_count ) )}</button>
    </div>
    <p class="a4-meta">{if $audit_retention_days|gt( 0 )}{'Events are kept for %days days.'|i18n( 'design/admin/user/audit',, hash( '%days', $audit_retention_days ) )}{else}{'Events are kept until they are removed by hand.'|i18n( 'design/admin/user/audit' )}{/if}</p>
</div>
</form>
{/if}

</div></div></div>
</div>
{undef $uri $form_uri $result_levels $result_names}
