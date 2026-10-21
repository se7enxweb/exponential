---
title: Eine Sprache hinzufügen
audience: customer, admin, businesses
category: customer-success
order: 6
summary: Was ein Website-Betreiber plant und entscheidet, bevor er einer Exponential-Website eine Inhaltssprache hinzufügt - das Hinzufügen einer Sprache übersetzt nichts und zeigt von selbst nichts an, was der Siteaccess braucht, Übersetzer und freigebende Personen, welche Inhalte zuerst kommen, und eine Reihenfolge für den Start.
keywords: Sprache hinzufügen, mehrsprachig, Inhaltssprache, Übersetzung, Siteaccess, SiteLanguageList, Sprachumschalter, Übersetzer, Start, Website-Betreiber, adding a language, multilingual, content language, translation, language switcher, translators, launch, site owner
type: help
---
# Eine Sprache hinzufügen

Diese Seite ist für den Betreiber, der die Website in einer weiteren Sprache anbieten möchte. In Exponential hat das
zwei technische Hälften (eine Inhaltssprache und einen Siteaccess, der sie anzeigt) und eine große menschliche Hälfte:
die Übersetzungsarbeit. Die Schritte für den Administrator stehen unter [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md);
die Planungssicht unter [Planung mehrsprachiger Websites](../../unternehmen/mehrsprachig/mehrsprachigkeit-planen.md).

## Inhalt

1. [Was das Hinzufügen einer Sprache bewirkt und was nicht](#was-das-hinzufügen-einer-sprache-bewirkt-und-was-nicht)
2. [Entscheidungen, bevor Sie beginnen](#entscheidungen-bevor-sie-beginnen)
3. [Die Schritte, und wer sie erledigt](#die-schritte-und-wer-sie-erledigt)
4. [Was zuerst zu übersetzen ist](#was-zuerst-zu-übersetzen-ist)
5. [Prüfungen vor dem Start](#prüfungen-vor-dem-start)

## Was das Hinzufügen einer Sprache bewirkt und was nicht

Aus [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md):

- **Setup > Sprachen > Sprache hinzufügen** fügt eine Inhaltssprache hinzu; auf einer Standardinstallation stehen
  132 Locales in der Liste, und eine eigene lässt sich aus einer Locale-Datei erstellen.
- **Das Hinzufügen übersetzt nichts und ändert keinen Siteaccess**: Noch sieht kein Besucher etwas Neues.
- Was ein Besucher sieht, legt jeder Siteaccess mit `SiteLanguageList` fest: die Inhaltssprachen, die er anzeigt, die
  bevorzugte zuerst. Ein Element wird in der ersten davon angezeigt, die es hat; Elemente in keiner davon sind
  verborgen, es sei denn, sie sind *immer verfügbar* (oder `ShowUntranslatedObjects` ist eingeschaltet).
- Eine Inhaltssprache ist nicht die Sprache der Oberfläche: Schaltflächen und Menüs kommen aus der `Locale` des
  Siteaccess und den Übersetzungsdateien.
- Eine Installation kann höchstens 62 Inhaltssprachen enthalten.

## Entscheidungen, bevor Sie beginnen

| Frage | Optionen | Lesen Sie |
|---|---|---|
| Wie erreichen Besucher die Sprache? | ein Pfad (`/de`), ein Host (`de.example.com`) oder eine Domain pro Land | [Sprachumschalter und Sprach-URLs](../../ersteller/website-ersteller/sprachumschalter-und-sprach-urls.md) |
| Was sehen Besucher, wenn eine Seite nicht übersetzt ist? | verborgen, oder in der Hauptsprache angezeigt | [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md) |
| Alles, oder eine Auswahl? | eine vollständige Website, oder nur die Hauptseiten | [Planung mehrsprachiger Websites](../../unternehmen/mehrsprachig/mehrsprachigkeit-planen.md) |
| Wer übersetzt und wer gibt frei? | eigenes Personal, Agentur, Freiberufler, unterstützt vom Schreibassistenten | [Übersetzungsroutine](../alltag/uebersetzungsroutine.md) |
| Von rechts nach links? | Arabisch, Hebräisch, Persisch und andere erfordern Unterstützung durch das Design | [Märkte mit Schreibrichtung von rechts nach links](../../unternehmen/mehrsprachig/maerkte-mit-rechts-nach-links-schrift.md) |
| Währungen, Daten und Formate? | die Locale bestimmt Datums- und Zahlenformate; ein Shop braucht auch Preise | [Währungen, Datums- und Zahlenformate](../../unternehmen/mehrsprachig/waehrungen-datumsangaben-und-formate.md) |

## Die Schritte, und wer sie erledigt

| Schritt | Wer |
|---|---|
| 1. Die obigen Fragen entscheiden | Betreiber |
| 2. Die Inhaltssprache unter **Setup > Sprachen** hinzufügen | Administrator |
| 3. Den Siteaccess, seine Adresse und `SiteLanguageList` anlegen oder anpassen; einen Sprachumschalter in das Design einbauen | Partner |
| 4. Übersetzern Konten geben, die auf die Sprache beschränkt sind; bei Bedarf eine Freigabe pro Sprache einrichten | Administrator ([Freigabe und Prüfung](../alltag/freigabe-und-pruefung.md)) |
| 5. Die Inhalte übersetzen (siehe unten) | Übersetzer |
| 6. Unter der neuen Adresse testen, abgemeldet | Betreiber und eine Person mit dieser Muttersprache |
| 7. Die Adresse öffentlich machen; Suchmaschinen informieren (Sitemap pro Sprache) | Partner ([XML-Sitemaps](../../ersteller/website-ersteller/sitemaps.md)) |

Der neue Siteaccess lässt sich testen, bevor er öffentlich ist; bitten Sie Ihren Partner um einen Testhost
([Eine Installation, viele Websites](../../unternehmen/multisite/eine-installation-viele-websites.md)).

## Was zuerst zu übersetzen ist

1. Die Startseite und die Menüordner: Über sie erreichen Besucher alles andere
   ([Übersetzungs-Workflow](../../unternehmen/mehrsprachig/uebersetzungsablauf.md)).
2. Kontakt-, Rechts- und Datenschutzseiten.
3. Die Seiten, die Besucher aus diesem Markt am meisten nutzen (Ihre Suchstatistiken und Analysen helfen dabei).
4. Formulare und ihre Bestätigungstexte; Newsletter-Listen für diese Sprache.
5. Alles andere, in der Reihenfolge des [Redaktionskalenders](../alltag/redaktionskalender.md).

## Prüfungen vor dem Start

- [ ] Jede Seite im Menü hat eine Übersetzung oder wird bewusst in der Hauptsprache angezeigt.
- [ ] Der Sprachumschalter führt von jeder Seite zur selben Seite in der anderen Sprache.
- [ ] Datums- und Zahlenangaben sind ortsüblich geschrieben.
- [ ] Formulare, Fehlermeldungen und E-Mails sind in der Sprache.
- [ ] Eine Person mit dieser Muttersprache hat die Hauptseiten gelesen.
- [ ] Die [Checkliste für Mehrsprachigkeit](../../unternehmen/mehrsprachig/checkliste-mehrsprachigkeit.md) ist abgehakt.

## Verwandte Seiten

- [Übersetzungsroutine](../alltag/uebersetzungsroutine.md)
- [Sprachen und Locales](../../unternehmen/mehrsprachig/sprachen-und-locales.md)
- [Mehrsprachige Websites](../../ersteller/website-ersteller/mehrsprachige-websites.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
