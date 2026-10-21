---
title: HTTPS und HSTS
audience: government, admin, hosting
category: public-sector
order: 170
summary: Für öffentliche Stellen, die ihre Website nur über HTTPS ausliefern - die Schritte aus der Installationsanleitung von Exponential (HTTPS auf jedem Namen, Umleitungen, Secure-Cookies, HSTS schrittweise angehoben, Zertifikatsüberwachung), die Einstellungen für Velocity und für Apache oder nginx und die Fallstricke von HSTS.
keywords: HTTPS, TLS, HSTS, Zertifikate, Umleitungen, Behördendomains, öffentlicher Sektor, Strict-Transport-Security, max-age, includeSubDomains, preload, HSTSMaxAge, certificates, ACME, Let's Encrypt, CookieSecure, redirects, public sector, government domains
type: help
---
# HTTPS und HSTS

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Hosting-Teams. Viele Regierungen erwarten,
dass ihre öffentlichen Websites ausschließlich über HTTPS ausgeliefert werden. HSTS (HTTP Strict Transport Security)
lässt Browser sich das merken und unverschlüsseltes HTTP zu diesem Host verweigern. In Exponential ist HSTS
**standardmäßig leer** und muss vom Betreiber gesetzt werden, sobald die gesamte Website nur noch über HTTPS läuft
([Sicherheitsstandards: bekannte Lücken](../../../../eng-us/standards/security.md#known-gaps)).

## Inhalt

1. [Die Reihenfolge der Schritte](#die-reihenfolge-der-schritte)
2. [HSTS-Einstellungen](#hsts-einstellungen)
3. [HSTS sicher anheben](#hsts-sicher-anheben)
4. [Prüfen](#prüfen)
5. [Verwandte Seiten](#verwandte-seiten)

## Die Reihenfolge der Schritte

Aus [HTTPS überall](../../../installation/13-sicherheitshaertung.md#überall-https):

1. Liefern Sie jeden Namen der Website über HTTPS aus: Velocity `[HTTPSSettings]` oder dessen ACME-Unterstützung,
   Apache `mod_ssl`, nginx `ssl`.
2. Leiten Sie unverschlüsseltes HTTP auf HTTPS um (Webserver), oder für einzelne Teilbäume mit `[SSLZoneSettings]`.
3. Setzen Sie `[Session] CookieSecure=true`.
4. Schalten Sie HSTS ein, zunächst kurz, und heben Sie den Wert an, sobald alles funktioniert.
5. Halten Sie die Zertifikate erneuert und überwacht (`./console exp:velocity ssl show --json`, Feld `daysLeft`, oder
   der Timer Ihres ACME-Clients).

## HSTS-Einstellungen

| Engine | Einstellung | Ab Werk |
|---|---|---|
| Velocity | `settings/velocity.ini [HTTPSSettings] HSTSMaxAge`, `HSTSIncludeSubDomains`, `HSTSPreload` | `HSTSMaxAge=300` |
| Apache oder nginx mit PHP-FPM | `site.ini [HTTPHeaderSettings] SecurityHeaders[Strict-Transport-Security]` | leer |

Für Apache oder nginx in `settings/override/site.ini.append.php`:

```ini
[HTTPHeaderSettings]
SecurityHeaders[Strict-Transport-Security]=max-age=300
```

Der Kernel sendet den Header nur über HTTPS. Leeren Sie danach den INI-Cache und laden Sie PHP neu.

## HSTS sicher anheben

Ein Browser merkt sich HSTS für den gesamten Zeitraum und verweigert unverschlüsseltes HTTP zu diesem Host, **auch
nachdem Sie den Wert gesenkt haben**.

| Stufe | Wert | Weiter, wenn |
|---|---|---|
| 1 | `max-age=300` (5 Minuten) | jede Seite und jede Ressource jedes Namens über HTTPS funktioniert |
| 2 | `max-age=86400` (1 Tag) | eine Woche ohne Probleme |
| 3 | `max-age=2592000` (30 Tage) | ein Monat ohne Probleme |
| 4 | `max-age=31536000` (1 Jahr) | jeder Name dauerhaft über HTTPS ausgeliefert wird |

Fügen Sie `includeSubDomains` nur hinzu, wenn **jede** Subdomain der Domain ausschließlich über HTTPS erreichbar ist:
Auf einer Behördendomain können das auch Dienste anderer Teams sein. Preload-Listen lassen sich nur schwer wieder
verlassen; entscheiden Sie das mit der Stelle, der die Domain gehört.

## Prüfen

```bash
curl -sI http://www.example.gov/ | head -3                        # a redirect to https
curl -sI https://www.example.gov/ | grep -i strict-transport
curl -sI https://www.example.gov/user/login | grep -i set-cookie   # Secure; HttpOnly; SameSite=Lax
```

Hinter einem Proxy, der TLS terminiert, muss der Kernel wissen, dass die Anfrage über HTTPS kam: Konfigurieren Sie
`TrustedProxies[]` (siehe „Proxys und vertrauenswürdige Header“), sonst meldet `CookieSecure=true` die Benutzer auf
jeder Seite ab.

## Verwandte Seiten

- [13.9 Sicherheits-Header und HTTPS](../../../installation/13-sicherheitshaertung.md#139-sicherheits-header-und-https)
- [HTTPS und Zertifikate in Velocity](../../../../eng-us/features/6.0/velocity-https-certificates.md)
- [Live gehen: Hosts und HTTPS](../../../../eng-us/help/creators/website-creators/going-live-hosts-https.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
