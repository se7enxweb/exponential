{* The right toolbar's interface choices in the classic admin design (uiswitch): the same tool as admin4's
   toolbar/full/admin_ui_switch.tpl, in the classic box markup. design/admin ends every admin chain, so this is
   also the tool of admin2 and admin3, and the way back for a user who switched the design to the classic one.
   Shown only to a user who holds uiswitch/<provider> and only on a siteaccess with the switch, not while editing
   or browsing (toolbar.ini [Toolbar_admin_right] Tool[]=admin_ui_switch). *}
{if and( ne( $ui_context, 'edit' ), ne( $ui_context, 'browse' ) )}
{def $exp_ui_choices = fetch( 'uiswitch', 'toolbar' )}
{if $exp_ui_choices|count}
<div id="uiswitch-tool">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h4>{'Interface'|i18n( 'design/admin/toolbar/uiswitch' )}</h4>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

{include uri='design:uiswitch/choices.tpl' choices=$exp_ui_choices prefix='uiswitch-classic'}

{* DESIGN: Content END *}</div></div></div>

</div>
{/if}
{undef $exp_ui_choices}
{/if}
