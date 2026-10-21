---
title: Schlagworte und Themen für Datensätze
audience: government, editor, website-creator
category: public-sector
order: 130
summary: Wie Sie Datensätze nach Themen einordnen, damit Menschen und Portale sie finden - eine kontrollierte Themenliste mit eztags (Tag-Baum, Synonyme, Tag-Seiten) oder ein einfaches Auswahlfeld, und Regeln, die die Liste klein und stabil halten.
keywords: Tags, Schlagworte, eztags, Themen, Taxonomie, Klassifikation, Synonyme, kontrolliertes Vokabular, Schlüsselwörter, dcat:theme, Datenkatalog, offene Daten, themes, taxonomy, classification, synonyms, controlled vocabulary, keywords, data catalog, open data
type: help
---
# Schlagworte und Themen für Datensätze

Diese Seite richtet sich an Redaktionen und Websiteersteller, die Datensätze einordnen. Eine kontrollierte
Themenliste macht einen Katalog durchstöberbar und erlaubt Ihnen, Ihre Themen später denen eines nationalen oder
europäischen Portals zuzuordnen. Exponential bietet zwei Wege: die Erweiterung `eztags`
([Verschlagworten mit eztags](../../../../eng-us/help/creators/website-creators/tagging-with-eztags.md)) und den Datentyp Auswahl des
Kernels.

## Inhalt

1. [Themen und Schlüsselwörter sind verschieden](#themen-und-schlüsselwörter-sind-verschieden)
2. [Möglichkeit 1: eztags](#möglichkeit-1-eztags)
3. [Möglichkeit 2: ein Auswahlfeld](#möglichkeit-2-ein-auswahlfeld)
4. [Regeln für eine Themenliste](#regeln-für-eine-themenliste)
5. [Zuordnung zu den Themen eines Portals](#zuordnung-zu-den-themen-eines-portals)

## Themen und Schlüsselwörter sind verschieden

| | Themen | Schlüsselwörter |
|---|---|---|
| Wer die Wörter wählt | das Datenteam, einmal | die Redaktion, pro Datensatz |
| Wie viele | eine kurze, feste Liste | so viele wie nützlich |
| Zweck | Stöbern und Filtern | Suche |
| DCAT-Begriff | `dcat:theme` | `dcat:keyword` |
| Datentyp | `eztags` oder `ezselection` | `ezkeyword` |

## Möglichkeit 1: eztags

`eztags` führt einen Tag-Baum mit Synonymen, gibt jedem Tag eine Seite, die alles damit Verschlagwortete auflistet,
und bietet im Bearbeitungsformular eine automatische Vervollständigung an.

1. Öffnen Sie **Tags** (`/tags/dashboard`) und legen Sie ein Tag der obersten Ebene „Datenthemen“ mit einem
   Untertag pro Thema an.
2. Fügen Sie Synonyme hinzu, wo Menschen andere Wörter verwenden („Verkehr“ mit dem Synonym „Mobilität“).
3. Fügen Sie der Datensatzklasse ein Tags-Feld (`theme`) hinzu und beschränken Sie es in seinen Datentypoptionen auf
   den Teilbaum „Datenthemen“.
4. Verschlagworten Sie jeden Datensatz; verlinken Sie die Seiten der Themen-Tags von der Katalogseite aus.

Mit Solr lassen sich Tag-Wolken aus der Suche erstellen (`eztags` `TagCloudOverSolr`)
([Suche mit ezfind und Solr](../../../../eng-us/help/creators/website-creators/search-with-ezfind-and-solr.md)).

## Möglichkeit 2: ein Auswahlfeld

Wenn `eztags` nicht aktiv ist, fügen Sie ein Auswahlfeld (`ezselection`) namens Thema mit einer Option pro Thema hinzu
und kreuzen die Mehrfachauswahl an, wenn ein Datensatz mehrere haben kann. Das ist einfach und fest; es hat keine
Synonyme und keine Tag-Seiten, sodass Sie die Datensätze pro Thema mit einem Template oder einem Ordner pro Thema
auflisten.

## Regeln für eine Themenliste

- Halten Sie sie kurz: Ein Dutzend bis zwanzig Themen lassen sich leicht überblicken.
- Benennen Sie Themen in einfacher Sprache und übersetzen Sie sie für jede Inhaltssprache.
- Benennen Sie ein Thema nie stillschweigend um; kündigen Sie Änderungen an, weil Nachnutzer danach filtern.
- Geben Sie jedem Datensatz mindestens ein Thema.
- Überprüfen Sie die Liste einmal im Jahr anhand von **Setup > Suchstatistiken**, um zu sehen, wonach Menschen suchen.

## Zuordnung zu den Themen eines Portals

Nationale und europäische Portale haben oft eigene Themenlisten. Führen Sie eine kleine Zuordnungstabelle (Ihr Thema,
Thema des Portals) in der Beschreibung des Tags „Datenthemen“ oder auf einer Seite für das Datenteam. Wenn Sie später
ein Template für einen DCAT-Export schreiben, verwendet es diese Tabelle. Exponential liefert das Vokabular keines
Portals mit.

## Verwandte Seiten

- [Verschlagworten mit eztags](../../../../eng-us/help/creators/website-creators/tagging-with-eztags.md)
- [eztags](../../../../eng-us/features/6.0/extensions/eztags.md)
- [Datentypen wählen](../../../../eng-us/help/creators/website-creators/choosing-datatypes.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
