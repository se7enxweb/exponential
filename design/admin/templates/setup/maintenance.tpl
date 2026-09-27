{* Setup > Maintenance: switch the site's maintenance mode (expMaintenance). *}
{if $feedback}
    <div class="{if $feedback[0]}message-feedback{else}message-warning{/if}">
        <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {$feedback[1]|wash}</h2>
    </div>
{/if}

<form name="maintenanceform" method="post" action={'/setup/maintenance/'|ezurl}>
<div class="context-block">
{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Maintenance'|i18n( 'design/admin/setup/maintenance' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>
{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">
<div class="block">
{if $maintenance_on}
    <p><strong>{'The site is in maintenance mode.'|i18n( 'design/admin/setup/maintenance' )}</strong>
       {'Visitors see the maintenance page (503); the administration stays reachable.'|i18n( 'design/admin/setup/maintenance' )}</p>
    <table class="list" cellspacing="0">
    <tr><th>{'Reason'|i18n( 'design/admin/setup/maintenance' )}</th><td>{if eq( $maintenance.reason, 'setup' )}{'An installation is running'|i18n( 'design/admin/setup/maintenance' )}{else}{'Maintenance window'|i18n( 'design/admin/setup/maintenance' )}{/if}{if is_set( $maintenance.by )} ({$maintenance.by|wash}){/if}</td></tr>
    {if and( is_set( $maintenance.since ), $maintenance.since )}<tr><th>{'Since'|i18n( 'design/admin/setup/maintenance' )}</th><td>{$maintenance.since|l10n( shortdatetime )}</td></tr>{/if}
    {if and( is_set( $maintenance.until ), $maintenance.until )}<tr><th>{'Expected back'|i18n( 'design/admin/setup/maintenance' )}</th><td>{$maintenance.until|l10n( shortdatetime )}</td></tr>{/if}
    {if and( is_set( $maintenance.message ), $maintenance.message )}<tr><th>{'Message'|i18n( 'design/admin/setup/maintenance' )}</th><td>{$maintenance.message|wash}</td></tr>{/if}
    {if and( is_set( $maintenance.allow_ips ), $maintenance.allow_ips )}<tr><th>{'Addresses that see the site'|i18n( 'design/admin/setup/maintenance' )}</th><td>{$maintenance.allow_ips|implode( ', ' )|wash}</td></tr>{/if}
    </table>
{else}
    <p>{'The site is online.'|i18n( 'design/admin/setup/maintenance' )}
       {'In maintenance mode every page request is answered with the maintenance page (503, never cached) before the settings or the database are used; images, styles and scripts are still served. Use it while you change the site, or it is switched on by the kickstarter while it installs.'|i18n( 'design/admin/setup/maintenance' )}</p>
    <label for="MaintenanceMessage">{'Message for visitors (optional)'|i18n( 'design/admin/setup/maintenance' )}</label>
    <input class="box" type="text" id="MaintenanceMessage" name="MaintenanceMessage" maxlength="500" value="" />
    <label for="MaintenanceMinutes">{'Expected duration in minutes (optional)'|i18n( 'design/admin/setup/maintenance' )}</label>
    <input class="halfbox" type="number" min="0" id="MaintenanceMinutes" name="MaintenanceMinutes" value="" />
    <label for="MaintenanceAllowIPs">{'Addresses that still see the site (optional, comma separated; yours, %ip, is added)'|i18n( 'design/admin/setup/maintenance',, hash( '%ip', $client_ip|wash ) )}</label>
    <input class="box" type="text" id="MaintenanceAllowIPs" name="MaintenanceAllowIPs" value="" />
{/if}
</div>
{* DESIGN: Content END *}</div></div></div>
<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
{if $maintenance_on}
    <input class="defaultbutton" type="submit" name="SwitchOffButton" value="{'Switch maintenance off'|i18n( 'design/admin/setup/maintenance' )}" />
{else}
    <input class="button" type="submit" name="SwitchOnButton" value="{'Switch maintenance on'|i18n( 'design/admin/setup/maintenance' )}" />
{/if}
</div>
{* DESIGN: Control bar END *}</div></div>
</div>
</div>
</form>
