---
title: Eine Anforderung zur Datenverarbeitung schreiben
audience: government, project-manager, admin
category: public-sector
order: 40
summary: Schritt für Schritt die Anforderungen an den Datenschutz für eine Ausschreibung einer Exponential-Site und ihres Hostings schreiben - Datenstandort, Auftragsverarbeiter, Dritte, Aufbewahrung, Rechte der Betroffenen und Rückgabe der Daten - auf Grundlage dessen, was die Software bietet und was bei Ihnen bleibt.
keywords: Anforderung zur Datenverarbeitung, DSGVO, Auftragsverarbeiter, Auftragsverarbeitungsvertrag, Datenstandort, Dritte, Aufbewahrung, Rechte, Löschung, Ausschreibung, Aufgabe zur Beschaffung, data processing requirement, GDPR, processor, processing agreement, data location, third parties, retention, rights, erasure, tender, procurement task
type: help
---
# Eine Anforderung zur Datenverarbeitung schreiben

**Wer:** die Beschaffungsverantwortlichen mit dem Datenschutzbeauftragten. **Wann:** beim Entwurf der Anforderungen. Die
Fakten stammen aus den [Datenschutzstandards](../../../../../eng-us/standards/privacy.md).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung. EU: [DSGVO](https://eur-lex.europa.eu/eli/reg/2016/679/oj).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Beispiel](#beispiel)
4. [Ergebnis](#ergebnis)
5. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Listen Sie die personenbezogenen Daten auf, die die Site halten wird: Konten, Formulareinsendungen,
  Newsletter-Abonnenten, Logs ([Checkliste zum Datenschutz](../../../../../eng-us/help/government/checklists/privacy-checklist.md)).
- Wissen Sie, was die Software bietet: Einwilligungsprotokoll, E-Mail-Kategorien mit Opt-in, Löschung beim Entfernen des
  Kontos, Maskierung der Logs und Einstellungen zur Aufbewahrung; und was ihr fehlt: kein „alle meine Daten
  exportieren“, kein Cookie-Banner ([Datenschutzstandards](../../../../../eng-us/standards/privacy.md#known-gaps)).

## Schritte

1. **Benennen Sie die Auftragsverarbeiter**: Hoster, Supportlieferant, jeder Dienst Dritter. Jeder unterzeichnet einen
   Auftragsverarbeitungsvertrag.
2. **Legen Sie den Datenstandort fest** für Produktion, Sicherungen und Supportzugang.
3. **Verbieten Sie unangekündigte Dritte**: keine Erweiterung, die Besucherdaten an Dritte sendet (Analyse, CAPTCHA,
   Anmeldung über soziale Netzwerke, KI-Anbieter), ohne Ihre schriftliche Zustimmung
   ([Datenschutzstandards](../../../../../eng-us/standards/privacy.md#third-party-services-in-shipped-extensions)).
4. **Legen Sie die Aufbewahrung fest**: Formulare, Logs (die allgemeinen Logs des Kernels haben standardmäßig keine
   Altersgrenze), Sicherungen, Audit-Einträge.
5. **Verlangen Sie Unterstützung für die Rechte der Betroffenen**: Der Lieferant dokumentiert, wie die Daten einer Person
   über Konten, Formulare und Newsletter gefunden und exportiert oder gelöscht werden, da es keine einzelne Funktion
   dafür gibt.
6. **Verlangen Sie, dass Test-, Entwicklungs- und Schulungskopien keine personenbezogenen Daten enthalten.**
7. **Verlangen Sie Rückgabe und Löschung** am Ende
   ([Personenbezogene Daten beim Ausstieg](../../../../../eng-us/help/government/procurement/exit-plans/personal-data-at-exit.md)).

## Beispiel

```text
DP-01  The Supplier acts as processor under a processing agreement in the form of Annex <x>.
DP-02  Production data, backups and support access remain in <location>.
DP-03  No component that transmits personal data to a third party is activated without <body>'s prior
       written approval; the Supplier lists every such component in the <period> report.
DP-04  Retention: form submissions <period>; logs <period>; backups <period>; audit records <period>.
DP-05  The Supplier documents and tests a procedure to export and erase one person's data within <time>.
DP-06  Test, development and training copies contain no personal data from production.
```

## Ergebnis

Anforderungen, die die Datenflüsse ausdrücklich und prüfbar machen, abgestimmt auf das, was die Software kann.

## Fallstricke

- Annehmen, die Software sei „DSGVO-konform“: Die Organisation ist der Verantwortliche.
- Sicherungen und Logs in den Regeln zur Aufbewahrung vergessen.
- Eine Erweiterung zur Analyse ohne Einwilligung und Hinweis aktivieren.

## Verwandte Seiten

- [Aufgaben zu Beschaffung und Schulung](README.md)
- [Anforderungen an Privatsphäre und Datenschutz](../../../../../eng-us/help/government/procurement/requirements/privacy-and-data-protection-requirements.md)
- [Klausel zu Datenschutz und Datenverarbeitung](../../../../../eng-us/help/government/procurement/tender-templates/privacy-and-data-processing-clause.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
