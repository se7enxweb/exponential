---
title: Das Skeleton 4.6.x
audience: dxp-developer
category: dxp-development
order: 43
summary: Wann Sie se7enxweb/exponential-platform-v4x-dxp-skeleton statt des v5-Skeletons verwenden - Platform 4.6.x auf Symfony 5.4 mit PHP 8.0 bis 8.5 (PHP 7.4 ohne Support nutzbar) - und wie es sich von der v5-DXP unterscheidet, die der Rest dieses Bereichs beschreibt.
keywords: 4.6, Skeleton v4x, exponential-platform-v4x-dxp-skeleton, Symfony 5.4, PHP 7.4, Ibexa 4.6, älteres PHP, Migrationsschritt, v4x skeleton, older PHP, migration step
type: help
---
# Das Skeleton 4.6.x

Diese Seite richtet sich an Entwickler, die PHP 8.3 noch nicht betreiben können oder ein Ibexa-4.6-Projekt Generation für
Generation umziehen. Der größte Teil dieses Bereichs beschreibt die v5-DXP; das Skeleton 4.6.x ist ihr älteres
Geschwister.

## Inhalt

1. [Die zwei Skeletons](#die-zwei-skeletons)
2. [Wann Sie das Skeleton 4.6.x verwenden](#wann-sie-das-skeleton-46x-verwenden)
3. [Was sich von v5 unterscheidet](#was-sich-von-v5-unterscheidet)
4. [Später auf v5 weiterziehen](#später-auf-v5-weiterziehen)

## Die zwei Skeletons

| Skeleton | Plattform | Symfony | PHP | Node.js |
|---|---|---|---|---|
| `se7enxweb/exponential-platform-dxp-skeleton` | v5 | 7.4 LTS | 8.3 oder neuer | 20 |
| `se7enxweb/exponential-platform-v4x-dxp-skeleton` | 4.6.x | 5.4 (`extra.symfony.require` auf `5.4.*` festgelegt) | 7.4 bis 8.5 | 20 |

Quelle: [DXP-Skeleton](../../funktionen/6.0/plattform-dxp-skeleton.md#was-es-ist).

## Wann Sie das Skeleton 4.6.x verwenden

- Ihre Server laufen mit PHP 7.4 bis 8.2 und können noch nicht umziehen.
- Sie migrieren ein Projekt auf Ibexa 4.0 bis 4.6: Kapitel 16 bringt es zuerst auf 4.6.latest, dann auf dieses Skeleton
  ([16.5.1](../../installation/16-migration-von-ez-platform-und-ibexa.md#1651-die-generation-wählen)).

Für ein neues Projekt bevorzugen Sie v5.

## Was sich von v5 unterscheidet

| Bereich | 4.6.x | v5 |
|---|---|---|
| Core-Tabellen | Namen `ez*`, plus einige Tabellen `ibexa_*` | jede Core-Tabelle `ibexa_*` |
| Bezeichner der Feldtypen | `ez*` (zum Beispiel `ezstring`) | `ibexa_*` (zum Beispiel `ibexa_string`) |
| Symfony | 5.4 | 7.4 |
| Befehlspräfix | `exponential:` mit Aliasen `ibexa:`, seit der Änderung des Kernel-Forks vom 07.04.2026 | dasselbe |

Befehle, Einstellungen und Paketnamen auf den anderen Seiten dieses Bereichs sind die von v5. Prüfen Sie auf 4.6.x jeden
mit `php bin/console list` und `composer show`, bevor Sie ihn verwenden.

## Später auf v5 weiterziehen

Der Umzug von 4.6 nach 5.0 umfasst die Tabellenumbenennungen und die neuen Bezeichner der Feldtypen
([16.5.6](../../installation/16-migration-von-ez-platform-und-ibexa.md#1656-die-datenbank-generation-für-generation)),
PHP 8.3 und Symfony 7.4. Planen Sie ihn als eigenes Projekt, mit einem Probelauf mit Zeitmessung an einer Kopie.

## Verwandte Seiten

- [Umstieg von Ibexa OSS oder eZ Platform](umstieg-von-ibexa-oss-oder-ez-platform.md)
- [Versionen von PHP, Symfony und Node.js](versionen-von-php-symfony-und-node.md)
- [Übersicht der Plattformpakete](../../spezifikationen/6.0/platform-paketzuordnung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
