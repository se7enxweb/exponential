---
title: Syndication between installations
audience: integrator, admin
category: integration
order: 110
summary: Sharing content between Exponential installations with the syndication extension - one-way and two-way syndication, feeds over SOAP, the export_feed and import_feed cronjob parts, protecting the source, operating it from the admin and the console, and when to choose something else.
keywords: syndication, content sharing, multi-installation, SOAP, export_feed, import_feed, ext:syndication, one-way, two-way, portal network, shared content
type: help
---
# Syndication between installations

This page is for integrators whose landscape has **several Exponential installations** that must share content: a
group site and its member sites, a portal network, a central product catalog published on several shops. The
`syndication` extension (required by the root `composer.json` as `~1.3.10`) exports part of one installation's
content tree as a feed and lets another installation import it, kept in step by cron. The feature page is
[syndication](../../features/6.0/extensions/syndication.md); every option is in the
[specification](../../specifications/6.0/syndication.md).

## Contents

1. [How it works](#how-it-works)
2. [Setting it up](#setting-it-up)
3. [Protecting the source](#protecting-the-source)
4. [Operating it](#operating-it)
5. [Syndication or something else](#syndication-or-something-else)
6. [Checklist](#checklist)

## How it works

```text
 main installation                                   second installation
 -----------------                                   -------------------
 export feed (a subtree)                             import (feed address + target node)
   |  cronjob part export_feed                          ^  cronjob part import_feed
   v                                                    |
 cached XML + binary files, one file per object  --->  SOAP request to the main installation
 served through soap.php (EnableSOAP=true)
```

- **One-way syndication**: shared content or authentication data between separate databases and client sites.
- **Two-way syndication**: each side exports and imports, for forums, portal networks and large e-commerce networks.

## Setting it up

On **both** installations:

1. Install the extension's tables (`sql/mysql.sql`, or `share/db_schema.dba` for every database since 1.3.1) and
   activate it.
2. Switch the SOAP server on for the exporting side, in `settings/override/soap.ini.append.php`:

   ```ini
   [GeneralSettings]
   EnableSOAP=true

   [ExtensionSettings]
   SOAPExtensions[]=syndication
   ```

On the **main** installation: open `/syndication/menu`, choose **Export**, then **New Feed**, and complete the wizard.
A subtree source includes the subtree root. Schedule the export part; every 3 minutes is suggested:

```bash
php runcronjobs.php export_feed
```

On the **second** installation: open `/syndication/menu`, choose **Import**, give the feed's server address and the
node that receives the content, and run the import part by hand first:

```bash
php runcronjobs.php import_feed
```

Review the first import, then schedule it. After the first run, imports are faster.

## Protecting the source

Since 1.2.0 the import sends a login and password, so the exporting site's SOAP endpoint can sit behind HTTP access
control. Give them in the server address (`https://<login>:<password>@<host>/`) or in the **Login** and **Password**
fields of the import's first step. The port defaults to 443 for `https`. Keep these credentials out of version
control, and restrict `soap.php` to the addresses of the importing installations at the web server.

## Operating it

The **Syndication** start page in the administration shows what is exported and imported, when each cronjob part last
ran, and what needs attention (a feed without a source, an import without a location, failed items, SOAP off).
**Export all active feeds** and **Fetch all active imports** start the cronjob code in the background with progress
on the page.

From the shell (`php bin/php/console list ext:syndication`):

```bash
php bin/php/console ext:syndication:status
php bin/php/console ext:syndication:export --dry-run
php bin/php/console ext:syndication:import --import=3 --fetch-only
```

## Syndication or something else

| Situation | Choose |
|---|---|
| Installations share a subtree continuously | `syndication` |
| A partner site only shows headlines | An RSS export ([feeds](rss-atom-and-opml-feeds.md)) |
| Move a site or a subtree once | A package ([packages](packages-import-and-export.md)) |
| Another system that is not Exponential reads the content | The REST API or `expservices` |
| One installation serves several sites | Not syndication at all: siteaccesses of one installation ([multi-site planning](../leadership/multisite-and-multilingual-planning.md)) |

## Checklist

- [ ] Tables installed and the extension active on every installation involved.
- [ ] `EnableSOAP=true` and `SOAPExtensions[]=syndication` on each exporting installation.
- [ ] The SOAP endpoint protected (HTTP authentication, address restriction, HTTPS).
- [ ] `export_feed` and `import_feed` scheduled; the first import reviewed by hand.
- [ ] Monitoring on the parts' last run (the Syndication start page, `ext:syndication:status`).

## Related pages

- [syndication](../../features/6.0/extensions/syndication.md) and its [specification](../../specifications/6.0/syndication.md)
- [WebDAV and SOAP](webdav-and-soap.md)
- [Cronjobs for integrations](cronjobs-for-integrations.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
