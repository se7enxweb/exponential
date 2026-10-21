---
title: Bibliotheken und Kataloge
audience: businesses, project-manager, website-creator, editor
category: business-solutions
order: 1
summary: Die Einstiegsseite des Bereichs Bibliotheken - was eine Bibliothek, ein Archiv, ein Dokumentationszentrum oder jede Organisation mit einem Katalog mit Exponential bauen kann, was es nicht ist, die Seiten des Bereichs und die Reihenfolge, in der Sie sie lesen.
keywords: Bibliothek, Katalog, Archiv, Sammlung, Datensätze, Schlagwörter, digitale Sammlung, Dokumentationszentrum, Bibliothekswebsite, library, catalog, archive, collection, records, subject headings, digital collection, documentation center, library website
type: help
---
# Bibliotheken und Kataloge

Dieser Bereich ist für Bibliotheken, Archive, Dokumentationszentren, Schulen und jede Organisation, die einen Katalog
von Einträgen veröffentlicht: Bücher, Berichte, Normen, Aufnahmen, Produkte mit langen Datenblättern. Er zeigt, wie sich
Inhaltsmodell, Verschlagwortung, Suche, Dateien, PDF-Exporte und Feeds von Exponential zu einer öffentlichen
Katalogwebsite verbinden, und sagt deutlich, wo weiterhin ein eigenes Bibliothekssystem nötig ist.

## Inhalt

