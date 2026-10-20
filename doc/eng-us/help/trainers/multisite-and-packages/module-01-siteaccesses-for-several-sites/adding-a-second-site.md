---
title: "Lesson 1.3: Adding a second site step by step"
audience: trainer, admin, developer
category: training
order: 13
summary: Learners add a second public site to their training installation, with its own root folder, siteaccess, start page and name, register it and reach it by its URL path. 60 minutes.
keywords: multisite and site packages, second site, siteaccess, RootNode, IndexPage, DefaultPage, SiteName, AvailableSiteAccessList, RelatedSiteAccessList, SiteList, FrontSiteAccess, exp:ini set, exp:ini add
type: help
---
# Lesson 1.3: Adding a second site step by step

This is the central practical lesson of the module. Each learner turns a single-site training installation into a
two-site one: a new folder becomes the root of a second site, a new siteaccess serves it, and the administration
learns that the site exists. Every setting is written with `exp:ini`, so learners can see and undo each change.

> **Note**
> Use your own training installation. The steps write settings files and clear the INI cache.

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Module | [Module 1: Siteaccesses for several sites](README.md) |
| Duration | 60 minutes (15 presenting, 45 exercise) |
| Level | Intermediate |
| Audience | Administrators and developers |
| Prerequisites | [Lesson 1.1](what-a-siteaccess-is.md) and [Lesson 1.2](planning-several-sites.md); a training installation with URL matching (the `exp:install` default) and an administrator account on it |

## Learning objectives

After this lesson, learners can:

- create the root folder of a second site and read its node ID;
- create a siteaccess by copying an existing one and set its name, start page and root node;
- register the siteaccess in `AvailableSiteAccessList[]` and `RelatedSiteAccessList[]`;
- reach the new site by its URL path and check it from the command line;
- tell the administration which public sites exist (`SiteList[]`).

## Trainer's notes

### How to present it

