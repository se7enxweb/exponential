---
title: exphelp.ini [SupportOrganization_7x]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SupportOrganization_7x] in exphelp.ini: Name, URL, Button, Page, Mission, Funds"
keywords: Einstellung, exphelp.ini, SupportOrganization_7x, INI, setting, Name, URL, Button, Page, Mission, Funds
type: generated
generated: exp:help:examples
---
# exphelp.ini [SupportOrganization_7x]

Der Block `[SupportOrganization_7x]` von `settings/exphelp.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `7x`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SupportOrganization_7x]
Name=7x
*/ ?>
```

## URL

Standard: `https://github.com/sponsors/se7enxweb`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SupportOrganization_7x]
URL=https://github.com/sponsors/se7enxweb
*/ ?>
```

## Button

Standard: `Sponsor 7x on GitHub`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SupportOrganization_7x]
Button=Sponsor 7x on GitHub
*/ ?>
```

## Page

Standard: `help/who-is-7x.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SupportOrganization_7x]
Page=help/who-is-7x.md
*/ ?>
```

## Mission

Standard: `The company that leads Exponential and the first sponsor of the project: it develops, supports and hosts it and makes the releases.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SupportOrganization_7x]
Mission=The company that leads Exponential and the first sponsor of the project: it develops, supports and hosts it and makes the releases.
*/ ?>
```

## Funds

Standard: eine Liste mit 4: `Monthly releases and the work that keeps the lights on`, `Security patches to the libraries Exponential builds on, such as Zeta Components`, `New features given back to the community as open source`, `The bridges, forks and tools around the platform`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SupportOrganization_7x]
# ein leeres "Funds[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Funds[]
Funds[]=Monthly releases and the work that keeps the lights on
Funds[]=Security patches to the libraries Exponential builds on, such as Zeta Components
Funds[]=New features given back to the community as open source
Funds[]=The bridges, forks and tools around the platform
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
