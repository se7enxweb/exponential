---
title: Katalog planen
audience: businesses, project-manager, editor
category: business-solutions
order: 10
summary: Die Entscheidungen, die eine Bibliothek, ein Archiv oder ein Dokumentationszentrum trifft, bevor eine Katalogwebsite auf Exponential gebaut wird - Umfang, Zielgruppen, Arten von Einträgen, Vokabulare, wer katalogisiert, wie Einträge hereinkommen, was öffentlich ist und woran sich Erfolg zeigt.
keywords: Katalog planen, Plan für die Bibliothekswebsite, Arten von Einträgen, Ablauf der Katalogisierung, Vokabular, Import, öffentliche und eingeschränkte Einträge, Archiv, Dokumentationszentrum, catalog planning, library website plan, record types, cataloging workflow, vocabulary, import, public and restricted records, archive, documentation center
type: help
---
# Katalog planen

Diese Seite ist für Bibliotheksleitungen, Archivare, Verantwortliche für Dokumentation und Projektleitungen. Sie listet
in einfacher Sprache die Entscheidungen auf, die zu treffen sind, bevor jemand Inhaltsklassen baut, und verweist auf die
Funktion, die jede umsetzt. Ein Katalog ist leicht zu beginnen und schwer umzubauen, wenn erst Tausende Einträge
existieren; diese Planung zahlt sich also aus.

## Inhalt

