{* How the messages come: at once, or combined into a digest. Form fields as before: ReceiveDigest_ezgeneraldigest,
   DigestType_ezgeneraldigest (3 daily, 1 weekly, 2 monthly), Time_, Weekday_, Monthday_; Store saves. *}
{def $settings = $handler.settings
     $type = $settings.digest_type
     $on = $settings.receive_digest}
<form class="nf-card" id="nf-digest" method="post" action={'notification/settings'|ezurl}>
    <h2>{'E-mail digest'|i18n( 'design/admin/notification/settings' )}</h2>
    <p class="nf-lead">{'By default every change is mailed at once. A digest holds the messages back and sends one e-mail with all of them: every day, once a week or once a month, at the hour you choose.'|i18n( 'design/admin/notification/settings' )}</p>

    <label class="nf-toggle">
        <input type="checkbox" name="ReceiveDigest_{$handler.id_string}" value="1"{if $on} checked="checked"{/if} />
        <div><b>{'Receive all messages combined in one digest'|i18n( 'design/admin/notification/settings' )}</b>
        <span>{'Switched off: one e-mail for each change, at once.'|i18n( 'design/admin/notification/settings' )}</span></div>
    </label>

    <div class="nf-choice">
        <input type="radio" id="nf-d3" name="DigestType_{$handler.id_string}" value="3"{if or( $type|eq( 3 ), $type|eq( 0 ) )} checked="checked"{/if} />
        <div class="nf-field"><label for="nf-d3">{'Daily, at'|i18n( 'design/admin/notification/settings' )}</label>
            <select name="Time_{$handler.id_string}" aria-label="{'Hour'|i18n( 'design/admin/notification/settings' )|wash}">
{foreach $handler.available_hours as $hour}<option value="{$hour}"{if eq( $hour, $settings.time )} selected="selected"{/if}>{$hour}</option>{/foreach}
            </select></div>
        <input type="radio" id="nf-d1" name="DigestType_{$handler.id_string}" value="1"{if $type|eq( 1 )} checked="checked"{/if} />
        <div class="nf-field"><label for="nf-d1">{'Once per week, on'|i18n( 'design/admin/notification/settings' )}</label>
            <select name="Weekday_{$handler.id_string}" aria-label="{'Weekday'|i18n( 'design/admin/notification/settings' )|wash}">
{foreach $handler.all_week_days as $day}<option value="{$day|wash}"{if eq( $day, $settings.day )} selected="selected"{/if}>{$day|wash}</option>{/foreach}
            </select></div>
        <input type="radio" id="nf-d2" name="DigestType_{$handler.id_string}" value="2"{if $type|eq( 2 )} checked="checked"{/if} />
        <div class="nf-field"><label for="nf-d2">{'Once per month, on day number'|i18n( 'design/admin/notification/settings' )}</label>
            <select name="Monthday_{$handler.id_string}" aria-label="{'Day of the month'|i18n( 'design/admin/notification/settings' )|wash}">
{foreach $handler.all_month_days as $day}<option value="{$day}"{if eq( $day, $settings.day )} selected="selected"{/if}>{$day}</option>{/foreach}
            </select></div>
    </div>
    <p class="nf-hint">{'If day number is larger than the number of days within the current month, the last day of the current month will be used.'|i18n( 'design/admin/notification/settings' )}
        {'The hour is the server time of the day the digest is made; the weekly and monthly choices use the time chosen above.'|i18n( 'design/admin/notification/settings' )}</p>
    <div class="nf-actions"><input class="nf-btn primary" type="submit" name="Store" value="{'Save digest settings'|i18n( 'design/admin/notification/settings' )}" /></div>
</form>
