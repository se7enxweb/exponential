---
title: migration.ini [StepSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [StepSettings] in migration.ini: StepTypeList, MaxLoopIterations"
keywords: Einstellung, migration.ini, StepSettings, INI, setting, StepTypeList, MaxLoopIterations
type: generated
generated: exp:help:examples
---
# migration.ini [StepSettings]

Der Block `[StepSettings]` von `settings/migration.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/migration.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/migration.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/migration.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/migration.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## StepTypeList

> Die Schritttypen von YAML-Migrationen: StepTypeList\[<Name>\]=<Klasse>, wobei <Name> das ist, was "type:" eines Schritts nennt, und  
> die Klasse expMigrationStepType implementiert. Eine Erweiterung registriert ihre eigenen in ihrer migration.ini.append.php.  

Standard: ein Hash mit 21: `reference=expMigrationStepReference`, `assert=expMigrationStepAssert`, `content=expMigrationStepContent`, `location=expMigrationStepLocation`, `content_version=expMigrationStepContentVersion`, `content_class=expMigrationStepContentClass`, `content_class_group=expMigrationStepContentClassGroup`, `user=expMigrationStepUser`, `user_group=expMigrationStepUserGroup`, `role=expMigrationStepRole`, `section=expMigrationStepSection`, `language=expMigrationStepLanguage` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[StepSettings]
# ein leeres "StepTypeList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
StepTypeList[]
StepTypeList[reference]=expMigrationStepReference
StepTypeList[assert]=expMigrationStepAssert
StepTypeList[content]=expMigrationStepContent
StepTypeList[location]=expMigrationStepLocation
StepTypeList[content_version]=expMigrationStepContentVersion
StepTypeList[content_class]=expMigrationStepContentClass
StepTypeList[content_class_group]=expMigrationStepContentClassGroup
StepTypeList[user]=expMigrationStepUser
StepTypeList[user_group]=expMigrationStepUserGroup
StepTypeList[role]=expMigrationStepRole
StepTypeList[section]=expMigrationStepSection
StepTypeList[language]=expMigrationStepLanguage
StepTypeList[object_state_group]=expMigrationStepObjectStateGroup
StepTypeList[tag]=expMigrationStepTag
StepTypeList[url_alias]=expMigrationStepUrlAlias
StepTypeList[trash]=expMigrationStepTrash
StepTypeList[file]=expMigrationStepFile
StepTypeList[image]=expMigrationStepImage
StepTypeList[cache]=expMigrationStepCache
StepTypeList[search]=expMigrationStepSearch
# ... 1 weitere in der ausgelieferten Datei
*/ ?>
```

## MaxLoopIterations

> Die meisten Durchläufe, die eine "while"-Schleife einer YAML-Migration macht, wenn sie kein "max" nennt; einer mehr lässt die Migration scheitern.  

Standard: `1000`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/migration.ini.append.php
[StepSettings]
MaxLoopIterations=1000
*/ ?>
```

## Siehe auch

- [Alle Blöcke von migration.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/migration.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