1. [Was Sie bauen können](#was-sie-bauen-können)
2. [Was Exponential nicht ist](#was-exponential-nicht-ist)
3. [Die Seiten dieses Bereichs](#die-seiten-dieses-bereichs)
4. [Empfohlene Lesereihenfolge](#empfohlene-lesereihenfolge)
5. [Benachbarte Bereiche](#benachbarte-bereiche)

## Was Sie bauen können

| Bedarf einer Bibliothek | Funktion von Exponential | Seite in diesem Bereich |
|---|---|---|
| Ein Eintrag pro Objekt mit strukturierten Feldern | Inhaltsklassen mit Datentypen wie `ezisbn`, `ezdate`, `ezobjectrelationlist` | [Katalogeinträge modellieren](katalogeintraege-modellieren.md) |
| Schlagwörter mit Hierarchie und Synonymen | Die Erweiterung `eztags`, mit dem Produkt ausgeliefert | [Tags und Schlagwörter](tags-und-schlagwoerter.md) |
| Einträge nach Wörtern finden und später nach Facetten | Die eingebaute Suchmaschine; Solr über `ezfind`, wenn der Katalog wächst | [Katalogsuche](katalogsuche.md) |
| Digitalisierte Dokumente und Downloads | File-Objekte, Downloads mit Berechtigungsprüfung, Textextraktion für die Suche | [Dateien und digitale Sammlungen](dateien-und-digitale-sammlungen.md) |
| Ein gedruckter Katalog oder PDF-Katalog | PDF-Exporte eines Knotens oder eines Baums | [PDF- und Druckexporte](pdf-und-druckexporte.md) |
| „Neu in der Bibliothek“ für Leser und andere Websites | Exporte als RSS 2.0, Atom und weitere Feeds | [Feeds für Neuerwerbungen](feeds-fuer-neuerwerbungen.md) |

All das gehört außer Solr zu einer Standardinstallation: `eztags`, `enhancedezbinaryfile` und `xrowmetadata` stehen im
Block `require` von `composer.json`, und `se7enxweb/ezfind` steht in dessen Block `suggest` mit dem Hinweis „Install it
with: composer require se7enxweb/ezfind:~3.0.7“.

## Was Exponential nicht ist

Exponential ist ein Content-Management-System. Im Repository gibt es keine Unterstützung für Austauschformate oder
Abläufe von Bibliotheken: keinen Import oder Export von MARC, keine Schnittstelle für Z39.50 oder SRU, keine Ausleihe,
keine Vormerkungen und keine Mahngebühren. Eine Suche in Code und Dokumentation nach diesen Begriffen findet nichts.
Wenn Sie sie brauchen, behalten Sie dafür Ihr integriertes Bibliothekssystem und verwenden Sie Exponential für die
öffentliche Website, die digitale Sammlung und die Neuigkeiten drumherum. Einträge kann ein Entwickler per Skript oder
über die REST API hereinholen
([Inhalte im Code anlegen und aktualisieren](../../../../eng-us/help/creators/app-creators/creating-content-in-code.md),
[REST API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md)).

> **Hinweis**
> Wenn dieser Bereich sagt, dass etwas möglich ist, nennt er die Funktion und die Seite, die sie dokumentiert. Wenn
> eine Funktion, die Sie brauchen, nicht erwähnt wird, gehen Sie davon aus, dass es sie nicht gibt, und bitten Sie
> Ihren Entwickler oder Partner, es zu prüfen.

## Die Seiten dieses Bereichs

| Seite | Was sie beantwortet |
|---|---|
| [Katalog planen](katalog-planen.md) | Umfang, Zielgruppen, die Arten von Einträgen, wer katalogisiert und die Entscheidungen, die zuerst zu treffen sind |
| [Katalogeinträge modellieren](katalogeintraege-modellieren.md) | Aus einem Eintrag eine Inhaltsklasse machen: Felder, Datentypen, Relationen, Pflichtfelder |
| [Tags und Schlagwörter](tags-und-schlagwoerter.md) | Ein kontrolliertes Vokabular mit eztags: Hierarchie, Synonyme, Übersetzungen, Berechtigungen |
| [Katalogsuche](katalogsuche.md) | Die eingebaute Suche, Suchstatistiken und wann sich Solr lohnt |
| [Dateien und digitale Sammlungen](dateien-und-digitale-sammlungen.md) | Digitalisierte Objekte, geschützte Downloads, Lizenzen, große Dateien, fehlende Dateien |
| [PDF- und Druckexporte](pdf-und-druckexporte.md) | Ein PDF eines Knotens oder eines Baums, einmalig oder bei Bedarf erzeugt, und was öffentlich ist |
| [Feeds für Neuerwerbungen](feeds-fuer-neuerwerbungen.md) | Feeds neuer Einträge für Leser, Partnerwebsites und Newsletter |
| [Checkliste für Bibliothekswebsites](checkliste-fuer-bibliothekswebsites.md) | Eine Checkliste von der Planung bis zum Start und die Routine danach |

## Empfohlene Lesereihenfolge

1. [Katalog planen](katalog-planen.md)
2. [Katalogeinträge modellieren](katalogeintraege-modellieren.md)
3. [Tags und Schlagwörter](tags-und-schlagwoerter.md)
4. [Katalogsuche](katalogsuche.md)
5. [Dateien und digitale Sammlungen](dateien-und-digitale-sammlungen.md)
6. [Feeds für Neuerwerbungen](feeds-fuer-neuerwerbungen.md) und [PDF- und Druckexporte](pdf-und-druckexporte.md)
7. [Checkliste für Bibliothekswebsites](checkliste-fuer-bibliothekswebsites.md)

## Benachbarte Bereiche

- [Newsletter](../newsletter/README.md): ein Newsletter über Neuerwerbungen.
- [Mitglieder](../mitglieder/README.md): Leserkonten und Inhalte nur für Mitglieder.
- [Gemeinnützige Organisationen](../gemeinnuetzige/README.md) und [Vereine und kleine Gruppen](../vereine/README.md):
  Viele Bibliotheken werden von einer solchen Organisation betrieben oder unterstützen eine.
- [Mehrsprachige Websites](../mehrsprachig/README.md): Kataloge in mehreren Sprachen.
- Öffentliche Bibliotheken in Trägerschaft einer Behörde: siehe auch
  [Behörden: hier beginnen](../../../../eng-us/help/government/government-start-here.md).

## Verwandte Seiten

- [Unternehmen: hier beginnen](../unternehmen-hier-beginnen.md)
- [Ihr Inhaltsmodell entwerfen](../../../../eng-us/help/creators/website-creators/designing-your-content-model.md)
- [Was Sie bauen können](../../../../eng-us/help/creators/website-creators/what-you-can-build.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
