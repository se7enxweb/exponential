---
title: Die Sicherheitsheader prüfen
audience: government, admin, hosting
category: public-sector
order: 130
summary: Schritt für Schritt - von außen prüfen, dass jeder Siteaccess von Exponential die erwarteten Sicherheitsheader und Cookie-Flags sendet, dass interne Dateien nicht ausgeliefert werden, und das Ergebnis als Nachweis für ein Audit festhalten.
keywords: Sicherheitsheader, curl, X-Content-Type-Options, X-Frame-Options, Referrer-Policy, Permissions-Policy, Content-Security-Policy, Strict-Transport-Security, Set-Cookie-Flags, Nachweis, monatliche Prüfung, öffentlicher Sektor, security headers, Set-Cookie flags, evidence, monthly check, public sector
type: help
---
# Die Sicherheitsheader prüfen

Für Administratoren und Hosting-Teams, nach einer Änderung am Webserver, einem Deployment oder monatlich als Nachweis.
Die Befehle stammen aus [Sicherheit und Audit, 1](../../../../anleitungen/sicherheit-und-audit.md#1-die-bereits-aktive-härtung-prüfen-5-minuten)
und [13.9](../../../../installation/13-sicherheitshaertung.md#139-sicherheits-header-und-https).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Was zu erwarten ist](#was-zu-erwarten-ist)
4. [Das Ergebnis festhalten](#das-ergebnis-festhalten)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Eine Shell mit `curl`, nach Möglichkeit außerhalb des Netzwerks des Servers (eine Prüfung von innen kann einen Proxy
  umgehen).
- Die Liste der Hostnamen: öffentliche Siteaccesses und die Administration.

## Schritte

```bash
for host in www.example.gov admin.example.gov; do
  echo "== $host"
  curl -sI "https://$host/" | grep -i -E 'x-content-type|x-frame|referrer|permissions|content-security|strict-transport'
  curl -sI "https://$host/user/login" | grep -i set-cookie
done
for p in .env .git/config settings/site.ini composer.json extension/expservices/ezinfo.php; do
  curl -s -o /dev/null -w "$p %{http_code}\n" "https://www.example.gov/$p"
done
```

## Was zu erwarten ist

| Prüfung | Erwartet |
|---|---|
| `x-content-type-options` | `nosniff` |
| `x-frame-options` | `SAMEORIGIN` |
| `content-security-policy` | `frame-ancestors 'self'`; in der Administration zusätzlich eine Richtlinie oder eine Richtlinie im Modus report-only |
| `referrer-policy` | `strict-origin-when-cross-origin` |
| `permissions-policy` | `camera=(), microphone=(), payment=(), usb=()` |
| `strict-transport-security` | Ihr Wert, sobald gesetzt |
| Sitzungscookie | `Secure; HttpOnly; SameSite=Lax` |
| interne Dateien | `404` oder eine Seite von Exponential, niemals der Inhalt der Datei |

Ein fehlender Header nach einer Änderung bedeutet meist, dass ein Override die Liste ersetzt hat oder ein Proxy Header
entfernt.

## Das Ergebnis festhalten

Speichern Sie die Ausgabe mit Datum und Host in Ihrer Sicherheitsdokumentation. Vergleichen Sie mit dem Vormonat; jeder
Unterschied braucht eine Erklärung.

## Verwandte Seiten

- [Sicherheitsheader](../../../../../eng-us/help/government/security/security-headers.md)
- [HSTS hinzufügen](hsts-hinzufuegen.md)
- [Sicherheits-Checkliste für den Go-live](../../../../../eng-us/help/government/security/go-live-security-checklist.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
