---
title: Anforderungen Nachweisen zuordnen
audience: government, project-manager, partner, sales-marketing
category: public-sector
order: 150
summary: Eine Referenztabelle, die übliche Anforderungen an ein CMS im öffentlichen Sektor den genauen Seiten, Einstellungen und Tests von Exponential zuordnet, die sie belegen, mit dem dokumentierten Status, für Bewertende, die Antworten prüfen, und Bieter, die sie schreiben.
keywords: Nachweise, Zuordnung, Erfüllungsmatrix, Rückverfolgbarkeit von Anforderungen, Standards, WCAG, OWASP, DSGVO, i18n, Status, Erfüllt, Teilweise erfüllt, Nicht geprüft, Nicht erfüllt, Complies, Partly complies, Unverified, Bewertung, Angebotsantwort, evidence, mapping, compliance matrix, requirements traceability, standards, GDPR, status, evaluation, bid response
type: reference
---
# Anforderungen Nachweisen zuordnen

Diese Seite richtet sich an Bewertende, die ein Angebot prüfen, und an Bieter, die eine Ausschreibung mit Exponential
beantworten. Sie ordnet die Anforderungsbereiche dieses Ordners den Nachweisen zu, die das Repository tatsächlich
enthält: einer Dokumentationsseite, einer Einstellung, einem Test, einem Befehl. Die Statusspalte übernimmt die
Statusangaben der [Seiten zu den Standards](../../../../standards/README.md) genau; werten Sie sie in einem Angebot nicht
auf.

