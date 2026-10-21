---
title: Rettungsdienste
audience: government, project-manager, admin, website-creator
category: public-sector
order: 10
summary: Verzeichnis für Feuerwehren und Rettungsdienste, Katastrophenschutz und Stellen des Krisenmanagements, die eine Exponential-Website planen - was sie veröffentlichen, Inhaltsmodell, Struktur und Rollen, Anforderungen, ein Plan für 90 Tage und Warnungen mit Hochverfügbarkeit, Wartungsmodus und statischem Cache.
keywords: Rettungsdienste, Feuerwehr, Rettung, Katastrophenschutz, Bevölkerungsschutz, Krisenmanagement, Warnungen, Einsätze, Vorsorge, Hochverfügbarkeit, statischer Cache, emergency services, fire service, rescue, civil protection, emergency management, alerts, incidents, preparedness, high availability, static cache
type: help
---
# Rettungsdienste

Dieser Ordner richtet sich an Projektverantwortliche, Administratorinnen und Administratoren und Website-Ersteller
einer Feuerwehr, eines Rettungsdienstes, einer Katastrophenschutzbehörde oder einer Stelle des Krisenmanagements. Ihre
Websites haben zwei Leben: ein ruhiges (Hinweise zur Vorbeugung, Nachwuchsgewinnung, Berichte) und ein Notfallleben,
wenn viele Menschen gleichzeitig aktuelle, richtige Informationen brauchen und die Website erreichbar bleiben muss. Die
Seiten beantworten, was Rettungsdienste veröffentlichen, wie Sie es in Exponential bauen, welche Anforderungen am meisten
wiegen, wie Sie die ersten 90 Tage gestalten und wie Sie Warnungen mit Caches, statischen Seiten und dem Wartungsmodus
verfügbar halten.

Die Fakten zum Produkt stammen aus der Dokumentation dieses Repositorys; die Beschreibungen von Rettungsdiensten geben
allgemeine Praxis im öffentlichen Sektor wieder. Eine Website ist nie der einzige Warnkanal; Warnsysteme für die
Bevölkerung, Rundfunk und Telefonnetze liegen außerhalb von Exponential.

## Inhalt

1. [Die Seiten](#die-seiten)
2. [Lesereihenfolge](#lesereihenfolge)
3. [Was Rettungsdienste besonders macht](#was-rettungsdienste-besonders-macht)

## Die Seiten

| Seite | Die Antwort in einer Zeile |
|---|---|
| [Was ein Rettungsdienst veröffentlicht](was-veroeffentlicht-wird.md) | Informationen zu Einsätzen und Warnungen, Hinweise zur Vorsorge, Vorbeugung, Dienstleistungen, Nachwuchsgewinnung, Berichte |
| [Inhaltsmodell für Rettungsdienste](inhaltsmodell.md) | Mitgelieferte Klassen plus Klassen für Einsatzmeldung und Vorsorgeratgeber, klein und schnell auszufüllen |
| [Struktur, Sektionen und Rollen für Rettungsdienste](struktur-sektionen-und-rollen.md) | Ein Einsatzbereich ganz oben, ein Bereitschaftsteam für die Veröffentlichung ohne Freigabe, alles andere geprüft |
| [Schwerpunkte der Anforderungen für Rettungsdienste](schwerpunkte-der-anforderungen.md) | Verfügbarkeit, Geschwindigkeit, Richtigkeit, Barrierefreiheit unter Stress, Sprachen, Sicherheit |
| [Die ersten 90 Tage für Rettungsdienste](die-ersten-90-tage.md) | Ein Plan, der Verfügbarkeit und das Einsatzverfahren vor dem Start nachweist |
| [Warnungen, Verfügbarkeit, Wartungsmodus und statischer Cache](warnungen-verfuegbarkeit-wartung-und-statischer-cache.md) | Einsatzinformationen unter Last und bei Ausfällen online halten, mit den vorhandenen Werkzeugen |

## Lesereihenfolge

1. **Warnungen, Verfügbarkeit, Wartungsmodus und statischer Cache**: Diese Seite prägt den Rest.
2. **Was ein Rettungsdienst veröffentlicht**.
3. **Schwerpunkte der Anforderungen**.
4. **Inhaltsmodell** und **Struktur, Sektionen und Rollen**.
5. **Die ersten 90 Tage**.

Nach oben: [Verwaltungsebenen](../README.md). Die Einstiegsseite des Bereichs für Verwaltungen,
[Verwaltungen: hier beginnen](../../verwaltungen-hier-beginnen.md), ist der Ausgangspunkt für alle Leserinnen und Leser.
Gesundheitswarnungen behandelt
[Warnungen in vielen Sprachen und in einfacher Sprache](../oeffentliche-gesundheit/warnungen-mehrsprachig-und-in-einfacher-sprache.md).

## Was Rettungsdienste besonders macht

| Thema | Warum es wichtig ist |
|---|---|
| Spitzen | der Verkehr während eines Einsatzes kann weit über dem Normalen liegen |
| Geschwindigkeit | Informationen sind für Minuten oder Stunden nützlich |
| Ausfälle | der Einsatz kann Strom, Netze oder das Hosting selbst betreffen |
| Stress | die Lesenden haben Angst; Klarheit und Barrierefreiheit zählen am meisten |
| Nacht und Wochenende | veröffentlicht wird zu jeder Stunde, von wenigen Menschen |

## Verwandte Seiten

- [Verwaltungsebenen](../README.md)
- [Öffentliche Gesundheit](../oeffentliche-gesundheit/README.md)
- [Wartungsmodus](../../../../anleitungen/wartungsmodus.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
