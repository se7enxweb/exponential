---
title: "HTTP-Sicherheit: Header, CSP, Anfrageregeln und Proxys"
audience: software-engineer, admin, developer
category: software-engineering
order: 710
summary: Die HTTP-Sicherheitsschicht von Exponential - Sicherheits-Header aus SecurityHeaders[], die Content Security Policy mit Modi, Direktiven, Nonces und Berichten, Anfrageregeln, die nach der URL-Übersetzung erlauben, verweigern, weiterleiten oder protokollieren, vertrauenswürdige Proxys für weitergeleitete Header, sichere Weiterleitungen und HTTP-Caching-Regeln, die private Seiten privat halten.
keywords: HTTP-Sicherheit, Sicherheits-Header, Content Security Policy, CSP, Nonce, csp_nonce, report-only, Anfrageregeln, vertrauenswürdige Proxys, X-Forwarded-For, HSTS, sichere Weiterleitungen, Cache-Control, Softwareingenieur, HTTP security, security headers, request rules, trusted proxies, safe redirects
type: help
---
# HTTP-Sicherheit: Header, CSP, Anfrageregeln und Proxys

Diese Seite richtet sich an Ingenieure, die die HTTP-Schicht einer Exponential-Website konfigurieren oder prüfen. Jeder
Mechanismus hier ist eine Einstellung in `site.ini` oder in einer eigenen INI-Datei und lässt sich daher wie Code prüfen
und versionieren.

## Inhalt

