{* setup/settingstoolbar opened by its address: the quick settings form of the administration's sidebar
   (setup/quick_settings.tpl) on a page of its own. Variables: siteaccess, select_siteaccess, redirect_uri (the view). *}
<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Quick settings'|i18n( 'design/admin/pagelayout' )}</h1>
{* DESIGN: Header END *}</div></div>
{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">
    {include uri='design:setup/quick_settings.tpl' form_id='quicksettings-page' ui_context=''}
{* DESIGN: Content END *}</div></div></div>
</div>
