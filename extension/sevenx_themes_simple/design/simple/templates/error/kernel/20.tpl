{* BC Note: Replaced textual concept of 'module' for 'page' and dropped unrelated user unfriendly message about siteaccess URI string *}
<div class="warning">
<h2>{"Page not found"|i18n("design/standard/error/kernel")}</h2>
<p>{"The requested page %module could not be found."|i18n("design/standard/error/kernel",,
                                                            hash('%module',$parameters.module|wash))}</p>
<p>{"Possible reasons for this are"|i18n("design/standard/error/kernel")}:</p>
<ul>
    <li>{"The page URL was misspelled, try changing the URL."|i18n("design/standard/error/kernel")}</li>
    <li>{"The page no longer exists on this site."|i18n("design/standard/error/kernel")}</li>
</ul>
<p>{"Apologies for any inconvenience this may have caused"|i18n("design/standard/error/kernel")}</p>
</div>

{if $embed_content}

{$embed_content}
{/if}
