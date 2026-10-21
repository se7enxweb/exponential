---
title: Inhaltsmodell für den öffentlichen Gesundheitsdienst
audience: government, website-creator, project-manager
category: public-sector
order: 30
summary: Mitgelieferte Klassen von Exponential, die zur Website des öffentlichen Gesundheitsdienstes passen, und Rezepte für die Klassen Gesundheitsthema, Warnung und Sprechstelle mit echten Datentypen, einschließlich Überprüfungsdaten, medizinischer Freigabe sowie Schweregrad und Endzeit von Warnungen.
keywords: Inhaltsmodell, öffentliche Gesundheit, Klasse Gesundheitsthema, Klasse Warnung, Schweregrad, Klasse Sprechstelle, Überprüfungsdatum, medizinische Freigabe, Klasseneditor, content model, public health, health topic class, alert class, severity, clinic class, review date, clinical sign-off, ezdatetime, ezselection, eztags, class editor
type: help
---
# Inhaltsmodell für den öffentlichen Gesundheitsdienst

Diese Seite richtet sich an Website-Ersteller und Projektverantwortliche, die die Klassen der Website des öffentlichen
Gesundheitsdienstes entwerfen. Die genannten Klassen und Datentypen gibt es in Exponential
([sevenx_classes](../../../seitenpakete/sevenx-multisite/sevenx-classes-das-paket-der-inhaltsklassen.md), `kernel/classes/datatypes/`, genannte
Erweiterungen); der Klasseneditor ist unter
[Inhaltsklassen für Websites](../../../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md) beschrieben.

## Inhalt

1. [Mitgelieferte Klassen, die passen](#mitgelieferte-klassen-die-passen)
2. [Hinzuzufügende Klasse: Gesundheitsthema](#hinzuzufügende-klasse-gesundheitsthema)
3. [Hinzuzufügende Klasse: Warnung](#hinzuzufügende-klasse-warnung)
4. [Hinzuzufügende Klasse: Sprechstelle](#hinzuzufügende-klasse-sprechstelle)
5. [Einstellungen für Übersetzungen](#einstellungen-für-übersetzungen)

## Mitgelieferte Klassen, die passen

| Klasse | Bezeichner | Verwendung |
|---|---|---|
| Folder | `folder` | Themengruppen, Ordner für Warnungen, Bereich für Fachleute |
| Article | `article` | Nachrichten, Pressemitteilungen |
| File | `file` | Berichte, Plakate, Datendateien |
| Documentation page | `documentation_page` | Verfahrensanweisungen mit Kapiteln für Fachleute |
| Event | `event` | Impftage, Informationsveranstaltungen |
| Video | `video` | Erklärvideos (mit Untertiteln aus der Quelle) |
| Feedback form | `feedback_form` | allgemeiner Kontakt (nie Patientendaten) |

## Hinzuzufügende Klasse: Gesundheitsthema

| Attribut | Bezeichner | Datentyp | Warum |
|---|---|---|---|
| Titel | `title` | Textzeile (`ezstring`) | der Name, den die Menschen verwenden („Grippe“, nicht nur der Fachbegriff) |
| Kurz gesagt | `summary` | Textabschnitt (`eztext`) | drei Kernbotschaften in einfacher Sprache |
| Hinweise | `body` | XML-Block (`ezxmltext`) | Überschriften: Symptome, was zu tun ist, wann man Hilfe holt |
| Wann man dringend Hilfe holt | `urgent` | XML-Block | gut sichtbar angezeigt |
| Quellen | `sources` | XML-Block | Evidenz und Nachweise |
| Medizinisch geprüft am | `reviewed_on` | Datum (`ezdate`) | auf der Seite angezeigt |
| Geprüft von (Funktion) | `reviewed_by` | Textzeile | eine Funktion, kein Name, wenn Ihre Richtlinie das bevorzugt |
| Nächste Überprüfung | `review_date` | Datum | steuert die Liste der überfälligen Seiten |
| Themen | `tags` | Tags (`eztags`) | Erkrankungen, Zielgruppen, Lebensphasen |
| Zielgruppe | `audience` | Auswahl (`ezselection`) | Öffentlichkeit, Fachleute |

## Hinzuzufügende Klasse: Warnung

| Attribut | Bezeichner | Datentyp | Warum |
|---|---|---|---|
| Schlagzeile | `title` | Textzeile | kurz, einfach, die Handlung zuerst |
| Schweregrad | `severity` | Auswahl | Information, Hinweis, Warnung |
| Was zu tun ist | `action` | Textabschnitt | ein bis drei Sätze |
| Einzelheiten | `body` | XML-Block | |
| Betroffenes Gebiet | `area` | Tags | ein Tag-Baum der Gebiete |
| Herausgegeben | `issued` | Datum und Zeit (`ezdatetime`) | |
| Zuletzt aktualisiert | `updated` | Datum und Zeit | auf der Seite angezeigt |
| Endet | `hide_date` | Datum und Zeit | von `hide.php` verwendet, um die Warnung herunterzunehmen |

Um Warnungen automatisch zu beenden: in `settings/override/content.ini.append.php`
`[HideSettings] RootNodeList[]=<alerts folder node>` und `HideDateAttributeList[alert]=hide_date`, dann den INI-Cache
leeren. Der Cronjob `hide.php` (globaler Teil) verbirgt Warnungen, deren Ende vorbei ist
([Cronjobs](../../../../anleitungen/cronjobs.md)). Prüfen Sie, dass der globale Teil oft genug für Ihre Warnungen läuft; eine
Warnung, die um 18:00 Uhr enden soll, endet beim nächsten Lauf danach.

## Hinzuzufügende Klasse: Sprechstelle

| Attribut | Bezeichner | Datentyp |
|---|---|---|
| Name | `name` | Textzeile |
| Dienste | `services` | Tags (Impfung, Früherkennung ...) |
| Anschrift | `address` | Textabschnitt |
| Öffnungszeiten | `opening_hours` | XML-Block |
| Wie man bucht | `booking` | XML-Block und URL (`ezurl`) |
| Barrierefreiheit | `access` | XML-Block |

## Einstellungen für Übersetzungen

| Attribut | Übersetzbar | Warum |
|---|---|---|
| Texte (Titel, Zusammenfassung, Hinweise, Handlung) | ja | jede Sprache hat ihre eigenen |
| Daten (herausgegeben, endet, Überprüfungsdaten) | nein | derselbe Zeitpunkt in jeder Sprache |
| Schweregrad, Tags | nein für die Auswahl; Tags haben eigene Übersetzungen | ein Wert für alle Sprachen |

Entfernen Sie im Klasseneditor das Häkchen **Übersetzbar** bei Datums- und Auswahlattributen, bevor Inhalte angelegt
werden ([Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md)).

## Verwandte Seiten

- [Öffentliche Gesundheit](README.md)
- [Warnungen in vielen Sprachen und in einfacher Sprache](warnungen-mehrsprachig-und-in-einfacher-sprache.md)
- [Datentypen für Ihre Felder wählen](../../../ersteller/website-ersteller/datentypen-waehlen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
