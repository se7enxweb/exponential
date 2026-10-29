---
title: HTTP-Caching und Reverse Proxies
audience: web-framework-developer, developer
category: web-frameworks
order: 290
summary: Caching auf HTTP-Ebene in Exponential für Entwickler, die den HttpCache von Symfony, Varnish-Setups, das Response-Caching von Laravel oder die Cache-Middleware von Django kennen - Seiten-Header für anonyme Besucher aus Einstellungen, der rollenbewusste HTTP-Cache mit seinem frühen Ausstieg, der eingebaute Response-Cache von Velocity, vertrauenswürdige Proxies, CDNs und was niemals gecacht werden darf.
keywords: HTTP-Cache, HTTP cache, Cache-Control, Expires, HTTPHeaderSettings, CustomHeader, OnlyForAnonymous, httpcache.ini, X-Exp-Cache, früher Ausstieg, early exit, Velocity-Response-Cache, Velocity response cache, Reverse Proxy, reverse proxy, Varnish, CDN, TrustedProxies, ETag, Web-Framework-Entwickler, web framework developer
type: help
---
# HTTP-Caching und Reverse Proxies

Diese Seite richtet sich an Entwickler, die `Cache-Control` in einem Controller setzen, Varnish vorschalten oder den
HTTP-Cache eines Frameworks verwenden. In Exponential sind Seiten-Header **Einstellungen**, und es gibt zwei eingebaute
Seiten-Caches, die vor dem Kernel liegen. Auf HTTP-Ebene wird nichts gecacht, bis Sie es einschalten.

## Inhalt

