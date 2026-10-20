---
title: "Thema: MongoDB als Datenbank"
audience: press
category: press-media
order: 70
summary: Ein Themenbeitrag darüber, dass Exponential 6.0.14 sein gesamtes Content-Repository in MongoDB speichert - was funktioniert, die dokumentierten Grenzen, wie es zu den sechs unterstützten Datenbanken passt - mit druckbaren Fakten, Quellen und den Aussagen, die Sie vermeiden sollten.
keywords: Thema, MongoDB, Dokumentdatenbank, NoSQL, Datenbank, 6.0.14, Content-Repository, Grenzen, Datenbanken, PHP-Erweiterung mongodb, story, document database, database, content repository, limits, databases, PHP mongodb extension
type: help
---
# Thema: MongoDB als Datenbank

Diese Seite ist für technische Journalisten. Ein Content-Management-System mit relationalem Inhaltsmodell, das auf einer
Dokumentdatenbank läuft, ist ungewöhnlich; Exponential 6.0.14 hat es hinzugefügt, mit aufgeschriebenen Grenzen.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Was neu ist, und seit wann](#was-neu-ist-und-seit-wann)
3. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
4. [Die dokumentierten Grenzen](#die-dokumentierten-grenzen)
5. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
6. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)

## Das Thema in einem Absatz

„Exponential 6.0.14 (Juni 2026) kann das gesamte Content-Repository statt in MySQL, PostgreSQL oder SQLite in MongoDB
speichern, und der Einrichtungsassistent bietet MongoDB neben den anderen Datenbanksystemen an“
([MongoDB als Datenbank](../../../eng-us/features/6.0/mongodb-database-support.md)). Ein Adapter verwandelt die SQL-artigen
Aufrufe des Kernels in MongoDB-Operationen. Das Projekt listet auf, was funktioniert und was nicht, und rät Lesern, die
Grenzen zu lesen, bevor sie es für eine produktive Website wählen.

## Was neu ist, und seit wann

| Was | Wann | Quelle |
|---|---|---|
| MongoDB 8 als Datenbank | 1. Juni 2026 | [Juni 2026](../../../eng-us/history/2026/2026-06a.md) |
| Erstes Release damit, 6.0.14 | 7. Juni 2026 | [Die 6.0.x-Releases auf einen Blick](../../../eng-us/help/press/the-6-0-x-releases-at-a-glance.md) |

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| MongoDB steht an dritter Stelle der Rangfolge: SQLite, PostgreSQL, MongoDB, MariaDB, Oracle, MySQL | [Datenbankunterstützung](../datenbankunterstuetzung.md) |
| Funktioniert: Bearbeiten und Veröffentlichen von Inhalten, der Inhaltsbaum, URL-Aliase, Rollen, die Administration, die öffentliche Website, der Einrichtungsassistent | [Was funktioniert und was nicht](../../../eng-us/features/6.0/mongodb-database-support.md#what-works-and-what-does-not-june-2026) |
| Eine vollständige Portierungsreferenz dokumentiert jede berührte Kernel-Klasse | [MongoDB als Datenbank](../../../eng-us/features/6.0/mongodb-database-support.md) |

## Die dokumentierten Grenzen

Laut Funktionsseite (Juni 2026): Das tiefe Neuerzeugen von URL-Aliasen nach dem Verschieben von Teilbäumen ist
unvollständig; die Listenseiten für Rollen und Richtlinien können leer sein, obwohl die Berechtigungen durchgesetzt
werden; einige Cronjobs sind nicht portiert; die Erweiterung `eztags` ist auf MongoDB nicht nutzbar; der Bestellvorgang
im Shop und die Blockplanung von `ezflow` sind nicht durchgängig getestet; handgeschriebenes SQL in Erweiterungen
liefert nichts zurück.

## Was Sie nicht behaupten sollten

| Sagen Sie nicht | Sagen Sie stattdessen |
|---|---|
| „Läuft vollständig auf MongoDB, mit jeder Funktion“ | Funktioniert für den Kern; die Grenzen sind dokumentiert |
| „Auf MongoDB produktionsreif“ | „Das Projekt rät, vor dem Produktiveinsatz die Grenzen zu lesen“ |
| „Experimentell“ (wie das Pressekit es im Juni formulierte) | Es wurde im Release 6.0.14 ausgeliefert, mit dokumentierten Grenzen |

## Blickwinkel und Ideen für Überschriften

- „Ein Inhaltsmodell von 2002 auf einer Dokumentdatenbank.“
- „Sechs Datenbanken, ein CMS.“

## Verwandte Seiten

- [Thema: sechs Datenbanken](../../../eng-us/help/press/story-databases.md)
- [Thema: SQLite als Standard](../../../eng-us/help/press/story-sqlite-by-default.md)
- [Datenbankunterstützung](../datenbankunterstuetzung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
