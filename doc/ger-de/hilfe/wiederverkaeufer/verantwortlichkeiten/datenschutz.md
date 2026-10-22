---
title: Datenschutz für Wiederverkäufer
audience: reseller, partner, hosting
category: reselling
order: 430
summary: Wie ein Wiederverkäufer die personenbezogenen Daten seiner Kunden und ihrer Besucher schützt - Ihre Rolle als Auftragsverarbeiter, Bedingungen der Verarbeitung und Unterauftragsverarbeiter, was Exponential für den Datenschutz bietet (Einwilligungsnachweise, maskierte Logs, Aufbewahrung, Löschung) und seine bekannten Lücken, wo Daten in der Praxis abfließen, und eine Checkliste. Keine Rechtsberatung.
keywords: Datenschutz, Privatsphäre, DSGVO, GDPR, personenbezogene Daten, Auftragsverarbeiter, Verantwortlicher, Auftragsverarbeitungsvertrag, Unterauftragsverarbeiter, Einwilligung, Aufbewahrung, Löschung, Logs, Staging, Leads, Fallstudien, data protection, privacy, personal data, processor, controller, data processing agreement, subprocessors, consent, retention, erasure, logs, staging, leads, case studies
type: help
---
# Datenschutz für Wiederverkäufer

Diese Seite ist für Inhaber und technische Leiter bei Wiederverkäufern. Wenn Sie die Website eines Kunden hosten,
betreiben oder betreuen, verarbeiten Sie in der Regel personenbezogene Daten im Auftrag des Kunden: Konten von
Redakteuren, Mitglieder, Bestellungen, Formulareingaben, Logs. Diese Seite nennt die Pflichten, die typischerweise
daraus folgen, und was Exponential bietet. Sie ist **keine Rechtsberatung**; Datenschutzrecht unterscheidet sich von
Land zu Land, fragen Sie also für Ihre Verträge einen Anwalt.

## Inhalt

