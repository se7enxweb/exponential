{* See parts/ini_menu.tpl and menu.ini for more info, or parts/setup/menu.tpl for full example *}
{include uri='design:parts/ini_menu.tpl' ini_section='Leftmenu_my' i18n_hash=hash(
    'my_account',         'My account'|i18n( 'design/admin/parts/my/menu' ),
    'my_drafts',          'My drafts'|i18n( 'design/admin/parts/my/menu' ),
    'my_pending',         'My pending items'|i18n( 'design/admin/parts/my/menu' ),
    'my_notifications',   'My notification settings'|i18n( 'design/admin/parts/my/menu' ),
    'my_mail',            'My e-mail preferences'|i18n( 'design/admin/parts/my/menu' ),
    'my_bookmarks',       'My bookmarks'|i18n( 'design/admin/parts/my/menu' ),
    'collaboration',      'Collaboration'|i18n( 'design/admin/parts/my/menu' ),
    'change_password',    'Change password'|i18n( 'design/admin/parts/my/menu' ),
    'my_shopping_basket', 'My shopping basket'|i18n( 'design/admin/parts/my/menu' ),
    'my_wish_list',       'My wish list'|i18n( 'design/admin/parts/my/menu' ),
    'edit_profile',       'Edit profile'|i18n( 'design/admin/parts/my/menu' ),
    'dashboard',          'Dashboard'|i18n( 'design/admin/parts/my/menu' ),
)}


{* On the collaboration pages: the inbox's own shortcuts, with the open counts (admin4 and admin4l share this menu) *}
{if and( $uri_string|begins_with( 'collaboration/' ), fetch( 'user', 'has_access_to', hash( 'module', 'collaboration', 'function', 'view' ) ) )}
{def $cb_counts = fetch( 'collaboration', 'inbox', hash( 'limit', 1 ) ).counts}
<div id="collaboration-menu">
{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h4 id="leftmenu-title-collaboration">{'Inbox'|i18n( 'design/admin/collaboration/inbox' )}</h4>
{* DESIGN: Header END *}</div></div>
{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">
{* the same list as every ini menu (parts/ini_menu_items.tpl): no bullets, 24px targets, the current one marked *}
{include uri='design:parts/ini_menu_items.tpl' labelledby='leftmenu-title-collaboration' current_uri_string=$uri_string items=array(
    hash( 'key', 'all',       'state', 'link', 'url', 'collaboration/view/summary',                                    'name', concat( 'All items'|i18n( 'design/admin/collaboration/inbox' )|wash, ' (', $cb_counts.all, ')' ) ),
    hash( 'key', 'waiting',   'state', 'link', 'url', 'collaboration/view/summary/(status)/waiting/(role)/approver', 'name', concat( 'Waiting for your decision'|i18n( 'design/admin/collaboration/inbox' )|wash, ' (', $cb_counts.waiting_for_me, ')' ) ),
    hash( 'key', 'others',    'state', 'link', 'url', 'collaboration/view/summary/(status)/waiting/(role)/author',   'name', concat( 'Your items waiting for others'|i18n( 'design/admin/collaboration/inbox' )|wash, ' (', $cb_counts.waiting_for_others, ')' ) ),
    hash( 'key', 'approved',  'state', 'link', 'url', 'collaboration/view/summary/(status)/approved',                'name', concat( 'Approved'|i18n( 'design/admin/collaboration/inbox' )|wash, ' (', $cb_counts.approved, ')' ) ),
    hash( 'key', 'denied',    'state', 'link', 'url', 'collaboration/view/summary/(status)/denied',                  'name', concat( 'Denied'|i18n( 'design/admin/collaboration/inbox' )|wash, ' (', $cb_counts.denied, ')' ) )
)}
{* DESIGN: Content END *}</div></div></div>
</div>
{undef $cb_counts}
{/if}


{def $custom_root_node = fetch( 'content', 'node', hash( 'node_id', 1 ) )}
{if $custom_root_node.can_read}
<div id="content-tree">
{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h4>{'Site structure'|i18n( 'design/admin/parts/content/menu' )}</h4>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

{* Treemenu. *}
<div id="contentstructure">
    {include uri='design:contentstructuremenu/content_structure_menu_dynamic.tpl' custom_root_node=$custom_root_node menu_persistence=false() hide_node_list=array(ezini( 'NodeSettings', 'DesignRootNode', 'content.ini'), ezini( 'NodeSettings', 'SetupRootNode', 'content.ini'))}
</div>

{* DESIGN: Content END *}</div></div></div>
</div>
{/if}

{* Left menu width control. *}
{include uri='design:parts/left_width_control.tpl'}
