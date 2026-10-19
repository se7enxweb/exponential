{* exphelp/parts/support: the "Support Exponential" block at the bottom of the help dashboard and on the landing pages
   that list the block support. It asks for sustaining sponsorships through GitHub Sponsors of the two organizations
   behind Exponential, 7x and the Exponential Foundation, with what the funding of each pays for. Everything comes
   from exphelp.ini [SupportSettings] and [SupportOrganization_<id>] (Exponential\View\Kernel\Exphelp\Audience::support());
   only https addresses are shown. Styles: stylesheets/modules/help.css (.a4-help-support*); no inline CSS or script.
   The same file is in design/admin and design/admin4.

   Variables: support (title, text, organizations: id, name, url, button, page, mission, funds; more). *}
{def $help_sctx = 'kernel/exphelp'}
<section class="a4-help-support" aria-labelledby="help-support-title">
    <div class="a4-help-support-head">
        <span class="a4-help-tile" aria-hidden="true"><span class="a4-icon a4-bi-piggy-bank"></span></span>
        <div>
            <h2 id="help-support-title">{$support.title|wash}</h2>
            {if $support.text|ne( '' )}<p>{$support.text|wash}</p>{/if}
        </div>
    </div>
    <ul class="a4-help-support-orgs">
    {foreach $support.organizations as $org}
        <li class="a4-card a4-help-support-org">
            <h3 class="a4-card-title">{$org.name|wash}</h3>
            {if $org.mission|ne( '' )}<p>{$org.mission|wash}</p>{/if}
            {if $org.funds|count|gt( 0 )}
            <p class="a4-help-support-funds-head">{'Your sponsorship pays for:'|i18n( $help_sctx )}</p>
            <ul class="a4-help-support-funds">
                {foreach $org.funds as $fund}<li><span class="a4-icon a4-bi-check-lg" aria-hidden="true"></span>{$fund|wash}</li>{/foreach}
            </ul>
            {/if}
            <div class="a4-help-support-actions">
                <a class="a4-btn a4-btn-primary" href="{$org.url|wash}" rel="noopener noreferrer" target="_blank"><span class="a4-icon a4-bi-piggy-bank" aria-hidden="true"></span>{cond( $org.button|ne( '' ), $org.button, $org.name )|wash}<span class="a4-sr-only"> {'(opens in a new window)'|i18n( $help_sctx )}</span></a>
                {if $org.page|ne( '' )}<a class="a4-btn" href={concat( '/exphelp/viewer/', $org.page )|ezurl}>{'About %name'|i18n( $help_sctx,, hash( '%name', $org.name ) )|wash}</a>{/if}
            </div>
        </li>
    {/foreach}
    </ul>
    <p class="a4-help-support-foot">
        {'A monthly sponsorship, even a small one, is planned income: it pays for work that one-time gifts cannot keep going.'|i18n( $help_sctx )}
        {if $support.more|ne( '' )}<a href={concat( '/exphelp/viewer/', $support.more )|ezurl}>{'What sponsorship funds'|i18n( $help_sctx )}</a>{/if}
    </p>
</section>
{undef $help_sctx}
