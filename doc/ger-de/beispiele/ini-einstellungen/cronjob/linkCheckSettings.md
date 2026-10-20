---
title: cronjob.ini [linkCheckSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [linkCheckSettings] in cronjob.ini: SiteURL, Timeout, ConnectTimeout, MaxRedirects, HostDelay, RecheckInterval, MaxURLsPerRun, UserAgent ..."
keywords: Einstellung, cronjob.ini, linkCheckSettings, INI, setting, SiteURL, Timeout, ConnectTimeout, MaxRedirects, HostDelay, RecheckInterval, MaxURLsPerRun, UserAgent, AllowedPrivateHosts
type: generated
generated: exp:help:examples
---
# cronjob.ini [linkCheckSettings]

Der Block `[linkCheckSettings]` von `settings/cronjob.ini`, mit 9 Einstellung(en).

Bearbeiten Sie `settings/cronjob.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/cronjob.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/cronjob.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/cronjob.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## SiteURL

> The link check (linkcheck.php, part infrequent) tests the links of published content.  
> Guide: doc/eng-us/guides/urls-and-aliases.md  
>
> Specify full path of all site URL; a path that is not a URL alias or module  
> view is tried on these addresses.  
> Example:  
> SiteURL\[\]=http://exponential.earth/admin  
> SiteURL\[\]=http://exponential.earth/news  

Standard: eine Liste mit 1: ``.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[linkCheckSettings]
# ein leeres "SiteURL[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SiteURL[]
SiteURL[]=
*/ ?>
```

## Timeout

> Seconds a request may take, and seconds to connect  

Standard: `15`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[linkCheckSettings]
Timeout=15
*/ ?>
```

## ConnectTimeout

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[linkCheckSettings]
ConnectTimeout=5
*/ ?>
```

## MaxRedirects

> Redirects followed before a link counts as broken  

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[linkCheckSettings]
MaxRedirects=5
*/ ?>
```

## HostDelay

> Milliseconds between two requests to the same host  

Standard: `1000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[linkCheckSettings]
HostDelay=1000
*/ ?>
```

## RecheckInterval

> Seconds before a checked link is checked again; 0 checks every link on every run  

Standard: `72000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[linkCheckSettings]
RecheckInterval=72000
*/ ?>
```

## MaxURLsPerRun

> Links checked in one run, the ones checked longest ago first; 0 checks all  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[linkCheckSettings]
MaxURLsPerRun=0
*/ ?>
```

## UserAgent

> The User-Agent header of the requests  

Standard: `Exponential Link Validator (+https://exponential.earth)`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[linkCheckSettings]
UserAgent=Exponential Link Validator (+https://exponential.earth)
*/ ?>
```

## AllowedPrivateHosts

> Addresses on a private, loopback or link-local network are never requested,  
> so content cannot make the server reach inside its own network. List the  
> hosts, addresses or IPv4 ranges that may be tested anyway.  
> Example:  
> AllowedPrivateHosts\[\]=intranet.example.local  
> AllowedPrivateHosts\[\]=10.1.0.0/16  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[linkCheckSettings]
# ein leeres "AllowedPrivateHosts[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AllowedPrivateHosts[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von cronjob.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/cronjob.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
