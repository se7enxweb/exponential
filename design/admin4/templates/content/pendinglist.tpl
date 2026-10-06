{* The pending page of the current user (content/pendinglist, "My pending items").

   What a pending version is, figures (yours, waiting for your approval, held by an approval), filters by list and
   class, the order, then one card per version: its name, class, location (or "new object"), translation, version,
   who sent it and when, what holds it (the approval with its state and approvers, the workflows), and links to the
   version, the approval (when you take part in it) and the object. A version waiting for your approval is shown only
   when you may read it (content/versionread).

   The same file is in design/admin and design/admin4; the look is content/history_exp_style.tpl (.exp-history) and
   content/draft_exp_style.tpl. The list comes from the view (pending_rows, pending_overview, pending_filters ...),
   worked out by expContentPendingList. Guide: doc/guides/drafts-and-pending.md *}
{include uri='design:content/history_exp_style.tpl'}
{include uri='design:content/draft_exp_style.tpl'}

{def $rows = first_set( $pending_rows, array() )
     $overview = first_set( $pending_overview, hash( 'mine', 0, 'approve', 0, 'held_by_approval', 0, 'classes', hash() ) )
     $filters = first_set( $pending_filters, hash( 'scope', 'all', 'class', '', 'sort', 'newest' ) )
     $links = first_set( $pending_links, hash( 'scope', hash(), 'class', hash( 'all', '' ), 'sort', hash() ) )
     $suffix = first_set( $pending_suffix, '' )
     $count = first_set( $pending_count, 0 )
     $page_limit = first_set( $pending_limit, 25 )
     $total = sum( $overview.mine, $overview.approve )
     $base = '/content/pendinglist'
     $state_text = hash( 'waiting', 'Waiting for approval'|i18n( 'design/admin/content/pendinglist' ),
                         'accepted', 'Approved'|i18n( 'design/admin/content/pendinglist' ),
                         'denied', 'Denied'|i18n( 'design/admin/content/pendinglist' ),
                         'deferred', 'Sent back for changes'|i18n( 'design/admin/content/pendinglist' ) )
     $state_class = hash( 'waiting', 'is-warn', 'accepted', 'is-ok', 'denied', 'is-bad', 'deferred', 'is-info' )}

<div class="context-block exp-history exp-drafts exp-pending">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'My pending items (%pending_count)'|i18n( 'design/admin/content/pendinglist',, hash( '%pending_count', $overview.mine ) )}</h1>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'A version is pending when you have sent it for publishing and a workflow holds it, usually an approval: it is published when it is approved. Here are your pending versions and, if you approve content, the versions waiting for your approval that you may read. Open the approval to read the comments or to approve.'|i18n( 'design/admin/content/pendinglist' )}</p>

{if $total|gt( 0 )}
<section aria-labelledby="pending-overview-title">
<h2 class="exp-sr" id="pending-overview-title">{'Overview'|i18n( 'design/admin/content/pendinglist' )}</h2>
<ul class="exp-figures">
    <li class="exp-figure{if eq( $filters.scope, 'mine' )} is-current{/if}"><a href={concat( $base, $links.scope.mine )|ezurl}><strong>{$overview.mine}</strong> <span>{'Sent by you'|i18n( 'design/admin/content/pendinglist' )}</span></a></li>
    <li class="exp-figure{if $overview.approve|gt( 0 )} is-attention{/if}{if eq( $filters.scope, 'approve' )} is-current{/if}"><a href={concat( $base, $links.scope.approve )|ezurl}><strong>{$overview.approve}</strong> <span>{'Waiting for your approval'|i18n( 'design/admin/content/pendinglist' )}</span></a></li>
    <li class="exp-figure"><strong>{$overview.held_by_approval}</strong> <span>{'Held by an approval'|i18n( 'design/admin/content/pendinglist' )}</span></li>
</ul>
</section>

