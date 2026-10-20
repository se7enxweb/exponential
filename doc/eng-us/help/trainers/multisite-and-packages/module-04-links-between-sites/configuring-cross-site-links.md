---
title: "Lesson 4.2: Configuring cross-site links"
audience: trainer, admin, developer
category: training
order: 42
summary: Learners check that ezurlmultisite is active, add the second site to its SiteAccessList and RootNodeList with the node IDs of their own installation, clear the right caches and verify a cross-site link; developers see the PHP call for links built in code. 50 minutes.
keywords: multisite and site packages, ezurlmultisite, ezurlmultisite.ini, MultisiteSettings, SiteAccessList, RootNodeList, ezurl_roots, eZURLMultisite::crossSiteURL, postInstallWriteMultisiteURLSettings, template cache, autoloads
type: help
---
# Lesson 4.2: Configuring cross-site links

On an installation made from one of the multisite site packages, `ezurlmultisite` is already active and configured
for the sites the installer created. A site added later, like `site_b` of this course, has to be added by hand, with
the node IDs of this installation. This lesson does that on the lab and checks a link from the main site into the
second site.

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Module | [Module 4: Links between sites](README.md) |
| Duration | 50 minutes (15 presenting, 35 exercise) |
| Level | Advanced |
| Audience | Administrators and developers |
| Prerequisites | [Lesson 4.1](why-links-to-another-site-break.md); `site_b` has `PathPrefix` set to its folder's alias ([Lesson 2.1](../module-02-matching-requests/matching-by-url-path.md), step 6) |

## Learning objectives

After this lesson, learners can:

- check that `ezurlmultisite` is active and read its settings;
- add a site to `[MultisiteSettings] SiteAccessList[]` and `RootNodeList[]` with the correct node ID;
- clear the caches that hold the old `ezurl` code;
- list the participating sites with the template operator `ezurl_roots()`;
- use `eZURLMultisite::crossSiteURL()` in PHP code that builds links itself.

## Trainer's notes

### How to present it

