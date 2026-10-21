---
title: Verantwortlicher, Auftragsverarbeiter und die Software
audience: government, project-manager
category: public-sector
order: 30
summary: Für öffentliche Stellen, die die Datenschutzrollen rund um eine Website klären - Exponential ist Software, kein Verantwortlicher; wer in der Regel Verantwortlicher und wer Auftragsverarbeiter ist (Ihre Stelle, Hosting-Unternehmen, Umsetzungspartner, Dienste von Drittanbietern in Erweiterungen), und welche Verträge und Verzeichnisse daraus folgen.
keywords: Verantwortlicher, Auftragsverarbeiter, Unterauftragsverarbeiter, Auftragsverarbeitungsvertrag, AVV, Hosting-Unternehmen, Umsetzungspartner, Support, Dienste von Drittanbietern, DSGVO Artikel 28, Exponential-Projekt, Rollen, öffentlicher Sektor, controller, processor, data processing agreement, GDPR
type: help
---
# Verantwortlicher, Auftragsverarbeiter und die Software

Diese Seite richtet sich an Datenschutzbeauftragte und Projektleitungen. Die Aussage des Projekts ist eindeutig:
„Exponential ist Software, kein Verantwortlicher. Die Einhaltung ... liegt in der Verantwortung der Organisation, die
die Website betreibt“ ([Datenschutzstandards](../../../../eng-us/standards/privacy.md)). Rund um eine öffentliche Website haben
mehrere Parteien mit personenbezogenen Daten zu tun; diese Seite hilft Ihnen, sie zu benennen.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Typische Parteien](#typische-parteien)
2. [Dienste von Drittanbietern, die Erweiterungen hinzufügen](#dienste-von-drittanbietern-die-erweiterungen-hinzufügen)
3. [Verträge und Verzeichnisse](#verträge-und-verzeichnisse)
4. [Verwandte Seiten](#verwandte-seiten)

## Typische Parteien

| Partei | Übliche Rolle (mit Ihrer Rechtsberatung zu bestätigen) | Womit sie zu tun hat |
|---|---|---|
| Ihre öffentliche Stelle | Verantwortlicher | alles, was die Website verarbeitet |
| Hosting-Unternehmen | Auftragsverarbeiter | Server, Datenbank, Datensicherungen, Protokolle |
| Umsetzungspartner oder Agentur | Auftragsverarbeiter, wenn er auf Produktionsdaten zugreift | Inhalte, Benutzerkonten, Formulareinsendungen während der Arbeit |
| Supportanbieter (7x oder ein Partner) | Auftragsverarbeiter, wenn er auf die Installation zugreift | wie oben, für Supportfälle |
| Das Exponential-Projekt | keine für Ihre Website: Es veröffentlicht Software | nichts, außer Sie senden Daten in einer Supportanfrage |

Der Fehlerbericht des Projekts im Hilfesystem erstellt seine Zusammenfassung der Installation aus einer festen Liste von
Fakten und entfernt E-Mail-Adressen, IP-Adressen, Zugangsdaten und Pfade, bevor irgendetwas Ihre Website verlässt
([Datenschutzstandards: DSGVO](../../../../eng-us/standards/privacy.md#gdpr)).

## Dienste von Drittanbietern, die Erweiterungen hinzufügen

Einige mit Exponential installierte Erweiterungen senden Besucherdaten an einen Drittanbieter, wenn eine Website sie
aktiviert ([Datenschutzstandards: Dienste von Drittanbietern](../../../../eng-us/standards/privacy.md#third-party-services-in-shipped-extensions)):

| Erweiterung | Drittanbieter |
|---|---|
| `bcwebsitestatistics` | Google Analytics / Google Tag Manager |
| `recaptcha` | Google reCAPTCHA |
| `hcaptcha` | hCaptcha |
| `sevenx_authentication_2fa` Social Login | der gewählte Identitätsanbieter |
| `ezgmaplocation` | Google Maps (die Karte für Redakteure und Karten in Ihren Templates) |

Jeder davon ist eine eigene Beziehung (Auftragsverarbeiter oder eigenständiger Verantwortlicher, je nach Dienst und
Einschätzung Ihrer Rechtsberatung) mit eigenen Bedingungen.

## Verträge und Verzeichnisse

1. Ein Auftragsverarbeitungsvertrag mit jedem Auftragsverarbeiter (Hosting, Umsetzung, Support).
2. Ein Eintrag in Ihrem Verzeichnis von Verarbeitungstätigkeiten für die Verarbeitungen der Website (siehe
   „Verzeichnis von Verarbeitungstätigkeiten“).
3. Eine Liste der aktiven Erweiterungen mit Drittanbietern: **Setup > Extensions** oder
   `./console exp:ini where site.ini/ExtensionSettings/ActiveExtensions --allow-root-user`.
4. Die Datenschutzerklärung nennt die Empfänger (siehe „Gliederung einer Datenschutzerklärung“).

## Verwandte Seiten

- [Datenschutzstandards](../../../../eng-us/standards/privacy.md)
- [Hosting-Unternehmen: Compliance und DSGVO](../../hosting/sicherheit/compliance-und-dsgvo.md)
- [Umsetzungspartner](../../partner/implementierungspartner.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
