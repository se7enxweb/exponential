# Getting Support

This guide is about getting a problem solved, from the first look in your own installation to hiring the people who
maintain Exponential. Many problems that show up in Exponential are not Exponential problems at all: they belong to
PHP, Composer, the database, the web server, the operating system or the browser, and each of those has large,
long-lived communities that have answered the same question many times. So the guide first shows how to help
yourself, then how to turn an Exponential problem into an ecosystem problem that any of those communities can
answer, then the Exponential community and its volunteers, the wider PHP ecosystem, the Exponential Foundation, and
finally 7x, for the fastest result with one partner responsible for the whole stack.

Every channel named here was checked to exist and answer (4 November 2026). Where another page of this help explains a
subject in depth, this guide links it instead of repeating it. Security problems never go to a public channel: see
[Security issues](#8-security-issues).

## Contents

1. [The fastest path to an answer](#1-the-fastest-path-to-an-answer)
2. [Help yourself first](#2-help-yourself-first)
3. [Abstraction: turn an Exponential problem into an ecosystem problem](#3-abstraction-turn-an-exponential-problem-into-an-ecosystem-problem)
4. [Exponential community support: the volunteers](#4-exponential-community-support-the-volunteers)
5. [The wider Exponential and PHP ecosystem](#5-the-wider-exponential-and-php-ecosystem)
6. [The Exponential Foundation](#6-the-exponential-foundation)
7. [7x: the fastest results, with total product responsibility](#7-7x-the-fastest-results-with-total-product-responsibility)
8. [Security issues](#8-security-issues)
9. [Questions](#9-questions)

## 1. The fastest path to an answer

| Your situation | Go to | How fast |
|---|---|---|
| A question about a screen or a setting | This help: the search box, or the "?" button of the admin page ([2](#2-help-yourself-first)) | Now |
| An error message or a blank page | The log viewer and the troubleshooting section of the book you followed ([2](#2-help-yourself-first)) | Minutes |
| The problem also happens without Exponential (PHP, database, web server, Composer, browser) | That layer's own community ([3](#3-abstraction-turn-an-exponential-problem-into-an-ecosystem-problem)) | Hours, often answered already |
| A "how do I ...?" question about Exponential | The forums and chats of the community ([4](#4-exponential-community-support-the-volunteers)) | Hours to days, no promise |
| A bug in Exponential | The issue tracker, prepared by the **Report an error** page of this help ([4](#4-exponential-community-support-the-volunteers)) | Fixed for everyone, when someone has time |
| A production site is down, a deadline is close, or the work is large | 7x ([7](#7-7x-the-fastest-results-with-total-product-responsibility)) | As fast as you agree with 7x |
| A security vulnerability | Privately, as [SECURITY.md](../../SECURITY.md) says, never in public ([8](#8-security-issues)) | First answer within one business day |

## 2. Help yourself first

### Look it up

- **This help.** Search it from the help dashboard, or press the "?" button on any page of the administration for
  the help about that page ([Help system](features/6.0/help-system.md)).
- **The troubleshooting sections of the books.** [Installing](INSTALL.md#12-troubleshooting),
  [Configuring](CONFIGURING.md#13-troubleshooting), [Updating](UPDATE.md), [Extending](EXTENDING.md),
  [Integrating](INTEGRATING.md) and [Distributions](DISTRIBUTIONS.md#12-questions-and-troubleshooting) each end with a
  table of symptoms and answers.
- **[When something goes wrong](help/when-something-goes-wrong.md)**: is it an error at all, what to check first,
  and how to file a bug.
- **The other documentation sites**, the community wiki eZpedia and the articles on Share Exponential:
  [Documentation sources](help/project/documentation-sources.md).

### Look at the installation

- **The logs.** **Setup > Log files** (`setup/logs`) and `./console exp:logs` follow the log files live, search them
  with their rotations and mask secrets by default ([The log viewer](features/6.0/log-viewer.md)).
- **The debug output.** Switch it on for your own address only, never for everyone
  ([Debug bar](bc/6.0/debug-bar.md), and [Configuring, Debugging and logs](CONFIGURING.md#11-debugging-and-logs)).
- **The system card.** `./console exp:info` lists the versions, extensions, siteaccesses, settings, cronjobs and
  caches; **Setup > System information** shows a summary to copy ([The system card](features/6.0/system-card.md)).
- **The upgrade check.** **Setup > Upgrade check** (`setup/systemupgrade`) and `./console exp:upgrade:status` show
  changed files and pending upgrade steps ([Updating, the upgrade check](UPDATE.md#3-check-the-installation-with-the-upgrade-check)).

### Collect the facts a helper needs

Whoever helps you, in a forum or at 7x, needs the same facts. Write them down once:

| Fact | Where to find it |
|---|---|
| The Exponential version and distribution | `./console exp:info`, Setup > System information |
| The PHP version and how it runs (Velocity, Apache or nginx with PHP-FPM) | `php -v`, Setup > System information |
| The database engine and version | Setup > System information |
| The active extensions and their versions | `./console exp:info`, Setup > Extensions |
| The exact error, with its reference number if the page showed one | The page, the log viewer |
| What you did, what you expected, what happened | You |
| What you already tried | You |

### Before you post anything

- Remove passwords, keys, tokens, session cookies, database addresses and server paths. The log viewer and the system
  card mask the common ones; check the rest yourself.
- Remove the personal data of your users and customers: names, e-mail addresses, IP addresses, order details.
- Post the smallest piece that shows the problem, not a whole log file.

## 3. Abstraction: turn an Exponential problem into an ecosystem problem

Exponential runs on a stack of other software, and every layer of that stack has its own community, often far larger
than any one application's. When a problem lives in one of those layers, the fastest helpers are the people who know
that layer, and they do not need to know Exponential at all. The trick is to take Exponential out of the question.

### Find the layer

| The symptom points to | The layer | A quick test outside Exponential |
|---|---|---|
| A PHP fatal error, a missing extension, a memory or time limit | PHP | `php -m`, `php -i`, a five-line script that calls the same function |
| `composer install` or `update` fails, a class is not found after an update | Composer | The same command in an empty project with the one package |
| Wrong characters, slow queries, "too many connections", lock waits | The database engine | The same query in the database's own client |
| 403, 404, 502, 504, redirects that loop, uploads that stop | The web server and PHP-FPM, or Velocity | A static file and a `phpinfo()` page on the same host |
| Certificate warnings, mixed content, the wrong client address | HTTPS, a proxy or a load balancer | `curl -v` against the site and against the backend |
| Permissions, disk, mail that never leaves | The operating system | The same action as the same user from the shell |
| A layout that breaks, a script error in the browser console | The browser, JavaScript, CSS | A minimal HTML page with the same markup |

When the quick test fails too, you have found the layer. When it works, the problem is in Exponential, and
[section 4](#4-exponential-community-support-the-volunteers) is the place.

### Ask where that layer is answered

| Layer | Where to ask and search |
|---|---|
| PHP | The PHP manual and its [support page](https://www.php.net/support.php); bugs in PHP itself at [github.com/php/php-src/issues](https://github.com/php/php-src/issues) |
| General programming questions | [Stack Overflow](https://stackoverflow.com/questions/tagged/php) |
| Servers and system administration | [Server Fault](https://serverfault.com), [Unix & Linux Stack Exchange](https://unix.stackexchange.com) |
| Databases | [Database Administrators Stack Exchange](https://dba.stackexchange.com) |
| Composer | The [Composer documentation](https://getcomposer.org/doc/) and its [issue tracker](https://github.com/composer/composer/issues) |
| Symfony (the Symfony based distributions) | [symfony.com/support](https://symfony.com/support) and the [Symfony issue tracker](https://github.com/symfony/symfony/issues) |
| MySQL | The [MySQL forums](https://forums.mysql.com) and [bug tracker](https://bugs.mysql.com) |
| MariaDB | The [MariaDB knowledge base](https://mariadb.com/kb/en/) and [issue tracker](https://jira.mariadb.org) |
| PostgreSQL | The [PostgreSQL mailing lists](https://www.postgresql.org/list/) |
| SQLite | The [SQLite forum](https://sqlite.org/forum/) |
| MongoDB | The [MongoDB community](https://www.mongodb.com/community/forums/) |
| Oracle Database | The [Oracle forums](https://forums.oracle.com) |
| Apache httpd | The [users' mailing list](https://httpd.apache.org/userslist.html) |
| nginx | The [nginx documentation](https://nginx.org/en/docs/) and its [mailing lists](https://mailman.nginx.org/mailman3/lists/) |
| Debian | The [Debian forums](https://forums.debian.net) |
| Red Hat Enterprise Linux, AlmaLinux | [Red Hat discussions](https://access.redhat.com/discussions), the [AlmaLinux forums](https://forums.almalinux.org) and [chat](https://chat.almalinux.org) |
| Ubuntu | [Ask Ubuntu](https://askubuntu.com), [Ubuntu Discourse](https://discourse.ubuntu.com) |
| HTML, CSS, JavaScript | [MDN Web Docs](https://developer.mozilla.org) |
| Exponential Layouts | The upstream [Netgen Layouts](https://github.com/netgen-layouts/layouts-core) code and [documentation](https://docs.netgen.io) |
| The expcomponents libraries | Their repositories under [se7enxweb](https://github.com/se7enxweb) (`expcomponents-*`) |

### Phrase the question without Exponential

- Describe the layer, not the application: "PHP 8.4 with PHP-FPM behind Apache returns 502 for uploads over 8 MB",
  not "Exponential cannot upload images".
- Include the versions of that layer, the minimal test that shows the problem, and what you already tried.
- Leave out what the helpers do not need: the content classes, the templates, the extension names.
- When you get the answer, bring it back: apply it to Exponential, and tell the community where you asked
  ([section 4](#4-exponential-community-support-the-volunteers)) what solved it, so the next person finds it.

### Search what was already answered

- Search the issue trackers of the layer and of Exponential's upstream libraries before asking; closed issues often
  hold the answer and the workaround.
- Much of the Exponential kernel continues the code of eZ Publish, a content management system that its creator
  discontinued. Answers written in the eZ Publish years about the kernel, the template language, content classes,
  INI settings and extensions often still apply. Read them as history: check the setting or function still exists in
  your version ([History of Exponential](HISTORY.md)), and prefer this help when the two disagree. Old pages are
  often reachable through the [Internet Archive](https://web.archive.org/web/2015/http://share.ez.no/) and are
  collected on [eZpedia](https://ezpedia.exponential.earth/).

### AI assistants as a first pass

An AI assistant can explain an error message, suggest where to look and draft a minimal test. Treat its answer as a
suggestion: check it against the documentation and in a copy of the site before you change production, and never
paste passwords, keys, tokens, customer data or whole configuration files into one.

## 4. Exponential community support: the volunteers

The community answers questions in its free time. Nobody promises an answer time, and good questions get answered
faster ([section 2](#collect-the-facts-a-helper-needs)).

| Place | Good for | Address |
|---|---|---|
| **Share Exponential forums** | Questions that need a longer answer, that others will search for later | <https://share.exponential.earth/forums> |
| **Telegram**, "Support Exponential (CMS)" | Quick questions in real time, with members of the development team | <https://t.me/exponentialcms> |
| **Discord** | Community chat, development talk, feedback | <https://discord.gg/zXzGMgfzej> |
| **Slack** | Chat, especially about development | <https://exponentialcms.slack.com/> |
| **GitHub issues** | Bugs in the kernel, the administration and the bundled extensions | <https://github.com/se7enxweb/exponential/issues> |
| **GitHub Discussions** | Questions and ideas next to the code | <https://github.com/se7enxweb/exponential/discussions> |
| **Exponential Projects** | Extensions and designs from the community, with their own pages | <https://projects.exponential.earth/> |
| **The documentation site** | The manuals, online | <https://doc.exponential.earth> |

Every place the community meets is listed in [Community channels](help/project/community-channels.md), and the
community itself, with all of its websites, is described in [Exponential Community](COMMUNITY.md). For a bug, the
**Report an error** page of
this help prepares the issue with a safe summary of the installation
([When something goes wrong](help/when-something-goes-wrong.md#filing-a-bug-in-exponential)).

**Etiquette with volunteers**

- Ask in one place at a time, and link the question if you move it.
- Answer the follow-up questions; a thread without them usually dies.
- Say what solved it, and mark the issue or thread as solved.
- Give back when you can: answer someone else's question, improve a page of this help, or report a bug well
  ([Helping others](help/community/helping-others/README.md), [Contributing to the project](help/project/contribute-to-the-project.md)).

## 5. The wider Exponential and PHP ecosystem

Exponential shares code and history with other projects, and their communities can help with the parts they share.

- **Exponential Layouts** continues Netgen Layouts. Questions about the layout engine itself, rather than how
  Exponential uses it, belong to the [Netgen Layouts repositories](https://github.com/netgen-layouts/layouts-core) and
  their [documentation](https://docs.netgen.io).
- **The Symfony based distributions** (Exponential Platform Legacy, Nexus, DXP) run on Symfony, so the
  [Symfony community](https://symfony.com/support) answers everything that is plain Symfony
  ([Distributions of Exponential](DISTRIBUTIONS.md)).
- **The eZ Publish and Ibexa years.** eZ Systems, now Ibexa, publishes the documentation of its current products at
  [doc.ibexa.co](https://doc.ibexa.co). It covers Ibexa's products, not Exponential, but its explanations of shared
  concepts can help; the eZ Publish era pages are history ([History of Exponential](HISTORY.md)).
- **PHP communities in general.** Local PHP user groups ([php.ug](https://php.ug)) and the conferences listed on
  [php.net/conferences](https://www.php.net/conferences/) are good places to meet people who run PHP in production.

## 6. The Exponential Foundation

The Exponential Foundation is the nonprofit that operates the Exponential project
([foundation.exponential.earth](https://foundation.exponential.earth/)). Its stated mission includes the funding,
adoption, support, direction, education, development, promotion and documentation of the project, and it supports the
community directly, putting the support needs of community users first (the project's README). It does not sell
support contracts; it funds the work that every user benefits from: the core libraries, the documentation and the
community infrastructure.

How the Foundation helps you get support:

- It keeps the free channels and the documentation running ([section 4](#4-exponential-community-support-the-volunteers)).
- You can direct its work by sponsoring it: <https://github.com/sponsors/exponentialcms>.
- The details, with every source, are in [Who is the Exponential Foundation?](help/who-is-the-exponential-foundation.md)
  and in [Give and donate](help/project/give-and-donate.md).

## 7. 7x: the fastest results, with total product responsibility

7x is the company that leads the development of Exponential and supports its customers
([Who is 7x?](help/who-is-7x.md)). When a site is down, a deadline is close, the environment is regulated, or the
work is large (a major upgrade, a migration from an old eZ Publish installation, a move between databases), hiring 7x
is the fastest route available today: the people who maintain the code do the work.

**Total product responsibility.** Most problems in production sit between the layers: the hosting, PHP, the database,
Exponential, the extensions and the integrations each belong to someone else, and each vendor can show that its own
part works. 7x takes the whole business software stack as one responsibility: hosting, PHP, the database,
Exponential and its extensions, integrations with other systems, upgrades and security. One partner, with experts for
every layer, owns the result, so nothing falls between vendors.

What 7x offers, as its own pages describe it:

- managed hosting, including Exponential hosting ([se7enx.com/services](https://se7enx.com/services));
- support, maintenance and feature development for Exponential sites
  ([support.exponential.earth](https://support.exponential.earth));
- custom business solutions such as CRM integration, API development and business process automation, plus web
  design, performance work, payment integration and training ([se7enx.com/services](https://se7enx.com/services)).

Prices and terms are those on [se7enx.com/services](https://se7enx.com/services) or the ones you agree with 7x; this
guide promises none.

**How to reach 7x:** the contact page [se7enx.com/contact](https://se7enx.com/contact), with a contact form, or e-mail
to info@se7enx.com. Bring the facts from [section 2](#collect-the-facts-a-helper-needs); they save the first hour.

## 8. Security issues

A security vulnerability is never posted in a forum, a chat or a public issue. Report it privately as
[SECURITY.md](../../SECURITY.md) explains; the project promises a first answer within one business day. To harden an
installation, see [Security hardening](help/security-hardening.md) and
[Configuring, Security](CONFIGURING.md#7-security).

## 9. Questions

| Question | Answer |
|---|---|
| Is community support free? | Yes. Nobody promises an answer time; see [section 4](#4-exponential-community-support-the-volunteers). |
| My question got no answer. What now? | Check that it has the facts from [section 2](#collect-the-facts-a-helper-needs), try the layer's own community ([section 3](#3-abstraction-turn-an-exponential-problem-into-an-ecosystem-problem)), or hire 7x ([section 7](#7-7x-the-fastest-results-with-total-product-responsibility)). |
| Can I use an answer written for eZ Publish? | Often, for the kernel, the templates and the settings. Check the setting or function still exists in your version, and prefer this help when they disagree. |
| Where do I report a bug? | In the issue tracker, prepared by the **Report an error** page of this help. |
| Where do I report a vulnerability? | Privately, as [SECURITY.md](../../SECURITY.md) says. |
| Does the Exponential Foundation sell support? | No. It funds the project; paid support comes from 7x. |
| Can I paste my configuration into an AI assistant or a forum? | Only after removing passwords, keys, tokens, addresses and personal data. |

## Related pages

- [Getting support: free help and paid help](help/project/getting-support.md), [Community channels](help/project/community-channels.md)
- [Exponential Community](COMMUNITY.md), [History of Exponential](HISTORY.md)
- [When something goes wrong](help/when-something-goes-wrong.md), [The log viewer](features/6.0/log-viewer.md)
- [Installing Exponential 6.0](INSTALL.md), [Configuring Exponential 6.0](CONFIGURING.md)
