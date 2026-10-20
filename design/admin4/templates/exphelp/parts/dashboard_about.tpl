{* exphelp/parts/dashboard_about: the "Who makes Exponential" band of the help dashboard, linking the two pages about
   the people behind the software (doc/eng-us/help/who-is-7x.md, doc/eng-us/help/who-is-the-exponential-foundation.md). Included by
   exphelp/dashboard.tpl right below the search hero; the classic admin has the same file. Styles:
   stylesheets/modules/help.css (.a4-help-about*); no inline CSS or script. *}
{def $help_about_ctx = 'kernel/exphelp'}
<section class="a4-help-about" aria-labelledby="help-dash-about">
    <span class="a4-help-tile" aria-hidden="true"><span class="a4-icon a4-bi-info-circle"></span></span>
    <div class="a4-help-about-main">
        <h2 id="help-dash-about">{'Who makes Exponential'|i18n( $help_about_ctx )}</h2>
        <p>{'Exponential is free software with a history of nearly thirty years. Meet the company that leads it and the nonprofit foundation that operates the open source project.'|i18n( $help_about_ctx )}</p>
    </div>
    <ul class="a4-help-about-links">
        <li><a href={'/exphelp/viewer/help/who-is-7x.md'|ezurl}><span class="a4-icon a4-bi-people" aria-hidden="true"></span><span class="a4-help-about-text"><strong>{'Who is 7x?'|i18n( $help_about_ctx )}</strong> {'The company behind Exponential, from its first release to today.'|i18n( $help_about_ctx )}</span></a></li>
        <li><a href={'/exphelp/viewer/help/who-is-the-exponential-foundation.md'|ezurl}><span class="a4-icon a4-bi-globe" aria-hidden="true"></span><span class="a4-help-about-text"><strong>{'Who is the Exponential Foundation?'|i18n( $help_about_ctx )}</strong> {'The nonprofit, the product family and its commitments.'|i18n( $help_about_ctx )}</span></a></li>
    </ul>
</section>
{undef $help_about_ctx}
