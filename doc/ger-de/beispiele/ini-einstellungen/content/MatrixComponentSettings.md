---
title: content.ini [MatrixComponentSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [MatrixComponentSettings] in content.ini: CompanyAddress, PersonContactInfo, CompanyContactInfo"
keywords: Einstellung, content.ini, MatrixComponentSettings, INI, setting, CompanyAddress, PersonContactInfo, CompanyContactInfo
type: generated
generated: exp:help:examples
---
# content.ini [MatrixComponentSettings]

Der Block `[MatrixComponentSettings]` von `settings/content.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## CompanyAddress

Standard: eine Liste mit 2: `Postal Address`, `Visitor Address`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[MatrixComponentSettings]
# ein leeres "CompanyAddress[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CompanyAddress[]
CompanyAddress[]=Postal Address
CompanyAddress[]=Visitor Address
*/ ?>
```

## PersonContactInfo

Standard: eine Liste mit 5: `Phone`, `Fax`, `Email`, `Homepage`, `IM address`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[MatrixComponentSettings]
# ein leeres "PersonContactInfo[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
PersonContactInfo[]
PersonContactInfo[]=Phone
PersonContactInfo[]=Fax
PersonContactInfo[]=Email
PersonContactInfo[]=Homepage
PersonContactInfo[]=IM address
*/ ?>
```

## CompanyContactInfo

Standard: eine Liste mit 4: `Phone`, `Fax`, `Email`, `Homepage`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[MatrixComponentSettings]
# ein leeres "CompanyContactInfo[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
CompanyContactInfo[]
CompanyContactInfo[]=Phone
CompanyContactInfo[]=Fax
CompanyContactInfo[]=Email
CompanyContactInfo[]=Homepage
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