1. [Umfang: was in den Katalog kommt](#umfang-was-in-den-katalog-kommt)
2. [Zielgruppen und was sie tun](#zielgruppen-und-was-sie-tun)
3. [Arten von Einträgen](#arten-von-einträgen)
4. [Vokabulare](#vokabulare)
5. [Wie Einträge hereinkommen](#wie-einträge-hereinkommen)
6. [Wer katalogisiert und wer prüft](#wer-katalogisiert-und-wer-prüft)
7. [Öffentlich, eingeschränkt und intern](#öffentlich-eingeschränkt-und-intern)
8. [Sprachen](#sprachen)
9. [Erfolg messen](#erfolg-messen)
10. [Ein Arbeitsblatt zur Planung](#ein-arbeitsblatt-zur-planung)

## Umfang: was in den Katalog kommt

Schreiben Sie auf, was die Website enthalten muss und was anderswo bleibt:

| Frage | Typische Antworten |
|---|---|
| Ist das der vollständige Katalog oder ein Schaufenster? | Eine Auswahl, eine Sondersammlung, nur digitalisierte Objekte |
| Bleibt das Bibliothekssystem das führende System? | Ja für Ausleihe und Bestände; die Website enthält die öffentliche Beschreibung |
| Welche Objekte haben Dateien? | Digitalisierte Berichte, Bilder, Aufnahmen |
| Wie groß wird die Zahl der Einträge erwartet? | Wichtig für Suche und Hosting ([Katalogsuche](katalogsuche.md)) |

Exponential hat keine Ausleihe, keine Vormerkungen und keine Unterstützung für MARC (siehe
[Bibliotheken und Kataloge](README.md#was-exponential-nicht-ist)); planen Sie die Website um Beschreibung, Auffindbarkeit
und digitale Bereitstellung herum.

## Zielgruppen und was sie tun

Listen Sie Ihre Zielgruppen und ihre Hauptaufgabe auf der Website auf: Leser, die einen Titel suchen, Forschende, die
ein Thema durchstöbern, Lehrkräfte, die Material herunterladen, Partnerinstitutionen, die Ihre Liste der Neuerwerbungen
weiterverwenden. Jede Aufgabe sollte einem dieser Wege entsprechen: nach Thema stöbern (Tags), suchen (Suche),
herunterladen (Dateien), abonnieren (Feeds oder Newsletter) oder drucken (PDF-Export).

## Arten von Einträgen

Jede Art von Eintrag wird eine **Inhaltsklasse** mit eigenen Feldern
([Inhaltsklassen für Websites](../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md)). Halten Sie die Zahl
der Klassen klein; eine Art von Eintrag verdient eine eigene Klasse, wenn sich ihre Felder unterscheiden, nicht wenn
sich ihr Thema unterscheidet. Ein typischer Satz:

- [ ] Buch oder Monografie
- [ ] Artikel oder Bericht
- [ ] Audio- oder Videoaufnahme
- [ ] Person oder Organisation (Autoren, Urheber) als eigene Objekte, mit den Einträgen verknüpft
- [ ] Sammlung oder Reihe (ein Container für Einträge)

Einzelheiten unter [Katalogeinträge modellieren](katalogeintraege-modellieren.md).

## Vokabulare

Entscheiden Sie, welche Listen kontrolliert sind und wer sie verantwortet:

| Vokabular | Werkzeug |
|---|---|
| Schlagwörter mit Ober- und Unterbegriffen | Tag-Baum von `eztags` ([Tags und Schlagwörter](tags-und-schlagwoerter.md)) |
| Eine kurze feste Liste (Format, Sprache des Objekts) | Feld `ezselection` ([Datentypen wählen](../../../../eng-us/help/creators/website-creators/choosing-datatypes.md)) |
| Freie Schlüsselwörter | offenes Vokabular von `eztags` oder `ezkeyword` |
| Wo ein Objekt im Menü steht | der Inhaltsbaum, nicht Tags |

## Wie Einträge hereinkommen

| Weg | Wann | Wo er dokumentiert ist |
|---|---|---|
| Von Katalogisierenden in der Administration eingegeben | Kleine Sammlungen, besondere Objekte | [Inhalte bearbeiten](../../inhalte-bearbeiten.md) |
| Von einem Entwickler per Skript importiert | Ein vorhandener Export aus einem anderen System | [Inhalte im Code anlegen und aktualisieren](../../../../eng-us/help/creators/app-creators/creating-content-in-code.md) |
| Über die REST API angelegt | Ein anderes System schiebt Einträge herein | [REST API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md) |
| Aus dem RSS-Feed einer anderen Website gelesen | Ein Partner veröffentlicht einen Feed von Objekten | [RSS-Feeds](../../../anleitungen/rss-feeds.md) |
| Hochgeladene Writer-Dokumente | Berichte, die in LibreOffice oder OpenOffice geschrieben sind | [ezodf](../../../../eng-us/features/6.0/extensions/ezodf.md) |

Für Importe empfiehlt die Anleitung für Entwickler Remote-IDs für wiederholbare Importe, damit ein zweiter Lauf des
Imports Einträge aktualisiert, statt sie zu verdoppeln
([Inhalte im Code anlegen und aktualisieren](../../../../eng-us/help/creators/app-creators/creating-content-in-code.md)).

## Wer katalogisiert und wer prüft

- Katalogisierende brauchen Rechte zum Anlegen und Bearbeiten im Teilbaum des Katalogs und `tags/add` nur unterhalb der
  Tags, die sie betreuen ([Verschlagworten mit eztags, Permissions](../../../../eng-us/help/creators/website-creators/tagging-with-eztags.md#permissions)).
- Wenn Einträge geprüft werden müssen, bevor sie öffentlich werden, verwenden Sie einen Freigabe-Workflow
  ([Workflows](../../../anleitungen/workflows.md)).
- Jede Veröffentlichung behält ihre Versionen, sodass Sie sehen können, wer einen Eintrag wann geändert hat
  ([Verlauf von Inhalten](../../../anleitungen/inhaltsverlauf.md)).

## Öffentlich, eingeschränkt und intern

Trennen Sie mit Sektionen, was alle lesen dürfen, von dem, was nur Mitglieder oder Mitarbeiter lesen dürfen
([Sektionen](../../../anleitungen/sektionen.md),
[Sektionen und Objektzustände](../../../../eng-us/help/creators/website-creators/sections-and-object-states.md)). Downloads durchlaufen
eine Berechtigungsprüfung; eine Datei in einer Sektion nur für Mitglieder können also nur Mitglieder herunterladen
([Dateien und Downloads](../../../../eng-us/help/creators/website-creators/files-and-downloads.md#how-a-download-works)).

## Sprachen

Wenn Sie Einträge in mehreren Sprachen beschreiben, entscheiden Sie, welche Felder übersetzbar sind (Titel und
Beschreibungen meist, Kennungen wie eine ISBN nicht) und ob Tags übersetzt werden
([Mehrsprachige Websites](../../../../eng-us/help/creators/website-creators/multilingual-sites.md)).

## Erfolg messen

Wählen Sie einige Kennzahlen, die Sie regelmäßig prüfen: Suchen, die nichts finden (Suchstatistiken,
[Katalogsuche](katalogsuche.md#aus-dem-lernen-wonach-leser-suchen)), Downloads, Abonnenten von Feeds und neu
hinzugefügte Einträge pro Monat. Verwenden Sie Ihre eigenen Zahlen; diese Dokumentation liefert keine Vergleichswerte.

## Ein Arbeitsblatt zur Planung

```text
Catalog scope:                ...
Master system for holdings:   ...
Expected records (now / 3 y): ...
Record types (classes):       ...
Controlled vocabularies:      ...
Vocabulary owner:             ...
How records arrive:           typed / script import / REST / RSS import
Approval before publishing:   yes / no
Restricted material:          ... (section: ...)
Languages:                    ...
Search engine:                built-in / Solr (ezfind)
Measures reviewed monthly:    ...
```

## Verwandte Seiten

- [Bibliotheken und Kataloge](README.md)
- [Katalogeinträge modellieren](katalogeintraege-modellieren.md)
- [Ihre Website planen](../../../../eng-us/help/creators/website-creators/planning-your-website.md)
- [Workshops zur Inhaltsmodellierung](../../fuehrung/workshops-zur-inhaltsmodellierung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
