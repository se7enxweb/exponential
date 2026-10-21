---
title: Ein Supportangebot bei einem Partner anfragen
audience: government, project-manager, partner
category: public-sector
order: 110
summary: Schritt für Schritt bei 7x oder einem Exponential-Partner ein Supportangebot anfragen, das eine Behörde vergleichen kann - Umfang, Schweregrade, von Ihnen gewählte Reaktionszeiten, Stunden, Sicherheitsupdates, Eskalation, Berichte und Ausstieg - ohne personenbezogene Daten in der Anfrage.
keywords: Supportangebot, Angebotsanfrage, Partner, 7x, Supportvertrag, Schweregrade, Reaktionszeiten, SLA, Sicherheitsupdates, Aufgabe zur Beschaffung, support quote, request for quotation, partner, support contract, severities, response times, security updates, procurement task
type: help
---
# Ein Supportangebot bei einem Partner anfragen

**Wer:** der Projektleiter oder Vertragsverantwortliche. **Wann:** vor oder nach dem Aufbau, für die Betriebsphase.
Hintergrund: [Unterstützung erhalten](../../../projekt/unterstuetzung-erhalten.md),
[Einen Supportpartner bewerten](../../../../../eng-us/help/government/procurement/evaluation/evaluating-a-support-partner.md).

> **Hinweis** Diese Seite beschreibt gängige Praxis; sie ist keine Rechtsberatung. Folgen Sie den Vergaberegeln Ihrer
> Organisation, die oberhalb einer Schwelle eine förmliche Ausschreibung verlangen können.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Eine Vorlage für die Anfrage](#eine-vorlage-für-die-anfrage)
4. [Ergebnis](#ergebnis)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Wissen Sie, was Sie betreiben: Versionen, Erweiterungen, Engine, Datenbank, aus **Setup > Systeminformationen**,
  deren Zusammenfassung Passwörter und Schlüssel auslässt ([Systeminformationen](../../../../anleitungen/systeminformationen.md)).
- Wissen Sie, wer anbieten könnte: 7x ([Wer ist 7x?](../../../wer-ist-7x.md)) und Partner
  ([Partner: hier beginnen](../../../partner/partner-hier-beginnen.md)).
- Wissen Sie, was das Projekt selbst nicht anbietet: kein SLA
  ([SLA-Optionen für das Hosting von Exponential](../../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md)).

## Schritte

1. **Legen Sie den Umfang fest**: nur Support für die Anwendung oder auch Hosting; Ihre eigenen Erweiterungen
   eingeschlossen oder nicht.
2. **Legen Sie Schweregrade fest** in Ihren Worten (Site ausgefallen, Sicherheit, wichtige Funktion kaputt, gering), und
   fragen Sie je Schweregrad nach einer Reaktion und einem Lösungsansatz; wählen Sie die Zeiten, die Sie brauchen.
3. **Fragen Sie nach Sicherheitsupdates**: wie Releases beobachtet werden und wie schnell sie eingespielt werden.
4. **Fragen Sie nach routinemäßigen Updates**: inbegriffen oder nach Stunden, und Stunden je Update für Ihre Site.
5. **Fragen Sie nach dem Personal**: wer, wie viele Personen Exponential kennen, Vertretung außerhalb der Bürozeiten.
6. **Fragen Sie nach der Eskalation**: wie sie bei Problemen im Upstream das Projekt erreichen; Partner haben einen
   eigenen Weg ([Eskalationen und Sicherheitsprobleme](../../../partner/eskalationen-und-sicherheitsprobleme.md)).
7. **Fragen Sie nach Berichten und Ausstieg**: Inhalt des Monatsberichts; Mitwirkung bei der Übergabe.
8. **Schicken Sie dieselbe Anfrage** an jeden Lieferanten und vergleichen Sie auf derselben Grundlage.

## Eine Vorlage für die Anfrage

```text
Request for a support quote - <body>                         reply by <date>
Site: Exponential <version>, extensions <list>, engine <...>, database <...>, hosted by <...>
Scope: <application support | application + hosting>; custom extensions: <list>
Severities and the response we need: S1 <time>, S2 <time>, S3 <time>, S4 <time>, hours <...>
Security updates: applied within <time> of release
Routine updates: <included | hours per update>
Please state: staff and cover, escalation to the project, monthly report, price per <period>,
              hourly rate for extra work, notice period, exit cooperation.
```

## Ergebnis

Vergleichbare Angebote, die dieselben Fragen beantworten.

## Fallstricke

- Passwörter, Datenbankadressen oder personenbezogene Daten in der Anfrage senden
  ([Unterstützung erhalten](../../../projekt/unterstuetzung-erhalten.md#so-fragen-dass-sie-eine-antwort-bekommen)).
- Die Stufe eines Partners im Programm als Versprechen an Sie behandeln; was zählt, ist der eigene Vertrag des Partners
  ([Support-Stufen und Reaktionszeiten](../../../partner/support-stufen-und-reaktionszeiten.md)).

## Verwandte Seiten

- [Aufgaben zu Beschaffung und Schulung](README.md)
- [Supportverträge](../../../../../eng-us/help/government/procurement/total-cost-of-ownership/support-contracts.md)
- [Klausel zu Support und Wartung](../../../../../eng-us/help/government/procurement/tender-templates/support-and-maintenance-clause.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
