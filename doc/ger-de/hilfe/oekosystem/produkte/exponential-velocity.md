---
title: Exponential Velocity im Ökosystem
audience: ecosystem, admin, hosting, developer
category: ecosystem
order: 12
summary: Der Platz von Exponential Velocity im Ökosystem - ein in PHP geschriebener Web- und Anwendungsserver, seit 6.0.15 Teil von Exponential, seine drei Engines, warum er schneller ist, wann ein klassischer Server noch die richtige Antwort ist und wo er entwickelt wird.
keywords: Velocity, Anwendungsserver, Webserver, Qbix, FrankenPHP, eingebauter PHP-Server, persistente Worker, Antwort-Cache, HTTPS, exponential-velocity, Engines, application server, web server, PHP built-in server, persistent workers, response cache, engines
type: help
---
# Exponential Velocity im Ökosystem

**"Velocity is a web server written in PHP, bundled with Exponential, that keeps the application loaded between
requests"**, ein in PHP geschriebener Webserver, der mit Exponential ausgeliefert wird und die Anwendung zwischen den
Anfragen geladen hält (`README.md`, "Exponential Velocity — the fast option").

## Inhalt

1. [Sein Platz im Ökosystem](#sein-platz-im-ökosystem)
2. [Die drei Engines](#die-drei-engines)
3. [Warum er schneller ist](#warum-er-schneller-ist)
4. [Wann ein klassischer Server noch richtig ist](#wann-ein-klassischer-server-noch-richtig-ist)
5. [Wo er entwickelt wird](#wo-er-entwickelt-wird)

## Sein Platz im Ökosystem

- "As of 6.0.15 Velocity is part of Exponential itself. `composer.json` requires `se7enxweb/exponential-velocity
  ~0.0.4.54`, so `composer install` installs the server with the rest of the product", seit 6.0.15 ist Velocity also
  Teil von Exponential selbst, und `composer install` installiert den Server mit dem übrigen Produkt (`README.md`).
- Er "is based on the Qbix server and is developed alongside Exponential, so the two are tuned against each other",
  basiert also auf dem Qbix-Server und wird zusammen mit Exponential entwickelt, sodass beide aufeinander abgestimmt
  sind (ebenda).
- Er steht unter der MIT-Lizenz ([Exponential Velocity](../../wer-ist-die-exponential-foundation.md#exponential-velocity)).
- Die ganze Geschichte: [Velocity ist Teil von Exponential 6.0.15](../../velocity-ist-teil-von-exponential-6-0-15.md).

## Die drei Engines

| Engine | Rolle, wie die README sie beschreibt |
|---|---|
| Qbix | Der eigene Server von Velocity: "The recommended engine for every stage, from development to production, and the fastest", die empfohlene und schnellste Engine für jede Phase; liefert HTTP und HTTPS selbst aus |
| FrankenPHP | Caddy mit eingebautem PHP, ein einzelnes Binary mit geprüfter Prüfsumme: "The production-ready alternative", die produktionsreife Alternative |
| Der eingebaute Server von PHP | Nichts zu installieren; für die Entwicklung, "and the shipped default because it always works", und der mitgelieferte Standard, weil er immer funktioniert |

Die Engine wird mit `[ServerSettings] Engine` in `velocity.ini` gewählt. Einzelheiten:
[Velocity-Engines](../../../bc/6.0/velocity-engines.md).

## Warum er schneller ist

Velocity "loads the framework once into a parent process and forks workers that share it", lädt das Framework also
einmal in einen Elternprozess und forkt Worker, die es teilen, und ergänzt "a response cache that answers a repeat
request without waking the application at all", einen Antwort-Cache, der eine wiederholte Anfrage beantwortet, ohne die
Anwendung überhaupt zu wecken (`README.md`). Die README sagt auch: "Most of the speed came from fixing Exponential and
the cache, not from the server process model", der größte Teil der Geschwindigkeit kam also aus Korrekturen an
Exponential und am Cache, nicht aus dem Prozessmodell, daher "is fast under load on Apache or Nginx too", ist 6.0.15
auch auf Apache oder Nginx unter Last schnell.

## Wann ein klassischer Server noch richtig ist

Aus `README.md`, "When a traditional server is still the right answer":

- Sie brauchen Isolation auf Betriebssystemebene pro Website (betreiben Sie stattdessen eine Velocity-Instanz pro
  Website);
- Ihr Hosting erlaubt keinen langlebigen Prozess, wie bei Shared Hosting üblich;
- Sie haben schon ein abgestimmtes Nginx oder Apache davor (Velocity läuft gern hinter einem Reverse Proxy).

## Wo er entwickelt wird

- Repository und Issues: [se7enxweb/exponential-velocity](https://github.com/se7enxweb/exponential-velocity).
- Geschichte: [Chronik von Velocity](../../../geschichte/velocity/README.md).
- Funktionsseiten: [Server mit persistenten Workern](../../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md),
  [Antwort-Cache](../../../funktionen/6.0/velocity-antwort-cache.md),
  [HTTPS-Zertifikate](../../../funktionen/6.0/velocity-https-zertifikate.md).

## Verwandte Seiten

- [Produkte](README.md)
- [Webserver und Engines](../php-stack/webserver-und-engines.md)
- [Velocity für Unternehmen](../../unternehmen/zuverlaessigkeit/velocity-fuer-unternehmen.md)
- [Argumente: Geschwindigkeit und Velocity](../../fuersprecher/gespraechspunkte-geschwindigkeit-und-velocity.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
