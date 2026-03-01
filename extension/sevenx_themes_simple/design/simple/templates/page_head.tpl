{default enable_help=true() enable_link=true()}
{def $module_result_node_id=cond( is_set($module_result.node_id), $module_result.node_id )}
{let name=Path
     path=$module_result.path
     reverse_path=array()}
{def $desc=ezini( 'SiteSettings', 'SiteDescription', 'site.ini')}
  {section show=is_set($module_result.title_path)}
    {set path=$module_result.title_path}
  {/section}
  {section loop=$:path}
    {if and( $:item.text|ne("Home"), $:item.text|ne("Websites") )}{set reverse_path=$:reverse_path|array_prepend($:item)}{/if}
  {/section}
{set-block scope=root variable=site_title}
{if $Path:reverse_path|ne(array())}
{section loop=$Path:reverse_path}
{if $:item.text|ne( "Home" )}
{$:item.text|wash}
{/if}
{delimiter} / {/delimiter}
{/section} - {/if}{$site.title|wash}
{/set-block}
{/let}

{def $meta = metadata( $module_result_node_id ) }{if $meta}{if $meta.title}

    <title>{$meta.title|wash}</title>

    <meta property="og:title" content="{$meta.title|wash}"/>
    <meta name="twitter:title" content="{$meta.title|wash}"/>{else}
    <title>{$site_title}</title>

    <meta property="og:title" content="{$site_title}"/>
    <meta name="twitter:title" content="{$site_title}"/>
    {/if}
    {if $meta.keywords}

    <meta name="keywords" content="{$meta.keywords|implode(',')|wash}" />{/if}
    {if $meta.description}

    <meta name="description" content="{$meta.description|wash}" />
    <meta property="og:description" content="{$meta.description|wash}"/>
    <meta name="twitter:description" content="{$meta.description|wash}"/>{else}
    <meta name="description" content="{$desc|wash}" />
    <meta property="og:description" content="{$desc|wash}"/>
    <meta name="twitter:description" content="{$desc|wash}"/>
    {/if}
    <meta property="og:type" content="website" />

    <meta property="og:url" content="https://{ezsys('hostname')}/{$site.uri.original_uri}" />
    <meta property="og:image" content="https://{ezsys('hostname')}{"images/favicon/favicon-banner.png"|ezdesign(no)}" />
    <meta property="og:site_name" content="{ezini( 'SiteSettings', 'SiteName', 'site.ini')}" />
    <meta property="twitter:image" content="https://{ezsys('hostname')}{"images/favicon/favicon-banner.png"|ezdesign(no)}" />
    {else}

    <title>{$site_title}</title>

    <meta property="og:title" content="{$site_title}"/>
    <meta name="twitter:title" content="{$site_title}"/>
    <meta name="description" content="{$desc|wash}" />
    <meta property="og:description" content="{$desc|wash}"/>
    <meta name="twitter:description" content="{$desc|wash}"/>
    <meta property="og:type" content="website" />
    <meta property="og:url" content="https://{ezsys('hostname')}/{$site.uri.original_uri}" />
    <meta property="og:image" content="https://{ezsys('hostname')}{"images/favicon/favicon-banner.png"|ezdesign(no)}" />
    <meta property="og:site_name" content="{ezini( 'SiteSettings', 'SiteName', 'site.ini')}" />
    <meta property="twitter:image" content="https://{ezsys('hostname')}{"images/favicon/favicon-banner.png"|ezdesign(no)}" />{* foreach $site.meta as $key => $item }<meta name="{$key|wash}" content="{$item|wash}" />{/foreach *}{/if}
    {section show=and(is_set($#Header:extra_data),is_array($#Header:extra_data))}
      {section name=ExtraData loop=$#Header:extra_data}

      {$:item}{/section}{/section}{* check if we need a http-equiv refresh *}
    {section show=$site.redirect}

    <meta http-equiv="Refresh" content="{$site.redirect.timer}; URL={$site.redirect.location}" />{/section}
{section name=HTTP loop=$site.http_equiv}
<meta http-equiv="{$HTTP:key|wash}" content="{$HTTP:item|wash}" />
    {/section}

    {section name=meta loop=$site.meta}
<meta name="{$meta:key|wash}" content="{$meta:item|wash}" />
    {/section}
<meta name="MSSmartTagsPreventParsing" content="TRUE" />
    <meta name="generator" content="Exponential" />

{section show=$enable_link}{include uri="design:link.tpl" enable_help=$enable_help enable_link=$enable_link}{/section}
{/default}
