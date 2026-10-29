---
title: Folienplan für einen 20-Minuten-Vortrag
audience: advocate
category: advocacy
order: 42
summary: Ein Plan Folie für Folie für einen 20-minütigen Vortrag über Exponential - 16 Folien mit dem, was auf jede gehört, was Sie sagen, der Quelle und wo eine kurze Live-Demo hineinpasst - dazu Hinweise zum Anpassen und ein Fragenkatalog.
keywords: Vortrag, Präsentation, 20 Minuten, Folien, Gliederung, Konferenz, Treffen, Benutzergruppe, Sprechernotizen, Live-Demo, Fragen, talk, presentation, 20 minutes, slides, outline, conference, meetup, user group, speaker notes, live demo, questions
type: help
---
# Folienplan für einen 20-Minuten-Vortrag

Diese Seite ist für Fürsprecher, die einen vollständigen Vortrag vorbereiten: einen Platz bei einem Treffen, eine
Benutzergruppe, eine Konferenzsitzung oder eine Vorlesung. Sie bietet 16 Folien mit Sprechernotizen und Quellen, Platz
für eine fünfminütige Live-Demo und einen Katalog von Fragen, die Sie wahrscheinlich hören werden.

## Inhalt

1. [Aufbau](#aufbau)
2. [Die Folien](#die-folien)
3. [Der Abschnitt mit der Live-Demo](#der-abschnitt-mit-der-live-demo)
4. [Anpassen](#anpassen)
5. [Fragenkatalog](#fragenkatalog)
6. [Bevor Sie ihn halten](#bevor-sie-ihn-halten)

## Aufbau

| Teil | Minuten | Folien |
|---|---|---|
| Eröffnung und wer Sie sind | 1 | 1 bis 2 |
| Was Exponential ist | 4 | 3 bis 6 |
| Wer dahintersteht | 2 | 7 bis 8 |
| Live-Demo | 5 | 9 |
| Was neu ist | 3 | 10 bis 12 |
| Open Source und Community | 3 | 13 bis 15 |
| Abschluss | 2 | 16 |

## Die Folien

| # | Folientitel | Auf der Folie | Sagen Sie | Quelle |
|---|---|---|---|---|
| 1 | Exponential | Titel, Ihr Name | Legen Sie offen, wer Sie sind und ob Sie jemand bezahlt | [Offenlegung](offenlegung-und-interessenkonflikte.md) |
| 2 | Warum es mir wichtig ist | Ein Foto oder ein Satz zu Ihrer Nutzung | Ihre eigene Geschichte, keine Kundennamen ohne Zustimmung | [Die Privatsphäre von Menschen schützen](privatsphaere-schuetzen.md) |
| 3 | Was es ist | „PHP-Framework + CMS + Shop“ | Die Definition aus der README | [Exponential 6](../wer-ist-die-exponential-foundation.md#exponential-6-der-kernel) |
| 4 | Ihre Inhalte, Ihr Modell | Eine Inhaltsklasse mit Feldern | Inhaltstypen werden in der Administration definiert | [Inhaltsmodell](../../anleitungen/inhaltsmodell-und-bearbeitung.md) |
| 5 | Viele Websites, viele Sprachen | Siteaccesses, Sprachkennzeichen | 368 Locale-Dateien; Übersetzungen der Administration (die aktuellen Gesamtzahlen stehen am Anfang der Seite [Sprachen in Exponential](../../sprachen.md)) | [Sprachen und Barrierefreiheit](../wer-ist-die-exponential-foundation.md#sprachen-und-barrierefreiheit) |
| 6 | Läuft, wo Sie möchten | PHP 8.0-8.5, sechs Datenbanken, Stufen der Betriebssysteme | Nennen Sie SQLite als Standard | [Gesprächspunkte: Hosting](gespraechspunkte-hosting-datenbanken-freiheit.md) |
| 7 | Eine lange Geschichte | Zeitleiste 1999 bis 2026 | Die Geschichte, mit dem alten Namen in der Vergangenheitsform | [Gesprächspunkte: Langlebigkeit](gespraechspunkte-langlebigkeit-und-geschichte.md) |
| 8 | Wer dahintersteht | 7x und die Exponential Foundation nebeneinander | Zwei Organisationen, ein Projekt | [7x und die Foundation](7x-und-die-foundation.md) |
| 9 | Demo | „Live-Demo“ | Siehe unten | [Demos zeigen](demos-zeigen.md) |
| 10 | Velocity | „Ein PHP-Webserver“ | Empfohlene Engine; jung (2026) | [Gesprächspunkte: Geschwindigkeit](gespraechspunkte-geschwindigkeit-und-velocity.md) |
| 11 | Exponential Intelligence | „Nur Entwürfe“ | Veröffentlicht nie; Region oder lokales Modell wählbar | [Gesprächspunkte: Exponential Intelligence](gespraechspunkte-exponential-intelligence.md) |
| 12 | Sicherheit, die man sieht | Prozessdiagramm | Vertraulich melden, erste Antwort innerhalb eines Werktags, nächstes Release | [Gesprächspunkte: Sicherheit](gespraechspunkte-sicherheit.md) |
| 13 | Freie Software | GPL + GFDL | Code und Dokumentation sind frei | [Gesprächspunkte: Open Source](gespraechspunkte-open-source-und-lizenzierung.md) |
| 14 | Jeder Pull Request wird übernommen | Ein Screenshot eines übernommenen PR (öffentlich) | Die Regel des Projekts | [Wie das Projekt wächst](../wer-ist-die-exponential-foundation.md#wie-das-projekt-wächst) |
| 15 | Machen Sie mit | Foren, Telegram, eZpedia, GitHub, Projects | Nur dokumentierte Kanäle | [Die Community-Kanäle](community-kanaele.md) |
| 16 | Probieren Sie es aus | `./console exp:install` + Links | Ein Befehl, SQLite | [Installation mit einem Befehl](../../funktionen/6.0/installation-mit-einem-befehl.md) |

## Der Abschnitt mit der Live-Demo

Fünf Minuten, auf Ihrer eigenen Installation, entlang eines Wegs aus [Demos als Fürsprecher zeigen](demos-zeigen.md#drei-fertige-demo-wege).
Halten Sie eine **Rückfallebene** bereit: drei Screenshots desselben Wegs auf den Folien 9a bis 9c, falls Netzwerk oder
Laptop ausfallen. Regeln: [Sicherheit bei Live-Demos](sicherheit-bei-live-demos.md).

## Anpassen

| Publikum | Tauschen Sie | Gegen |
|---|---|---|
| PHP-Entwickler | Folien 5 und 11 | Erweiterungen und die RAD-Werkzeuge; Git zuerst ([Gesprächspunkte für Entwickler](gespraechspunkte-fuer-entwickler.md)) |
| Redakteure und Inhaltsteams | Folien 6 und 10 | Der Layouts-Editor; Versionen und Vorschau ([Gesprächspunkte für Redakteure](gespraechspunkte-fuer-redakteure.md)) |
| Studierende | Folie 12 | „Ihr erster Pull Request“ ([Einen ersten Beitrag begleiten](einen-ersten-beitrag-begleiten.md)) |
| Community für Barrierefreiheit | Folien 10 und 11 | Funktionen zur Barrierefreiheit ([Gesprächspunkte: Barrierefreiheit](gespraechspunkte-barrierefreiheit.md)) |

## Fragenkatalog

| Frage | Kurze Antwort und Seite |
|---|---|
| „Wird es noch gepflegt?“ | Fünfzehn Releases seit Januar 2024 ([Lebt es noch?](einwand-lebt-es-noch.md)) |
| „Wie viele Menschen nutzen es?“ | „Die Quellen zählen keine Benutzer.“ ([Aussagen, die Sie nicht machen dürfen](aussagen-die-sie-nicht-machen-duerfen.md)) |
| „Warum nicht CMS X?“ | Fragen Sie nach dem Bedarf ([Warum nicht ein anderes CMS?](einwand-warum-nicht-ein-anderes-cms.md)) |
| „Kann es Headless?“ | REST-API v1 und v2; GraphQL in der Symfony-Linie ([PHP und moderne Technik](einwand-php-und-moderne-technik.md)) |
| „Wen bezahle ich für Support?“ | Jeden Anbieter; 7x ist einer; Sponsoren finanzieren das Projekt ([7x und die Foundation](7x-und-die-foundation.md)) |
| „Ist die KI sicher?“ | Nur Entwürfe; Steuerungsmöglichkeiten aufgeführt ([Was ist mit den KI-Funktionen?](einwand-ki-funktionen.md)) |
| „Wie trage ich bei?“ | [Mit Git entwickeln](../mit-git-entwickeln.md) |

## Bevor Sie ihn halten

- [ ] Jede Zahl in derselben Woche gegen ihre Quelle geprüft.
- [ ] Die Folien folgen [Barrierefreie Folien und Grafiken](barrierefreie-folien-und-grafiken.md).
- [ ] Demo-Installation vorbereitet und geprobt; Screenshots als Rückfallebene.
- [ ] Folien danach unter einer Lizenz Ihrer Wahl veröffentlicht, mit Quellen
      ([Quellen zitieren und Ihre Texte lizenzieren](quellen-zitieren-und-texte-lizenzieren.md)).

## Verwandte Seiten

- [Skript für einen Lightning Talk](skript-lightning-talk.md)
- [Ablaufplan für einen Praxis-Workshop](ablaufplan-praxis-workshop.md)
- [Demo-Skripte, Die 45-Minuten-Demo](../geschaeft/demo-drehbuecher.md#die-45-minuten-demo)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
