---
title: "Thema: Engines, Velocity, FrankenPHP und die klassischen Server"
audience: press
category: press-media
order: 58
summary: Ein Themenbeitrag über die Wahl des Webservers für Exponential - das mitgelieferte Velocity, FrankenPHP als produktionsreife Alternative, der eingebaute Server von PHP für die Entwicklung und Apache, Nginx und die anderen klassischen Server - mit druckbaren Fakten, Quellen und den Aussagen, die Sie vermeiden sollten.
keywords: Thema, Engines, Velocity, Qbix, FrankenPHP, Caddy, eingebauter PHP-Server, Apache, Nginx, LiteSpeed, IIS, PHP-FPM, Webserver, Presse, story, engines, PHP built-in server, web server, press
type: help
---
# Thema: Engines, Velocity, FrankenPHP und die klassischen Server

Diese Seite ist für Journalisten und Rezensenten, die die Möglichkeiten vergleichen, Exponential zu betreiben. Sie
erklärt die drei Engines, die die Befehle `exp:velocity` steuern, die klassischen Webserver, die weiterhin
funktionieren, und wann welcher laut den Quellen die richtige Wahl ist.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Die Engines im Vergleich](#die-engines-im-vergleich)
3. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
4. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
5. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)
6. [Wen Sie fragen](#wen-sie-fragen)
7. [Verwandte Seiten](#verwandte-seiten)

## Das Thema in einem Absatz

Exponential verlangt keinen bestimmten Webserver: „All of these work. Exponential does not require any particular one“
([README, The traditional options](https://github.com/se7enxweb/exponential/blob/main/README.md#the-traditional-options)),
alle diese Server funktionieren also. Seit der Entwicklungslinie 6.0.15 steuern dieselben Konsolenbefehle auch drei
eigene Engines: den mitgelieferten, auf Qbix beruhenden Server von Velocity, „the recommended engine for every stage,
from development to production, and the fastest“ (die empfohlene und schnellste Engine für jede Stufe, von der
Entwicklung bis zur Produktion); FrankenPHP, „Caddy with PHP built in ... The production-ready alternative“ (Caddy mit
eingebautem PHP, die produktionsreife Alternative); und den eingebauten Server von PHP, „For development, and the
shipped default because it always works“ (für die Entwicklung und als mitgelieferte Voreinstellung, weil er immer
funktioniert) ([README, Engines](https://github.com/se7enxweb/exponential/blob/main/README.md#engines-frankenphp-phps-built-in-server-or-qbix)).
Das Thema handelt von Wahlfreiheit: Das Projekt empfiehlt eine Engine und dokumentiert, wann ein klassischer Server
noch richtig ist.

## Die Engines im Vergleich

| Engine | Rolle in den Quellen | Standardport | Quelle |
|---|---|---|---|
| Qbix (der eigene Server von Velocity) | Für jede Stufe empfohlen; liefert HTTP und HTTPS selbst aus | 8088 (HTTPS 8080) | [Velocity, Die Engines im Vergleich](../../../eng-us/features/6.0/velocity-persistent-worker-server.md#engines-side-by-side) |
| FrankenPHP | Produktionsreife Alternative; eine einzige Binärdatei mit geprüfter Prüfsumme | 8089 (HTTPS 8444) | ebenda |
| Eingebauter Server von PHP | Nur für die Entwicklung; die mitgelieferte Voreinstellung | 8087 | ebenda |
| Apache, Nginx, LiteSpeed, Caddy, IIS, lighttpd, H2O, Traefik | Klassische Server mit Rewrite-Regeln; alle funktionieren | ihre eigenen | [README, The traditional options](https://github.com/se7enxweb/exponential/blob/main/README.md#the-traditional-options) |

Wann ein klassischer Server laut README noch die richtige Wahl ist: bei einer Isolierung jeder Website auf Ebene des
Betriebssystems, bei Hosting, das keinen langlebigen Prozess erlaubt (Shared Hosting), oder wenn schon ein optimiertes
Nginx oder Apache davorsteht ([README](https://github.com/se7enxweb/exponential/blob/main/README.md#when-a-traditional-server-is-still-the-right-answer)).

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| Die mitgelieferte Voreinstellung in `settings/velocity.ini` ist `Engine=php`, weil sie keine Installation braucht; die Dokumentation sagt, sie für jede echte Website auf `qbix` zu ändern. | [Velocity, Wozu Sie es verwenden](../../../eng-us/features/6.0/velocity-persistent-worker-server.md#why-you-would-use-it) |
| Alle drei Engines können für Tests gleichzeitig laufen, jede mit eigenem Port, eigener PID-Datei und eigenen Protokollen. | [Velocity, Die Engines im Vergleich](../../../eng-us/features/6.0/velocity-persistent-worker-server.md#engines-side-by-side) |
| Der eingebaute Server von PHP ist „Single-threaded, no concurrency, not hardened. Never expose it to the internet.“ – einfädig, ohne Nebenläufigkeit, nicht gehärtet und nie dem Internet auszusetzen. | [README, The traditional options](https://github.com/se7enxweb/exponential/blob/main/README.md#the-traditional-options) |
| PHP 8.0 betreibt Exponential unter Apache, PHP-FPM oder Velocity (Velocity ab seinem Release 0.0.4.54). | [README, PHP](https://github.com/se7enxweb/exponential/blob/main/README.md#what-version-of-php-is-required) |
| Shared Hosting: Apache oder LiteSpeed mit `.htaccess`; Velocity kann dort meist nicht laufen. | [Unterstützte Betriebssysteme](../../../eng-us/help/os-support.md#containers-cloud-and-hosting-panels) |
| Worker halten die Anwendung im Speicher, deshalb braucht ein geändertes Template, eine geänderte Einstellung oder Klasse einen Neustart. | [README, One thing to know](https://github.com/se7enxweb/exponential/blob/main/README.md#one-thing-to-know-before-you-rely-on-it) |

## Was Sie nicht behaupten sollten

- „Sie müssen Velocity verwenden.“ Jeder in der README aufgeführte klassische Server funktioniert.
- „FrankenPHP ist Teil von Exponential.“ Es ist ein eigenes Projekt, das Exponential herunterladen und steuern kann.
- „Velocity ist immer die beste Wahl.“ Die README nennt Fälle, in denen ein klassischer Server die richtige Wahl ist.
- Geschwindigkeitsvergleiche zwischen Engines ohne eigene Messung und Methode
  ([Thema: wie die Velocity-Zahlen gemessen werden](thema-wie-die-velocity-zahlen-gemessen-werden.md)).

## Blickwinkel und Ideen für Überschriften

```text
"One CMS, three engines, any web server: how Exponential lets you choose"
"When the old web server is still the right answer - according to the project itself"
"Exponential drives FrankenPHP, Velocity and PHP's own server with the same commands"
```

Auf Deutsch zum Beispiel: „Ein CMS, drei Engines, jeder Webserver: wie Exponential Ihnen die Wahl lässt“, „Wann der
alte Webserver noch die richtige Wahl ist – nach Angaben des Projekts selbst“, „Exponential steuert FrankenPHP,
Velocity und den eigenen Server von PHP mit denselben Befehlen“.

## Wen Sie fragen

| Frage | Fragen Sie | Wo |
|---|---|---|
| Warum Velocity empfohlen wird | 7x | info@se7enx.com oder <https://exponential.earth/about/contact-us> |
| FrankenPHP selbst | das Projekt FrankenPHP | seine eigenen Kanäle |
| Erfahrungen auf echten Servern | die Community | <https://share.exponential.earth/forums> |

## Verwandte Seiten

- [Thema: Exponential Velocity](thema-exponential-velocity.md)
- [FrankenPHP und der eingebaute Server von PHP](../../../eng-us/bc/6.0/frankenphp.md)
- [Thema: Freiheit beim Hosting](thema-freiheit-beim-hosting.md)
- [Funktionsthemen: Übersicht](themenbeitraege-uebersicht.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
