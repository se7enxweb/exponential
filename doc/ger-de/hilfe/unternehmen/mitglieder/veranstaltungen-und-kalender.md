---
title: Veranstaltungen und Kalender
audience: businesses, editor, website-creator
category: business-solutions
order: 50
summary: Was Exponential einer Mitgliederorganisation für Veranstaltungen bietet - die Klassen Event, Event calendar und Multicalendar von ezwebin mit Kalender- und Programmansicht, Veranstaltungen nur für Mitglieder, Anmeldung über Formulare, Ankündigung von Veranstaltungen und die ehrliche Liste dessen, was nicht eingebaut ist, wie Tickets und der Export von Kalenderdateien.
keywords: Veranstaltungen, Veranstaltungskalender, Kalender, Programm, Multicalendar, ezwebin, Klasse Event, from_time, to_time, Anmeldeformular, Information Collector, Veranstaltungen nur für Mitglieder, Einladungen, Tickets, iCal, events, event calendar, calendar, program, event class, sign-up form, information collector, members-only events, invitations, ticketing
type: help
---
# Veranstaltungen und Kalender

Diese Seite ist für Organisatoren von Treffen, Kursen, Konferenzen und geselligen Veranstaltungen, die diese auf der
Mitgliederwebsite haben möchten. Sie beschreibt die Veranstaltungsklassen, die mit dem Design `ezwebin` kommen, geprüft
im Repository und auf dem Demonstrationsserver, wie Mitglieder Veranstaltungen finden und sich anmelden und was Sie
ergänzen müssten.

## Inhalt

