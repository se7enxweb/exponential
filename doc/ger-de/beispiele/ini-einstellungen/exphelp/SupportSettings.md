---
title: exphelp.ini [SupportSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SupportSettings] in exphelp.ini: Enabled, Title, Text, MorePage, Organizations"
keywords: Einstellung, exphelp.ini, SupportSettings, INI, setting, Enabled, Title, Text, MorePage, Organizations
type: generated
generated: exp:help:examples
---
# exphelp.ini [SupportSettings]

Der Block `[SupportSettings]` von `settings/exphelp.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> The "Support Exponential" block at the bottom of the help dashboard and on the landing pages that list the block  
> support: GitHub Sponsors of 7x and of the Exponential Foundation. enabled|disabled  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SupportSettings]
Enabled=enabled
*/ ?>
```

## Title

Standard: `Support Exponential`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SupportSettings]
Title=Support Exponential
*/ ?>
```

## Text

Standard: `Exponential is free software. Every new feature, help page, extension, theme and application is work that somebody pays for with their time. A sustaining monthly sponsorship, of any size, keeps that work going and lets it grow.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SupportSettings]
Text=Exponential is free software. Every new feature, help page, extension, theme and application is work that somebody pays for with their time. A sustaining monthly sponsorship, of any size, keeps that work going and lets it grow.
*/ ?>
```

## MorePage

> The help page that explains what sponsorship pays for ('' or a missing page: no link)  

Standard: `help/sponsors/what-sponsorship-funds.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SupportSettings]
MorePage=help/sponsors/what-sponsorship-funds.md
*/ ?>
```

## Organizations

> The organizations, in order; each has a \[SupportOrganization\_<id>\] group: Name, URL (https), Button, Page (its page  
> in the help), Mission and Funds\[\] (what its funding pays for). Texts are translated in kernel/exphelp.  

Standard: eine Liste mit 2: `7x`, `foundation`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SupportSettings]
# ein leeres "Organizations[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Organizations[]
Organizations[]=7x
Organizations[]=foundation
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
