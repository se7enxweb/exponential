{* The way from a user's profile to the API access page (apikey/list), shown to a signed in user who may make API
   keys or already has some. Variables: style ('box', the default: a small box with a sentence; 'item': a list item
   with the link only, for a design's own list of account links). The box's look is in
   stylesheets/apikey_account_link.css, linked here so it needs nothing of the page it is on. *}
{def $ak_user = fetch( 'user', 'current_user' )}
{if $ak_user.is_logged_in}
{def $ak_can = fetch( 'apikey', 'can_create' )
     $ak_counts = fetch( 'apikey', 'counts' )}
{if or( $ak_can, and( $ak_counts, $ak_counts.total|gt(0) ) )}
{if eq( first_set( $style, 'box' ), 'item' )}
<li><a href={'apikey/list'|ezurl}>{'API access'|i18n( 'design/standard/apikey' )}</a>{if and( $ak_counts, $ak_counts.active|gt(0) )} <span class="acc-count">{$ak_counts.active}</span>{/if}</li>
{else}
<link rel="stylesheet" type="text/css" href={'stylesheets/apikey_account_link.css'|ezdesign} />
<div class="ak-account-link">
    <p><b>{'API access'|i18n( 'design/standard/apikey' )}:</b>
        {if and( $ak_counts, $ak_counts.active|gt(0) )}{'You have %count active API keys.'|i18n( 'design/standard/apikey',, hash( '%count', $ak_counts.active ) )}{else}{'Make a personal key for your own scripts and tools to publish through the REST interface.'|i18n( 'design/standard/apikey' )}{/if}</p>
    <a class="ak-account-link-button" href={'apikey/list'|ezurl}>{'Manage my API keys'|i18n( 'design/standard/apikey' )}</a>
</div>
{/if}
{/if}
{undef $ak_can $ak_counts}
{/if}
{undef $ak_user}
