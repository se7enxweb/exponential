{* Setup > Overview (setup/menu, the Setup tab's first page) in the classic administration designs: every function
   of the Setup menu as a tile with a Bootstrap icon, its name and one line on what it does, in the groups of
   menu.ini [Leftmenu_setup], as admin4 draws it (design/admin4/templates/setup/setupmenu.tpl), inside this
   design's page frame. The links, names and access checks are those of the left menu (parts/setup/menu.tpl,
   parts/ini_menu.tpl with menu_layout='tiles'); the tiles are the shared components (parts/ini_menu_tiles.tpl,
   stylesheets/modules/setup.css). Guide: doc/features/6.0/admin4-design.md ("Setup overview") *}
{def $setup_menu_layout = 'tiles'}

<div class="context-block a4-page a4-setup-overview">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Setup'|i18n( 'design/admin/setup/setupmenu' )}</h1>
<div class="header-mainline"></div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">
<p class="a4-intro">{'Configure the installation: content model, site and URLs, workflows, users and the system.'|i18n( 'design/admin/setup/setupmenu' )}</p>

{include uri='design:parts/setup/menu.tpl'}
</div></div></div>

</div>
{undef $setup_menu_layout}
