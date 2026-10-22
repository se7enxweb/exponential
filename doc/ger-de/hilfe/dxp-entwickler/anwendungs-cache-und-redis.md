---
title: Anwendungs-Cache und Redis
audience: dxp-developer
category: dxp-development
order: 30
summary: Der Anwendungs-Cache der DXP - der Symfony-Cache, der tagfähige Cache-Pool für Inhalte (standardmäßig im Dateisystem, Redis oder Memcached als Optionen), wie Sie umstellen, wie Sie jeden Teil leeren und Sessions in Redis.
keywords: Anwendungs-Cache, Cache-Pool, CACHE_POOL, cache.tagaware.filesystem, cache.redis, cache.memcached, Redis, cache:clear, cache:warmup, cache:pool:clear, Sessions, application cache, cache pool, sessions
type: help
---
# Anwendungs-Cache und Redis

Diese Seite richtet sich an Entwickler und Administratoren, die die Caches hinter einer DXP-Website abstimmen. Drei
Caches werden leicht verwechselt; diese Seite trennt sie.

## Inhalt

1. [Drei Caches](#drei-caches)
2. [Der Cache-Pool für Inhalte](#der-cache-pool-für-inhalte)
3. [Auf Redis umstellen](#auf-redis-umstellen)
4. [Jeden Cache leeren](#jeden-cache-leeren)
5. [Sessions](#sessions)

## Drei Caches

| Cache | Enthält | Wo |
|---|---|---|
| Symfony-Cache | den kompilierten Container, Routen, Twig-Templates | `var/cache/<env>/` |
| Cache-Pool für Inhalte | Repository-Daten (Inhalte, Platzierungen, Typen), tagfähig | der Pool, den `CACHE_POOL` nennt |
| HTTP-Cache | ganze Antworten | der Symfony-Proxy oder Varnish ([HTTP-Cache](http-cache-und-varnish.md)) |

## Der Cache-Pool für Inhalte

`CACHE_POOL` nennt den Pool; der Standard ist `cache.tagaware.filesystem`. Ein installiertes Projekt hält die
vordefinierten Pools in `config/packages/cache_pool/`: `cache.tagaware.filesystem.yaml`, `cache.redis.yaml` und
`cache.memcached.yaml`. Jeder Siteaccess verwendet den Pool, der in `ibexa.system.default.cache_service_name` gesetzt ist
(`'%cache_pool%'`). Eine Installation mit mehreren Repositorys braucht je Repository einen anderen Pool.

## Auf Redis umstellen

```bash
CACHE_POOL=cache.redis
CACHE_DSN=redis://localhost:6379
```

Leeren Sie dann den Symfony-Cache. Redis 6 oder neuer ist das Minimum der Anleitung; sie empfiehlt Redis für Caching
und Sessions in der Produktion. Redis braucht die PHP-Erweiterung `redis`.

## Jeden Cache leeren

```bash
php bin/console cache:clear --env=prod                    # Symfony cache
php bin/console cache:warmup --env=prod                   # build it again before traffic arrives
php bin/console cache:pool:clear cache.tagaware.filesystem   # the content pool (or cache.redis)
php bin/console fos:httpcache:invalidate:path / --all     # the HTTP cache
```

Nur in der Entwicklung entfernt die "nukleare Option" der Anleitung `var/cache/dev` und `var/cache/prod` und wärmt
erneut vor. Löschen Sie auf einem Produktionsserver unter Verkehr keine Cache-Verzeichnisse; verwenden Sie die Befehle.

## Sessions

Sessions liegen standardmäßig als Dateien unter `var/sessions/<env>` (`SESSION_HANDLER_ID=session.handler.native_file`,
`SESSION_SAVE_PATH`). Bei mehreren Webservern müssen Sessions geteilt werden; Redis ist eine übliche Wahl. Ändern Sie den
Handler in `.env.local` und testen Sie die Anmeldung auf jedem Server.

## Verwandte Seiten

- [HTTP-Cache und Varnish](http-cache-und-varnish.md)
- [Befehle für den Alltag](befehle-fuer-den-alltag.md)
- [Fehlersuche](fehlersuche-in-der-dxp.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
