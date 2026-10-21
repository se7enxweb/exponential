---
title: Die ersten 90 Tage für Rettungsdienste
audience: government, project-manager, admin
category: public-sector
order: 60
summary: Ein Plan für 90 Tage für eine Feuerwehr, einen Rettungsdienst oder eine Stelle des Krisenmanagements, die mit Exponential beginnt - zuerst Einsatzverfahren und Verfügbarkeit, dann Inhalte, dann ein Start nach einem Einsatzprobelauf - mit Prüfungen.
keywords: Projektplan, 90 Tage, Rettungsdienste, Einsatzverfahren, Verfügbarkeit, Lasttest, statischer Cache, Probelauf, Start, Prüfungen, project plan, 90 days, emergency services, incident procedure, availability, load test, static cache, rehearsal, launch, checks
type: help
---
# Die ersten 90 Tage für Rettungsdienste

Diese Seite richtet sich an Projektverantwortliche und Administratorinnen und Administratoren, die die Website eines
Rettungsdienstes mit Exponential starten. Weisen Sie vor allem anderen nach, dass Einsatzinformationen schnell
veröffentlicht und unter Last ausgeliefert werden können. Der Plan setzt voraus, dass eine Installation existiert
([Erste Schritte](../../../../anleitungen/erste-schritte.md)).

## Inhalt

1. [Tage 1 bis 30: Einsatzverfahren](#tage-1-bis-30-einsatzverfahren)
2. [Tage 31 bis 60: Verfügbarkeit](#tage-31-bis-60-verfügbarkeit)
3. [Tage 61 bis 90: Inhalte, Probelauf, Start](#tage-61-bis-90-inhalte-probelauf-start)
4. [Laufend](#laufend)

## Tage 1 bis 30: Einsatzverfahren

| Schritt | Prüfung |
|---|---|
| Vereinbaren, wer Einsatzinformationen veröffentlicht, zu welchen Zeiten und wer sie prüft | ein Bereitschaftsplan mit mindestens zwei Personen je Schicht |
| Die Klasse Einsatzmeldung und den Ordner Incidents anlegen ([Inhaltsmodell](inhaltsmodell.md)) | eine Testmeldung lässt sich veröffentlichen |
| Die Rolle für die Veröffentlichung von Einsätzen und die Konten anlegen, mit zweistufiger Anmeldung ([Struktur](struktur-sektionen-und-rollen.md)) | jede Person in Bereitschaft hat sich von einem Telefon aus angemeldet |
| Laufende Einsätze oben auf der Startseite anzeigen | die Testmeldung erscheint dort |
| `[HideSettings]` konfigurieren und bestätigen, dass der globale Cronjob-Teil läuft | eine Testmeldung mit einer vergangenen Endzeit wird verborgen |
| Das Verfahren schreiben: veröffentlichen, aktualisieren, beenden, berichtigen, eine falsche Meldung herunternehmen | ein Verfahren auf einer Seite |

## Tage 31 bis 60: Verfügbarkeit

| Schritt | Lesen |
|---|---|
| Normale Last und Spitzenlast aus vergangenen Einsätzen messen | Ihre Statistiken |
| Den statischen Cache und die Auslagerungsregeln konfigurieren; ihn erzeugen | [Generator für den statischen Cache](../../../../funktionen/6.0/generator-fuer-den-statischen-cache.md) |
| Über den Antwort-Cache von Velocity und ein CDN entscheiden | [Antwort-Cache von Velocity](../../../../funktionen/6.0/velocity-antwort-cache.md), [HTTP-Caching und CDNs](../../../ersteller/website-ersteller/http-caching-und-cdns.md) |
| Lasttest mit eingeschalteten Caches | ein gemessenes Ergebnis über der erwarteten Spitze |
| Test: auf einer Testinstallation PHP oder die Datenbank anhalten; antworten statische Seiten weiterhin? | [Warnungen, Verfügbarkeit](warnungen-verfuegbarkeit-wartung-und-statischer-cache.md) |
| Den Wartungsmodus proben | [Wartungsmodus](../../../../anleitungen/wartungsmodus.md) |
| Einen Ausfall des Hostingstandorts planen | ein schriftlicher Plan (zweiter Standort, DNS, CDN) |

## Tage 61 bis 90: Inhalte, Probelauf, Start

| Schritt | Lesen |
|---|---|
| Vorsorge- und Vorbeugungsratgeber in einfacher Sprache schreiben | [Schwerpunkte der Anforderungen](schwerpunkte-der-anforderungen.md) |
| Wichtige Anweisungen in die Hauptsprachen des Gebiets übersetzen | [Inhaltssprachen](../../../../anleitungen/inhaltssprachen.md) |
| Vollständiger Einsatzprobelauf in der Nacht mit dem Bereitschaftsteam | das Verfahren |
| Test der Barrierefreiheit auf einem Telefon mit einem Screenreader | [Die Barrierefreiheit testen](../../../ersteller/website-ersteller/barrierefreiheit-testen.md) |
| Start | [Start-Checkliste](../../../ersteller/website-ersteller/checkliste-fuer-den-start.md) |

Prüfungen beim Start:

- [ ] Im Probelauf erschien eine Meldung für Besucher innerhalb der vereinbarten Zeit, auch über jeden Cache.
- [ ] Der Notruf steht auf jeder Seite.
- [ ] Jede Person in Bereitschaft hat mindestens einmal veröffentlicht.

## Laufend

| Wann | Aufgabe |
|---|---|
| Bei jedem Schichtwechsel | der Bereitschaftsplan ist aktuell |
| Monatlich | eine kurze Veröffentlichungsübung |
| Nach jedem Einsatz | eine Auswertung dessen, was zu lange gedauert hat |
| Jährlich | Vorsorgeratgeber überprüft; Lasttest wiederholt |

## Verwandte Seiten

- [Rettungsdienste](README.md)
- [Warnungen, Verfügbarkeit, Wartungsmodus und statischer Cache](warnungen-verfuegbarkeit-wartung-und-statischer-cache.md)
- [Start-Checkliste](../../../ersteller/website-ersteller/checkliste-fuer-den-start.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
