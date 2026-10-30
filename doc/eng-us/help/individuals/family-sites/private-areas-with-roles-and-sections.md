---
title: Private areas with roles and sections
audience: individual, website-creator, customer
category: personal-sites
order: 1702
summary: How one person makes a family or member area private on Exponential - how sections and the Anonymous role decide what visitors see, the Restricted section and the Members and Partners groups the default package creates, a private area step by step, a fully private site with RequireUserLogin, and how to test it as a stranger.
keywords: private area, members only, sections, Restricted section, Standard section, Anonymous role, roles, policies, content/read, user groups, Members, Partners, RequireUserLogin, family site, test as visitor, search engines
type: help
---
# Private areas with roles and sections

This page is for individuals who want part of their site, or all of it, to be visible only to people who sign in:
a family area, a members' corner, drafts for friends. It explains the mechanism once and then gives two recipes.

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../../examples/web-servers/nginx.md), [LiteSpeed](../../../examples/web-servers/litespeed.md),
> [Caddy](../../../examples/web-servers/caddy.md), [IIS](../../../examples/web-servers/iis.md), [Velocity](../../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../../hosting/panels/plesk.md), [cPanel](../../hosting/panels/cpanel.md), [DirectAdmin](../../hosting/panels/directadmin.md), [ISPConfig](../../hosting/panels/ispconfig.md).
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../../guides/litespeed-page-cache.md).

## Contents

1. [How visibility works](#how-visibility-works)
2. [What the default package already set up](#what-the-default-package-already-set-up)
3. [Recipe 1: a private area](#recipe-1-a-private-area)
4. [Recipe 2: a fully private site](#recipe-2-a-fully-private-site)
5. [Testing as a stranger](#testing-as-a-stranger)
6. [Mistakes to avoid](#mistakes-to-avoid)

## How visibility works

Three things work together ([Sections](../../../guides/sections.md), [Roles and policies](../../../guides/roles-and-policies.md)):

| Thing | What it is |
|---|---|
| **Section** | A label on a part of the content tree. Every object belongs to exactly one section. |
| **Role** | A set of permissions (policies), such as "read content in section X". |
| **User group** | Where accounts live. Roles are assigned to groups (or single users). |

The key fact: the default **Anonymous** role may read content only in the **Standard** section. Content placed in
any other section is invisible to visitors who are not signed in, and they are asked to log in when they open it
([Sections, In short](../../../guides/sections.md)).

## What the default package already set up

The installer of the default site package (`sevenx_multisite`) creates more than a bare installation
(`var/storage/packages/7x/sevenx_multisite/settings/sevenx-multi-site-installer.php`):

| Created | Purpose |
|---|---|
| Section **Restricted** | Content anonymous visitors cannot read |
| User group **Members** (the renamed "Guest accounts" group) with the role **Member** | Registered visitors; may comment and post in forums in the Standard section |
| User group **Partners** with the role **Partner** | May read the Restricted section and post there |
| The top-level *Archives* folder in Restricted | An example of content only Partners see |

So on a default installation, "people who may see the private area" already exists: the Partners group. For a family
site you will usually want your own names; the recipe below creates them, but reusing Restricted and Partners works too.

## Recipe 1: a private area

1. **Create the area**: a *Folder* called, say, "Family" under the home page, with everything private below it.
2. **Create a section** "Family" in **Setup > Sections** (or reuse *Restricted*)
   ([Sections and object states](../../creators/website-creators/sections-and-object-states.md)).
3. **Assign the folder to the section**, with its subtree. New content created below it inherits the section.
4. **Create a user group** "Family" under **Users**.
5. **Create a role** "Family reader" with `content/read` limited to the Family section, and `user/login` for the public
   siteaccess. For contributors, a second role "Family contributor" with `content/create` and `content/edit` for the
   classes they may add (blog posts, images, comments), limited to the Family section.
6. **Assign** the roles to the Family group ([Roles and policies](../../../guides/roles-and-policies.md)).
7. **Put relatives' accounts in the Family group** ([Inviting family members](inviting-family-members.md)).
8. **Test as a stranger** (below).

The Family folder may still appear in the menu for visitors as a link that leads to a login. If you do not want
strangers to see even its name, hide it from menus for anonymous visitors in the template, or use recipe 2.

## Recipe 2: a fully private site

Make the public siteaccess require sign-in for everything, by adding to its `site.ini.append.php`:

```ini
[SiteAccessSettings]
RequireUserLogin=true
```

Every page then asks for a login first; only the login, password-reset and (if allowed) registration pages are
reachable ([Member registration and members-only areas](../../creators/website-creators/member-registration-and-areas.md#a-members-only-area)).
Combine it with a role that lets the Family group read the Standard section. This is the simplest way to keep a whole
family site out of search engines and away from strangers.

## Testing as a stranger

After every change to sections or roles:

- [ ] Open a private browser window (not signed in) and visit the private area's address: you must be asked to log in.
- [ ] Open a private page's download link (a file attachment): you must be asked to log in.
- [ ] Search the site for a word from a private page: no result.
- [ ] Sign in as a test relative (not as yourself, the administrator): the area opens; administrator-only pages do not.
- [ ] Check the RSS exports: none must list private content.

Clear the caches if a change does not seem to take effect ([Caches](../../../guides/caches.md)).

> **Important**
> Image files are served by the web server directly: the shipped rewrite rules pass `var/.../storage/images/...`
> straight through (`.htaccess_root`, and the nginx example in
> [Serving the site](../../../install/08-serving-the-site.md)). Pages and file downloads are protected by your
> roles; an image file is not. Someone who has the exact address of a private image (because a relative copied it
> into a message, for example) can open it without signing in. The address is long and not guessable, but treat
> private photos as "unlisted", not "locked", and never upload a photo that would do harm if it leaked.

## Mistakes to avoid

| Mistake | Consequence |
|---|---|
| Testing only while signed in as administrator | You see everything; you learn nothing |
| Moving content into the private folder without its section changing | It stays readable; check the section after moving |
| An RSS export of the private blog | The feed publishes private posts to anyone with the address |
| One shared family account | You cannot remove one person's access |
| Giving relatives the Editor or Administrator role | They can change far more than you intend |

## Related pages

- [Family sites](README.md)
- [Sections and object states](../../creators/website-creators/sections-and-object-states.md)
- [Visitor permissions](../../creators/website-creators/visitor-permissions.md)
- [Members-only content](../../businesses/members/members-only-content.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
