{* user/preferences/set/<key>/<value> opened directly (a typed address, a bookmark) or from another site: nothing is
   changed until the user confirms here; the form posts from this site (with the form token). Variables:
   preference_key, preference_value, redirect_uri ('' when the address named no page to go back to). *}
{def $exp_i18n = 'design/standard/user/preferences'}
<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h1 class="context-title">{'Change a preference'|i18n( $exp_i18n )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="block">
{if eq( $preference_key, 'admin_navigation_content' )}
    <p>{cond( eq( $preference_value, '1' ), 'Switch the tabs on while browsing content?'|i18n( $exp_i18n ), 'Switch the tabs off while browsing content?'|i18n( $exp_i18n ) )}</p>
{else}
    <p>{'Set your preference "%key" to "%value"?'|i18n( $exp_i18n,, hash( '%key', $preference_key, '%value', $preference_value ) )|wash}</p>
{/if}
    <p>{'This address was opened directly or from another site, so nothing has been changed yet.'|i18n( $exp_i18n )}</p>
</div>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<form method="post" action={'/user/preferences/set'|ezurl}>
    <input type="hidden" name="Function" value="set" />
    <input type="hidden" name="Key" value="{$preference_key|wash}" />
    <input type="hidden" name="Value" value="{$preference_value|wash}" />
{if $redirect_uri}
    <input type="hidden" name="RedirectURIAfterSet" value="{$redirect_uri|wash}" />
{/if}
    <div class="block">
        <input class="defaultbutton" type="submit" name="ExpPreferenceConfirmButton" value="{'Change it'|i18n( $exp_i18n )}" />
        <a class="button" href={cond( $redirect_uri, $redirect_uri, '/' )|ezurl}>{'Cancel'|i18n( $exp_i18n )}</a>
    </div>
</form>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>
{undef $exp_i18n}
