---
title: Was Schulen und Hochschulen veröffentlichen
audience: government, project-manager, website-creator
category: public-sector
order: 20
summary: Die Inhalte, die öffentliche Schulen, Schulträger, Fachhochschulen und Universitäten typischerweise veröffentlichen - Zulassung, Studiengänge, Kalender, Nachrichten, Richtlinien, öffentliche Profile, Informationen für Beschäftigte und Lernende - und was jede Art von Exponential braucht.
keywords: Website einer Schule, Website einer Hochschule, Zulassung, Studiengänge, Kurse, akademischer Kalender, Richtlinien, Personalverzeichnis, Informationen für Studierende, Intranet, Umfang, school website, university website, admissions, programs, courses, academic calendar, policies, staff directory, student information, intranet, scope
type: help
---
# Was Schulen und Hochschulen veröffentlichen

Diese Seite richtet sich an Projektverantwortliche und Website-Ersteller einer Schule, eines Schulträgers, einer
Fachhochschule oder einer Universität, die den Umfang einer Website festlegen. Sie listet auf, was
Bildungseinrichtungen typischerweise veröffentlichen (allgemeine Praxis im öffentlichen Sektor), und was jede Art von
Inhalt braucht.

## Inhalt

1. [Öffentliche Inhalte](#öffentliche-inhalte)
2. [Inhalte für Beschäftigte und Lernende](#inhalte-für-beschäftigte-und-lernende)
3. [Was das CMS können muss](#was-das-cms-können-muss)
4. [Was in andere Systeme gehört](#was-in-andere-systeme-gehört)
5. [Zu klärende Fragen](#zu-klärende-fragen)

## Öffentliche Inhalte

| Inhalt | Zielgruppe | Rhythmus |
|---|---|---|
| Zulassung: wie man sich bewirbt, Fristen, Voraussetzungen, Tage der offenen Tür | künftige Lernende, Eltern | jährlicher Zyklus mit festen Fristen |
| Studiengänge und Kurse: was gelehrt wird, Zugangsvoraussetzungen, Abschlüsse | künftige Lernende | jährlich |
| Fachbereiche, Fakultäten, Forschungsgruppen | künftige Lernende, Forschende, Partner | ändert sich mit dem Personal |
| Akademischer Kalender: Halbjahre, Ferien, Prüfungen | alle | jährlich |
| Nachrichten und Veranstaltungen | Gemeinschaft, Presse | wöchentlich |
| Richtlinien: Zulassung, Verhalten, Beschwerden, Schutz von Kindern und Jugendlichen, Datenschutz | Eltern, Lernende, Aufsicht | jährlich überprüft |
| Berichte: Ergebnisse von Schulinspektionen, Leistungsinformationen, Jahresbericht | Eltern, Aufsichtsstellen | jährlich |
| Personen: Profile der öffentlichen Rolle von Beschäftigten (an Hochschulen von Forschenden) | die Öffentlichkeit | mit Personalwechseln |
| Stellen | Bewerberinnen und Bewerber | laufend |

## Inhalte für Beschäftigte und Lernende

| Inhalt | Zielgruppe |
|---|---|
| Stundenpläne und Raumänderungen | Lernende, Beschäftigte (oft aus einem anderen System) |
| Interne Nachrichten und Hinweise | Beschäftigte, Lernende |
| Formulare und Verfahren (Urlaub, Reisekosten, IT-Anfragen) | Beschäftigte |
| Handbücher: Handbuch für Studierende, Handbuch für Beschäftigte | Lernende, Beschäftigte |
| Interne Seiten der Fachbereiche | Mitglieder des Fachbereichs |

## Was das CMS können muss

| Bedarf | Exponential | Lesen |
|---|---|---|
| Subsites von Fachbereichen mit eigenem Aussehen | Siteaccesses und Designs auf einer Installation | [Intranets und Subsites von Fachbereichen](intranets-und-fachbereichs-subsites.md) |
| Eingeschränkte Seiten für Beschäftigte und Lernende | Sektionen, die Anonymous nicht lesen darf; Rollen für Mitglieder | [Sektionen, 13.1](../../../../anleitungen/sektionen.md#131-ein-mitgliederbereich) |
| Fristen, die Listen steuern | Datumsattribute und Fetches | [Datentypen wählen](../../../ersteller/website-ersteller/datentypen-waehlen.md) |
| Veranstaltungen mit einem Kalender | die Klassen Event und Event calendar | [Inhaltsmodell](inhaltsmodell.md) |
| Profile der Beschäftigten an einer Stelle, wiederverwendet | eine Klasse Person (`ng_person` wird mit dem Standardpaket ausgeliefert) und Beziehungen | [Beziehungen und Wiederverwendung](../../../ersteller/website-ersteller/relationen-und-wiederverwendung.md) |
| Suche über viele Fachbereiche | eingebaute Suche oder eZ Find mit Solr | [Suche mit eZ Find und Solr](../../../ersteller/website-ersteller/suche-mit-ezfind-und-solr.md) |
| Handbücher über mehrere Ebenen | die Klasse Documentation page (`documentation_page`) | [sevenx_classes](../../../seitenpakete/sevenx-multisite/sevenx-classes-das-paket-der-inhaltsklassen.md) |

## Was in andere Systeme gehört

| System | Der Anteil der Website |
|---|---|
| System der Schüler- und Studierendenverwaltung (Einschreibung, Noten) | nur Links |
| Lernmanagementsystem (Kurse, Aufgaben) | nur Links |
| Stundenplanung | Links oder ein Feed, wenn das System einen anbietet |
| Bibliothekskatalog | Links ([Veranstaltungen und Kataloge, verlinkt statt nachgebaut](../oeffentliche-bibliotheken/veranstaltungen-und-verlinkte-kataloge.md)) |
| Bewerbung um Zulassung | die Seite des Studiengangs verlinkt darauf |

Exponential kann sich mit Login-Handlern gegen andere Systeme authentifizieren, einschließlich der Gerüste für
OAuth-Social-Login in `sevenx_authentication_2fa`
([sevenx_authentication_2fa](../../../../funktionen/6.0/erweiterungen/sevenx_authentication_2fa.md)); die Anbindung an den
Identitätsanbieter einer Einrichtung ist eine Entwicklungsaufgabe, keine Einstellung.

## Zu klärende Fragen

- [ ] Wie viele Fachbereiche wollen eine eigene Website, und wer pflegt jede?
- [ ] Welche Inhalte sind nur für Beschäftigte und Lernende, und wie melden sie sich an?
- [ ] Wo sind Fotos von Schülerinnen und Schülern erlaubt, und wie wird die Einwilligung festgehalten?
- [ ] Welche Fristen des Jahres steuern die Website (Zulassung, Prüfungen)?

## Verwandte Seiten

- [Schulen und Hochschulen](README.md)
- [Inhaltsmodell für Schulen und Hochschulen](inhaltsmodell.md)
- [Ihre Website planen](../../../ersteller/website-ersteller/ihre-website-planen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
