---
title: "Thema: Sprachen und Schreibrichtung von rechts nach links"
audience: press
category: press-media
order: 64
summary: Ein Themenbeitrag über Sprachen in Exponential - 268 Locale-Dateien, 40 davon von rechts nach links, 73 Übersetzungen der Administration, alle vollständig, eine Administration, die sich für Hebräisch und Arabisch umdreht - mit druckbaren Fakten, einem Zählunterschied, auf den Sie achten sollten, und den Aussagen, die Sie vermeiden sollten.
keywords: Thema, Sprachen, Locales, Übersetzungen, rechts nach links, RTL, Hebräisch, Arabisch, mehrsprachig, Internationalisierung, Presse, story, languages, locales, translations, right to left, Hebrew, Arabic, multilingual, internationalization, press
type: help
---
# Thema: Sprachen und Schreibrichtung von rechts nach links

Diese Seite ist für Journalisten, die für Sprachgemeinschaften, ein internationales Publikum oder regionale Medien
schreiben. Sprachzahlen lassen sich leicht aufblähen. Diese Seite erklärt die drei Arten der Sprachunterstützung, gibt
die dokumentierten Zahlen mit ihrem Datum und zeigt, wo zwei Quellen unterschiedlich zählen.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Was neu ist, und seit wann](#was-neu-ist-und-seit-wann)
3. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
4. [Wo die Quellen voneinander abweichen](#wo-die-quellen-voneinander-abweichen)
5. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
6. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)
7. [Wen Sie fragen](#wen-sie-fragen)
8. [Verwandte Seiten](#verwandte-seiten)

## Das Thema in einem Absatz

Exponential trennt die Sprache der **Inhalte** von der Sprache der **Administration**. „268 Locale-Dateien (261
Sprachen und Länder, [eine Testsprache] und 6 Varianten, in 32 Schriften, 40 davon von rechts nach links geschrieben)
lassen sich als Inhaltssprachen und Siteaccess-Locales wählen; es gibt 73 Übersetzungen der Administration, alle
vollständig“ ([Sprachen in Exponential](../../sprachen.md)). Wenn ein Redakteur eine Oberflächensprache mit
Schreibrichtung von rechts nach links wählt, „dreht sich die Administration um“: Die Seite sagt `dir="rtl"`, die
Seitenspalten tauschen die Plätze, und Text beginnt rechts ([Rechts nach links](../../funktionen/6.0/rechts-nach-links.md)).
Das meiste davon kam im Oktober 2026, in der Entwicklungslinie 6.0.15, die nicht veröffentlicht ist.

## Was neu ist, und seit wann

| Was | Wann | Quelle |
|---|---|---|
| Vollständige englische und deutsche Kataloge, ein Übersetzungsstring für fast jeden sichtbaren Text | 27. und 28. September 2026 | [Übersetzungen und Sprachen](../../funktionen/6.0/uebersetzungen-und-sprachen.md) |
| 89 neue Locales und eine Seite für jede Sprache | 15. Oktober 2026 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#15-october-2026-89-new-locales-and-one-page-for-every-language) |
| Sprachen mit Schreibrichtung von rechts nach links, Hebräisch, Latein, Klingonisch und 81 weitere Locales | 16. Oktober 2026 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#16-october-2026-right-to-left-languages-hebrew-latin-klingon-and-81-more-locales) |
| Die Administration auf Arabisch, maschinell unterstützt und noch von einem Muttersprachler zu prüfen | 18. Oktober 2026 | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md#18-october-2026-the-administration-in-arabic-and-hebrew-for-the-newest-strings) |

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| 268 Locale-Dateien, 40 von rechts nach links geschrieben, in 32 Schriften (Stand 15. Oktober 2026). | [Sprachen in Exponential](../../sprachen.md) |
| 73 Übersetzungen der Administration, alle vollständig (26. Oktober 2026), aufgeführt in [Sprachen in Exponential, Abschnitt 4](../../sprachen.md#4-alle-übersetzungen-der-administrationsoberfläche). | [Sprachen in Exponential, Unterstützungsstufen](../../sprachen.md#2-unterstützungsstufen) |
| Jede Übersetzung eines Elements wird für sich veröffentlicht; Kennzeichen zeigen, was vorhanden ist und was fehlt. | [Sprachen](../sprachen.md) |
| Ein Land ohne eigene Übersetzung fällt auf eine verwandte zurück, zum Beispiel jedes deutschsprachige Land auf Deutsch. | [Sprachen in Exponential, Unterstützungsstufen](../../sprachen.md#2-unterstützungsstufen) |
| Eine Seite mit Schreibrichtung von links nach rechts bleibt nach der Arbeit an rechts nach links „Byte für Byte dasselbe Markup“. | [Rechts nach links](../../funktionen/6.0/rechts-nach-links.md) |
| Für die Sprachen ist zur Laufzeit keine PHP-Erweiterung nötig. | [Sprachen in Exponential, Abschnitt 10](../../sprachen.md#10-keine-php-extension-zur-laufzeit) |
| Eine Installation kann auf 64-Bit-PHP höchstens 62 Inhaltssprachen enthalten. | [Sprachen in Exponential, Abschnitt 11](../../sprachen.md#11-die-grenze-von-62-inhaltssprachen) |
| Mehrere vollständige Übersetzungen entstanden maschinell unterstützt; eine Prüfung durch Muttersprachler ist erwünscht. | [Sprachen in Exponential, Abschnitt 8](../../sprachen.md#8-eine-übersetzung-der-administrationsoberfläche-hinzufügen-oder-vervollständigen) |

## Wo die Quellen voneinander abweichen

Das README zählt „219 locales, 170 of them new“ ([README, Recent improvements](https://github.com/se7enxweb/exponential/blob/main/README.md#recent-improvements-to-exponential)),
während [Sprachen in Exponential](../../sprachen.md) am 15. Oktober 2026 268 Locale-Dateien zählt. Locales kamen im
Oktober in mehreren Schritten hinzu; verwenden Sie also die datierte Zahl der Sprachenseite und nennen Sie ihr Datum.

## Was Sie nicht behaupten sollten

- „Exponential ist in 268 Sprachen verfügbar.“ Sagen Sie „268 Locale-Dateien als Inhaltssprachen; 73 Übersetzungen der
  Administration, alle vollständig“.
- „Jede Übersetzung ist perfekt“ oder „professionell übersetzt“. Mehrere entstanden maschinell unterstützt.
- „Unbegrenzt viele Inhaltssprachen.“ Die dokumentierte Grenze liegt bei 62 pro Installation auf 64-Bit-PHP.
- „Unterstützung für Klingonisch“ als ernsthafte Funktion: Das README selbst sagt „(for fun)“.

## Blickwinkel und Ideen für Überschriften

- Regionaler Blickwinkel: Ist Ihre Sprache vollständig, teilweise oder nur für Inhalte vorhanden? Sehen Sie in
  Abschnitt 4 der Sprachenseite nach.
- Blickwinkel Community: Muttersprachler sind eingeladen, maschinell unterstützte Übersetzungen zu prüfen.

```text
„Eine Administration, die von rechts nach links liest: Exponential ergänzt Hebräisch und Arabisch“
„268 Locales, 71 vollständige Übersetzungen: Sprachen ehrlich zählen“
„Muttersprachler gesucht: ein mit maschineller Hilfe übersetztes CMS prüfen“
```

## Wen Sie fragen

| Frage | Fragen Sie | Wo |
|---|---|---|
| Die Arbeit an den Sprachen und ihre Zahlen | 7x | info@se7enx.com oder <https://exponential.earth/about/contact-us> |
| Bei einer Übersetzung helfen | die Community | <https://share.exponential.earth/forums>, <https://t.me/exponentialcms> |

## Verwandte Seiten

- [Gesprächspunkte: Sprachen und Schreibrichtung von rechts nach links](../fuersprecher/gespraechspunkte-sprachen-und-rtl.md)
- [Thema: Multisite](thema-multisite.md)
- [Thema: Barrierefreiheit](thema-barrierefreiheit.md)
- [Themenbeiträge: Übersicht](themenbeitraege-uebersicht.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
