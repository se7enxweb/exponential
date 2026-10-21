---
title: Sprachen und Locales
audience: businesses, project-manager, admin
category: business-solutions
order: 20
summary: Was Exponential bei Sprachen unterstützt, erklärt für Entscheider – Locales, Inhaltssprachen, Übersetzungen der Administration und Website-Locales, die drei Unterstützungsstufen, die Grenze von 62 Sprachen und wie Sie eine Sprache prüfen, bevor Sie sich auf einen Markt festlegen.
keywords: Sprachen, Locales, Inhaltssprachen, Übersetzung der Administration, Sprache der Oberfläche, Unterstützungsstufen, 268 Locales, 33 Übersetzungen, Grenze von 62 Sprachen, Ausweichsprache, Unternehmen, languages, locales, content languages, administration translation, interface language, support levels, limit of 62 languages, fallback, business
type: help
---
# Sprachen und Locales

Diese Seite richtet sich an Führungskräfte, die wissen müssen, ob und in welcher Stufe Exponential die Sprachen ihrer
Märkte unterstützt, bevor sie einen Start zusagen. Sie erklärt vier Begriffe, die leicht verwechselt werden, die drei
Unterstützungsstufen, die Grenze für die Zahl der Inhaltssprachen und wie Sie eine bestimmte Sprache prüfen. Alle
Zahlen stammen aus [Sprachen in Exponential](../../../sprachen.md), der Referenzseite zu Sprachen.

## Inhalt

