{* mailpreferences/admin/suppression: addresses that get no optional e-mail at all. Only a hash of each address is
   stored, so the list cannot show addresses; an address typed in the check form is hashed and looked up.
   Variables: rows (hash( hash, reason, note, created )), total, offset, limit, reason_names (hash( reason => label )),
   add_reasons (the reasons an administrator can choose),
   check (hash( email, suppressed, row ) after a check, or false), notice, page_uri. *}
{include uri='design:mailpreferences/parts/page_start.tpl'
         title='E-mail preferences: suppression list'|i18n( 'design/admin/mailpreferences' )
         intro='No optional e-mail is sent to an address on this list: hard bounces, complaints, requests to stop all e-mail and legal requests. Essential e-mail still goes. Only a hash of the address is kept, never the address itself.'|i18n( 'design/admin/mailpreferences' )
         crumb=false() wide=true() admin_tab='suppression'}

{include uri='design:mailpreferences/parts/notice.tpl' notice=first_set( $notice, false() )}

<div class="mp-layout two">
<section class="mp-card">
    <h2>{'Check an address'|i18n( 'design/admin/mailpreferences' )}</h2>
    <form method="post" action={'mailpreferences/admin/suppression'|ezurl}>
        <div class="mp-field">
            <label for="mp-sup-check">{'E-mail address'|i18n( 'design/admin/mailpreferences' )}</label>
            <input type="email" id="mp-sup-check" name="CheckEmail" value="{if $check}{$check.email|wash}{/if}" required="required" />
        </div>
        <div class="mp-actions"><input class="mp-btn" type="submit" name="CheckButton" value="{'Check'|i18n( 'design/admin/mailpreferences' )}" /></div>
    </form>
{if $check}
    <div class="mp-notice {if $check.suppressed}mp-notice-warning{else}mp-notice-info{/if}" role="status" style="margin-top:.8em;">
        <p>{if $check.suppressed}{'This address is on the list (%reason, since %date).'|i18n( 'design/admin/mailpreferences',, hash( '%reason', cond( is_set( $reason_names[$check.row.reason] ), $reason_names[$check.row.reason], $check.row.reason ), '%date', cond( $check.row.created, $check.row.created|l10n( 'shortdate' ), '?' ) ) )|wash}{else}{'This address is not on the list.'|i18n( 'design/admin/mailpreferences' )}{/if}</p>
    </div>
{if $check.suppressed}
    <form method="post" action={'mailpreferences/admin/suppression'|ezurl}>
        <input type="hidden" name="LiftEmail" value="{$check.email|wash}" />
        <div class="mp-actions"><input class="mp-btn" type="submit" name="LiftButton" value="{'Lift the block for this address'|i18n( 'design/admin/mailpreferences' )}" /></div>
    </form>
{/if}
{/if}
</section>

<section class="mp-card">
    <h2>{'Add an address'|i18n( 'design/admin/mailpreferences' )}</h2>
    <form method="post" action={'mailpreferences/admin/suppression'|ezurl}>
        <div class="mp-field">
            <label for="mp-sup-email">{'E-mail address'|i18n( 'design/admin/mailpreferences' )}</label>
            <input type="email" id="mp-sup-email" name="AddEmail" value="" required="required" />
        </div>
        <div class="mp-field">
            <label for="mp-sup-reason">{'Reason'|i18n( 'design/admin/mailpreferences' )}</label>
            <select id="mp-sup-reason" name="Reason">
{foreach $add_reasons as $reason => $label}
                <option value="{$reason|wash}">{$label|wash}</option>
{/foreach}
            </select>
        </div>
        <div class="mp-field">
            <label for="mp-sup-note">{'Note (optional; no personal data)'|i18n( 'design/admin/mailpreferences' )}</label>
            <input type="text" id="mp-sup-note" name="Note" value="" maxlength="255" />
        </div>
        <div class="mp-actions"><input class="mp-btn primary" type="submit" name="AddButton" value="{'Add to the list'|i18n( 'design/admin/mailpreferences' )}" /></div>
    </form>
</section>
</div>

<section class="mp-card">
    <h2>{'On the list'|i18n( 'design/admin/mailpreferences' )} <span class="mp-badge">{$total}</span></h2>
{if $rows|count|eq( 0 )}
    <p class="mp-hint">{'The list is empty.'|i18n( 'design/admin/mailpreferences' )}</p>
{else}
    <div class="mp-scroll">
    <table class="mp-table mp-stack">
        <thead><tr>
            <th scope="col">{'Since'|i18n( 'design/admin/mailpreferences' )}</th>
            <th scope="col">{'Reason'|i18n( 'design/admin/mailpreferences' )}</th>
            <th scope="col">{'Note'|i18n( 'design/admin/mailpreferences' )}</th>
            <th scope="col">{'Address hash'|i18n( 'design/admin/mailpreferences' )}</th>
            <th scope="col"><span class="mp-sr">{'Actions'|i18n( 'design/admin/mailpreferences' )}</span></th>
        </tr></thead>
        <tbody>
{foreach $rows as $row}
        <tr>
            <td class="mp-num" data-label="{'Since'|i18n( 'design/admin/mailpreferences' )}">{if $row.created}{$row.created|l10n( 'shortdatetime' )}{/if}</td>
            <td data-label="{'Reason'|i18n( 'design/admin/mailpreferences' )}">{if is_set( $reason_names[$row.reason] )}{$reason_names[$row.reason]}{else}{$row.reason|wash}{/if}</td>
            <td data-label="{'Note'|i18n( 'design/admin/mailpreferences' )}">{$row.note|wash}</td>
            <td data-label="{'Address hash'|i18n( 'design/admin/mailpreferences' )}"><code title="{$row.hash|wash}">{$row.hash|shorten( 16, '…' )|wash}</code></td>
            <td>
                <form method="post" action={$page_uri|ezurl}>
                    <input type="hidden" name="LiftHash" value="{$row.hash|wash}" />
                    <input class="mp-btn small" type="submit" name="LiftButton" value="{'Lift'|i18n( 'design/admin/mailpreferences' )}" />
                </form>
            </td>
        </tr>
{/foreach}
        </tbody>
    </table>
    </div>
    {include uri='design:mailpreferences/parts/pager.tpl' page_uri=$page_uri total=$total offset=$offset limit=$limit}
{/if}
</section>

{include uri='design:mailpreferences/parts/page_end.tpl'}
