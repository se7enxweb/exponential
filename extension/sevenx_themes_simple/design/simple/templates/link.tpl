{default enable_print=true()}

<link rel="Home" href={"/"|ezurl} title="{'%sitetitle front page'|i18n('design/ezwebin/link',,hash('%sitetitle',$site.title))}" />
<link rel="Index" href={"/"|ezurl} />
<link rel="Top"  href={"/"|ezurl} title="{$site_title}" />
<link rel="Search" href={"content/advancedsearch"|ezurl} title="{'Search %sitetitle'|i18n('design/ezwebin/link',,hash('%sitetitle',$site.title))}" />
<!-- favicon: START -->
<link rel="apple-touch-icon" sizes="57x57" href="{"favicon/apple-icon-57x57.png"|ezimage(no)}">
<link rel="apple-touch-icon" sizes="60x60" href="{"favicon/apple-icon-60x60.png"|ezimage(no)}">
<link rel="apple-touch-icon" sizes="72x72" href="{"favicon/apple-icon-72x72.png"|ezimage(no)}">
<link rel="apple-touch-icon" sizes="76x76" href="{"favicon/apple-icon-76x76.png"|ezimage(no)}">
<link rel="apple-touch-icon" sizes="114x114" href="{"favicon/apple-icon-114x114.png"|ezimage(no)}">
<link rel="apple-touch-icon" sizes="120x120" href="{"favicon/apple-icon-120x120.png"|ezimage(no)}">
<link rel="apple-touch-icon" sizes="144x144" href="{"favicon/apple-icon-144x144.png"|ezimage(no)}">
<link rel="apple-touch-icon" sizes="152x152" href="{"favicon/apple-icon-152x152.png"|ezimage(no)}">
<link rel="apple-touch-icon" sizes="180x180" href="{"favicon/apple-icon-180x180.png"|ezimage(no)}">
<link rel="icon" type="image/png" sizes="32x32" href="{"favicon/favicon-32x32.png"|ezimage(no)}">
<link rel="icon" type="image/png" sizes="16x16" href="{"favicon/favicon-16x16.png"|ezimage(no)}">
<link rel="manifest" href="{"images/favicon/site.webmanifest.json"|ezdesign(no)}">
<link rel="mask-icon" href="{"favicon/safari-pinned-tab.svg"|ezimage(no)}" color="#5bbad5">
<meta name="msapplication-TileColor" content="#9f00a7">
<meta name="theme-color" content="#ffffff">
<!-- favicon: END -->


<link rel="Copyright" href={"/ezinfo/copyright"|ezurl} />
<link rel="Author" href={"/ezinfo/about"|ezurl} />

{if and( is_set($pagedesign), $pagedesign.data_map.rss_feed.has_content )}
<link rel="Alternate" type="application/rss+xml" title="RSS" href="{$pagedesign.data_map.rss_feed.data_text|ezurl(no)}" />
{/if}

{if $enable_print}
<link rel="Alternate" href={concat("layout/set/print/", $site.uri.original_uri)|ezurl} media="print" title="{'Printable version'|i18n('design/ezwebin/link')}" />
{/if}

{/default}