1. [Die Veranstaltungsklassen](#die-veranstaltungsklassen)
2. [Kalender- und Programmansicht](#kalender--und-programmansicht)
3. [Mehrere Kalender in einem](#mehrere-kalender-in-einem)
4. [Öffentliche Veranstaltungen und Veranstaltungen nur für Mitglieder](#öffentliche-veranstaltungen-und-veranstaltungen-nur-für-mitglieder)
5. [Für eine Veranstaltung anmelden](#für-eine-veranstaltung-anmelden)
6. [Veranstaltungen ankündigen](#veranstaltungen-ankündigen)
7. [Nach der Veranstaltung](#nach-der-veranstaltung)
8. [Was nicht eingebaut ist](#was-nicht-eingebaut-ist)
9. [Checkliste](#checkliste)
10. [Verwandte Seiten](#verwandte-seiten)

## Die Veranstaltungsklassen

`ezwebin` liefert Installationspakete für die Klassen `event`, `event_calendar` und `multicalendar`
(`extension/ezwebin/packages/`) mit Templates in seinem Design; die drei Klassen existieren auf dem
Demonstrationsserver ([ezwebin](../../../../eng-us/features/6.0/extensions/ezwebin.md)).

**Event** (`event`), Felder wie auf dem Demonstrationsserver definiert:

| Feld | Kennung | Typ | Pflicht |
|---|---|---|---|
| Vollständiger Titel | `title` | Textzeile | nein |
| Kurztitel | `short_title` | Textzeile | ja |
| Text | `text` | Rich Text | nein |
| Kategorie | `category` | Schlüsselwörter | nein |
| Beginn | `from_time` | Datum und Uhrzeit | ja |
| Ende | `to_time` | Datum und Uhrzeit | nein |
| Metadaten | `metadata` | Metadaten (`xrowmetadata`) | nein |

**Event calendar** (`event_calendar`) ist ein Ordner für Veranstaltungen mit einem Titel, einem Kurztitel und einer
Auswahl **View**.

Datums- und Zeitfelder verwenden seit ezwebin 6.0.14 und 6.0.15 die Datumsauswahl von Exponential UI
(`exp::datepicker`), und die Texte des Veranstaltungskalenders sind übersetzt (ezwebin 6.0.9 und 6.0.10)
([ezwebin, What changed](../../../../eng-us/features/6.0/extensions/ezwebin.md#what-changed-in-the-exponential-6-releases)).

## Kalender- und Programmansicht

Die vollständige Ansicht eines Veranstaltungskalenders (`full/event_calendar.tpl`) bindet je nach dem Feld **View** des
Kalenders entweder `event_view_calendar.tpl` oder `event_view_program.tpl` ein. Redakteure wählen pro Kalender, ob
Mitglieder einen Monatskalender oder eine Programmliste sehen. Die vollständige Ansicht einer Veranstaltung zeigt Titel,
Kategorie, Zeitraum und Text (`full/event.tpl`). Die vollständige Ansicht des Kalenders setzt eine Cache-Zeit von 400
Sekunden (`cache_ttl`); eine neue Veranstaltung kann also einige Minuten brauchen, bis sie dort erscheint
([Set block und append block](../../../../eng-us/help/designers/templates/set-block-and-append-block.md)).

`settings/menu.ini.append.php` von ezwebin führt `event_calendar` unter den Klassen auf, die im oberen und linken Menü
erscheinen; ein im Baum platzierter Kalender erscheint also in der Navigation
([Menüs und Navigation](../../../../eng-us/help/creators/website-creators/menus-and-navigation.md)).

## Mehrere Kalender in einem

**Multicalendar** (`multicalendar`) hat einen Namen, eine Beschreibung und ein Feld **Calendars**, das mehrere
Veranstaltungskalender verknüpft; eine Seite kann so zum Beispiel die Kalender mehrerer Regionalgruppen zusammenführen.

## Öffentliche Veranstaltungen und Veranstaltungen nur für Mitglieder

Ein Veranstaltungskalender ist ein Inhalt wie jeder andere; seine Sektion entscheidet also, wer ihn sieht:

| Kalender | Sektion | Wer ihn sieht |
|---|---|---|
| Öffentliche Veranstaltungen | Standard | alle |
| Veranstaltungen für Mitglieder | Mitgliederbereich | nur Mitglieder |

Bereiche nur für Mitglieder beschreibt [Inhalte nur für Mitglieder](inhalte-nur-fuer-mitglieder.md). Ein öffentlicher Teaser
(„Mitgliederabend am ..., melden Sie sich für Einzelheiten an“) kann eine Veranstaltung nur für Mitglieder ankündigen.

## Für eine Veranstaltung anmelden

Es gibt kein Modul zur Veranstaltungsanmeldung. Zwei dokumentierte Bausteine erledigen die Aufgabe für die meisten
Organisationen:

- **Ein Formular mit dem Information Collector.** Felder einer Klasse lassen sich als Informationssammler markieren,
  sodass Besucher auf der Seite ein Formular ausfüllen; Einsendungen werden gespeichert und können an einen Empfänger
  gemailt werden
  ([Formulare mit dem Information Collector](../../../../eng-us/help/creators/website-creators/forms-with-the-information-collector.md),
  [Formulareinsendungen](../../../../eng-us/help/creators/website-creators/form-submissions.md)). Ein Anmeldeformular pro Veranstaltung
  oder eine Gruppe von Anmeldefeldern, die Ihr Partner der Klasse Event hinzufügt, sammelt Name, E-Mail und Optionen.
- **Eine Newsletter-Liste pro Veranstaltungsreihe.** Mitglieder, die Einladungen möchten, abonnieren sie
  ([Kommunikation mit Mitgliedern](kommunikation-mit-mitgliedern.md)).

Schützen Sie öffentliche Anmeldeformulare mit einem CAPTCHA gegen Spam
([Spamschutz für Formulare](../../../../eng-us/help/creators/website-creators/spam-protection-for-forms.md)), und sagen Sie im Formular,
welche Daten aufbewahrt werden und warum ([Einwilligung und Formulare](../sicherheit/einwilligung-und-formulare.md)).

## Veranstaltungen ankündigen

- Ein Block „kommende Veranstaltungen“ auf der Startseite, mit dem Layout-Editor oder `ezflow`
  ([Landingpages mit Layouts](../../../../eng-us/help/creators/website-creators/landing-pages-with-layouts.md)).
- Eine Einladung im Newsletter.
- Benachrichtigungen für Mitglieder, die dem Veranstaltungskalender folgen
  ([Benachrichtigungen: die Anleitung für Benutzer](../../../../eng-us/features/6.0/notifications.md)).
- Ein RSS-Export des Kalenders für öffentliche Veranstaltungen ([RSS-Feeds](../../../anleitungen/rss-feeds.md)).
- Metadaten für das Teilen in sozialen Medien
  ([Metadaten und Teilen in sozialen Medien](../../../../eng-us/help/creators/website-creators/metadata-and-social-sharing.md)).

## Nach der Veranstaltung

Fügen Sie der Veranstaltungsseite Folien, Aufzeichnungen und Fotos hinzu, oder einem damit verknüpften Ordner nur für
Mitglieder. Große Videos liegen besser bei einem Videodienst und werden eingebettet
([Dateien und Downloads, Large files](../../../../eng-us/help/creators/website-creators/files-and-downloads.md#large-files)). Vergangene
Veranstaltungen bleiben als Archiv im Kalender.

## Was nicht eingebaut ist

Im Repository durchsucht am 19. Oktober 2026:

| Nicht eingebaut | Stattdessen planen |
|---|---|
| Veranstaltungsanmeldung mit Platzgrenzen, Wartelisten, Tickets | eine Erweiterung oder einen externen Ticketdienst, von der Veranstaltung aus verlinkt |
| Bezahlte Tickets | ein Produkt pro Veranstaltung im eingebauten Shop ([Shops](../shops/README.md)) oder einen externen Dienst |
| Kalenderdateien (iCal) für die Kalender-Apps der Mitglieder | eine Erweiterung; im Code wurde kein solcher Export gefunden |
| Wiederkehrende Veranstaltungen | ein Event-Objekt pro Termin |
| Automatische Erinnerungen vor einer Veranstaltung | eine Newsletter-Ausgabe oder eine Mail der Geschäftsstelle |

Bitten Sie einen Partner, zu schätzen, was Sie brauchen ([Einen Partner auswählen](../auswahl/einen-partner-auswaehlen.md)).

## Checkliste

- [ ] Veranstaltungsklassen installiert (sie kommen mit den Paketen von ezwebin).
- [ ] Ein Veranstaltungskalender pro Zielgruppe, jeder in der richtigen Sektion.
- [ ] Kalender- oder Programmansicht pro Kalender gewählt.
- [ ] Anmeldeformular entschieden und getestet, mit CAPTCHA und einem Datenschutzhinweis.
- [ ] Ein Block für kommende Veranstaltungen auf der Startseite.
- [ ] Ablauf für Einladungen mit dem Newsletter.
- [ ] Fehlende Funktionen (Tickets, iCal) geplant oder bewusst ausgelassen.

## Verwandte Seiten

- [Veranstaltungen und Neuigkeiten für Gruppen](../vereine/veranstaltungen-und-neuigkeiten-fuer-gruppen.md)
- [Kommunikation mit Mitgliedern](kommunikation-mit-mitgliedern.md)
- [Inhaltsklassen für Websites](../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md)
- [Ehrenamtliche und Anmeldeformulare](../gemeinnuetzige/ehrenamtliche-und-anmeldeformulare.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
