---
title: Die ersten 90 Tage für den öffentlichen Gesundheitsdienst
audience: government, project-manager, admin
category: public-sector
order: 60
summary: Ein Plan für 90 Tage für ein Gesundheitsamt oder eine Behörde des öffentlichen Gesundheitsdienstes, die mit Exponential beginnen - zuerst das Verfahren für Warnungen, dann Hinweise mit medizinischer Prüfung, Sprachen und Last, dann der Start - mit Prüfungen.
keywords: Projektplan, 90 Tage, öffentliche Gesundheit, Verfahren für Warnungen, medizinische Prüfung, Übersetzungen, Lasttest, statischer Cache, Start, Prüfungen, project plan, 90 days, public health, alert procedure, clinical review, translations, load test, static cache, launch, checks
type: help
---
# Die ersten 90 Tage für den öffentlichen Gesundheitsdienst

Diese Seite richtet sich an Projektverantwortliche und Administratorinnen und Administratoren, die die Website des
öffentlichen Gesundheitsdienstes mit Exponential starten. Bringen Sie zuerst das Verfahren für Warnungen zum Laufen: Ein
Ausbruch wartet nicht auf den Starttermin. Der Plan setzt voraus, dass eine Installation existiert
([Erste Schritte](../../../../anleitungen/erste-schritte.md)).

## Inhalt

1. [Tage 1 bis 30: zuerst die Warnungen](#tage-1-bis-30-zuerst-die-warnungen)
2. [Tage 31 bis 60: Hinweise und Prüfung](#tage-31-bis-60-hinweise-und-prüfung)
3. [Tage 61 bis 90: Sprachen, Last und Start](#tage-61-bis-90-sprachen-last-und-start)
4. [Laufend](#laufend)

## Tage 1 bis 30: zuerst die Warnungen

| Schritt | Prüfung |
|---|---|
| Vereinbaren, wer zu jeder Stunde Warnungen veröffentlicht (mindestens zwei Personen) und wer sie übersetzt | ein Bereitschaftsplan |
| Die Klasse Warnung und den Ordner Alerts anlegen ([Inhaltsmodell](inhaltsmodell.md)) | eine Testwarnung lässt sich veröffentlichen |
| Aktuelle Warnungen auf der Startseite anzeigen (eine Template-Liste oder ein Block von Layouts) | die Testwarnung erscheint auf der Startseite |
| `[HideSettings]` für Warnungen konfigurieren | eine Testwarnung mit einer vergangenen Endzeit wird verborgen, nachdem `hide.php` gelaufen ist |
| Die Rolle für die Veröffentlichung von Warnungen anlegen, mit zweistufiger Anmeldung | wer Warnungen veröffentlicht, kann außerhalb von Alerts nichts tun |
| Proben: veröffentlichen, aktualisieren, übersetzen, beenden | mit Zeitmessung; schriftlich festgehalten ([Warnungen in vielen Sprachen](warnungen-mehrsprachig-und-in-einfacher-sprache.md)) |

## Tage 31 bis 60: Hinweise und Prüfung

| Schritt | Prüfung |
|---|---|
| Die Klassen Gesundheitsthema und Sprechstelle anlegen | Testobjekte lassen sich veröffentlichen |
| Sektionen und Rollen anlegen; die medizinische Prüfung einrichten ([Struktur](struktur-sektionen-und-rollen.md)) | die Änderung einer Redakteurin oder eines Redakteurs wartet; eine prüfende Person gibt sie frei |
| Bestätigen, dass der Workflow-Cronjob oft genug läuft | Statusleiste unter **Setup > Workflow-Prozesse** |
| Die meistbesuchten Themen migrieren, in einfacher Sprache neu geschrieben | jedes hat eine Zusammenfassung, ein Überprüfungsdatum und eine prüfende Person |
| Test der Barrierefreiheit der Templates | kein offener schwerwiegender Befund ([Die Barrierefreiheit testen](../../../../../eng-us/help/creators/website-creators/testing-accessibility.md)) |

## Tage 61 bis 90: Sprachen, Last und Start

| Schritt | Lesen |
|---|---|
| Sprach-Siteaccesses und Rollen für Übersetzende einrichten | [Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md) |
| Vorlagen für Warnungen und die wichtigsten Themen in die vorrangigen Sprachen übersetzen | |
| Lasttest mit eingeschalteten Caches und Entscheidung über statischen Cache oder ein CDN für Spitzen | [Leistung und Caching](../../../../../eng-us/help/creators/website-creators/performance-and-caching.md) |
| Ein Wartungsfenster und eine Wiederherstellung proben | [Wartungsmodus](../../../../anleitungen/wartungsmodus.md) |
| Start | [Start-Checkliste](../../../../../eng-us/help/creators/website-creators/launch-checklist.md) |

Prüfungen beim Start:

- [ ] Eine vom Bereitschaftsteam veröffentlichte Warnung erscheint innerhalb der festgelegten Zeit auf der Startseite in
  jedem Sprach-Siteaccess, auch nach dem Auffrischen der Caches.
- [ ] Jedes migrierte Thema zeigt sein Überprüfungsdatum.
- [ ] Die Website hält die Spitze des Lasttests mit eingeschalteten Caches.

## Laufend

| Wann | Aufgabe |
|---|---|
| Wöchentlich | Themen nach ihrem Überprüfungsdatum auflisten; an die Prüfenden schicken |
| Nach jeder Warnung | auswerten, was zu lange gedauert hat |
| Vierteljährlich | das Verfahren für Warnungen mit einem neuen Teammitglied proben |

## Verwandte Seiten

- [Öffentliche Gesundheit](README.md)
- [Warnungen in vielen Sprachen und in einfacher Sprache](warnungen-mehrsprachig-und-in-einfacher-sprache.md)
- [Start-Checkliste](../../../../../eng-us/help/creators/website-creators/launch-checklist.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