1. [Ihre Rolle](#ihre-rolle)
2. [Verträge und Unterauftragsverarbeiter](#verträge-und-unterauftragsverarbeiter)
3. [Was Exponential bietet](#was-exponential-bietet)
4. [Bekannte Lücken, die Sie einplanen sollten](#bekannte-lücken-die-sie-einplanen-sollten)
5. [Wo Daten in der Praxis abfließen](#wo-daten-in-der-praxis-abfließen)
6. [Checkliste](#checkliste)

## Ihre Rolle

| Situation | Typische Rolle nach Gesetzen wie der DSGVO |
|---|---|
| Sie hosten oder betreiben die Website des Kunden | Auftragsverarbeiter für den Kunden, der der Verantwortliche ist |
| Sie kaufen Hosting oder White-Label-Dienstleistungen von 7x ein | Ihr Lieferant ist Ihr Unterauftragsverarbeiter |
| Ihre eigenen Leads, die Kontaktdaten Ihrer eigenen Kunden | Sie sind der Verantwortliche |

Lassen Sie die Rollen von Ihrem Anwalt bestätigen; sie bestimmen, was Ihr Vertrag sagen muss.

## Verträge und Unterauftragsverarbeiter

- [ ] Ein Auftragsverarbeitungsvertrag mit jedem Kunden oder Bedingungen der Verarbeitung im Vertrag.
- [ ] Eine Liste der Unterauftragsverarbeiter (Hosting-Lieferant, Speicher für Datensicherungen, Mail-Dienst, 7x für
      White-Label-Dienstleistungen) und wo die Daten gespeichert werden ([White-Label-Wiederverkäufer](../modelle/white-label-wiederverkaeufer.md#klären-sie-dies-schriftlich)).
- [ ] Wie und wann Sie den Kunden über eine Datenpanne informieren.
- [ ] Was mit den Daten am Ende des Vertrags geschieht ([Übergabe](../kunden/uebergabe.md#nach-der-übergabe)).

## Was Exponential bietet

Aus [Datenschutzstandards](../../../standards/datenschutz.md):

| Thema | Was mitgeliefert wird |
|---|---|
| Einwilligung | Optionale E-Mail-Kategorien sind aus, bis die Person sie einschaltet; Double-Opt-in für Newsletter und Marketing |
| Nachweise der Einwilligung | Tabelle `expmail_consent_log`, mit dem genau angezeigten Text |
| Auskunft und Übertragbarkeit | Mail-Einstellungen und Einwilligungsverlauf als JSON oder CSV herunterladbar; Audit-Einträge von oder über einen Benutzer exportierbar mit `./console exp:audit export --subject-user=<id>` |
| Löschung | Das Entfernen eines Benutzerkontos löscht die Mail-Einstellungen und anonymisiert das Einwilligungsprotokoll; Audit-Einträge werden pseudonymisiert |
| Datensparsamkeit in Logs | Audit-Adressen gekürzt, Sitzungs-IDs gehasht; Request-Logs mit `MaskIP=enabled`; der Log-Viewer maskiert Adressen und Geheimnisse |
| Speicherbegrenzung | Audit: 90 Tage live und 2 Jahre archiviert; `settings/logviewer.ini [RotationSettings] MaxAgeDays[]` für einige Logs |
| Keine Aufrufe Dritter aus der Administration | Admin-Skripte werden lokal ausgeliefert; admin4 lädt keine Webfonts oder Skripte von anderen Hosts |

Status auf der Seite der Standards: **Teilweise erfüllt**. Der Betreiber der Website entscheidet weiterhin über
Zwecke und Rechtsgrundlagen, führt das Verzeichnis der Verarbeitungstätigkeiten, schreibt die Datenschutzerklärung
und beantwortet Anfragen. Hoster haben eine eigene Seite:
[Compliance und DSGVO für Hoster](../../hosting/sicherheit/compliance-und-dsgvo.md).

## Bekannte Lücken, die Sie einplanen sollten

Aus [Datenschutzstandards](../../../standards/datenschutz.md#bekannte-lücken):

- Keine einzelne Funktion „alle meine Daten exportieren“ oder „mich löschen“; die Daten einer Person werden über
  getrennte Werkzeuge erreicht.
- Das Entfernen von Benutzern erfolgt manuell und Konto für Konto.
- Kernel-Logs wie `error.log` und `debug.log` werden nach Größe rotiert, standardmäßig ohne Altersgrenze.
- Formulare zur Informationssammlung speichern, was Besucher eingeben; die Website muss einen Einwilligungstext und
  eine Aufbewahrungsregel ergänzen.
- Suchbegriffe werden gespeichert und können personenbezogene Daten enthalten.

Bieten Sie an, diese als Teil Ihrer Dienstleistung zu übernehmen, und sagen Sie, welche der Kunde selbst übernehmen
muss.

## Wo Daten in der Praxis abfließen

| Ort | Vorbeugung |
|---|---|
| Support-Tickets und Fehlermeldungen | Logs vor dem Einfügen bereinigen; Platzhalter wie `customer-site.example` |
| Screenshots | Testkonten; zuschneiden oder unkenntlich machen |
| Datenbank-Dumps zur Fehlersuche | Eine Reproduktion teilen, keinen Dump |
| Staging-Kopien | Anonymisieren; Staging wie die Produktion schützen |
| Demo-Websites | Nie echte Daten ([Demo-Websites für Wiederverkäufer](../angebote/demo-websites.md)) |
| Weitergegebene Leads | Zuerst die Einwilligung festhalten ([Leads übergeben als Wiederverkäufer](../verkauf/leads-uebergeben.md)) |
| Fallstudien | Schriftliche Freigabe ([Fallstudien für Wiederverkäufer](../verkauf/fallstudien.md)) |

Quelle: [Kundendaten schützen](../../partner/kundendaten-schuetzen.md).

## Checkliste

- [ ] Rollen bestätigt; Bedingungen der Verarbeitung in jedem Vertrag.
- [ ] Liste der Unterauftragsverarbeiter aktuell und mit Kunden geteilt.
- [ ] Personal zu Platzhaltern, Screenshots und Dumps geschult.
- [ ] Staging anonymisiert; Demos ohne echte Daten.
- [ ] Aufbewahrung der Logs festgelegt, wo der Kunde es braucht.
- [ ] Verfahren zur Meldung von Datenpannen aufgeschrieben.
- [ ] Löschroutine für Exporte, Dumps und ausgeschiedene Kunden.

## Verwandte Seiten

- [Kundendaten schützen](../../partner/kundendaten-schuetzen.md)
- [Aufbewahrung von Logs und Daten](../../hosting/sicherheit/aufbewahrung-von-logs-und-daten.md)
- [DSGVO-Anfragen (für Website-Betreiber)](../../kunden/handel/dsgvo-anfragen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
