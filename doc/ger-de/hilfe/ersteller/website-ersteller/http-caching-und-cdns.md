---
title: HTTP-Caching und CDNs
audience: website-creator, admin
category: website-creation
order: 640
summary: Browser, Reverse Proxys und CDNs Seiten und statische Dateien für anonyme Besucher vorhalten lassen - HTTPHeaderSettings, StaticMaxAge, der rollenbewusste HTTP-Cache, vertrauenswürdige Proxys und was nie gecacht werden darf.
keywords: HTTP-Caching, CDN, Reverse Proxy, vertrauenswürdige Proxys, anonyme Besucher, Website-Ersteller, HTTP caching, Cache-Control, Expires, reverse proxy, HTTPHeaderSettings, CustomHeader, StaticMaxAge, httpcache, exphttpcache, TrustedProxies, anonymous visitors, website creator
type: help
---
# HTTP-Caching und CDNs

Diese Seite richtet sich an Website-Ersteller, die eine Website hinter ein CDN oder einen Reverse Proxy stellen oder
einfach wollen, dass die Browser wiederkehrender Besucher Stylesheets und Bilder behalten. Standardmäßig sagt jede Seite
„nicht cachen“; Sie öffnen das nur für anonyme Besucher
([HTTP-Caching für anonyme Besucher](../../../bc/6.0/http-caching.md)).

## Inhalt

1. [Was standardmäßig gesendet wird](#was-standardmäßig-gesendet-wird)
2. [Seiten für anonyme Besucher](#seiten-für-anonyme-besucher)
3. [Statische Dateien](#statische-dateien)
4. [Der rollenbewusste HTTP-Cache](#der-rollenbewusste-http-cache)
5. [Hinter einem Proxy oder CDN](#hinter-einem-proxy-oder-cdn)
6. [Niemals cachen](#niemals-cachen)

## Was standardmäßig gesendet wird

Seiten: `Cache-Control: no-cache, must-revalidate`, ein abgelaufenes `Expires` und `Pragma: no-cache`. Für einen
angemeldeten Benutzer ändert sich in keiner Konfiguration etwas.

## Seiten für anonyme Besucher

Die Seiten-Header werden in `site.ini [HTTPHeaderSettings]` gesetzt (standardmäßig `CustomHeader=disabled`). Die
BC-Seite zeigt die Einstellungen, um nur anonymen Besuchern ein kurzes `Cache-Control: public, max-age=...` zu senden.
Wählen Sie eine kurze Lebensdauer (einige Minuten), es sei denn, Inhalte ändern sich selten: Ein Browser behält, was er
hat, bis es abläuft.

Prüfen:

```bash
curl -sI https://www.example.com/ | grep -i -E "cache-control|expires|pragma"
```

## Statische Dateien

Velocity sendet statische Dateien mit einer Lebensdauer, die `StaticMaxAge` festlegt; gepackte Stylesheets und Skripte
tragen den Änderungszeitpunkt im Namen, daher ist für sie eine lange Lebensdauer sicher. Senken Sie sie, während Sie an
einem Stylesheet arbeiten. Apache und nginx brauchen eigene Regeln für Dateien in `design/`, `extension/*/design/` und
`var/storage/`.

## Der rollenbewusste HTTP-Cache

`settings/httpcache.ini` schaltet den HTTP-Cache des Kernels (`exphttpcache`) ein, der Antworten pro Rollenkombination
speichert; er wird mit den Tags content und template geleert. Siehe [Der HTTP-Cache](../../../bc/6.0/httpcache.md).

## Hinter einem Proxy oder CDN

- Tragen Sie die Adressen des Proxys in `[HTTPHeaderSettings] TrustedProxies[]` ein, damit weitergeleitete Schema- und
  Host-Angaben nur von ihm geglaubt werden ([Vertrauenswürdige Proxys](../../../bc/6.0/vertrauenswuerdige-proxys.md),
  [8.8 Hinter einem Reverse Proxy](../../../installation/08-die-website-ausliefern.md#88-hinter-einem-reverse-proxy)).
- Lassen Sie das CDN nur cachen, was die Website als cachebar kennzeichnet; angemeldete Sitzungen tragen ein Cookie, das
  das CDN durchreichen muss.
- Leeren Sie das CDN nach Design-Änderungen oder verlassen Sie sich auf die wechselnden Dateinamen des Packers.

## Niemals cachen

- Seiten für angemeldete Benutzer, Warenkörbe, Kasse, Kontoseiten.
- Formulare mit einem Formular-Token (das Token gilt pro Sitzung).
- Die Administration.

## Verwandte Seiten

- [Statischer Cache und Velocity](statischer-cache-und-velocity.md)
- [Leistung und Caching](leistung-und-caching.md)
- [HTTP/2 und Cache-Vorwärmung](../../../bc/6.0/http2-und-cache-vorwaermen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
