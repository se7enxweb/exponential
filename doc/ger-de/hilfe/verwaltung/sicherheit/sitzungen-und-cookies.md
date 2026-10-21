---
title: Sitzungen und Cookies
audience: government, admin
category: public-sector
order: 200
summary: Für Administratorinnen und Administratoren öffentlicher Websites - die Attribute des Sitzungs-Cookies, die Exponential standardmäßig setzt, empfohlene Einstellungen für eine reine HTTPS-Website, getrennte Cookies je Siteaccess, Zeitlimits für Sitzungen und wie Sie Benutzer nach einer vermuteten Kompromittierung abmelden.
keywords: Sitzungen, Sitzungs-Cookie, Zeitlimit, Sitzungsübernahme, Abmelden, öffentlicher Sektor, sessions, session cookie, CookieSecure, CookieHttponly, CookieSameSite, SessionTimeout, SessionNameHandler, session hijacking, session.use_strict_mode, ezpSessionHandlerDB, Setup Sessions, sign out, password stamp, public sector
type: help
---
# Sitzungen und Cookies

Diese Seite richtet sich an Administratorinnen und Administratoren. Ein gestohlenes Sitzungs-Cookie ist so viel wert
wie das Passwort. Exponential setzt standardmäßig sichere Cookie-Attribute und meldet bei einer Passwortänderung andere
Sitzungen ab; einige Einstellungen machen es strenger
([13.7 Sitzungen und Cookies](../../../installation/13-sicherheitshaertung.md#137-sessions-und-cookies)).

## Inhalt

1. [Die Standardwerte](#die-standardwerte)
2. [Empfohlen für eine reine HTTPS-Website](#empfohlen-für-eine-reine-https-website)
3. [Getrennte Cookies für die Administration](#getrennte-cookies-für-die-administration)
4. [Benutzer abmelden](#benutzer-abmelden)
5. [Fallstricke](#fallstricke)
6. [Verwandte Seiten](#verwandte-seiten)

## Die Standardwerte

`settings/site.ini [Session]` (die Cookie-Schlüssel sind auskommentiert; es gelten ihre Standardwerte):

| Schlüssel | Standard | Bedeutung |
|---|---|---|
| `CookieSecure` | `auto` | `Secure`, wenn die Anfrage über HTTPS kam |
| `CookieHttponly` | `true` | Skripte können das Cookie nicht lesen |
| `CookieSameSite` | `Lax` | wird bei den meisten websiteübergreifenden Anfragen nicht mitgesendet |
| `SessionTimeout` | `259200` | Sekunden (3 Tage) |
| `SessionNameHandler` | `default` | `custom` gibt jedem Siteaccess ein eigenes Cookie |

Außerdem: eine neue Sitzungs-ID bei der Anmeldung; das Abmelden beendet die Sitzung endgültig; eine Passwortänderung
meldet alle anderen Sitzungen des Benutzers ab (`EndOtherSessions=enabled`); die Sitzungen eines deaktivierten Kontos
enden bei ihrer nächsten Anfrage.

## Empfohlen für eine reine HTTPS-Website

```ini
# settings/override/site.ini.append.php
[Session]
CookieSecure=true
CookieHttponly=true
CookieSameSite=Lax
SessionTimeout=28800
```

und in der Konfiguration von PHP `session.use_strict_mode=1`. Wählen Sie das Zeitlimit gemeinsam mit Ihrer Redaktion:
Ein kurzes begrenzt, wie lange eine gestohlene Sitzung nützlich ist, meldet aber Menschen ab, die langsam arbeiten.

## Getrennte Cookies für die Administration

```ini
[Session]
SessionNameHandler=custom
SessionNamePrefix=eZSESSID
SessionNamePerSiteAccess=enabled
```

Eine Anmeldung auf der öffentlichen Website (zum Beispiel in einem Mitgliederbereich) ist dann keine Anmeldung in der
Administration.

## Benutzer abmelden

- **Nach einer vermuteten Kompromittierung eines Kontos**: Ändern Sie sein Passwort; jede andere Sitzung endet bei ihrer
  nächsten Anfrage. Deaktivieren Sie das Konto bei Bedarf (Benutzer > Benutzerkonten prüfen, **Deaktivieren**).
- **Alle**: Mit dem Datenbank-Session-Handler (`[Session] Handler=ezpSessionHandlerDB`) listet und entfernt
  **Setup > Sitzungen** Sitzungen, mit einer Bestätigung ([Sitzungen](../../../anleitungen/sitzungen.md)). Mit dem
  Standard-Handler kann die Seite Sitzungen nicht einzeln entfernen; ein Wechsel des Handlers meldet einmal alle ab.
- Das Entfernen einer Sitzung sperrt den Benutzer nicht; deaktivieren Sie zusätzlich das Konto oder ändern Sie das
  Passwort.

## Fallstricke

- `CookieSecure=true` hinter einem HTTPS-Proxy, dem nicht vertraut wird: Benutzer werden auf jeder Seite abgemeldet.
  Konfigurieren Sie `TrustedProxies[]` (siehe „Proxys und vertrauenswürdige Header“).
- `CookieSameSite=Strict` verliert die Sitzung beim ersten Klick aus einer E-Mail auf eine Seite, die eine Anmeldung
  voraussetzt.
- `CookieSameSite=None` wird nur zusammen mit `Secure` beachtet.

## Verwandte Seiten

- [13.7 Sitzungen und Cookies](../../../installation/13-sicherheitshaertung.md#137-sessions-und-cookies)
- [Sitzungen: wer angemeldet ist](../../../anleitungen/sitzungen.md)
- [Sicherheitshärtung: Sitzungsübernahme](../../sicherheitshaertung.md#sitzungsübernahme)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
