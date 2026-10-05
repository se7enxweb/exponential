{* The body of the e-mail preference page, shared by every design and every way in: the signed-in page
   (mailpreferences/settings), the personal link (mailpreferences/manage/<token>) and the administrator's page for a user
   (mailpreferences/admin/user/<id>). The design's own page template wraps it.

   Variables (from Exponential\Service\MailPreferencesPage::templateVariables()):
     mode           'account', 'token' or 'admin'
     form_action    the address the forms post to (not yet through ezurl)
     email          the address the preferences are for
     master         true when optional e-mail is on
     suppressed     true when the address is on the suppression list (optional e-mail is not sent at all)
     categories     optional categories: hash( identifier, name, description, on, pending, frequency, frequencies )
     essential      essential categories: hash( identifier, name, description )
     history        the newest consent records: hash( time, category, action, source, wording )
     history_total  how many records there are
     export         hash( json, csv ): download addresses, or false when downloading is not offered here
     notice         hash( type, text ) or false; notices: more of them
   Works without JavaScript: every switch is a checkbox or a button in a form. *}
{def $paused = and( $master|not, $categories|count|gt( 0 ) )
     $frequency_names = hash( 'immediate', 'At once'|i18n( 'design/standard/mailpreferences' ),
                              'daily', 'Daily summary'|i18n( 'design/standard/mailpreferences' ),
                              'weekly', 'Weekly summary'|i18n( 'design/standard/mailpreferences' ) )}

{include uri='design:mailpreferences/parts/notice.tpl' notice=first_set( $notice, false() ) notices=first_set( $notices, array() )}

{if $suppressed}
<div class="mp-notice mp-notice-warning" role="note">
    <p><b>{'No optional e-mail is sent to this address.'|i18n( 'design/standard/mailpreferences' )}</b>
    {'It is on our list of addresses that receive no optional e-mail, for example after a request to stop all e-mail or after messages to it could not be delivered. Essential messages about your account are still sent.'|i18n( 'design/standard/mailpreferences' )}
{if $mode|ne( 'admin' )}
    {'If you stopped all e-mail yourself, turning optional e-mail on again lifts the block. A block because messages could not be delivered stays until you contact us.'|i18n( 'design/standard/mailpreferences' )}
{/if}</p>
</div>
{/if}

{* 1. The main switch *}
<section class="mp-card mp-master" id="mp-master" aria-labelledby="mp-master-title">
    <div>
        <h2 id="mp-master-title">{'Send me optional e-mail'|i18n( 'design/standard/mailpreferences' )}</h2>
        <p><span class="mp-state">{if $master}{'On'|i18n( 'design/standard/mailpreferences' )}{else}{'Off'|i18n( 'design/standard/mailpreferences' )}{/if}.</span>
        {if $master}{'You receive the kinds of e-mail you turned on below.'|i18n( 'design/standard/mailpreferences' )}
        {else}{'You receive no optional e-mail. Your choices below are kept and come back when you turn it on again.'|i18n( 'design/standard/mailpreferences' )}{/if}</p>
    </div>
    <form method="post" action={$form_action|ezurl}>
        <input type="hidden" name="MailPreferencesForm" value="master" />
{if $master}
        <input class="mp-btn" type="submit" name="MasterOffButton" value="{'Turn off all optional e-mail'|i18n( 'design/standard/mailpreferences' )}" />
{else}
        <input class="mp-btn" type="submit" name="MasterOnButton" value="{'Turn on optional e-mail'|i18n( 'design/standard/mailpreferences' )}" />
{/if}
    </form>
</section>

{* 2. The categories: equal switches, each with what it is and how often *}
<section class="mp-card{if $paused} mp-paused{/if}" id="mp-categories" aria-labelledby="mp-categories-title">
    <h2 id="mp-categories-title">{'What you want to receive'|i18n( 'design/standard/mailpreferences' )}</h2>
{if $categories|count|eq( 0 )}
    <p class="mp-lead">{'This site sends no optional e-mail at the moment.'|i18n( 'design/standard/mailpreferences' )}</p>
{else}
    <p class="mp-lead">{'Turn on only what you want. Nothing is on until you turn it on, and you can turn anything off here at any time.'|i18n( 'design/standard/mailpreferences' )}
    {if $paused}<b>{'Optional e-mail is off at the moment, so none of these is sent.'|i18n( 'design/standard/mailpreferences' )}</b>{/if}</p>
    <form method="post" action={$form_action|ezurl}>
        <input type="hidden" name="MailPreferencesForm" value="categories" />
        <ul class="mp-list">
{foreach $categories as $category}
            <li class="mp-cat">
                <input type="hidden" name="CategoryShown[]" value="{$category.identifier|wash}" />
                <span class="mp-switch">
                    <input type="checkbox" role="switch" id="mp-cat-{$category.identifier|wash}" name="Category[{$category.identifier|wash}]" value="1"{if or( $category.on, $category.pending )} checked="checked"{/if} aria-describedby="mp-cat-{$category.identifier|wash}-desc" />
                    <span class="mp-track" aria-hidden="true"></span>
                </span>
                <div class="mp-cat-text">
                    <label class="mp-cat-name" for="mp-cat-{$category.identifier|wash}">{$category.name|wash}
                    {if $category.pending}<span class="mp-badge pending">{'Waiting for your confirmation'|i18n( 'design/standard/mailpreferences' )}</span>
                    {elseif $category.on}<span class="mp-badge on">{'On'|i18n( 'design/standard/mailpreferences' )}</span>
                    {else}<span class="mp-badge">{'Off'|i18n( 'design/standard/mailpreferences' )}</span>{/if}</label>
                    <p class="mp-cat-desc" id="mp-cat-{$category.identifier|wash}-desc">{$category.description|wash}</p>
{if $category.pending}
                    <p class="mp-hint">{'We sent a confirmation link to %email. This starts once you open it.'|i18n( 'design/standard/mailpreferences',, hash( '%email', $email ) )|wash}</p>
{elseif $category.double_opt_in}
                    <p class="mp-hint">{'When you turn this on, we first send you a link to confirm it.'|i18n( 'design/standard/mailpreferences' )}</p>
{/if}
{if $category.frequencies|count|gt( 1 )}
                    <fieldset class="mp-freq">
                        <legend>{'How often:'|i18n( 'design/standard/mailpreferences' )}</legend>
    {foreach $category.frequencies as $frequency}
                        <label><input type="radio" name="Frequency[{$category.identifier|wash}]" value="{$frequency|wash}"{if $frequency|eq( $category.frequency )} checked="checked"{/if} /> {if is_set( $frequency_names[$frequency] )}{$frequency_names[$frequency]}{else}{$frequency|wash}{/if}</label>
    {/foreach}
                    </fieldset>
{/if}
                </div>
            </li>
{/foreach}
        </ul>
        <div class="mp-actions">
            <input class="mp-btn primary" type="submit" name="StoreButton" value="{'Save my choices'|i18n( 'design/standard/mailpreferences' )}" />
        </div>
    </form>
{/if}
</section>

