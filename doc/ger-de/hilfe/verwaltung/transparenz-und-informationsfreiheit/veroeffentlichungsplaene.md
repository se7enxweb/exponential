---
title: Veröffentlichungspläne
audience: government, project-manager, editor, website-creator
category: public-sector
order: 40
summary: Wie Sie in Exponential einen Veröffentlichungsplan erstellen (einen Wegweiser zu den Informationen, die eine öffentliche Stelle veröffentlicht), als eine Seite pro Informationsart mit Links, Verantwortlichen und Formaten, und wie Sie ihn genau halten.
keywords: Veröffentlichungsplan, Wegweiser zu Informationen, Informationsverzeichnis, Informationsarten, Transparenz, Informationsfreiheit, proaktive Veröffentlichung, publication scheme, guide to information, information asset register, classes of information, transparency, FOI, proactive publication
type: help
---
# Veröffentlichungspläne

Diese Seite richtet sich an Führungskräfte und Websiteersteller, die einen Wegweiser zu den Informationen
veröffentlichen müssen, die ihre Stelle bereitstellt, oft Veröffentlichungsplan genannt (englisch „publication
scheme“). Ob einer vorgeschrieben ist und welche Gliederung er haben muss, hängt von Ihrer Rechtsordnung ab. In
Exponential ist ein Veröffentlichungsplan gewöhnlicher Inhalt: eine Seite, die Informationsarten auflistet und jeweils
dorthin verlinkt, wo die Informationen liegen.

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Was ein Veröffentlichungsplan enthält](#was-ein-veröffentlichungsplan-enthält)
2. [Ihn als Inhalt aufbauen](#ihn-als-inhalt-aufbauen)
3. [Eine Klasse für die Einträge](#eine-klasse-für-die-einträge)
4. [Ihn genau halten](#ihn-genau-halten)
5. [Checkliste](#checkliste)

## Was ein Veröffentlichungsplan enthält

Für jede Informationsart sagt ein Veröffentlichungsplan üblicherweise:

- um welche Informationen es sich handelt;
- wo sie zu finden sind (ein Link) oder wie man sie anfordert;
- das Format (Webseite, PDF, CSV, Papier auf Anfrage);
- ob Gebühren anfallen;
- welche Sprachfassungen verfügbar sind.

## Ihn als Inhalt aufbauen

1. Legen Sie unterhalb des Transparenzbereichs einen Ordner „Veröffentlichungsplan“ an.
2. Schreiben Sie im Ordner eine Einleitung: was der Plan ist, wie man nicht aufgeführte Informationen anfordert,
   das Kontaktpostfach.
3. Legen Sie pro Informationsart ein Unterobjekt an (siehe die Klasse unten).
4. Lassen Sie die Vollansicht des Ordners die Unterobjekte mit ihrer Zusammenfassung und ihrem Link auflisten.

Da jeder Eintrag ein Objekt ist, hat er Versionen, lässt sich in jede Inhaltssprache übersetzen, und Änderungen stehen
im Prüfprotokoll ([Inhaltsgeschichte](../../../anleitungen/inhaltsverlauf.md), [Prüfprotokoll](../../../../eng-us/features/6.0/audit-trail.md)).

## Eine Klasse für die Einträge

Legen Sie unter **Setup > Klassen** eine Klasse `scheme_entry` an
([Inhaltsklassen für Websites](../../../../eng-us/help/creators/website-creators/content-classes-for-websites.md)):

| Attribut | Datentyp | Hinweis |
|---|---|---|
| Titel | Textzeile | die Informationsart |
| Beschreibung | Formatierter Text | was sie enthält |
| Wo zu finden | Objekt Relations Liste | Links zu den Ordnern oder Dokumenten auf der Website |
| Externe Adresse | URL | wenn sie anderswo veröffentlicht wird |
| Formate | Auswahl, mehrfach | Webseite, PDF, CSV, Papier |
| Gebühr | Textzeile | „kostenlos“ oder die Regel |
| Verantwortlich | Textzeile | ein Team oder eine Funktion, keine Person |
| Zuletzt geprüft | Datum | wird auf der Seite ausgegeben |

Objektrelationen halten Links korrekt, wenn das Ziel verschoben wird, weil sie auf das Objekt zeigen, nicht auf eine
Adresse ([Relationen und Wiederverwendung](../../../../eng-us/help/creators/website-creators/relations-and-reuse.md)).

## Ihn genau halten

- Prüfen Sie jeden Eintrag in dem Abstand, den Ihre Richtlinie vorgibt; aktualisieren Sie „Zuletzt geprüft“.
- **Setup > URL-Verwaltung** zeigt externe Links, die nicht mehr funktionieren.
- **Ungültige Relationen** unter Setup (`/contentcheck/brokenrelations`) findet Relationen zu Inhalten, die entfernt
  wurden.
- Wenn eine Informationsart eingestellt wird, behalten Sie den Eintrag mit einem Hinweis, statt ihn zu entfernen.

## Checkliste

- [ ] Jede Informationsart hat einen Eintrag, eine verantwortliche Stelle und ein Format.
- [ ] Jeder Eintrag verlinkt auf die Informationen oder sagt, wie man sie anfordert.
- [ ] Der Plan existiert in jeder Amtssprache der Website.
- [ ] Prüfdaten werden gepflegt.

## Verwandte Seiten

- [Proaktive Veröffentlichung](proaktive-veroeffentlichung.md)
- [Relationen und Wiederverwendung](../../../../eng-us/help/creators/website-creators/relations-and-reuse.md)
- [Link- und Inhaltsprüfungen](../../../../eng-us/help/creators/website-creators/link-and-content-checks.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
