---
title: Sicherheits-Checkliste vor dem Go-live öffentlicher Websites
audience: government, admin, project-manager, hosting
category: public-sector
order: 60
summary: Eine druckbare Sicherheits-Checkliste für den Start einer öffentlichen Website mit Exponential, aufgebaut aus der Go-live-Checkliste und dem Härtungsleitfaden des Projekts, mit einer Spalte für die Nachweise, die eine öffentliche Stelle zu jedem Punkt ablegen sollte.
keywords: Go-live-Checkliste, Start, Inbetriebnahme, Sicherheits-Checkliste, Abnahme, Nachweise, Produktionsreife, Härtung, Freigabe, öffentlicher Sektor, Verwaltung, go-live checklist, launch, security checklist, acceptance, evidence, production readiness, hardening, sign-off, public sector, government
type: help
---
# Sicherheits-Checkliste vor dem Go-live öffentlicher Websites

Diese Seite richtet sich an die Administratorin oder den Administrator, die oder der einen Start vorbereitet, und an
die Projektverantwortlichen oder Sicherheitsbeauftragten, die ihn freigeben. Sie verbindet
[13.16 Go-live-Checkliste](../../../installation/13-sicherheitshaertung.md#1316-checkliste-für-den-livegang) und die
[Härtungs-Checkliste](../../sicherheitshaertung.md#checkliste) und ergänzt die Nachweise, die eine öffentliche Stelle
üblicherweise für ihre Sicherheitsdokumentation ablegt.

## Inhalt

1. [Vor dem Start](#vor-dem-start)
2. [Konten und Anmeldung](#konten-und-anmeldung)
3. [Webserver, TLS und Header](#webserver-tls-und-header)
4. [Protokollierung und Betrieb](#protokollierung-und-betrieb)
5. [Freigabe](#freigabe)
6. [Verwandte Seiten](#verwandte-seiten)

## Vor dem Start

| Punkt | Abzulegender Nachweis |
|---|---|
| Umschreiberegeln aktuell; `/.git/config`, `/settings/site.ini`, `/composer.json` liefern die Datei nicht aus | `curl`-Ausgabe der drei Anfragen (404 oder eine Seite, nie die Datei) |
| `settings/override/` nicht in der Versionsverwaltung, als Geheimnis gesichert, Modus `0640` oder strenger | `ls -l settings/override`, Sicherungsnachweis |
| `[SiteAccessSettings] CheckValidity=false`, `AllowWizardOnInstalledSite=disabled` | `./console exp:ini get site.ini/SiteAccessSettings/CheckValidity --allow-root-user` |
| `[DebugSettings] DebugOutput=disabled`; PHP `display_errors=Off` | Ausgabe der Einstellungen |
| `ezformtoken` aktiv | `./console exp:ini where site.ini/ExtensionSettings/ActiveExtensions --allow-root-user` |
| Dateikonsistenz geprüft | `bash bin/shell/verifyfiles.sh` mit Exit-Status 0 oder der Bericht der Upgrade-Prüfung |

## Konten und Anmeldung

| Punkt | Abzulegender Nachweis |
|---|---|
| Generiertes Administratorpasswort geändert; `var/log/initial-admin-password` und `kickstart.ini` entfernt | Datum und wer |
| Ein persönliches Administratorkonto je Person; `admin` als Anmeldung stillgelegt; Notfallverfahren getestet | Liste der Administratorinnen und Administratoren; Verfahrensdokument |
| Zweistufige Anmeldung mindestens für Administratorinnen und Administratoren verlangt | Einstellungen in `sevenxauthentication2fa.ini`; Liste der Registrierungen |
| Passwortrichtlinie festgelegt (`MinPasswordLength`, `[PasswordSettings]`), `EndOtherSessions=enabled` | Ausgabe der Einstellungen |
| Entscheidung zur Kontosperre festgehalten (`MaxNumberOfFailedLogin`) | Entscheidungsvermerk |
| Rollen überprüft: minimale Rechte, `audit/read` und `setup/setup` nur für diejenigen, die sie brauchen | Rollenexport |

## Webserver, TLS und Header

| Punkt | Abzulegender Nachweis |
|---|---|
| HTTPS auf jedem Namen; HTTP umgeleitet; Zertifikatserneuerung überwacht | `curl -sI http://...` zeigt eine Umleitung; Überwachungseintrag |
| `[Session] CookieSecure=true`; `HttpOnly` und `SameSite` aktiv | `curl -sI https://<host>/user/login` zeigt die Attribute |
| Sicherheits-Header vorhanden | Ausgabe von `curl -sI https://<host>/` |
| HSTS angehoben, sobald der Betrieb stabil ist | Header-Wert und Datum |
| Hinter einem Proxy: `TrustedProxies[]` genau Ihre Proxys | Ausgabe der Einstellungen und die Prüfung aus 13.10 |
| Die Administration auf eigenem Host, eingeschränkt per Netzwerk oder zweiter Authentifizierung | Auszug aus der Webserver-Konfiguration |
| Content Security Policy in der Administration durchgesetzt, auf Websites Report-only | `Mode` je Siteaccess |

## Protokollierung und Betrieb

| Punkt | Abzulegender Nachweis |
|---|---|
| `exp:audit verify` intakt; Cronjob-Gruppe `frequent` läuft | Ausgabe des Befehls; Crontab |
| Empfänger der Alarme geprüft | `./console exp:audit alerts recipients --allow-root-user` |
| Audit-Schlüssel gesichert; Weiterleitung an ein SIEM konfiguriert | Sicherungsnachweis; Ausgabe des Zieltests |
| E-Mail: `exp:mail:status` in Ordnung, Fußzeile ausgefüllt, Bounce-Leser konfiguriert | Ausgabe des Befehls |
| Ein Benutzer schreibt `var/` und `settings/`; keine für alle beschreibbaren Dateien; Worker nie als root | `find settings extension/*/settings -perm -o+w` gibt nichts aus |
| request-shield vorgeschaltet (erst beobachten, dann durchsetzen) | Regeldateien, Modus |
| Sicherungen außerhalb des Rechners; Wiederherstellung getestet | Nachweis des Wiederherstellungstests |
| Aktualisierungsroutine: wer Releases beobachtet, wer Sicherheits-E-Mails erhält | benannte Verantwortliche |

## Freigabe

Halten Sie Datum, Version (`composer show se7enxweb/exponential`), die prüfenden Personen und offene Punkte mit
Verantwortlichen und Termin fest. Bewahren Sie die Checkliste mit den Nachweisen auf; sie ist die Grundlage für spätere
Audits.

## Verwandte Seiten

- [13.16 Go-live-Checkliste](../../../installation/13-sicherheitshaertung.md#1316-checkliste-für-den-livegang)
- [Sicherheitshärtung](../../sicherheitshaertung.md)
- [Start-Checkliste für Websites](../../../../eng-us/help/creators/website-creators/launch-checklist.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
