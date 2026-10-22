---
title: Sicherheitsvorgaben eines Bundles
audience: bundler, hosting
category: bundling
order: 380
summary: Der Sicherheitszustand, den ein Bundle übergeben sollte - was Exponential standardmäßig bereits tut, die Einstellungen, die ein Bundle entscheiden sollte (Dateimodi, Debug-Ausgabe, CheckValidity, Cookies, HTTPS und Header, vertrauenswürdige Proxys, Demo-Modus aus), was nie ausgeliefert werden darf, und die Go-live-Checkliste der Installationsanleitung, angewendet auf Bundles.
keywords: Sicherheit, Vorgaben, Härtung, Sicherheits-Header, Dateirechte, Demo-Modus, Audit, Go-live-Checkliste, security, defaults, hardening, bundle, CheckValidity, DebugOutput, CookieSecure, HTTPS, HSTS, security headers, TrustedProxies, file permissions, demo mode, ezformtoken, audit, go-live checklist
type: help
---
# Sicherheitsvorgaben eines Bundles

Diese Seite richtet sich an Bündler, deren Bundle sicher sein soll, sobald es eingeschaltet wird, noch bevor der
Empfänger etwas gelesen hat. Die Referenz ist
[Kapitel 13: Sicherheitshärtung für die Produktion](../../installation/13-sicherheitshaertung.md); die Übersicht für
Administratoren ist [Sicherheitshärtung](../sicherheitshaertung.md).

## Inhalt

