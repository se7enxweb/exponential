<div id="content-tree">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h4>{'Media library'|i18n( 'design/admin/parts/media/menu' )}</h4>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

{* Treemenu. *}
<div id="contentstructure">
    {include uri='design:contentstructuremenu/content_structure_menu_dynamic.tpl' custom_root_node_id=ezini( 'NodeSettings', 'MediaRootNode', 'content.ini')}
</div>

{* Trashcan. *}
{include uri='design:parts/left_trash.tpl'}

{* DESIGN: Content END *}</div></div></div>
</div>

{* See parts/ini_menu.tpl and menu.ini for more info, or parts/setup/menu.tpl for full example *}
{include uri='design:parts/ini_menu.tpl' ini_section='Leftmenu_media'}

{* Left menu width control. *}
{include uri='design:parts/left_width_control.tpl'}
