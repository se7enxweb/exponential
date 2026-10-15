{* The right toolbar's interface choices (#506): the display language of the administration, and what other
   providers of uiswitch.ini add; shown only to a user who holds uiswitch/<provider> and only on a siteaccess with the
   switch, not while editing or browsing. Placed after Bookmarks (toolbar.ini [Toolbar_admin_right]). *}
{if and( ne( $ui_context, 'edit' ), ne( $ui_context, 'browse' ) )}
{def $exp_ui_choices = fetch( 'uiswitch', 'toolbar' )}
{if $exp_ui_choices|count}
<div id="uiswitch-tool" class="a4-uiswitch">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h4>{'Interface'|i18n( 'design/admin/toolbar/uiswitch' )}</h4>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

{include uri='design:uiswitch/choices.tpl' choices=$exp_ui_choices prefix='a4-uiswitch'}

{* DESIGN: Content END *}</div></div></div>

</div>
{/if}
{undef $exp_ui_choices}
{/if}
