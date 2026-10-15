{* Setup > Overview (setup/menu, the Setup tab's first page) in the classic administration designs. admin4 draws
   every function of the Setup menu as a tile (design/admin4/templates/setup/setupmenu.tpl); here the left menu
   lists them, and a toolbar "setup" that a site configures in toolbar.ini still shows. *}
<div class="context-block">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Setup'|i18n( 'design/admin/setup/setupmenu' )}</h1>
<div class="header-mainline"></div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">
<p>{'Choose a function in the Setup menu.'|i18n( 'design/admin/setup/setupmenu' )}</p>
<div class="toolbar-setup">
{tool_bar name=setup view=line}
</div>
</div></div></div>

</div>
