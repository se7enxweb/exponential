---
title: Veranstaltungen und Neuigkeiten für Gruppen
audience: businesses, editor, website-creator
category: business-solutions
order: 30
summary: Wie ein Verein, eine Gesellschaft oder eine kleine Gruppe Neuigkeiten und Veranstaltungen mit Exponential veröffentlicht - die mitgelieferten Klassen für Artikel, Veranstaltungen und Veranstaltungskalender, einen aktuellen Kalender, Feeds für Neuigkeiten und Veranstaltungen, Benachrichtigungen für Mitglieder, die einer Seite folgen, ein Newsletter und die Startseite in Layouts.
keywords: Veranstaltungen, Veranstaltungskalender, Neuigkeiten, Vereinsnachrichten, Treffen, RSS-Feed, Benachrichtigungen, Newsletter, kleine Gruppe, events, event calendar, news, club news, meetings, ezwebin, article, event_calendar, multicalendar, RSS feed, notifications, newsletter, small group
type: help
---
# Veranstaltungen und Neuigkeiten für Gruppen

Diese Seite ist für die Ehrenamtlichen, die die Neuigkeiten und den Kalender einer Gruppe aktuell halten, und für den
Web-Koordinator, der beides einrichtet. Sie zeigt, welche mitgelieferten Klassen Sie verwenden, wie Mitglieder von
neuen Einträgen erfahren, ohne die Website jeden Tag zu prüfen, und wie der Kalender nicht veraltet, wenn die
Ehrenamtlichen wechseln.

## Inhalt

