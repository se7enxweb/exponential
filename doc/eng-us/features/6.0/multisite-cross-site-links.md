# Links to content of another site of a multisite installation

Read this page if one installation serves several sites from one content tree (the multisite demo: Fit & Healthy
on `/site` or `/`, Bold Agency on `/bold`, their translations) and a page of one site lists content of another: a tag
page, a search result, a related item. Without the `ezurlmultisite` extension those links point into the wrong site
and answer 404.

## In short

| | |
|---|---|
| The problem | Each site hides its subtree's path with `PathPrefix`. A node of another site keeps that site's path in its URL alias, and `ezurl` puts the current siteaccess in front: `/bold/fit-healthy/video/...`, which does not exist (`kernel (20)`, module not found). |
| The fix | The extension `ezurlmultisite` (`se7enxweb/ezurlmultisite`, required by the root `composer.json`). `ezurl` and the media theme's own link operators (`exp_path`, `exp_url`, `content_link` ...) give the address on the siteaccess that serves the content: `/video/...` (or `/site/video/...`). |
| Unchanged | Links within the current site, module links (`user/login`, `content/search`), files and design paths, and every link made on a siteaccess that does not take part, which includes every administration siteaccess. |
| Configuration | `settings/override/ezurlmultisite.ini.append.php`, `[MultisiteSettings]`: `SiteAccessList[]` (the sites, in order of preference) and `RootNodeList[<siteaccess>]=<node id>` (the root node of each site's subtree). The multisite installer writes it with the node ids of the installation it creates. |
| Without the file | The public siteaccesses with a `PathPrefix` take part, each with the node of its `content.ini` `RootNode` or of its `IndexPage`. |
| Cost | No database query per link; the siteaccess table is read once per request (once per worker under Exponential Velocity). |

## How a link is resolved

1. The link is made on a siteaccess of `SiteAccessList[]`; otherwise `ezurl` is the kernel's.
2. For a node or object, the site is the one whose root node is the deepest on the node's path
   (`RootNodeList`). For a URL alias string (`$node.url_alias|ezurl`, the usual case), it is the site whose
   `PathPrefix` the alias starts with, the longest prefix first.
3. When that is another site, the address is built from that siteaccess's `SiteURL` with its `PathPrefix`
   removed: host-relative on the current host (`/bold/services`, so the scheme and the port of the page are kept,
   for example on Velocity's `:8080`), absolute on another host (`https://bold.example.com/services`).
4. A site and its translations share a subtree. The current siteaccess wins, then one in the current locale, then
   the first in `SiteAccessList[]`.

## Installing it on an existing site

```ini
# settings/override/site.ini.append.php
[ExtensionSettings]
ActiveExtensions[]=ezurlmultisite
```

```ini
# settings/override/ezurlmultisite.ini.append.php (the node ids of YOUR installation)
[MultisiteSettings]
SiteAccessList[]
SiteAccessList[]=site
SiteAccessList[]=bold
SiteAccessList[]=bold_ger
RootNodeList[]
RootNodeList[site]=97
RootNodeList[bold]=81
RootNodeList[bold_ger]=81
```

Then regenerate the extension autoloads and clear the INI, template and content caches: compiled templates hold
the kernel's `ezurl` code until they are compiled again.

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/ezcache.php --clear-tag=template --allow-root-user
php bin/php/ezcache.php --clear-id=content --allow-root-user
```

Under Exponential Velocity, restart it (`./console exp:velocity deploy` does all of the above).

The node ids differ between installations; never copy them from another one. To find yours, the root node of a
site is the node of its siteaccess's `IndexPage` (`/content/view/full/<id>`) or `content.ini [NodeSettings]
RootNode`; the template operator `ezurl_roots()` lists what the extension uses.

## New installations

The multisite installers (`sevenx_multisite`, `sevenx_multisite_clean`, `cjw_multisite`) activate the extension
and run the post-install step `postInstallWriteMultisiteURLSettings`, which reads the siteaccess files the install
has just written (PathPrefix, `RootNode`, `IndexPage`) and writes `settings/override/ezurlmultisite.ini.append.php`.
Administration siteaccesses (login required or an admin design) are left out.

## For extension developers

Code that builds content links itself instead of through `ezurl` asks the extension the same question:

```php
if ( class_exists( 'eZURLMultisite' ) && eZURLMultisite::isActive() )
{
    $url = eZURLMultisite::crossSiteURL( $nodeOrURLAlias, 'relative' ); // false: keep the local link
}
```

The extension's README lists its other settings (`[HostMap]`, `[PathMap]`, `[RoleMap]`, class-based links) and the
operators `ezurl_path`, `ezurl_siteaccess` and `ezurl_roots`.
