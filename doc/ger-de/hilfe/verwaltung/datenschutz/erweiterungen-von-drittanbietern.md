---
title: Erweiterungen von Drittanbietern und Datenschutz
audience: government, admin, project-manager
category: public-sector
order: 210
summary: Für öffentliche Stellen - die mit Exponential installierten Erweiterungen, die nach dem Aktivieren Besucher- oder Inhaltsdaten an Drittanbieter senden (Webanalyse, CAPTCHAs, Karten, Social Login, KI-Anbieter), dass keine davon standardmäßig aktiv ist, und die Prüfungen vor dem Aktivieren.
keywords: Dienste von Drittanbietern, Erweiterungen, bcwebsitestatistics, Google Analytics, recaptcha, hcaptcha, ezgmaplocation, Google Maps, Social Login, expai, KI-Anbieter, Datenübermittlung, Auftragsverarbeiter, ActiveExtensions, Auswirkungen auf den Datenschutz, öffentlicher Sektor, third-party services, extensions, AI providers, data transfer, processor
type: help
---
# Erweiterungen von Drittanbietern und Datenschutz

Diese Seite richtet sich an Administratoren, Projektleitungen und Datenschutzbeauftragte. Composer installiert mehrere
Erweiterungen zusammen mit Exponential, aber sie sind **nicht aktiv**, bis eine Website sie aktiviert:
`settings/site.ini [ExtensionSettings] ActiveExtensions[]` wird nur mit `xrowmetadata` ausgeliefert, und der
Einrichtungsassistent fügt `ezjscore`, `ezoe` und `ezformtoken` hinzu
([Datenschutzstandards: Dienste von Drittanbietern](../../../standards/datenschutz.md#drittanbieterdienste-in-mitgelieferten-extensions)).
Einige davon senden nach dem Aktivieren Daten an einen Drittanbieter. Ob ein Seitenpaket eine davon aktiviert, sollten
Sie in Ihrer Installation prüfen.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Die Erweiterungen](#die-erweiterungen)
2. [Prüfen, was aktiv ist](#prüfen-was-aktiv-ist)
3. [Bevor Sie eine aktivieren](#bevor-sie-eine-aktivieren)
4. [Verwandte Seiten](#verwandte-seiten)

## Die Erweiterungen

| Erweiterung | Drittanbieter | Daten | Seite in diesem Bereich |
|---|---|---|---|
| `bcwebsitestatistics` | Google Analytics / Google Tag Manager | Seitenaufrufe der Besucher; Shop-Bestellungen, wenn `OrderSubmit` eingeschaltet ist | Webanalyse |
| `recaptcha` | Google reCAPTCHA | Browser und Interaktion des Besuchers auf geschützten Formularen | CAPTCHAs |
| `hcaptcha` | hCaptcha | wie oben | CAPTCHAs |
| `ezgmaplocation` | Google Maps | die Kartensuchen der Redakteure; Kartenaufrufe der Besucher in Ihren Templates | Karten und Einbettungen |
| `sevenx_authentication_2fa` Social Login | der gewählte Identitätsanbieter | Anmeldung über diesen Anbieter | Social Login |
| `expai` (Exponential Intelligence) | der konfigurierte KI-Anbieter | Inhalte und Bilder für Alternativtexte, nach ihrer Datenrichtlinie | (unten) |
| Statistiken von `cjw_newsletter` | keiner (auf der Website) | Öffnungen und Klicks pro Person, nur mit der Einwilligung „Newsletter-Statistik“ | Einwilligungsnachweise für E-Mail |

`expai` wird über `composer.json` installiert und ist nur dort aktiv, wo eine Website es aktiviert; seine
Datenrichtlinie entscheidet, welche Inhalte an welchen Anbieter und in welche Region gehen dürfen
([Exponential Intelligence](../../../funktionen/6.0/erweiterungen/expai.md)). Beziehen Sie Ihre bzw. Ihren
Datenschutzbeauftragten vor dem Aktivieren ein.

## Prüfen, was aktiv ist

```bash
./console exp:ini where site.ini/ExtensionSettings/ActiveExtensions --allow-root-user
```

oder **Setup > Extensions**. Sehen Sie sich auch `ActiveAccessExtensions[]` in jedem Siteaccess an.

## Bevor Sie eine aktivieren

1. Zweck und Rechtsgrundlage: warum der Dienst nötig ist und auf welcher Grundlage.
2. Die Rolle des Anbieters (Auftragsverarbeiter oder eigenständiger Verantwortlicher) und seine Bedingungen; wo nötig
   ein Auftragsverarbeitungsvertrag.
3. Übermittlungen außerhalb Ihrer Rechtsordnung, wo Ihr Recht sie beschränkt.
4. Einwilligung vor dem Laden, wo nötig (siehe „Cookie-Banner und Einwilligung“).
5. Die Datenschutzerklärung aktualisiert.
6. Die Content Security Policy: die Hosts des Anbieters mit `AdditionalSources[]` nur auf dem Siteaccess hinzugefügt,
   der sie braucht.
7. Barrierefreiheit dessen, was der Dienst anzeigt (CAPTCHAs und Karten sind häufige Barrieren).
8. Ein Eintrag in Ihrer Bestandsaufnahme mit dem Datum der Aktivierung.

## Verwandte Seiten

- [Datenschutzstandards](../../../standards/datenschutz.md)
- [Erweiterungen](../../../anleitungen/extensions.md)
- [Verantwortlicher, Auftragsverarbeiter und die Software](verantwortlicher-auftragsverarbeiter-und-exponential.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
