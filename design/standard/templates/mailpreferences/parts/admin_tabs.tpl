{* The administrator's pages of the e-mail preferences, as tabs. $current: status, categories, suppression, consent, user. *}
<nav aria-label="{'E-mail preferences administration'|i18n( 'design/admin/mailpreferences' )}">
<ul class="mp-tabs">
    <li><a href={'mailpreferences/admin/status'|ezurl}{if $current|eq( 'status' )} aria-current="page"{/if}>{'Status'|i18n( 'design/admin/mailpreferences' )}</a></li>
    <li><a href={'mailpreferences/admin/categories'|ezurl}{if $current|eq( 'categories' )} aria-current="page"{/if}>{'Categories'|i18n( 'design/admin/mailpreferences' )}</a></li>
    <li><a href={'mailpreferences/admin/suppression'|ezurl}{if $current|eq( 'suppression' )} aria-current="page"{/if}>{'Suppression list'|i18n( 'design/admin/mailpreferences' )}</a></li>
    <li><a href={'mailpreferences/admin/consent'|ezurl}{if $current|eq( 'consent' )} aria-current="page"{/if}>{'Consent log'|i18n( 'design/admin/mailpreferences' )}</a></li>
    <li><a href={'mailpreferences/admin/user'|ezurl}{if $current|eq( 'user' )} aria-current="page"{/if}>{'A user'|i18n( 'design/admin/mailpreferences' )}</a></li>
</ul>
</nav>
