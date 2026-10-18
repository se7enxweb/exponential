{* exphelp/dashboard (doc/features/6.0/help-system.md): the help start page. Variables: help_audiences,
   help_categories, help_featured, help_recent, help_tree, help_trackers, help_search_url, help_report_url
   (kernel/private/classes/views/exphelp/dashboard.php). Skeleton: workstream D. *}
<div class="a4-page a4-help a4-help-dashboard">
    <h1>{'Help'|i18n( 'kernel/exphelp' )}</h1>
    <form class="a4-help-search" method="get" action={$help_search_url|ezurl} role="search">
        <label for="exphelp-q">{'Search the help'|i18n( 'kernel/exphelp' )}</label>
        <input type="search" id="exphelp-q" name="q" value="" />
        <button type="submit" class="a4-btn a4-btn-primary">{'Search'|i18n( 'kernel/exphelp' )}</button>
    </form>
    <p><a class="a4-btn" href={$help_report_url|ezurl}>{'Report an error'|i18n( 'kernel/exphelp' )}</a></p>
</div>
