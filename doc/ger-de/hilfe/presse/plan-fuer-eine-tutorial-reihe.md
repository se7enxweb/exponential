---
title: Plan für eine Tutorial-Reihe
audience: press
category: press-media
order: 168
summary: Ein fertiger Plan für eine Tutorial-Reihe über Exponential in drei Strängen - Redakteure, Website-Bauer und Administratoren - mit zwölf Folgen, jede verknüpft mit einer Hilfeseite und einem Demo-Skript, der anzugebenden Version, den Voraussetzungen und wie eine Reihe korrekt bleibt, wenn Releases die Bildschirme verändern.
keywords: Tutorial-Reihe, Kurs, Folgen, Playlist, Lehrplan, Redakteure, Website-Bauer, Administratoren, Entwickler, Lernpfad, Creator, Videoreihe, tutorial series, course, episodes, playlist, curriculum, editors, site builders, administrators, developers, learning path, creator, video series
type: help
---
# Plan für eine Tutorial-Reihe

Diese Seite ist für Creator, die statt eines einzelnen Videos eine Reihe machen wollen: eine Playlist, einen Kurs,
einen wöchentlichen Stream. Sie liefert einen Plan mit zwölf Folgen in drei Strängen, jede mit einer Hilfeseite
verknüpft, damit Zuschauer weiterlesen und Sie jeden Schritt prüfen können. Passen Sie die Reihenfolge Ihrem Publikum
an; halten Sie sich an die Regel „eine Aufgabe pro Folge“
([Ein Tutorial schreiben, das funktioniert](../fuersprecher/ein-tutorial-schreiben.md)).

## Inhalt

