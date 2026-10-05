{* The approval item (admin4): header with state and facts, the decision with Approve / Deny / Comment (each asks
   to confirm), the content preview, the participants and the message thread. Variables: collab_item, notice.
   The forms are POST forms: the form token filter adds the token. The action goes to collaboration/action, as before. *}
{def $row = fetch( 'collaboration', 'inbox_row', hash( 'item_id', $collab_item.id ) )
     $content_version = fetch( 'content', 'version', hash( 'object_id', $collab_item.content.content_object_id, 'version_id', $collab_item.content.content_object_version ) )
     $current_participant = fetch( 'collaboration', 'participant', hash( 'item_id', $collab_item.id ) )
     $participant_list = fetch( 'collaboration', 'participant_map', hash( 'item_id', $collab_item.id ) )
     $message_list = fetch( 'collaboration', 'message_list', hash( 'item_id', $collab_item.id, 'limit', 200, 'offset', 0 ) )
     $group_tree = fetch( 'collaboration', 'group_tree', hash( 'parent_group_id', 0 ) )
     $title = $row.title
     $contentobject_link = concat( '<strong>', $row.title|wash, '</strong>' )
     $waiting = eq( $collab_item.data_int3, 0 )
     $item_url = concat( 'collaboration/item/full/', $collab_item.id )}
{include uri='design:collaboration/parts/style.tpl'}

<div class="cb" id="exp-collab">

    <div class="cb-head">
        <div>
            <p class="cb-crumb"><a href={'collaboration/view/summary'|ezurl}>{'Collaboration'|i18n( 'design/admin/collaboration/inbox' )}</a> / {$row.type_name|wash}</p>
            <h1>{$title|wash}</h1>
        </div>
        <span class="cb-badge {$row.state|wash}">
            {switch match=$row.state}
            {case match='approved'}{'Approved'|i18n( 'design/admin/collaboration/inbox' )}{/case}
            {case match='denied'}{'Denied'|i18n( 'design/admin/collaboration/inbox' )}{/case}
            {case}{'Waiting for approval'|i18n( 'design/admin/collaboration/inbox' )}{/case}
            {/switch}
        </span>
    </div>

    {include uri='design:collaboration/parts/notice.tpl' notice=first_set( $notice, false() )}

    <div class="cb-card">
        <h2>{'Approval'|i18n( 'design/admin/collaboration/handler/view/full/ezapprove' )}</h2>

        <dl class="cb-facts">
            <div><dt>{'Sent by'|i18n( 'design/admin/collaboration/inbox' )}</dt><dd>{$row.author_name|wash}</dd></div>
            <div><dt>{'Sent'|i18n( 'design/admin/collaboration/inbox' )}</dt><dd>{$collab_item.created|l10n( 'shortdatetime' )}</dd></div>
            <div><dt>{'Last activity'|i18n( 'design/admin/collaboration/inbox' )}</dt><dd>{$collab_item.modified|l10n( 'shortdatetime' )}</dd></div>
            <div><dt>{'Group'|i18n( 'design/admin/collaboration/inbox' )}</dt><dd>{if $row.group_title}<a href={concat( 'collaboration/group/list/', $row.group_id )|ezurl}>{$row.group_title|wash}</a>{else}-{/if}</dd></div>
        </dl>

        <form class="cb-form" method="post" action={$item_url|ezurl}>
            <label for="cb-move">{'Move to group'|i18n( 'design/admin/collaboration/inbox' )}</label>
            <select id="cb-move" name="CollaborationGroupID">
{foreach $group_tree as $group}
                <option value="{$group.id}"{if eq( $group.id, $row.group_id )} selected="selected"{/if}>{cond( $group.depth|gt( 0 ), '- ', '' )}{$group.title|wash}</option>
{/foreach}
            </select>
            <input class="cb-btn" type="submit" name="CollaborationMoveItem" value="{'Move'|i18n( 'design/admin/collaboration/inbox' )}" />
        </form>
    </div>

    <div class="cb-card">
        <h2>{'Decision'|i18n( 'design/admin/collaboration/inbox' )}</h2>
{switch match=$collab_item.data_int3}
{case match=0}
    {if $collab_item.is_creator}
        <p>{"The content object %1 awaits approval before it can be published."|i18n( 'design/admin/collaboration/handler/view/full/ezapprove',, array( $contentobject_link ) )}</p>
        <p class="cb-hint">{"Do you want to send a message to the person approving it?"|i18n( 'design/admin/collaboration/handler/view/full/ezapprove' )}</p>
    {else}
        <p>{"The content object %1 needs your approval before it can be published."|i18n( 'design/admin/collaboration/handler/view/full/ezapprove',, array( $contentobject_link ) )}</p>
        <p class="cb-hint">{"Do you approve of the content object being published?"|i18n( 'design/admin/collaboration/handler/view/full/ezapprove' )}</p>
    {/if}
{/case}
{case match=1}
        <p>{"The content object %1 was approved and will be published when the publishing workflow continues."|i18n( 'design/admin/collaboration/handler/view/full/ezapprove',, array( $contentobject_link ) )}</p>
{/case}
{case in=array( 2, 3 )}
    {if $collab_item.is_creator}
        <p>{"The content object %1 was not accepted but is still available as a draft."|i18n( 'design/admin/collaboration/handler/view/full/ezapprove',, array( $contentobject_link ) )}</p>
        <p class="cb-hint">{"You may edit the draft and publish it, in which case an approval is required again."|i18n( 'design/admin/collaboration/handler/view/full/ezapprove' )}</p>
        {if $content_version|null()|not()}
        <p><a class="cb-btn" href={concat( 'content/edit/', $content_version.contentobject_id, '/', $content_version.version )|ezurl}>{"Edit the object"|i18n( 'design/admin/collaboration/handler/view/full/ezapprove' )}</a></p>
        {/if}
    {else}
        <p>{"The content object %1 was not accepted but will be available as a draft for the author."|i18n( 'design/admin/collaboration/handler/view/full/ezapprove',, array( $contentobject_link ) )}</p>
        <p class="cb-hint">{"The author can edit the draft and publish it again, in which case a new approval is required."|i18n( 'design/admin/collaboration/handler/view/full/ezapprove' )}</p>
    {/if}
{/case}
{case/}
{/switch}

