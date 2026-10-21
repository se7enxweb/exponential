---
title: "Ausschreibungsvorlage: Klausel zur Sicherheit"
audience: government, project-manager, partner, admin
category: public-sector
order: 40
summary: Klauseltext für die Sicherheitspflichten in einem Vertrag über ein CMS - gelieferte und dokumentierte sichere Konfiguration, Schutz der Anmeldung, Protokollierung, Zugang des Lieferanten, Sicherheitstests und Meldung von Sicherheitsvorfällen - mit Lücken und den Einstellungen von Exponential, die sie berührt.
keywords: Klausel zur Sicherheit, Ausschreibungsvorlage, Härtung, sichere Konfiguration, Zwei-Faktor, Kontosperre, CSP, HSTS, Audit-Protokoll, Penetrationstest, Meldung von Sicherheitsvorfällen, NIS2, Vertrag, security clause, tender template, hardening, secure configuration, two-factor, lockout, audit trail, penetration test, incident notification, contract
type: help
---
# Ausschreibungsvorlage: Klausel zur Sicherheit

Diese Seite richtet sich an Beschaffungsstellen und Informationssicherheitsbeauftragte, die die Sicherheitspflichten
eines Vertrags über ein CMS schreiben. Die Klausel macht aus den Anforderungsbereichen der
[Sicherheitsanforderungen](../anforderungen/sicherheitsanforderungen.md) Vertragsbedingungen. Patches und der Umgang mit
Schwachstellen haben eine eigene Vorlage: [Offenlegung von Schwachstellen und Patches](klausel-offenlegung-von-schwachstellen-und-patches.md).

**Vorlage** - an Ihre Vergaberegeln anpassen; keine Rechtsberatung

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt
1. [Die Klausel](#die-klausel)
2. [Die Anlage zur sicheren Konfiguration](#die-anlage-zur-sicheren-konfiguration)
3. [Was ein auf Exponential beruhendes Angebot zeigen kann](#was-ein-auf-exponential-beruhendes-angebot-zeigen-kann)
4. [Hinweise für die bewertende Person](#hinweise-für-die-bewertende-person)

## Die Klausel

```text
Y. SECURITY

Y.1 Secure configuration. Before go-live the Supplier shall configure the system
    according to the secure configuration annex <annex> and deliver a document
    listing every security-relevant setting that differs from the software's
    shipped defaults, with the reason.

Y.2 Sign-in. All accounts with access to the administration shall use a second
    authentication factor [from <date>]. Repeated failed sign-ins shall be
    limited as set out in <annex>.

Y.3 Least privilege. The Supplier shall implement the role model in <annex>.
    Supplier staff shall have personal accounts; shared accounts are not permitted.
    The Supplier shall review access with the Customer every <interval>.

Y.4 Logging. Security-relevant events shall be logged in a tamper-evident form,
    retained for <period>, and made available to the Customer on request within
    <time>. [Events shall be forwarded to <Customer's log platform>.]

Y.5 Transport. The system shall be reachable only over HTTPS with a current
    TLS configuration; HTTP Strict Transport Security shall be enabled.

Y.6 Security testing. Before go-live and every <interval>, [the Supplier shall
    commission / the Customer may commission] a security test by <party>.
    Findings rated <severity> or higher shall be corrected within <time>.

Y.7 Incidents. The Supplier shall notify the Customer of a security incident
    affecting the system or the Customer's data within <time> of becoming aware
    of it, and shall cooperate with the Customer's incident response
    [and reporting obligations under <applicable rules>].

Y.8 Secrets. Credentials and keys shall be stored only in <location>, never in
    the source code repository, and rotated at the end of the contract.

Y.9 Evidence. The Supplier shall state any external security assessment of the
    software or service and its date, or state that none exists.
```

## Die Anlage zur sicheren Konfiguration

Führen Sie die Entscheidungen auf, damit sich Y.1 bei der Abnahme prüfen lässt. Für Exponential sind die Einstellungen in
[Sicherheitshärtung für den Produktivbetrieb](../../../../installation/13-sicherheitshaertung.md) und den
[Sicherheitsstandards](../../../../../eng-us/standards/security.md) dokumentiert:

| Thema | Einstellung (Datei, Block, Schlüssel) | Mitgelieferter Standardwert | Ihre Entscheidung |
|---|---|---|---|
| Mindestlänge des Passworts | `site.ini [UserSettings] MinPasswordLength` | 10 | <Wert> |
| Kontosperre | `site.ini [UserSettings] MaxNumberOfFailedLogin` | 0 (aus) | <Wert> |
| Zweistufige Anmeldung | Erweiterung `sevenx_authentication_2fa` ([Zweistufige Anmeldung](../../../../anleitungen/zwei-faktor-anmeldung.md)) | nicht aktiv | <an für Rollen> |
| Content Security Policy | `site.ini [ContentSecurityPolicy] Mode` | `auto` (Report-only in der Administration, keine auf Websites) | <Modus je Siteaccess> |
| HSTS | `site.ini [HTTPHeaderSettings] SecurityHeaders[Strict-Transport-Security]` | leer | <Wert> |
| Audit-Protokoll | `audit.ini [AuditSettings] Audit` | `enabled` | beibehalten |
| Im Produktivbetrieb gesperrte Einstellungen | [Vom Deployment verwaltete Konfiguration](../../../../../eng-us/features/6.0/managed-by-deployment.md) | aus | <Entscheidung> |
| Drosselung davor | request-shield ([Sicherheitshärtung](../../../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield)) | nicht installiert | <Entscheidung> |

## Was ein auf Exponential beruhendes Angebot zeigen kann

- Y.2: Die zweistufige Anmeldung gibt es als Erweiterung auf Wunsch; die Kontosperre gibt es, sie ist aber
  standardmäßig aus.
- Y.4: Das Audit-Protokoll ist standardmäßig an und per Hash verkettet; `./console exp:audit verify` prüft es;
  Audit-Senken können Ereignisse weiterleiten, auch über einen signierten Webhook ([Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md)).
- Y.5: Velocity verwaltet Zertifikate ([HTTPS-Zertifikate von Velocity](../../../../../eng-us/features/6.0/velocity-https-certificates.md));
  HSTS setzt der Betreiber.
- Y.9: Für die Software sind weder eine ASVS-Bewertung noch eine externe Bewertung dokumentiert.

## Hinweise für die bewertende Person

- Ein Bieter, der eine leere Liste für Y.1 zurückgibt, hat die Härtung nicht geplant; die mitgelieferten Standardwerte
  lassen die Kontosperre aus und die CSP nicht durchgesetzt.
- Y.6 ist günstiger und unabhängiger, wenn der Kunde die prüfende Stelle wählt.

## Verwandte Seiten
- [Ausschreibungsvorlagen](README.md)
- [Sicherheitsanforderungen](../anforderungen/sicherheitsanforderungen.md)
- [Klausel zu Offenlegung von Schwachstellen und Patches](klausel-offenlegung-von-schwachstellen-und-patches.md)
- [Leitfaden zu Sicherheit und Audit](../../../../anleitungen/sicherheit-und-audit.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
