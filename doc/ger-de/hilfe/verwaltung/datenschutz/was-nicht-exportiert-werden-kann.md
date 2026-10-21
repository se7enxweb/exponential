---
title: Was nicht exportiert werden kann
audience: government, admin, project-manager, developer
category: public-sector
order: 160
summary: Für öffentliche Stellen, die planen, wie sie Auskunfts- und Übertragbarkeitsanträge beantworten - die Grenzen der Exporte von Exponential (kein einheitlicher Export der Daten einer Person, Konten als Inhaltsobjekte, Formulareinsendungen pro Formular, pseudonymisierter Index, maskierte Protokolle, Daten außerhalb von Exponential) und Möglichkeiten, die Lücke zu schließen.
keywords: Grenzen des Exports, Datenübertragbarkeit, Auskunftsantrag, kein Export aller meiner Daten, Benutzer-Inhaltsobjekt, Informationssammlung, pseudonymisiert, maskierte Protokolle, Webserver-Protokolle, Datensicherungen, eigener Export, Entwickler, öffentlicher Sektor, export limits, data portability, access request, custom export
type: help
---
# Was nicht exportiert werden kann

Diese Seite richtet sich an Datenschutzbeauftragte, Administratoren und Entwickler. Wenn Sie die Grenzen im Voraus
kennen, können Sie das Verfahren für Auskunftsanträge planen und, wenn Anträge häufig sind, einen eigenen Export in
Auftrag geben. Die Fakten stammen aus [Datenschutzstandards: Bekannte Lücken](../../../../eng-us/standards/privacy.md#known-gaps).

> **Hinweis** Diese Seite beschreibt Funktionen und gängige Praxis; sie ist keine Rechtsberatung. Prüfen Sie das für
> Ihre Organisation geltende Recht mit Ihrer Rechtsberatung.

## Inhalt

1. [Die Grenzen](#die-grenzen)
2. [Warum](#warum)
3. [Möglichkeiten](#möglichkeiten)
4. [Verwandte Seiten](#verwandte-seiten)

## Die Grenzen

| Grenze | Folge |
|---|---|
| Keine einheitliche Funktion „alle meine Daten exportieren“ | jeder Ort wird einzeln exportiert (siehe „Das Auskunftsrecht“) |
| Ein Benutzerkonto ist ein Inhaltsobjekt | seine Felder werden an seinem Knoten gelesen; über die CSV von „Benutzerkonten prüfen“ hinaus gibt es keinen eigenen Kontoexport |
| Formulareinsendungen werden pro Formular exportiert | Sie exportieren die CSV jedes Formulars und wählen die Zeilen der Person aus |
| Audit-Index nach 90 Tagen pseudonymisiert | `exp:audit export --subject-user` findet pseudonymisierte Einträge weiterhin; die vollständigen Werte bleiben in den Archiven |
| Protokolle werden beim Anzeigen maskiert | maskierte Protokolle können keine Person identifizieren; unmaskiertes Lesen erfordert `setup/unmasklogs` und wird aufgezeichnet |
| Suchbegriffen ist keine Person zugeordnet | sie lassen sich keiner antragstellenden Person zuordnen |
| Die Sperrliste speichert Hashes | sie kann bestätigen, dass eine Adresse aufgeführt ist, sie aber nicht auflisten |
| Protokolle von Webserver, Velocity, request-shield und Mailserver | außerhalb von Exponential: fragen Sie den Hoster |
| Datensicherungen | nicht pro Person durchsucht; sie laufen mit Ihrer Sicherungsrotation aus |

## Warum

Das Inhaltsmodell von Exponential speichert viele Arten von Daten als Inhaltsobjekte in Klassen, die Ihre Website
definiert; ein allgemeiner Export „alles über diese Person“ müsste wissen, welche Ihrer Klassen und Felder
personenbezogen sind. Das Projekt stellt eigene Exporte dort bereit, wo ihm das Datenmodell gehört
(E-Mail-Einstellungen, Einwilligungsprotokoll, Audit).

## Möglichkeiten

- **Ein Verfahren** mit einer Checkliste pro Ort (die Bestandsaufnahme in „Wo personenbezogene Daten in Exponential
  liegen“).
- **Ein eigener Export** durch einen Entwickler, für Ihre Klassen und Formulare: Die API der persistenten Objekte und
  die Fetch-Funktionen der Templates geben Zugriff auf die Daten; legen Sie ihn in eine eigene Erweiterung und schützen
  Sie ihn mit einer Richtlinie.
- **Weniger Orte**: Minimieren Sie, was Sie erheben (siehe „Datenminimierung“), und entfernen Sie
  Formulareinsendungen, wenn Vorgänge abgeschlossen sind.

## Verwandte Seiten

- [Das Auskunftsrecht](auskunftsrecht.md)
- [Datenschutzstandards](../../../../eng-us/standards/privacy.md)
- [Entwicklung von Erweiterungen](../../../../eng-us/help/extension-development/README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
