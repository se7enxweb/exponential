---
title: Ihre erste Woche als Fürsprecher
audience: advocate
category: advocacy
order: 4
summary: Ein Plan Tag für Tag für die erste Woche der Fürsprache, mit einer kleinen, nützlichen Aufgabe pro Tag, der Seite, die sie erklärt, und dem, was Sie am Ende der Woche haben.
keywords: erste Woche, Einstieg, Fürsprecher, Plan, Tag für Tag, erste Schritte, Forum, Telegram, Hilfeseite, Demo, Gesprächspunkte, first week, onboarding, advocate, plan, day by day, getting started, forum, help page, demo, talking points
type: help
---
# Ihre erste Woche als Fürsprecher

Diese Seite ist für Menschen, die sich gerade entschieden haben, Exponential-Fürsprecher zu werden. Sie nennt eine
Aufgabe pro Tag für sieben Tage, je etwa 30 bis 60 Minuten. Am Ende der Woche kennen Sie die Fakten, die Namen und die
Kanäle und haben bereits mindestens zwei Menschen geholfen.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Tag 1: Wer und was](#tag-1-wer-und-was)
3. [Tag 2: das Produkt ausprobieren](#tag-2-das-produkt-ausprobieren)
4. [Tag 3: die Gesprächspunkte lernen](#tag-3-die-gesprächspunkte-lernen)
5. [Tag 4: die Kanäle besuchen](#tag-4-die-kanäle-besuchen)
6. [Tag 5: eine Frage beantworten](#tag-5-eine-frage-beantworten)
7. [Tag 6: eine Seite verbessern](#tag-6-eine-seite-verbessern)
8. [Tag 7: die nächsten 90 Tage planen](#tag-7-die-nächsten-90-tage-planen)
9. [Ihre Woche auf einer Karte](#ihre-woche-auf-einer-karte)

## Bevor Sie beginnen

- Ein kostenloses GitHub-Konto (für Issues, Pull Requests und **Auf GitHub bearbeiten**).
- Ein Konto auf [Share](https://share.exponential.earth/) für die Foren.
- Optional: Telegram, für den [Community-Chat](https://t.me/exponentialcms).
- Ein Notizbuch oder eine Textdatei für Ihr [Beitragsprotokoll](ihre-beitraege-festhalten.md).

## Tag 1: Wer und was

Lesen Sie in dieser Reihenfolge:

1. [Wer ist die Exponential Foundation?, Kurz gesagt](../wer-ist-die-exponential-foundation.md#kurz-gesagt)
2. [Wer ist 7x?, Kurz gesagt](../wer-ist-7x.md#kurz-gesagt)
3. [Die Produktfamilie kennen](die-produktfamilie-kennen.md)
4. [Die Geschichte von Exponential in zwei Minuten](die-geschichte-in-zwei-minuten.md)

**Erledigt, wenn** Sie ohne Notizen erklären können, was Exponential ist, wer 7x ist und was die Foundation tut.

## Tag 2: das Produkt ausprobieren

Installieren Sie es auf Ihrem eigenen Rechner. Ein Befehl installiert eine vollständige Website auf SQLite, ohne
Datenbankserver ([Exponential mit einem Befehl installieren](../../../en/us/features/6.0/install-in-one-command.md)):

```bash
./console exp:install --dry-run    # checks, installs nothing
./console exp:install              # installs the site on SQLite
```

Folgen Sie dann [Erste Schritte](../erste-schritte.md) und dem [Rundgang durch die Administration](../rundgang-durch-die-administration.md).
Öffnen Sie die Hilfe mit dem „?“ in der Kopfzeile.

> **Warnung**
> Der Installer gibt ein erzeugtes Administratorpasswort aus und schreibt es in `var/log/initial-admin-password`.
> Ändern Sie es nach der ersten Anmeldung und löschen Sie diese Datei
> ([Das Standardpasswort des Administrators](../standard-administratorpasswort.md)). Veröffentlichen Sie es nirgends.

**Erledigt, wenn** Sie einen Inhalt bearbeitet und veröffentlicht und ein Layout im Layouts-Editor geöffnet haben.

## Tag 3: die Gesprächspunkte lernen

1. Lesen Sie [Gesprächspunkte: das Grundset](gespraechspunkte-grundset.md) und sagen Sie jeden Punkt einmal laut.
2. Lesen Sie [Aussagen, die Sie nicht machen dürfen](aussagen-die-sie-nicht-machen-duerfen.md).
3. Lesen Sie die [Kurzkarte zu den Namensregeln](kurzkarte-namensregeln.md).
4. Wählen Sie die Seite für die Zielgruppe, die Ihnen am nächsten ist: [Redakteure](gespraechspunkte-fuer-redakteure.md),
   [Entwickler](gespraechspunkte-fuer-entwickler.md), [Administratoren](gespraechspunkte-fuer-administratoren.md) oder
   [Entscheider](gespraechspunkte-fuer-entscheider.md).

**Erledigt, wenn** Sie die Zwei-Minuten-Geschichte und drei Gesprächspunkte erzählen können, jeden mit seiner Quelle.

## Tag 4: die Kanäle besuchen

| Kanal | Tun Sie Folgendes |
|---|---|
| [Foren](https://share.exponential.earth/forums) | Lesen Sie zehn aktuelle Diskussionen; notieren Sie die Fragen, die Sie beantworten könnten |
| [Telegram](https://t.me/exponentialcms) | Treten Sie bei, lesen Sie, grüßen Sie kurz |
| [Issues von Exponential](https://github.com/se7enxweb/exponential/issues) | Lesen Sie fünf offene Issues; sehen Sie, wie gute Meldungen aussehen |
| [Issues der Community](https://github.com/se7enxweb/exponential-community/issues) | Sehen Sie, was das Projekt über den Code hinaus bespricht |
| [eZpedia](https://ezpedia.exponential.earth/) und [Projects](https://projects.exponential.earth/) | Stöbern Sie; notieren Sie einen Artikel oder ein Projekt für später |

Details: [Die Community-Kanäle](community-kanaele.md).

## Tag 5: eine Frage beantworten

Wählen Sie eine offene Frage von Tag 4, die Sie beantworten können. Folgen Sie [Fragen gut beantworten](fragen-gut-beantworten.md):
Formulieren Sie die Frage neu, antworten Sie kurz, verlinken Sie die Hilfeseite, sagen Sie, mit welcher Version Sie es
geprüft haben.

**Erledigt, wenn** eine Person eine Antwort von Ihnen hat, mit Quelle.

## Tag 6: eine Seite verbessern

Finden Sie eine Sache in der Hilfe, die Sie diese Woche verwirrt hat: einen Tippfehler, einen fehlenden Schritt, einen
veralteten Satz. Korrigieren Sie sie mit **Auf GitHub bearbeiten** und eröffnen Sie einen Pull Request
([Die Hilfe verbessern](die-hilfe-verbessern.md)). Jeder Pull Request wird übernommen
([Wie das Projekt wächst](../wer-ist-die-exponential-foundation.md#wie-das-projekt-wächst)).

## Tag 7: die nächsten 90 Tage planen

1. Entscheiden Sie, welche Arten von Fürsprache zu Ihrer Zeit passen ([Was ein Fürsprecher ist](was-ein-fuersprecher-ist.md#arten-der-fürsprache)).
2. Kopieren Sie die Vorlage aus [Ein 90-Tage-Plan für Fürsprecher](ein-90-tage-plan.md) und füllen Sie sie aus.
3. Beginnen Sie Ihr [Beitragsprotokoll](ihre-beitraege-festhalten.md) mit dem, was Sie diese Woche getan haben.

## Ihre Woche auf einer Karte

```text
MY FIRST WEEK AS AN EXPONENTIAL ADVOCATE                    Start date: <date>

Day 1  Who and what          [ ] Foundation  [ ] 7x  [ ] product family  [ ] two-minute story
Day 2  Try it                [ ] installed   [ ] edited and published  [ ] opened a layout
Day 3  Talking points        [ ] core set    [ ] claims not to make     [ ] naming card
Day 4  Channels              [ ] forums  [ ] Telegram  [ ] issues  [ ] eZpedia  [ ] Projects
Day 5  One answer            Link: <forum or chat link>
Day 6  One page improved     Pull request: <link>
Day 7  90-day plan           [ ] written   [ ] contribution log started
```

## Verwandte Seiten

- [Fürsprecher: hier beginnen](fuersprecher-hier-beginnen.md)
- [Ein 90-Tage-Plan für Fürsprecher](ein-90-tage-plan.md)
- [Checklisten für Fürsprecher](checklisten-fuer-fuersprecher.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
