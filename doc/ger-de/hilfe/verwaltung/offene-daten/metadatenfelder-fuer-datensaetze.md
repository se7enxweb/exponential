---
title: Metadatenfelder für Datensätze
audience: government, editor, website-creator
category: public-sector
order: 50
summary: Eine Referenz für Redaktionen und Websiteersteller, die jeden gängigen Metadatenbegriff für Datensätze (DCAT 3) einem Datentyp von Exponential zuordnet, mit Hinweisen, was hineingehört und welche Felder ein Katalog meist verlangt.
keywords: Metadaten, DCAT, Datensatzmetadaten, Titel, Beschreibung, Herausgeber, Lizenz, Thema, Schlüsselwörter, veröffentlicht, geändert, Häufigkeit, Zuordnung der Datentypen, offene Daten, metadata, dataset metadata, title, description, publisher, license, theme, keywords, issued, modified, frequency, datatype mapping, open data
type: reference
---
# Metadatenfelder für Datensätze

Diese Referenz richtet sich an Redaktionen, die Datensätze beschreiben, und an Websiteersteller, die die
Datensatzklasse entwerfen. Sie listet die Metadatenbegriffe des W3C-[Data Catalog Vocabulary (DCAT 3)](https://www.w3.org/TR/vocab-dcat-3/)
auf, den Datentyp von Exponential, der jeden aufnimmt, und wie guter Inhalt dafür aussieht. Die Datentypen sind die
in [Datentypen wählen](../../ersteller/website-ersteller/datentypen-waehlen.md) aufgeführten.

## Inhalt

1. [Begriffe für Datensätze](#begriffe-für-datensätze)
2. [Begriffe für Distributionen](#begriffe-für-distributionen)
3. [Gute Metadaten schreiben](#gute-metadaten-schreiben)
4. [Felder, die Exponential für Sie füllt](#felder-die-exponential-für-sie-füllt)

## Begriffe für Datensätze

| DCAT-Begriff | Bezeichnung für die Redaktion | Datentyp | Was hineingehört |
|---|---|---|---|
| `dct:title` | Titel | `ezstring` | Ein Name, nach dem ein Mensch suchen kann: „Wartungsanfragen Straßenbeleuchtung 2025“, nicht „SLMR_v3“ |
| `dct:description` | Beschreibung | `ezxmltext` | Was die Daten sind, wie sie erhoben wurden, bekannte Lücken, wie oft sie sich ändern |
| `dct:publisher` | Herausgeber | `ezstring` oder `ezobjectrelation` | Die Organisation, zum Beispiel „Amt für Beispiele“ |
| `dcat:contactPoint` | Kontakt | `ezemail` | Ein Teampostfach, nie eine persönliche Adresse |
| `dct:license` | Lizenz | `ezselection` | Eine der Lizenzen, die Ihre Stelle genehmigt hat |
| `dcat:theme` | Thema | `eztags` oder `ezselection` | Ein oder mehrere Themen aus einer festen Liste |
| `dcat:keyword` | Schlüsselwörter | `ezkeyword` | Freie Wörter, nach denen Menschen suchen, in jeder Sprache |
| `dct:issued` | Erstmals veröffentlicht | `ezdate` | Das Datum, an dem der Datensatz erstmals öffentlich wurde |
| `dct:modified` | Zuletzt geändert | `ezdate` | Das Datum, an dem sich die Daten zuletzt geändert haben (nicht der Seitentext) |
| `dct:accrualPeriodicity` | Aktualisierungshäufigkeit | `ezselection` | Täglich, wöchentlich, monatlich, jährlich, unregelmäßig, nie |
| `dct:spatial` | Abgedecktes Gebiet | `ezstring` | Das Gebiet, auf das sich die Daten beziehen |
| `dct:temporal` | Abgedeckter Zeitraum | `ezstring` | Zum Beispiel „Januar 2020 bis Dezember 2025“ |
| `dct:language` | Sprache der Daten | `ezselection` | Die Sprache der Werte in der Datei, die von der Seite abweichen kann |
| `dct:conformsTo` | Standard oder Schema | `ezurl` | Ein Link auf das Schema oder den Standard, dem die Daten folgen |

## Begriffe für Distributionen

| DCAT-Begriff | Bezeichnung | Datentyp | Was hineingehört |
|---|---|---|---|
| `dct:title` | Titel | `ezstring` | „CSV, alle Jahre“ |
| `dcat:downloadURL` | Datei | `ezbinaryfile` | Die Datei selbst; ihre Download-Adresse erzeugt Exponential |
| `dcat:accessURL` | Adresse | `ezurl` | Wenn die Daten anderswo bereitgestellt werden (eine API, ein anderes Portal) |
| `dct:format` | Format | `ezselection` | CSV, JSON, XML, XLSX, ODS ... |
| `dcat:byteSize` | Größe | vom Datentyp Datei angezeigt | Exponential zeigt die Größe einer hochgeladenen Datei |
| `dcat:mediaType` | Medientyp | vom Datentyp Datei angezeigt | Exponential speichert den MIME-Typ des Uploads |

## Gute Metadaten schreiben

- **Beschreiben Sie die Daten, nicht das Projekt.** Sagen Sie, was eine Zeile ist, was jede Spalte bedeutet und woher
  die Daten kommen. Ein kurzes Datenwörterbuch in der Beschreibung oder eine eigene CSV-Datei mit
  Spaltendefinitionen hilft Nachnutzern am meisten.
- **Verwenden Sie dieselben Wörter wie Ihre Nutzer.** Prüfen Sie unter **Setup > Suchstatistiken**, was Besucher
  eintippen ([Suchstatistiken](../../../anleitungen/urls-und-aliase.md#5-suchstatistiken)), und fügen Sie diese Wörter
  den Schlüsselwörtern hinzu.
- **Halten Sie die Daten richtig.** „Zuletzt geändert“ bezieht sich auf die Daten. Exponential verzeichnet in seiner
  Versionsgeschichte, wann sich die Seite geändert hat; das ist ein anderes Datum.
- **Eine Lizenz pro Datensatz.** Wenn Teile unter anderen Bedingungen stehen, teilen Sie den Datensatz auf.
- **Einfache Sprache** in Titeln und Beschreibungen; vermeiden Sie interne Kürzel und Abkürzungen.

## Felder, die Exponential für Sie füllt

| Information | Woher sie kommt |
|---|---|
| Erstellungs- und Änderungszeit der Seite | die Versionen des Objekts ([Inhaltsgeschichte](../../../anleitungen/inhaltsverlauf.md)) |
| Wer sie veröffentlicht hat | der Ersteller der Version und das Prüfprotokoll ([Prüfprotokoll](../../../funktionen/6.0/pruefprotokoll.md)) |
| Adresse der Seite | der URL-Alias des Knotens |
| Größe und MIME-Typ einer Datei | der Datentyp Datei |
| Sprachen, in denen die Seite existiert | die Übersetzungen des Objekts ([Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)) |

## Verwandte Seiten

- [Eine Datensatzklasse entwerfen](eine-datensatzklasse-entwerfen.md)
- [Verschlagworten mit eztags](../../ersteller/website-ersteller/verschlagworten-mit-eztags.md)
- [Metadaten und Teilen in sozialen Medien](../../ersteller/website-ersteller/metadaten-und-teilen-in-sozialen-medien.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
