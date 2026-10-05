{* One message of an approval thread (admin4): an avatar with the author's initial and a bubble. $item is the message,
   $item_link its link (with the participant), $is_read whether the current user has read it. *}
{def $author = $item_link.participant
     $author_object = fetch( 'content', 'object', hash( 'object_id', $item_link.participant_id ) )
     $author_name = cond( $author_object, $author_object.name, '' )
     $me = fetch( 'user', 'current_user' )
     $is_new = and( $is_read|not, ne( $item_link.participant_id, $me.contentobject_id ) )}
<li class="cb-msg{if $is_new} new{/if}">
    <span class="cb-avatar" aria-hidden="true">{$author_name|wash|extract_left( 1 )}</span>
    <div class="cb-bubble">
        <div class="cb-msg-head"><b>{collaboration_participation_view view=text_linked collaboration_participant=$item_link.participant}</b>
            <span>{$item.created|l10n( 'shortdatetime' )}</span>{if $is_new} <span class="cb-badge waiting">{'New'|i18n( 'design/admin/collaboration/inbox' )}</span>{/if}</div>
        <p>{$item.data_text1|wash|break}</p>
    </div>
</li>
