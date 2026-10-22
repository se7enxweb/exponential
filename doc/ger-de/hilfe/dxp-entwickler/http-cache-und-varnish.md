---
title: HTTP-Cache und Varnish
audience: dxp-developer
category: dxp-development
order: 29
summary: Wie die DXP mit ibexa/http-cache ganze Antworten zwischenspeichert - standardmäßig der eingebaute Symfony-Proxy, Varnish als Option, die Einstellungen zum Purging, die Lebensdauer, Purging nach Pfad und Tag und was Sie prüfen, wenn eine Seite veraltet bleibt.
keywords: HTTP-Cache, ibexa/http-cache, Varnish, Symfony HttpCache, APP_HTTP_CACHE, HTTPCACHE_PURGE_TYPE, Purge, Invalidierung, fos:httpcache, Tags, TTL, TRUSTED_PROXIES, HTTP cache, purge, invalidate, tags
type: help
---
# HTTP-Cache und Varnish

Diese Seite richtet sich an Entwickler und Administratoren, die eine DXP-Website schnell machen. Die DXP liefert
`ibexa/http-cache` mit: Ganze Antworten werden zwischengespeichert, mit den Inhalten getaggt, die sie zeigen, und
geleert, wenn sich diese Inhalte ändern. Ohne Varnish übernimmt der HTTP-Cache-Proxy von Symfony innerhalb der
Anwendung diese Aufgabe.

## Inhalt

1. [Die zwei Betriebsarten](#die-zwei-betriebsarten)
2. [Einstellungen](#einstellungen)
3. [Auf Varnish umstellen](#auf-varnish-umstellen)
4. [Von Hand leeren](#von-hand-leeren)
5. [Wenn eine Seite veraltet bleibt](#wenn-eine-seite-veraltet-bleibt)

## Die zwei Betriebsarten

| Betriebsart | Setzen | Purge-Typ |
|---|---|---|
| HTTP-Cache von Symfony (Standard) | `APP_HTTP_CACHE=1` im Webserver | `HTTPCACHE_PURGE_TYPE=local` |
| Varnish davor | `APP_HTTP_CACHE=0` (stattdessen speichert Varnish) | `HTTPCACHE_PURGE_TYPE=varnish` |

Die `config/packages/ibexa.yaml` eines installierten Projekts erklärt, warum eine lange Lebensdauer sicher ist: Das
HTTP-Cache-Bundle "is designed to expire all affected cache on changes".

## Einstellungen

| Variable | Standard der Anleitung | Bedeutung |
|---|---|---|
| `HTTPCACHE_PURGE_TYPE` | `local` | `local` oder `varnish` |
| `HTTPCACHE_DEFAULT_TTL` | `86400` | Sekunden, die eine Antwort zwischengespeichert werden darf |
| `HTTPCACHE_PURGE_SERVER` | `http://localhost:80` | wohin Purge-Anfragen gehen (Adresse von Varnish) |
| `HTTPCACHE_VARNISH_INVALIDATE_TOKEN` | nicht gesetzt | ein gemeinsames Geheimnis für Purge-Anfragen, wo eine IP-Freigabeliste nicht möglich ist |
| `TRUSTED_PROXIES` | nicht gesetzt | die Adresse des Proxys, damit Symfony seinen Headern vertraut |

## Auf Varnish umstellen

Aus der Anleitung des Skeletons (Abschnitt 19):

1. Setzen Sie in `.env.local`:

   ```bash
   HTTPCACHE_PURGE_TYPE=varnish
   HTTPCACHE_PURGE_SERVER=http://127.0.0.1:6081
   HTTPCACHE_VARNISH_INVALIDATE_TOKEN=<your secret>
   TRUSTED_PROXIES=127.0.0.1
   ```

2. Setzen Sie `APP_HTTP_CACHE=0` in der Konfiguration des Webservers.
3. Laden Sie eine VCL, die die Tags und Purge-Anfragen des Bundles versteht. Die Anleitung verweist auf ein Verzeichnis
   `doc/varnish/` "if present" oder auf die Upstream-Dokumentation; im Skeleton selbst wird keine VCL mitgeliefert.
4. Leeren Sie den Cache und leeren Sie einmal alles:

   ```bash
   php bin/console cache:clear
   php bin/console fos:httpcache:invalidate:path / --all
   ```

Varnish 6.0 oder 7.1 und neuer sind die Versionen, die die Anleitung nennt.

## Von Hand leeren

```bash
php bin/console fos:httpcache:invalidate:path / --all     # every path
php bin/console fos:httpcache:invalidate:tag <tag>        # one tag
```

Das normale Veröffentlichen leert von selbst; leeren Sie nur nach einer Bereitstellung von Templates oder Konfiguration
von Hand.

## Wenn eine Seite veraltet bleibt

1. Ist es der Browser? Laden Sie ohne Cache neu oder testen Sie mit `curl -I`.
2. Ist es der HTTP-Cache? Sehen Sie sich `Age` und die Cache-Header der Antwort an.
3. Ist es der Anwendungs-Cache? Leeren Sie ihn ([Anwendungs-Cache und Redis](anwendungs-cache-und-redis.md)).
4. Mit Varnish: Erreicht ihn der Purge? Prüfen Sie `HTTPCACHE_PURGE_SERVER` und das Token sowie das Log von Varnish.
5. Nach einer Bereitstellung: Haben Sie den Cache nach dem Leeren vorgewärmt ([Bereitstellen](dxp-bereitstellen.md))?

## Verwandte Seiten

- [Anwendungs-Cache und Redis](anwendungs-cache-und-redis.md)
- [Umgebungsvariablen](umgebungsvariablen.md#suche-cache-e-mail-und-der-rest)
- [Webserver einrichten](webserver-einrichten.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
