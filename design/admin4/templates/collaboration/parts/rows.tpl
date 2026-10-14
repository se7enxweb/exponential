{* The rows of an inbox page. Variables: rows (inbox rows), user_id (the current user). *}
<ul class="a4-collaboration-list">
{foreach $rows as $row}
    <li class="a4-collaboration-row a4-collaboration-state-{$row.state|wash}{if $row.is_unread} a4-collaboration-unread{/if}">
        <a class="a4-collaboration-row-main" href={concat( 'collaboration/item/full/', $row.id )|ezurl}>
            <span class="a4-collaboration-title">{$row.title|wash}{if $row.unread_messages|gt( 0 )} <span class="a4-badge is-accent" title="{'Unread messages'|i18n( 'design/admin/collaboration/inbox' )|wash}">{$row.unread_messages}</span>{/if}</span>
            <span class="a4-collaboration-meta">
                {switch match=$row.state}
                {case match='waiting'}
                    {if $row.is_creator}<span class="a4-badge is-warn">{'Waiting for approval'|i18n( 'design/admin/collaboration/inbox' )}</span>
                    {else}<span class="a4-badge is-warn">{'Needs your decision'|i18n( 'design/admin/collaboration/inbox' )}</span>{/if}
                {/case}
                {case match='approved'}<span class="a4-badge is-ok">{'Approved'|i18n( 'design/admin/collaboration/inbox' )}</span>{/case}
                {case match='denied'}<span class="a4-badge is-bad">{'Denied'|i18n( 'design/admin/collaboration/inbox' )}</span>{/case}
                {case match='closed'}<span class="a4-badge">{'Closed'|i18n( 'design/admin/collaboration/inbox' )}</span>{/case}
                {case}<span class="a4-badge">{'Open'|i18n( 'design/admin/collaboration/inbox' )}</span>{/case}
                {/switch}
                <span>{$row.type_name|wash}</span>
                {if $row.is_creator}<span>{'sent by you'|i18n( 'design/admin/collaboration/inbox' )}</span>
                {elseif $row.author_name}<span>{'from %name'|i18n( 'design/admin/collaboration/inbox',, hash( '%name', $row.author_name ) )|wash}</span>{/if}
                {if $row.group_title}<span>{$row.group_title|wash}</span>{/if}
                {if $row.message_count|eq( 1 )}<span>{'1 message'|i18n( 'design/admin/collaboration/inbox' )}</span>
                {elseif $row.message_count|gt( 1 )}<span>{'%n messages'|i18n( 'design/admin/collaboration/inbox',, hash( '%n', $row.message_count ) )}</span>{/if}
                <span>{$row.modified|l10n( 'shortdatetime' )}</span>
            </span>
            {if $row.last_message}<span class="a4-collaboration-excerpt"><b>{$row.last_message.author|wash}:</b> {$row.last_message.text|wash|shorten( 140 )}</span>{/if}
        </a>
    </li>
{/foreach}
</ul>