Source: [Setting up several sites in one installation, step by step](../../../creators/website-creators/multisite-setup.md#step-by-step-a-second-site).

1. **The five changes** (5 minutes). Show the order on the board: content (a folder), siteaccess (a directory),
   registration (two lists), matching (module 2; with URL matching nothing is needed), caches. Explain that with
   `MatchOrder=uri` and the shipped `URIMatchType=element`, the first element of the path is taken as the name of a
   siteaccess, so `/site_b` reaches `site_b` as soon as it is in `AvailableSiteAccessList[]`.
2. **The settings of the new siteaccess** (5 minutes). In `site.ini [SiteSettings]`: `SiteName`, `IndexPage` and
   `DefaultPage` (`/content/view/full/<node id>`). In `content.ini [NodeSettings]`: `RootNode`. Mention
   `[DesignSettings] SiteDesign` for a different look; the theme design course covers designs.
3. **Why `exp:ini`** (3 minutes). It changes only the lines of the setting, keeps comments, backs the file up and
   clears the INI cache; `--dry-run` shows the diff first. Show one `--dry-run` on the projector.
4. **Telling the administration** (2 minutes). `SiteList[]` in `site.ini [SiteSettings]` lists the public sites, so
   previews and links can use them. The installer writes it into the global override, so add a new site there. When
   several sites each have their own administration, `FrontSiteAccess` in that
   administration's `site.ini` names the site its **Open the site** links lead to.

### Common questions

**Why copy `site` instead of starting empty?** The copy brings the design, the language and the other settings the
installer wrote, so the new site renders at once. Change what differs.

**Does the new site need its own users?** No. Users and roles are shared; lesson 4.3 limits a team to one site.

**What if the copy carries a `PathPrefix`?** A `PathPrefix` hides the first part of URL aliases for that
siteaccess. A copy keeps the prefix of the original, which belongs to the other site's folder. Step 6 checks it.

**Is `/site_b` the final address?** For the training lab, yes. Module 2 gives the site a host name of its own.

### Pitfalls

- **Node ID or object ID.** The **Details** tab shows both. `RootNode` and `IndexPage` need the **Node ID**.
- **A typo in the siteaccess name.** `exp:ini` refuses a scope for a siteaccess that has no directory; copy the
  directory first, then write the settings.
- **Velocity.** If the lab runs on Exponential Velocity, restart it after the settings change
  (`php bin/php/console exp:velocity restart`), or the workers keep the old settings.
- **Unpublished folder.** A folder that was only saved as a draft has no node; publish it first.

## Hands-on exercise

Use your own training installation (URL matching, as installed by `exp:install` without `--access`). Commands run in
its root directory; add `--allow-root-user` if you work as root.

1. Sign in to the administration. In **Content structure**, open the top folder. Under **Sub items**, press
   **Create new** and choose **Folder**. Name it `Site B` and press **Send for publishing**.
2. Open the new folder, open its **Details** tab and note the **Node ID**.
3. In the shell, copy the public siteaccess:

   ```bash
   cp -r settings/siteaccess/site settings/siteaccess/site_b
   ```

4. Set the name and the start page (replace `NNN` with the node ID):

   ```bash
   php bin/php/console exp:ini set site.ini/SiteSettings/SiteName "Site B" siteaccess:site_b --dry-run
   php bin/php/console exp:ini set site.ini/SiteSettings/SiteName "Site B" siteaccess:site_b
   php bin/php/console exp:ini set site.ini/SiteSettings/IndexPage /content/view/full/NNN siteaccess:site_b
   php bin/php/console exp:ini set site.ini/SiteSettings/DefaultPage /content/view/full/NNN siteaccess:site_b
   ```

5. Set the root node:

   ```bash
   php bin/php/console exp:ini set content.ini/NodeSettings/RootNode NNN siteaccess:site_b
   ```

6. Check the path prefix the copy inherited:

   ```bash
   php bin/php/console exp:ini get site.ini/SiteAccessSettings/PathPrefix siteaccess:site_b
   ```

   If it prints a value, remove it from the copy:
   `php bin/php/console exp:ini rem site.ini/SiteAccessSettings/PathPrefix siteaccess:site_b`.
7. Register the siteaccess for the whole installation:

   ```bash
   php bin/php/console exp:ini add 'site.ini/SiteAccessSettings/AvailableSiteAccessList[]' site_b global
   php bin/php/console exp:ini add 'site.ini/SiteAccessSettings/RelatedSiteAccessList[]' site_b global
   ```

8. Tell the administration about the site:

   ```bash
   php bin/php/console exp:ini add 'site.ini/SiteSettings/SiteList[]' site_b global
   ```

9. Clear the INI cache (and restart Velocity if it serves the lab):

   ```bash
   php bin/php/ezcache.php --clear-tag=ini
   ```

10. Check from the shell, with your lab's address:

    ```bash
    curl -sI http://127.0.0.1:8087/site_b | head -1
    ```

11. Open `/site_b` in the browser. The page title shows *Site B* and the start page is the new folder.
12. Run `php bin/php/console exp:ini where site.ini/SiteSettings/SiteName site_b` and find the file that wins.

## Expected result

- `curl` answers `HTTP/1.1 200 OK` (or `HTTP/2 200`) for `/site_b`.
- The browser shows the folder *Site B* as the start page, with the design of the main site and the name *Site B*.
- `exp:ini where` lists `settings/siteaccess/site_b/site.ini.append.php` with `SiteName=Site B` as the value in
  effect (unless the global override also sets `SiteName`; then the override wins, and the learner removes that line
  or sets the name there per siteaccess with module 3's tools).
- **Setup** > **System information**, section **Siteaccesses**, counts one siteaccess more than in lesson 1.1.
- The trainer checks two learners' `/site_b` on the projector.

## Quiz

1. Which ID does `RootNode` take, the node ID or the object ID?
2. With `MatchOrder=uri` and `URIMatchType=element`, what makes `/site_b` reach the siteaccess `site_b`?
   A. A `URIMatchMapItems[]` entry B. The name is in `AvailableSiteAccessList[]` C. A DNS entry D. A Velocity restart
3. Why copy `settings/siteaccess/site` instead of starting with an empty directory?
4. Which setting lets the administration know about the public sites?
5. After the change, the site still shows the old name. Name two causes.

<details>
<summary>Answers</summary>

1. The node ID, as on the **Details** tab.
2. **B.** The element method takes the first part of the path as the siteaccess name; it must be an available
   siteaccess (`settings/site.ini`, `URIMatchType=element`).
3. The copy brings the design, language and other settings, so the new site renders at once; you change only what
   differs.
4. `SiteList[]` in `site.ini [SiteSettings]`, which the installer writes into the global override
   ([Setting up several sites](../../../creators/website-creators/multisite-setup.md#step-by-step-a-second-site)).
5. The INI cache was not cleared; the global override sets `SiteName` and wins; Velocity was not restarted.

</details>

## Further reading

- [Setting up several sites in one installation](../../../creators/website-creators/multisite-setup.md): the steps
  with host matching.
- [Changing settings from the command line: exp:ini](../../../../bc/6.0/console-exp-ini.md): `set`, `add`, `rem`,
  `--dry-run`.
- [10.2 Siteaccesses and site addresses](../../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses).
- [Structuring the content tree](../../../creators/website-creators/structuring-the-content-tree.md).

## Related pages

- [Module 1: Siteaccesses for several sites](README.md)
- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
