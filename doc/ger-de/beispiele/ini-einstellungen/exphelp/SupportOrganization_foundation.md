---
title: exphelp.ini [SupportOrganization_foundation]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SupportOrganization_foundation] in exphelp.ini: Name, URL, Button, Page, Mission, Funds"
keywords: Einstellung, exphelp.ini, SupportOrganization_foundation, INI, setting, Name, URL, Button, Page, Mission, Funds
type: generated
generated: exp:help:examples
---
# exphelp.ini [SupportOrganization_foundation]

Der Block `[SupportOrganization_foundation]` von `settings/exphelp.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/exphelp.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/exphelp.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/exphelp.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/exphelp.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Name

Standard: `Exponential Foundation`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SupportOrganization_foundation]
Name=Exponential Foundation
*/ ?>
```

## URL

Standard: `https://github.com/sponsors/exponentialcms`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SupportOrganization_foundation]
URL=https://github.com/sponsors/exponentialcms
*/ ?>
```

## Button

Standard: `Sponsor the Exponential Foundation on GitHub`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SupportOrganization_foundation]
Button=Sponsor the Exponential Foundation on GitHub
*/ ?>
```

## Page

Standard: `help/who-is-the-exponential-foundation.md`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SupportOrganization_foundation]
Page=help/who-is-the-exponential-foundation.md
*/ ?>
```

## Mission

Standard: `The nonprofit that operates the open source project and is dedicated to its funding, adoption, support, education, development, promotion and documentation.`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SupportOrganization_foundation]
Mission=The nonprofit that operates the open source project and is dedicated to its funding, adoption, support, education, development, promotion and documentation.
*/ ?>
```

## Funds

Standard: eine Liste mit 4: `Maintenance of the core libraries`, `Documentation, tutorials, how-tos and FAQs`, `Integration layers: GraphQL, REST and Solr`, `Community infrastructure: chat, mirrors, wikis and developer resources`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/exphelp.ini.append.php
[SupportOrganization_foundation]
# ein leeres "Funds[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Funds[]
Funds[]=Maintenance of the core libraries
Funds[]=Documentation, tutorials, how-tos and FAQs
Funds[]=Integration layers: GraphQL, REST and Solr
Funds[]=Community infrastructure: chat, mirrors, wikis and developer resources
*/ ?>
```

## Siehe auch

- [Alle Blöcke von exphelp.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/exphelp.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
