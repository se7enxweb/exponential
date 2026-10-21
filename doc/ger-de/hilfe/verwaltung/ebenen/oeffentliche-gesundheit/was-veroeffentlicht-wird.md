---
title: Was eine Stelle des öffentlichen Gesundheitsdienstes veröffentlicht
audience: government, project-manager, website-creator
category: public-sector
order: 20
summary: Die Inhalte, die Gesundheitsämter und Behörden des öffentlichen Gesundheitsdienstes typischerweise veröffentlichen - Gesundheitshinweise, Warnungen, Dienste und Sprechstunden, Leitlinien für Fachleute, Daten und Berichte - und was jede Art von Exponential braucht.
keywords: Website des öffentlichen Gesundheitsdienstes, Gesundheitshinweise, Gesundheitsthemen, Warnungen, Ausbruch, Impfung, Sprechstunden, Leitlinien für Fachleute, Surveillance-Daten, Berichte, Umfang, public health website, health advice, health topics, alerts, outbreak, vaccination, clinics, professional guidance, surveillance data, reports, scope
type: help
---
# Was eine Stelle des öffentlichen Gesundheitsdienstes veröffentlicht

Diese Seite richtet sich an Projektverantwortliche und Website-Ersteller eines Gesundheitsamts oder einer Behörde des
öffentlichen Gesundheitsdienstes, die den Umfang einer Website festlegen. Sie listet auf, was solche Stellen
typischerweise veröffentlichen (allgemeine Praxis im öffentlichen Sektor), und was jede Art von Inhalt braucht.

## Inhalt

1. [Die Inhalte](#die-inhalte)
2. [Was das CMS können muss](#was-das-cms-können-muss)
3. [Was anderswo hingehört](#was-anderswo-hingehört)
4. [Zu klärende Fragen](#zu-klärende-fragen)

## Die Inhalte

| Inhalt | Zielgruppe | Rhythmus |
|---|---|---|
| Gesundheitshinweise nach Thema: Erkrankungen, Vorbeugung, gesunde Lebensweise, Impfung | die Öffentlichkeit | in einem Zyklus überprüft und wenn sich die Evidenz ändert |
| Warnungen: Ausbrüche, Hitze, Luftqualität, Wasser, Produktwarnungen | die Öffentlichkeit, Medien | dringend; während des Ereignisses aktualisiert |
| Dienste: Sprechstunden, Impfstellen, Früherkennungsprogramme, wie man einen Termin bucht | die Öffentlichkeit | ändert sich oft |
| Leitlinien für Fachleute: Falldefinitionen, Meldepflichten, Verfahrensanweisungen | Ärztinnen und Ärzte, Schulen, Arbeitgeber | ändert sich mit der Evidenz |
| Daten und Berichte: Surveillance-Zahlen, jährliche Gesundheitsberichte | Fachleute, Medien, Forschende | wöchentlich bis jährlich |
| Kampagnen | Zielgruppen | saisonal |
| Nachrichten und Presse | Medien | häufig während Ereignissen |
| Über uns, Kontakt, Stellen | alle | jährlich |

## Was das CMS können muss

| Bedarf | Exponential | Lesen |
|---|---|---|
| Medizinische Freigabe vor dem Veröffentlichen | ein Ereignis *Freigeben* mit medizinischen Freigebenden | [Workflows](../../../../anleitungen/workflows.md) |
| Warnungen in Minuten veröffentlicht, von einem Bereitschaftsteam | von der Freigabe ausgenommen; ein Bereich für Warnungen auf der Startseite | [Warnungen in vielen Sprachen](warnungen-mehrsprachig-und-in-einfacher-sprache.md) |
| Hinweise nach Plan überprüft | ein Attribut für das Überprüfungsdatum und eine Liste überfälliger Seiten | [Inhaltsmodell für den öffentlichen Gesundheitsdienst](inhaltsmodell.md) |
| Viele Sprachen | Übersetzungen je Objekt, ein Siteaccess je Sprache | [Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md) |
| Last während eines Ausbruchs | Caches, statischer Cache, Velocity | [Leistung und Caching](../../../ersteller/website-ersteller/leistung-und-caching.md) |
| Meldungen an Folgende | Benachrichtigungen, RSS, Newsletter | [RSS-Feeds](../../../../anleitungen/rss-feeds.md) |
| Bereich für Fachleute | ein eigener Ordner; standardmäßig öffentlich, nur wo nötig eingeschränkt | [Sektionen](../../../../anleitungen/sektionen.md) |

## Was anderswo hingehört

| Inhalt | Wo |
|---|---|
| Patientenakten, Testergebnisse, Terminbuchungen | klinische und Buchungssysteme; die Website verlinkt |
| Fallmeldungen durch Fachleute | ein sicheres Meldesystem; nie ein Formular des Informationssammlers mit Patientendaten |
| Live-Dashboards mit Surveillance-Daten | eine Datenplattform; die Website verlinkt oder bettet mit Sorgfalt für die Barrierefreiheit ein |

Ein Formular der Website darf nie Gesundheitsdaten über identifizierbare Personen erheben, sofern Ihre oder Ihr
Datenschutzbeauftragte(r) Zweck, Sicherheit und Aufbewahrung nicht genehmigt hat.

## Zu klärende Fragen

- [ ] Wer gibt Gesundheitshinweise frei, und wer darf nachts eine Warnung veröffentlichen?
- [ ] Welche Sprachen werden für Warnungen gebraucht, und wer übersetzt innerhalb von Stunden?
- [ ] Welchen Verkehr hat der geschäftigste Tag vergangener Ereignisse gebracht?
- [ ] Welche Themen werden wie oft überprüft?

## Verwandte Seiten

- [Öffentliche Gesundheit](README.md)
- [Inhaltsmodell für den öffentlichen Gesundheitsdienst](inhaltsmodell.md)
- [Ihre Website planen](../../../ersteller/website-ersteller/ihre-website-planen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
