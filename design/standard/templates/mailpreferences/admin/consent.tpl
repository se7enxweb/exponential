{* mailpreferences/admin/consent: the consent log, every change of every person, with a CSV export of what the
   filters select. Variables: rows (hash( time, who, user_id, category, action, source, wording, ip, actor )), total,
   offset, limit, filters (hash( person, category, source, from, to )), category_names (hash( identifier => name )),
   source_names (hash( source => label )), export_uri, query, page_uri, notice. Each row is
   Exponential\Service\MailPreferencesPage::logRow() with who and actor added. *}
{include uri='design:mailpreferences/parts/page_start.tpl'
         title='E-mail preferences: consent log'|i18n( 'design/admin/mailpreferences' )
         intro='Every change of an e-mail preference: when, by whom, where, the exact text the person saw and the address the request came from. The log is kept for the life of the account and the retention time after it; a removed account leaves an anonymised record that proves the withdrawal.'|i18n( 'design/admin/mailpreferences' )
         crumb=false() wide=true() admin_tab='consent'}

{include uri='design:mailpreferences/parts/notice.tpl' notice=first_set( $notice, false() )}

<section class="mp-card">
    <h2>{'Filter'|i18n( 'design/admin/mailpreferences' )}</h2>
    <form method="get" action={'mailpreferences/admin/consent'|ezurl}>
        <div class="mp-filters">
            <div class="mp-field">
                <label for="mp-log-person">{'E-mail address or user ID'|i18n( 'design/admin/mailpreferences' )}</label>
                <input type="text" id="mp-log-person" name="person" value="{$filters.person|wash}" />
            </div>
            <div class="mp-field">
                <label for="mp-log-category">{'Category'|i18n( 'design/admin/mailpreferences' )}</label>
                <select id="mp-log-category" name="category">
                    <option value="">{'All'|i18n( 'design/admin/mailpreferences' )}</option>
{foreach $category_names as $identifier => $name}
                    <option value="{$identifier|wash}"{if $filters.category|eq( $identifier )} selected="selected"{/if}>{$name|wash}</option>
{/foreach}
                </select>
            </div>
            <div class="mp-field">
                <label for="mp-log-source">{'Where'|i18n( 'design/admin/mailpreferences' )}</label>
                <select id="mp-log-source" name="source">
                    <option value="">{'All'|i18n( 'design/admin/mailpreferences' )}</option>
{foreach $source_names as $source => $label}
                    <option value="{$source|wash}"{if $filters.source|eq( $source )} selected="selected"{/if}>{$label|wash}</option>
{/foreach}
                </select>
            </div>
            <div class="mp-field">
                <label for="mp-log-from">{'From'|i18n( 'design/admin/mailpreferences' )}</label>
                <input type="date" id="mp-log-from" name="from" value="{$filters.from|wash}" />
            </div>
            <div class="mp-field">
                <label for="mp-log-to">{'To'|i18n( 'design/admin/mailpreferences' )}</label>
                <input type="date" id="mp-log-to" name="to" value="{$filters.to|wash}" />
            </div>
        </div>
        <div class="mp-actions" style="margin-top:0;">
            <input class="mp-btn primary" type="submit" value="{'Show'|i18n( 'design/admin/mailpreferences' )}" />
            <a class="mp-btn" href={'mailpreferences/admin/consent'|ezurl}>{'Clear'|i18n( 'design/admin/mailpreferences' )}</a>
{if $export_uri}
            <a class="mp-btn" href={$export_uri|ezurl} download="download">{'Export as CSV'|i18n( 'design/admin/mailpreferences' )}</a>
{/if}
        </div>
    </form>
</section>

<section class="mp-card">
    <h2>{'Records'|i18n( 'design/admin/mailpreferences' )} <span class="mp-badge">{$total}</span></h2>
{if $rows|count|eq( 0 )}
    <p class="mp-hint">{'No records match.'|i18n( 'design/admin/mailpreferences' )}</p>
{else}
    <div class="mp-scroll">
    <table class="mp-table mp-stack">
        <thead><tr>
            <th scope="col">{'When'|i18n( 'design/admin/mailpreferences' )}</th>
            <th scope="col">{'Person'|i18n( 'design/admin/mailpreferences' )}</th>
            <th scope="col">{'Category'|i18n( 'design/admin/mailpreferences' )}</th>
            <th scope="col">{'Change'|i18n( 'design/admin/mailpreferences' )}</th>
            <th scope="col">{'Where'|i18n( 'design/admin/mailpreferences' )}</th>
            <th scope="col">{'Text shown'|i18n( 'design/admin/mailpreferences' )}</th>
            <th scope="col">{'IP address'|i18n( 'design/admin/mailpreferences' )}</th>
        </tr></thead>
        <tbody>
{foreach $rows as $row}
        <tr>
            <td class="mp-num" data-label="{'When'|i18n( 'design/admin/mailpreferences' )}">{if $row.time}{$row.time|l10n( 'shortdatetime' )}{/if}</td>
            <td data-label="{'Person'|i18n( 'design/admin/mailpreferences' )}">{if $row.user_id}<a href={concat( 'mailpreferences/admin/user/', $row.user_id )|ezurl}>{$row.who|wash}</a>{else}{$row.who|wash}{/if}{if $row.actor} <span class="mp-wording">({'by %actor'|i18n( 'design/admin/mailpreferences',, hash( '%actor', $row.actor ) )|wash})</span>{/if}</td>
            <td data-label="{'Category'|i18n( 'design/admin/mailpreferences' )}">{$row.category|wash}</td>
            <td data-label="{'Change'|i18n( 'design/admin/mailpreferences' )}">{$row.change|wash}</td>
            <td data-label="{'Where'|i18n( 'design/admin/mailpreferences' )}">{$row.source_name|wash}</td>
            <td class="mp-wording" data-label="{'Text shown'|i18n( 'design/admin/mailpreferences' )}">{$row.wording|wash}</td>
            <td data-label="{'IP address'|i18n( 'design/admin/mailpreferences' )}">{if $row.ip}<code>{$row.ip|wash}</code>{/if}</td>
        </tr>
{/foreach}
        </tbody>
    </table>
    </div>
    {include uri='design:mailpreferences/parts/pager.tpl' page_uri=$page_uri total=$total offset=$offset limit=$limit query=$query}
{/if}
</section>

{include uri='design:mailpreferences/parts/page_end.tpl'}
