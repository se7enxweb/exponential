---
title: No required external services
audience: government, admin, project-manager
category: public-sector
order: 50
summary: Exponential serves pages and runs its administration without any outside service: no license server and no third-party scripts or fonts in the admin. This page lists what does reach the internet, and only when you use it.
keywords: external services, third party, offline, air gap, CDN, LoadFromCDN, telemetry, license server, outbound connections, sovereignty, privacy, admin assets
type: help
---
# No required external services

This page is for administrators, security officers and data protection officers who must know whether the platform
contacts anyone outside the organization. Exponential needs no outside service to serve the site or to run the
administration. Some features reach the internet when you use or activate them; they are listed below so you can
allow, block or document them.

## Contents

1. [What runs without any outside service](#what-runs-without-any-outside-service)
2. [The administration loads no third-party assets](#the-administration-loads-no-third-party-assets)
3. [Features that reach the internet when used](#features-that-reach-the-internet-when-used)
4. [Extensions that involve third parties](#extensions-that-involve-third-parties)
5. [Checking it yourself](#checking-it-yourself)

## What runs without any outside service

Serving pages, editing and publishing, search with the built-in engine, roles and sections, versions, the audit
trail, workflows, caches and the cronjobs that maintain them all run on your server and your database. There is no
license key and no license server: the software is GPL ([Licensing explained for buyers](../../business/licensing-for-buyers.md)).

## The administration loads no third-party assets

The privacy standard states: jQuery and the other admin scripts are served locally
(`extension/ezjscore/settings/ezjscore.ini [eZJSCore] LoadFromCDN=disabled`), and the admin4 design loads no web fonts
or scripts from other hosts (`design/admin4/templates/pagelayout.tpl`) ([Privacy standards](../../../standards/privacy.md)).
Public designs are your own; check the ones you use for fonts, scripts and embeds from other hosts.

## Features that reach the internet when used

| Feature | What it contacts | When |
|---|---|---|
| Composer, **Setup > Updates and packages** (`ezupdate`) | packagist.org and the package sources in `composer.json` | when you search, update or install packages; packagist.org can be switched off |
| Link check (`linkcheck.php`) | the external addresses in your content | when the cronjob part runs; it never requests addresses on a private network |
| RSS imports | the feeds you configured | when the `rssimport` cronjob runs |
| Mail | your mail server | when mail is sent |
| Solr (`ezfind`) | the Solr server you configure | when active |
| REST API clients, OAuth | the clients you registered | inbound, when they call |

## Extensions that involve third parties

Installed with Exponential but **not active** until a site activates them; each sends visitor or content data to a
third party ([Privacy standards](../../../standards/privacy.md#third-party-services-in-shipped-extensions)):

| Extension | Third party |
|---|---|
| `bcwebsitestatistics` | Google Analytics / Google Tag Manager |
| `recaptcha`, `hcaptcha` | Google reCAPTCHA, hCaptcha |
| `sevenx_authentication_2fa` social login handlers | the chosen identity provider |
| `ezgmaplocation` with an API key | Google Maps |
| `expai` | the AI provider you configure; a local model through Ollama is also possible ([expai](../../../features/6.0/extensions/expai.md)) |

See "Third-party services to review" for a review routine.

## Checking it yourself

- **Setup > Extensions** lists what is active; `settings/override/site.ini.append.php` holds `ActiveExtensions[]`.
- In a browser's developer tools, load an admin page and a public page and look for requests to other hosts.
- On the server, watch outbound connections from the PHP processes during a day of normal use, with your usual
  network tools or firewall logs.
- A Content Security Policy makes the browser report what a page loads ([Content Security Policy](../../../features/6.0/content-security-policy.md)).

## Related pages

- [Privacy standards](../../../standards/privacy.md)
- [Security standards](../../../standards/security.md)
- [Links, URL aliases, wildcards and search statistics](../../../guides/urls-and-aliases.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