1. [Vier Dinge, die leicht verwechselt werden](#vier-dinge-die-leicht-verwechselt-werden)
2. [Was verfügbar ist](#was-verfügbar-ist)
3. [Die drei Unterstützungsstufen](#die-drei-unterstützungsstufen)
4. [Wenn eine Sprache keine eigene Übersetzung der Oberfläche hat](#wenn-eine-sprache-keine-eigene-übersetzung-der-oberfläche-hat)
5. [Die Grenze für Inhaltssprachen](#die-grenze-für-inhaltssprachen)
6. [Eine Sprache prüfen, bevor Sie sich festlegen](#eine-sprache-prüfen-bevor-sie-sich-festlegen)

## Vier Dinge, die leicht verwechselt werden

| Begriff | Was er ist, unternehmerisch betrachtet | Technischer Ort |
|---|---|---|
| **Locale** | Die Regeln einer Sprache in einem Land: ihr Name, die Schreibrichtung, Datums- und Zeitformate, Tages- und Monatsnamen, der erste Tag der Woche, Zahlen- und Währungsformate | `share/locale/<code>.ini` |
| **Inhaltssprache** | Eine Sprache, in der Ihre Inhalte geschrieben werden können, etwa Deutsch für Ihre Produktseiten | Setup > Sprachen |
| **Übersetzung der Administration** | Die Wörter der Bearbeitungsoberfläche: Schaltflächen, Menüs, Meldungen | `share/translations/<code>/` |
| **Website-Locale** | Die Locale, mit der eine Website-Adresse formatiert, und die Übersetzung der Oberfläche, die sie verwendet | `site.ini [RegionalSettings] Locale` |

Quelle: [Sprachen, Abschnitt 1](../../../sprachen.md#1-locales-übersetzungen-inhaltssprachen-und-siteaccesses).
Verwenden Sie genau diese Begriffe in Plänen und Verträgen; das vermeidet die meisten Missverständnisse mit dem
technischen Team.

Eine Inhaltssprache braucht nur eine Locale-Datei. Eine hinzugefügte Locale-Datei macht eine Sprache auswählbar; sie
fügt keine Inhaltssprache hinzu, ändert keine Website und belegt keinen der Plätze für Inhaltssprachen.

## Was verfügbar ist

Gezählt in [Sprachen in Exponential](../../../sprachen.md) (Exponential 6.0.15, Oktober 2026):

- **268 Locale-Dateien**: 261 Sprachen und Länder, die Testsprache Lorem Ipsum und 6 Varianten.
- **32 Schriftsysteme**, von Lateinisch, Kyrillisch und Griechisch bis Arabisch, Hebräisch, Devanagari, Chinesisch und
  viele weitere.
- **40 Locales mit Rechts-nach-links-Schrift** ([Märkte mit Rechts-nach-links-Schrift](maerkte-mit-rechts-nach-links-schrift.md)).
- **33 Übersetzungen der Administration**, davon neunzehn vollständig: `eng-US`, `ger-DE`, Spanisch für Spanien und
  Mexiko, Französisch, Ukrainisch, Hebräisch, Arabisch, Türkisch, Polnisch, Italienisch, Portugiesisch (Brasilien),
  Niederländisch, Schwedisch, Norwegisch (Bokmål), Dänisch, Tschechisch, Kroatisch, Latein und, zum Spaß, Klingonisch.
- **Zur Laufzeit ist keine PHP-Erweiterung nötig**: Locales sind einfache INI-Dateien
  ([Sprachen, Abschnitt 10](../../../sprachen.md#10-keine-php-extension-zur-laufzeit)).

> **Hinweis**
> Alle vollständigen Übersetzungen außer `eng-US` und `ger-DE` sind zumindest teilweise maschinell unterstützt
> entstanden. Die Referenzseite sagt, dass eine Prüfung durch Muttersprachler nötig ist, bevor sich eine Website darauf
> verlässt. Planen Sie diese Prüfung ein, wenn Ihre Redakteure in einer dieser Sprachen arbeiten werden.

## Die drei Unterstützungsstufen

Aus [Sprachen, Abschnitt 2](../../../sprachen.md#2-unterstützungsstufen):

| Stufe | Was funktioniert | Planen Sie ein |
|---|---|---|
| **Vollständig: Oberfläche und Inhalte** | Die Administration spricht die Sprache vollständig; Inhalte, Datumsangaben, Zahlen und Währung folgen der Locale | Redakteure können in ihrer eigenen Sprache arbeiten |
| **Oberfläche teilweise, Inhalte vollständig** | Eine ältere Übersetzung der Administration deckt einen Teil der heutigen Texte ab; der Rest erscheint auf Englisch | Redakteure, die etwas Englisch lesen, oder ein Übersetzungsprojekt |
| **Nur Inhalte** | Keine Übersetzung der Administration: Die Oberfläche bleibt englisch; Inhalte und Formate folgen der Locale | Besucher bekommen ihre Sprache; Redakteure arbeiten auf Englisch |

Für die meisten Unternehmen ist die entscheidende Frage nicht „Wird die Sprache unserer Kunden unterstützt?“ (Inhalte
in fast jeder geschriebenen Sprache werden es), sondern „Brauchen unsere Redakteure die Administration in ihrer
Sprache?“.

## Wenn eine Sprache keine eigene Übersetzung der Oberfläche hat

Eine Locale ohne eigene Übersetzung weicht auf eine andere derselben Sprache aus, wo es eine gibt: jedes deutsch-,
französisch- und spanischsprachige Land auf `ger-DE`, `fre-FR` und `esl-ES`, die arabischen Länder auf `ara-SA` und so
weiter. Ketten von Ausweichsprachen sind möglich, und neue Länder-Locales einer übersetzten Sprache weichen
automatisch aus ([Sprachen, Abschnitt 13](../../../sprachen.md#13-übersetzungs-fallbacks)).

Für ein Unternehmen heißt das: Ein österreichisches oder Schweizer Team bekommt die deutsche Administration ohne
zusätzlichen Aufwand.

## Die Grenze für Inhaltssprachen

Eine Installation enthält höchstens **62 Inhaltssprachen unter 64-Bit-PHP** (30 unter 32-Bit-PHP). Jede Inhaltssprache
ist ein Bit einer Zahl, die jedes Objekt speichert. Setup > Sprachen zeigt, wie viele belegt sind und wie viele noch
hinzugefügt werden können, und lehnt eine Sprache über der Grenze ab
([Sprachen, Abschnitt 11](../../../sprachen.md#11-die-grenze-von-62-inhaltssprachen)).

Die Referenzseite erklärt, warum die Grenze beibehalten wird: Sie anzuheben würde das Datenbankschema jeder
Installation ändern, „für einen Fall, den keine bekannte Website erreicht hat“. Zwei Folgen für die Planung:

- Eine Sprache, die hinzugefügt und nie verwendet wurde, belegt trotzdem einen Platz, bis sie entfernt wird.
- 64-Bit-PHP wird empfohlen: `composer.json` schlägt es „for support of more than 30 languages“ vor
  ([Unterstützte Betriebssysteme](../../betriebssystemunterstuetzung.md)).

## Eine Sprache prüfen, bevor Sie sich festlegen

- [ ] Suchen Sie die Locale in [Sprachen, Abschnitt 3](../../../sprachen.md#3-alle-locales) und notieren Sie ihren
      Code.
- [ ] Prüfen Sie ihre Unterstützungsstufe in [Abschnitt 2](../../../sprachen.md#2-unterstützungsstufen).
- [ ] Wenn Redakteure die Oberfläche in dieser Sprache brauchen und sie nicht vollständig ist, entscheiden Sie: auf
      Englisch arbeiten oder eine Übersetzung finanzieren
      ([Abschnitt 8](../../../sprachen.md#8-eine-übersetzung-der-administrationsoberfläche-hinzufügen-oder-vervollständigen)).
- [ ] Wenn die Sprache von rechts nach links geschrieben wird, lesen Sie [Märkte mit Rechts-nach-links-Schrift](maerkte-mit-rechts-nach-links-schrift.md).
- [ ] Wenn Ihre Sprache nicht aufgeführt ist, nennt [Abschnitt 6](../../../sprachen.md#6-verbreitete-sprachen-die-noch-fehlen)
      die verbreiteten Sprachen, die noch fehlen, und [Abschnitt 7](../../../sprachen.md#7-eine-locale-hinzufügen), wie eine
      Locale hinzugefügt wird.
- [ ] Stellen Sie die Inhaltssprachen, die Sie in den nächsten Jahren brauchen werden, der Grenze gegenüber.

## Verwandte Seiten

- [Mehrsprachigkeit planen](mehrsprachigkeit-planen.md)
- [Währungen, Datumsangaben und Formate](waehrungen-datumsangaben-und-formate.md)
- [Sprachen in Exponential](../../../sprachen.md)
- [Übersetzungen und Sprachen](../../../funktionen/6.0/uebersetzungen-und-sprachen.md)
- [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md)
- [Sprachen](../../sprachen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
