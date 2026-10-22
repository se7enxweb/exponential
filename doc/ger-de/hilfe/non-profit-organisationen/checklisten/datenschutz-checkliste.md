---
title: Datenschutz-Checkliste für Non-Profit-Organisationen
audience: non-profit, businesses, admin, government
category: non-profits
order: 50
summary: Eine Datenschutz-Checkliste für die Website einer Non-Profit-Organisation mit Exponential, die Sie zum Start, jährlich und bei jedem neuen Formular oder Dienst durcharbeiten - Bestandsaufnahme, Datensparsamkeit, Zugänge und Zwei-Faktor-Anmeldung, Aufbewahrung und Löschung, Einwilligung für Newsletter, Auftragsverarbeiter in der Datenschutzerklärung, Anfragen von Betroffenen, Exporte und Sicherungen sowie Vorfälle. Grundlagen, keine Rechtsberatung.
keywords: Datenschutz-Checkliste, DSGVO, Bestandsaufnahme, Datensparsamkeit, Zugänge, Zwei-Faktor-Anmeldung, Aufbewahrung, Löschung, Einwilligung, Double-Opt-in, Auftragsverarbeiter, Datenschutzerklärung, Anfragen, Exporte, Sicherungen, Vorfälle, Non-Profit, data protection checklist, privacy checklist, GDPR, minimization, retention, consent, double opt-in, processors, privacy notice, incidents
type: help
---
# Datenschutz-Checkliste für Non-Profit-Organisationen

Arbeiten Sie diese Liste zum Start, einmal im Jahr und immer dann durch, wenn Sie ein Formular, eine Liste oder einen
externen Dienst hinzufügen. Jede Zeile verlinkt auf die Seite, die sie erklärt. Die Erläuterung steht unter
[Datenschutz für Spender und Ehrenamtliche](../betrieb/datenschutz-fuer-spender-und-ehrenamtliche.md).

> **Hinweis**
> Grundlagen, keine Rechtsberatung. Was Ihr Recht verlangt, entscheiden Ihr Datenschutzbeauftragter oder Ihr Anwalt.

## Inhalt

