---
title: Your first site, step by step
audience: website-creator, admin
category: website-creation
order: 80
summary: From an empty directory to a running site you can edit, in ten steps - get the code, install, serve it with Velocity, sign in, add content, change a template and check the result.
keywords: first site, tutorial, quick start, install, exp:install, Velocity, sign in, first content, template override, walkthrough, website creator
type: help
---
# Your first site, step by step

This page is for website creators who want a running site on their own machine to learn on. It takes you from an
empty directory to a site with your own content and one changed template. Each step ends with something you can
check. It uses the defaults on purpose: SQLite (no database server), the default site package and Velocity. For a
real site, read [Choosing an install method](choosing-an-install-method.md) afterwards.

![The help center in the administration, where this page and the rest of the website creators' path are](../../images/creators/help-dashboard-first-site.webp)

## Contents

1. [Before you start](#before-you-start)
2. [Steps 1-3: code, install, serve](#steps-1-3-code-install-serve)
3. [Steps 4-6: sign in and add content](#steps-4-6-sign-in-and-add-content)
4. [Steps 7-9: change how a page looks](#steps-7-9-change-how-a-page-looks)
5. [Step 10: look around](#step-10-look-around)
6. [If something does not work](#if-something-does-not-work)

## Before you start

You need PHP 8.1 or newer on the command line, Composer 2 and Git
([Requirements](../../../install/02-requirements.md)). Run every command from the installation root, the directory
that holds `index.php`, as your own user (not `root`).

## Steps 1-3: code, install, serve

**Step 1. Get the code** ([Getting the code](../../../install/03-getting-the-code.md)):

```bash
git clone https://github.com/se7enxweb/exponential.git mysite
cd mysite
composer install
```

Check: `ls index.php kernel settings` lists all three.

**Step 2. Install.** With no options, `exp:install` installs the `sevenx_multisite` package on SQLite with the
administrator `admin` and a generated password ([The console install](../../../install/07-console-install.md)):

```bash
php bin/php/console exp:install --url=http://127.0.0.1:8088
```

Check: the summary at the end says "Exponential is installed" and shows the password once. Write it down; it is also
in `var/log/initial-admin-password` (readable only by you) until you delete that file.

**Step 3. Serve it.** Install the Velocity engine and start it
([8.3 Exponential Velocity](../../../install/08-serving-the-site.md#83-exponential-velocity-the-recommended-way)):

```bash
composer require se7enxweb/exponential-velocity
php bin/php/console exp:velocity config set ServerSettings Engine qbix
php bin/php/console exp:velocity start
php bin/php/console exp:velocity status
```

Check: `status` lists the addresses; `curl -sI http://127.0.0.1:8088/ | head -1` prints `200` or a redirect.

> **Tip**
> No Velocity yet? `php bin/php/console exp:velocity start --engine=php` uses PHP's built-in server, which is enough to
> look around (port 8087, development only).

## Steps 4-6: sign in and add content

**Step 4. Open the site** at the `Site:` address the install printed. You see the demo front page.

**Step 5. Sign in** at the `Admin login:` address with `admin` and the password, then change the password
(**My account**; see [Accounts and sign-in](../../accounts-and-sign-in.md)). Delete
`var/log/initial-admin-password` afterwards.

**Step 6. Create content.** In the administration, open the content structure, choose a folder, pick **Article** in
the create menu, fill in the title and body and press **Send for publishing**
([Editing content](../../editing-content.md)). Check: the article appears in the folder's sub-items and on the site.

## Steps 7-9: change how a page looks

**Step 7. Find the design.** Ask which design the public siteaccess uses:

```bash
php bin/php/console exp:ini where site/DesignSettings/SiteDesign site
```

The last line, `In effect`, names the design ([Templates and design](../../../guides/templates-and-design.md)).

**Step 8. Override the article's template** for this one article, with the click path: **Design > Template Editor**,
open `node/view/full.tpl`, press **New override**, choose *Default copy*, give the article's object ID and create it.
Add a line such as `<p>My first override</p>` and save. Details: [Template overrides for site builders](template-overrides.md).

**Step 9. Check.** Reload the article on the site: your line is there. Remove the override again from the same list
when you are done.

## Step 10: look around

- The **help** (`/exphelp/dashboard` in the administration) has pages for editors and administrators; your editors
  will start there.
- **Setup > System information** shows the PHP, database and extensions in use
  ([System information](../../../guides/system-information.md)).
- The **layouts editor** builds landing pages without templates ([Landing pages with the layouts editor](landing-pages-with-layouts.md)).

## If something does not work

| You see | Do |
|---|---|
| `composer install` refuses your PHP | install PHP 8.1 or newer ([2.2 PHP](../../../install/02-requirements.md#22-php)) |
| The install stops in `SystemCheck` | read the printed fix; usually a missing PHP extension or permission |
| A maintenance page instead of the site | `php bin/php/maintenance.php status`, then `off` |
| Errors about files that cannot be written | you ran a command as `root`; see [12. Troubleshooting](../../../install/12-troubleshooting.md) |
| Anything else | [When something goes wrong](../../when-something-goes-wrong.md) |

## Related pages

- [Website creators: start here](website-creators-start-here.md)
- [Getting started (administrators)](../../../guides/getting-started.md)
- [Choosing a design or theme](choosing-a-design-or-theme.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
