---
title: Sichere Sitzungscookies setzen
audience: government, admin
category: public-sector
order: 120
summary: Schritt für Schritt - auf einer reinen HTTPS-Site das Flag Secure am Sitzungscookie von Exponential erzwingen, HttpOnly und SameSite=Lax beibehalten, mit Ihren Redakteuren eine Sitzungsdauer wählen und die Flags mit curl prüfen.
keywords: Sitzungscookie, CookieSecure, CookieHttponly, CookieSameSite, SessionTimeout, Flag Secure, HTTPS, session.use_strict_mode, Set-Cookie, öffentlicher Sektor, session cookie, Secure flag, public sector
type: help
---
# Sichere Sitzungscookies setzen

Für Administratoren reiner HTTPS-Sites. Standardmäßig ist das Sitzungscookie nur dann `Secure`, wenn die Anfrage selbst
über HTTPS kam (`CookieSecure=auto`); `true` erzwingt es, sodass das Cookie nie über einfaches HTTP gesendet wird
([13.7 Sitzungen und Cookies](../../../../installation/13-sicherheitshaertung.md#137-sessions-und-cookies)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Wenn etwas schiefgeht](#wenn-etwas-schiefgeht)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Die Site wird nur über HTTPS ausgeliefert; hinter einem Proxy listet `TrustedProxies[]` ihn, und er setzt
  `X-Forwarded-Proto`.
- Vereinbaren Sie mit den Redakteuren eine Sitzungsdauer (mitgeliefert: 259200 Sekunden, drei Tage).

## Schritte

1. In `settings/override/site.ini.append.php`:

   ```ini
   [Session]
   CookieSecure=true
   CookieHttponly=true
   CookieSameSite=Lax
   SessionTimeout=28800
   ```

2. In der PHP-Konfiguration der Site `session.use_strict_mode=1`.
3. `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`; laden Sie PHP-FPM neu oder rollen Sie auf Velocity aus.

## Prüfen, ob es funktioniert hat

```bash
curl -sI https://www.example.gov/user/login | grep -i set-cookie
```

zeigt `Secure; HttpOnly; SameSite=Lax` am Sitzungscookie. Melden Sie sich an und arbeiten Sie eine Weile: Sie bleiben
angemeldet.

## Wenn etwas schiefgeht

- **Auf jeder Seite abgemeldet**: Der Kernel hält die Anfragen für einfaches HTTP (Proxy nicht vertrauenswürdig oder
  kein `X-Forwarded-Proto`); korrigieren Sie die Einstellungen des Proxys
  ([Proxys und vertrauenswürdige Header](../../../../../eng-us/help/government/security/proxies-and-trusted-headers.md)).
- `SameSite=Strict` verliert die Sitzung beim ersten Klick aus einer E-Mail; behalten Sie `Lax`, sofern Sie nicht
  wissen, dass Sie `Strict` brauchen.

## Verwandte Seiten

- [Sitzungen und Cookies](../../../../../eng-us/help/government/security/sessions-and-cookies.md)
- [Getrennte Sitzungscookies für die Administration](getrennte-sitzungscookies-fuer-die-administration.md)
- [HSTS hinzufügen](hsts-hinzufuegen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
