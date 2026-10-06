{* The way from a user's profile to the API access page (apikey/list), shown to a signed in user who may make API
   keys or already has some. Variables: style ('box', the default: a small box with a sentence; 'item': a list item
   with the link only, for a design's own list of account links). Inline styles, so it needs nothing of the page
   it is on. *}
{def $ak_user = fetch( 'user', 'current_user' )}
{if $ak_user.is_logged_in}
{def $ak_can = fetch( 'apikey', 'can_create' )
     $ak_counts = fetch( 'apikey', 'counts' )}
{if or( $ak_can, and( $ak_counts, $ak_counts.total|gt(0) ) )}
{if eq( first_set( $style, 'box' ), 'item' )}
<li><a href={'apikey/list'|ezurl}>{'API access'|i18n( 'design/standard/apikey' )}</a>{if and( $ak_counts, $ak_counts.active|gt(0) )} <span class="acc-count">{$ak_counts.active}</span>{/if}</li>
{else}
<div class="ak-account-link" style="display:flex;flex-wrap:wrap;gap:.4em 1em;align-items:center;justify-content:space-between;margin:0 0 1em;padding:.7em .95em;border:1px solid #d9dde3;border-radius:9px;background:#f5f6f8;color:#1f2430;">
    <p style="margin:0;flex:1 1 18em;"><b>{'API access'|i18n( 'design/standard/apikey' )}:</b>
        {if and( $ak_counts, $ak_counts.active|gt(0) )}{'You have %count active API keys.'|i18n( 'design/standard/apikey',, hash( '%count', $ak_counts.active ) )}{else}{'Make a personal key for your own scripts and tools to publish through the REST interface.'|i18n( 'design/standard/apikey' )}{/if}</p>
    <a href={'apikey/list'|ezurl} style="display:inline-block;padding:.4em .95em;border:1px solid #9a3412;border-radius:8px;background:#fff;color:#9a3412;font-weight:650;text-decoration:none;">{'Manage my API keys'|i18n( 'design/standard/apikey' )}</a>
</div>
{/if}
{/if}
{undef $ak_can $ak_counts}
{/if}
{undef $ak_user}