1. [Die Klassen für Neuigkeiten und Veranstaltungen](#die-klassen-für-neuigkeiten-und-veranstaltungen)
2. [Neuigkeiten einrichten](#neuigkeiten-einrichten)
3. [Den Kalender einrichten](#den-kalender-einrichten)
4. [Den Kalender aktuell halten](#den-kalender-aktuell-halten)
5. [Mitglieder informieren](#mitglieder-informieren)
6. [Die Startseite](#die-startseite)
7. [Neuigkeiten und Veranstaltungen nur für Mitglieder](#neuigkeiten-und-veranstaltungen-nur-für-mitglieder)
8. [Checkliste für Veranstaltungen und Neuigkeiten](#checkliste-für-veranstaltungen-und-neuigkeiten)

## Die Klassen für Neuigkeiten und Veranstaltungen

Die Website-Oberfläche `ezwebin`, die `composer.json` voraussetzt, bringt diese Klassenpakete in
`extension/ezwebin/packages/` mit:

| Paket | Verwendung |
|---|---|
| `article`, `article_main_page`, `article_sub_page` | Neuigkeiten und längere Artikel |
| `folder` | Das Nachrichtenarchiv, auf Wunsch nach Jahren gruppiert |
| `event` | Ein Treffen oder eine Veranstaltung |
| `event_calendar` | Ein Kalender, der Veranstaltungen enthält |
| `multicalendar` | Eine Seite, die mehrere Kalender zusammenführt |
| `blog`, `blog_post` | Ein zwangloses Gruppenblog |

In den Rollen des Multisite-Website-Pakets werden `event`-Objekte unterhalb eines `event_calendar` angelegt
([Rollen und Richtlinien von sevenx_multisite](../../seitenpakete/sevenx-multisite/rollen-und-richtlinien-von-sevenx-multisite.md)). Der
ezwebin-Veranstaltungskalender speichert seine Seite für kurze Zeit im Cache („Der ezwebin-Veranstaltungskalender
behält seine Seite 400 Sekunden lang“, [Caching für Designer](../../../../eng-us/help/designers/theme-design/caching-for-designers.md)),
daher kann es einige Minuten dauern, bis eine neue Veranstaltung in der Kalenderansicht erscheint.

## Neuigkeiten einrichten

1. Legen Sie in der öffentlichen Sektion einen Ordner „Neuigkeiten“ an.
2. Redakteure legen darin Artikel an; jeder Artikel hat einen Titel, eine Einleitung, einen Haupttext und ein Bild.
3. Geben Sie dem Ordner eine Vollansicht, die die neuesten Artikel auflistet; der Website-Ersteller richtet das einmal
   ein ([Menüs und Navigation](../../../../eng-us/help/creators/website-creators/menus-and-navigation.md)).
4. Legen Sie fest, wie lange Neuigkeiten auf der Startseite bleiben und wann alte Neuigkeiten in einen Archivordner
   wandern.

## Den Kalender einrichten

1. Legen Sie einen Veranstaltungskalender dort an, wo Mitglieder ihn erwarten („Veranstaltungen“).
2. Redakteure fügen darunter Veranstaltungen hinzu, mit Datum und Uhrzeit, Ort und einer kurzen Beschreibung.
3. Legen Sie für mehrere Aktivitätsgruppen je einen Kalender an und eine Multikalender-Seite, die sie zusammenführt.
4. Verlinken Sie den Kalender im Hauptmenü und auf der Startseite.

Templates und Ansichten sind die Arbeit des Website-Erstellers; siehe
[Inhaltsklassen für Websites](../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md), wenn Sie eine Klasse
ändern möchten, weil Ihre Gruppe ein zusätzliches Feld braucht (zum Beispiel „Anmeldung erforderlich“).

## Den Kalender aktuell halten

Ein Kalender mit den Veranstaltungen des letzten Jahres ist schlechter als gar keiner. Machen Sie ihn zur Aufgabe
einer bestimmten Person:

- [ ] Eine benannte Rolle („Veranstaltungsredakteur“) ist für den Kalender verantwortlich.
- [ ] Veranstaltungen werden eingetragen, sobald sie vereinbart sind, nicht erst in der Woche davor.
- [ ] Abgesagte Veranstaltungen werden in ihrem Text als abgesagt gekennzeichnet und nicht stillschweigend entfernt,
  damit Mitglieder, die kommen wollten, es sehen.
- [ ] Vergangene Veranstaltungen bleiben stehen oder wandern in ein Archiv; Links auf sie funktionieren weiter.
- [ ] Bei jeder Übergabe wird der Kalender geprüft ([Übergabe bei Wechsel der Ehrenamtlichen](uebergabe-bei-wechsel-der-ehrenamtlichen.md)).

## Mitglieder informieren

| Kanal | Wie | Seite |
|---|---|---|
| Benachrichtigungen | Mitglieder mit einem Konto folgen dem Ordner „Neuigkeiten“ oder dem Kalender und erhalten eine E-Mail, „wenn unterhalb eines Elements, dem Sie folgen, etwas veröffentlicht wird“, sofort oder als tägliche, wöchentliche oder monatliche Zusammenfassung | [Benachrichtigungen: das Handbuch für Benutzer](../../../../eng-us/features/6.0/notifications.md) |
| Feeds | Ein RSS- oder Atom-Export des Ordners „Neuigkeiten“ und des Kalenders unter `/rss/feed/<address>` | [RSS-Feeds für Ihre Website](../../../../eng-us/help/creators/website-creators/rss-feeds-for-your-site.md) |
| Newsletter | Ein regelmäßiger Newsletter mit `cjw_newsletter`, auf Wunsch automatisch aus neuen Artikeln befüllt | [Newsletter](../newsletter/README.md) |

Benachrichtigungen brauchen ein Konto mit der Richtlinie `notification/use` (dieselbe Seite zu Benachrichtigungen).
Jede optionale Mail durchläuft die E-Mail-Einstellungen der Website, sodass Mitglieder wählen, was sie bekommen, und
es mit einem Klick abbestellen können ([E-Mail-Einstellungen](../../../../eng-us/features/6.0/mail-preferences.md)).

> **Tipp**
> Für eine kleine Gruppe reichen Benachrichtigungen oft aus: Es muss kein Newsletter geschrieben werden, und Mitglieder,
> die Neuigkeiten wollen, folgen dem Ordner „Neuigkeiten“. Ein Newsletter lohnt sich, wenn Sie auswählen und erklären
> möchten, was wichtig ist.

## Die Startseite

Die Startseite zeigt meist die nächsten Veranstaltungen und die neuesten Neuigkeiten. Mit Exponential Layouts hält
sich ein `list`-Block mit einer dynamischen Sammlung selbst aktuell („die neuesten fünf Artikel in dieser Sektion“,
in der [Layouts-Referenz](../../../../eng-us/bc/6.0/LAYOUTS.md)), und ein Redakteur kann die Seite ohne Entwickler neu anordnen
([Landingpages mit Layouts](../../../../eng-us/help/creators/website-creators/landing-pages-with-layouts.md)). Vereinbaren Sie, dass
immer nur eine Person zur selben Zeit das Layout der Startseite bearbeitet, denn Layout-Entwürfe werden geteilt.

## Neuigkeiten und Veranstaltungen nur für Mitglieder

Manche Neuigkeiten (Protokolle, interne Beschlüsse) sind nur für Mitglieder bestimmt. Legen Sie sie in eine Sektion,
die nur Mitglieder lesen dürfen; Besucher können sie dann nicht öffnen, und die Suche zeigt Besuchern nur, was sie lesen
dürfen ([Website-Suche](../../../../eng-us/help/creators/website-creators/site-search.md#the-built-in-engine)). Verwenden Sie Ordner nur
für Mitglieder nicht als Quellen öffentlicher Feeds oder als Artikelvorrat eines öffentlichen Newsletters
([Sektionen und Objektzustände](../../../../eng-us/help/creators/website-creators/sections-and-object-states.md),
[Inhalte nur für Mitglieder](../mitglieder/inhalte-nur-fuer-mitglieder.md)).

## Checkliste für Veranstaltungen und Neuigkeiten

- [ ] Ordner „Neuigkeiten“ und Veranstaltungskalender aus den mitgelieferten Klassen angelegt.
- [ ] Ein Veranstaltungsredakteur benannt; der Kalender wird bei jeder Übergabe geprüft.
- [ ] Feeds für Neuigkeiten und Veranstaltungen aktiv und verlinkt.
- [ ] Mitglieder wissen, wie sie Neuigkeiten folgen (Benachrichtigungen) oder sich anmelden (Newsletter).
- [ ] Die Startseite zeigt die nächsten Veranstaltungen und die neuesten Neuigkeiten automatisch.
- [ ] Einträge nur für Mitglieder liegen in einer Sektion nur für Mitglieder; abgemeldet geprüft.

## Verwandte Seiten

- [Vereine und kleine Gruppen](README.md)
- [Veranstaltungen und Kalender](../mitglieder/veranstaltungen-und-kalender.md)
- [Kommunikation mit Mitgliedern](../mitglieder/kommunikation-mit-mitgliedern.md)
- [Feeds für Neuerwerbungen](../bibliotheken/feeds-fuer-neuerwerbungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
