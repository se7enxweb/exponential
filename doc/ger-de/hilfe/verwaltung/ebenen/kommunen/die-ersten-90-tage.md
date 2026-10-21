---
title: Die ersten 90 Tage für Kommunen
audience: government, project-manager, admin
category: public-sector
order: 60
summary: Ein realistischer Plan für 90 Tage für eine Stadt, Gemeinde oder einen Rat, die mit einem kleinen Team mit Exponential beginnen - zuerst die wichtigsten Anliegen, dann die Veröffentlichung für den Rat, die Fachbereiche und der Start - mit Prüfungen im Produkt.
keywords: Projektplan, 90 Tage, Kommune, Rat, kleines Team, wichtigste Anliegen, Migration, Start, Schulung, Prüfungen, project plan, 90 days, municipality, council, small team, top tasks, migration, launch, training, checks
type: help
---
# Die ersten 90 Tage für Kommunen

Diese Seite richtet sich an Projektverantwortliche und Webverantwortliche einer Stadt, Gemeinde oder eines Rats, die mit
einem kleinen Team eine Website mit Exponential beginnen. Sie stellt die meistgenutzten Dienstleistungen an den Anfang
und lässt den Rest für die Zeit nach dem Start. Sie setzt voraus, dass eine Installation existiert, gehostet von Ihnen
oder einem Lieferanten ([Erste Schritte](../../../../anleitungen/erste-schritte.md)).

## Inhalt

1. [Tage 1 bis 30: die wichtigsten Anliegen und der Plan](#tage-1-bis-30-die-wichtigsten-anliegen-und-der-plan)
2. [Tage 31 bis 60: den Kern bauen](#tage-31-bis-60-den-kern-bauen)
3. [Tage 61 bis 90: Fachbereiche, Rat, Start](#tage-61-bis-90-fachbereiche-rat-start)
4. [Was warten kann](#was-warten-kann)

## Tage 1 bis 30: die wichtigsten Anliegen und der Plan

| Schritt | Ergebnis |
|---|---|
| Die wichtigsten Anliegen aus Suchprotokollen, Themen des Bürgertelefons und Statistiken der aktuellen Website ermitteln | eine Liste von etwa 20 Dienstleistungen, die die Einwohnerschaft am meisten braucht |
| Bestandsaufnahme der aktuellen Website; Seiten zum Behalten, Neuschreiben oder Aufgeben kennzeichnen | eine Bestandsliste ([Sitemap und Inhaltsinventar](../../../../../eng-us/help/creators/website-creators/site-map-and-content-inventory.md)) |
| Mit dem Sitzungsdienst vereinbaren, wie Sitzungen veröffentlicht werden | der Sitzungszyklus ([Ratssitzungen und örtliche Dienstleistungen](ratssitzungen-und-oertliche-dienstleistungen.md)) |
| Je Fachbereich, der bearbeiten wird, eine Redakteurin oder einen Redakteur benennen | eine Kontaktliste |
| Vereinbaren, wer außerhalb der Dienstzeit eine dringende Warnung veröffentlichen darf | ein Bereitschaftsplan für Warnungen |
| Wenn ein Lieferant hostet: Bedingungen für Updates, Sicherungen und Support vereinbaren | eine Leistungsvereinbarung |

Prüfungen: **Setup > Systeminformationen** zeigt die installierte Version; Sie können sich in der Administration und im
Editor-Siteaccess anmelden.

## Tage 31 bis 60: den Kern bauen

| Schritt | Prüfung |
|---|---|
| Die Klassen Dienstleistung, Sitzung, Öffentliche Bekanntmachung und Anlaufstelle anlegen ([Inhaltsmodell](inhaltsmodell.md)) | je ein Testobjekt lässt sich veröffentlichen |
| Den Baum nach Anliegen anlegen ([Struktur](struktur-sektionen-und-rollen.md)) | das Menü zeigt die Leistungsbereiche |
| Die 20 wichtigsten Dienstleistungsseiten in einfacher Sprache schreiben | jede hat alle Pflichtfelder |
| `[HideSettings]` für öffentliche Bekanntmachungen einrichten | eine Testbekanntmachung mit einem vergangenen Datum zum Verbergen wird verborgen, nachdem `hide.php` gelaufen ist |
| Verpflichtenden Alternativtext für Bildattribute einschalten | das Speichern eines Bildfelds ohne ihn wird im Bearbeitungsformular abgewiesen (der Mehrfach-Upload fragt nicht) |
| Die Barrierefreiheit des Designs testen | kein offener schwerwiegender Befund |
| Die zweistufige Anmeldung für Beschäftigte einschalten | das Webteam hat sie eingerichtet |

## Tage 61 bis 90: Fachbereiche, Rat, Start

| Schritt | Lesen |
|---|---|
| Sektionen und Rollen für die Fachbereiche anlegen, die bearbeiten | [Sektionen](../../../../anleitungen/sektionen.md) |
| Die Redaktion der Fachbereiche mit je einer echten Aufgabe schulen | [Editors 101](../../../trainer/redakteure-101/README.md) |
| Die Sitzungen des laufenden Jahres veröffentlichen | [Ratssitzungen und örtliche Dienstleistungen](ratssitzungen-und-oertliche-dienstleistungen.md) |
| Die alten Adressen der wichtigsten Anliegen umleiten | [Umleitungen und verschobene Inhalte](../../../../../eng-us/help/creators/website-creators/redirects-and-moved-content.md) |
| Die Erklärung zur Barrierefreiheit und die Datenschutzerklärung veröffentlichen | [Barrierefreiheit für Website-Ersteller](../../../../../eng-us/help/creators/website-creators/accessibility-for-site-builders.md) |
| Start | [Start-Checkliste](../../../../../eng-us/help/creators/website-creators/launch-checklist.md) |

Prüfungen beim Start:

- [ ] Jede Seite der wichtigsten Anliegen öffnet sich abgemeldet und wird von der Suche der Website mit den Wörtern
  gefunden, die die Einwohnerschaft verwendet.
- [ ] Das Verfahren für Warnungen wurde geprobt: eine Warnung veröffentlicht, auf der Startseite angezeigt und entfernt.
- [ ] Die Seite der Cronjobs zeigt den globalen und den häufigen Teil als geplant (**Setup > Cronjobs**).

## Was warten kann

| Punkt | Warum er warten kann |
|---|---|
| Selten genutzte Seiten neu schreiben | die alten Dokumente verlinken und nach Nutzung verbessern |
| Ein Newsletter | zuerst RSS und Nachrichtenseiten |
| Viele Sprachen | mit den wichtigsten Anliegen in der am meisten gebrauchten Sprache beginnen |
| Ein Veranstaltungskalender für jeden Veranstaltungsort | zuerst ein Kalender |

## Verwandte Seiten

- [Kommunen](README.md)
- [Ratssitzungen und örtliche Dienstleistungen](ratssitzungen-und-oertliche-dienstleistungen.md)
- [Ihre erste Website Schritt für Schritt](../../../../../eng-us/help/creators/website-creators/your-first-site-step-by-step.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
