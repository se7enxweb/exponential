{* audit/reauth: the password re-entry before an audit/manage action ([AuditConsoleSettings] ReauthForManage;
   expAuditReauth::gate()). Posts to the view of the action with the action's button again, so a correct password
   runs the action at once. Variables: action_url, button, label, failed, minutes, login. Never a password value. *}
{include uri='design:audit/style.tpl'}
<div class="context-block au-view" id="exp-audit-reauth">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Confirm your password'|i18n( 'design/admin/audit' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

{if $failed}
<div class="message-error" role="alert"><h2>{'The password is not correct. The attempt was recorded.'|i18n( 'design/admin/audit' )}</h2></div>
{/if}

<form method="post" action={$action_url|ezurl} class="au-reauth" autocomplete="off">
<div class="block">
    <p>{'"%action" is an audit management action. Enter the password of %login again to continue; it stays confirmed for %minutes minutes in this session.'|i18n( 'design/admin/audit',, hash( '%action', $label, '%login', $login, '%minutes', $minutes ) )|wash}</p>
    <label for="au-reauth-password">{'Password'|i18n( 'design/admin/audit' )}</label>
    <input id="au-reauth-password" class="box" type="password" name="AuditReauthPassword" value="" size="30" autocomplete="current-password" required="required" autofocus="autofocus" />
    <input type="hidden" name="{$button|wash}" value="1" />
</div>
<div class="controlbar"><div class="block">
    <input type="submit" class="defaultbutton" name="AuditReauthButton" value="{'Confirm and continue'|i18n( 'design/admin/audit' )|wash}" />
    <input type="submit" class="button" name="AuditReauthCancelButton" value="{'Cancel'|i18n( 'design/admin/audit' )|wash}" formnovalidate="formnovalidate" />
</div></div>
</form>

{* DESIGN: Content END *}</div></div></div>
</div>
