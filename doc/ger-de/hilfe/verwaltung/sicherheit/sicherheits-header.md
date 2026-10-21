---
title: Sicherheits-Header
audience: government, admin, hosting
category: public-sector
order: 160
summary: Für Administratorinnen und Administratoren öffentlicher Websites - die fünf Sicherheits-Header, die Exponential standardmäßig auf jeder Seite sendet, wie Sie sie prüfen, wie Sie einen sicher ändern und wie Velocity, Apache und nginx mit statischen Dateien umgehen.
keywords: Sicherheits-Header, Clickjacking, Einbetten in Frames, Referrer, statische Dateien, öffentlicher Sektor, security headers, X-Content-Type-Options, X-Frame-Options, Content-Security-Policy frame-ancestors, Referrer-Policy, Permissions-Policy, Strict-Transport-Security, HTTPHeaderSettings, SecurityHeaders, clickjacking, Velocity ResponseHeaders, public sector
type: help
---
# Sicherheits-Header

Diese Seite richtet sich an Administratorinnen und Administratoren sowie Hosting-Teams. HTTP-Sicherheits-Header weisen
Browser an, häufige Angriffe abzuwehren: das Erraten von Inhaltstypen, das Einbetten in Frames (Clickjacking), das
Preisgeben von Adressen im Referrer und den Zugriff auf Kamera oder Mikrofon. Exponential sendet fünf davon auf jeder
Seite, die Administration eingeschlossen
([13.9 Sicherheits-Header und HTTPS](../../../installation/13-sicherheitshaertung.md#139-sicherheits-header-und-https)).

## Inhalt

1. [Die Standardwerte](#die-standardwerte)
2. [Prüfen](#prüfen)
3. [Einen ändern](#einen-ändern)
4. [Statische Dateien](#statische-dateien)
5. [Verwandte Seiten](#verwandte-seiten)

## Die Standardwerte

`settings/site.ini [HTTPHeaderSettings] SecurityHeaders[]`:

| Header | Standard | Schützt vor |
|---|---|---|
| `X-Content-Type-Options` | `nosniff` | Browsern, die den Typ einer Datei erraten und sie ausführen |
| `X-Frame-Options` | `SAMEORIGIN` | dem Einbetten der Website in eine andere Website |
| `Content-Security-Policy` | `frame-ancestors 'self'` | demselben, in der modernen Form |
| `Referrer-Policy` | `strict-origin-when-cross-origin` | dem Abfließen vollständiger Adressen an andere Websites |
| `Permissions-Policy` | `camera=(), microphone=(), payment=(), usb=()` | Seiten, die Gerätefunktionen nutzen |
| `Strict-Transport-Security` | leer (aus; wird nur über HTTPS gesendet) | siehe „HTTPS und HSTS“ |

Seiten, die aus dem HTTP-Cache des Kernels beantwortet werden, tragen dieselben Header.

## Prüfen

```bash
curl -sI https://www.example.gov/ | grep -i -E 'x-content-type|x-frame|referrer|permissions|content-security|strict-transport'
curl -sI https://admin.example.gov/user/login | grep -i -E 'x-frame|content-security'
```

Erwartet: die fünf Zeilen der Tabelle (die Reihenfolge kann abweichen). Halten Sie die Ausgabe nach jeder Änderung als
Nachweis in Ihrer Sicherheitsdokumentation fest.

## Einen ändern

Verwenden Sie einen Override, nie `settings/site.ini` selbst. Lesen Sie zuerst den aktuellen Wert:

```bash
./console exp:ini get 'site.ini/HTTPHeaderSettings/SecurityHeaders[Content-Security-Policy]' --allow-root-user
```

Beispiel: einem vertrauenswürdigen Bearbeitungshost erlauben, die Administration einzubetten:

```ini
# settings/override/site.ini.append.php
[HTTPHeaderSettings]
SecurityHeaders[Content-Security-Policy]=frame-ancestors 'self' https://edit.example.gov
```

Ein leerer Wert in einem Override entfernt einen Header; Namen und Werte, die die Antwort aufspalten könnten, werden
ignoriert. Je Pfad kann `[HTTPHeaderSettings] HeaderList` weiterhin jeden Header überschreiben. Leeren Sie danach den
INI-Cache und laden Sie PHP neu.

## Statische Dateien

- **Velocity** sendet vier davon (alle außer der Content Security Policy) auf dem, was es selbst beantwortet (statische
  Dateien, skalierte Bilder, Fehlerseiten, Umleitungen), über `settings/velocity.ini [ServerSettings] ResponseHeaders[]`.
- **Apache und nginx** senden auf statischen Dateien von sich aus nichts zusätzlich; ergänzen Sie die Header mit
  `mod_headers` (`Header always set X-Content-Type-Options "nosniff"`) oder `add_header ... always;`, wenn Sie sie dort
  haben möchten.
- SVG-Dateien aus dem Speicher erhalten durch die mitgelieferte `.htaccess_root` einen Sandbox-Header, wenn
  `mod_headers` vorhanden ist.

## Verwandte Seiten

- [13.9 Sicherheits-Header und HTTPS](../../../installation/13-sicherheitshaertung.md#139-sicherheits-header-und-https)
- [Sicherheit und Audit, 1](../../../anleitungen/sicherheit-und-audit.md#1-die-bereits-aktive-härtung-prüfen-5-minuten)
- [TLS und HTTPS für Hoster](../../hosting/sicherheit/tls-und-https.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