1. [Was Exponential bereits tut](#was-exponential-bereits-tut)
2. [Was ein Bundle entscheiden sollte](#was-ein-bundle-entscheiden-sollte)
3. [Was nie ausgeliefert werden darf](#was-nie-ausgeliefert-werden-darf)
4. [Die Go-live-Checkliste für Bundles](#die-go-live-checkliste-für-bundles)
5. [Sicherheitsprobleme nach dem Release](#sicherheitsprobleme-nach-dem-release)

## Was Exponential bereits tut

Aus der Tabelle der Standardwerte in
[13.1 Die Schichten auf einen Blick](../../installation/13-sicherheitshaertung.md#131-die-schichten-im-überblick):

| Schicht | Standard |
|---|---|
| Rewrite-Regeln | strikte Liste, PHP unterhalb des Wurzelverzeichnisses abgelehnt |
| Geheimnisse | in `settings/override` erzeugt |
| Debug-Ausgabe | aus |
| Admin-Siteaccess | Anmeldung erforderlich |
| Passwörter | 10 Zeichen, bcrypt |
| Sitzungs-Cookie | `HttpOnly`, `SameSite=Lax`, `Secure` über HTTPS |
| Formular-Tokens | aktiv |
| Weitergeleitete Header | nur von `127.0.0.1` und `::1` geglaubt |
| Sicherheits-Header | fünf Header auf jeder Seite |
| Audit | an |

## Was ein Bundle entscheiden sollte

| Thema | Entscheidung | Wo |
|---|---|---|
| Dateimodi | Die ausgelieferten Modi in `[FileSettings]` sind `0777`/`0666`; `site.ini` empfiehlt `0770`/`0660` mit dem richtigen Benutzer und der richtigen Gruppe | [Dateirechte und Besitz von var/](dateirechte-und-besitz-von-var.md) |
| Besitzrechte | ein Benutzer (oder eine Gruppe) schreibt `var/` und `settings/`; Worker nie als root | dieselbe Seite |
| HTTPS | auf jedem Namen, HTTP umgeleitet; HSTS erhöht, sobald stabil | [13.9](../../installation/13-sicherheitshaertung.md#139-sicherheits-header-und-https) |
| Cookies | `[Session] CookieSecure=true` bei einem Bundle nur mit HTTPS | [13.7](../../installation/13-sicherheitshaertung.md#137-sitzungen-und-cookies) |
| Proxys | `TrustedProxies[]` nennt genau die Proxys, die Ihr Bundle vorschaltet | [13.10](../../installation/13-sicherheitshaertung.md#1310-hinter-einem-proxy-vertrauenswürdige-header) |
| Administration | ein eigener Host, wo das Bundle es zulässt (`exp:install --access=host --host ... --admin-host ...`) | [13.5](../../installation/13-sicherheitshaertung.md#135-der-administrations-siteaccess) |
| Einstellungen aus dem Browser | `[ConfigurationSettings] ManagedByDeployment=enabled` für Bundles, die per Bereitstellung verwaltet werden | [ManagedByDeployment](../../funktionen/6.0/durch-deployment-verwaltet.md) |
| Schwache Passwörter | Ihren Produktnamen zu `WeakPasswordList[]` hinzufügen | [Standardzugangsdaten](standardzugangsdaten-und-erste-anmeldung.md) |

## Was nie ausgeliefert werden darf

- Ein befülltes `settings/override/` oder `settings/siteaccess/` aus Ihrem Build
  ([Erster Start und einmalige Installation](erster-start-und-einmalige-installation.md#was-nicht-im-bundle-sein-darf)).
- Eine `kickstart.ini` mit Passwörtern; `var/log/initial-admin-password`; Protokolle Ihres Builds.
- `EXP_DEMO_MODE=1` in einer Unit, einem Pool, einem Image oder einer Crontab eines Bundles für echte Websites
  ([Der Demo-Modus muss aus sein](../../installation/13-sicherheitshaertung.md#der-demo-modus-muss-ausgeschaltet-sein)).
- `[DebugSettings] DebugOutput=enabled` oder PHP `display_errors=On`.
- Webserver-Regeln, die `settings/`, `var/`, `.git` oder `composer.json` ausliefern.
- Private Schlüssel oder Tokens in Erweiterungen: Exporte lehnen "eine Erweiterung, die einen geheimen Wert oder
  Schlüsselmaterial enthält" ab, und Ihr Build sollte das auch tun.

## Die Go-live-Checkliste für Bundles

Wenden Sie die [13.16 Go-live-Checkliste](../../installation/13-sicherheitshaertung.md#1316-checkliste-für-den-livegang) in Ihrer
Testumgebung auf das installierte Bundle an. Die Punkte, die ein Bundle vor dem Release klären kann:

- [ ] `/.git/config`, `/settings/site.ini`, `/composer.json` liefern die Datei nicht aus.
- [ ] `settings/override/` hat nach der Installation den Modus `0640` oder strenger.
- [ ] `[DebugSettings] DebugOutput=disabled`; `display_errors=Off`.
- [ ] `[SiteAccessSettings] CheckValidity=false` nach der Installation (der Installer schreibt es).
- [ ] Kein Hinweisbalken **Demo-Modus**.
- [ ] `ezformtoken` aktiv.
- [ ] Sicherheits-Header vorhanden.
- [ ] Ein Benutzer oder eine Gruppe schreibt `var/` und `settings/`; keine für alle beschreibbaren Dateien; Worker nie
      als root.

Der Rest (persönliche Konten, HTTPS-Zertifikate, Mail, Empfänger von Audit-Warnungen, die Update-Routine) gehört dem
Empfänger; führen Sie ihn in Ihrem Text für die ersten Schritte auf.

## Sicherheitsprobleme nach dem Release

- Eine Schwachstelle in Exponential: Melden Sie sie vertraulich an security@se7enx.com, "nicht im öffentlichen
  Issue-Tracker" (`SECURITY.md`); Korrekturen kommen im nächsten 6.0.x-Release.
- Ihr Bundle braucht dann ein Release mit dieser Version
  ([Updates und Upgrade-Pfade](updates-und-upgrade-pfade.md)).
- Partner: [Eskalationen und Sicherheitsprobleme](../partner/eskalationen-und-sicherheitsprobleme.md).

## Verwandte Seiten

- [Härtung für Hoster](../hosting/sicherheit/haertung-fuer-hoster.md)
- [Verantwortung für die Sicherheit](../partner/sicherheitspflichten.md)
- [Standardzugangsdaten und erste Anmeldung](standardzugangsdaten-und-erste-anmeldung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
