{* The buttons of the log viewer's pages, one row on every one of them (setup/logs, its archives, setup/logsmanage,
   setup/logshealth, setup/logsdigest): Logs (back to setup/logs), Manage logs (only with setup/managelogs), Log health,
   Error digest. Include it with log_nav_current = logs | manage | health | digest; the current page's button is marked
   aria-current="page" (and still works as a link, to reload the page). log_nav_intro, optional, is the page's intro,
   drawn below the row: every page is heading, row, intro, so the row never moves. One file in both designs. *}
{if is_unset( $log_nav_current )}{def $log_nav_current = ''}{/if}
{def $log_nav_items = array(
         hash( 'key', 'logs',   'url', 'setup/logs',       'label', 'Logs'|i18n( 'design/admin/setup/logs' ),         'icon', 'journal-text' ),
         hash( 'key', 'manage', 'url', 'setup/logsmanage', 'label', 'Manage logs'|i18n( 'design/admin/setup/logs' ),  'icon', 'arrow-clockwise' ),
         hash( 'key', 'health', 'url', 'setup/logshealth', 'label', 'Log health'|i18n( 'design/admin/setup/logs' ),   'icon', 'speedometer2' ),
         hash( 'key', 'digest', 'url', 'setup/logsdigest', 'label', 'Error digest'|i18n( 'design/admin/setup/logs' ), 'icon', 'exclamation-diamond' ) )
     $log_nav_manage = fetch( 'user', 'has_access_to', hash( 'module', 'setup', 'function', 'managelogs' ) )}
<nav class="a4-setup-log-nav" aria-label="{'Log tools'|i18n( 'design/admin/setup/logs' )|wash}">
<ul class="a4-setup-log-nav-list">
{foreach $log_nav_items as $log_nav_item}
    {if or( $log_nav_item.key|ne( 'manage' ), $log_nav_manage )}
    <li><a class="a4-btn a4-btn-primary a4-setup-log-nav-btn{if $log_nav_item.key|eq( $log_nav_current )} is-current{/if}" href={$log_nav_item.url|ezurl}{if $log_nav_item.key|eq( $log_nav_current )} aria-current="page"{/if}><span class="a4-icon a4-bi-{$log_nav_item.icon}" aria-hidden="true"></span>{$log_nav_item.label|wash}</a></li>
    {/if}
{/foreach}
</ul>
</nav>
{* The page's intro, always below the row, so the row sits at the same place on every page (heading, row, intro) *}
{if and( is_set( $log_nav_intro ), $log_nav_intro|ne( '' ) )}<p class="a4-intro a4-setup-log-intro">{$log_nav_intro|wash}</p>{/if}
{undef $log_nav_items $log_nav_manage}
