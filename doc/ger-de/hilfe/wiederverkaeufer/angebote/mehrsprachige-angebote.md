---
title: Mehrsprachige Angebote
audience: reseller, partner, sales-marketing
category: reselling
order: 230
summary: Wie Sie mehrsprachige Exponential-Websites ehrlich verkaufen - Übersetzungen je Inhaltsobjekt, ein Siteaccess je Sprache, 268 Locale-Dateien und 54 Übersetzungen der Administration, Unterstützung für Schreibrichtung von rechts nach links, die Grenze von 62 Inhaltssprachen bei 64-Bit-PHP, was Sie prüfen, bevor Sie eine Sprache versprechen, und was Sie bepreisen.
keywords: mehrsprachig, Sprachen, Übersetzung, Locale, Inhaltssprache, Übersetzung der Administration, Siteaccess je Sprache, rechts nach links, RTL, 62 Inhaltssprachen, 64 Bit, maschinengestützte Übersetzungen, Preisgestaltung, multilingual, languages, translation, locale, content language, administration translation, siteaccess per language, right to left, 62 content languages, 64-bit, machine-assisted translations, pricing
type: help
---
# Mehrsprachige Angebote

Diese Seite ist für Wiederverkäufer, die Websites in mehreren Sprachen verkaufen oder in Märkte verkaufen, deren
Redakteure in einer anderen Sprache als Englisch arbeiten. Exponential ist hier stark, und die ehrlichen Details
machen den Unterschied zwischen einem zufriedenen und einem überraschten Kunden.

## Inhalt

