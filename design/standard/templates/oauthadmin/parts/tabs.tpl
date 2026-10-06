{* The two pages of the REST administration: the applications (OAuth clients) and the personal API keys.
   Variables: current ('applications' or 'keys'), application_count, key_count (both optional). *}
<nav aria-label="{'REST administration'|i18n( 'design/admin/oauthadmin' )}">
<ul class="exp-tabs">
    <li><a href={'oauthadmin/list'|ezurl}{if $current|eq( 'applications' )} aria-current="page"{/if}>{'Applications'|i18n( 'design/admin/oauthadmin' )}{if is_set( $application_count )}{if $application_count|ne( false() )} <span class="exp-count">{$application_count}</span>{/if}{/if}</a></li>
    <li><a href={'oauthadmin/keys'|ezurl}{if $current|eq( 'keys' )} aria-current="page"{/if}>{'API keys'|i18n( 'design/admin/oauthadmin' )}{if is_set( $key_count )}{if $key_count|ne( false() )} <span class="exp-count">{$key_count}</span>{/if}{/if}</a></li>
</ul>
</nav>
