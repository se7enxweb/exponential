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

> Der Block „Exponential unterstützen“ unten auf dem Dashboard der Hilfe und auf den Startseiten, die den Block  
> support aufführen: GitHub Sponsors von 7x und der Exponential Foundation. enabled|disabled  

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

> Die Hilfeseite, die erklärt, wofür das Sponsoring bezahlt ('' oder eine fehlende Seite: kein Link)  

Standard: `help/sponsors/what-sponsorship-funds.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SupportSettings]
MorePage=help/sponsors/what-sponsorship-funds.md
*/ ?>
```

## Organizations

> Die Organisationen, in Reihenfolge; jede hat eine Gruppe \[SupportOrganization\_<id>\]: Name, URL (https), Button, Page (ihre Seite  
> in der Hilfe), Mission und Funds\[\] (wofür ihre Finanzierung bezahlt). Die Texte werden in kernel/exphelp übersetzt.  

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
