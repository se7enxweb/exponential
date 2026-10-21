---
title: CDN
audience: hosting, admin
category: hosting
order: 80
summary: Ein Content Delivery Network vor gehostete Exponential-Websites stellen - was sicher am Edge zwischengespeichert werden darf, den Adressen des CDN für HTTPS und die Adresse des Besuchers vertrauen, die CDN-Header, die Velocity versteht, Purge-Tags, Zertifikate und die Prüfungen vor dem Go-Live.
keywords: CDN, Content Delivery Network, Cloudflare, CloudFront, Fastly, CF-Visitor, CF-Connecting-IP, CloudFront-Forwarded-Proto, TrustedProxies, ClientIpByCustomHTTPHeader, Cache-Tag, Surrogate-Key, StaticMaxAge, Edge-Cache, Origin, content delivery network, edge cache, origin
type: help
---
# CDN

Diese Seite ist für Systemadministratoren und Produktmanager bei Hosting-Anbietern, die ein Content Delivery Network (CDN) vor
Kunden-Websites anbieten, ein eigenes oder das eines Dritten. Sie beantwortet: was Exponential sendet, das ein CDN zwischenspeichern
kann, wofür dem CDN vertraut werden muss, welche CDN-Header Exponential und Velocity verstehen und wie Sie verhindern, dass der Edge
veraltete oder persönliche Seiten ausliefert. Dashboards und Regelsprachen der Anbieter werden hier nicht beschrieben; lesen Sie in der
Dokumentation des Anbieters nach.

## Inhalt

