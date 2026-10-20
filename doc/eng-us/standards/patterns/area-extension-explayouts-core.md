---
title: Patterns in extension/explayouts_core
audience: developer
category: standards
order: 417
summary: Every class, view, fetch function, operator and plugin point of extension/explayouts_core with its design patterns.
keywords: extension/explayouts_core, design patterns, classes, extension points
type: catalogue
---
# Patterns in extension/explayouts_core

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `extension/explayouts_core` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (13)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expLayoutsCoreAuditBranch` | class implements expAuditTaxonomyBranch | [INI plugin point (extension point)](ini-plugin-point.md) | `extension/explayouts_core/classes/explayoutscoreauditbranch.php:11` | explayouts_core's branch of the audit taxonomy, registered in settings/audit.ini.append.php ([AuditEventSettings] Branches[explayouts_core]). |
| `expLayoutsCoreBlockService` | class | [Facade / service](facade.md) | `extension/explayouts_core/classes/explayoutscoreblockservice.php:3` | – |
| `expLayoutsCoreCollectionService` | class | [Facade / service](facade.md) | `extension/explayouts_core/classes/explayoutscorecollectionservice.php:3` | – |
| `expLayoutsCoreLayoutService` | class | [Facade / service](facade.md) | `extension/explayouts_core/classes/explayoutscorelayoutservice.php:3` | – |
| `expLayoutsCoreRuleService` | class | [Facade / service](facade.md) | `extension/explayouts_core/classes/explayoutscoreruleservice.php:3` | – |
| `expLayoutsCoreTransferDiff` | class | [Utility (static helper)](utility.md) | `extension/explayouts_core/classes/explayoutscoretransferdiff.php:23` | Compares the documents of a layouts transfer archive with the same documents exported from the target installation, and plans what an import does wit… |
| `expLayoutsCoreTransferExporter` | class | – | `extension/explayouts_core/classes/explayoutscoretransferexporter.php:13` | Builds the documents of a layouts transfer archive from the database. |
| `expLayoutsCoreTransferFormat` | class | [Utility (static helper)](utility.md) | `extension/explayouts_core/classes/explayoutscoretransferformat.php:19` | The layouts transfer file: a ZIP holding JSON documents and a manifest. |
| `expLayoutsCoreTransferImporter` | class extends expLayoutsCoreLayoutService | – | `extension/explayouts_core/classes/explayoutscoretransferimporter.php:25` | Writes a planned layouts transfer into the database, in one transaction. |
| `expLayoutsCoreTransferReferences` | class | – | `extension/explayouts_core/classes/explayoutscoretransferreferences.php:14` | Node and object ids of a layouts transfer, mapped by remote id. |
| `expLayoutsCoreTransferService` | class | [Facade / service](facade.md) | `extension/explayouts_core/classes/explayoutscoretransferservice.php:17` | Export and import of layouts, shared layouts and rules: the one entry point the admin pages (explayouts_ui/transfer_export, explayouts_ui/transfer_im… |
| `expLayoutsCoreZoneService` | class | [Facade / service](facade.md) | `extension/explayouts_core/classes/explayoutscorezoneservice.php:3` | – |
| `explayouts_coreInfo` | class | [Value object / struct](value-object.md) | `extension/explayouts_core/ezinfo.php:12` | – |

## INI plugin points (1)

| Setting | Class | Set in |
|---|---|---|
| `[AuditEventSettings] Branches[explayouts_core]` | `expLayoutsCoreAuditBranch` | `extension/explayouts_core/settings/audit.ini.append.php:6` |