{if $waiting}
        <form class="cb-compose" method="post" action={'collaboration/action/'|ezurl}>
            <label for="Collaboration_ApproveComment">{"Comment"|i18n( 'design/admin/collaboration/handler/view/full/ezapprove' )}</label>
            <textarea id="Collaboration_ApproveComment" name="Collaboration_ApproveComment" rows="4"></textarea>
            <input type="hidden" name="CollaborationActionCustom" value="custom" />
            <input type="hidden" name="CollaborationTypeIdentifier" value="ezapprove" />
            <input type="hidden" name="CollaborationItemID" value="{$collab_item.id}" />
            <div class="cb-actions">
                <input class="cb-btn" type="submit" name="CollaborationAction_Comment" value="{'Add Comment'|i18n( 'design/admin/collaboration/handler/view/full/ezapprove' )}" />
    {if $collab_item.is_creator|not}
                <input class="cb-btn approve" type="submit" name="CollaborationAction_Accept" value="{'Approve'|i18n( 'design/admin/collaboration/handler/view/full/ezapprove' )}"
                       data-cb-confirm="{'Approve "%title"? It is published when the publishing workflow continues.'|i18n( 'design/admin/collaboration/inbox',, hash( '%title', $title ) )|wash}" />
                <input class="cb-btn deny" type="submit" name="CollaborationAction_Deny" value="{'Deny'|i18n( 'design/admin/collaboration/handler/view/full/ezapprove' )}"
                       data-cb-confirm="{'Deny "%title"? It becomes a draft for the author again.'|i18n( 'design/admin/collaboration/inbox',, hash( '%title', $title ) )|wash}" />
    {/if}
            </div>
    {if $collab_item.is_creator}
            <p class="cb-hint">{'Only an approver can approve or deny this item.'|i18n( 'design/admin/collaboration/inbox' )}</p>
    {/if}
        </form>
{else}
        <p class="cb-hint">{'This approval is closed: comments can no longer be added.'|i18n( 'design/admin/collaboration/inbox' )}</p>
{/if}
    </div>

{if $content_version|null()|not()}
    <details class="cb-card" open="open">
        <summary><h2>{'Preview'|i18n( 'design/admin/collaboration/handler/view/full/ezapprove' )}</h2></summary>
        <p class="cb-hint">{'Version %v of the content, saved %time by %name'|i18n( 'design/admin/collaboration/inbox',, hash( '%v', $content_version.version, '%time', $content_version.modified|l10n( 'shortdatetime' ), '%name', $content_version.creator.name ) )|wash}
            {if $content_version.contentobject.main_node_id} - <a href={concat( 'content/view/full/', $content_version.contentobject.main_node_id )|ezurl}>{'Open the content'|i18n( 'design/admin/collaboration/inbox' )}</a>{/if}</p>
        <div class="cb-preview" title="{$content_version.contentobject.name|wash} {'Object ID'|i18n( 'design/admin/node/view/full' )}: {$content_version.contentobject_id}">
            <div class="mainobject-window">{content_version_view_gui view=plain content_version=$content_version}</div>
        </div>
    </details>
{/if}

    <div class="cb-card">
        <h2>{'Participants'|i18n( 'design/admin/collaboration/handler/view/full/ezapprove' )}</h2>
        <ul class="cb-people">
{foreach $participant_list as $role}
    {foreach $role.items as $participant}
            <li>{collaboration_participation_view view=text_linked collaboration_participant=$participant} <small>{$role.name|wash}</small></li>
    {/foreach}
{/foreach}
        </ul>
    </div>

    <div class="cb-card">
        <h2 id="messages">{"Messages"|i18n( 'design/admin/collaboration/handler/view/full/ezapprove' )} <small>({$message_list|count})</small></h2>
{if $message_list|count|gt( 0 )}
        <ul class="cb-thread">
    {foreach $message_list as $link}
        {collaboration_simple_message_view view=element sequence='' is_read=$current_participant.last_read|gt( $link.modified ) item_link=$link collaboration_message=$link.simple_message}
    {/foreach}
        </ul>
{else}
        <p class="cb-hint">{'There are no messages yet.'|i18n( 'design/admin/collaboration/inbox' )}</p>
{/if}
    </div>

</div>
<script type="text/javascript">
{literal}
(function () {
    var buttons = document.querySelectorAll('#exp-collab [data-cb-confirm]');
    for (var i = 0; i < buttons.length; i++) {
        buttons[i].addEventListener('click', function (e) { if (!window.confirm(this.getAttribute('data-cb-confirm'))) e.preventDefault(); });
    }
})();
{/literal}
</script>
