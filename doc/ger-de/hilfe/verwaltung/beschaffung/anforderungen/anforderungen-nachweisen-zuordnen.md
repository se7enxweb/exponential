---
title: Anforderungen Nachweisen zuordnen
audience: government, project-manager, partner, sales-marketing
category: public-sector
order: 150
summary: Eine Referenztabelle, die übliche Anforderungen an ein CMS im öffentlichen Sektor den genauen Seiten, Einstellungen und Tests von Exponential zuordnet, die sie belegen, mit dem dokumentierten Status, für Bewertende, die Antworten prüfen, und Bieter, die sie schreiben.
keywords: Nachweise, Zuordnung, Erfüllungsmatrix, Rückverfolgbarkeit von Anforderungen, Standards, WCAG, OWASP, DSGVO, i18n, Status, Complies, Partly complies, Unverified, Bewertung, Angebotsantwort, evidence, mapping, compliance matrix, requirements traceability, standards, GDPR, status, evaluation, bid response
type: reference
---
# Anforderungen Nachweisen zuordnen

Diese Seite richtet sich an Bewertende, die ein Angebot prüfen, und an Bieter, die eine Ausschreibung mit Exponential
beantworten. Sie ordnet die Anforderungsbereiche dieses Ordners den Nachweisen zu, die das Repository tatsächlich
enthält: einer Dokumentationsseite, einer Einstellung, einem Test, einem Befehl. Die Statusspalte übernimmt die
Statusangaben der [Seiten zu den Standards](../../../../../eng-us/standards/README.md) genau; werten Sie sie in einem Angebot nicht
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

- **Bieter**: Nennen Sie Seite und Abschnitt und zitieren Sie den Status wie geschrieben. Wo der Status „Partly
  complies“ oder „Unverified“ lautet, sagen Sie, was Sie für diesen Kunden gegen die Lücke tun.
- **Bewertende**: Öffnen Sie den Link und prüfen Sie, ob er sagt, was das Angebot behauptet. Einstellungen lassen sich
  in einer mit `./console exp:install` erstellten Testinstallation prüfen ([Exponential mit einem Befehl installieren](../../../../../eng-us/features/6.0/install-in-one-command.md)).

## Barrierefreiheit

| Anforderungsbereich | Nachweis | Status |
|---|---|---|
| WCAG 2.2 AA, Administration | [Standards zur Barrierefreiheit](../../../../../eng-us/standards/accessibility.md); `tests/tests/design/` (Beschriftungen, Landmarks, Kontrast, Tastatur) | Partly complies |
| WCAG 2.2 AA, öffentliche Website | keiner für mitgelieferte öffentliche Designs | nicht geprüft |
| WAI-ARIA 1.2 | dieselbe Seite, Muster von admin4 | Partly complies |
| EN 301 549 | dieselbe Seite | Unverified |
| Section 508 / VPAT | dieselbe Seite; kein VPAT oder ACR | Unverified |
| Rechts nach links | [Sprachen von rechts nach links](../../../../../eng-us/features/6.0/right-to-left.md) | dokumentiert |

## Sicherheit

| Anforderungsbereich | Nachweis | Status |
|---|---|---|
| OWASP Top 10 (2021) | [Sicherheitsstandards](../../../../../eng-us/standards/security.md), Tabelle A01 bis A10 | Partly complies |
| OWASP ASVS 4.0.3 | dieselbe Seite | Unverified (keine Bewertung durchgeführt) |
| Content Security Policy | [Content Security Policy](../../../../../eng-us/features/6.0/content-security-policy.md); `settings/site.ini [ContentSecurityPolicy]` | Partly complies |
| Sicherheitsprotokollierung | [Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md); `settings/audit.ini [AuditSettings] Audit=enabled` | Complies (A09) |
| Zweistufige Anmeldung | [Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md) | Erweiterung auf Wunsch |
| Schritte zur Härtung | [Sicherheitshärtung für den Produktivbetrieb](../../../../installation/13-sicherheitshaertung.md), [Sicherheitshärtung](../../../sicherheitshaertung.md) | dokumentiert |
| Offenlegung von Schwachstellen | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md) | Partly complies (keine security.txt) |

