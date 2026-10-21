---
title: Inhaltsmodell für Rettungsdienste
audience: government, website-creator, project-manager
category: public-sector
order: 30
summary: Mitgelieferte Klassen von Exponential, die zur Website eines Rettungsdienstes passen, und Rezepte für die Klassen Einsatzmeldung, Vorsorgeratgeber und Wache - wenige Felder, schnell auf dem Telefon auszufüllen, mit einer Endzeit für das automatische Verbergen.
keywords: Inhaltsmodell, Rettungsdienste, Klasse Einsatzmeldung, Warnung, Vorsorgeratgeber, Klasse Wache, Datum zum Verbergen, Klasseneditor, content model, emergency services, incident update class, warning, preparedness guide, station class, hide date, ezdatetime, ezselection, eztags, class editor
type: help
---
# Inhaltsmodell für Rettungsdienste

Diese Seite richtet sich an Website-Ersteller und Projektverantwortliche, die die Klassen der Website eines
Rettungsdienstes entwerfen. Einsatzinhalte werden unter Druck geschrieben, daher hat ihre Klasse wenige Felder. Die
genannten Klassen und Datentypen gibt es in Exponential
([sevenx_classes](../../../seitenpakete/sevenx-multisite/sevenx-classes-das-paket-der-inhaltsklassen.md), `kernel/classes/datatypes/`); der
Klasseneditor ist unter [Inhaltsklassen für Websites](../../../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md)
beschrieben.

## Inhalt

1. [Mitgelieferte Klassen, die passen](#mitgelieferte-klassen-die-passen)
2. [Hinzuzufügende Klasse: Einsatzmeldung](#hinzuzufügende-klasse-einsatzmeldung)
3. [Hinzuzufügende Klasse: Vorsorgeratgeber](#hinzuzufügende-klasse-vorsorgeratgeber)
4. [Hinzuzufügende Klasse: Wache](#hinzuzufügende-klasse-wache)
5. [Gestaltungsregeln für Einsatzinhalte](#gestaltungsregeln-für-einsatzinhalte)

## Mitgelieferte Klassen, die passen

| Klasse | Bezeichner | Verwendung |
|---|---|---|
| Folder | `folder` | Einsätze, Vorbeugung, Dienstleistungen, über uns |
| Article | `article` | Nachrichten, Pressemitteilungen |
| File | `file` | Berichte, Pläne, Faltblätter |
| Job position | `ng_job_position` | Nachwuchsgewinnung, mit Bewerbungsfrist |
| Event | `event` | Tage der offenen Tür, Veranstaltungen zur Sicherheit im Ort |
| Image, Gallery | `image`, `gallery` | Fotos mit Alternativtext |
| Link | `link` | Warndienste, Partnerbehörden |

## Hinzuzufügende Klasse: Einsatzmeldung

| Attribut | Bezeichner | Datentyp | Pflicht | Warum |
|---|---|---|---|---|
| Schlagzeile | `title` | Textzeile (`ezstring`) | ja | was und wo |
| Status | `status` | Auswahl (`ezselection`) | ja | laufend, unter Kontrolle, beendet |
| Was zu tun ist | `action` | Textabschnitt (`eztext`) | ja | Anweisungen, einfacher Text, funktioniert auf jedem Gerät |
| Einzelheiten | `body` | XML-Block (`ezxmltext`) | nein | Straßensperrungen, Notunterkünfte, Hintergrund |
| Gebiet | `area` | Tags (`eztags`) | nein | ein Tag-Baum der Gebiete |
| Aktualisiert | `updated` | Datum und Zeit (`ezdatetime`) | ja | oben angezeigt |
| Verbergen nach | `hide_date` | Datum und Zeit | nein | von `hide.php` verwendet |

Textabschnitte statt Rich Text für die wichtigsten Felder: Sie sind auf dem Telefon schnell getippt und werden überall
als einfacher Text dargestellt, auch in RSS-Readern. Richten Sie das automatische Verbergen in
`settings/override/content.ini.append.php` ein:

```ini
[HideSettings]
RootNodeList[]=<node id of the Incidents folder>
HideDateAttributeList[incident_update]=hide_date
```

Leeren Sie danach den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`); `hide.php` läuft im globalen Cronjob-Teil
([Cronjobs](../../../../anleitungen/cronjobs.md)).

## Hinzuzufügende Klasse: Vorsorgeratgeber

| Attribut | Bezeichner | Datentyp |
|---|---|---|
| Titel | `title` | Textzeile |
| Kurz gesagt | `summary` | Textabschnitt |
| Schritte | `body` | XML-Block (nummerierte Schritte) |
| Checkliste zum Drucken | `checklist` | Objektbeziehung zu einer Datei (ein barrierefreies PDF) |
| Für wen | `audience` | Tags (Haushalte, Unternehmen, Schulen, Menschen mit Behinderungen) |
| Nächste Überprüfung | `review_date` | Datum (`ezdate`) |

## Hinzuzufügende Klasse: Wache

| Attribut | Bezeichner | Datentyp |
|---|---|---|
| Name | `name` | Textzeile |
| Anschrift | `address` | Textabschnitt |
| Kontakt außerhalb des Notrufs | `contact` | Textabschnitt |
| Dienstleistungen | `services` | Tags |
| Bild | `image` | Bild (`ezimage`) |

## Gestaltungsregeln für Einsatzinhalte

| Regel | Warum |
|---|---|
| Wenige Pflichtfelder | schnelleres Veröffentlichen unter Druck |
| Kein Pflichtbild | ein Bild verlangsamt das Veröffentlichen; fügen Sie später eines hinzu, wenn es nützt |
| Daten als Datum | Sortierung, Anzeige „aktualisiert“ und automatisches Verbergen |
| Ein Objekt je Einsatz, an Ort und Stelle aktualisiert | eine Adresse, die Menschen teilen; der Verlauf in den Versionen |
| Getrennte Objekte für getrennte Einsätze | eine klare Liste dessen, was gerade läuft |

## Verwandte Seiten

- [Rettungsdienste](README.md)
- [Warnungen, Verfügbarkeit, Wartungsmodus und statischer Cache](warnungen-verfuegbarkeit-wartung-und-statischer-cache.md)
- [Datentypen für Ihre Felder wählen](../../../ersteller/website-ersteller/datentypen-waehlen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
