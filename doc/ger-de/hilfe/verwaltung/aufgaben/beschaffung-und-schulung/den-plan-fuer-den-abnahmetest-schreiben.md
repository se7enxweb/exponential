---
title: Den Plan für den Abnahmetest schreiben
audience: government, project-manager, admin, editor
category: public-sector
order: 230
summary: Schritt für Schritt den Plan schreiben, mit dem eine Behörde eine gelieferte Exponential-Site abnimmt - ein Test je Anforderung, Tests zu Funktion, Barrierefreiheit, Sicherheit, Datenschutz, Leistung, Betrieb und Dokumentation, Kriterien für das Bestehen und der Umgang mit Fehlern.
keywords: Plan für den Abnahmetest, Abnahmetests, Benutzerabnahme, Anforderungen, Testfälle, Test der Barrierefreiheit, Sicherheitstest, Leistung, Abzeichnung, Aufgabe zur Beschaffung, acceptance test plan, acceptance testing, user acceptance, requirements, test cases, accessibility test, security test, performance, sign-off, procurement task
type: help
---
# Den Plan für den Abnahmetest schreiben

**Wer:** der Projektleiter mit den Fachleuten des Bewertungsteams. **Wann:** vor der Unterzeichnung, damit der Vertrag
darauf verweisen kann; verfeinern Sie ihn während des Aufbaus. Klausel:
[Klausel zu Abnahmetests](../../../../../eng-us/help/government/procurement/tender-templates/acceptance-testing-clause.md).

> **Hinweis** Diese Seite beschreibt gängige Praxis; sie ist keine Rechtsberatung. Lassen Sie die Bedingungen der
> Abnahme von Ihrer Rechtsberatung prüfen.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Testbereiche](#testbereiche)
4. [Ergebnis](#ergebnis)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Die Anforderungen, jede mit einer Kennung
  ([Wie man prüfbare Anforderungen schreibt](../../../../../eng-us/help/government/procurement/requirements/how-to-write-verifiable-requirements.md)).
- Eine Staging-Umgebung, die der Produktion entspricht.

## Schritte

1. **Ein Test je Anforderung**, mit der Kennung der Anforderung, Schritten, Testdaten und erwartetem Ergebnis.
2. **Benennen Sie, wer testet**: Redakteure testen die Bearbeitung, die Verantwortlichen für Barrierefreiheit testen die
   Barrierefreiheit und so weiter.
3. **Legen Sie Kriterien für das Bestehen** und Schweregrade für Fehler fest; entscheiden Sie, welche Schweregrade die
   Abnahme blockieren.
4. **Legen Sie den Ablauf fest**: Testzeitraum, wie Fehler gemeldet werden, Zyklen aus Korrektur und erneutem Test,
   abschließende Abzeichnung.
5. **Verwenden Sie erfundene Testdaten**, niemals personenbezogene Daten aus der Produktion.
6. **Halten Sie die Ergebnisse** je Test mit Datum, Tester und Nachweis **fest**.

## Testbereiche

| Bereich | Beispieltests | Hilfe |
|---|---|---|
| Funktion | jeden Inhaltstyp anlegen, freigeben und veröffentlichen; Formulare; Suche; Sprachen | [Checkliste zur redaktionellen Qualität](../../checklisten/redaktionelle-qualitaet.md) |
| Barrierefreiheit | die vereinbarte Bewertung von Templates und wichtigen Seiten; Aufgaben nur mit der Tastatur | [Checkliste zur Barrierefreiheit](../../checklisten/checkliste-barrierefreiheit.md) |
| Sicherheit | Grundhärtung, 2FA für Administratoren, Header, Kette des Audits verifiziert | [Checkliste zur Sicherheit](../../checklisten/checkliste-sicherheit.md) |
| Datenschutz | Texte zur Einwilligung, Dritte wie genehmigt, Einstellungen zur Aufbewahrung | [Checkliste zum Datenschutz](../../checklisten/checkliste-datenschutz.md) |
| Leistung | wichtige Seiten unter der vereinbarten Last, gemessen mit `exp:benchmark` | [Benchmarking](../../../../anleitungen/benchmarking.md) |
| Betrieb | Sicherung und Wiederherstellung auf Staging, ein routinemäßiges Update, Cronjobs, Monitoring | [Bereitschaft der Sicherungen](../../checklisten/bereitschaft-fuer-datensicherungen.md), [Bereitschaft für Upgrades](../../checklisten/bereitschaft-fuer-aktualisierungen.md) |
| Aktualisierbarkeit | **Setup > Upgrade-Prüfung** zeigt keine geänderten Dateien des Kernels | [Die Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md) |
| Dokumentation und Übergabe | Dokumente gegen die Site geprüft; Administratorkonten für Ihre Mitarbeiter | [Die Dokumentation am Vertragsende übergeben](die-dokumentation-am-vertragsende-uebergeben.md) |
| Ausstieg | eine Übung des Exports auf Staging | [Eine Ausstiegsübung durchführen](eine-ausstiegsuebung-durchfuehren.md) |

## Ergebnis

Ein von beiden Parteien vereinbarter Plan, der „fertig“ in eine Liste bestandener Tests verwandelt.

## Fallstricke

- Nur testen, was die Demo gezeigt hat.
- Keine Kriterien für das Bestehen: Dann wird die Abnahme zu einer Verhandlung.
- Tests zu Barrierefreiheit und Betrieb bis nach dem Go-live aufschieben.

## Verwandte Seiten

- [Aufgaben zu Beschaffung und Schulung](README.md)
- [Checkliste für den Go-live einer öffentlichen Site](../../checklisten/checkliste-fuer-den-livegang.md)
- [Anforderungen Nachweisen zuordnen](../../../../../eng-us/help/government/procurement/requirements/mapping-requirements-to-evidence.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
