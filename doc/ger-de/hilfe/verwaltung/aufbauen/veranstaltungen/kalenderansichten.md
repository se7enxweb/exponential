---
title: Kalenderansichten
audience: government, website-creator, designer
category: public-sector
order: 40
summary: Wie die Kalender- und Programmansicht von ezwebin für die Klasse Event calendar funktionieren - die Ansicht wählen, Adressen für die Monatsnavigation, Blättern, Caching - und was bei einem Design ohne Templates für Veranstaltungen zu tun ist, einschließlich der Barrierefreiheit eines Monatsrasters.
keywords: Kalenderansicht, Programmansicht, Veranstaltungskalender, View-Parameter, Monat, Jahr, Template-Override, Barrierefreiheit, Raster, calendar view, program view, event calendar, ezwebin, event_view_calendar, event_view_program, view parameters, month, year, offset, cache_ttl, template override, accessibility, grid
type: help
---
# Kalenderansichten

Diese Seite richtet sich an Site-Ersteller und Designer, die Veranstaltungen als Kalender oder als Liste zeigen. Sie
beschreibt die beiden Ansichten des Designs ezwebin und wie man dasselbe in einem anderen Design bereitstellt. Die
Fakten zu den Templates stammen aus `extension/ezwebin/design/ezwebin/templates/full/` und
[Was mitgeliefert wird](was-fuer-veranstaltungen-mitgeliefert-wird.md).

## Inhalt

1. [Die Ansicht wählen](#die-ansicht-wählen)
2. [Die Kalenderansicht](#die-kalenderansicht)
3. [Die Programmansicht](#die-programmansicht)
4. [Caching](#caching)
5. [In einem Design ohne Templates für Veranstaltungen](#in-einem-design-ohne-templates-für-veranstaltungen)
6. [Barrierefreiheit von Kalendern](#barrierefreiheit-von-kalendern)

## Die Ansicht wählen

Bearbeiten Sie das Event-calendar-Objekt; die Auswahl **View** entscheidet, welches Template
`full/event_calendar.tpl` einbindet: den Kalender oder das Programm. Jedes Kalenderobjekt hat eine Ansicht; zwei
Kalenderobjekte (oder ein Multicalendar) können dieselben Veranstaltungen unterschiedlich zeigen.

## Die Kalenderansicht

`event_view_calendar.tpl` zeigt einen Monat als Raster:

| Merkmal | Detail |
|---|---|
| Angezeigter Monat | der aktuelle Monat, oder der in der Adresse: `<calendar address>/(month)/<1-12>/(year)/<yyyy>` |
| Navigation | Links zum vorherigen und nächsten Monat (betitelt „Previous month“ / „Next month“) |
| Veranstaltungen | unter dem Kalender mit Attributfiltern auf From Time und To Time des Monats abgerufen |
| Ein Tag | `(day)/<n>` in der Adresse wählt einen Tag |

## Die Programmansicht

`event_view_program.tpl` listet Veranstaltungen in zeitlicher Reihenfolge, 15 je Seite, mit Links zu neueren und
älteren Veranstaltungen; die Seite steht als Offset in der Adresse. Für viele Besucher ist diese Liste leichter zu
benutzen als ein Raster, auf Telefonen und mit Screenreadern.

## Caching

Der Veranstaltungskalender von ezwebin setzt in seiner vollständigen Ansicht ein `cache_ttl`, damit der „aktuelle
Monat“ ohne Veröffentlichen weiterrückt ([Caching für Designer](../../../../../eng-us/help/designers/theme-design/caching-for-designers.md)).
Das Veröffentlichen einer Veranstaltung leert den View-Cache der Veranstaltung und ihres übergeordneten Kalenders.
Listen von Veranstaltungen an anderer Stelle (Blöcke auf der Startseite) brauchen ihren eigenen Ablauf
([Kommende Veranstaltungen auflisten](kommende-veranstaltungen-auflisten.md)).

## In einem Design ohne Templates für Veranstaltungen

Das Standarddesign neuer Installationen (`simple`) hat keine Templates für Veranstaltungen. Möglichkeiten:

| Möglichkeit | Aufwand |
|---|---|
| Fügen Sie Ihrem Design Overrides `full/event_calendar.tpl` und `full/event.tpl` hinzu, auf Grundlage der Templates von ezwebin | mittel; kopieren, Markup und Texte anpassen |
| Nur eine Liste im Stil des Programms: ein Ordner mit einem Fetch-Template, sortiert nach From Time | gering ([Kommende Veranstaltungen auflisten](kommende-veranstaltungen-auflisten.md)) |
| Exponential Layouts: ein Block, der die Veranstaltungen eines Kalenders listet | keine Template-Arbeit für Redakteure ([Der Layout-Editor](../../../layout-editor.md)) |

Template-Overrides werden über den Bezeichner der Klasse zugeordnet
([Template-Overrides](../../../../../eng-us/help/creators/website-creators/template-overrides.md)). Die Templates von ezwebin sind
GPL-Code; sie in Ihr Design zu kopieren, ist unter derselben Lizenz erlaubt.

## Barrierefreiheit von Kalendern

| Problem | Praxis |
|---|---|
| Ein Monatsraster ist eine Datentabelle | echtes Tabellen-Markup mit Kopfzellen für die Wochentage (`scope="col"`) und einer Beschriftung mit dem Monat |
| Veranstaltungen in Zellen sind oft winzige Links | eine Mindestgröße der Zielfläche (WCAG-2.2-Kriterium 2.5.8) und lesbarer Text |
| Navigationslinks nur als Pfeile | Linktext oder ein zugänglicher Name: „Vorheriger Monat“ |
| Tage ohne Veranstaltungen | kündigen Sie leere Zellen nicht als Links an |
| Alternative | bieten Sie neben dem Raster immer die Programmliste an |

Testen Sie mit Tastatur und Screenreader
([Barrierefreiheit testen](../../../../../eng-us/help/creators/website-creators/testing-accessibility.md)); das mitgelieferte Template
von ezwebin wurde nicht geprüft ([Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md)).

## Verwandte Seiten

- [Veranstaltungen](README.md)
- [Was mitgeliefert wird](was-fuer-veranstaltungen-mitgeliefert-wird.md)
- [Designs und die Fallback-Kette](../../../../../eng-us/help/creators/website-creators/designs-and-the-fallback-chain.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