1. [Bestandsaufnahme](#bestandsaufnahme)
2. [Weniger erheben](#weniger-erheben)
3. [Zugänge](#zugänge)
4. [Aufbewahrung und Löschung](#aufbewahrung-und-löschung)
5. [Einwilligung](#einwilligung)
6. [Auftragsverarbeiter und die Datenschutzerklärung](#auftragsverarbeiter-und-die-datenschutzerklärung)
7. [Anfragen von Betroffenen](#anfragen-von-betroffenen)
8. [Exporte, Sicherungen und Vorfälle](#exporte-sicherungen-und-vorfälle)
9. [Abzeichnung](#abzeichnung)

## Bestandsaufnahme

- [ ] Jeder Ort, an dem personenbezogene Daten liegen, ist aufgeführt: Einsendungen, Konten, Bestellungen,
      Abonnements, Logs, Audit-Protokoll, Suchbegriffe, Exporte, Sicherungen ([Personenbezogene Daten in Exponential](../../unternehmen/sicherheit/personenbezogene-daten-in-exponential.md)).
- [ ] Für jeden gibt es eine verantwortliche Person in der Organisation.
- [ ] Ein Verzeichnis der Verarbeitungstätigkeiten wird geführt, sofern Ihr Recht es verlangt ([Verzeichnis der Verarbeitungstätigkeiten](../../verwaltung/datenschutz/verzeichnis-von-verarbeitungstaetigkeiten.md)).

## Weniger erheben

- [ ] Jedes Formularfeld hat einen Zweck ([Arbeitsblatt Felder des Ehrenamtsformulars](../vorlagen/arbeitsblatt-felder-des-ehrenamtsformulars.md)).
- [ ] Keine sensiblen Daten (Gesundheit, Religion, Aufenthaltsstatus) über die Website erhoben ohne eine Entscheidung
      der Ansprechperson für den Datenschutz.
- [ ] Suchstatistiken nur eingeschaltet, wenn sie genutzt werden ([Wirkung messen](../foerdermittel/wirkung-mit-website-daten-messen.md)).

## Zugänge

- [ ] `infocollector/read` nur für Koordinatoren.
- [ ] Verwaltung von Shop und Newsletter nur für namentlich benannte Personen.
- [ ] `audit/read` nur für Administratoren.
- [ ] Zwei-Faktor-Anmeldung für alle mit Zugang zu personenbezogenen Daten ([Zwei-Faktor-Anmeldung](../../../anleitungen/zwei-faktor-anmeldung.md)).
- [ ] Konten von Ausgeschiedenen deaktiviert ([Wenn Ehrenamtliche gehen](../betrieb/wenn-ehrenamtliche-gehen.md)).

## Aufbewahrung und Löschung

- [ ] Eine schriftlich festgelegte Aufbewahrungsfrist für jede Art von Daten ([Aufbewahrung und Löschung von Daten](../../unternehmen/sicherheit/aufbewahrung-und-loeschung-von-daten.md)).
- [ ] Alte Einsendungen planmäßig entfernt ([Bewerbungen von Ehrenamtlichen bearbeiten](../ehrenamtliche/bewerbungen-von-ehrenamtlichen-bearbeiten.md#ein-aufbewahrungsplan)).
- [ ] Anmeldungen zu Veranstaltungen nach der Veranstaltung gelöscht.
- [ ] Rotation der Sicherungen bekannt, damit gelöschte Daten rechtzeitig auch aus den Sicherungen verschwinden.

## Einwilligung

- [ ] Newsletter nur per Double-Opt-in; Spender werden nicht ohne eigenes Abonnement eingetragen ([Opt-in und Einwilligung](../../unternehmen/newsletters/opt-in-und-einwilligung.md)).
- [ ] Abmeldelink in jeder Ausgabe ([Abmeldung mit einem Klick](../../verwaltung/datenschutz/abmelden-mit-einem-klick.md)).
- [ ] Einwilligungstext neben den Kontrollkästchen in Formularen ([Einwilligung und Formulare](../../unternehmen/sicherheit/einwilligung-und-formulare.md)).
- [ ] Cookies und Analyse: Einwilligung dort eingeholt, wo sie erforderlich ist ([Cookie-Banner und Einwilligung](../../verwaltung/datenschutz/cookie-banner-und-einwilligung.md)).
- [ ] Fotos von Menschen nur mit Erlaubnis veröffentlicht.

## Auftragsverarbeiter und die Datenschutzerklärung

- [ ] Die Datenschutzerklärung nennt: Hoster, Zahlungsanbieter oder Spendenplattform, Mailversand, CAPTCHA-Anbieter,
      Analyse, Karten und Video, KI-Anbieter, falls verwendet ([Gliederung einer Datenschutzerklärung](../../verwaltung/datenschutz/gliederung-einer-datenschutzerklaerung.md)).
- [ ] Verträge zur Auftragsverarbeitung bestehen, wo sie erforderlich sind.
- [ ] Für jeden Auftragsverarbeiter ist bekannt, wo (in welchem Land) die Daten gespeichert werden.

## Anfragen von Betroffenen

- [ ] Eine benannte Person bearbeitet Anfragen auf Auskunft, Berichtigung und Löschung ([Auskunftsrecht](../../verwaltung/datenschutz/auskunftsrecht.md),
      [Recht auf Löschung](../../verwaltung/datenschutz/recht-auf-loeschung.md)).
- [ ] Anfragen und Antworten protokolliert ([Vorlage für ein DSGVO-Anfragenprotokoll](../../kunden/handel/vorlage-dsgvo-anfragenprotokoll.md)).

## Exporte, Sicherungen und Vorfälle

- [ ] CSV-Exporte nach der Verwendung gelöscht; das Audit-Protokoll erfasst jeden Export mit seiner Zeilenzahl
      ([Gesammelte Informationen](../../../anleitungen/gesammelte-informationen.md)).
- [ ] Sicherungen sicher und mit eingeschränktem Zugriff aufbewahrt.
- [ ] Ein Plan für eine Datenpanne: wer entscheidet, wer wen informiert, innerhalb welcher Frist
      ([Vorlage für Vorfallnotizen](../../kunden/gesund-halten/vorlage-vorfallnotizen.md)).

## Abzeichnung

```text
Data protection contact .................. date ________
Director ................................. date ________
Next review .............................. date ________
```

## Verwandte Seiten

- [Checklisten für Non-Profit-Organisationen](README.md)
- [Grundlagen der DSGVO](../../unternehmen/sicherheit/dsgvo-grundlagen.md)
- [Checkliste Privacy by Design](../../verwaltung/datenschutz/checkliste-datenschutz-durch-technikgestaltung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
