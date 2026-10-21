---
title: Inhaltsmodell für Länder und Regionen
audience: government, website-creator, project-manager
category: public-sector
order: 30
summary: Mitgelieferte Klassen von Exponential, die zur Website eines Landes oder einer Region passen, und Rezepte für die Klassen Plan, Förderprogramm, Beschluss und Dienststelle aus echten Datentypen, einschließlich des Datentyps für Kartenorte und seiner Folgen als Dienst eines Dritten.
keywords: Inhaltsmodell, Region, Land, Klasse Plan, Förderprogramm, Förderaufruf, Klasse Beschluss, Klasse Dienststelle, Klasseneditor, content model, regional, state, plan class, funding program, grant call, decision class, office class, ezgmaplocation, ezdatetime, ezselection, eztags, class editor
type: help
---
# Inhaltsmodell für Länder und Regionen

Diese Seite richtet sich an Website-Ersteller und Projektverantwortliche, die die Inhaltsklassen der Website eines
Bundeslands, einer Provinz, einer Region oder eines Kreises entwerfen. Sie listet die mitgelieferten Klassen auf, die
passen, und gibt vier Rezepte für Klassen. Die genannten Klassen und Datentypen gibt es in Exponential
([sevenx_classes](../../../seitenpakete/sevenx-multisite/sevenx-classes-das-paket-der-inhaltsklassen.md), die Datentypen in
`kernel/classes/datatypes/` und die genannten Erweiterungen); der Klasseneditor ist unter
[Inhaltsklassen für Websites](../../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md) beschrieben.

## Inhalt

