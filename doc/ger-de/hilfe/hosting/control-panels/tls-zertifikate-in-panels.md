---
title: TLS-Zertifikate in Panels
audience: hosting, admin
category: hosting
order: 100
summary: Wie HTTPS für eine Exponential-Website auf einem Server mit Panel funktioniert - das Zertifikat des Panels für Apache oder nginx, was Exponential selbst mitgeteilt werden muss (URLs der Website, sichere Cookies, HSTS, vertrauenswürdige Proxys), und wie Velocity oder FrankenPHP neben dem Panel das Zertifikat des Panels oder ein eigenes nutzen können, einschließlich der Falle bei der Erneuerung.
keywords: TLS, HTTPS, Zertifikat, Let's Encrypt, ACME, Panel, Plesk, Umleitung, HSTS, Strict-Transport-Security, CookieSecure, SiteURL, SSLZoneSettings, vertrauenswürdige Proxys, X-Forwarded-Proto, Velocity, FrankenPHP, Erneuerung, exp:velocity ssl show, certificate, panel, redirect, trusted proxies, renewal
type: help
---
# TLS-Zertifikate in Panels

Diese Seite ist für Administratoren und Support-Mitarbeitende bei Hosting-Anbietern, die HTTPS für Exponential-Websites auf Servern
mit Panel einrichten. Sie beantwortet, wer TLS beendet (der Webserver des Panels, Velocity oder FrankenPHP), was Exponential
mitgeteilt werden muss, sobald eine Website auf HTTPS ist, und wie Sie das Zertifikat eines Panels mit Velocity oder FrankenPHP
teilen, ohne nach der nächsten Erneuerung ein abgelaufenes auszuliefern.

## Inhalt

