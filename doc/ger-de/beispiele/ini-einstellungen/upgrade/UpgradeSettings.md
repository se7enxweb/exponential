---
title: upgrade.ini [UpgradeSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [UpgradeSettings] in upgrade.ini: StepList, CaptionKeep, CaptionKeepDays, CaptionKeepUpgrades, SmokeURL, ExtensionStepList"
keywords: Einstellung, upgrade.ini, UpgradeSettings, INI, setting, StepList, CaptionKeep, CaptionKeepDays, CaptionKeepUpgrades, SmokeURL, ExtensionStepList
type: generated
generated: exp:help:examples
---
# upgrade.ini [UpgradeSettings]

Der Block `[UpgradeSettings]` von `settings/upgrade.ini`, mit 6 Einstellung(en).

Bearbeiten Sie `settings/upgrade.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/upgrade.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/upgrade.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/upgrade.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## StepList

> Die Upgrade-Schritte des Kernels, Klassennamen (expUpgradeStep). Jeder Schritt gehört zu einer Stufe  
> (code, libraries, database, scripts, finish); sie laufen nach Stufe, dann nach Version, dann in  
> der hier genannten Reihenfolge, nach den Schritten, die sie in after() nennen.  
> Stufe code: die Git-Checkouts, nur Fast-Forward, über git\_manager  
> Stufe libraries: Composer (ein Update nur auf Wunsch mit --update, dann install aus composer.lock)  
> Stufe database: Schema und Daten, jede Engine (Schritt 0 zuerst: die Aufzeichnungstabelle)  
> Stufe scripts: die PHP-Upgrade-Schritte, über die Klassen der Skripte und Setup-Befehle  
> Stufe finish: die Änderungen wirksam machen und die Site prüfen (nur wenn der Lauf etwas geändert hat)  

Standard: eine Liste mit 40: `expUpgradeStepCodeUpdate`, `expUpgradeStepLibrariesUpdate`, `expUpgradeStepLibrariesInstall`, `expUpgradeStepRecordTable`, `expUpgradeStepMigrationTables`, `expUpgradeStepPasswordHashWidth`, `expUpgradeStepTrashColumns`, `expUpgradeStepPdfExportFooter`, `expUpgradeStepRssOpmlPodcast`, `expUpgradeStepOrderStatuses`, `expUpgradeStepAuditIndexTables`, `expUpgradeStepBookmarkFolders` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upgrade.ini.append.php
[UpgradeSettings]
# ein leeres "StepList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
StepList[]
StepList[]=expUpgradeStepCodeUpdate
StepList[]=expUpgradeStepLibrariesUpdate
StepList[]=expUpgradeStepLibrariesInstall
StepList[]=expUpgradeStepRecordTable
StepList[]=expUpgradeStepMigrationTables
StepList[]=expUpgradeStepPasswordHashWidth
StepList[]=expUpgradeStepTrashColumns
StepList[]=expUpgradeStepPdfExportFooter
StepList[]=expUpgradeStepRssOpmlPodcast
StepList[]=expUpgradeStepOrderStatuses
StepList[]=expUpgradeStepAuditIndexTables
StepList[]=expUpgradeStepBookmarkFolders
StepList[]=expUpgradeStepBookmarkViewTable
StepList[]=expUpgradeStepBookmarkViewColumns
StepList[]=expUpgradeStepMailPreferenceTables
StepList[]=expUpgradeStepApiKeyTable
StepList[]=expUpgradeStepTransferRunTable
StepList[]=expUpgradeStepDataTableTables
StepList[]=expUpgradeStepUserSessionTable
StepList[]=expUpgradeStepOAuthPkceColumns
# ... 20 weitere in der ausgelieferten Datei
*/ ?>
```

## CaptionKeep

> Sicherungspunkte (exp:upgrade:captions --prune): die neuesten CaptionKeep Sicherungspunkte behalten, die jünger  
> als CaptionKeepDays sind, und die Sicherungspunkte pre und post der letzten CaptionKeepUpgrades Upgrades. Nur die  
> Sicherungspunkte von exp:upgrade werden ausgedünnt; die eigenen von git\_manager richten sich nach dessen MaxBackups.  

Standard: `10`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upgrade.ini.append.php
[UpgradeSettings]
CaptionKeep=10
*/ ?>
```

## CaptionKeepDays

Standard: `90`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upgrade.ini.append.php
[UpgradeSettings]
CaptionKeepDays=90
*/ ?>
```

## CaptionKeepUpgrades

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upgrade.ini.append.php
[UpgradeSettings]
CaptionKeepUpgrades=3
*/ ?>
```

## SmokeURL

> Die Adressen, die exp:upgrade:verify --after abruft (200 bis 399 besteht). Leer: velocity.ini  
> \[DeploySettings\] WarmUpURL\[\] (die Adressen von Apache und von Velocity), sonst die Startseite  
> und die Anmeldeseite von \[SiteSettings\] SiteURL.  
> SmokeURL\[\]=https://www.example.com/  
> SmokeURL\[\]=https://www.example.com/admin/user/login  
> SmokeURL\[\]=https://www.example.com:8080/  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upgrade.ini.append.php
[UpgradeSettings]
# ein leeres "SmokeURL[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SmokeURL[]
*/ ?>
```

## ExtensionStepList

> Die Schritte von Erweiterungen: Jede Erweiterung hängt ihre Klassen in ihrer eigenen  
> settings/upgrade.ini.append.php an, z. B. ExtensionStepList\[\]=myextUpgradeStepWidgetTable  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/upgrade.ini.append.php
[UpgradeSettings]
# ein leeres "ExtensionStepList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ExtensionStepList[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von upgrade.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/upgrade.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
