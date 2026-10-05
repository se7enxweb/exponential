{* The collaboration summary as an inbox (admin4). Variables: view_parameters (offset, status, role, type, group), notice.
   Old URLs keep working: /collaboration/view/summary and /collaboration/view/summary/(offset)/10.
   The filters are view parameters: /collaboration/view/summary/(status)/waiting/(role)/approver/(type)/ezapprove *}
{def $limit = 15
     $base = 'collaboration/view/summary'
     $ps = first_set( $view_parameters.status, 'all' )
     $pr = first_set( $view_parameters.role, 'all' )
     $pt = first_set( $view_parameters.type, 'all' )
     $inbox = fetch( 'collaboration', 'inbox', hash( 'status', $ps, 'role', $pr, 'type', $pt,
                                                    'offset', first_set( $view_parameters.offset, 0 ), 'limit', $limit ) )
     $counts = $inbox.counts
     $group_tree = fetch( 'collaboration', 'group_tree', hash( 'parent_group_id', 0 ) )}
{include uri='design:collaboration/parts/style.tpl'}

<div class="cb" id="exp-collab">

    <div class="cb-head">
        <div>
            <h1>{'Collaboration'|i18n( 'design/admin/collaboration/inbox' )}</h1>
            <p>{'Content that waits for a decision, your own submissions and the conversations about them.'|i18n( 'design/admin/collaboration/inbox' )}</p>
        </div>
    </div>

    {include uri='design:collaboration/parts/notice.tpl' notice=first_set( $notice, false() )}

    <ul class="cb-stats">
        <li><a class="cb-stat{if $counts.waiting_for_me|gt( 0 )} hot{/if}{if and( $ps|eq( 'waiting' ), $pr|eq( 'approver' ) )} current{/if}" href={concat( $base, '/(status)/waiting/(role)/approver' )|ezurl}><strong>{$counts.waiting_for_me}</strong><span>{'Waiting for your decision'|i18n( 'design/admin/collaboration/inbox' )}</span></a></li>
        <li><a class="cb-stat{if and( $ps|eq( 'waiting' ), $pr|eq( 'author' ) )} current{/if}" href={concat( $base, '/(status)/waiting/(role)/author' )|ezurl}><strong>{$counts.waiting_for_others}</strong><span>{'Your items waiting for others'|i18n( 'design/admin/collaboration/inbox' )}</span></a></li>
        <li><a class="cb-stat{if $ps|eq( 'approved' )} current{/if}" href={concat( $base, '/(status)/approved' )|ezurl}><strong>{$counts.approved}</strong><span>{'Approved'|i18n( 'design/admin/collaboration/inbox' )}</span></a></li>
        <li><a class="cb-stat{if $ps|eq( 'denied' )} current{/if}" href={concat( $base, '/(status)/denied' )|ezurl}><strong>{$counts.denied}</strong><span>{'Denied'|i18n( 'design/admin/collaboration/inbox' )}</span></a></li>
        <li><div class="cb-stat{if $counts.unread_messages|gt( 0 )} hot{/if}"><strong>{$counts.unread_messages}</strong><span>{'Unread messages'|i18n( 'design/admin/collaboration/inbox' )}</span></div></li>
    </ul>

{if $counts.all|eq( 0 )}

    <div class="cb-empty">
        <h2>{'Nothing to handle yet'|i18n( 'design/admin/collaboration/inbox' )}</h2>
        <p>{'Collaboration items are created by the system, not by hand: content that is sent for approval appears here, with the conversation about it.'|i18n( 'design/admin/collaboration/inbox' )}</p>
        <ol>
            <li>{'Create a workflow with an Approve event: choose the approvers and, if needed, the sections and user groups it applies to.'|i18n( 'design/admin/collaboration/inbox' )} <a href={'workflow/grouplist'|ezurl}>{'Workflows'|i18n( 'design/admin/collaboration/inbox' )}</a></li>
            <li>{'Attach the workflow to the trigger content / publish / before.'|i18n( 'design/admin/collaboration/inbox' )} <a href={'trigger/list'|ezurl}>{'Triggers'|i18n( 'design/admin/collaboration/inbox' )}</a></li>
            <li>{'When an editor publishes content the workflow applies to, an item is created for the approvers and appears in their inbox.'|i18n( 'design/admin/collaboration/inbox' )}</li>
        </ol>
        <p>{'To see the tool with example content, run:'|i18n( 'design/admin/collaboration/inbox' )} <code>./console exp:collaboration:sample-data</code></p>
    </div>