1. [Sicherheits-Header](#sicherheits-header)
2. [Content Security Policy](#content-security-policy)
3. [Anfrageregeln](#anfrageregeln)
4. [Vertrauenswürdige Proxys](#vertrauenswürdige-proxys)
5. [Weiterleitungen](#weiterleitungen)
6. [Caching-Header](#caching-header)
7. [Eine Prüfliste](#eine-prüfliste)

## Sicherheits-Header

`site.ini [HTTPHeaderSettings] SecurityHeaders[]` wird auf jeder Seite gesendet. Mitgeliefert:

```ini
SecurityHeaders[X-Content-Type-Options]=nosniff
SecurityHeaders[X-Frame-Options]=SAMEORIGIN
SecurityHeaders[Content-Security-Policy]=frame-ancestors 'self'
SecurityHeaders[Referrer-Policy]=strict-origin-when-cross-origin
SecurityHeaders[Permissions-Policy]=camera=(), microphone=(), payment=(), usb=()
SecurityHeaders[Strict-Transport-Security]=
```

`Strict-Transport-Security` ist leer, bis Sie es setzen, weil HSTS erst gesendet werden darf, wenn HTTPS überall
funktioniert. Unter Velocity tut `[HTTPSSettings] HSTSMaxAge` dasselbe für den Server.

## Content Security Policy

`site.ini [ContentSecurityPolicy]`:

| Einstellung | Bedeutung |
|---|---|
| `Mode` | `disabled`, `report-only`, `enforce` oder `auto` (mitgeliefert): nur berichtend auf Administrations-Siteaccesses, auf den anderen aus |
| `Directives[]` | eine Direktive je Eintrag; mitgeliefert `'self'` für Skripte, Styles und Verbindungen, `object-src 'none'` |
| `Nonce=enabled`, `NonceDirectives[]` | eine Nonce je Anfrage, die `script-src` hinzugefügt wird; Templates geben sie mit `csp_nonce()` aus |
| `ReportURI` | wohin Browser Berichte senden; der Kernel hat außerdem einen Sammler `csp/report`, und Verstöße gehen nach `var/log/csp.log` |

Für eine Website einschalten: Betreiben Sie sie in `report-only`, sammeln Sie, was sie blockieren würde (Erweiterungen und
Website-Designs sind die übliche Quelle), verschieben Sie Inline-Skripte in Dateien oder geben Sie ihnen die Nonce und
schalten Sie dann auf `enforce` um ([Content Security Policy](../../funktionen/6.0/content-security-policy.md),
[CSP: Inline-Skripte herausverlagern](../erweiterungsentwicklung/teil-4-aktualisieren/csp-inline-skripte-auslagern.md)).

## Anfrageregeln

Anfrageregeln laufen nach der URL-Übersetzung und nachdem die Rollenrichtlinien die View erlaubt haben, und können
erlauben, verweigern, weiterleiten, umschreiben oder protokollieren. Die klassische Verwendung leitet System-URLs wie
`/content/view/full/123` auf ihren Alias weiter, sofern der Benutzer nicht `content/view_system_url` hat. Prüfen Sie die
Entscheidung für eine beliebige Adresse, ohne etwas zu ändern:

```bash
php bin/php/ezrequestrules.php -s site --check
php bin/php/ezrequestrules.php -s site --uri=content/view/full/2
```

Siehe [Anfrageregeln](../../funktionen/6.0/anfrageregeln.md) und [Sicherheit von view/full](../../bc/6.0/sicherheit-von-content-view-full.md).

## Vertrauenswürdige Proxys

Seit dem 5. Oktober 2026 glaubt der Kernel `X-Forwarded-For`, `-Proto`, `-Port`, `-Server` und `-Host` nur, wenn die
Verbindung von einer Adresse in `site.ini [HTTPHeaderSettings] TrustedProxies[]` kommt (mitgeliefert: `127.0.0.1` und
`::1`). `X-Forwarded-For` wird von rechts gelesen, wobei vertrauenswürdige Proxys übersprungen werden. Ohne die richtige
Liste hinter einem Load Balancer sind HTTPS-Erkennung, absolute URLs, die Client-Adresse von `DebugByIP`, die
Anmeldesperre, Anfrageregeln nach Netzwerk und das Audit-Log alle falsch; mit einer zu weiten Liste kann ein Besucher sie
fälschen ([Vertrauenswürdige Proxys](../../bc/6.0/vertrauenswuerdige-proxys.md)).

## Weiterleitungen

Weiterleitungen laufen über `eZRedirectManager`, der nur Hosts folgt, die in `[SiteSettings] AllowedRedirectHosts` erlaubt
sind ([Sichere Weiterleitungen](../../funktionen/6.0/sichere-weiterleitungen.md),
[Schutz für Weiterleitungen, Abfragen und Bildvarianten](../../bc/6.0/schutz-fuer-weiterleitungs-query-und-bildaliase.md)).

## Caching-Header

Seiten werden standardmäßig mit `no-cache` gesendet. Caching-Header für Seiten lassen sich nur für anonyme Besucher
einschalten; die Seite eines angemeldeten Benutzers wird durch sie nie cachebar
([HTTP-Caching](../../bc/6.0/http-caching.md)). Jeder Cache, den Sie davorschalten (CDN, Proxy), muss dieselbe Trennung
einhalten, sonst erreicht die Seite eines Benutzers einen anderen.

## Eine Prüfliste

- [ ] `TrustedProxies[]` listet genau die Proxys vor der Website, nicht mehr.
- [ ] HSTS ist gesetzt, sobald HTTPS jeden Hostnamen abdeckt.
- [ ] Die CSP wird in der Administration nach einer nur berichtenden Phase durchgesetzt und ist für die Websites geplant.
- [ ] Kein Inline-Skript ohne die Nonce; keine `on...=`-Attribute oder `javascript:`-URLs in Templates.
- [ ] Anfrageregeln existieren für System-URLs, wenn sie nicht erreichbar sein dürfen.
- [ ] Kein Proxy oder CDN speichert Seiten angemeldeter Benutzer.

## Verwandte Seiten

- [Sicherheitstechnik im Überblick](sicherheitstechnik-im-ueberblick.md)
- [Content Security Policy für Erweiterungen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/content-security-policy-fuer-erweiterungen.md)
- [Content Security Policy, für Anwendungsentwickler](../ersteller/anwendungsentwickler/content-security-policy.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
