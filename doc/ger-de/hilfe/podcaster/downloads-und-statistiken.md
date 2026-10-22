---
title: Downloads und Statistiken
audience: podcaster
category: podcasting-streaming
order: 42
summary: Was eine Exponential-Website einer Sendung über ihr Publikum sagen kann - der Download-Zähler von Dateifeldern, Seitenaufrufe, Webserver-Logs, Analyse auf Episodenseiten -, was sie nicht zählen kann, etwa Wiedergaben in Podcast-Apps, und wie Sie die Zahlen ehrlich lesen.
keywords: Statistiken, Downloads, Download-Zähler, Analyse, Hörer, Wiedergaben, Aufrufzähler, updateviewcount, Server-Logs, Webanalyse, Podcast-Statistik, Publikum, Messung, statistics, downloads, download count, analytics, listeners, plays, view count, server logs, web analytics, podcast statistics, audience, measurement
type: help
---
# Downloads und Statistiken

Diese Seite ist für Produzenten und Ansprechpartner von Sponsoren, die wissen möchten, wie viele Menschen zuhören. Eine
Exponential-Website zählt manches gut und anderes gar nicht. Das Publikum von Podcasts ist in jedem System schwer zu
messen; diese Seite sagt, was jede Zahl wirklich bedeutet.

## Inhalt

1. [Was die Website zählt](#was-die-website-zählt)
2. [Der Download-Zähler](#der-download-zähler)
3. [Seitenaufrufe und Analyse](#seitenaufrufe-und-analyse)
4. [Server-Logs](#server-logs)
5. [Was hier nicht gezählt werden kann](#was-hier-nicht-gezählt-werden-kann)
6. [Zahlen berichten](#zahlen-berichten)

## Was die Website zählt

| Zahl | Quelle | Zählt |
|---|---|---|
| Downloads eines **Dateifelds** | `download_count` von `ezbinaryfile` | Anfragen nach der Datei über `content/download` |
| Downloads eines **Medienfelds** | keine | der Mediendatentyp hat keinen Zähler |
| Seitenaufrufe | der Cronjob-Teil `updateviewcount`, aus dem Zugriffslog | Aufrufe von Episodenseiten |
| Besucher, Quellen, Geräte | ein Analysedienst auf den Seiten, wenn Sie einen hinzufügen | Browserbesuche von Seiten |
| Jede Anfrage | das Zugriffslog des Webservers | alles, auch Apps, die Dateien und den Feed abrufen |

Die Möglichkeiten für Seitenaufrufe und Analyse stehen unter [Besucherstatistiken und Analyse](../ersteller/website-ersteller/besucherstatistik-und-analyse.md).

## Der Download-Zähler

Jeder Download eines Dateifelds über `content/download` erhöht seinen `download_count` um eins
(`kernel/classes/datatypes/ezbinaryfile/ezbinaryfiletype.php`). Der Zähler gilt pro Feld und Version. Die Listen der
Unterelemente in der Administration können ihn als Spalte zeigen
(`kernel/classes/subitems/columns/expsubitemsmediacolumn.php`).

Was er bedeutet:

- Jede Anfrage wird gezählt, auch eine `Range`-Anfrage, die nur einen Teil abruft; ein Anhören kann also mehrfach
  zählen, ein anderes (aus einem Cache oder CDN bedient) gar nicht;
- Anfragen von Bots und Crawlern der Verzeichnisse zählen ebenfalls;
- eine Episode, deren Audio in einem **Medienfeld** liegt, wird gar nicht gezählt.

Verwenden Sie ihn als Trend ("mehr als im letzten Monat"), nicht als Zahl der Hörer.

## Seitenaufrufe und Analyse

Seitenaufrufe zählen Menschen, die die Episodenseite im Browser geöffnet haben. Ein Analyseskript auf der Seite sieht
nur Browserbesuche: Es sieht nie eine App, die die Datei aus dem Feed herunterlädt, und es braucht in vielen
Rechtsordnungen die Einwilligung des Besuchers ([Datenschutz und Player von Drittanbietern](datenschutz-und-player-von-drittanbietern.md)).
Auf Installationen mit `bcwebsitestatistics` fügt diese Erweiterung ein Skript von Google Analytics hinzu; jeder andere
Dienst lässt sich über das Design ergänzen.

## Server-Logs

Das Zugriffslog des Webservers hält jede Anfrage an `/content/download/` und `/rss/feed/` fest. Ein Hoster oder
Administrator kann pro Episodendatei zählen:

- Anfragen und eindeutige Client-Adressen pro Tag;
- die User-Agents, die oft die Podcast-App nennen;
- Anfragen nach dem Feed, die zeigen, wie viele Apps ihn prüfen.

Fragen Sie Ihren Hoster nach diesen Zahlen; Logs auszuwerten ist Serverarbeit, außerhalb der Administration. Behandeln
Sie Adressen in Logs als personenbezogene Daten ([Aufbewahrung von Logs und Daten](../hosting/sicherheit/aufbewahrung-von-logs-und-daten.md)).

## Was hier nicht gezählt werden kann

- **Wiedergaben in Podcast-Apps**: Apps laden Dateien herunter und spielen sie offline ab; die Website sieht einen
  Download, kein Anhören.
- **Hörer in Verzeichnissen**: Die Verzeichnisse haben eigene Statistiken für ihre Apps; lesen Sie sie in deren
  Werkzeugen.
- **Downloads, die ein CDN beantwortet** und die die Website nie erreichen ([CDN und externes Medienhosting](cdn-und-externes-medienhosting.md)).

## Zahlen berichten

- Sagen Sie, was gezählt wurde: "Dateianfragen im Server-Log, 1. bis 30. September", nicht "Hörer".
- Nennen Sie Quelle und Zeitraum, und behalten Sie die Methode von Monat zu Monat bei.
- Addieren Sie keine Zahlen aus verschiedenen Quellen (Downloads der Website plus Wiedergaben in Verzeichnissen), als
  wären sie dasselbe.
- Fragt ein Sponsor nach Reichweitenzahlen, geben Sie die Zahlen der Verzeichnisse und den Download-Trend der Website
  getrennt an, jeweils mit ihrer Definition.

## Verwandte Seiten

- [Besucherstatistiken und Analyse](../ersteller/website-ersteller/besucherstatistik-und-analyse.md)
- [Analyse und was Sie messen sollten](../kunden/alltag/analyse-und-was-sie-messen-sollten.md)
- [Anfrage-Logs](../hosting/betrieb/anfrage-logs.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
