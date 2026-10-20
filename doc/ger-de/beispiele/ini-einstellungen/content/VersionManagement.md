---
title: content.ini [VersionManagement]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [VersionManagement] in content.ini: DefaultVersionHistoryLimit, VersionHistoryClass, DeleteDrafts, DraftsDuration, DraftsCleanUpLimit, InternalDraftsDuration, InternalDraftsCleanUpLimit"
keywords: Einstellung, content.ini, VersionManagement, INI, setting, DefaultVersionHistoryLimit, VersionHistoryClass, DeleteDrafts, DraftsDuration, DraftsCleanUpLimit, InternalDraftsDuration, InternalDraftsCleanUpLimit
type: generated
generated: exp:help:examples
---
# content.ini [VersionManagement]

Der Block `[VersionManagement]` von `settings/content.ini`, mit 7 Einstellung(en).

> ViewModes\[\]=print  
> Define maximum versions can be managed. Use syntax  
> VersionHistoryClass\[class\_id\]=maximum\_nr to specify value for particular  
> classes. Or if you prefer to use class\_identifier (since 4.1.1)  
> VersionHistoryClass\[class\_identifier\]=maximum\_nr  
> Otherwise, the value of DefaultVersionHistoryLimit will be used.  
> When decreasing DefaultVersionHistoryLimit or one value in VersionHistoryClass,  
> you can execute bin/php/cleanupversions.php to remove the exceeded archived versions.  

Bearbeiten Sie `settings/content.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/content.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/content.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/content.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## DefaultVersionHistoryLimit

Standard: `45`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[VersionManagement]
DefaultVersionHistoryLimit=45
*/ ?>
```

## VersionHistoryClass

Standard: ein Hash mit 1: `1=75`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[VersionManagement]
# ein leeres "VersionHistoryClass[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
VersionHistoryClass[]
VersionHistoryClass[1]=75
*/ ?>
```

## DeleteDrafts

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[VersionManagement]
DeleteDrafts=disabled
*/ ?>
```

## DraftsDuration

> Time duration (number of days, hours, minutes and seconds)  
> before a draft is considered old and can be removed  

Standard: ein Hash mit 4: `days=90`, `hours=0`, `minutes=0`, `seconds=0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[VersionManagement]
# ein leeres "DraftsDuration[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
DraftsDuration[]
DraftsDuration[days]=90
DraftsDuration[hours]=0
DraftsDuration[minutes]=0
DraftsDuration[seconds]=0
*/ ?>
```

## DraftsCleanUpLimit

> Clean-up limit per one run of the 'old\_drafts\_cleanup.php' cronjob script  

Standard: `100`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[VersionManagement]
DraftsCleanUpLimit=100
*/ ?>
```

## InternalDraftsDuration

> Time duration (number of days, hours, minutes and seconds)  
> before an internal draft is considered old and can be removed  

Standard: ein Hash mit 4: `days=1`, `hours=0`, `minutes=0`, `seconds=0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[VersionManagement]
# ein leeres "InternalDraftsDuration[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
InternalDraftsDuration[]
InternalDraftsDuration[days]=1
InternalDraftsDuration[hours]=0
InternalDraftsDuration[minutes]=0
InternalDraftsDuration[seconds]=0
*/ ?>
```

## InternalDraftsCleanUpLimit

> Clean-up limit per one run of the 'internal\_drafts\_cleanup.php' cronjob script  

Standard: `100`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/content.ini.append.php
[VersionManagement]
InternalDraftsCleanUpLimit=100
*/ ?>
```

## Siehe auch

- [Alle Blöcke von content.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/content.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
