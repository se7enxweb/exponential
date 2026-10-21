---
title: Eine Klasse für Veranstaltungen modellieren
audience: government, website-creator
category: public-sector
order: 30
summary: Welche Attribute Sie der mitgelieferten Klasse Event für öffentliche Veranstaltungen hinzufügen - Ort, Barrierefreiheit des Veranstaltungsorts, Zielgruppe, Buchungsstatus, Buchungslink, Veranstalter, Schlagworte - wie Sie sie auf einer Live-Site sicher hinzufügen und wann eine eigene Klasse sinnvoll ist.
keywords: Klasse für Veranstaltungen, Klasseneditor, Attribut hinzufügen, Ort, Veranstaltungsort, Barrierefreiheit, Zielgruppe, Buchungsstatus, Veranstalter, Template aktualisieren, event class, class editor, add attribute, place, venue, accessibility, audience, booking status, organizer, eztags, ezobjectrelation, ezselection, ezurl, template update
type: help
---
# Eine Klasse für Veranstaltungen modellieren

Diese Seite richtet sich an Site-Ersteller, die die Klasse Event für Veranstaltungen des öffentlichen Sektors anpassen.
Die mitgelieferte Klasse ([Was mitgeliefert wird](was-fuer-veranstaltungen-mitgeliefert-wird.md)) hat einen Titel, einen Text, Schlüsselwörter als
Kategorie sowie Beginn und Ende. Öffentliche Veranstaltungen brauchen meist mehr strukturierte Informationen, damit sie
einheitlich gezeigt und gefiltert werden können.

## Inhalt

1. [Hinzuzufügende Attribute](#hinzuzufügende-attribute)
2. [Sie sicher hinzufügen](#sie-sicher-hinzufügen)
3. [Eine Klasse für Orte](#eine-klasse-für-orte)
4. [Erweitern oder eine eigene Klasse anlegen](#erweitern-oder-eine-eigene-klasse-anlegen)

## Hinzuzufügende Attribute

| Attribut | Bezeichner | Datentyp | Warum |
|---|---|---|---|
| Ort | `place` | Objekt Verknüpfung (`ezobjectrelation`) zu einem Place-Objekt | ein Ort, von vielen Veranstaltungen wiederverwendet |
| Barrierefreiheit bei der Veranstaltung | `access` | XML Block | stufenloser Zugang, Induktionsschleife, Gebärdensprache, Ruheraum |
| Zielgruppe | `audience` | Tags (`eztags`) oder Auswahlliste | Einwohner, Unternehmen, Familien |
| Themen | `tags` | Tags | ersetzen oder ergänzen die Kategorie Keywords |
| Buchung | `booking` | Auswahlliste: freier Eintritt, Buchung erforderlich, ausgebucht, abgesagt, verschoben | oben angezeigt |
| Buchungslink | `booking_url` | URL (`ezurl`) | wenn Buchungen anderswo laufen |
| Veranstalter | `organizer` | Textzeile | eine Abteilung oder ein Partner, keine Person |
| Kosten | `cost` | Textzeile | „Kostenlos“ oder der Betrag |
| Bild | `image` | Objekt Verknüpfung zu einem Bild | optional, mit Alternativtext |

## Sie sicher hinzufügen

Attribute einer Klasse hinzuzufügen, die Objekte hat, ist sicher: Bestehende Objekte erhalten leere Werte
([Inhaltsklassen für Websites](../../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md#changing-a-class-on-a-live-site)).

1. Machen Sie die Änderung zuerst auf einer Testinstallation; übertragen Sie sie mit einem Paket
   ([Pakete](../../../../anleitungen/pakete.md)).
2. **Setup > Klassen**, Gruppe Content, **Event**, **Bearbeiten**; fügen Sie jedes Attribut hinzu; schalten Sie
   **Übersetzbar** für Booking, Booking link und Place aus; **OK**.
3. Aktualisieren Sie die Templates (`full/event.tpl`, die Kalender- und Programmansicht, Ihre Listen), damit sie die
   neuen Felder zeigen; die mitgelieferten Templates von ezwebin kennen sie nicht
   ([Template-Overrides](../../../../../eng-us/help/creators/website-creators/template-overrides.md)).
4. Leeren Sie die Caches: `php bin/php/ezcache.php --clear-tag=template` und
   `php bin/php/ezcache.php --clear-id=content` ([Caches](../../../../anleitungen/caches.md)).

Große Klassen brauchen eine Weile für die Änderung; tun Sie es außerhalb der Hauptzeiten und mit einer Sicherung.

## Eine Klasse für Orte

| Attribut | Bezeichner | Datentyp |
|---|---|---|
| Name | `name` | Textzeile |
| Adresse | `address` | Textabschnitt |
| Anfahrt | `directions` | XML Block |
| Barrierefreiheit des Veranstaltungsorts | `access` | XML Block |
| Kartenstandort | `location` | Google Maps Location (`ezgmaplocation`), optional, ein Dienst eines Drittanbieters |

Veranstaltungen verknüpfen einen Ort; ein Template auf der Seite des Ortes listet seine kommenden Veranstaltungen mit
einem Fetch umgekehrter Beziehungen.

## Erweitern oder eine eigene Klasse anlegen

| Die mitgelieferte Klasse Event erweitern | Eine eigene Klasse anlegen |
|---|---|
| Sie verwenden die Kalenderansichten von ezwebin | Ihr Design hat ohnehin eigene Templates |
| die mitgelieferten Felder passen | Sie brauchen eine andere Struktur (zum Beispiel Sitzungstermine eines Rates) |
| ein späterer Paketimport von `sevenx_classes` soll dieselbe Klasse aktualisieren | Ihre Klasse darf von Paketimporten nicht berührt werden |

Eine eigene Klasse funktioniert mit denselben Fetches, wenn sie ein Startattribut vom Typ Datum und Zeit hat; passen
Sie die Bezeichner von Klasse und Attribut in den Templates an.

## Verwandte Seiten

- [Veranstaltungen](README.md)
- [Barrierefreie Informationen zu Veranstaltungen](barrierefreie-informationen-zu-veranstaltungen.md)
- [Datentypen für Ihre Felder wählen](../../../../../eng-us/help/creators/website-creators/choosing-datatypes.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
