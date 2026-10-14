{* An inline notice: $notice is array( 'type' => success|warning|error, 'text' => ... ) or false. It is shown once. *}
{if and( is_set( $notice ), $notice )}
<div class="a4-notice {if eq( $notice.type, 'success' )}is-ok{elseif eq( $notice.type, 'warning' )}is-warn{elseif eq( $notice.type, 'error' )}is-bad{else}is-info{/if}" role="status">
    <p>{$notice.text|wash}</p>
    <button type="button" aria-label="{'Dismiss'|i18n( 'design/admin/collaboration/inbox' )}" onclick="this.parentNode.hidden=true">&times;</button>
</div>
{/if}
