---
title: Über Velocity-Releases berichten
audience: press
category: press-media
order: 108
summary: Wie Sie über Releases von Exponential Velocity, dem PHP-Webserver, berichten - was er ist, wie seine Versionen zählen, wo seine Release Notes stehen, wie Sie seine Leistungszahlen mit ihren Bedingungen zitieren und was Sie nicht behaupten sollten.
keywords: Velocity, Exponential Velocity, Webserver, Release, 0.0.4.50, Versionsnummern, Leistung, Benchmark, Qbix, MIT, Release Notes, GitHub Releases, web server, release, version numbers, performance, release notes
type: help
---
# Über Velocity-Releases berichten

Diese Seite ist für Fachpresse, Podcaster und Video-Creator, die über Exponential Velocity berichten. Velocity wird weit
häufiger veröffentlicht als Exponential selbst und hat ein Versionsschema, das sich leicht falsch lesen lässt. Seine
Leistungszahlen sind mit ihren Bedingungen dokumentiert, und diese Bedingungen gehören in Ihren Beitrag.

## Inhalt

1. [Was Velocity ist](#was-velocity-ist)
2. [Releases und Versionsnummern](#releases-und-versionsnummern)
3. [Wo die Fakten zu Releases stehen](#wo-die-fakten-zu-releases-stehen)
4. [Die Leistungszahlen zitieren](#die-leistungszahlen-zitieren)
5. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)

## Was Velocity ist

| Fakt | Quelle |
|---|---|
| "Velocity is a web server written in PHP, bundled with Exponential, that keeps the application loaded between requests." | [README.md](https://github.com/se7enxweb/exponential/blob/main/README.md), „Exponential Velocity — the fast option“ |
| "It began as a fork of the Qbix web server engine, and the two projects have since drifted far apart" | Die README des Pakets, zitiert in [Wer ist 7x?, Exponential Velocity](../wer-ist-7x.md#exponential-velocity) |
| Der PHP-Anwendungsserver, "that Exponential recommends for every stage from development to production" | [Chronik von Velocity](../../geschichte/velocity/README.md) |
| MIT-lizenziert | Seine `composer.json`, laut [Wer ist 7x?](../wer-ist-7x.md#open-source-lizenzen) |
| Ein eigenes Composer-Paket, das die `composer.json` des Kernels in `~0.0.4.54` verlangt; ab diesem Release läuft es auf PHP 8.0 bis 8.5 (bis dahin brauchte es 8.1 und wurde nur empfohlen) | [Den Code beschaffen](../../installation/03-den-code-beschaffen.md#34-das-velocity-paket) |
| Erste Commits am 20. Juli 2026, erstes Release `v0.0.1` im September 2026, Teil von Exponential seit dem 22. September 2026 | [Wer ist 7x?](../wer-ist-7x.md#exponential-velocity) |

## Releases und Versionsnummern

Velocity-Versionen haben vier Stellen, und die letzte zählt immer weiter: "`0.0.4.9` is followed by `0.0.4.10`, never
`0.0.5.0`" ([Release Notes von Velocity](../../../changelogs/extensions/exponential-velocity.md)). Am 18. Oktober 2026 war
das neueste Release in den Release Notes `v0.0.4.50` vom 12. Oktober 2026; GitHub zeigte am 19. Oktober 2026 `v0.0.4.53`
als neuestes Tag. `v0.0.4.54` vom 20. Oktober 2026 ist das erste Release, das auf PHP 8.0 läuft, und die `composer.json`
des Kernels verlangt `~0.0.4.54`, was jedes spätere 0.0.4.x akzeptiert
([Den Code beschaffen](../../installation/03-den-code-beschaffen.md#34-das-velocity-paket)). Prüfen Sie die Tags am Tag, an dem
Sie schreiben ([Ein Release selbst prüfen](ein-release-selbst-pruefen.md)).

Viele Releases können auf einen Tag fallen: Die Chronik führt allein für den 22. September 2026 `v0.0.2.1` bis
`v0.0.4.14` auf ([Chronik von Velocity](../../geschichte/velocity/README.md#monatsseiten)). Berichten Sie über das Release,
über das Sie schreiben, mit seinen eigenen Notes; stellen Sie nicht jedes als große Neuigkeit dar.

Der alte Name lebt in Bezeichnern weiter: "In the code you will still see `Q_WebServer`, `qbixserver.php`, `qbixctl` ...
these are identifiers and stay as they are. The product is called Velocity" ([Chronik von Velocity](../../geschichte/velocity/README.md#ein-hinweis-zu-den-namen)).

## Wo die Fakten zu Releases stehen

| Was | Wo |
|---|---|
| Releases bis v0.0.4.42, vollständig | [Release Notes von Velocity](../../../changelogs/extensions/exponential-velocity.md) |
| Releases ab v0.0.4.43 | Eine Übersichtstabelle auf derselben Seite, vollständige Notes unter [GitHub Releases](https://github.com/se7enxweb/exponential-velocity/releases) |
| Die Geschichte, Monat für Monat | [Chronik von Velocity](../../geschichte/velocity/README.md) |
| Wie Sie aktualisieren | [Upgrade-Hinweise zur Velocity-Engine](../../bc/6.0/velocity-engine-upgrade-hinweise.md) |
| Pakete (deb, rpm, Docker, Binärdateien) | [Velocity-Pakete und -Binärdateien](../../funktionen/6.0/velocity-pakete-und-binaerdateien.md) |
| Das Composer-Paket | [se7enxweb/exponential-velocity auf Packagist](https://packagist.org/packages/se7enxweb/exponential-velocity) |

Drei Tags, `v0.0.4.21`, `v0.0.4.22` und `v0.0.4.26`, haben keine Release-Seite, weil "their builds did not produce a
usable artifact" (Release Notes). Berichten Sie über sie nicht als Releases mit Funktionen.

## Die Leistungszahlen zitieren

Die README nennt die Zahlen, die "currently documented for this stack" sind: "0.36 ms per cached page, 8,941 requests
per second sustained, and repeat visits never touch the network". Sie erklärt sie auch, und die Erklärungen gehören zu
einem ehrlichen Zitat:

| Zahl | In der README genannte Bedingung |
|---|---|
| 0,36 ms | "the server's own work to answer a cached page", ein Median "over hundreds of requests" |
| 8.941 Anfragen pro Sekunde | "measured on a 12-core machine serving a real page, with zero errors at every concurrency tried, up to 384 connections in flight" |
| Wiederholte Besuche berühren nie das Netzwerk | "refers to the optional navigation cache (a service worker)" |

Die README ergänzt, dass die Zahlen "reproducible with scripts that ship with the project" sind und dass "Most of the
speed came from fixing Exponential and the cache". Zitieren Sie die Zahlen mit ihren Bedingungen und dem Link oder gar
nicht ([Zahlen prüfen](zahlen-pruefen.md)).

## Was Sie nicht behaupten sollten

| Schreiben Sie nicht | Warum |
|---|---|
| „N-mal schneller als Apache / nginx / FrankenPHP“ | Es gibt keinen veröffentlichten Vergleich ([Aussagen, die Sie nicht machen dürfen](../fuersprecher/aussagen-die-sie-nicht-machen-duerfen.md#zahlen)) |
| „Ihre Website antwortet in 0,36 ms“ | Die Zahl gilt für eine Seite aus dem Cache auf dem gemessenen System |
| „Velocity ist experimentell“ | Das Projekt nennt ihn die empfohlene Engine; FrankenPHP ist "the production-ready alternative" ([Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#exponential-velocity)) |
| „Velocity ersetzt das Caching von Exponential“ | Es fügt einen Antwort-Cache hinzu; die README schreibt den größten Teil der Geschwindigkeit Korrekturen an Exponential und seinem Cache zu |
| „Velocity ist Qbix“ | Es begann als Fork; die Projekte "have since drifted far apart" |

## Verwandte Seiten

- [Über ein Release berichten](ueber-ein-release-berichten.md)
- [Wie Versionsnummern funktionieren](wie-versionsnummern-funktionieren.md)
- [Faire Vergleiche](faire-vergleiche.md)
- [Gesprächspunkte: Geschwindigkeit und Velocity](../fuersprecher/gespraechspunkte-geschwindigkeit-und-velocity.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
