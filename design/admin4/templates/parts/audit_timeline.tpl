{* A list of audit events (fetch audit/events, newest first) as a timeline (.a4-timeline, components.css): the
   time (local, the UTC instant in datetime), the event linking to audit/event, the actor and the result. The state
   dot repeats the result badge's word. Parameters: events, show_reason (optional, default false). *}
{if is_set( $show_reason )|not}{def $show_reason = false()}{/if}
<ol class="a4-timeline" role="list">
{foreach $events as $e}
{def $audit_state = cond( eq( $e.result, 'success' ), 'is-ok', eq( $e.result, 'failed' ), 'is-bad', eq( $e.result, 'refused' ), 'is-warn', '' )}
    <li{if $audit_state} class="{$audit_state}"{/if}>
        <time class="a4-timeline-time" datetime="{$e.time_utc|wash}" title="{$e.time_utc|wash}">{$e.time|wash}</time>
        <div class="a4-timeline-body">
            <a href={concat( 'audit/event/', $e.id )|ezurl} title="{$e.name|wash}">{$e.label|wash}</a>
            <span class="a4-timeline-meta"><span class="a4-sr-only">{'Actor'|i18n( 'design/admin/audit' )}: </span>{$e.actor|wash}</span>
            <span class="a4-badge{if $audit_state} {$audit_state}{/if}"><span class="a4-sr-only">{'Result'|i18n( 'design/admin/audit' )}: </span>{cond( eq( $e.result, 'success' ), 'success'|i18n( 'design/admin/audit' ), eq( $e.result, 'failed' ), 'failed'|i18n( 'design/admin/audit' ), eq( $e.result, 'refused' ), 'refused'|i18n( 'design/admin/audit' ), $e.result )|wash}</span>
            {if and( $show_reason, $e.reason )}<span class="a4-timeline-meta">({$e.reason|wash})</span>{/if}
        </div>
    </li>
{undef $audit_state}
{/foreach}
</ol>
