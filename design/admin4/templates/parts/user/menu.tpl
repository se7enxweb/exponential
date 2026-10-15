{if eq( $ui_context, 'edit' )}
{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h4>{'Role information'|i18n( 'design/admin/parts/user/menu' )}</h4>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

<p>
<label>{'Name'|i18n( 'design/admin/parts/user/menu' )}:</label>
{$role.name|wash}
</p>

<p>
<label>{'ID'|i18n( 'design/admin/parts/user/menu' )}:</label>
{$role.id|wash}
</p>

{* DESIGN: Content END *}</div></div></div>

{else}

<div id="content-tree">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h4>{'User accounts'|i18n( 'design/admin/parts/user/menu' )}</h4>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

{* Treemenu. *}
<div id="contentstructure">
    {include uri='design:contentstructuremenu/content_structure_menu_dynamic.tpl' custom_root_node_id=ezini( 'NodeSettings', 'UserRootNode', 'content.ini')}
</div>

{* Trashcan. *}
{include uri='design:parts/left_trash.tpl'}

{* DESIGN: Content END *}</div></div></div>
</div>

{* Roles & policies *}
{* See parts/ini_menu.tpl and menu.ini for more info, or parts/setup/menu.tpl for full example *}
{include uri='design:parts/ini_menu.tpl' ini_section='Leftmenu_user' i18n_hash=hash(
    'access_controll',    'Access control'|i18n( 'design/admin/parts/user/menu' ),
    'roles_and_policies', 'Roles and policies'|i18n( 'design/admin/parts/user/menu' ),
    'unactivated',        'Unactivated users'|i18n( 'design/admin/parts/user/menu' ),
    'user_hygiene',       'Check user accounts'|i18n( 'design/admin/parts/user/menu' ),
)}

{* Left menu width control. *}
{include uri='design:parts/left_width_control.tpl'}
{/if}{* if ne( $ui_context, 'edit' ) *}