1. [Mitgelieferte Klassen, die passen](#mitgelieferte-klassen-die-passen)
2. [Hinzuzufügende Klasse: Plan](#hinzuzufügende-klasse-plan)
3. [Hinzuzufügende Klasse: Förderprogramm](#hinzuzufügende-klasse-förderprogramm)
4. [Hinzuzufügende Klasse: Beschluss](#hinzuzufügende-klasse-beschluss)
5. [Hinzuzufügende Klasse: Dienststelle](#hinzuzufügende-klasse-dienststelle)
6. [Hinweise](#hinweise)

## Mitgelieferte Klassen, die passen

| Klasse | Bezeichner | Verwendung |
|---|---|---|
| Folder | `folder` | Themen, Fachbereiche, Einstiegsseiten eines Bereichs der Website |
| Article | `article` | Dienstleistungsseiten, Nachrichten, Erklärungen |
| File | `file` | Pläne, Niederschriften, Beschlüsse als Dokumente |
| Event | `event` / Event calendar `event_calendar` | Beteiligungsveranstaltungen, Informationsabende, regionale Veranstaltungen |
| Geo Article | `geo_article` | ein Artikel mit einem Kartenort (`ezgmaplocation`) |
| Link | `link` | Links zu Onlinediensten und zu Websites der Kommunen |
| Feedback form | `feedback_form` | Kontaktformulare |
| Job position | `ng_job_position` | Stellenangebote |

`geo_article` steht im Paket `sevenx_classes`, zeigt aber eine Google-Karte und braucht einen Google-Maps-Schlüssel
(`site.ini [SiteSettings] GMapsKey`) und die Erweiterung `ezgmaplocation`. Das Laden einer Google-Karte sendet Daten der
Besucher an einen Dritten; entscheiden Sie das mit Ihrer oder Ihrem Datenschutzbeauftragten und erwägen Sie eine einfache
Anschrift mit einem Link auf Ihr eigenes Kartenportal.

## Hinzuzufügende Klasse: Plan

| Attribut | Bezeichner | Datentyp | Warum |
|---|---|---|---|
| Titel | `title` | Textzeile (`ezstring`) | |
| Zusammenfassung | `summary` | XML-Block (`ezxmltext`) | was der Plan festlegt, in einfacher Sprache |
| Status | `status` | Auswahl (`ezselection`) | Entwurf zur Beteiligung, beschlossen, aufgehoben |
| Beschlossen am | `adopted_on` | Datum (`ezdate`) | |
| Ende der Beteiligung | `consultation_closes` | Datum und Zeit (`ezdatetime`) | angezeigt, solange offen |
| Dokumente | `documents` | Objektbeziehungen (`ezobjectrelationlist`) | der Plan, Anlagen, Karten als Objekte der Klasse File |
| Gebiet | `area` | Tags (`eztags`) | die Orte, die er abdeckt, aus einem Tag-Baum der Gebiete der Region |
| Themen | `themes` | Tags (`eztags`) | |

Ein Tag-Baum der Gebiete (Region > Kreis > Gemeinde) lässt Besucher jeden Plan für einen Ort auflisten; er braucht kein
GIS ([eztags](../../../../../eng-us/features/6.0/extensions/eztags.md)).

## Hinzuzufügende Klasse: Förderprogramm

| Attribut | Bezeichner | Datentyp | Warum |
|---|---|---|---|
| Titel | `title` | `ezstring` | |
| Wer sich bewerben kann | `eligibility` | `ezxmltext` | |
| Budget | `budget` | `ezstring` | so geschrieben wie veröffentlicht, mit Währung |
| Beginn | `opens` | `ezdatetime` | |
| Ende | `closes` | `ezdatetime` | Listen offener Aufrufe vergleichen es mit der aktuellen Zeit |
| Wie man sich bewirbt | `how_to_apply` | `ezxmltext` | mit einem Link zum Antragssystem |
| Link zum Antrag | `application_url` | URL (`ezurl`) | der Vorgang läuft meist anderswo |
| Dokumente | `documents` | `ezobjectrelationlist` | Leitfäden, Formulare |
| Ergebnisse | `results` | `ezxmltext` | nach der Entscheidung veröffentlicht |

Nehmen Sie Anträge in einem eigenen System entgegen, wenn sie personenbezogene oder finanzielle Daten enthalten; der
Informationssammler eignet sich für kurze Fragen, nicht für Förderanträge mit Anlagen und Bewertung.

## Hinzuzufügende Klasse: Beschluss

| Attribut | Bezeichner | Datentyp | Warum |
|---|---|---|---|
| Titel | `title` | `ezstring` | |
| Beschlussnummer | `number` | `ezstring` | die zitierfähige Fundstelle |
| Gefasst von | `body` | `ezselection` | Volksvertretung, Exekutive, Ausschuss |
| Datum | `decision_date` | `ezdate` | |
| Zusammenfassung | `summary` | `ezxmltext` | |
| Beschlussdokument | `document` | Datei (`ezbinaryfile`) | der unterzeichnete Text |
| Sitzung | `meeting` | Objektbeziehung (`ezobjectrelation`) | die Sitzungsseite, auf der er gefasst wurde |

## Hinzuzufügende Klasse: Dienststelle

| Attribut | Bezeichner | Datentyp | Warum |
|---|---|---|---|
| Name | `name` | `ezstring` | |
| Anschrift | `address` | Textabschnitt (`eztext`) | einfacher Text, ohne Karte lesbar |
| Öffnungszeiten | `opening_hours` | `ezxmltext` | eine Tabelle oder Liste |
| Telefon | `phone` | `ezstring` | eine Servicenummer, nicht die einer Person |
| E-Mail | `email` | E-Mail (`ezemail`) | ein gemeinsames Postfach |
| Barrierefreiheit des Gebäudes | `access` | `ezxmltext` | stufenloser Eingang, Parken, Induktionsschleife |
| Lage | `location` | Google Maps Location (`ezgmaplocation`) | optional; Dritter |

## Hinweise

| Regel | Grund |
|---|---|
| Daten als Datumsattribute | Listen offener Aufrufe und der Ablauf hängen davon ab |
| Orte als Tags oder einfacher Text, eine Karte nur, wo sie hilft | kein GIS im Produkt, Karten sind ein Dritter |
| Ein Objekt Beschluss je Beschluss, mit seiner Sitzung verknüpft | Zitate verlinken auf eine stabile Adresse |
| Gemeinsame Kontaktdaten in Objekten Dienststelle, aus Dienstleistungsseiten verknüpft | einmal geändert |

## Verwandte Seiten

- [Länder und Regionen](README.md)
- [Struktur, Sektionen und Rollen für Länder und Regionen](struktur-sektionen-und-rollen.md)
- [Datentypen für Ihre Felder wählen](../../../../../eng-us/help/creators/website-creators/choosing-datatypes.md)
- [Beziehungen und Wiederverwendung](../../../../../eng-us/help/creators/website-creators/relations-and-reuse.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
