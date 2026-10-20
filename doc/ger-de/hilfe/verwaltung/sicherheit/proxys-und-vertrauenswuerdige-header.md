---
title: Proxys und vertrauenswürdige Header
audience: government, admin, hosting
category: public-sector
order: 370
summary: Für öffentliche Websites hinter Load Balancern, CDNs oder TLS-Terminatoren - warum weitergeleiteten Headern nur von Ihren eigenen Proxys vertraut werden darf, die Einstellung TrustedProxies von Exponential und das Lesen von rechts, die eigene Liste von Velocity und wie Sie prüfen, dass ein Besucher weder HTTPS noch eine Adresse vortäuschen kann.
keywords: Proxy, Load Balancer, CDN, Reverse Proxy, weitergeleitete Header, IP-Spoofing, Kontosperre, Audit-Adressen, öffentlicher Sektor, proxy, load balancer, reverse proxy, X-Forwarded-For, X-Forwarded-Proto, X-Forwarded-Host, TrustedProxies, ClientIpByCustomHTTPHeader, Velocity proxy trusted, IP spoofing, lockout, DebugByIP, audit addresses, public sector
type: help
---
# Proxys und vertrauenswürdige Header

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Hosting-Teams von Websites hinter einem
Proxy, Load Balancer oder CDN, wie sie im Hosting von Verwaltungen üblich sind. Der Proxy teilt der Anwendung in
Headern mit, was der Besucher angefragt hat (`X-Forwarded-Proto`, `X-Forwarded-Host`, `X-Forwarded-For`), aber jeder
Besucher kann diese Header ebenfalls senden. Blind geglaubt, lassen sie einen Besucher HTTPS, einen Hostnamen oder die
Adresse vortäuschen, die die Kontosperre, `DebugByIP`, Anfrageregeln, das Audit-Protokoll und das
Einwilligungsprotokoll aufzeichnen
([13.10 Hinter einem Proxy: vertrauenswürdige Header](../../../installation/13-sicherheitshaertung.md#1310-hinter-einem-proxy-vertrauenswürdige-header)).

## Inhalt

1. [Die Regel](#die-regel)
2. [Welcher Aufbau was braucht](#welcher-aufbau-was-braucht)
3. [Unter Velocity](#unter-velocity)
4. [Prüfen](#prüfen)
5. [Verwandte Seiten](#verwandte-seiten)

## Die Regel

Seit 6.0.15 glaubt der Kernel weitergeleiteten Headern nur, wenn `REMOTE_ADDR` in
`site.ini [HTTPHeaderSettings] TrustedProxies[]` steht; der Standard vertraut nur `127.0.0.1` und `::1`. Die Adresse
des Besuchers in `X-Forwarded-For` wird **von rechts** gelesen: Der Durchlauf übergeht vertrauenswürdige Proxys und
hält bei der ersten Adresse an, die keiner ist, sodass ein gefälschter Eintrag weiter links nie verwendet wird.

## Welcher Aufbau was braucht

| Aufbau | Tun |
|---|---|
| Kein Proxy | nichts |
| Ein Proxy auf demselben Rechner, der die Header setzt | nichts; lassen Sie den Proxy `X-Forwarded-Proto` selbst setzen |
| Ein Proxy, Load Balancer oder CDN auf einer anderen Adresse | seine Adressen oder Bereiche eintragen, nur diese; sicherstellen, dass der Anwendungsserver nicht an ihnen vorbei erreichbar ist |
| Ein Frontend, das `REMOTE_ADDR` umschreibt (`mod_remoteip`, nginx `realip`) | nichts in Exponential |
| Sie möchten die echte Adresse des Besuchers | `ClientIpByCustomHTTPHeader=X-Forwarded-For` mit den Proxys in `TrustedProxies[]` |

Beispiel:

```ini
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
TrustedProxies[]
TrustedProxies[]=10.0.0.0/16
ClientIpByCustomHTTPHeader=X-Forwarded-For
```

Erweitern Sie die Liste nie auf ein ganzes Providernetz oder `0.0.0.0/0`. Hostnamen werden nicht aufgelöst, und ein
Eintrag, der keine Adresse ist, wird ignoriert.

## Unter Velocity

Velocity führt eine eigene Liste, `Q.webserver.proxy.trusted` (standardmäßig Loopback), gesetzt in einem Snippet unter
`/etc/vc/conf-available/`; tragen Sie auch dort genau Ihre Proxys ein
([13.12 Velocity-spezifische Härtung](../../../installation/13-sicherheitshaertung.md#1312-velocity-spezifische-härtung)).

## Prüfen

Die Prüfung aus der Installationsanleitung: Senden Sie eine Anfrage direkt, ohne den Proxy, mit einem gefälschten
`X-Forwarded-Proto: https`; die Umleitung muss `http` bleiben, weil der Header nichts geändert hat
([Prüfen](../../../installation/13-sicherheitshaertung.md#prüfen)). Prüfen Sie außerdem nach einer Testanmeldung, dass das
Audit Ihre Testadresse aufzeichnet und keine gefälschte.

Was bei falscher Konfiguration schiefgeht: Benutzer werden mit `CookieSecure=true` auf jeder Seite abgemeldet,
Umleitungsschleifen, jeder Besucher wird mit der Adresse des Proxys aufgezeichnet, oder eine Kontosperre sperrt alle
hinter dem Proxy.

## Verwandte Seiten

- [13.10 Hinter einem Proxy](../../../installation/13-sicherheitshaertung.md#1310-hinter-einem-proxy-vertrauenswürdige-header)
- [Weitergeleiteten Headern wird nur von konfigurierten Proxys vertraut](../../../../eng-us/bc/6.0/trusted-proxies.md)
- [HTTP-Caching und CDNs](../../../../eng-us/help/creators/website-creators/http-caching-and-cdns.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