1. [Was ein CDN für eine Exponential-Website tun kann](#was-ein-cdn-für-eine-exponential-website-tun-kann)
2. [Statische Dateien am Edge](#statische-dateien-am-edge)
3. [Seiten am Edge](#seiten-am-edge)
4. [Dem CDN vertrauen](#dem-cdn-vertrauen)
5. [Leeren](#leeren)
6. [Zertifikate](#zertifikate)
7. [Prüfungen vor dem Go-Live](#prüfungen-vor-dem-go-live)
8. [Checkliste](#checkliste)

## Was ein CDN für eine Exponential-Website tun kann

| Inhalt | Am Edge zwischenspeicherbar? | Was es entscheidet |
|---|---|---|
| Stylesheets, Skripte, Schriften, Bilder von Designs und Erweiterungen | ja | die Lebensdauer, die der Server sendet (`StaticMaxAge` unter Velocity) |
| Bilder von Inhalten (`var/*/storage/images`) | ja | Exponential ändert die URL eines Bildes, wenn das Bild aktualisiert wird |
| Anonyme Seiten | nur, wenn Sie zwischenspeicherbare Header einschalten | `[HTTPHeaderSettings]` mit `OnlyForAnonymous=enabled` |
| Seiten angemeldeter Benutzer, die Administration, Formulare | nein | standardmäßig `no-cache`; Seiten mit einem Formular-Token sind `private` |

Der Hinweis zu Bild-URLs steht im mitgelieferten Beispiel für Apache: Exponential hängt die Versionsnummer an die URL eines Bildes
(Datentyp ezimage) an, sodass sich die URL ändert, wenn ein Bild aktualisiert wird
([doc/eng-us/examples/ezpublish.conf](../../../../eng-us/examples/ezpublish.conf)).

## Statische Dateien am Edge

Velocity sendet für statische Dateien eine Lebensdauer von einem Jahr (`[ServerSettings] StaticMaxAge=31536000`); unter Apache oder
nginx fügen Sie die Header selbst hinzu ([HTTP-Cache-Header](http-cache-header.md#statische-dateien)). Ein unter demselben Namen ersetztes
Stylesheet wird erst nach Ablauf seiner Lebensdauer erneut geholt, am Edge wie im Browser. Gepackte Skripte und Stylesheets des
ezjscore-Packers tragen ihre Änderungszeit im Namen, wenn `ezjscore.ini [Packer] AppendLastModifiedTime=enabled` (so ausgeliefert),
was sie sicher für lange Zwischenspeicherung macht.

## Seiten am Edge

Seiten werden mit `Cache-Control: no-cache, must-revalidate` ausgeliefert, sodass ein CDN, das die Header des Origins beachtet, keine
speichert. Damit der Edge anonyme Seiten behalten darf, schalten Sie zwischenspeicherbare Header nur für anonyme Besucher ein, mit
einem kurzen `max-age` ([HTTP-Cache-Header](http-cache-header.md#zwischenspeicherbare-seiten-für-anonyme-besucher)).

> **Warnung**
> Lassen Sie den Edge nie eine Antwort auf eine Anfrage speichern, die das Sitzungscookie oder `is_logged_in` trug, und
> konfigurieren Sie das CDN nie so, dass es `private` oder `no-cache` des Origins ignoriert. Eine am Edge zwischengespeicherte Seite
> eines angemeldeten Benutzers wird Fremden ausgeliefert.

Wenn die Website des Kunden auf Velocity läuft, beantwortet bereits dessen Antwort-Cache anonyme Seiten am Origin; das CDN spart dann
vor allem die Netzwerkstrecke und nimmt statische Dateien ab.

## Dem CDN vertrauen

Hinter einem CDN kommt jede Anfrage von den Adressen des CDN. Ohne Vertrauen behandelt der Kernel Anfragen als reines HTTP von der
Adresse des Proxys, was sich als URLs mit `http://`, Umleitungen auf `http://` und jeder Besucher mit der Adresse des CDN zeigt
([Weitergeleitete Header](../../../bc/6.0/vertrauenswuerdige-proxys.md#wer-handeln-muss)).

```ini
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
TrustedProxies[]
TrustedProxies[]=127.0.0.1
TrustedProxies[]=::1
# the CDN's published ranges, one per line, copied from the provider's own list
TrustedProxies[]=192.0.2.0/24
ClientIpByCustomHTTPHeader=X-Forwarded-For
```

- Führen Sie nur die Bereiche auf, die der Anbieter veröffentlicht, und halten Sie sie aktuell: Bereiche ändern sich von Zeit zu Zeit.
- Cloudflare hängt an `X-Forwarded-For` an und sendet außerdem `CF-Connecting-IP` (eine Adresse); beides funktioniert als
  `ClientIpByCustomHTTPHeader`.
- Ein CDN vor einem Load Balancer: Führen Sie beide auf; der Durchgang von rechts geht durch alle.
- Stellen Sie sicher, dass der Origin nicht am CDN vorbei erreichbar ist (Firewall, Sicherheitsgruppe), sonst spricht ein Besucher
  direkt mit PHP.

Unter Velocity entscheidet stattdessen die eigene Liste der Engine, `Q.webserver.proxy.trusted`, und von einem vertrauenswürdigen Proxy
behandelt sie eine einfache Verbindung als HTTPS bei `X-Forwarded-Proto: https`, `CloudFront-Forwarded-Proto: https` oder
`CF-Visitor: {"scheme":"https"}` von Cloudflare ([Kapitel 8, Abschnitt 8.3.9](../../../installation/08-die-website-ausliefern.md#839-https-direkt-von-velocity)).
Legen Sie die Bereiche in ein Snippet, wie auf [Velocity neben Apache oder nginx](../server/velocity-neben-apache-oder-nginx.md#wer-dem-proxy-vertraut)
gezeigt.

## Leeren

Exponential sendet keine Anfragen zum Leeren an ein CDN. Möglichkeiten:

- halten Sie die Lebensdauer anonymer Seiten kurz, sodass sich der Edge von selbst auffrischt;
- `httpcache.ini [HttpCacheSettings] TagHeader=Cache-Tag` (Cloudflare) oder `Surrogate-Key` (Fastly) nennt die Purge-Tags jeder Seite,
  nach denen das CDN leeren kann; standardmäßig aus, weil die Tags interne IDs nennen;
- ein Listener auf das Ereignis `content/cache` des Kernels, das bei jeder Veröffentlichung mit IDs von Knoten und Objekten ausgelöst
  wird, kann die Purge-API des Anbieters aufrufen
  ([Varnish und Reverse Proxys](varnish-und-reverse-proxys.md#leeren-exponential-sendet-nichts)).

## Zertifikate

Wenn das CDN TLS für den Besucher beendet, braucht der Origin trotzdem ein Zertifikat, wenn das CDN sich über HTTPS verbindet. Die
ACME-Challenge HTTP-01 von Velocity braucht `/.well-known/acme-challenge/...` auf Port 80 jedes Namens, damit sie den Server erreicht;
mit einem CDN davor prüfen Sie, dass dieser Pfad den Origin erreicht, oder nutzen Sie die dokumentierten Alternativen (`dns-01` mit
einem Hook-Programm oder Ihre eigenen Zertifikatsdateien) ([HTTPS-Zertifikate in Velocity](../server/https-zertifikate-in-velocity.md)).
`HSTSMaxAge` sendet der Origin, und das CDN gibt es weiter; erhöhen Sie es erst, wenn jeder Name dauerhaft auf HTTPS ist.

## Prüfungen vor dem Go-Live

```bash
# through the CDN: static files cacheable, pages not, unless configured
curl -sI https://example.com/robots.txt | grep -i cache-control
curl -sI https://example.com/ | grep -i -E 'cache-control|x-cache|x-exp-cache'
# directly at the origin, bypassing the CDN: a forged header must change nothing
curl -s -o /dev/null -w '%{redirect_url}\n' -H 'X-Forwarded-Proto: https' http://origin.example.com/user/logout
```

Melden Sie sich dann mit einem Testkonto über das CDN an und prüfen Sie, dass die Seite des angemeldeten Benutzers mit `no-cache`
beantwortet und nie einem zweiten, anonymen Browser ausgeliefert wird.

## Checkliste

- [ ] Die Bereiche des CDN stehen in `TrustedProxies[]` (oder `Q.webserver.proxy.trusted`) und werden aktuell gehalten.
- [ ] Der Origin ist nicht am CDN vorbei erreichbar.
- [ ] Statische Dateien haben lange Lebensdauern; anonyme Seiten kurze, nur mit `OnlyForAnonymous=enabled`.
- [ ] Anfragen mit dem Sitzungscookie oder `is_logged_in` umgehen den Edge-Cache.
- [ ] Eine Strategie zum Leeren ist gewählt.
- [ ] Die Erneuerung von Zertifikaten funktioniert mit dem CDN davor.

## Verwandte Seiten

- [Varnish und Reverse Proxys](varnish-und-reverse-proxys.md)
- [HTTP-Cache-Header](http-cache-header.md)
- [TLS und HTTPS](../sicherheit/tls-und-https.md)
- [Weitergeleiteten Headern wird nur von konfigurierten Proxys vertraut](../../../bc/6.0/vertrauenswuerdige-proxys.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