<section aria-labelledby="pending-find-title">
<h2 class="exp-sr" id="pending-find-title">{'Filter and order'|i18n( 'design/admin/content/pendinglist' )}</h2>
<div class="exp-toolbar">
    <div class="exp-field">
        <span class="exp-field-label" id="pending-scope-label"><strong>{'Show'|i18n( 'design/admin/content/pendinglist' )}</strong></span>
        <ul class="exp-tabs" aria-labelledby="pending-scope-label">
        {foreach array( hash( 'scope', 'all', 'text', 'All'|i18n( 'design/admin/content/pendinglist' ) ),
                        hash( 'scope', 'mine', 'text', 'Sent by you'|i18n( 'design/admin/content/pendinglist' ) ),
                        hash( 'scope', 'approve', 'text', 'Waiting for your approval'|i18n( 'design/admin/content/pendinglist' ) ) ) as $tab}
            <li>{if eq( $tab.scope, $filters.scope )}<span class="current" aria-current="true">{$tab.text|wash}</span>{else}<a href={concat( $base, $links.scope[$tab.scope] )|ezurl}>{$tab.text|wash}</a>{/if}</li>
        {/foreach}
        </ul>
    </div>
    {if or( $overview.classes|count|gt( 1 ), $filters.class|ne( '' ) )}
    <div class="exp-field">
        <span class="exp-field-label" id="pending-class-label"><strong>{'Class'|i18n( 'design/admin/content/pendinglist' )}</strong></span>
        <ul class="exp-tabs" aria-labelledby="pending-class-label">
            <li>{if eq( $filters.class, '' )}<span class="current" aria-current="true">{'All'|i18n( 'design/admin/content/pendinglist' )}</span>{else}<a href={concat( $base, $links.class.all )|ezurl}>{'All'|i18n( 'design/admin/content/pendinglist' )}</a>{/if}</li>
        {foreach $overview.classes as $identifier => $class_name}
            <li>{if eq( $filters.class, $identifier )}<span class="current" aria-current="true">{$class_name|wash}</span>{else}<a href={concat( $base, $links.class[$identifier] )|ezurl}>{$class_name|wash}</a>{/if}</li>
        {/foreach}
        </ul>
    </div>
    {/if}
    <div class="exp-field">
        <span class="exp-field-label" id="pending-sort-label"><strong>{'Order'|i18n( 'design/admin/content/pendinglist' )}</strong></span>
        <ul class="exp-tabs" aria-labelledby="pending-sort-label">
        {foreach array( hash( 'sort', 'newest', 'text', 'Newest first'|i18n( 'design/admin/content/pendinglist' ) ),
                        hash( 'sort', 'oldest', 'text', 'Waiting longest'|i18n( 'design/admin/content/pendinglist' ) ),
                        hash( 'sort', 'name', 'text', 'Name'|i18n( 'design/admin/content/pendinglist' ) ) ) as $tab}
            <li>{if eq( $tab.sort, $filters.sort )}<span class="current" aria-current="true">{$tab.text|wash}</span>{else}<a href={concat( $base, $links.sort[$tab.sort] )|ezurl}>{$tab.text|wash}</a>{/if}</li>
        {/foreach}
        </ul>
    </div>
</div>
</section>
{/if}

<section class="exp-section" aria-labelledby="pending-list-title">
<div class="exp-section-head">
    <h2 class="exp-h2" id="pending-list-title">{'Pending versions'|i18n( 'design/admin/content/pendinglist' )}</h2>
    {if $count|gt( 0 )}
    <span class="exp-meta">{'%from to %to of %count'|i18n( 'design/admin/content/pendinglist',, hash( '%from', sum( $view_parameters.offset, 1 ), '%to', min( sum( $view_parameters.offset, $page_limit ), $count ), '%count', $count ) )}</span>
    {/if}
</div>

{if $rows|count|eq( 0 )}
<p class="exp-empty">
{if $total|eq( 0 )}
    {'The pending list is empty.'|i18n( 'design/admin/content/pendinglist' )} {'A version appears here when you send it for publishing and a workflow, such as an approval, holds it.'|i18n( 'design/admin/content/pendinglist' )}
{else}
    {'No pending version matches these filters.'|i18n( 'design/admin/content/pendinglist' )} <a href={$base|ezurl}>{'Show all'|i18n( 'design/admin/content/pendinglist' )}</a>
{/if}
</p>
{else}
<ul class="exp-cards" id="pending-list">
{foreach $rows as $row}
    {def $card_id = concat( 'pending-', $row.id )}
