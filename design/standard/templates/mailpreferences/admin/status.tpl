{* mailpreferences/admin/status: the state of the e-mail preferences for administrators.
   Variables: problems (hash( level: error|warning|info, text )), stats (hash( label, value, level: ''|bad )),
   facts (hash( label, value )), notice (or false), links (hash( url, text ): the related pages). *}
{include uri='design:mailpreferences/parts/page_start.tpl'
         title='E-mail preferences: status'|i18n( 'design/admin/mailpreferences' )
         intro='Every e-mail the system sends passes the mail gate: it checks the main switch, the category and the suppression list of the recipient, and adds the footer and the unsubscribe links to optional e-mail. This page shows whether that works and what it did.'|i18n( 'design/admin/mailpreferences' )
         crumb=false() wide=true() admin_tab='status'}

{include uri='design:mailpreferences/parts/notice.tpl' notice=first_set( $notice, false() )}

{if $problems|count|gt( 0 )}
    <ul class="mp-problems">
{foreach $problems as $problem}
        <li class="{$problem.level|wash}"><b>{if $problem.level|eq( 'error' )}{'Problem'|i18n( 'design/admin/mailpreferences' )}{elseif $problem.level|eq( 'warning' )}{'Attention'|i18n( 'design/admin/mailpreferences' )}{else}{'Note'|i18n( 'design/admin/mailpreferences' )}{/if}:</b> {$problem.text|wash}</li>
{/foreach}
    </ul>
{else}
    <div class="mp-notice mp-notice-success" role="status"><p>{'Nothing looks wrong.'|i18n( 'design/admin/mailpreferences' )}</p></div>
{/if}

{if $stats|count|gt( 0 )}
    <ul class="mp-stats">
{foreach $stats as $stat}
        <li><div class="mp-stat{if $stat.level} {$stat.level|wash}{/if}"><strong>{$stat.value|wash}</strong><span>{$stat.label|wash}</span></div></li>
{/foreach}
    </ul>
{/if}

{if $facts|count|gt( 0 )}
    <section class="mp-card">
        <h2>{'Settings in use'|i18n( 'design/admin/mailpreferences' )}</h2>
        <dl class="mp-facts">
{foreach $facts as $fact}
            <div><dt>{$fact.label|wash}</dt><dd>{$fact.value|wash}</dd></div>
{/foreach}
        </dl>
        <p class="mp-hint">{'The settings are in mailpreferences.ini; the organisation, the postal address, the bounce mailbox and the site secret belong in a settings override.'|i18n( 'design/admin/mailpreferences' )}</p>
    </section>
{/if}

{if $links|count|gt( 0 )}
    <section class="mp-card">
        <h2>{'Related pages'|i18n( 'design/admin/mailpreferences' )}</h2>
        <div class="mp-actions">
{foreach $links as $link}
            <a class="mp-btn small" href={$link.url|ezurl}>{$link.text|wash}</a>
{/foreach}
        </div>
    </section>
{/if}

{include uri='design:mailpreferences/parts/page_end.tpl'}