Source: [Links to content of another site, installing it and new installations](../../../../features/6.0/multisite-cross-site-links.md#installing-it-on-an-existing-site).

1. **The settings** (5 minutes):

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

   `SiteAccessList[]` names the sites in order of preference; `RootNodeList[<siteaccess>]` the root node of each
   site's subtree. Without the file, the public siteaccesses with a `PathPrefix` take part, each with the node of its
   `content.ini` `RootNode` or of its `IndexPage`.
2. **Never copy node IDs** (2 minutes). They differ between installations. The root node of a site is the node of its
   siteaccess's `IndexPage` or `content.ini [NodeSettings] RootNode`.
3. **New installations** (3 minutes). The multisite installers (`sevenx_multisite`, `sevenx_multisite_clean`,
   `cjw_multisite`) activate the extension and run the post-install step `postInstallWriteMultisiteURLSettings`,
   which writes the file from the siteaccess files the install has just written. Administration siteaccesses are left
   out.
4. **After a change** (2 minutes). Regenerate the extension autoloads when the extension was just activated, and
   clear the INI, template and content caches: compiled templates hold the kernel's `ezurl` code until they are
   compiled again. Under Velocity, `php bin/php/console exp:velocity deploy` does all of it.
5. **For developers** (3 minutes). Code that builds content links itself asks the extension the same question:

   ```php
   if ( class_exists( 'eZURLMultisite' ) && eZURLMultisite::isActive() )
   {
       $url = eZURLMultisite::crossSiteURL( $nodeOrURLAlias, 'relative' ); // false: keep the local link
   }
   ```

### Common questions

**What does `ezurl_roots()` return?** Per participating siteaccess: `siteaccess`, `host`, `prefix` and
`root_node_id` (the extension's README). Use it in a debug template, never in production output.

**Where is the full list of settings?** The extension's README lists the others: `[HostMap]`, `[PathMap]`,
`[RoleMap]`, class-based links, and the operators `ezurl_path`, `ezurl_siteaccess` and `ezurl_roots`.

**Why must `bold_ger` be listed with the same node as `bold`?** A site and its translations share a subtree; listing
both lets the current siteaccess win for a visitor of the translation.

**Can `exp:ini` write this file?** Yes, as any settings file: `exp:ini add 'ezurlmultisite.ini/MultisiteSettings/SiteAccessList[]' site_b global`.

### Pitfalls

- **An empty `SiteAccessList[]` reset in the file without the old entries.** Adding `site_b` must keep the entries the
  installer wrote. Read the file first.
- **Forgetting the template cache.** The setting is right but the old links stay until templates are compiled again.
- **A site without `PathPrefix`.** A URL alias string is matched by the prefix it starts with; a site whose aliases
  carry no prefix is found through its root node only.

## Hands-on exercise

Use your training installation from module 3. Replace `http://127.0.0.1:8087` with your lab's address.

1. Check that the extension is active:
   `php bin/php/console exp:ini get 'site.ini/ExtensionSettings/ActiveExtensions[]' | grep ezurlmultisite`.
2. Read its settings in effect: `php bin/php/console exp:ini list ezurlmultisite.ini/MultisiteSettings`. If the
   installer wrote `settings/override/ezurlmultisite.ini.append.php`, open it and note the entries.
3. Find the node ID of the folder *Site B* (lesson 1.3) and of the main site's root (its `IndexPage`:
   `php bin/php/console exp:ini get site.ini/SiteSettings/IndexPage -s site`).
4. If the file lists sites, add `site_b` and its root node, keeping the existing entries:

   ```bash
   php bin/php/console exp:ini add 'ezurlmultisite.ini/MultisiteSettings/SiteAccessList[]' site_b global
   php bin/php/console exp:ini set 'ezurlmultisite.ini/MultisiteSettings/RootNodeList[site_b]' NNN global
   ```

   If no file exists, the public siteaccesses with a `PathPrefix` take part automatically; skip this step and note it.
5. Clear the caches:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-tag=template
   php bin/php/ezcache.php --clear-id=content
   ```

6. On the main site, search for the folder *News* made in lesson 2.1: open `/site/content/search?SearchText=News` and
   find its link in the result list (or in the page source: `curl -s ... | grep -i news`).
7. Note the `href` of the link and open it.
8. In the administration, check that links there are unchanged (they do not take part).

## Expected result

- Step 1 lists `ezurlmultisite`.
- After step 5, the link to *News* on the main site points to `/site_b/News` (the address on `site_b`, its prefix
  removed), and opening it shows the folder on *Site B*.
- When search shows nothing (an empty search index on a fresh lab), the trainer runs
  `php bin/php/updatesearchindex.php` on one lab, or shows the link check on the projector instead.
- The trainer checks the `href` of step 7 on one lab.

## Quiz

1. Which two keys of `[MultisiteSettings]` configure the participating sites?
2. Where do you take the root node ID of a site from?
   A. Another installation B. The siteaccess's `IndexPage` or `content.ini` `RootNode` C. The URL alias D. The
   object ID
3. Which caches must be cleared after a change, and why the template cache?
4. Which installer step writes the file on a new multisite installation?
5. In PHP, which call returns the cross-site address of a node, and what does `false` mean?

<details>
<summary>Answers</summary>

1. `SiteAccessList[]` and `RootNodeList[<siteaccess>]`.
2. **B.** Node IDs differ between installations; never copy them.
3. INI, template and content: compiled templates hold the kernel's `ezurl` code until they are compiled again.
4. `postInstallWriteMultisiteURLSettings` of the multisite installers.
5. `eZURLMultisite::crossSiteURL( $nodeOrURLAlias, 'relative' )`; `false` means keep the local link
   ([For extension developers](../../../../features/6.0/multisite-cross-site-links.md#for-extension-developers)).

</details>

## Further reading

- [Links to content of another site of a multisite installation](../../../../features/6.0/multisite-cross-site-links.md).
- [Search with eZ Find and Solr](../../../creators/website-creators/search-with-ezfind-and-solr.md) and
  [Site search](../../../creators/website-creators/site-search.md): when search results link across sites.
- [Caches](../../../../guides/caches.md): the template and content caches.

## Related pages

- [Module 4: Links between sites](README.md)
- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
