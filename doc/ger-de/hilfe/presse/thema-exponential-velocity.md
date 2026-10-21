---
title: "Thema: Exponential Velocity"
audience: press
category: press-media
order: 56
summary: Ein Themenbeitrag über Exponential Velocity, den mit Exponential ausgelieferten Web- und Anwendungsserver in PHP - was er ist, woher er kommt, wie jung er ist, was er für Betreiber von Websites ändert - mit druckbaren Fakten, Quellen und den Aussagen, die Sie vermeiden sollten.
keywords: Thema, Velocity, Exponential Velocity, vc, Webserver, Anwendungsserver, dauerhafte Worker, Qbix, HTTPS, HTTP/2, MIT, Presse, story, web server, application server, persistent workers, press
type: help
---
# Thema: Exponential Velocity

Diese Seite ist für Journalisten und Creator, die darüber schreiben, wie Exponential ausgeliefert wird, und über
Velocity, den in PHP geschriebenen Webserver, der mitgeliefert wird. Für die veröffentlichten Geschwindigkeitswerte und
ihre Bedingungen lesen Sie [Thema: wie die Velocity-Zahlen gemessen werden](thema-wie-die-velocity-zahlen-gemessen-werden.md),
bevor Sie eine Zahl zitieren.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Was neu ist, und seit wann](#was-neu-ist-und-seit-wann)
3. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
4. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
5. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)
6. [Wen Sie fragen können](#wen-sie-fragen-können)
7. [Verwandte Seiten](#verwandte-seiten)

## Das Thema in einem Absatz

Eine klassische PHP-Website startet die Anwendung für jede Anfrage neu und verwirft sie danach. „Velocity is a web
server written in PHP, bundled with Exponential, that keeps the application loaded between requests“
([README, Exponential Velocity](https://github.com/se7enxweb/exponential/blob/main/README.md#how-exponential-is-served)). Er „began as a fork of the Qbix web
server engine, and the two projects have since drifted far apart“ (das README des Pakets, zitiert auf
[Wer ist 7x?, Exponential Velocity](../wer-ist-7x.md#exponential-velocity)). Er liefert HTTPS vom ersten Start an,
spricht HTTP/2, hat einen Antwort-Cache und ein Control Panel im Browser und ist die Engine, die Exponential „for every
stage, from development to production“ empfiehlt ([Velocity](../../../eng-us/features/6.0/velocity-persistent-worker-server.md#why-you-would-use-it)).
Er ist außerdem sehr jung, und auch das gehört zur Geschichte.

## Was neu ist, und seit wann

| Was | Wann | Quelle |
|---|---|---|
| Erste Commits des Servers | 20. Juli 2026 | [Chronik von Velocity](../../../eng-us/history/velocity/README.md) |
| Erstes Release, `v0.0.1` | September 2026 (Zeitraum 7. bis 21. September) | ebenda |
| Velocity kommt zu Exponential und wird innerhalb von zwei Tagen die empfohlene Engine | 22. September 2026 | [Wer ist 7x?, Zeitleiste](../wer-ist-7x.md#fast-dreißig-jahre-die-zeitleiste) |
| Worker bleiben zwischen den Anfragen erhalten (`ForkPerRequest=disabled`) | 30. September 2026 | [Velocity, Einstellungen, die Sie zuerst kennen sollten](../../../eng-us/features/6.0/velocity-persistent-worker-server.md#settings-to-know-first) |
| Streams, Deployments ohne unnötiges Neuladen, jeder Worker startet sauber | 11. Oktober 2026 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#11-october-2026-exponential-velocity-streams-deploys-without-a-needless-reload-and-starts-each-worker-clean) |
| Ein abgebrochener Neustart oder ein abgebrochenes Deployment lässt Velocity nicht mehr ausgefallen zurück | 17. Oktober 2026 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#17-october-2026-a-restart-or-deploy-that-is-cut-off-no-longer-leaves-velocity-down) |

Velocity in Exponential gehört zur Entwicklungslinie 6.0.15, die nicht veröffentlicht ist.

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| Velocity ist ein in PHP geschriebener Webserver, der die Anwendung zwischen den Anfragen geladen hält. | [README](https://github.com/se7enxweb/exponential/blob/main/README.md#how-exponential-is-served) |
| Er steht unter der MIT-Lizenz (Exponential selbst unter der GPL). | [Wer ist 7x?, Open-Source-Lizenzen](../wer-ist-7x.md#open-source-lizenzen) |
| Er läuft ab seinem Release 0.0.4.54 auf PHP 8.0 oder später (bis dahin 8.1). | [README, What version of PHP is required](https://github.com/se7enxweb/exponential/blob/main/README.md#what-version-of-php-is-required) |
| HTTPS vom ersten Start an mit einem selbst signierten Zertifikat; eigene Zertifikate oder Let's Encrypt sind möglich. | [Velocity](../../../eng-us/features/6.0/velocity-persistent-worker-server.md#why-you-would-use-it), [README](https://github.com/se7enxweb/exponential/blob/main/README.md#how-exponential-is-served) |
| HTTP/2 ist in den mitgelieferten Server eingebaut. | [Velocity](../../../eng-us/features/6.0/velocity-persistent-worker-server.md#why-you-would-use-it) |
| Ein Befehl bringt eine PHP-Änderung in Betrieb: `./console exp:velocity deploy`. | [Velocity, Eine PHP-Änderung in Betrieb nehmen](../../../eng-us/features/6.0/velocity-persistent-worker-server.md#deploy-a-php-change-in-one-command) |
| Das Behalten der Worker zwischen den Anfragen hat angemeldete Seiten der Administration von 256 bis 284 ms auf 73 bis 80 ms verkürzt, „in the measurement that led to the change“. | [Velocity, Einstellungen, die Sie zuerst kennen sollten](../../../eng-us/features/6.0/velocity-persistent-worker-server.md#settings-to-know-first) |
| Ein Dashboard (`/Q/dashboard`) und ein passwortgeschütztes Control Panel (`/Q/panel`) sind eingebaut. | [Velocity Control Panel](../../../eng-us/features/6.0/velocity-control-panel.md) |
| Das README nennt ihn als erforderlich in `~0.0.4.54`. | [Exponential Velocity](../wer-ist-die-exponential-foundation.md#exponential-velocity) |

## Was Sie nicht behaupten sollten

- „In großem Maßstab im Produktivbetrieb bewährt.“ Die Quellen beschreiben einen jungen Server: erste Commits im Juli
  2026.
- „N-mal schneller als Apache.“ Kein veröffentlichter Vergleich stützt einen Faktor; das README selbst sagt „Most of
  the speed came from fixing Exponential and the cache, not from the server process model“.
- „Velocity ersetzt jeden Webserver.“ Apache, Nginx, LiteSpeed, Caddy, IIS und andere liefern Exponential weiterhin
  aus.
- „Er läuft auf Shared Hosting.“ Das geht „usually“ nicht ([README](https://github.com/se7enxweb/exponential/blob/main/README.md#how-exponential-is-served)).
- „Ein Velocity-Server trennt Kunden voneinander.“ Das README sagt, ein einzelner Prozess, der mehrere Websites
  ausliefert, „is a routing convenience, not a security boundary“.

## Blickwinkel und Ideen für Überschriften

- Ein Content-Management-System, das seinen eigenen Server mitbringt.
- Jung und offen: ein Server, der seine eigenen Grenzen dokumentiert.
- Die Verbesserung, die allen geholfen hat: Arbeit am Cache, die auch Apache und Nginx schneller macht.

```text
"Exponential ships its own web server, written in PHP"
"From first commit to recommended engine in two months: the Velocity story"
"Why Exponential's speed work helps Apache users too"
```

## Wen Sie fragen können

| Frage | Fragen Sie | Wo |
|---|---|---|
| Geschichte, Aufbau, Roadmap | 7x | info@se7enx.com oder <https://exponential.earth/about/contact-us> |
| Der eigene Code und die Issues des Servers | sein Repository | <https://github.com/se7enxweb/exponential-velocity> |
| Ein Sicherheitsproblem in Velocity | vertraulich | security@se7enx.com ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)) |

## Verwandte Seiten

- [Gesprächspunkte: Geschwindigkeit und Velocity](../fuersprecher/gespraechspunkte-geschwindigkeit-und-velocity.md)
- [Thema: Engines, Velocity, FrankenPHP und die klassischen Server](thema-engines-velocity-frankenphp-und-klassische-server.md)
- [Thema: das Velocity Control Panel](thema-das-velocity-control-panel.md)
- [Themenbeiträge: Übersicht](../../../eng-us/help/press/feature-stories-index.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