{else}

    <div class="cb-filters">
        <div class="cb-filter"><b>{'Status'|i18n( 'design/admin/collaboration/inbox' )}</b>
            <a class="cb-chip{if $ps|eq( 'all' )} current{/if}" href={concat( $base, '/(role)/', $pr, '/(type)/', $pt )|ezurl}>{'All'|i18n( 'design/admin/collaboration/inbox' )} <small>{$counts.all}</small></a>
            <a class="cb-chip{if $ps|eq( 'waiting' )} current{/if}" href={concat( $base, '/(status)/waiting/(role)/', $pr, '/(type)/', $pt )|ezurl}>{'Waiting'|i18n( 'design/admin/collaboration/inbox' )} <small>{$counts.waiting}</small></a>
            <a class="cb-chip{if $ps|eq( 'approved' )} current{/if}" href={concat( $base, '/(status)/approved/(role)/', $pr, '/(type)/', $pt )|ezurl}>{'Approved'|i18n( 'design/admin/collaboration/inbox' )} <small>{$counts.approved}</small></a>
            <a class="cb-chip{if $ps|eq( 'denied' )} current{/if}" href={concat( $base, '/(status)/denied/(role)/', $pr, '/(type)/', $pt )|ezurl}>{'Denied'|i18n( 'design/admin/collaboration/inbox' )} <small>{$counts.denied}</small></a>
        </div>
        <div class="cb-filter"><b>{'Your role'|i18n( 'design/admin/collaboration/inbox' )}</b>
            <a class="cb-chip{if $pr|eq( 'all' )} current{/if}" href={concat( $base, '/(status)/', $ps, '/(type)/', $pt )|ezurl}>{'Any'|i18n( 'design/admin/collaboration/inbox' )}</a>
            <a class="cb-chip{if $pr|eq( 'approver' )} current{/if}" href={concat( $base, '/(status)/', $ps, '/(role)/approver/(type)/', $pt )|ezurl}>{'I decide'|i18n( 'design/admin/collaboration/inbox' )}</a>
            <a class="cb-chip{if $pr|eq( 'author' )} current{/if}" href={concat( $base, '/(status)/', $ps, '/(role)/author/(type)/', $pt )|ezurl}>{'I sent it'|i18n( 'design/admin/collaboration/inbox' )}</a>
        </div>
{if $inbox.types|count|gt( 1 )}
        <div class="cb-filter"><b>{'Type'|i18n( 'design/admin/collaboration/inbox' )}</b>
            <a class="cb-chip{if $pt|eq( 'all' )} current{/if}" href={concat( $base, '/(status)/', $ps, '/(role)/', $pr )|ezurl}>{'All'|i18n( 'design/admin/collaboration/inbox' )}</a>
    {foreach $inbox.types as $type_id => $type_name}
            <a class="cb-chip{if $pt|eq( $type_id )} current{/if}" href={concat( $base, '/(status)/', $ps, '/(role)/', $pr, '/(type)/', $type_id )|ezurl}>{$type_name|wash}</a>
    {/foreach}
        </div>
{/if}
    </div>

    <div class="cb-layout has-side">
        <div class="cb-main">
{if $inbox.items|count|gt( 0 )}
            {include uri='design:collaboration/parts/rows.tpl' rows=$inbox.items user_id=$inbox.user_id}
            {include name=Navigator uri='design:navigator/google.tpl' page_uri=concat( '/', $base ) item_count=$inbox.total
                     view_parameters=$view_parameters item_limit=$limit}
{else}
            <div class="cb-empty">
                <h2>{'No items match these filters'|i18n( 'design/admin/collaboration/inbox' )}</h2>
                <p><a href={$base|ezurl}>{'Show all items'|i18n( 'design/admin/collaboration/inbox' )}</a></p>
            </div>
{/if}
        </div>

        <div class="cb-side">
            <div class="cb-card">
                <h2>{'Groups'|i18n( 'design/admin/collaboration/inbox' )}</h2>
                <ul class="cb-tree">
{foreach $group_tree as $group}
                    <li style="padding-left: {mul( sub( $group.depth, 0 ), 0.9 )}em"><a href={concat( 'collaboration/group/list/', $group.id )|ezurl}><span>{$group.title|wash}</span><span class="count">{$group.item_count}</span></a></li>
{/foreach}
                </ul>
                <form class="cb-form" method="post" action={$base|ezurl}>
                    <input type="text" name="CollaborationGroupTitle" maxlength="255" placeholder="{'New group'|i18n( 'design/admin/collaboration/inbox' )|wash}" aria-label="{'New group'|i18n( 'design/admin/collaboration/inbox' )|wash}" />
                    <input class="cb-btn" type="submit" name="CollaborationGroupCreate" value="{'Add'|i18n( 'design/admin/collaboration/inbox' )}" />
                </form>
            </div>
        </div>
    </div>

{/if}

</div>
