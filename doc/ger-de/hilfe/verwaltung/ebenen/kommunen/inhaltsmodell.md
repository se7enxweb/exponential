---
title: Inhaltsmodell für Kommunen
audience: government, website-creator, project-manager
category: public-sector
order: 30
summary: Mitgelieferte Klassen von Exponential, die zur Website einer Kommune passen, und Rezepte für die Klassen Dienstleistung, Sitzung, Öffentliche Bekanntmachung und Anlaufstelle mit echten Datentypen, klein genug für ein kleines Team.
keywords: Inhaltsmodell, Kommune, Rat, Klasse Dienstleistung, Klasse Sitzung, Klasse Öffentliche Bekanntmachung, Anlaufstelle, Datum zum Verbergen, Klasseneditor, content model, municipality, council, service class, meeting class, public notice class, contact point, hide date, unpublish_date, ezdatetime, ezselection, class editor
type: help
---
# Inhaltsmodell für Kommunen

Diese Seite richtet sich an Website-Ersteller und Projektverantwortliche, die die Klassen der Website einer Stadt,
Gemeinde oder eines Rats entwerfen. Ein kleines Team pflegt die Website, daher bleibt das Modell klein: vier
hinzugefügte Klassen neben den mitgelieferten. Jede genannte Klasse und jeder genannte Datentyp existiert in Exponential
([sevenx_classes](../../../seitenpakete/sevenx-multisite/sevenx-classes-das-paket-der-inhaltsklassen.md), `kernel/classes/datatypes/`, die genannten
Erweiterungen); der Klasseneditor ist unter
[Inhaltsklassen für Websites](../../../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md) beschrieben.

## Inhalt

