---
title: DCAT und nationale Portale
audience: government, developer, website-creator, project-manager
category: public-sector
order: 170
summary: Exponential hat keine DCAT-Ausgabe und keinen CKAN-Connector. Diese Seite erklärt die Möglichkeiten, in Exponential veröffentlichte Datensätze in ein nationales oder europäisches Datenportal zu bringen - ein eigenes DCAT-Template, ein getrenntes Katalogprodukt oder die Registrierung von Hand.
keywords: DCAT, DCAT-AP, DCAT-AP.de, CKAN, Harvesting, Datenportal, nationales Portal, GovData, Katalog, RDF, JSON-LD, Turtle, Template, offene Daten, Metadatenexport, harvesting, data portal, national portal, catalog, template, open data, metadata export
type: help
---
# DCAT und nationale Portale

Diese Seite richtet sich an Projektleitungen und Entwickler, deren Datensätze in einem nationalen oder europäischen
Open-Data-Portal erscheinen müssen. Viele Portale sammeln Kataloge ein, die im W3C-Vokabular
[DCAT 3](https://www.w3.org/TR/vocab-dcat-3/) beschrieben sind, oft in einem Anwendungsprofil wie DCAT-AP. Exponential
erzeugt kein DCAT und hat keinen CKAN-Connector; das wurde durch eine Suche nach DCAT und CKAN im Kernel, in den
Einstellungen und in der Dokumentation geprüft. Die Möglichkeiten unten sind das, was bleibt.

## Inhalt

1. [Wie Harvesting funktioniert](#wie-harvesting-funktioniert)
2. [Möglichkeit A: ein DCAT-Template in Ihrem Design](#möglichkeit-a-ein-dcat-template-in-ihrem-design)
3. [Möglichkeit B: ein getrenntes Katalogprodukt](#möglichkeit-b-ein-getrenntes-katalogprodukt)
4. [Möglichkeit C: Registrierung von Hand](#möglichkeit-c-registrierung-von-hand)
5. [Auswählen](#auswählen)

## Wie Harvesting funktioniert

Ein Portal ruft in regelmäßigen Abständen ein Katalogdokument von Ihrer Website ab. Das Dokument listet die Datensätze
mit ihren Metadaten und den Adressen ihrer Distributionen auf. Das Portal speichert die Beschreibung und verlinkt auf
Ihre Dateien; die Dateien bleiben auf Ihrer Website. Ihre Adressen müssen also stabil (siehe „Stabile URLs für
Datensätze“) und die Metadaten vollständig sein.

Fragen Sie das Portal, welches Format und welches Profil es einsammelt (RDF/XML, Turtle, JSON-LD; welche Version von
DCAT-AP) und welche Felder es verlangt.

## Möglichkeit A: ein DCAT-Template in Ihrem Design

Ein Entwickler kann ein Template schreiben, das den Katalogordner als DCAT ausgibt, aus den Feldern Ihrer
Datensatzklasse (siehe „Eine Datensatzklasse entwerfen“, deren Bezeichner DCAT-Begriffen folgen):

1. Legen Sie eine Ansicht des Katalogordners an, zum Beispiel einen eigenen Siteaccess oder einen eigenen
   Ansichtsmodus, dessen Pagelayout `Content-Type: text/turtle` oder `application/ld+json` sendet und kein HTML um den
   Inhalt herum.
2. Holen Sie im Template die Datensätze unterhalb des Katalogordners und geben Sie einen `dcat:Dataset` pro Knoten und
   eine `dcat:Distribution` pro Unterobjekt aus, mit `dcat:downloadURL`, gebildet aus
   `content/download/<object ID>/<attribute ID>`.
3. Maskieren Sie jeden Wert für das Ausgabeformat, nicht mit der HTML-Maskierung.
4. Prüfen Sie die Ausgabe mit dem Validator des Portals, bevor Sie die Adresse bekanntgeben.

Grundlagen zu Templates: [Template-Overrides](../../../../eng-us/help/creators/website-creators/template-overrides.md) und
[Das Pagelayout](../../../../eng-us/help/creators/website-creators/the-pagelayout.md); Inhalte in Templates holen:
[die Hilfe zur Template-Sprache](../../template-sprache/README.md). Ein solches Template ist Ihr eigener Code, den Sie
bei jeder Änderung der Klasse oder des Profils pflegen müssen.

## Möglichkeit B: ein getrenntes Katalogprodukt

Betreiben Sie neben Exponential eine eigene Katalogsoftware, die Ihre Datensatzbeschreibungen einsammelt oder
importiert und Portalen DCAT und eine API anbietet. Exponential bleibt dann der Ort, an dem Seiten und Dateien
veröffentlicht werden; der Katalog verweist darauf. Die Daten für den Katalog können aus einem JSON- oder CSV-Export der
Datensatzklasse mit `xrowextract` kommen (siehe „Inhalte als CSV, JSON oder XML exportieren“) oder aus der REST-API
(siehe „Die REST-API als Datenschnittstelle“). Das bringt ein weiteres System, das betrieben und abgesichert werden muss.

## Möglichkeit C: Registrierung von Hand

Kleine Programme mit wenigen Datensätzen können jeden Datensatz von Hand im eigenen Webformular des Portals
registrieren und auf die Datensatzseite in Exponential verlinken. Das braucht keine Entwicklung, aber jede Änderung muss
im Portal wiederholt werden.

## Auswählen

| Situation | Vorschlag |
|---|---|
| Weniger als ein Dutzend Datensätze, selten geändert | C |
| Viele Datensätze, ein Entwickler verfügbar, ein Portalprofil | A |
| Ein nationales Programm mit vielen Veröffentlichenden oder ein Portal, das seine API braucht | B |

Wofür Sie sich auch entscheiden, halten Sie die Datensatzfelder in Exponential vollständig: Sie sind die Quelle für
alle drei.

## Verwandte Seiten

- [Was offene Daten für eine öffentliche Stelle bedeuten](was-offene-daten-bedeuten.md)
- [Metadatenfelder für Datensätze](metadatenfelder-fuer-datensaetze.md)
- [Das Pagelayout](../../../../eng-us/help/creators/website-creators/the-pagelayout.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
