{* The items of one collaboration group, with the tools to manage the group (admin4). Variables: collab_group,
   view_parameters (offset, status, role, type), notice. *}
{def $limit = 15
     $gid = $collab_group.id
     $base = concat( 'collaboration/group/list/', $gid )
     $ps = first_set( $view_parameters.status, 'all' )
     $pr = first_set( $view_parameters.role, 'all' )
     $pt = first_set( $view_parameters.type, 'all' )
     $inbox = fetch( 'collaboration', 'inbox', hash( 'status', $ps, 'role', $pr, 'type', $pt, 'group_id', $gid,
                                                    'offset', first_set( $view_parameters.offset, 0 ), 'limit', $limit ) )
     $is_main = eq( $gid, $inbox.main_group_id )
     $group_tree = fetch( 'collaboration', 'group_tree', hash( 'parent_group_id', 0 ) )}
{include uri='design:collaboration/parts/style.tpl'}

<div class="cb" id="exp-collab">

    <div class="cb-head">
        <div>
            <p class="cb-crumb"><a href={'collaboration/view/summary'|ezurl}>{'Collaboration'|i18n( 'design/admin/collaboration/inbox' )}</a> / {'Group'|i18n( 'design/admin/collaboration/inbox' )}</p>
            <h1>{$collab_group.title|wash}</h1>
            <p>{'%n items in this group.'|i18n( 'design/admin/collaboration/inbox',, hash( '%n', $inbox.total ) )}</p>
        </div>
    </div>

    {include uri='design:collaboration/parts/notice.tpl' notice=first_set( $notice, false() )}

    <div class="cb-filters">
        <div class="cb-filter"><b>{'Status'|i18n( 'design/admin/collaboration/inbox' )}</b>
            <a class="cb-chip{if $ps|eq( 'all' )} current{/if}" href={$base|ezurl}>{'All'|i18n( 'design/admin/collaboration/inbox' )}</a>
            <a class="cb-chip{if $ps|eq( 'waiting' )} current{/if}" href={concat( $base, '/(status)/waiting' )|ezurl}>{'Waiting'|i18n( 'design/admin/collaboration/inbox' )}</a>
            <a class="cb-chip{if $ps|eq( 'approved' )} current{/if}" href={concat( $base, '/(status)/approved' )|ezurl}>{'Approved'|i18n( 'design/admin/collaboration/inbox' )}</a>
            <a class="cb-chip{if $ps|eq( 'denied' )} current{/if}" href={concat( $base, '/(status)/denied' )|ezurl}>{'Denied'|i18n( 'design/admin/collaboration/inbox' )}</a>
        </div>
    </div>

    <div class="cb-layout has-side">
        <div class="cb-main">
{if $inbox.items|count|gt( 0 )}
            {include uri='design:collaboration/parts/rows.tpl' rows=$inbox.items user_id=$inbox.user_id}
            {include name=Navigator uri='design:navigator/google.tpl' page_uri=concat( '/', $base ) item_count=$inbox.total
                     view_parameters=$view_parameters item_limit=$limit}
{else}
            <div class="cb-empty">
                <h2>{'No items in this group'|i18n( 'design/admin/collaboration/inbox' )}</h2>
                <p>{'Move an item here from its page, or choose this group there.'|i18n( 'design/admin/collaboration/inbox' )}</p>
            </div>
{/if}
        </div>

        <div class="cb-side">
            <div class="cb-card">
                <h2>{'Groups'|i18n( 'design/admin/collaboration/inbox' )}</h2>
                <ul class="cb-tree">
{foreach $group_tree as $group}
                    <li style="padding-left: {mul( $group.depth, 0.9 )}em"><a{if eq( $group.id, $gid )} class="current" aria-current="page"{/if} href={concat( 'collaboration/group/list/', $group.id )|ezurl}><span>{$group.title|wash}</span><span class="count">{$group.item_count}</span></a></li>
{/foreach}
                </ul>
            </div>

            <div class="cb-card">
                <h2>{'Manage this group'|i18n( 'design/admin/collaboration/inbox' )}</h2>
                <form class="cb-form" method="post" action={$base|ezurl}>
                    <input type="text" name="CollaborationGroupTitle" maxlength="255" placeholder="{'New subgroup'|i18n( 'design/admin/collaboration/inbox' )|wash}" aria-label="{'New subgroup'|i18n( 'design/admin/collaboration/inbox' )|wash}" />
                    <input class="cb-btn" type="submit" name="CollaborationGroupCreate" value="{'Add'|i18n( 'design/admin/collaboration/inbox' )}" />
                </form>
{if $is_main|not}
                <form class="cb-form" method="post" action={$base|ezurl}>
                    <input type="text" name="CollaborationGroupTitle" maxlength="255" value="{$collab_group.title|wash}" aria-label="{'Rename the group'|i18n( 'design/admin/collaboration/inbox' )|wash}" />
                    <input class="cb-btn" type="submit" name="CollaborationGroupRename" value="{'Rename'|i18n( 'design/admin/collaboration/inbox' )}" />
                </form>
                <form class="cb-form" method="post" action={$base|ezurl} data-cb-confirm="{'Delete this group and its subgroups? Their items move to the main group.'|i18n( 'design/admin/collaboration/inbox' )|wash}">
                    <input class="cb-btn deny" type="submit" name="CollaborationGroupDelete" value="{'Delete the group'|i18n( 'design/admin/collaboration/inbox' )}" />
                </form>
{else}
                <p class="cb-hint">{'This is your main group: it cannot be renamed or deleted.'|i18n( 'design/admin/collaboration/inbox' )}</p>
{/if}
            </div>
        </div>
    </div>

</div>
<script type="text/javascript">
{literal}
(function () {
    var forms = document.querySelectorAll('#exp-collab form[data-cb-confirm]');
    for (var i = 0; i < forms.length; i++) {
        forms[i].addEventListener('submit', function (e) { if (!window.confirm(this.getAttribute('data-cb-confirm'))) e.preventDefault(); });
    }
})();
{/literal}
</script>
