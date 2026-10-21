---
title: Die OWASP Top 10 auf Exponential abgebildet
audience: government, admin, developer, project-manager
category: public-sector
order: 30
summary: Eine Betrachtung der OWASP Top 10 (2021) Kategorie für Kategorie für öffentliche Stellen - die Gegenmaßnahmen, die Exponential mitliefert, der Anteil des Betreibers und die Nachweise für eine Prüferin oder einen Prüfer, auf Grundlage der Sicherheitserklärung des Projekts.
keywords: OWASP Top 10, fehlerhafte Zugriffskontrolle, Injection, XSS, Fehlkonfiguration, verwundbare Komponenten, Authentifizierung, Integrität, Protokollierung, SSRF, Zuordnung, Prüfnachweise, A01, A02, A03, A04, A05, A06, A07, A08, A09, A10, broken access control, injection, misconfiguration, vulnerable components, authentication, integrity, logging, mapping, audit evidence
type: reference
---
# Die OWASP Top 10 auf Exponential abgebildet

Diese Seite richtet sich an Informationssicherheitsbeauftragte, Prüferinnen und Prüfer sowie Administratorinnen und
Administratoren, die die OWASP Top 10 (<https://owasp.org/Top10/>) als Checkliste verwenden. Für jede Kategorie
wiederholt sie, was Exponential mitliefert, aus
[Sicherheitsstandards: OWASP Top 10](../../../../eng-us/standards/security.md#owasp-top-10-2021), und ergänzt, was der Betreiber
einer öffentlichen Website tun muss und welche Nachweise er aufbewahrt. Das Projekt bewertet das Ganze mit
**Teilweise erfüllt**.

## Inhalt

1. [Die Zuordnung](#die-zuordnung)
2. [Aufzubewahrende Nachweise](#aufzubewahrende-nachweise)
3. [Verwandte Seiten](#verwandte-seiten)

## Die Zuordnung

| ID | Kategorie | Exponential liefert mit | Ihr Anteil |
|---|---|---|---|
| A01 | Broken Access Control (fehlerhafte Zugriffskontrolle) | Rollen und Richtlinien für jede Modulansicht geprüft; Lesezugriff auf Inhalte je Knoten; Downloads nur über `content/download`; PHP unterhalb des Wurzelverzeichnisses abgewiesen; sichere Umleitungen; Einrichtungsassistent nach der Installation gesperrt | Rollen mit minimalen Rechten, ein Konto je Person, Anfrageregeln für System-URLs, Rollen jährlich überprüfen |
| A02 | Cryptographic Failures (kryptografische Fehler) | `password_hash()` (bcrypt), Aktualisierung alter Hashes; Tokens aus `random_bytes()`; `Secure`-Cookies über HTTPS; HSTS verfügbar | HTTPS auf jedem Namen, `CookieSecure=true`, HSTS anheben, sobald der Betrieb stabil ist |
| A03 | Injection | Maskierung mit `wash` in Templates; SQL-Maskierung und Ganzzahl-Umwandlungen überprüft; `escapeshellarg()`; CRLF aus Headern entfernt | eigene Erweiterungen auf dieselbe Disziplin prüfen; request-shield vorschalten |
| A04 | Insecure Design (unsicheres Design) | sichere Standardwerte seit September 2026; Anmeldung schlägt im Zweifel geschlossen fehl; `ManagedByDeployment` | `ManagedByDeployment` einschalten, wo die Konfiguration aus der Versionsverwaltung kommt |
| A05 | Security Misconfiguration (Sicherheitsfehlkonfiguration) | Sicherheits-Header; Debug auf gelistete Adressen beschränkt; Dateikonsistenz unter **Setup > Upgrade-Prüfung** | Debug aus lassen; Dateikonsistenz nach Updates prüfen; Go-live-Checkliste |
| A06 | Vulnerable and Outdated Components (verwundbare und veraltete Komponenten) | jQuery 4, YUI entfernt; unterstützte Versionen in `SECURITY.md`; `composer audit` empfohlen | Patch-Rhythmus; `composer audit`; Releases der Erweiterungen verfolgen (kein Abhängigkeitsscan in der CI des Projekts) |
| A07 | Identification and Authentication Failures (Fehler bei Identifizierung und Authentifizierung) | Mindestlänge 10, Prüfung auf schwache Passwörter, einheitliche Antworten, neue Sitzungs-ID bei der Anmeldung, andere Sitzungen bei Passwortänderung beendet; optionale Kontosperre; optionale zweistufige Anmeldung | zweistufige Anmeldung verlangen, Mindestlänge anheben, über die Kontosperre entscheiden |
| A08 | Software and Data Integrity Failures (Fehler bei der Integrität von Software und Daten) | unveränderliche Release-Tags; `share/filelist.md5` in der CI und in der Administration geprüft; CSRF-Tokens; SVG-Bereiniger; hash-verkettetes Audit | Dateien nach Updates prüfen; `ezformtoken` aktiv lassen |
| A09 | Security Logging and Monitoring Failures (Fehler bei Sicherheitsprotokollierung und Überwachung) | Audit-Protokoll standardmäßig an, mit Alarmregeln; `security.log`; `csp.log` — bewertet mit **Erfüllt** | die Cronjob-Gruppe `frequent` ausführen, Alarm-E-Mails an Menschen leiten, an ein SIEM weiterleiten, Protokolle wöchentlich prüfen |
| A10 | Server-Side Request Forgery | „Hochladen von URL“ im Online-Editor weist private und reservierte Adressen ab und prüft Umleitungen erneut; die Linkprüfung überspringt private Hosts, sofern sie nicht gelistet sind | `AllowedPrivateHosts` leer lassen, sofern nicht nötig |

## Aufzubewahrende Nachweise

| Kategorie | Nachweis, den eine Prüferin oder ein Prüfer kontrollieren kann |
|---|---|
| A01 | Export der Rollen und Zuweisungen; verwendete Anfrageregeln (`settings/requestrules.ini`) |
| A02 | Ausgabe von `curl -sI` mit Headern und Cookie-Attributen; Überwachung des Zertifikatsablaufs |
| A05 | `bash bin/shell/verifyfiles.sh` mit Exit 0 nach dem letzten Update; `DebugOutput=disabled` |
| A06 | Aktualisierungsprotokoll mit Daten; Ausgabe von `composer audit` |
| A07 | Einstellungen für Passwort und 2FA; Liste der Benutzer ohne zweiten Schritt |
| A08 | Ausgabe von `exp:audit verify`; Bericht der Dateikonsistenzprüfung |
| A09 | Empfänger der Alarme (`./console exp:audit alerts recipients`), Konfiguration der Weiterleitung an ein SIEM, Notizen der wöchentlichen Protokollprüfung |

## Verwandte Seiten

- [Sicherheitsstandards](../../../../eng-us/standards/security.md)
- [Sicherheitshärtung](../../sicherheitshaertung.md)
- OWASP, [Top 10](https://owasp.org/Top10/)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