## Datenschutz

| Anforderungsbereich | Nachweis | Status |
|---|---|---|
| Funktionen für die DSGVO | [Datenschutzstandards](../../../../../eng-us/standards/privacy.md) | Partly complies (Funktionen bereitgestellt) |
| Cookies (ePrivacy) | dieselbe Seite, Cookie-Tabelle | Partly complies (kein Einwilligungsbanner) |
| Einwilligungsnachweise, Abmeldung | [E-Mail-Einstellungen](../../../../../eng-us/features/6.0/mail-preferences.md) | CAN-SPAM Complies; RFC 8058 Complies |
| Datensparsamkeit in Protokollen | [Log-Betrachter](../../../../../eng-us/features/6.0/log-viewer.md), [Anfrageprotokolle](../../../../../eng-us/features/6.0/request-logs.md) | dokumentiert |
| Bereinigung von Konten | [Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md) | dokumentiert |

## Sprachen und Webstandards

| Anforderungsbereich | Nachweis | Status |
|---|---|---|
| UTF-8 | [Standards zur Internationalisierung](../../../../../eng-us/standards/internationalization.md) | Complies |
| 4-Byte-UTF-8 unter MySQL/MariaDB | dieselbe Seite | Partly complies |
| Pluralformen | dieselbe Seite | Does not comply |
| HTTP, HTML, CSS, Cookies, robots | [Standards der Webplattform](../../../../../eng-us/standards/web-platform.md) | je Zeile dieser Seite |
| APIs (REST, OAuth, LDAP, RSS, SMTP) | [Standards zu APIs und Protokollen](../../../../../eng-us/standards/apis-and-protocols.md) | je Zeile dieser Seite |
| Barrierefreiheit von PDF (PDF/UA) | [Standards zu Dokumentation und Veröffentlichung](../../../../../eng-us/standards/documentation-and-publishing.md) | Does not comply |

## Betrieb, Schriftgut und Ausstieg

| Anforderungsbereich | Nachweis |
|---|---|
| Sicherungen | [Sicherungen: was zu sichern ist](../../../hosting/betrieb/backups-was-gesichert-werden-muss.md), [10.9 Sicherung und Wiederherstellung](../../../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung) |
| Wartungsfenster | [Wartungsmodus](../../../../anleitungen/wartungsmodus.md) |
| Upgrade-Prüfungen | [Die Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md) |
| Versionsgeschichte | [Versionen eines Objekts](../../../../anleitungen/inhaltsverlauf.md) |
| Exporte | [Von Exponential migrieren](../../../../installation/migration/exponential-verlassen.md) |
| Datenbanken | [Standards zu Datenbanken und SQL](../../../../../eng-us/standards/databases-and-sql.md), [Datenbankunterstützung](../../../datenbankunterstuetzung.md) |

## Release- und Qualitätspraxis

| Anforderungsbereich | Nachweis | Status |
|---|---|---|
| Dauerhafte Release-Tags | [Quellcodeverwaltung und Releases](../../../../../eng-us/standards/source-control-and-releases.md) | Complies (per Richtlinie) |
| Signierte Commits oder Tags | dieselbe Seite | Does not comply |
| Automatisierte Tests und CI | [Tests und kontinuierliche Integration](../../../../../eng-us/standards/testing-and-ci.md) | Complies |
| Berichte zur Codeabdeckung | dieselbe Seite | Does not comply |

## Verwandte Seiten
- [Anforderungen an ein CMS für den öffentlichen Sektor](README.md)
- [Was Exponential nachweisen kann und was nicht](was-exponential-nachweisen-kann-und-was-nicht.md)
- [Prüfbare Anforderungen schreiben](pruefbare-anforderungen-schreiben.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
