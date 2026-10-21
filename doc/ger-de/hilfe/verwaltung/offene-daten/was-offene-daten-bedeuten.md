---
title: Was offene Daten für eine öffentliche Stelle bedeuten
audience: government, project-manager, editor, admin
category: public-sector
order: 20
summary: Für Menschen in einer öffentlichen Stelle, die „offene Daten veröffentlichen“ sollen - was der Begriff in der Open Definition und der EU-Richtlinie über offene Daten bedeutet, was DCAT ist und welche Teile der Aufgabe ein Content-Management-System wie Exponential übernehmen kann und welche nicht.
keywords: offene Daten, offene Verwaltungsdaten, Open Data, Open Definition, Richtlinie über offene Daten, 2019/1024, DCAT, Datensatz, Datenkatalog, Informationen des öffentlichen Sektors, Weiterverwendung, PSI, open government data, Open Data Directive, dataset, data catalog, public sector information, re-use
type: help
---
# Was offene Daten für eine öffentliche Stelle bedeuten

Diese Seite richtet sich an Führungskräfte, Redaktionen und Administratoren in einer öffentlichen Stelle, die offene
Daten veröffentlichen sollen und wissen wollen, was der Begriff bedeutet, bevor sie die Arbeit planen. Sie erklärt die
drei Bezugspunkte, die die meisten Open-Data-Programme verwenden (die Open Definition, die EU-Richtlinie über offene
Daten und das W3C-Vokabular DCAT), und trennt dann den Teil der Arbeit, den Exponential übernehmen kann, von dem Teil,
den es nicht übernehmen kann. Die Angaben zum Produkt stammen aus der Dokumentation von Exponential, die in jedem
Abschnitt verlinkt ist.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Offene Daten in einem Absatz](#offene-daten-in-einem-absatz)
2. [Die Open Definition](#die-open-definition)
3. [Die EU-Richtlinie über offene Daten](#die-eu-richtlinie-über-offene-daten)
4. [DCAT: Datensätze für Kataloge beschreiben](#dcat-datensätze-für-kataloge-beschreiben)
5. [Was ein Content-Management-System beiträgt](#was-ein-content-management-system-beiträgt)
6. [Was Exponential nicht tut](#was-exponential-nicht-tut)
7. [Ein erster Plan](#ein-erster-plan)

## Offene Daten in einem Absatz

Offene Daten sind Daten, die jeder für jeden Zweck nutzen, verändern und weitergeben darf, höchstens mit der Auflage,
die Quelle zu nennen und sie offen zu halten. Für eine öffentliche Stelle heißt das meist: Datensätze, die sie bereits
hat (Haushalte, Register, Statistiken, Fahrpläne, Servicestellen), werden als Dateien oder über eine Schnittstelle
veröffentlicht, in einem Format, das eine Maschine lesen kann, unter einer offenen Lizenz, unter einer Adresse, die
weiter funktioniert, mit einer Beschreibung, die sagt, was die Daten sind, wer verantwortlich ist, wie aktuell sie
sind und wie man sie zitiert.

## Die Open Definition

Die [Open Definition 2.1](https://opendefinition.org/od/2.1/en/) legt fest, wann ein Werk „offen“ ist. Prüfen Sie für
einen Datensatz in der Praxis diese Punkte:

| Punkt der Definition | Was er für einen Datensatz bedeutet, den Sie veröffentlichen |
|---|---|
| Offene Lizenz | Der Datensatz trägt eine Lizenz, die Nutzung, Weitergabe und Veränderung durch jeden für jeden Zweck erlaubt |
| Zugang | Der ganze Datensatz ist verfügbar, vorzugsweise über das Internet herunterladbar, zu höchstens angemessenen Kosten (normalerweise kostenlos) |
| Maschinenlesbarkeit | Er wird in einer Form angeboten, die ein Computer verarbeiten kann, zum Beispiel CSV statt einer gescannten Tabelle |
| Offenes Format | Das Format lässt sich mit freier Software lesen und unterliegt keinen Nutzungsbeschränkungen |

Eine PDF-Seite mit einer Tabelle kann eine gute Veröffentlichung sein, ist aber in diesem Sinne keine offenen Daten.
Eine CSV-Datei daneben ist es.

## Die EU-Richtlinie über offene Daten

In der Europäischen Union legt die [Richtlinie (EU) 2019/1024](https://eur-lex.europa.eu/eli/dir/2019/1024/oj) über
offene Daten und die Weiterverwendung von Informationen des öffentlichen Sektors die Regeln für Dokumente öffentlicher
Stellen fest, die zur Weiterverwendung bereitgestellt werden. Zu den Themen, die sie behandelt, gehören die Formate, in
denen Dokumente angeboten werden sollen, die Entgelte, die verlangt werden dürfen, die Lizenzen, die Nutzung von
Programmierschnittstellen für dynamische Daten und eine Gruppe hochwertiger Datensätze. Jeder Mitgliedstaat setzt sie
in eigenes Recht um (in Deutschland etwa mit dem Datennutzungsgesetz), sodass die genauen Pflichten Ihrer Stelle von
diesem nationalen Recht abhängen; fragen Sie Ihre Rechtsberatung, welche gelten.

Außerhalb der EU haben viele Rechtsordnungen eigene Open-Data-Richtlinien. In den Vereinigten Staaten ist das Portal
des Bundes zur Informationsfreiheit [FOIA.gov](https://www.foia.gov/); die Pflichten zu offenen Daten ergeben sich aus
eigenen Gesetzen und Richtlinien, die diese Seite nicht behandelt.

## DCAT: Datensätze für Kataloge beschreiben

Das W3C-[Data Catalog Vocabulary, Version 3](https://www.w3.org/TR/vocab-dcat-3/) (DCAT 3) ist das Vokabular, mit dem
Kataloge Datensätze beschreiben. Seine zentralen Begriffe sind:

| DCAT-Begriff | Bedeutung | Typisches Feld auf Ihrer Datensatzseite |
|---|---|---|
| `dcat:Catalog` | Eine kuratierte Sammlung von Datensatzbeschreibungen | Ihre Datenkatalogseite |
| `dcat:Dataset` | Eine Sammlung von Daten, veröffentlicht von einem Akteur | Eine Datensatzseite |
| `dcat:Distribution` | Eine zugängliche Form des Datensatzes, etwa eine CSV-Datei | Eine herunterladbare Datei |
| `dcat:DataService` | Eine Schnittstelle, die Zugang zu Daten gibt | Ihre REST-API oder ein Feed |

Nationale und europäische Portale sammeln DCAT-Beschreibungen (oft in einem Profil wie DCAT-AP) aus den Katalogen
öffentlicher Stellen ein. Damit Ihr Katalog eingesammelt werden kann, muss er eine solche Beschreibung in
maschinenlesbarer Form anbieten.

## Was ein Content-Management-System beiträgt

Exponential ist ein Content-Management-System. Bei offenen Daten ist es auf der Seite des Veröffentlichens stark:

- **Dateien unter stabilen Adressen.** Eine Datensatzdatei ist ein Inhaltsobjekt mit einem Dateifeld; die
  Download-Adresse `content/download/<object ID>/<attribute ID>` prüft die Rechte und funktioniert, solange das Objekt
  existiert ([Dateien und Downloads](../../ersteller/website-ersteller/dateien-und-downloads.md)).
- **Beschreibungen als strukturierte Inhalte.** Sie können eine Inhaltsklasse „Datensatz“ mit Feldern für Titel,
  Beschreibung, Herausgeber, Lizenz, Aktualisierungshäufigkeit und Dateien festlegen
  ([Inhaltsklassen für Websites](../../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md)).
- **Feeds neuer und geänderter Datensätze.** RSS-2.0- oder Atom-Exporte listen die neuesten Objekte unterhalb eines
  Ordners auf ([RSS-Feeds](../../../anleitungen/rss-feeds.md)).
- **Eine JSON-Schnittstelle.** Die REST-API liefert Knoten, Unterknoten und Felder als JSON unter `/api/ezp/v1/...` und
  `/api/ezp/v2/...` ([REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md)).
- **Versionen und ein Prüfprotokoll**, damit Sie zeigen können, was sich wann geändert hat
  ([Inhaltsgeschichte](../../../anleitungen/inhaltsverlauf.md), [Prüfprotokoll](../../../funktionen/6.0/pruefprotokoll.md)).

## Was Exponential nicht tut

Ehrliche Grenzen, im Repository geprüft:

- **Keine DCAT-Ausgabe und kein CKAN-Connector.** Nichts im Kernel, in den mitgelieferten Einstellungen oder in der
  Dokumentation erzeugt DCAT (RDF, JSON-LD) oder spricht die CKAN-API. Ein Katalog, der eingesammelt werden muss,
  braucht ein Template, das Sie selbst schreiben und das DCAT ausgibt, oder ein eigenes Katalogprodukt neben der
  Website.
- **Es wird keine Datensatzklasse mitgeliefert.** Sie entwerfen die Klasse; die Seiten dieses Bereichs zeigen, wie.
- **Keine Prüfung der Daten.** Exponential speichert die Datei, die Sie hochladen; es prüft nicht, ob eine CSV-Datei
  wohlgeformt ist oder einem Schema entspricht.
- **Standardmäßig kein Lizenzfeld.** Eine Lizenz ist ein Feld, das Sie Ihrer Klasse hinzufügen.

## Ein erster Plan

1. Listen Sie die Datensätze auf, die Sie haben, und entscheiden Sie, welche veröffentlicht werden dürfen (Datenschutz,
   Sicherheit, Rechte Dritter).
2. Wählen Sie mit Ihrer Rechtsberatung für jeden Datensatz eine offene Lizenz.
3. Legen Sie in Exponential die Datensatzklasse und einen Katalogordner an.
4. Veröffentlichen Sie jeden Datensatz als Seite mit seinen Dateien, unter einer Adresse, die Sie behalten wollen.
5. Fügen Sie einen Feed neuer Datensätze hinzu und entscheiden Sie, ob Ihr Katalog DCAT für ein nationales Portal
   braucht.

Die anderen Seiten dieses Bereichs behandeln jeden Schritt der Reihe nach.

## Verwandte Seiten

- [Dateien und Downloads](../../ersteller/website-ersteller/dateien-und-downloads.md)
- [REST-API v1 und v2](../../../anleitungen/rest-api-v1-und-v2.md)
- [RSS-Feeds](../../../anleitungen/rss-feeds.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
