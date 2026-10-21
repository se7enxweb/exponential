---
title: Muster in extension/ezownerchange
audience: developer
category: standards
order: 448
summary: Jede Klasse, Ansicht, Fetch-Funktion, jeder Operator und Plugin-Punkt von extension/ezownerchange mit den Entwurfsmustern.
keywords: extension/ezownerchange, Entwurfsmuster, Klassen, Erweiterungspunkte, design patterns, classes, extension points
type: catalogue
---
# Muster in extension/ezownerchange

> **Hinweis**
> Diese Seite wird aus dem Quellcode von `php bin/php/exppatterncatalogue.php --write` erzeugt. Bearbeiten Sie sie nicht von Hand:
> Ändern Sie den Code oder den Generator und führen Sie den Befehl dann erneut aus. Wie die Muster erkannt werden: [der Musterkatalog](README.md#wie-der-katalog-gebaut-wird).

Jede Klasse, Schnittstelle und jeder Trait von `extension/ezownerchange` mit den Mustern, als die sie erkannt wurden, gefolgt von den Modulansichten, Richtlinienfunktionen, Fetch-Funktionen, Template-Operatoren, Cronjobs, Ereignis-Hooks und INI-Plugin-Punkten des Bereichs. [Alle Muster](README.md).

## Klassen (5)

| Klasse | Art | Muster | Datei | Beschreibung |
| --- | --- | --- | --- | --- |
| `expOwnerChangeAuditBranch` | class implements expAuditTaxonomyBranch | [INI-Plugin-Punkt (Erweiterungspunkt)](ini-plugin-punkt.md) | `extension/ezownerchange/classes/expownerchangeauditbranch.php:12` | Der Zweig von ezownerchange in der Audit-Taxonomie, registriert in settings/audit.ini.append.php ([AuditEventSettings] Branches[ezownerchange]). |
| `expOwnerChangePlan` | class | – | `extension/ezownerchange/classes/expownerchangeplan.php:20` | Der Plan eines Besitzerwechsels: jedes Objekt, das die Anfrage erreicht, mit dem, was mit ihm geschieht. |
| `expOwnerChangeRequest` | class | [Fabrik und statische Fabrikmethoden](fabrik.md) | `extension/ezownerchange/classes/expownerchangerequest.php:14` | Eine Anfrage zum Besitzerwechsel, geprüft, bevor irgendetwas gelesen oder geschrieben wird: der neue Besitzer, der Umfang (ein Objekt oder ein Teilbaum), der Start (Objekt oder Knoten),… |
| `expOwnerChangeService` | class | [Fassade / Dienst](fassade.md) | `extension/ezownerchange/classes/expownerchangeservice.php:23` | Ändert den Eigentümer von Inhaltsobjekten: eines Objekts oder jedes Objekts mit einem Ort in einem Teilbaum. |
| `ezownerchangeInfo` | class | [Wertobjekt / Struktur](wertobjekt.md) | `extension/ezownerchange/ezinfo.php:10` | Die Informationen von ezownerchange für die Seite „Über“ (ezinfo/about). |

## Modulansichten (1)

| Ansicht | Skript | Richtlinienfunktionen | Definiert in |
| --- | --- | --- | --- |
| `/owner/change` | `change.php` | change | `extension/ezownerchange/modules/owner/module.php:22` |

## Richtlinienfunktionen (2)

| Funktion | Einschränkungen | Definiert in |
| --- | --- | --- |
| `owner/change` | – | `extension/ezownerchange/modules/owner/module.php:39` |
| `owner/subtree` | – | `extension/ezownerchange/modules/owner/module.php:40` |

## INI-Plugin-Punkte (1)

| Einstellung | Klasse | Gesetzt in |
| --- | --- | --- |
| `[AuditEventSettings] Branches[ezownerchange]` | `expOwnerChangeAuditBranch` | `extension/ezownerchange/settings/audit.ini.append.php:6` |
