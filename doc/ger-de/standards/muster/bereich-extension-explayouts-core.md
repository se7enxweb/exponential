---
title: Muster in extension/explayouts_core
audience: developer
category: standards
order: 417
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/explayouts_core mit den Entwurfsmustern.
keywords: extension/explayouts_core, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/explayouts_core

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/explayouts_core` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (13)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expLayoutsCoreAuditBranch` | class implements expAuditTaxonomyBranch | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/explayouts_core/classes/explayoutscoreauditbranch.php:11` | Der Zweig von explayouts_core in der Audit-Taxonomie, registriert in settings/audit.ini.append.php ([AuditEventSettings] Branches[explayouts_core]). |
| `expLayoutsCoreBlockService` | class | [Fassade / Dienst](fassade.md) | `extension/explayouts_core/classes/explayoutscoreblockservice.php:3` | – |
| `expLayoutsCoreCollectionService` | class | [Fassade / Dienst](fassade.md) | `extension/explayouts_core/classes/explayoutscorecollectionservice.php:3` | – |
| `expLayoutsCoreLayoutService` | class | [Fassade / Dienst](fassade.md) | `extension/explayouts_core/classes/explayoutscorelayoutservice.php:3` | – |
| `expLayoutsCoreRuleService` | class | [Fassade / Dienst](fassade.md) | `extension/explayouts_core/classes/explayoutscoreruleservice.php:3` | – |
| `expLayoutsCoreTransferDiff` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts_core/classes/explayoutscoretransferdiff.php:23` | Vergleicht die Dokumente eines Layout-Transferarchivs mit denselben Dokumenten, die aus der Zielinstallation exportiert wurden, und plant, was ein Import damit tut… |
| `expLayoutsCoreTransferExporter` | class | – | `extension/explayouts_core/classes/explayoutscoretransferexporter.php:13` | Baut die Dokumente eines Layout-Übertragungsarchivs aus der Datenbank auf. |
| `expLayoutsCoreTransferFormat` | class | [Hilfsklasse (statische Hilfsfunktionen)](hilfsklasse.md) | `extension/explayouts_core/classes/explayoutscoretransferformat.php:19` | Die Layout-Transferdatei: ein ZIP mit JSON-Dokumenten und einem Manifest. |
| `expLayoutsCoreTransferImporter` | class extends expLayoutsCoreLayoutService | – | `extension/explayouts_core/classes/explayoutscoretransferimporter.php:25` | Schreibt eine geplante Layout-Übertragung in einer Transaktion in die Datenbank. |
| `expLayoutsCoreTransferReferences` | class | – | `extension/explayouts_core/classes/explayoutscoretransferreferences.php:14` | Knoten- und Objekt-IDs einer Layout-Übertragung, über die Remote-ID zugeordnet. |
| `expLayoutsCoreTransferService` | class | [Fassade / Dienst](fassade.md) | `extension/explayouts_core/classes/explayoutscoretransferservice.php:17` | Export und Import von Layouts, geteilten Layouts und Regeln: der eine Einstiegspunkt, den die Admin-Seiten (explayouts_ui/transfer_export, explayouts_ui/transfer_im… |
| `expLayoutsCoreZoneService` | class | [Fassade / Dienst](fassade.md) | `extension/explayouts_core/classes/explayoutscorezoneservice.php:3` | – |
| `explayouts_coreInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/explayouts_core/ezinfo.php:12` | – |

## INI-Plugin-Punkte (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[AuditEventSettings] Branches[explayouts_core]` | `expLayoutsCoreAuditBranch` | `extension/explayouts_core/settings/audit.ini.append.php:6` |
