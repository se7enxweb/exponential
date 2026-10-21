---
title: Datenminimierung
audience: government, admin, developer, project-manager
category: public-sector
order: 50
summary: Für öffentliche Stellen, die Datenminimierung und datenschutzfreundliche Voreinstellungen umsetzen - was Exponential bereits minimiert (Audit, Anfrageprotokolle, Protokollbetrachter, E-Mail) und was Sie selbst in Inhaltsklassen, Formularen, Protokollen, Suchstatistiken und Erweiterungen minimieren.
keywords: Datenminimierung, datenschutzfreundliche Voreinstellungen, Datenschutz durch Technikgestaltung, DSGVO Artikel 5, Artikel 25, IP-Maskierung, Pseudonymisierung, Hashing, Formulare, Inhaltsklassen, Suchstatistiken, Anfrageprotokolle, Datenschutz im Audit, Erweiterungen, öffentlicher Sektor, data minimization, privacy by default, privacy by design, GDPR
type: help
---
# Datenminimierung

Diese Seite richtet sich an Datenschutzbeauftragte, Administratoren und Entwickler. Erheben Sie nur, was Sie brauchen,
bewahren Sie nur auf, was Sie müssen, und maskieren Sie, was Sie anzeigen. Exponential minimiert mehrere seiner
eigenen Aufzeichnungen standardmäßig; der Rest ist eine Frage von Konfiguration und Gestaltung
([Datenschutzstandards: DSGVO](../../../../eng-us/standards/privacy.md#gdpr)).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Standardmäßig minimiert](#standardmäßig-minimiert)
2. [Was Sie minimieren](#was-sie-minimieren)
3. [Eine Checkliste zur Überprüfung](#eine-checkliste-zur-überprüfung)
4. [Verwandte Seiten](#verwandte-seiten)

## Standardmäßig minimiert

| Aufzeichnung | Minimierung in der Voreinstellung |
|---|---|
| Audit-Trail | Adressen auf /24 gekürzt (IPv6 /48); Sitzungskennungen gehasht; User-Agents auf Browser- und Betriebssystemfamilie reduziert; E-Mail-Adressen und Anmeldeversuche unbekannter Benutzer gehasht; keine Inhaltswerte, Passwörter, Token oder POST-Inhalte; Lesezugriffe aus; Index nach 90 Tagen pseudonymisiert |
| `404.log`, `security.log` | Client-Adressen maskiert (`MaskIP=enabled`); jeder Abfragewert in 404.log `***`; User-Agent gehasht und gekürzt |
| Protokollbetrachter | E-Mail-Adressen, IP-Adressen und Geheimnisse auf dem Bildschirm und in Downloads maskiert |
| E-Mail-Sperrliste | nur ein gesalzener Hash jeder Adresse |
| Werkzeuge für E-Mail-Einstellungen auf der Konsole | Adressen verborgen, sofern nicht `--addresses` angegeben ist |
| Suchstatistiken | standardmäßig aus (`LogSearchStats=disabled`) |
| Cookie für die Erkennung mobiler Geräte | standardmäßig aus (`DetectMobileDevice=disabled`) |
| Administration | keine Webfonts oder Skripte von anderen Hosts |

## Was Sie minimieren

- **Benutzerklasse**: Entfernen Sie Attribute, die Sie nicht brauchen (Geburtsdatum, Telefon, Anschrift), aus der
  Benutzerklasse; jedes Feld sind personenbezogene Daten, die Sie schützen, exportieren und löschen müssen.
- **Formulare**: Fragen Sie nur ab, was der Vorgang braucht; kennzeichnen Sie Optionales als optional; kein
  Freitextfeld „Anmerkungen“ in einem Formular, in dem es nicht nötig ist. Entfernen Sie Einsendungen, wenn der Vorgang
  abgeschlossen ist (siehe „Formulare zur Informationssammlung“).
- **Suchstatistiken**: Schalten Sie sie nur dort ein, wo Sie sie nutzen, und setzen Sie sie zurück, wenn die Zahlen
  nicht mehr gebraucht werden.
- **Audit**: Für strengere Anforderungen pro Feld `full | truncate | hash | off` in `[AuditPrivacySettings] Field[]`
  und `BeforeAfter=keys` ([Datenschutz im Audit](datenschutz-im-audit-trail.md)).
- **Protokolle**: Geben Sie den Kernel-Protokollen eine Altersgrenze (siehe „Aufbewahrung von Protokollen“).
- **Erweiterungen**: Aktivieren Sie Dienste von Drittanbietern nur mit einem Zweck und einer Rechtsgrundlage (siehe
  „Erweiterungen von Drittanbietern“).
- **Exporte**: CSV-Dateien mit Benutzern, Einwilligungsprotokollen und Einsendungen enthalten personenbezogene Daten;
  löschen Sie sie, wenn die Aufgabe erledigt ist.

## Eine Checkliste zur Überprüfung

- [ ] Jedes personenbezogene Feld jeder Inhaltsklasse hat einen Zweck.
- [ ] Jedes Formular fragt nur notwendige Daten ab, sagt, warum, und hat eine Aufbewahrungsregel.
- [ ] Suchstatistiken sind aus oder werden regelmäßig zurückgesetzt.
- [ ] Für jedes Protokoll ist eine Aufbewahrung festgelegt.
- [ ] Die Datenschutzeinstellungen des Audits sind mit der bzw. dem Datenschutzbeauftragten überprüft.
- [ ] Keine Erweiterung eines Drittanbieters ist ohne Entscheidung aktiv.
- [ ] Exporte werden nach der Nutzung gelöscht.

## Verwandte Seiten

- [Datenschutzstandards](../../../../eng-us/standards/privacy.md)
- [Wo personenbezogene Daten in Exponential liegen](bestandsaufnahme-personenbezogener-daten.md)
- [Checkliste Datenschutz durch Technikgestaltung](checkliste-datenschutz-durch-technikgestaltung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
