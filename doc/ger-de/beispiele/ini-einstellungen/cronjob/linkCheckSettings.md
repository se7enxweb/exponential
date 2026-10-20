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

> Die Linkprüfung (linkcheck.php, Teil infrequent) testet die Links veröffentlichter Inhalte.  
> Anleitung: doc/eng-us/guides/urls-and-aliases.md  
>
> Geben Sie den vollständigen Pfad jeder Website-URL an; ein Pfad, der kein URL-Alias und keine Modul-  
> ansicht ist, wird an diesen Adressen versucht.  
> Beispiel:  
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

> Sekunden, die eine Anfrage dauern darf, und Sekunden für den Verbindungsaufbau  

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

> Weiterleitungen, denen gefolgt wird, bevor ein Link als defekt gilt  

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[linkCheckSettings]
MaxRedirects=5
*/ ?>
```

## HostDelay

> Millisekunden zwischen zwei Anfragen an denselben Host  

Standard: `1000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[linkCheckSettings]
HostDelay=1000
*/ ?>
```

## RecheckInterval

> Sekunden, bis ein geprüfter Link erneut geprüft wird; 0 prüft jeden Link bei jedem Lauf  

Standard: `72000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[linkCheckSettings]
RecheckInterval=72000
*/ ?>
```

## MaxURLsPerRun

> In einem Lauf geprüfte Links, die am längsten nicht geprüften zuerst; 0 prüft alle  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[linkCheckSettings]
MaxURLsPerRun=0
*/ ?>
```

## UserAgent

> Der User-Agent-Header der Anfragen  

Standard: `Exponential Link Validator (+https://exponential.earth)`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/cronjob.ini.append.php
[linkCheckSettings]
UserAgent=Exponential Link Validator (+https://exponential.earth)
*/ ?>
```

## AllowedPrivateHosts

> Adressen in einem privaten, Loopback- oder Link-Local-Netz werden nie angefragt,  
> damit Inhalte den Server nicht in sein eigenes Netz greifen lassen können. Listen Sie die  
> Hosts, Adressen oder IPv4-Bereiche auf, die trotzdem geprüft werden dürfen.  
> Beispiel:  
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
