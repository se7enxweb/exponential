---
title: Aufbewahrungspläne für Website-Inhalte
audience: government, project-manager, editor, admin
category: public-sector
order: 110
summary: Für Zuständige für Schriftgut und Projektleitungen öffentlicher Stellen - wie Sie mit Exponential einen Aufbewahrungsplan für Website-Inhalte und Systemdaten schreiben und anwenden, obwohl das Produkt keine Funktion für Aufbewahrungspläne hat - Klassen und Zustände, die die Regeln tragen, die zeitgesteuerten Skripte hide und unpublish und feste Abläufe.
keywords: Aufbewahrungsplan, Aktenplan, Aussonderung, Lebenszyklus von Inhalten, Aufbewahrungsregeln, Versteckdatum, unpublish_date, Objektzustände, Sektionen, Aufbewahrung des Audits, Papierkorb leeren, Routinen, öffentlicher Sektor, retention schedule, records schedule, disposition, content lifecycle, retention rules, hide date, object states, sections, audit retention, trash purge, routines, public sector
type: help
---
# Aufbewahrungspläne für Website-Inhalte

Diese Seite richtet sich an Zuständige für Schriftgut, Projektleitungen und Verantwortliche für Inhalte. Ein
Aufbewahrungsplan legt fest, wie lange jede Art von Information aufbewahrt wird und was danach geschieht. **Exponential
hat keine Funktion für Aufbewahrungspläne**: keine Aussonderungsbefugnis, keine automatischen Prüftermine. Es hat
Bausteine, um Ihre Regeln zu tragen und anzuwenden, und einige Systemdaten haben eigene Einstellungen zur Aufbewahrung.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Den Plan nach Klassen schreiben](#den-plan-nach-klassen-schreiben)
2. [Die Regel im Inhalt tragen](#die-regel-im-inhalt-tragen)
3. [Ihn anwenden](#ihn-anwenden)
4. [Systemdaten](#systemdaten)
5. [Verwandte Seiten](#verwandte-seiten)

## Den Plan nach Klassen schreiben

Inhalte in Exponential sind nach **Klassen** typisiert (Artikel, Bekanntmachung, Veranstaltung ...). Schreiben Sie den
Plan pro Klasse:

| Klasse | Auf der Website behalten | Danach | Kopie als Schriftgut |
|---|---|---|---|
| `<notice>` | bis zu ihrem Enddatum | verstecken oder in den Archivordner verschieben | beim Veröffentlichen in das Schriftgutsystem exportieren |
| `<news article>` | 2 Jahre | Archivordner | keine |
| `<event>` | bis zur Veranstaltung | verstecken | keine |
| `<consultation>` | bis zum Abschluss | Archivordner, öffentlich zugänglich | Dokumente exportieren |

Füllen Sie die Werte nach Ihrem Recht und Ihrer Richtlinie aus; diese Zeilen zeigen nur die Form.

## Die Regel im Inhalt tragen

- **Datumsfelder** in der Klasse (`hide_date`, `unpublish_date` oder ein eigenes Prüfdatum), die die Redaktion beim
  Veröffentlichen ausfüllt.
- **Objektzustände** (zum Beispiel `lifecycle`: Aktuell, Archiviert; `hold`: Keine, Aufbewahrungssperre), um zu
  kennzeichnen, wo ein Eintrag steht.
- **Sektionen** für unterschiedliche Zugriffsregeln archivierter Inhalte.

## Ihn anwenden

| Aktion | Werkzeug |
|---|---|
| An einem Datum verstecken | Cronjob-Skript `hide.php` mit `content.ini [HideSettings]` |
| An einem Datum in den Papierkorb verschieben | Cronjob-Skript `unpublish.php` mit `content.ini [UnpublishSettings]` und einem Attribut `unpublish_date`; nur dort verwenden, wo das Entfernen die Entscheidung ist |
| In ein Archiv verschieben | von Hand oder mit einem Inhaltsauftrag auf einer Auswahl |
| Als archiviert kennzeichnen | den Zustand setzen, auch für einen ganzen Teilbaum |
| Den Papierkorb nach einer Frist leeren | Cronjob-Teil `trashpurge` mit `[TrashSettings] KeepItemsForDays` |
| Überprüfen | eine Liste in Ihrem Template oder eine Suche, gefiltert nach Datum oder Zustand, monatlich abgearbeitet |

Beide Cronjob-Skripte stehen im Standardteil von `settings/cronjob.ini` und tun nichts, bis ihre Wurzelknoten gesetzt
sind. Eine Aufbewahrungssperre muss all das für die betroffenen Einträge stoppen (siehe „Aufbewahrungssperren“).

## Systemdaten

| Daten | Einstellung |
|---|---|
| Inhaltsversionen | Grenze der Versionsgeschichte in `content.ini [VersionManagement]` |
| Audit-Einträge | `audit.ini [AuditChannel_<channel>] LiveDays`, `ArchiveDays` |
| Protokolle | `logviewer.ini [RotationSettings] MaxAgeDays[<key>]` |
| Einwilligungsprotokoll | `mailpreferences.ini [ConsentSettings] RetentionDays` |
| Formulareinsendungen | keine Einstellung: ein manueller Ablauf |

Der Bereich Datenschutz dieser Hilfe enthält einen vollständigen Überblick über die Aufbewahrung.

## Verwandte Seiten

- [Alte Bekanntmachungen archivieren](alte-bekanntmachungen-archivieren.md)
- [Objektzustände für Schriftgut](objektzustaende-fuer-schriftgut.md)
- [Der Papierkorb und das Wiederherstellen von Inhalten](papierkorb-und-wiederherstellen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
