{* The notices of the e-mail preference pages: $notice is hash( 'type', success|warning|error|info, 'text', ... ) or false,
   $notices an array of more of them. Shown once, as a status message that a screen reader announces. *}
{if and( is_set( $notice ), $notice )}
<div class="mp-notice mp-notice-{$notice.type|wash}" role="{if $notice.type|eq( 'error' )}alert{else}status{/if}">
    <p>{$notice.text|wash}</p>
</div>
{/if}
{if and( is_set( $notices ), $notices )}
{foreach $notices as $item}
<div class="mp-notice mp-notice-{$item.type|wash}" role="{if $item.type|eq( 'error' )}alert{else}status{/if}">
    <p>{$item.text|wash}</p>
</div>
{/foreach}
{/if}