## Inhalt
1. [Wie Sie diese Tabelle verwenden](#wie-sie-diese-tabelle-verwenden)
2. [Barrierefreiheit](#barrierefreiheit)
3. [Sicherheit](#sicherheit)
4. [Datenschutz](#datenschutz)
5. [Sprachen und Webstandards](#sprachen-und-webstandards)
6. [Betrieb, Schriftgut und Ausstieg](#betrieb-schriftgut-und-ausstieg)
7. [Release- und Qualitätspraxis](#release--und-qualitätspraxis)

## Wie Sie diese Tabelle verwenden

- **Bieter**: Nennen Sie Seite und Abschnitt und zitieren Sie den Status wie geschrieben. Wo der Status „Teilweise
  erfüllt“ oder „Nicht geprüft“ lautet, sagen Sie, was Sie für diesen Kunden gegen die Lücke tun.
- **Bewertende**: Öffnen Sie den Link und prüfen Sie, ob er sagt, was das Angebot behauptet. Einstellungen lassen sich
  in einer mit `./console exp:install --apply` erstellten Testinstallation prüfen ([Exponential mit einem Befehl installieren](../../../../funktionen/6.0/installation-mit-einem-befehl.md)).

## Barrierefreiheit

| Anforderungsbereich | Nachweis | Status |
|---|---|---|
| WCAG 2.2 AA, Administration | [Standards zur Barrierefreiheit](../../../../standards/barrierefreiheit.md); `tests/tests/design/` (Beschriftungen, Landmarks, Kontrast, Tastatur) | Teilweise erfüllt |
| WCAG 2.2 AA, öffentliche Website | keiner für mitgelieferte öffentliche Designs | nicht geprüft |
| WAI-ARIA 1.2 | dieselbe Seite, Muster von admin4 | Teilweise erfüllt |
| EN 301 549 | dieselbe Seite | Nicht geprüft |
| Section 508 / VPAT | dieselbe Seite; kein VPAT oder ACR | Nicht geprüft |
| Rechts nach links | [Sprachen von rechts nach links](../../../../funktionen/6.0/rechts-nach-links.md) | dokumentiert |

## Sicherheit

| Anforderungsbereich | Nachweis | Status |
|---|---|---|
| OWASP Top 10 (2021) | [Sicherheitsstandards](../../../../standards/sicherheit.md), Tabelle A01 bis A10 | Teilweise erfüllt |
| OWASP ASVS 4.0.3 | dieselbe Seite | Nicht geprüft (keine Bewertung durchgeführt) |
| Content Security Policy | [Content Security Policy](../../../../funktionen/6.0/content-security-policy.md); `settings/site.ini [ContentSecurityPolicy]` | Teilweise erfüllt |
| Sicherheitsprotokollierung | [Audit-Protokoll](../../../../funktionen/6.0/pruefprotokoll.md); `settings/audit.ini [AuditSettings] Audit=enabled` | Erfüllt (A09) |
| Zweistufige Anmeldung | [Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md) | Erweiterung auf Wunsch |
| Schritte zur Härtung | [Sicherheitshärtung für den Produktivbetrieb](../../../../installation/13-sicherheitshaertung.md), [Sicherheitshärtung](../../../sicherheitshaertung.md) | dokumentiert |
| Offenlegung von Schwachstellen | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) | Teilweise erfüllt (keine security.txt) |

## Datenschutz

| Anforderungsbereich | Nachweis | Status |
|---|---|---|
| Funktionen für die DSGVO | [Datenschutzstandards](../../../../standards/datenschutz.md) | Teilweise erfüllt (Funktionen bereitgestellt) |
| Cookies (ePrivacy) | dieselbe Seite, Cookie-Tabelle | Teilweise erfüllt (kein Einwilligungsbanner) |
| Einwilligungsnachweise, Abmeldung | [E-Mail-Einstellungen](../../../../funktionen/6.0/e-mail-einstellungen.md) | CAN-SPAM: Erfüllt; RFC 8058: Erfüllt |
| Datensparsamkeit in Protokollen | [Log-Betrachter](../../../../funktionen/6.0/log-betrachter.md), [Anfrageprotokolle](../../../../funktionen/6.0/anfrageprotokolle.md) | dokumentiert |
| Bereinigung von Konten | [Benutzerkonten prüfen](../../../../funktionen/6.0/benutzerpflege.md) | dokumentiert |

## Sprachen und Webstandards

| Anforderungsbereich | Nachweis | Status |
|---|---|---|
| UTF-8 | [Standards zur Internationalisierung](../../../../standards/internationalisierung.md) | Erfüllt |
| 4-Byte-UTF-8 unter MySQL/MariaDB | dieselbe Seite | Teilweise erfüllt |
| Pluralformen | dieselbe Seite | Nicht erfüllt |
| HTTP, HTML, CSS, Cookies, robots | [Standards der Webplattform](../../../../standards/webplattform.md) | je Zeile dieser Seite |
| APIs (REST, OAuth, LDAP, RSS, SMTP) | [Standards zu APIs und Protokollen](../../../../standards/apis-und-protokolle.md) | je Zeile dieser Seite |
| Barrierefreiheit von PDF (PDF/UA) | [Standards zu Dokumentation und Veröffentlichung](../../../../standards/dokumentation-und-veroeffentlichung.md) | Nicht erfüllt |

## Betrieb, Schriftgut und Ausstieg

| Anforderungsbereich | Nachweis |
|---|---|
| Sicherungen | [Sicherungen: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md), [10.9 Sicherung und Wiederherstellung](../../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung) |
| Wartungsfenster | [Wartungsmodus](../../../../anleitungen/wartungsmodus.md) |
| Upgrade-Prüfungen | [Die Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md) |
| Versionsgeschichte | [Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md) |
| Exporte | [Von Exponential migrieren](../../../../installation/migration/exponential-verlassen.md) |
| Datenbanken | [Standards zu Datenbanken und SQL](../../../../standards/datenbanken-und-sql.md), [Datenbankunterstützung](../../../datenbankunterstuetzung.md) |

## Release- und Qualitätspraxis

| Anforderungsbereich | Nachweis | Status |
|---|---|---|
| Dauerhafte Release-Tags | [Quellcodeverwaltung und Releases](../../../../standards/versionsverwaltung-und-releases.md) | Erfüllt (per Richtlinie) |
| Signierte Commits oder Tags | dieselbe Seite | Nicht erfüllt |
| Automatisierte Tests und CI | [Tests und kontinuierliche Integration](../../../../standards/tests-und-ci.md) | Erfüllt |
| Berichte zur Codeabdeckung | dieselbe Seite | Nicht erfüllt |

## Verwandte Seiten
- [Anforderungen an ein CMS für den öffentlichen Sektor](README.md)
- [Was Exponential nachweisen kann und was nicht](was-exponential-nachweisen-kann-und-was-nicht.md)
- [Prüfbare Anforderungen schreiben](pruefbare-anforderungen-schreiben.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