<li class="exp-card{if $row.approver} is-attention{/if}" id="{$card_id}">
    <div class="exp-card-head">
        <div class="exp-card-title">
            <h3 id="{$card_id}-title">{$row.class_identifier|class_icon( small, $row.class_name|wash )} {if $row.can_versionread}<a href={concat( '/content/versionview/', $row.object_id, '/', $row.version, '/', $row.language, '/' )|ezurl}>{$row.name|wash}</a>{else}{$row.name|wash}{/if}</h3>
            <ul class="exp-badges">
                {if $row.approval_id}<li class="exp-badge {$state_class[$row.approval_state_name]}">{$state_text[$row.approval_state_name]|wash}</li>{else}<li class="exp-badge is-warn">{'Pending'|i18n( 'design/admin/content/pendinglist' )}</li>{/if}
                {if $row.mine}<li class="exp-badge">{'Sent by you'|i18n( 'design/admin/content/pendinglist' )}</li>{/if}
                {if $row.approver}<li class="exp-badge is-info">{'You approve it'|i18n( 'design/admin/content/pendinglist' )}</li>{/if}
                {if $row.is_new}<li class="exp-badge is-info">{'New object'|i18n( 'design/admin/content/pendinglist' )}</li>{/if}
            </ul>
        </div>
        <div class="exp-actions">
            {if $row.can_versionread}<a class="exp-btn exp-btn-small" href={concat( '/content/versionview/', $row.object_id, '/', $row.version, '/', $row.language, '/' )|ezurl} aria-describedby="{$card_id}-title">{'View'|i18n( 'design/admin/content/pendinglist' )}</a>{/if}
            {if and( $row.approval_id, $row.approval_link )}<a class="exp-btn exp-btn-small{if $row.approver} exp-btn-primary{/if}" href={concat( '/collaboration/item/full/', $row.approval_id )|ezurl} aria-describedby="{$card_id}-title">{if $row.approver}{'Open the approval'|i18n( 'design/admin/content/pendinglist' )}{else}{'Approval and comments'|i18n( 'design/admin/content/pendinglist' )}{/if}</a>{/if}
            {if $row.node_id|gt( 0 )}<a class="exp-btn exp-btn-small" href={concat( '/content/view/full/', $row.node_id )|ezurl} aria-describedby="{$card_id}-title">{'Published page'|i18n( 'design/admin/content/pendinglist' )}</a>{/if}
        </div>
    </div>
    <dl class="exp-facts">
        <div>
            <dt>{'Class'|i18n( 'design/admin/content/pendinglist' )}</dt>
            <dd>{$row.class_name|wash}</dd>
        </div>
        <div>
            <dt>{'Location'|i18n( 'design/admin/content/pendinglist' )}</dt>
            <dd>{if $row.is_new}{if $row.location|ne( '' )}{'New, to be published below %location'|i18n( 'design/admin/content/pendinglist',, hash( '%location', $row.location ) )|wash}{else}{'New object'|i18n( 'design/admin/content/pendinglist' )}{/if}{elseif $row.location|ne( '' )}{'Below %location'|i18n( 'design/admin/content/pendinglist',, hash( '%location', $row.location ) )|wash}{else}{'Unknown'|i18n( 'design/admin/content/pendinglist' )}{/if}</dd>
        </div>
        <div>
            <dt>{'Translation'|i18n( 'design/admin/content/pendinglist' )}</dt>
            <dd><img src="{$row.language|flag_icon}" width="18" height="12" alt="" /> {$row.language_name|wash} &middot; {'version %number'|i18n( 'design/admin/content/pendinglist',, hash( '%number', $row.version ) )}</dd>
        </div>
        <div>
            <dt>{'Sent by'|i18n( 'design/admin/content/pendinglist' )}</dt>
            <dd>{$row.creator_name|wash}</dd>
        </div>
        <div>
            <dt>{'Sent'|i18n( 'design/admin/content/pendinglist' )}</dt>
            <dd>{$row.sent|l10n( shortdatetime )}</dd>
        </div>
    </dl>
    <div class="exp-holds">
        {if $row.approval_id}
        <p><strong>{'Held by an approval.'|i18n( 'design/admin/content/pendinglist' )}</strong> {if $row.approvers|count}{'Approvers: %names.'|i18n( 'design/admin/content/pendinglist',, hash( '%names', $row.approvers|implode( ', ' ) ) )|wash}{/if}</p>
        {/if}
        {if $row.workflows|count}
        <p>{'Workflow: %names.'|i18n( 'design/admin/content/pendinglist',, hash( '%names', $row.workflows|implode( ', ' ) ) )|wash}</p>
        {elseif $row.approval_id|not}
        <p>{'No workflow process or approval holds this version any more. If it stays pending, ask an administrator to look at Setup > Workflow processes.'|i18n( 'design/admin/content/pendinglist' )}</p>
        {/if}
    </div>
</li>
    {undef $card_id}
{/foreach}
</ul>
{/if}

<div class="exp-listfoot">
    <p class="exp-sizes">
        <span>{'Per page'|i18n( 'design/admin/content/pendinglist' )}:</span>
    {foreach first_set( $pending_limit_choices, array( 10, 25, 50 ) ) as $limit_index => $limit_option}
        {if eq( $limit_option, $page_limit )}
        <span class="current" aria-current="true">{$limit_option}</span>
        {else}
        <a href={concat( '/user/preferences/set/admin_pending_list_limit/', $limit_index|inc )|ezurl} title="{'Show %count versions per page.'|i18n( 'design/admin/content/pendinglist',, hash( '%count', $limit_option ) )}">{$limit_option}</a>
        {/if}
    {/foreach}
    </p>
    <div class="exp-pager">
    {include name=navigator
             uri='design:navigator/google.tpl'
             page_uri=$base
             page_uri_suffix=$suffix
             item_count=$count
             view_parameters=$view_parameters
             item_limit=$page_limit}
    </div>
</div>
</section>

</div></div></div>
</div>
