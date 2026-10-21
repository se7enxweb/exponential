---
title: Sicherheitsanforderungen
audience: government, project-manager, admin, partner
category: public-sector
order: 50
summary: Wie Sie die Sicherheitsanforderungen einer Ausschreibung für ein CMS schreiben - Authentifizierung, Zugriffskontrolle, Behandlung von Eingaben, Header und CSP, Protokollierung, Updates und Umgang mit Schwachstellen - mit prüfbaren Formulierungen und dem dokumentierten Status von Exponential gegenüber OWASP Top 10, ASVS und CSP.
keywords: Sicherheitsanforderungen, OWASP Top 10, ASVS, CSP, Zwei-Faktor, 2FA, MFA, Passwort, Kontosperre, Audit, Protokollierung, Patches, Offenlegung von Schwachstellen, NIS2, NIST CSF, Ausschreibung, security requirements, two-factor, password, lockout, logging, patching, vulnerability disclosure, tender
type: help
---
# Sicherheitsanforderungen

Diese Seite richtet sich an Beschaffungsteams, Informationssicherheitsbeauftragte und Projektleitungen, die den
Sicherheitsteil einer Ausschreibung für ein CMS schreiben. Sie gibt Formulierungen für Anforderungen je Bereich und gibt
an, was Exponential in den [Sicherheitsstandards](../../../../../eng-us/standards/security.md) dokumentiert, einschließlich der
Lücken. Verwenden Sie sie zusammen mit Ihrer eigenen Risikobewertung; Rahmenwerke wie das
[NIST Cybersecurity Framework 2.0](https://www.nist.gov/cyberframework) oder in der EU die
[NIS2-Richtlinie](https://eur-lex.europa.eu/eli/dir/2022/2555/oj) können den Rahmen für Ihre Organisation setzen.

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt
1. [Produkt und Leistung](#produkt-und-leistung)
2. [Formulierung der Anforderungen nach Bereich](#formulierung-der-anforderungen-nach-bereich)
3. [Der dokumentierte Status von Exponential](#der-dokumentierte-status-von-exponential)
4. [Einstellungen, die eine bewertende Person prüfen kann](#einstellungen-die-eine-bewertende-person-prüfen-kann)
5. [Fallstricke](#fallstricke)

## Produkt und Leistung

Trennen Sie, was die **Software** leisten muss, von dem, was der **Lieferant oder Host** leisten muss. „Die Software
unterstützt eine Anmeldung mit zwei Faktoren“ ist eine Produktanforderung; „der Lieferant schaltet sie für jeden
Administrator ein und lässt sie eingeschaltet“ ist eine Leistungsanforderung. Beide werden gebraucht.

## Formulierung der Anforderungen nach Bereich

| Id | Bereich | Anforderung |
|---|---|---|
| SEC-01 | Authentifizierung | Von Administratoren und Redaktion kann bei der Anmeldung ein zweiter Faktor verlangt werden. Nennen Sie den Mechanismus. |
| SEC-02 | Authentifizierung | Passwörter werden mit einer aktuellen adaptiven Hash-Funktion gespeichert; geben Sie die Mindestlänge an und ob sie sich erhöhen lässt. |
| SEC-03 | Authentifizierung | Wiederholte fehlgeschlagene Anmeldungen werden erkannt und können gedrosselt oder gesperrt werden; geben Sie die Standardwerte an. |
| SEC-04 | Zugriffskontrolle | Jede administrative Funktion ist durch ein Berechtigungsmodell mit minimalen Rechten geschützt; beschreiben Sie es. |
| SEC-05 | Ein- und Ausgabe | Formulare sind gegen Cross-Site Request Forgery geschützt; Ausgaben werden gegen Cross-Site Scripting maskiert; Uploads werden anhand ihres Inhalts geprüft. |
| SEC-06 | Header | Das System sendet Sicherheits-Header (zum Beispiel `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`) und unterstützt eine Content Security Policy ([CSP Level 3](https://www.w3.org/TR/CSP3/)). Geben Sie an, was standardmäßig eingeschaltet ist. |
| SEC-07 | Protokollierung | Sicherheitsrelevante Ereignisse (Anmeldungen, Änderungen an Berechtigungen, Entfernen von Inhalten, Änderungen an Einstellungen) werden manipulationssicher protokolliert und lassen sich exportieren. |
| SEC-08 | Updates | Der Lieferant gibt die unterstützten Versionen an, wie Sicherheitskorrekturen veröffentlicht werden und wie schnell sie unter diesem Vertrag eingespielt werden. |
| SEC-09 | Offenlegung | Es gibt einen veröffentlichten, vertraulichen Kanal zum Melden von Schwachstellen. |
| SEC-10 | Absicherung | Geben Sie jede externe Bewertung an (Penetrationstest, Stufe nach [OWASP ASVS](https://owasp.org/www-project-application-security-verification-standard/)) und ihr Datum, oder geben Sie an, dass es keine gibt. |

## Der dokumentierte Status von Exponential

Aus [Sicherheitsstandards](../../../../../eng-us/standards/security.md):

| Anforderung | Was dokumentiert ist | Status oder Lücke |
|---|---|---|
| SEC-01 | Zweistufige Anmeldung mit der Erweiterung `sevenx_authentication_2fa` ([Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md)) | Auf Wunsch: eine Erweiterung und ein Login-Handler zum Konfigurieren |
| SEC-02 | `password_hash()` (heute bcrypt), neues Hashing bei der Anmeldung; Mindestlänge 10 (`MinPasswordLength`) | Unter ASVS 4.0.3 V2.1.1 (12), sofern nicht erhöht |
| SEC-03 | Brute-Force wird von Alarmregeln des Audits erkannt; Kontosperre mit `MaxNumberOfFailedLogin` | **Kontosperre standardmäßig aus** (`MaxNumberOfFailedLogin=0`); Drosselung bleibt dem Webserver oder request-shield überlassen ([Sicherheitshärtung](../../../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield)) |
| SEC-04 | Rollen und Richtlinien werden für jede Modulansicht geprüft ([Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md)) | OWASP A01 **Partly complies** |
| SEC-05 | Formular-Tokens (`ezformtoken`), Maskierung mit `wash`, Prüfung von Uploads, SVG-Bereinigung | Maskierung in Templates ist nicht automatisch; keine Prepared Statements in der Datenbankschicht |
| SEC-06 | Sicherheits-Header auf jeder Seite; CSP mit Nonces und Berichten ([Content Security Policy](../../../../../eng-us/features/6.0/content-security-policy.md)) | CSP standardmäßig **Report-only in der Administration, aus auf öffentlichen Websites**; HSTS leer, bis der Betreiber es setzt |
| SEC-07 | Audit-Protokoll standardmäßig an, per Hash verkettet, signierte Manifeste ([Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md)) | OWASP A09 **Complies** |
| SEC-08 | `SECURITY.md`: 6.0 ist die gepflegte Linie; Korrekturen auf `main`, veröffentlicht als nächstes 6.0.x; veröffentlichte Tags ändern sich nie | Keine Prüfung der Abhängigkeiten auf Schwachstellen in der CI |
| SEC-09 | Vertrauliche Meldung per E-Mail, erste Antwort innerhalb eines Werktags ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)) | Keine `security.txt` ([RFC 9116](https://www.rfc-editor.org/rfc/rfc9116)) mitgeliefert; ein Betreiber kann eine hinzufügen |
| SEC-10 | Drei dokumentierte Härtungsdurchgänge | **Keine Bewertung nach ASVS durchgeführt** (Status Unverified); keine externe Bewertung dokumentiert |

Insgesamt: OWASP Top 10 (2021) **Partly complies**; ASVS 4.0.3 **Unverified**; CSP Level 3 **Partly complies**.

## Einstellungen, die eine bewertende Person prüfen kann

In einer Testinstallation ([Exponential mit einem Befehl installieren](../../../../../eng-us/features/6.0/install-in-one-command.md)):

| Einstellung | Wo | Mitgelieferter Wert |
|---|---|---|
| `[UserSettings] MinPasswordLength` | `settings/site.ini` | 10 |
| `[UserSettings] MaxNumberOfFailedLogin` | `settings/site.ini` | 0 (aus) |
| `[ContentSecurityPolicy] Mode` | `settings/site.ini` | `auto` (Report-only in der Administration, keine auf Websites) |
| `[AuditSettings] Audit` | `settings/audit.ini` | `enabled` |
| `[HTTPHeaderSettings] SecurityHeaders[]` | `settings/site.ini` | die in den Sicherheitsstandards aufgeführten Header |

Fragen Sie den Bieter, welche davon er für Ihre Website ändern wird, und lassen Sie die geänderten Werte als Teil der
Dokumentation liefern. Kapitel 13 des Installationsbuchs führt die Schritte für den Produktivbetrieb auf
([Sicherheitshärtung für den Produktivbetrieb](../../../../installation/13-sicherheitshaertung.md)).

## Fallstricke

- Die Standardwerte für den gelieferten Zustand halten. Die Schritte zur Härtung sind Arbeit des Lieferanten; nehmen Sie
  sie in den Vertrag auf.
- Nach einer „ISO-27001-Zertifizierung der Software“ fragen. ISO/IEC 27001 zertifiziert das Managementsystem einer
  Organisation, kein Produkt; fragen Sie gegebenenfalls den Hosting-Lieferanten danach ([ISO/IEC 27001](https://www.iso.org/standard/27001)).
- Zusagen zur Zeit bis zur Behebung, die vom vorgelagerten Projekt abhängen. Fragen Sie stattdessen nach der Zeit bis
  zum Einspielen einer veröffentlichten Korrektur ([SLA-Optionen](../../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md)).

## Verwandte Seiten
- [Anforderungen an ein CMS für den öffentlichen Sektor](README.md)
- [Ausschreibungsvorlage: Klausel zur Sicherheit](../ausschreibungsvorlagen/klausel-sicherheit.md)
- [Ausschreibungsvorlage: Offenlegung von Schwachstellen und Patches](../ausschreibungsvorlagen/klausel-offenlegung-von-schwachstellen-und-patches.md)
- [Leitfaden zu Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