{* 3. Essential mail: not switchable, and why *}
{if $essential|count|gt( 0 )}
<section class="mp-card" id="mp-essential" aria-labelledby="mp-essential-title">
    <h2 id="mp-essential-title">{'Always sent'|i18n( 'design/standard/mailpreferences' )}</h2>
    <p class="mp-lead">{'These messages are needed to run your account or are required by law. They cannot be turned off, but they never contain advertising.'|i18n( 'design/standard/mailpreferences' )}</p>
    <ul class="mp-essential">
{foreach $essential as $category}
        <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 1.5a5.5 5.5 0 0 0-5.5 5.5v3H5a1.5 1.5 0 0 0-1.5 1.5v10A1.5 1.5 0 0 0 5 23h14a1.5 1.5 0 0 0 1.5-1.5v-10A1.5 1.5 0 0 0 19 10h-1.5V7A5.5 5.5 0 0 0 12 1.5zm-3.5 5.5a3.5 3.5 0 1 1 7 0v3h-7V7z"/></svg>
            <div><b>{$category.name|wash}</b> <span>{$category.description|wash}</span></div></li>
{/foreach}
    </ul>
</section>
{/if}

{* 4. The data: what was agreed, when and how; a download of everything *}
<section class="mp-card" id="mp-data" aria-labelledby="mp-data-title">
    <h2 id="mp-data-title">{if $mode|eq( 'admin' )}{'Consent history'|i18n( 'design/standard/mailpreferences' )}{else}{'Your e-mail data'|i18n( 'design/standard/mailpreferences' )}{/if}</h2>
    <p class="mp-lead">{'Every change is recorded with its time, where it was made and the exact text that was shown.'|i18n( 'design/standard/mailpreferences' )}</p>
{if $export}
    <div class="mp-actions">
        <a class="mp-btn" href={$export.json|ezurl} download="download">{if $mode|eq( 'admin' )}{'Download as JSON'|i18n( 'design/standard/mailpreferences' )}{else}{'Download my e-mail data (JSON)'|i18n( 'design/standard/mailpreferences' )}{/if}</a>
        <a class="mp-btn" href={$export.csv|ezurl} download="download">{if $mode|eq( 'admin' )}{'Download as CSV'|i18n( 'design/standard/mailpreferences' )}{else}{'Download my e-mail data (CSV)'|i18n( 'design/standard/mailpreferences' )}{/if}</a>
    </div>
{/if}
{if $history|count|eq( 0 )}
    <p class="mp-hint">{'No changes recorded yet.'|i18n( 'design/standard/mailpreferences' )}</p>
{else}
    <div class="mp-scroll">
    <table class="mp-table mp-stack">
{if $history_total|gt( $history|count )}
        <caption class="mp-hint" style="caption-side: bottom; text-align: left;">{'The newest %shown of %total records. The download has all of them.'|i18n( 'design/standard/mailpreferences',, hash( '%shown', $history|count, '%total', $history_total ) )}</caption>
{/if}
        <thead><tr>
            <th scope="col">{'When'|i18n( 'design/standard/mailpreferences' )}</th>
            <th scope="col">{'What'|i18n( 'design/standard/mailpreferences' )}</th>
            <th scope="col">{'Change'|i18n( 'design/standard/mailpreferences' )}</th>
            <th scope="col">{'Where'|i18n( 'design/standard/mailpreferences' )}</th>
            <th scope="col">{'Text shown'|i18n( 'design/standard/mailpreferences' )}</th>
        </tr></thead>
        <tbody>
{foreach $history as $row}
        <tr>
            <td class="mp-num" data-label="{'When'|i18n( 'design/standard/mailpreferences' )}">{if $row.time}{$row.time|l10n( 'shortdatetime' )}{/if}</td>
            <td data-label="{'What'|i18n( 'design/standard/mailpreferences' )}">{$row.category|wash}</td>
            <td data-label="{'Change'|i18n( 'design/standard/mailpreferences' )}">{$row.change|wash}</td>
            <td data-label="{'Where'|i18n( 'design/standard/mailpreferences' )}">{$row.source_name|wash}</td>
            <td class="mp-wording" data-label="{'Text shown'|i18n( 'design/standard/mailpreferences' )}">{$row.wording|wash}</td>
        </tr>
{/foreach}
        </tbody>
    </table>
    </div>
{/if}
</section>
{undef $paused $frequency_names}
