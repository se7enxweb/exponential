---
title: Meldung von Datenschutzverletzungen
audience: government, admin, project-manager
category: public-sector
order: 70
summary: Wie das technische Team die Entscheidung einer öffentlichen Stelle über die Meldung einer Verletzung des Schutzes personenbezogener Daten unterstützt - feststellen, welche Daten, wessen Daten und wann, mit dem Audit-Trail, den Logs und den Exporten, die Exponential bietet - ohne Rechtsberatung zu geben.
keywords: Datenschutzverletzung, Verletzung des Schutzes personenbezogener Daten, Meldung, DSGVO, Aufsichtsbehörde, Datenschutzbeauftragte, Beweise, Audit-Trail, Zeitablauf, betroffene Personen, Vorfall, Datenschutz, data breach, personal data breach, notification, GDPR, supervisory authority, data protection officer, evidence, audit trail, timeline, affected persons, incident, privacy
type: help
---
# Meldung von Datenschutzverletzungen

> **Hinweis** Diese Seite beschreibt Funktionen und übliche Praxis; sie ist keine Rechtsberatung. Klären Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

Diese Seite richtet sich an die Administration und die Vorfallsleitung, die der oder dem Datenschutzbeauftragten und
der Rechtsberatung die Fakten liefern müssen, die diese für die Entscheidung über die Meldung einer Verletzung des
Schutzes personenbezogener Daten brauchen. In der EU legt die
[Datenschutz-Grundverordnung](https://eur-lex.europa.eu/eli/reg/2016/679/oj) Pflichten zur Meldung an die
Aufsichtsbehörde (Artikel 33) und in manchen Fällen zur Benachrichtigung der betroffenen Personen (Artikel 34) fest;
andere Rechtsordnungen haben eigene Regeln. Die Entscheidung liegt bei diesen Stellen; die Fakten liegen bei Ihnen.

## Inhalt
1. [Die richtigen Personen früh einbeziehen](#die-richtigen-personen-früh-einbeziehen)
2. [Die Fragen, die Ihnen gestellt werden](#die-fragen-die-ihnen-gestellt-werden)
3. [Wo die Antworten stehen](#wo-die-antworten-stehen)
4. [Welche personenbezogenen Daten eine Exponential-Website enthalten kann](#welche-personenbezogenen-daten-eine-exponential-website-enthalten-kann)
5. [Umgang mit den Beweisen](#umgang-mit-den-beweisen)
6. [Verwandte Seiten](#verwandte-seiten)

## Die richtigen Personen früh einbeziehen

Sobald personenbezogene Daten betroffen sein könnten, informieren Sie die oder den Datenschutzbeauftragten, zu jeder
Uhrzeit: Meldepflichten können ab dem Moment, in dem die Organisation Kenntnis erlangt, mit kurzen Fristen laufen.
Führen Sie ab dem ersten Anzeichen einen Zeitablauf.

## Die Fragen, die Ihnen gestellt werden

1. Was ist passiert, und wann hat es begonnen und geendet?
2. Welche Kategorien von Daten, und ungefähr wie viele Datensätze und Personen?
3. Wurden die Daten eingesehen, kopiert, geändert oder gelöscht, oder waren sie nur zugänglich?
4. Ist der Vorfall eingedämmt?
5. Welche Folgen sind für die Personen wahrscheinlich?
6. Was wurde getan, und was wird getan?

## Wo die Antworten stehen

| Frage | Quelle in Exponential |
|---|---|
| Wann es begann | Audit-Trail (Anmeldungen im Kanal `access`, Änderungen im Kanal `content`), `security.log`, Logs des Webservers |
| Welche Konten gehandelt haben | `./console exp:audit search --login=<login> --allow-root-user`; die Konsole nach Benutzer gefiltert |
| Was exportiert wurde | Ereignisse `data.export.csv` (Kontoliste, Exporte von Unterelementen) mit Zeilenzahlen; Audit-Exporte |
| Welche Datensätze sich geändert haben | der Kanal `content`; Versionsseiten |
| Ob die Aufzeichnung verlässlich ist | `./console exp:audit verify --allow-root-user` und externe Kopien (Syslog, Webhook) |
| Welche Anfragen die Website erreicht haben | `security.log` (Sondierungen, Injektionen, mit Status), `404.log` |

Beachten Sie die Grenzen: Knotenansichten, Suchen und Downloads werden nur aufgezeichnet, wenn Lesezugriffe
eingeschaltet sind (der Kanal `read`); Logs maskieren Adressen; ein Anfrage-Log beweist nicht, was eine Antwort
enthielt.

## Welche personenbezogenen Daten eine Exponential-Website enthalten kann

| Daten | Wo | Verweis |
|---|---|---|
| Benutzerkonten (Name, Login, E-Mail, Passwort-Hash) | Benutzer und die Benutzerklasse | [Pflege von Benutzerkonten](../personen-und-berechtigungen/pflege-von-benutzerkonten.md) |
| Formulareinsendungen | gesammelte Informationen | [Gesammelte Informationen](../../../../anleitungen/gesammelte-informationen.md) |
| Newsletter-Abonnenten und Einwilligungsprotokoll | cjw_newsletter, E-Mail-Einstellungen | [E-Mail-Einstellungen](../../../../../eng-us/features/6.0/mail-preferences.md) |
| Shop-Bestellungen, falls ein Shop läuft | Bestellungen | [Shop-Dashboard](../../../../../eng-us/features/6.0/store-dashboard.md) |
| Logs und Audit (maskierte Adressen, pseudonymisiert, wo konfiguriert) | `var/log/`, Audit | [Datenschutzstandard](../../../../../eng-us/standards/privacy.md) |
| Datensicherungen von allem oben Genannten | Ihr Speicher für Datensicherungen | [Schlüssel und Geheimnisse in Datensicherungen](../datensicherung-und-wiederherstellung/schluessel-und-geheimnisse-in-datensicherungen.md) |

Ihr Verzeichnis von Verarbeitungstätigkeiten sollte bereits angeben, welche davon Ihre Website enthält.

## Umgang mit den Beweisen

- Kopieren Sie Logs und das Audit-Verzeichnis an einen geschützten Ort, bevor Rotation oder Bereinigung sie entfernen;
  erwägen Sie, die Aufbewahrung vorübergehend zu verlängern.
- Exporte für die Untersuchung sind selbst personenbezogene Daten: beschränkter Zugriff, kurze Aufbewahrung.
- Heben Sie die Maskierung von Logs nicht weiter auf als nötig (`setup/unmasklogs` nur für die untersuchende Person).

## Verwandte Seiten
- [Verdacht auf ein kompromittiertes Konto](verdacht-auf-kompromittiertes-konto.md)
- [Compliance und DSGVO (Hosting)](../../../hosting/sicherheit/compliance-und-dsgvo.md)
- [Aufbewahrung und Rotation von Logs](../ueberwachung/aufbewahrung-und-rotation-von-protokollen.md)
- [Nachbetrachtung eines Vorfalls](nachbetrachtung-eines-vorfalls.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