1. [Was Exponential bietet](#was-exponential-bietet)
2. [Drei Fragen, bevor Sie eine Sprache versprechen](#drei-fragen-bevor-sie-eine-sprache-versprechen)
3. [Märkte mit Schreibrichtung von rechts nach links](#märkte-mit-schreibrichtung-von-rechts-nach-links)
4. [Was Sie bepreisen](#was-sie-bepreisen)
5. [Checkliste für das Angebot](#checkliste-für-das-angebot)

## Was Exponential bietet

| Funktion | Was sie für den Kunden bedeutet | Quelle |
|---|---|---|
| Übersetzungen je Inhaltsobjekt | Jede Seite kann in mehreren Sprachen existieren, getrennt bearbeitet | [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md) |
| Ein Siteaccess je Sprache | Jede Sprache kann ihre eigene Adresse haben, etwa `example.com/ger` oder `example.de` | [Siteaccess nach Host-Präfix und die Weiterleitung zur Sprache](../../../funktionen/6.0/siteaccess-zuordnung-nach-host-und-sprache.md) |
| Locale je Website | Datum, Zahlen und Währung folgen `site.ini [RegionalSettings] Locale` | [Sprachen und Locales](../../unternehmen/mehrsprachig/sprachen-und-locales.md) |
| 268 Locale-Dateien, 32 Schriftsysteme | Inhalte in fast jeder geschriebenen Sprache | [Sprachen](../../sprachen.md) |
| 54 Übersetzungen der Administration, 51 davon vollständig (Stand 25. Oktober 2026) | Redakteure können in ihrer eigenen Sprache arbeiten, bei den vollständigen | ebenda |
| 40 Locales mit Schreibrichtung von rechts nach links | Hebräisch, Arabisch, Persisch, Urdu und andere; die Administration dreht sich um | [Sprachen mit Schreibrichtung von rechts nach links](../../../funktionen/6.0/rechts-nach-links.md) |
| Rückfall bei Übersetzungen der Oberfläche | Ein österreichisches oder Schweizer Team erhält ohne Zusatzaufwand die deutsche Administration | [Sprachen und Locales](../../unternehmen/mehrsprachig/sprachen-und-locales.md) |

Zahlen gezählt für Exponential 6.0.15, Oktober 2026.

## Drei Fragen, bevor Sie eine Sprache versprechen

1. **Brauchen sie die Besucher oder auch die Redakteure?** Inhalte in einer Sprache brauchen nur eine Locale-Datei.
   Eine Administration in dieser Sprache braucht eine Übersetzung. Prüfen Sie die Unterstützungsstufe der Sprache:
   vollständig, teilweise Oberfläche oder nur Inhalte.
2. **Ist die Übersetzung der Administration geprüft?** Alle vollständigen Übersetzungen außer `eng-US` und `ger-DE`
   sind zumindest teilweise maschinengestützt; bevor sich eine Website auf sie verlässt, ist eine Prüfung durch
   einen Muttersprachler nötig. Planen Sie sie ein oder sagen Sie es im Angebot.
3. **Wie viele Inhaltssprachen über die Jahre?** Eine Installation fasst höchstens **62 Inhaltssprachen bei
   64-Bit-PHP** (30 bei 32-Bit-PHP). Eine hinzugefügte und nie genutzte Sprache belegt dennoch einen Platz, bis sie
   entfernt wird. Verwenden Sie 64-Bit-PHP.

Quelle: [Sprachen und Locales](../../unternehmen/mehrsprachig/sprachen-und-locales.md).

## Märkte mit Schreibrichtung von rechts nach links

Wenn die Sprache der Oberfläche von rechts nach links geschrieben wird, setzt die Administration `dir="rtl"`, die
Seitenspalten tauschen die Plätze und Symbole zeigen in die andere Richtung; Inhalte in einer Inhaltssprache mit
Schreibrichtung von rechts nach links werden dort, wo sie angezeigt und bearbeitet werden, ebenso gekennzeichnet
([Sprachen mit Schreibrichtung von rechts nach links](../../../funktionen/6.0/rechts-nach-links.md)). Ihr **öffentliches
Design** ist Ihre eigene Arbeit: Testen Sie es von rechts nach links, bevor Sie es versprechen
([Märkte mit Schreibrichtung von rechts nach links](../../unternehmen/mehrsprachig/maerkte-mit-rechts-nach-links-schrift.md)).

## Was Sie bepreisen

| Position | Hinweis |
|---|---|
| Einrichtung je Sprache | Locale, Siteaccess, Hostname oder Pfad, Zertifikat, Tests |
| Designarbeit je Schreibrichtung | Layouts von rechts nach links brauchen eigene Tests |
| Einrichtung des Übersetzungs-Workflows | Rollen, Zustände, Prüfschritte ([Übersetzungs-Workflow](../../unternehmen/mehrsprachig/uebersetzungsablauf.md)) |
| Prüfung einer maschinengestützten Übersetzung der Administration | Nur wenn Redakteure sie brauchen |
| Support je Sprache | Wer beantwortet Redakteure in dieser Sprache? |
| Übersetzung der Inhalte | Meist Aufgabe des Kunden oder eines Übersetzungsbüros; sagen Sie, wer sie übernimmt |

Exponential Intelligence kann bei der Übersetzung helfen, wenn der Kunde das wählt; lesen Sie
[Unterstützte Übersetzung mit Exponential Intelligence](../../unternehmen/mehrsprachig/unterstuetzte-uebersetzung-mit-exponential-intelligence.md),
bevor Sie sie aufnehmen, und beachten Sie die Datenschutzfragen, die sie aufwirft.

## Checkliste für das Angebot

- [ ] Sprachen aufgelistet, mit dem Locale-Code jeder Sprache.
- [ ] Unterstützungsstufe jeder Sprache geprüft ([Sprachen](../../sprachen.md)).
- [ ] Wer Inhalte übersetzt, wer sie prüft, wer sie freigibt.
- [ ] Adressschema je Sprache entschieden (Pfad oder Hostname).
- [ ] Design von rechts nach links getestet, falls eine Sprache es braucht.
- [ ] Anzahl der Inhaltssprachen für die nächsten Jahre innerhalb der Grenze.
- [ ] Supportsprache für Redakteure im Angebot genannt.

## Verwandte Seiten

- [Mehrsprachige Planung](../../unternehmen/mehrsprachig/mehrsprachigkeit-planen.md)
- [Checkliste für Mehrsprachigkeit](../../unternehmen/mehrsprachig/checkliste-mehrsprachigkeit.md)
- [Multisite-Angebote](multisite-angebote.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