1. [Die Version einmal festlegen](#die-version-einmal-festlegen)
2. [Strang A: Redakteure](#strang-a-redakteure)
3. [Strang B: Website-Bauer](#strang-b-website-bauer)
4. [Strang C: Administratoren](#strang-c-administratoren)
5. [Eine Vorlage für eine Folge](#eine-vorlage-für-eine-folge)
6. [Die Reihe korrekt halten](#die-reihe-korrekt-halten)

## Die Version einmal festlegen

Eine Reihe muss durchgehend eine Version zeigen oder deutlich sagen, wann sie wechselt. Die meisten Bildschirme in der
Hilfe gehören zur Entwicklungslinie 6.0.15, die kein Veröffentlichungsdatum hat; das neueste Release ist 6.0.14
(7. Juni 2026). Nennen Sie die Version im Titel der Reihe oder in der ersten Folge und in jeder Beschreibung. Wenn
6.0.15 getaggt ist, prüfen Sie jede Folge anhand des endgültigen Changelogs ([Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)).

## Strang A: Redakteure

| # | Folge | Länge | Hilfeseite | Skript |
|---|---|---|---|---|
| A1 | Anmelden und sich zurechtfinden | 6 Min. | [Erste Schritte](../erste-schritte.md), [Im Editor anmelden](../anmelden-im-editor.md) | [Rundgang für Redakteure, Teil 1](../../../eng-us/help/press/demo-script-editor-tour.md#part-1-finding-your-way-2-minutes) |
| A2 | Ein Element bearbeiten: Entwürfe und die Feldleiste | 8 Min. | [Inhalte anlegen und bearbeiten](../inhalte-bearbeiten.md) | [Rundgang für Redakteure, Teil 2](../../../eng-us/help/press/demo-script-editor-tour.md#part-2-editing-a-folder-4-minutes) |
| A3 | Vorschau, Veröffentlichen, Versionen und Zurückgehen | 6 Min. | [Veröffentlichen, Versionen und der Papierkorb](../veroeffentlichen-und-versionen.md) | [Rundgang für Redakteure, Teil 3](../../../eng-us/help/press/demo-script-editor-tour.md#part-3-versions-1-minute-30) |
| A4 | Bilder und Dateien | 6 Min. | [Medien und Uploads](../medien-und-uploads.md) | Ihr eigenes, nach der Hilfeseite |
| A5 | Inhalte übersetzen; die Oberfläche in anderen Sprachen | 6 Min. | [Sprachen](../sprachen.md) | [Rundgang für Redakteure, Teil 4](../../../eng-us/help/press/demo-script-editor-tour.md#part-4-languages-and-right-to-left-2-minutes-30) |

## Strang B: Website-Bauer

| # | Folge | Länge | Hilfeseite | Skript |
|---|---|---|---|---|
| B1 | Von null auf SQLite installieren | 10 Min. | [Installation mit einem Befehl](../../../eng-us/features/6.0/install-in-one-command.md) | [Installation von null](../../../eng-us/help/press/demo-script-install-from-zero.md) |
| B2 | Layouts, Zonen und Blöcke | 8 Min. | [Der Layout-Editor](../layout-editor.md) | [Exponential Layouts](../../../eng-us/help/press/demo-script-layouts.md) |
| B3 | Inhaltstypen: Klassen und Attribute | 8 Min. | [Das Inhaltsmodell und das Bearbeiten von Inhalten](../../anleitungen/inhaltsmodell-und-bearbeitung.md) | [Rundgang für Administratoren, Teil 1](../../../eng-us/help/press/demo-script-administrator-tour.md#part-1-the-content-model-2-minutes) |
| B4 | Arbeiten mit Git: der eigene Branch, Updates und ein erster Pull Request | 10 Min. | [Mit Git entwickeln](../mit-git-entwickeln.md) | Ihr eigenes, nach der Hilfeseite |

## Strang C: Administratoren

| # | Folge | Länge | Hilfeseite | Skript |
|---|---|---|---|---|
| C1 | Die Website unter Velocity betreiben | 8 Min. | [Velocity](../../../eng-us/features/6.0/velocity-persistent-worker-server.md) | [Velocity](../../../eng-us/help/press/demo-script-velocity.md) |
| C2 | Rollen, zweistufige Anmeldung und der Audit-Trail | 8 Min. | [Sicherheitshärtung](../sicherheitshaertung.md) | [Rundgang für Administratoren, Teil 4](../../../eng-us/help/press/demo-script-administrator-tour.md#part-4-security-3-minutes) |
| C3 | Logs, Upgrade-Prüfung und Caches | 8 Min. | [Eine Website betreiben](../../anleitungen/betrieb-einer-website.md) | [Rundgang für Administratoren, Teil 3](../../../eng-us/help/press/demo-script-administrator-tour.md#part-3-operations-3-minutes) |

Ein optionaler vierter Strang kann Exponential Intelligence zeigen ([sein Demo-Skript](../../../eng-us/help/press/demo-script-exponential-intelligence.md)),
sobald Sie einen Anbieterschlüssel haben, den Sie widerrufen können.

## Eine Vorlage für eine Folge

```text
EPISODE <#> - <title>
Series:      <name>, episode <#> of <n>          Version: Exponential <version>
Promise:     After this episode you can <task>.
Before:      Episode <#> (or: a demo installation, see <link>)
Disclosure:  <line>

0:00  Recap of the last episode (15 seconds), promise, disclosure
0:30  The task, step by step (names exactly as on screen)
...   The result
...   What can go wrong, and the fix
...   Next episode; the help page; where to ask (forums, Telegram)

DESCRIPTION: help page link, commands shown, chapters, license of the video
```

## Die Reihe korrekt halten

| Wann | Was Sie tun |
|---|---|
| Ein Release wird getaggt | Lesen Sie seinen Changelog im Hinblick auf die Bildschirme jeder Folge; notieren Sie, was sich geändert hat |
| Ein Schritt funktioniert nicht mehr | Heften Sie eine Korrektur an die Folge; korrigieren Sie die Beschreibung; nehmen Sie neu auf, wenn der Schritt zentral ist |
| Eine Folge ist veraltet | Sagen Sie es in ihrem Titel; verlinken Sie den Ersatz |
| Ein Zuschauer meldet einen Fehler | Prüfen Sie ihn anhand der Hilfe; bedanken Sie sich; korrigieren Sie öffentlich ([Ein Video oder einen Beitrag korrigieren](ein-video-oder-einen-beitrag-korrigieren.md)) |

Führen Sie eine kleine Tabelle Ihrer Folgen mit der Version und dem Datum, an dem Sie jede zuletzt geprüft haben.

## Verwandte Seiten

- [Ein Tutorial schreiben, das funktioniert](../fuersprecher/ein-tutorial-schreiben.md)
- [Leitfaden für Video-Rundgänge](leitfaden-fuer-video-rundgaenge.md)
- [Gliederungen für Podcast-Folgen](gliederungen-fuer-podcast-folgen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
