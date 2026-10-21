{* exphelp/parts/dashboard_about: the "Who makes Exponential" band of the help dashboard, linking the two pages about
   the people behind the software (doc/eng-us/help/who-is-7x.md, doc/eng-us/help/who-is-the-exponential-foundation.md),
   then the Partners, the Community and the Ecosystem, each to its audience's landing page (exphelp/audience/<id>), or
   to its search while [LandingSettings] Enabled is disabled (landing_on, given by exphelp/dashboard.tpl). Included by
   exphelp/dashboard.tpl right below the search hero; the classic admin has the same file. Styles:
   stylesheets/modules/help.css (.a4-help-about*); no inline CSS or script. *}
{def $help_about_ctx = 'kernel/exphelp'
     $help_about_landing = cond( is_set( $landing_on ), $landing_on, true() )}
<section class="a4-help-about" aria-labelledby="help-dash-about">
    <span class="a4-help-tile" aria-hidden="true"><span class="a4-icon a4-bi-info-circle"></span></span>
    <div class="a4-help-about-main">
        <h2 id="help-dash-about">{'Who makes Exponential'|i18n( $help_about_ctx )}</h2>
        <p>{'Exponential is free software with a history of nearly thirty years. Meet the company that leads it and the nonprofit foundation that operates the open source project.'|i18n( $help_about_ctx )}</p>
    </div>
    <ul class="a4-help-about-links">
        <li><a href={'/exphelp/viewer/help/who-is-7x.md'|ezurl}><span class="a4-icon a4-bi-people" aria-hidden="true"></span><span class="a4-help-about-text"><strong>{'Who is 7x?'|i18n( $help_about_ctx )}</strong> {'The company behind Exponential, from its first release to today.'|i18n( $help_about_ctx )}</span></a></li>
        <li><a href={'/exphelp/viewer/help/who-is-the-exponential-foundation.md'|ezurl}><span class="a4-icon a4-bi-globe" aria-hidden="true"></span><span class="a4-help-about-text"><strong>{'Who is the Exponential Foundation?'|i18n( $help_about_ctx )}</strong> {'The nonprofit, the product family and its commitments.'|i18n( $help_about_ctx )}</span></a></li>
        <li><a href={cond( $help_about_landing, '/exphelp/audience/partner', '/exphelp/search/(audience)/partner' )|ezurl}><span class="a4-icon a4-bi-building" aria-hidden="true"></span><span class="a4-help-about-text"><strong>{'Partners'|i18n( $help_about_ctx )}</strong> {'Exponential Partners contribute financially to the sustained future of the project, contribute code and more back to the project, and engage directly with 7x and the Exponential Foundation.'|i18n( $help_about_ctx )}</span></a></li>
        <li><a href={cond( $help_about_landing, '/exphelp/audience/community', '/exphelp/search/(audience)/community' )|ezurl}><span class="a4-icon a4-bi-chat-left-dots" aria-hidden="true"></span><span class="a4-help-about-text"><strong>{'Community'|i18n( $help_about_ctx )}</strong> {'Exponential Community members use, contribute to, promote and develop software using Exponential worldwide.'|i18n( $help_about_ctx )}</span></a></li>
        <li><a href={cond( $help_about_landing, '/exphelp/audience/ecosystem', '/exphelp/search/(audience)/ecosystem' )|ezurl}><span class="a4-icon a4-bi-globe2" aria-hidden="true"></span><span class="a4-help-about-text"><strong>{'Ecosystem'|i18n( $help_about_ctx )}</strong> {'The greater ecosystem extends beyond the community to cover all aspects of the Exponential world within the PHP stack: its users and creators.'|i18n( $help_about_ctx )}</span></a></li>
    </ul>
</section>
{undef $help_about_ctx $help_about_landing}
