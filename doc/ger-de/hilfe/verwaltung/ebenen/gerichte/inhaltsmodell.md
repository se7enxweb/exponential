---
title: Inhaltsmodell für Gerichte
audience: government, website-creator, project-manager
category: public-sector
order: 30
summary: Mitgelieferte Klassen von Exponential, die zur Website eines Gerichts passen, und Rezepte für die Klassen Entscheidung, Gerichtsstandort und Ratgeber mit echten Datentypen, wobei der Veröffentlichungsstatus der Entscheidung als Objektzustand geführt wird.
keywords: Inhaltsmodell, Gericht, Spruchkörper, Klasse Entscheidung, Urteil, Gerichtsstandort, Klasse Ratgeber, Aktenzeichen, Objektzustand, anonymisiert, Klasseneditor, content model, court, tribunal, decision class, judgment, court location, guide class, case number, object state, anonymized, class editor
type: help
---
# Inhaltsmodell für Gerichte

Diese Seite richtet sich an Website-Ersteller und Projektverantwortliche, die die Klassen der Website eines Gerichts
oder Spruchkörpers entwerfen. Die genannten Klassen und Datentypen gibt es in Exponential
([sevenx_classes](../../../seitenpakete/sevenx-multisite/sevenx-classes-das-paket-der-inhaltsklassen.md), `kernel/classes/datatypes/`); der
Klasseneditor ist unter [Inhaltsklassen für Websites](../../../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md)
beschrieben.

## Inhalt

1. [Mitgelieferte Klassen, die passen](#mitgelieferte-klassen-die-passen)
2. [Hinzuzufügende Klasse: Entscheidung](#hinzuzufügende-klasse-entscheidung)
3. [Der Veröffentlichungsstatus als Objektzustand](#der-veröffentlichungsstatus-als-objektzustand)
4. [Hinzuzufügende Klasse: Gerichtsstandort](#hinzuzufügende-klasse-gerichtsstandort)
5. [Hinzuzufügende Klasse: Ratgeber](#hinzuzufügende-klasse-ratgeber)
6. [Regeln für Inhalte von Gerichten](#regeln-für-inhalte-von-gerichten)

## Mitgelieferte Klassen, die passen

| Klasse | Bezeichner | Verwendung |
|---|---|---|
| Folder | `folder` | Bereiche mit Hinweisen, Sammlungen von Entscheidungen nach Jahr oder Rechtsgebiet |
| Article | `article` | Nachrichten, Erklärungen |
| File | `file` | Formulare, Vorschriften, Verfahrensanweisungen, Jahresberichte |
| Documentation page | `documentation_page` | Hinweise über mehrere Kapitel |
| Link | `link` | Systeme für den elektronischen Rechtsverkehr, Beratungs- und Prozesskostenhilfe |
| Feedback form | `feedback_form` | allgemeine Anfragen (nie Verfahrensdokumente) |

## Hinzuzufügende Klasse: Entscheidung

| Attribut | Bezeichner | Datentyp | Warum |
|---|---|---|---|
| Titel | `title` | Textzeile (`ezstring`) | die Bezeichnung des Verfahrens, so wie die Vorschriften ihre Veröffentlichung erlauben (oft Initialen oder eine neutrale Bezeichnung) |
| Aktenzeichen | `case_number` | Textzeile | die zitierfähige Fundstelle |
| Gericht oder Kammer | `chamber` | Auswahl (`ezselection`) | |
| Datum der Entscheidung | `decision_date` | Datum (`ezdate`) | |
| Rechtsgebiet | `area` | Tags (`eztags`) | |
| Zusammenfassung | `summary` | XML-Block (`ezxmltext`) | ein kurzer Leitsatz, ebenfalls anonymisiert |
| Text der Entscheidung | `document` | Datei (`ezbinaryfile`) | das anonymisierte Dokument |
| Veröffentlicht bis | `hide_date` | Datum und Zeit (`ezdatetime`) | nur wo Vorschriften den Zeitraum der Veröffentlichung begrenzen; von `hide.php` verwendet |

Bewahren Sie in keinem Attribut nicht anonymisierten Text auf: Zusammenfassungen und Titel transportieren
personenbezogene Daten genauso leicht wie das Dokument.

## Der Veröffentlichungsstatus als Objektzustand

Eine Zustandsgruppe `decision_status` mit *In Prüfung*, *Veröffentlicht*, *Berichtigt*, *Zurückgezogen*:

| Verwendung | Wie |
|---|---|
| Den Status auf der Seite zeigen | Templates lesen den Zustand |
| Begrenzen, wer zurückziehen darf | `state/assign` mit `NewState` = Zurückgezogen nur für die Leitung der Veröffentlichung |
| Die Rechte der Prüfenden an Inhalten in Prüfung erhalten | `content/read` eingeschränkt nach `StateGroup_decision_status` |

Das Setzen eines Zustands erzeugt keine neue Version und ändert keinen Text
([Objektzustände](../../../../anleitungen/objektzustaende.md)).

## Hinzuzufügende Klasse: Gerichtsstandort

| Attribut | Bezeichner | Datentyp |
|---|---|---|
| Name | `name` | Textzeile |
| Anschrift | `address` | Textabschnitt (`eztext`) |
| Öffnungszeiten | `opening_hours` | XML-Block |
| Anfahrt und Barrierefreiheit | `access` | XML-Block (stufenloser Zugang, Induktionsschleifen, Ruheräume) |
| Einlasskontrolle | `security` | XML-Block (was mitgebracht werden darf) |
| Kontakt | `contact` | XML-Block (ein Postfach und eine Telefonnummer der Geschäftsstelle) |

## Hinzuzufügende Klasse: Ratgeber

| Attribut | Bezeichner | Datentyp | Warum |
|---|---|---|---|
| Titel | `title` | Textzeile | „Auf eine Klage erwidern“ |
| Kurz gesagt | `summary` | Textabschnitt | drei bis fünf Sätze in einfacher Sprache |
| Schritte | `body` | XML-Block | nummerierte Schritte mit Überschriften |
| Formulare | `forms` | Objektbeziehungen (`ezobjectrelationlist`) | Objekte der Klasse File |
| Gilt ab | `effective_from` | Datum | wenn sich Vorschriften ändern |
| Nächste Überprüfung | `review_date` | Datum | |

## Regeln für Inhalte von Gerichten

| Regel | Grund |
|---|---|
| Entscheidungen in eigener Klasse und Sektion | getrennte Berechtigungen und Prüfung |
| Ein Objekt je Entscheidung, nach der Veröffentlichung inhaltlich nie bearbeitet, außer zur Berichtigung, mit Hinweis | der veröffentlichte Text ist der Text des Gerichts |
| Für die Suche indizierte Attribute mit Sorgfalt gewählt | der Suchindex enthält, was durchsuchbar ist; markieren Sie nur Zusammenfassung und Metadaten als durchsuchbar, wenn Dokumente nicht indiziert werden dürfen |
| Formulare als Dateien, Hinweise als HTML | Hinweise müssen ohne Download lesbar sein |

## Verwandte Seiten

- [Gerichte](README.md)
- [Entscheidungen mit Sorgfalt für personenbezogene Daten veröffentlichen](entscheidungen-mit-sorgfalt-fuer-personenbezogene-daten-veroeffentlichen.md)
- [Datentypen für Ihre Felder wählen](../../../ersteller/website-ersteller/datentypen-waehlen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