1. [Mitgelieferte Klassen, die passen](#mitgelieferte-klassen-die-passen)
2. [Hinzuzufügende Klasse: Dienstleistung](#hinzuzufügende-klasse-dienstleistung)
3. [Hinzuzufügende Klasse: Sitzung](#hinzuzufügende-klasse-sitzung)
4. [Hinzuzufügende Klasse: Öffentliche Bekanntmachung](#hinzuzufügende-klasse-öffentliche-bekanntmachung)
5. [Hinzuzufügende Klasse: Anlaufstelle](#hinzuzufügende-klasse-anlaufstelle)
6. [Das Modell klein halten](#das-modell-klein-halten)

## Mitgelieferte Klassen, die passen

| Klasse | Bezeichner | Verwendung |
|---|---|---|
| Folder | `folder` | Leistungsbereiche (Abfall, Parken ...), der Rat, über uns |
| Article | `article` | Nachrichten, Erklärungen |
| File | `file` | Tagesordnungen, Niederschriften, Formulare zum Drucken |
| Event, Event calendar | `event`, `event_calendar` | der Veranstaltungskalender |
| Feedback form | `feedback_form` | Kontakt und einfache Meldeformulare |
| Poll | `poll` | schnelle Meinungsfragen (keine Beteiligungsverfahren) |
| Link | `link` | Links zu den Onlinediensten anderer Systeme |
| Image, Gallery | `image`, `gallery` | Fotos |

## Hinzuzufügende Klasse: Dienstleistung

| Attribut | Bezeichner | Datentyp | Pflicht |
|---|---|---|---|
| Name der Dienstleistung | `title` | Textzeile (`ezstring`) | ja |
| Kurz gesagt | `summary` | Textabschnitt (`eztext`) | ja |
| Wer sie nutzen kann | `eligibility` | XML-Block (`ezxmltext`) | nein |
| Was sie kostet | `cost` | XML-Block | nein |
| Wie man sie beantragt | `how_to_apply` | XML-Block | ja |
| Online beantragen | `online_link` | URL (`ezurl`) | nein |
| Wie lange es dauert | `processing_time` | Textzeile | nein |
| Kontakt | `contact` | Objektbeziehung (`ezobjectrelation`) zu einer Anlaufstelle | nein |
| Zugehörige Dokumente | `documents` | Objektbeziehungen (`ezobjectrelationlist`) | nein |
| Themen und Lebenslagen | `tags` | Tags (`eztags`) | nein |
| Nächste Überprüfung | `review_date` | Datum (`ezdate`) | ja |

Das verpflichtende Überprüfungsdatum erinnert daran, dass Informationen zu Dienstleistungen veralten. Eine Liste der
Dienstleistungen, deren Überprüfungsdatum überschritten ist, lässt sich in einem Template für die Dashboard-Seite des
Webteams abrufen.

## Hinzuzufügende Klasse: Sitzung

| Attribut | Bezeichner | Datentyp | Warum |
|---|---|---|---|
| Gremium | `body` | Auswahl (`ezselection`) | Rat, ein Ausschuss |
| Datum und Uhrzeit | `meeting_date` | Datum und Zeit (`ezdatetime`) | |
| Ort | `place` | Textzeile | der Raum und die Anschrift |
| Tagesordnung | `agenda` | XML-Block | die Punkte, mit Links zu den Vorlagen |
| Vorlagen | `papers` | Objektbeziehungen | Objekte der Klasse File |
| Niederschrift | `minutes` | Objektbeziehung | das Objekt der Klasse File nach der Genehmigung |
| Status | `status` | Auswahl | geplant, Tagesordnung veröffentlicht, abgehalten, Niederschrift genehmigt, abgesagt |
| Ansehen | `webcast` | URL | eine Aufzeichnung oder ein Stream, falls vorhanden |

Den Veröffentlichungszyklus beschreibt [Ratssitzungen und örtliche Dienstleistungen](ratssitzungen-und-oertliche-dienstleistungen.md).

## Hinzuzufügende Klasse: Öffentliche Bekanntmachung

| Attribut | Bezeichner | Datentyp | Warum |
|---|---|---|---|
| Titel | `title` | Textzeile | |
| Art der Bekanntmachung | `notice_type` | Auswahl | Bauleitplanung, Straßensperrung, amtlich, sonstige |
| Text | `text` | XML-Block | |
| Gültig ab | `valid_from` | Datum | |
| Verbergen nach | `hide_date` | Datum und Zeit | von `hide.php` verwendet |
| Dokumente | `documents` | Objektbeziehungen | |

Um Bekanntmachungen automatisch zu verbergen, nennen Sie die Klasse und ihr Datumsattribut in `content.ini`:

```ini
# settings/override/content.ini.append.php
[HideSettings]
RootNodeList[]=<node id of the Notices folder>
HideDateAttributeList[public_notice]=hide_date
```

Leeren Sie dann den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`) und stellen Sie sicher, dass der Cronjob
`hide.php` ausführt (er steht im globalen Teil, `[CronjobSettings] Scripts[]`). Verborgene Bekanntmachungen bleiben für
die Akten in der Administration ([Cronjobs](../../../../anleitungen/cronjobs.md)). Der andere Cronjob, `unpublish.php`,
verschiebt Objekte mit einem Attribut namens genau `unpublish_date` in den Papierkorb; verbergen Sie Bekanntmachungen
lieber, wenn Sie nachweisen müssen, dass sie veröffentlicht waren.

## Hinzuzufügende Klasse: Anlaufstelle

| Attribut | Bezeichner | Datentyp |
|---|---|---|
| Name (ein Team oder Schalter, keine Person) | `name` | Textzeile |
| Anschrift | `address` | Textabschnitt |
| Öffnungszeiten | `opening_hours` | XML-Block |
| Telefon | `phone` | Textzeile |
| E-Mail | `email` | E-Mail (`ezemail`) |
| Barrierefreiheit des Gebäudes | `access` | XML-Block |

## Das Modell klein halten

| Versuchung | Besser |
|---|---|
| Eine Klasse je Fachbereich | eine Klasse Dienstleistung; der Fachbereich ist eine Sektion |
| Eine Klasse je Art von Bekanntmachung | eine Klasse Öffentliche Bekanntmachung mit einer Auswahl |
| Daten als freier Text | Datumsattribute, damit Listen und Ablauf funktionieren |
| Benannte Bedienstete als Kontakte | Anlaufstellen von Teams; Menschen wechseln, Schalter bleiben |

## Verwandte Seiten

- [Kommunen](README.md)
- [Struktur, Sektionen und Rollen für Kommunen](struktur-sektionen-und-rollen.md)
- [Datentypen für Ihre Felder wählen](../../../ersteller/website-ersteller/datentypen-waehlen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