1. [Wer TLS beendet](#wer-tls-beendet)
2. [Das Zertifikat des Panels für Apache oder nginx](#das-zertifikat-des-panels-für-apache-oder-nginx)
3. [Was Exponential mitgeteilt werden muss](#was-exponential-mitgeteilt-werden-muss)
4. [Velocity oder FrankenPHP neben dem Panel](#velocity-oder-frankenphp-neben-dem-panel)
5. [Den Ablauf beobachten](#den-ablauf-beobachten)
6. [Checkliste](#checkliste)

## Wer TLS beendet

| Einrichtung | TLS endet in | Das Zertifikat kommt von |
|---|---|---|
| Apache (oder nginx) des Panels mit PHP-FPM | dem Webserver des Panels | der Zertifikatsverwaltung des Panels (`mod_ssl` plus certbot oder ein Hosting-Panel) |
| Das nginx des Panels vor Apache (wie bei Plesk) | dem vorderen nginx; nginx spricht über HTTPS mit Apache | dem Panel |
| Velocity zu den Besuchern hin | dem eigenen Prozess von Velocity | Ihren Dateien, einem selbstsignierten Ersatz oder ACME (Let's Encrypt und andere CAs) |
| FrankenPHP über Velocity | FrankenPHP | Ihrem Zertifikat oder einem selbstsignierten; Velocity steuert dafür kein ACME |
| Velocity oder FrankenPHP hinter dem Webserver des Panels | dem Webserver des Panels | dem Panel; dem Proxy muss vertraut werden |

Quellen: [8.1](../../../installation/08-die-website-ausliefern.md#81-die-wahl-in-einer-tabelle),
[Vertrauenswürdige Proxys](../../../bc/6.0/vertrauenswuerdige-proxys.md), [FrankenPHP](../../../bc/6.0/frankenphp.md).

## Das Zertifikat des Panels für Apache oder nginx

Für einen vom Panel verwalteten Webserver stellen Sie das Zertifikat im Panel aus oder laden es hoch und schalten die Umleitung von
HTTP auf HTTPS ein. Für Plesk sagen die Quellen genau das: Installieren Sie das Zertifikat in Plesk (seine Let's-Encrypt-Erweiterung
oder Ihr eigenes), und schalten Sie die Umleitung auf HTTPS ein ([8.5.3](../../../installation/08-die-website-ausliefern.md#853-plesk)). Für andere
Panels lesen Sie in der Dokumentation Ihres Panels nach, wo Zertifikate ausgestellt und erneuert werden.

ACME-Challenges nach HTTP-01 brauchen ein erreichbares `/.well-known/`. Die mitgelieferten Rewrite-Regeln halten es so: Dateien und
Verzeichnisse mit einem Punkt am Anfang antworten mit 404, mit `.well-known` als Ausnahme
([13.2](../../../installation/13-sicherheitshaertung.md#132-was-der-webserver-niemals-ausliefern-darf)).

## Was Exponential mitgeteilt werden muss

Ein Zertifikat allein macht eine Exponential-Website nicht zu „überall HTTPS“. Aus
[13.9](../../../installation/13-sicherheitshaertung.md#139-sicherheits-header-und-https):

1. Liefern Sie jeden Namen der Website über HTTPS aus.
2. Leiten Sie reines HTTP auf HTTPS um. Für einzelne Teilbäume hat Exponential eigene `[SSLZoneSettings]`.
3. Setzen Sie `[Session] CookieSecure=true`.
4. Schalten Sie HSTS ein, zuerst kurz, und erhöhen Sie es, wenn alles funktioniert: unter Apache oder nginx mit
   `SecurityHeaders[Strict-Transport-Security]=max-age=31536000` in `site.ini`, vom Kernel nur über HTTPS gesendet; unter Velocity
   mit `[HTTPSSettings] HSTSMaxAge` (300 mitgeliefert).
5. Halten Sie Zertifikate erneuert und beobachtet.

Stellen Sie sicher, dass die Adressen, die die Website erzeugt, HTTPS-Adressen sind (`site.ini [SiteSettings] SiteURL` und die URLs
der Siteaccesses), bevor Sie HSTS erhöhen ([8.3.9](../../../installation/08-die-website-ausliefern.md#839-https-direkt-von-velocity)).

> **Warnung**
> Ein Browser merkt sich HSTS für den ganzen Zeitraum und verweigert reines HTTP zu diesem Host, auch nachdem Sie den Wert gesenkt
> haben. Erhöhen Sie ihn erst auf ein Jahr, wenn jeder Name dauerhaft über HTTPS ausgeliefert wird.

**Hinter einem Proxy.** Wenn TLS vor der Anwendung endet (das nginx eines Panels vor Velocity oder PHP-FPM, ein Load Balancer),
entscheidet Exponential anhand der Header des Proxys, ob die Anfrage HTTPS war, und nur bei vertrauenswürdigen Proxys. Ein Proxy auf
derselben Maschine, der die Header setzt, braucht nichts: Die Standardliste `TrustedProxies[]` deckt `127.0.0.1` und `::1` ab; lassen
Sie den Proxy `X-Forwarded-Proto` selbst setzen. Auch die Einrichtung von Plesk mit nginx vor Apache braucht nichts, weil
`mod_remoteip` von Apache den Besucher zur Gegenstelle macht und Apache `HTTPS=on` setzt
([13.10](../../../installation/13-sicherheitshaertung.md#1310-hinter-einem-proxy-vertrauenswürdige-header)).

```bash
curl -sI https://example.com/ | grep -i -E 'strict-transport|x-content-type|x-frame|referrer|permissions|content-security'
curl -sI https://example.com/user/login | grep -i set-cookie     # Secure; HttpOnly; SameSite=Lax
```

## Velocity oder FrankenPHP neben dem Panel

Wenn Velocity dieselbe Website auf einem eigenen Port neben dem Webserver des Panels ausliefert, beendet es TLS selbst und braucht ein
eigenes Zertifikat. Drei Wege:

| Weg | Wie | Erneuerung |
|---|---|---|
| ACME von Velocity | ein Snippet `https-acme` in `/etc/vc/conf-available/`, aktiviert mit `exp:velocity conf enable` | Velocity erneuert, wenn ein Drittel der Laufzeit übrig ist |
| Eine Kopie des Zertifikats des Panels | `[HTTPSSettings] Certificate` und `Key` zeigen auf die kopierten Dateien, `Enabled=true` | die Kopie nach jeder Erneuerung durch das Panel wiederholen |
| Selbstsigniert | nichts konfiguriert | nur für Entwicklung und Tests |

ACME von Velocity braucht einen erreichbaren Port 80 des Servers für die Challenge HTTP-01. Auf einem Server mit Panel gehört Port 80
meist dem Webserver des Panels; die Quellen nennen für diesen Fall `webroot` (wenn ein anderer Server Port 80 belegt) und die
Challenge `dns-01` als Möglichkeiten ([8.3.9](../../../installation/08-die-website-ausliefern.md#839-https-direkt-von-velocity),
[HTTPS und Zertifikate in Velocity](../../../funktionen/6.0/velocity-https-zertifikate.md)).

**Die Falle bei der Erneuerung.** Die Testinstallation des Projekts teilt das Let's-Encrypt-Zertifikat, das Plesk für den Host hält, mit
FrankenPHP und Velocity. Plesk erneuert das Let's-Encrypt-Zertifikat nach eigenem Zeitplan und schreibt dabei eine **neue Datei unter
einem neuen Namen**; die exportierte Kopie veraltet dann. Der Export muss nach jeder Erneuerung wiederholt werden, gefolgt von einem
Neuladen ([FrankenPHP, Erneuerung](../../../bc/6.0/frankenphp.md)):

```bash
# after copying the renewed certificate and key into place:
php bin/php/console exp:velocity graceful --engine=frankenphp --allow-root-user
```

Velocity selbst prüft seine Zertifikatsdateien alle `watchInterval` Sekunden (60) und tauscht ein geändertes, gültiges Paar live aus;
für Velocity genügt es also, die neuen Dateien an ihren Platz zu kopieren. Ein Paar, das scheitert, wird nie einem Besucher gezeigt; ein
selbstsigniertes Zertifikat springt ein, bis das echte wieder nutzbar ist.

> **Tipp**
> Automatisieren Sie die Kopie mit dem Erneuerungs-Hook des Panels, wenn es einen hat (lesen Sie in der Dokumentation Ihres Panels
> nach), oder mit einem täglichen Job, der das aktuelle Zertifikat des Panels mit der Kopie vergleicht. Beobachten Sie dann den Ablauf
> wie unten, damit eine kaputte Kopie bemerkt wird, bevor Besucher den selbstsignierten Ersatz sehen.

## Den Ablauf beobachten

```bash
php bin/php/console exp:velocity ssl show --allow-root-user          # served certificate, every known one by expiry
php bin/php/console exp:velocity ssl show --json --allow-root-user   # for monitoring: watch daysLeft
php bin/php/console exp:velocity ssl renew --apply example.com --allow-root-user
```

Ein CA-Zertifikat mit 90 Tagen Laufzeit wird erneuert, wenn etwa 30 Tage übrig sind; eines mit weniger als 14 verbleibenden Tagen
bedeutet also, dass die Erneuerung seit mehr als zwei Wochen scheitert. Lassen Sie NTP laufen: Zertifikate werden gegen die Uhr des
Systems geprüft. Für Zertifikate, die das Panel ausliefert, überwachen Sie den öffentlichen Endpunkt mit Ihren üblichen Werkzeugen.
Siehe [Ablauf von Zertifikaten](../runbooks/ablauf-von-zertifikaten.md).

## Checkliste

- [ ] Jeder Name jeder Website hat ein CA-Zertifikat; HTTP leitet auf HTTPS um.
- [ ] `CookieSecure=true`; die URLs der Website sind HTTPS.
- [ ] HSTS kurz begonnen, erst auf ein Jahr erhöht, wenn alles HTTPS ist.
- [ ] Vorgeschalteten Proxys wird vertraut (oder sie schreiben `REMOTE_ADDR` selbst um, wie das vordere nginx von Plesk).
- [ ] Velocity oder FrankenPHP neben dem Panel: Weg für das Zertifikat gewählt; Kopien nach jeder Erneuerung durch das Panel aufgefrischt.
- [ ] Ablauf überwacht (`exp:velocity ssl show --json` oder der öffentliche Endpunkt).

## Verwandte Seiten

- [Control Panels: Überblick](README.md), [Plesk](plesk.md)
- [TLS und HTTPS](../sicherheit/tls-und-https.md), [HTTPS-Zertifikate in Velocity](../server/https-zertifikate-in-velocity.md)
- [Kapitel 13: Sicherheitshärtung](../../../installation/13-sicherheitshaertung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