1. [Was standardmäßig gesendet wird](#was-standardmäßig-gesendet-wird)
2. [Header für anonyme Besucher](#header-für-anonyme-besucher)
3. [Der rollenbewusste HTTP-Cache](#der-rollenbewusste-http-cache)
4. [Der Velocity-Response-Cache](#der-velocity-response-cache)
5. [Hinter einem Proxy oder CDN](#hinter-einem-proxy-oder-cdn)
6. [Niemals cachen](#niemals-cachen)
7. [Im Vergleich mit anderen Frameworks](#im-vergleich-mit-anderen-frameworks)

## Was standardmäßig gesendet wird

Seiten: `Cache-Control: no-cache, must-revalidate`, ein abgelaufenes `Expires` und `Pragma: no-cache`. So behält kein
Browser, Proxy oder CDN eine Seite, bis Sie es anders entscheiden
([HTTP-Caching für anonyme Besucher](../../bc/6.0/http-caching.md)).

## Header für anonyme Besucher

```ini
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
CustomHeader=enabled
OnlyForAnonymous=enabled
OnlyForContent=enabled

HeaderList[]
HeaderList[]=Cache-Control
HeaderList[]=Expires

Cache-Control[]
Cache-Control[/]=public, max-age=60
Expires[]
Expires[/]=60
```

`OnlyForAnonymous` macht das sicher: Ein angemeldeter Benutzer erhält immer die No-Cache-Header. Die Schlüssel sind
Pfade, sodass verschiedene Teile des Baums unterschiedliche Lebensdauern erhalten können. Der Preis ist Veraltung: Ein
anonymer Besucher sieht nach einer Bearbeitung möglicherweise eine Seite, die bis zu `max-age` Sekunden alt ist.

## Der rollenbewusste HTTP-Cache

Der HTTP-Cache des Kernels (`settings/httpcache.ini`, ausgeliefert mit `[HttpCacheSettings] Enabled=disabled`)
speichert gerenderte Inhalts-Views mit einem Schlüssel aus Schema, Host, Siteaccess, Pfad und dem
**Berechtigungskontext** des Besuchers: eine Kopie für anonyme Besucher, eine pro Kombination aus Rollen und
Rolleneinschränkungen und eine private Kopie nur dort, wo eine Richtlinie `content/read` vom Benutzer abhängt. Der
nächste Request wird beantwortet, bevor der Kernel startet: durch einen frühen Ausstieg, der unter Apache, PHP-FPM oder
FrankenPHP aus `config.php` eingebunden wird, und unter Velocity durch den Serverprozess selbst. Der Header
`X-Exp-Cache` meldet `HIT`, `STALE`, `MISS` oder `BYPASS` mit einer Begründung. Das eigene Formular-Token des Besuchers
wird bei jedem Treffer wieder in die gespeicherte Seite eingesetzt ([Der rollenbewusste HTTP-Cache](../../bc/6.0/httpcache.md)).

Das ist ungewöhnlich: Die meisten HTTP-Caches von Frameworks bedienen nur anonymen Verkehr. Hier teilen sich
angemeldete Mitglieder gecachte Seiten auf sichere Weise, weil der Schlüssel ihr Berechtigungskontext ist.

## Der Velocity-Response-Cache

Velocity kann wiederholte Requests für öffentliche Seiten innerhalb des Serverprozesses beantworten, noch bevor ein
Worker gewählt wird: bedingte Requests (`304`), `ETag`, `Age`, Stale-While-Revalidate und Invalidierung, wie ein kleiner
in den Server eingebauter Reverse Proxy. Er ist ausgeschaltet, bis eine Einstellung ihn aktiviert
([Velocity-Response-Cache](../../funktionen/6.0/velocity-antwort-cache.md)). Leeren Sie ihn bei einem Deployment als
Letztes: `./console exp:velocity cache clear --apply`.

## Hinter einem Proxy oder CDN

- Tragen Sie die Adressen des Proxys in `site.ini [HTTPHeaderSettings] TrustedProxies[]` ein, damit weitergereichtes
  Schema und weitergereichter Host nur von ihnen geglaubt werden
  ([Vertrauenswürdige Proxies](../../bc/6.0/vertrauenswuerdige-proxys.md),
  [Hinter einem Reverse Proxy](../../installation/08-die-website-ausliefern.md#88-hinter-einem-reverse-proxy)).
- Lassen Sie das CDN nur cachen, was die Site als cachebar kennzeichnet; angemeldete Sessions tragen ein Cookie, das
  das CDN durchreichen muss.
- Für einen vorgeschalteten Proxy mit Purging kann `httpcache.ini [HttpCacheSettings] TagHeader` die Purge-Tags jeder
  Seite (ihre Knoten, Objekte und Layouts) in einem Header wie `xkey` (Varnish-xkey-VMOD), `Surrogate-Key` (Fastly)
  oder `Cache-Tag` (Cloudflare) senden; standardmäßig ist die Einstellung `disabled`, weil die Tags interne IDs nennen.
  Den Purge-Request beim Veröffentlichen an den Proxy zu senden, ergänzen Sie selbst, zum Beispiel in einem
  `content/cache`-Listener ([Ereignisse und Listener](ereignisse-und-listener.md)).

## Niemals cachen

Aus [HTTP-Caching und CDNs](../ersteller/website-ersteller/http-caching-und-cdns.md#niemals-cachen): Seiten für angemeldete
Benutzer in einem gemeinsamen Cache, Warenkörbe, Checkout, Kontoseiten, Formulare mit einem Formular-Token (das Token
gilt pro Session) und die Administrationsoberfläche.

## Im Vergleich mit anderen Frameworks

| Framework | Konzept | Exponential |
|---|---|---|
| Symfony | Reverse Proxy `HttpCache`, `#[Cache]`, ESI | über Einstellungen gesteuerte Header, der rollenbewusste HTTP-Cache, der Velocity-Response-Cache; kein ESI |
| Laravel | Pakete für Response-Caching, Middleware für `Cache-Control` | dieselben Einstellungen und Caches |
| Django | `cache_page`, `UpdateCacheMiddleware` | dasselbe |
| Rails | `expires_in`, `fresh_when` | Header pro Pfad in den Einstellungen |

## Verwandte Seiten

- [Caching: die Ebenen](cache-ebenen.md)
- [HTTP/2 und Cache-Aufwärmen](../../bc/6.0/http2-und-cache-vorwaermen.md)
- [Statischer Cache und Velocity](../ersteller/website-ersteller/statischer-cache-und-velocity.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
