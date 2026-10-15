<div id="content-tree">
{* In case of error menu is hidden. It prevents showing content tree in case of errors i.e. module / action is disabled *}
{if ne(module_params().module_name, 'error')}
{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h4>{'Content structure'|i18n( 'design/admin/parts/content/menu' )}</h4>
{* DESIGN: Header END *}</div></div>
{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

{* Treemenu. *}
<div id="contentstructure">
    {include uri='design:contentstructuremenu/content_structure_menu_dynamic.tpl'}
</div>

{* Trashcan. *}
{include uri='design:parts/left_trash.tpl'}

{* The content finder box: only with contentfinder/search (doc/features/6.0/content-finder.md). *}
{if ne( $ui_context, 'browse' )}
{include uri='design:contentfinder/box.tpl'}
{/if}

{* DESIGN: Content END *}</div></div></div>
{/if}
</div>

{* See parts/ini_menu.tpl and menu.ini for more info, or parts/setup/menu.tpl for full example *}
{include uri='design:parts/ini_menu.tpl' ini_section='Leftmenu_content'}

{* Left menu width control. *}
{include uri='design:parts/left_width_control.tpl'}
