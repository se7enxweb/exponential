{* The rows of an inbox page. Variables: rows (inbox rows), user_id (the current user). *}
<ul class="cb-list">
{foreach $rows as $row}
    <li class="cb-row cb-state-{$row.state|wash}{if $row.is_unread} cb-unread{/if}">
        <a class="cb-row-main" href={concat( 'collaboration/item/full/', $row.id )|ezurl}>
            <span class="cb-title">{$row.title|wash}{if $row.unread_messages|gt( 0 )} <span class="cb-pill" title="{'Unread messages'|i18n( 'design/admin/collaboration/inbox' )|wash}">{$row.unread_messages}</span>{/if}</span>
            <span class="cb-meta">
                {switch match=$row.state}
                {case match='waiting'}
                    {if $row.is_creator}<span class="cb-badge waiting">{'Waiting for approval'|i18n( 'design/admin/collaboration/inbox' )}</span>
                    {else}<span class="cb-badge waiting">{'Needs your decision'|i18n( 'design/admin/collaboration/inbox' )}</span>{/if}
                {/case}
                {case match='approved'}<span class="cb-badge approved">{'Approved'|i18n( 'design/admin/collaboration/inbox' )}</span>{/case}
                {case match='denied'}<span class="cb-badge denied">{'Denied'|i18n( 'design/admin/collaboration/inbox' )}</span>{/case}
                {case match='closed'}<span class="cb-badge">{'Closed'|i18n( 'design/admin/collaboration/inbox' )}</span>{/case}
                {case}<span class="cb-badge">{'Open'|i18n( 'design/admin/collaboration/inbox' )}</span>{/case}
                {/switch}
                <span>{$row.type_name|wash}</span>
                {if $row.is_creator}<span>{'sent by you'|i18n( 'design/admin/collaboration/inbox' )}</span>
                {elseif $row.author_name}<span>{'from %name'|i18n( 'design/admin/collaboration/inbox',, hash( '%name', $row.author_name ) )|wash}</span>{/if}
                {if $row.group_title}<span>{$row.group_title|wash}</span>{/if}
                {if $row.message_count|eq( 1 )}<span>{'1 message'|i18n( 'design/admin/collaboration/inbox' )}</span>
                {elseif $row.message_count|gt( 1 )}<span>{'%n messages'|i18n( 'design/admin/collaboration/inbox',, hash( '%n', $row.message_count ) )}</span>{/if}
                <span>{$row.modified|l10n( 'shortdatetime' )}</span>
            </span>
            {if $row.last_message}<span class="cb-excerpt"><b>{$row.last_message.author|wash}:</b> {$row.last_message.text|wash|shorten( 140 )}</span>{/if}
        </a>
    </li>
{/foreach}
</ul>
