---
title: Die Sicherheitsheader prüfen
audience: government, admin, hosting
category: public-sector
order: 310
summary: Schritt für Schritt - prüfen, dass jede Seite einer öffentlichen Website die fünf Sicherheitsheader trägt, die Exponential standardmäßig sendet, dass Punkt-Dateien und PHP-Dateien unter der Wurzel mit 404 antworten und dass das Sitzungscookie die richtigen Flags hat; einen Header bei Bedarf gezielt ändern.
keywords: Sicherheitsheader, X-Content-Type-Options, X-Frame-Options, Content-Security-Policy, Referrer-Policy, Permissions-Policy, HSTS, Cookies, HttpOnly, SameSite, Secure, curl, Prüfung der Härtung, security headers, cookies, hardening check
type: help
---
# Die Sicherheitsheader prüfen

Diese Aufgabe ist für Administratoren und Sicherheitsbeauftragte, beim Go-live, nach Upgrades und bei regelmäßigen
Sicherheitsüberprüfungen. Seit September 2026 sendet eine Standardinstallation diese Header; prüfen Sie, statt zu
vertrauen ([Sicherheit und Audit, Schritt 1](../../../../anleitungen/sicherheit-und-audit.md#1-die-bereits-aktive-härtung-prüfen-5-minuten)).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Einen Header ändern](#einen-header-ändern)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- `curl` auf einer beliebigen Maschine, die die Site erreicht; ein Browser für die Prüfung der Cookies.

## Schritte

1. Header auf der öffentlichen Site und in der Administration:

   ```bash
   curl -sI https://www.example.gov/ | grep -i -E 'x-content-type|x-frame|referrer|permissions|content-security'
   curl -sI https://www.example.gov/admin/user/login | grep -i -E 'x-content-type|x-frame|referrer|permissions|content-security'
   ```

   Erwartet: `x-content-type-options: nosniff`, `x-frame-options: SAMEORIGIN`, `content-security-policy:
   frame-ancestors 'self'`, `referrer-policy: strict-origin-when-cross-origin`, `permissions-policy: camera=(),
   microphone=(), payment=(), usb=()`. Die Administration sendet zusätzlich eine Content Security Policy im Modus
   report-only.
2. Dateien, die nicht ausgeliefert werden dürfen:

   ```bash
   for p in .env .git/config extension/expservices/ezinfo.php; do
     curl -s -o /dev/null -w "$p %{http_code}\n" https://www.example.gov/$p
   done
   ```

   Erwartet: dreimal `404`.
3. Das Sitzungscookie: Melden Sie sich an, öffnen Sie die Entwicklerwerkzeuge des Browsers, Cookies: `HttpOnly`,
   `SameSite=Lax` und unter HTTPS `Secure`.

## Einen Header ändern

Lesen Sie zuerst den Wert und ändern Sie ihn dann im Override-Bereich über Ihren Änderungsprozess:

```bash
./console exp:ini get 'site.ini/HTTPHeaderSettings/SecurityHeaders[Content-Security-Policy]' --allow-root-user
```

Schalten Sie HSTS erst ein, wenn jeder Name der Site dauerhaft HTTPS ist:
`SecurityHeaders[Strict-Transport-Security]=max-age=31536000` (unter Velocity `[HTTPSSettings] HSTSMaxAge`).

Halten Sie die Ausgabe der Prüfungen mit Ihrer Sicherheitsüberprüfung fest.

## Verwandte Seiten
- [Den Host härten](../../betrieb/hosting/den-host-haerten.md)
- [CSP-Berichte](../../betrieb/ueberwachung/csp-berichte.md)
- [Sicherheitsstandard](../../../../standards/sicherheit.md)
- [Checkliste zur Sicherheitsüberprüfung (Hosting)](../../../hosting/checklisten/checkliste-fuer-die-sicherheitspruefung.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
