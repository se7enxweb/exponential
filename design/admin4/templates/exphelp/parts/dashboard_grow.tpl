{* exphelp/parts/dashboard_grow: the "Grow from user to developer" band of the help dashboard. Three steps from the
   administration to the code (doc/help/getting-started.md, doc/help/develop-with-git.md, contributing back) and links
   to the Git guide and the installation chapter. Included by exphelp/dashboard.tpl; the classic admin has the same
   file. Styles: stylesheets/modules/help.css (.a4-help-grow*); no inline CSS or script. *}
{def $help_grow_ctx = 'kernel/exphelp'}
<section class="a4-help-grow" aria-labelledby="help-dash-grow">
    <span class="a4-help-tile" aria-hidden="true"><span class="a4-icon a4-bi-git"></span></span>
    <div class="a4-help-grow-main">
        <h2 id="help-dash-grow">{'Grow from user to developer'|i18n( $help_grow_ctx )}</h2>
        <p>{'Exponential is free software with its full history on GitHub. Install it as Git clones, the recommended way to build websites, and you can follow every change, try fixes before they are released and send your own improvements back.'|i18n( $help_grow_ctx )}</p>
        <ol class="a4-help-grow-steps">
            <li><a href={'/exphelp/viewer/help/getting-started.md'|ezurl}><span class="a4-help-grow-num" aria-hidden="true">1</span><span class="a4-help-grow-step"><strong>{'Use the administration'|i18n( $help_grow_ctx )}</strong> {'Sign in, edit and publish.'|i18n( $help_grow_ctx )}</span></a></li>
            <li><a href={'/exphelp/viewer/help/develop-with-git.md'|ezurl}><span class="a4-help-grow-num" aria-hidden="true">2</span><span class="a4-help-grow-step"><strong>{'Install with Git'|i18n( $help_grow_ctx )}</strong> {'The kernel and every extension as clones with history.'|i18n( $help_grow_ctx )}</span></a></li>
            <li><a href={'/exphelp/viewer/help/develop-with-git.md#contribute-a-change-with-a-pull-request'|ezurl}><span class="a4-help-grow-num" aria-hidden="true">3</span><span class="a4-help-grow-step"><strong>{'Contribute back'|i18n( $help_grow_ctx )}</strong> {'Fork, fix and open a pull request.'|i18n( $help_grow_ctx )}</span></a></li>
        </ol>
    </div>
    <div class="a4-help-grow-actions">
        <a class="a4-btn a4-btn-primary" href={'/exphelp/viewer/help/develop-with-git.md'|ezurl}><span class="a4-icon a4-bi-git" aria-hidden="true"></span>{'Develop with Git'|i18n( $help_grow_ctx )}</a>
        <a class="a4-btn" href={'/exphelp/viewer/install/03-getting-the-code.md'|ezurl}><span class="a4-icon a4-bi-box-seam" aria-hidden="true"></span>{'Installation guide'|i18n( $help_grow_ctx )}</a>
    </div>
</section>
{undef $help_grow_ctx}
