{* An inline notice: $notice is array( 'type' => success|warning|error|info, 'text' => ... ) or false. It is shown once. *}
{if and( is_set( $notice ), $notice )}
<div class="nf-notice nf-notice-{$notice.type|wash}" role="status">
    <p>{$notice.text|wash}</p>
    <button type="button" aria-label="{'Dismiss'|i18n( 'design/admin/notification/settings' )}" onclick="this.parentNode.hidden=true">&times;</button>
</div>
{/if}
