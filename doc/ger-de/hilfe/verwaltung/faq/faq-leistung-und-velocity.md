---
title: "FAQ: Leistung und Velocity"
audience: government, project-manager, hosting, admin
category: public-sector
order: 190
summary: Antworten auf die Leistungsfragen öffentlicher Stellen zu Exponential - wie schnell es ist, Caching, Lastspitzen, Exponential Velocity, andere Engines, CDNs und wie Sie die Geschwindigkeit selbst messen -, ohne ungeprüfte Zahlen.
keywords: FAQ Leistung, Geschwindigkeit, Velocity, Caching, Lastspitze, CDN, statischer Cache, Benchmark, Last, Skalierbarkeit, Engines, öffentlicher Sektor, performance FAQ, speed, caching, traffic spike, static cache, benchmark, load, scalability, engines, public sector
type: help
---
# FAQ: Leistung und Velocity

Diese Seite beantwortet die Leistungsfragen öffentlicher Stellen zu Exponential. Sie nennt keine eigenen Zahlen zur
Geschwindigkeit: Messen Sie Ihre eigene Website. Die Antworten stammen aus
[Der Webserver Velocity](../../../../eng-us/features/6.0/velocity-web-server.md), [Caches](../../../anleitungen/caches.md) und
[Benchmarking](../../../anleitungen/benchmarking.md).

## Inhalt

1. [Wie schnell ist Exponential?](#wie-schnell-ist-exponential)
2. [Was ist Exponential Velocity?](#was-ist-exponential-velocity)
3. [Müssen wir Velocity verwenden?](#müssen-wir-velocity-verwenden)
4. [Wie funktioniert das Caching?](#wie-funktioniert-das-caching)
5. [Hält es eine Lastspitze aus?](#hält-es-eine-lastspitze-aus)
6. [Können wir ein CDN davorsetzen?](#können-wir-ein-cdn-davorsetzen)
7. [Wie messen wir es selbst?](#wie-messen-wir-es-selbst)

## Wie schnell ist Exponential?

Das hängt von Ihrem Design, Ihren Inhalten, Caches, der Engine und der Hardware ab. Bitten Sie die Bieter, Ihre Website
unter der erwarteten Last zu messen, und schreiben Sie das Ergebnis in die Abnahmetests. Wie veröffentlichte Zahlen
gemessen werden, ist für die Presse unter
[Wie die Zahlen von Velocity gemessen werden](../../presse/thema-wie-die-velocity-zahlen-gemessen-werden.md) erklärt.

## Was ist Exponential Velocity?

Ein in PHP geschriebener Webserver, der Exponential aus einem Pool langlebiger PHP-Prozesse ausführt. Er spricht
HTTP/1.1, HTTP/2 über TLS und WebSockets, liefert statische Dateien aus und speichert Seiten in seinem eigenen Prozess
zwischen; er braucht kein Apache, nginx oder PHP-FPM davor, funktioniert aber auch dahinter
([Der Webserver Velocity](../../../../eng-us/features/6.0/velocity-web-server.md)).

## Müssen wir Velocity verwenden?

Nein. Apache oder nginx mit PHP-FPM und FrankenPHP werden ebenfalls unterstützt
([8. Die Website ausliefern](../../../installation/08-die-website-ausliefern.md)). Das Projekt empfiehlt Velocity
([das Glossar von Exponential](../../../../eng-us/glossary.md#v)). Velocity läuft unter PHP 8.0 oder später (ab seinem Release
0.0.4.54; bis dahin 8.1).

## Wie funktioniert das Caching?

Mehrere Caches arbeiten zusammen: der View-Cache für Seiten, Template-Block-Caches sowie optional der statische Cache
und der Antwort-Cache von Velocity; **Setup > Cache-Verwaltung** listet sie auf ([Caches](../../../anleitungen/caches.md),
[Caching-Ebenen](../../hosting/leistung/cache-schichten.md)).

## Hält es eine Lastspitze aus?

Die Caches tragen den größten Teil der Last. Wärmen Sie sie vor einem angekündigten Ereignis vor
([Caches vorwärmen](../../../anleitungen/caches-vorwaermen.md)), erwägen Sie den statischen Cache oder einen Reverse Proxy und
betreiben Sie für mehr Kapazität mehrere Webknoten ([Mehrere Webknoten](../../hosting/betrieb/mehrere-web-knoten.md)).
Siehe [Bereitschaft für Notfallwarnungen](../checklisten/bereitschaft-fuer-notfallwarnungen.md).

## Können wir ein CDN davorsetzen?

Ja; HTTP-Cache-Header und Reverse Proxys sind für Hoster dokumentiert ([CDN](../../hosting/leistung/cdn.md),
[HTTP-Cache-Header](../../hosting/leistung/http-cache-header.md)).

## Wie messen wir es selbst?

Mit `exp:benchmark`: Seiten über HTTP, Vergleiche zwischen Engines und Schutz vor Leistungsrückschritten
([Benchmarking](../../../anleitungen/benchmarking.md),
[Benchmarking einer gehosteten Website](../../hosting/leistung/benchmark-einer-gehosteten-website.md)).

## Verwandte Seiten

- [FAQ für Verwaltungen](README.md)
- [Leistung und Caching](../../../../eng-us/help/creators/website-creators/performance-and-caching.md)
- [Velocity für Hoster](../../hosting/server/velocity-fuer-hoster.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
