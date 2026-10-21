---
title: Varnish und Reverse Proxys
audience: hosting, admin
category: hosting
order: 70
summary: Einen Reverse Proxy oder einen zwischenspeichernden Proxy wie Varnish vor Exponential-Websites stellen - ob Sie einen brauchen, dem Proxy für HTTPS und die Adresse des Besuchers vertrauen, was zwischengespeichert werden darf und was nicht, und wie Sie leeren, wenn Exponential nichts zum Leeren sendet.
keywords: Reverse Proxy, zwischenspeichernder Proxy, Varnish, xkey, Purge, Ban, TrustedProxies, ClientIpByCustomHTTPHeader, X-Forwarded-Proto, X-Forwarded-For, X-Forwarded-Host, Q.webserver.proxy.trusted, TagHeader, Ereignis content/cache, Load Balancer, Sitzungscookie, reverse proxy, caching proxy, purge, ban, load balancer, session cookie
type: help
---
# Varnish und Reverse Proxys

Diese Seite ist für Systemadministratoren bei Hosting-Anbietern, die einen Reverse Proxy, einen zwischenspeichernden Proxy wie Varnish
oder einen Load Balancer vor Kunden-Websites betreiben. Sie beantwortet: ob Exponential überhaupt einen braucht, was der Proxy senden
muss und wofür ihm vertraut werden muss, was ein zwischenspeichernder Proxy speichern darf und wie Sie ihn leeren. Die eigene
Konfigurationssprache von Varnish wird hier nicht beschrieben; lesen Sie in seiner Dokumentation nach. Die Quellen sind
[Kapitel 8, Abschnitt 8.8](../../../installation/08-die-website-ausliefern.md#88-hinter-einem-reverse-proxy),
[Weitergeleiteten Headern wird nur von konfigurierten Proxys vertraut](../../../../eng-us/bc/6.0/trusted-proxies.md) und
[Kapitel 15, Abschnitt 15.14](../../../installation/15-migration-von-5x-legacy.md#1514-caches).

## Inhalt

1. [Brauchen Sie einen Proxy?](#brauchen-sie-einen-proxy)
2. [Was der Proxy senden muss](#was-der-proxy-senden-muss)
3. [Dem Proxy vertrauen](#dem-proxy-vertrauen)
4. [Ein zwischenspeichernder Proxy: was gespeichert werden darf](#ein-zwischenspeichernder-proxy-was-gespeichert-werden-darf)
5. [Leeren: Exponential sendet nichts](#leeren-exponential-sendet-nichts)
6. [Prüfungen und Symptome](#prüfungen-und-symptome)
7. [Checkliste](#checkliste)

## Brauchen Sie einen Proxy?

| Grund | Nötig? |
|---|---|
| Beenden von TLS | nein für Velocity, das HTTPS selbst ausliefert; ja, wenn ein Load Balancer oder CDN TLS bereits beendet |
| Mehrere Dienste oder Instanzen von Kunden hinter einer öffentlichen Adresse | ein einfacher Reverse Proxy ist die richtige Form |
| Velocity als gewöhnlicher Benutzer auf einem hohen Port betreiben | ein Proxy oder eine Umleitung der Firewall davor |
| Anonyme Seiten zwischenspeichern | nicht unbedingt: der Antwort-Cache von Velocity und der rollenbewusste HTTP-Cache tun das innerhalb von Exponential |

Für Websites, die von älteren Plattformen mit Varnish migriert wurden, sagt das Installationsbuch: Lassen Sie Varnish entweder weg (der
HTTP-Cache oder der Antwort-Cache von Velocity erledigt dieselbe Aufgabe innerhalb von Exponential), oder lassen Sie es anonyme Seiten
nur kurz zwischenspeichern, oder leeren Sie es selbst.

## Was der Proxy senden muss

| Header | Warum |
|---|---|
| `X-Forwarded-Proto` (vom Proxy gesetzt, nie vom Besucher durchgereicht) | damit erzeugte URLs, Umleitungen und das Merkmal `Secure` des Sitzungscookies stimmen |
| `X-Forwarded-For` (angehängt oder gesetzt) | die Adresse des Besuchers; Exponential liest die Liste von rechts, Anhängen ist also sicher |
| der ursprüngliche `Host` | Velocity nutzt `X-Forwarded-Host` nicht; unter PHP-FPM ersetzt ein vertrauenswürdiges `X-Forwarded-Host` den `Host` |

Abgeleitete Beispiele für nginx und Apache vor Velocity stehen auf
[Velocity neben Apache oder nginx](../server/velocity-neben-apache-oder-nginx.md#form-1-ein-proxy-vor-velocity).

## Dem Proxy vertrauen

Weitergeleitete Header zählen nur von einer vertrauenswürdigen Gegenstelle. Es gibt zwei Listen:

| Server hinter dem Proxy | Liste | Standard |
|---|---|---|
| Apache oder nginx mit PHP-FPM, FrankenPHP | `site.ini [HTTPHeaderSettings] TrustedProxies[]` | `127.0.0.1`, `::1` |
| Velocity | `Q.webserver.proxy.trusted` in der Konfiguration der Engine | `127.0.0.1`, `::1` |

Ein Proxy auf derselben Maschine (einschließlich Varnish auf Loopback) ist abgedeckt. Ein Proxy anderswo muss aufgeführt werden:

```ini
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
TrustedProxies[]
TrustedProxies[]=127.0.0.1
TrustedProxies[]=::1
TrustedProxies[]=10.0.0.0/16
ClientIpByCustomHTTPHeader=X-Forwarded-For
```

- Einträge sind Adressen oder Bereiche, IPv4 und IPv6; Hostnamen werden nicht aufgelöst. Eine Überschreibung, die mit
  `TrustedProxies[]` allein in einer Zeile beginnt, ersetzt die Liste.
- Kommentare gehören in eigene Zeilen: Nach einem Wert wird ein `#` als Teil des Werts gelesen, und der Eintrag wird ignoriert.
- `ClientIpByCustomHTTPHeader` ist nur unter PHP-FPM nötig; unter Velocity übergibt die Engine die Adresse des Besuchers als `REMOTE_ADDR`.
- Einem Proxy zu vertrauen heißt, ihm zu vertrauen, dass er die Header **setzt**. Ein lokaler Proxy, der das eigene `X-Forwarded-Proto`
  eines Besuchers weitergibt, würde es durchreichen; wenn Sie einen solchen Proxy nicht ändern können, setzen Sie `TrustedProxies[]` leer.
- Stellen Sie sicher, dass der Origin nicht am Proxy vorbei erreichbar ist (Firewall, Sicherheitsgruppe).

Für Velocity legen Sie die Liste in ein Snippet wie `/etc/vc/conf-available/reverse-proxy.conf` und aktivieren es mit
`exp:velocity conf enable reverse-proxy` ([Velocity neben Apache oder nginx](../server/velocity-neben-apache-oder-nginx.md#wer-dem-proxy-vertraut)).
Engine 0.0.4.44 oder später ist nötig, damit das weitergeleitete Protokoll nur von vertrauenswürdigen Proxys gelesen wird.

## Ein zwischenspeichernder Proxy: was gespeichert werden darf

> **Warnung**
> Ein zwischenspeichernder Proxy darf keine Seiten angemeldeter Benutzer speichern. Speichern Sie keine Antworten zwischen, die das
> Sitzungscookie setzen oder davon abhängen ([Kapitel 8, Abschnitt 8.8](../../../installation/08-die-website-ausliefern.md#88-hinter-einem-reverse-proxy)).

Was Exponential einem Proxy zum Arbeiten gibt:

- Seiten sind standardmäßig `no-cache`. Damit ein Proxy anonyme Seiten speichern darf, schalten Sie `[HTTPHeaderSettings]` mit
  `OnlyForAnonymous=enabled` und einem kurzen `max-age` ein ([HTTP-Cache-Header](http-cache-header.md)).
- Seiten, die ein Formular-Token tragen, werden `private` gesendet.
- Der Name des Sitzungscookies folgt `site.ini [Session] SessionNameHandler`: Bei `default` benennt PHP es; bei `custom` ist es
  `SessionNamePrefix` (mitgeliefert `eZSESSID`) plus ein Digest pro Siteaccess. Der Kernel setzt außerdem `is_logged_in` für angemeldete
  Besucher. Eine Proxy-Regel, die den Cache bei einem der beiden Cookies umgeht, entspricht dem, was der eigene Antwort-Cache von
  Velocity tut.
- Statische Dateien tragen unter Velocity lange Lebensdauern (`StaticMaxAge`) und können frei zwischengespeichert werden.

## Leeren: Exponential sendet nichts

Exponential sendet keine Anfragen zum Leeren oder Sperren an einen Proxy; im Kernel gibt es kein `PURGE` oder `BAN`. Ein Proxy, der
Seiten länger als ihr `max-age` behält, liefert nach einer Bearbeitung veraltete Seiten aus. Möglichkeiten aus dem Installationsbuch:

1. **Nur kurze Lebensdauern:** Lassen Sie den Proxy anonyme Seiten für das konfigurierte `max-age` behalten, und nicht länger.
2. **Nach Tag leeren:** `httpcache.ini [HttpCacheSettings] TagHeader=xkey` nennt die Purge-Tags jeder Seite in einem Header für das
   Modul xkey von Varnish (`Surrogate-Key` für Fastly, `Cache-Tag` für Cloudflare). Die Einstellung und die Tags gehören zum
   rollenbewussten HTTP-Cache ([HTTP-Cache](../../../../eng-us/bc/6.0/httpcache.md)); testen Sie, dass Seiten sie tragen.
3. **Beim Veröffentlichen selbst leeren:** Der Kernel löst bei jeder Veröffentlichung `content/cache` mit den IDs von Knoten und
   Objekten aus (`kernel/classes/ezcontentcachemanager.php`), was ein Listener in `site.ini [Event] Listeners[]` in ein Leeren umsetzen
   kann.
4. **Den Proxy-Cache weglassen** und den Antwort-Cache von Velocity oder den HTTP-Cache nutzen, die sich selbst leeren.

## Prüfungen und Symptome

Wenn die Website direkt erreichbar ist, darf ein weitergeleiteter Header eines Besuchers nichts ändern:

```bash
curl -s -o /dev/null -w '%{redirect_url}\n' -H 'X-Forwarded-Proto: https' http://example.com/user/logout
# expected: http://example.com/   -- https:// here means the header was believed
```

| Symptom | Ursache | Abhilfe |
|---|---|---|
| Umleitungen und absolute Links gehen auf `http://` | dem Proxy wird nicht vertraut, oder er sendet kein `X-Forwarded-Proto` | ihn aufführen; den Header setzen |
| jeder Besucher hat die Adresse des Proxys (Sperre bei der Anmeldung, Logs) | dem Proxy wird nicht vertraut, oder `ClientIpByCustomHTTPHeader` ist unter PHP-FPM nicht gesetzt | den Proxy aufführen; die Einstellung für den Header setzen |
| eine Umleitungsschleife nach dem Wechsel auf HTTPS | der Server weiß nicht, dass der Besucher HTTPS genutzt hat | wie in der ersten Zeile |
| Seiten angemeldeter Benutzer werden anderen Besuchern ausgeliefert | ein zwischenspeichernder Proxy speichert Seiten mit einem Sitzungscookie | den Cache beim Sitzungscookie und bei `is_logged_in` umgehen |
| Seiten bleiben hinter Varnish veraltet | Exponential sendet nichts zum Leeren | eine der Möglichkeiten zum Leeren oben |

## Checkliste

- [ ] Der Proxy setzt `X-Forwarded-Proto` und hängt `X-Forwarded-For` an oder setzt es; er reicht `Host` durch.
- [ ] Dem Proxy wird vertraut (`TrustedProxies[]` oder `Q.webserver.proxy.trusted` für Velocity); der Origin ist nicht an ihm vorbei
      erreichbar.
- [ ] Die curl-Prüfung besteht direkt und über den Proxy.
- [ ] Ein zwischenspeichernder Proxy umgeht Anfragen mit dem Sitzungscookie oder `is_logged_in` und speichert nur `public`-Seiten.
- [ ] Eine Strategie zum Leeren ist gewählt und in den Tarif geschrieben.

## Verwandte Seiten

- [Velocity neben Apache oder nginx](../server/velocity-neben-apache-oder-nginx.md)
- [HTTP-Cache-Header](http-cache-header.md)
- [CDN](cdn.md)
- [Weitergeleiteten Headern wird nur von konfigurierten Proxys vertraut](../../../../eng-us/bc/6.0/trusted-proxies.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
