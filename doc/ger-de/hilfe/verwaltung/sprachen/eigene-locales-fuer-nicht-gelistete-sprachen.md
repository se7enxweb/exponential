---
title: Eigene Locales für nicht gelistete Sprachen
audience: government, admin, developer
category: public-sector
order: 140
summary: Wie Sie eine Sprache hinzufügen, für die Exponential keine Locale mitliefert - share/locale/<code>.ini mit Namen, HTTP-Sprachangabe, Schreibrichtung, Formaten sowie Tages- und Monatsnamen schreiben, den Code wählen, sie als individuelle Inhaltssprache hinzufügen und prüfen.
keywords: eigene Locale, neue Locale, share/locale, Locale-Datei, Sprachcode, ISO 639, BCP 47, ContentLanguage, TextDirection, individuelle Übersetzung, nicht gelistete Sprache, custom locale, new locale, locale file, language code, custom translation, unlisted language
type: help
---
# Eigene Locales für nicht gelistete Sprachen

Diese Seite richtet sich an Administratoren und Entwickler, die eine Sprache brauchen, für die es noch keine
Locale-Datei gibt. Eine Inhaltssprache braucht eine Datei `share/locale/<code>.ini`, denn die Locale entscheidet über
den Namen der Sprache, ihre HTTP-Sprachangabe, ihre Schreibrichtung und darüber, wie Datum und Zahlen geschrieben
werden. Das Dateiformat ist in [Sprachen in Exponential, Abschnitt 7](../../../sprachen.md#7-eine-locale-hinzufügen)
beschrieben.

## Inhalt

1. [Den Code wählen](#den-code-wählen)
2. [Die Datei schreiben](#die-datei-schreiben)
3. [Als Inhaltssprache hinzufügen](#als-inhaltssprache-hinzufügen)
4. [Prüfen](#prüfen)
5. [Teilen](#teilen)

## Den Code wählen

Drei Buchstaben für die Sprache, ein Bindestrich, das zweibuchstabige Land nach ISO 3166: `xxx-YY`. Exponential behält
für ältere Sprachen seine historischen Codes (`eng`, `ger`, `fre`, `esl` ...) und verwendet für andere den Code nach
ISO 639-2/T; eine Sprache ohne Code nach ISO 639-2 erhält ihren Code nach ISO 639-3. Numerische Regionen können nicht
verwendet werden. Prüfen Sie zuerst die Liste der mitgelieferten Dateien und Abschnitt 6 von
[Sprachen in Exponential](../../../sprachen.md#6-verbreitete-sprachen-die-noch-fehlen) für Sprachen, von denen bekannt ist,
dass sie fehlen.

## Die Datei schreiben

Beginnen Sie mit der Locale einer eng verwandten Sprache oder desselben Landes, kopieren Sie sie unter dem neuen
Namen und ändern Sie:

| Gruppe | Zu ändernde Schlüssel |
|---|---|
| `[RegionalSettings]` | `Country`, `LanguageName` (eigener Name), `InternationalLanguageName` (englischer Name) |
| `[LanguageSettings]` | `TextDirection=rtl` nur, wenn von rechts nach links geschrieben wird |
| `[HTTP]` | `ContentLanguage`: die BCP-47-Angabe, die als `lang` der Seite ausgegeben wird |
| `[Currency]`, `[Numbers]` | Währung und Zahlenformat des Landes; Trennzeichen müssen ein einzelnes Byte sein |
| `[DateTime]` | Datums- und Zeitformate, `MondayFirst` |
| `[ShortMonthNames]`, `[LongMonthNames]`, `[ShortDayNames]`, `[LongDayNames]` | die Namen in der Sprache |

Die Datei ist UTF-8 mit der ersten Zeile `#?ini charset="utf-8"?`. Bitten Sie eine Sprecherin oder einen Sprecher, die
Namen zu liefern oder zu prüfen.

## Als Inhaltssprache hinzufügen

**Setup > Sprachen > Sprache hinzufügen**, wählen Sie **Individuell**, füllen Sie **Name der spezifischen
Übersetzung** und **Örtlichkeit der spezifischen Übersetzung** aus. Fehlt die Datei, meldet das Formular *There is no
locale ... in share/locale* und behält Ihre Eingabe
([Inhaltssprachen, Abschnitt 4](../../../anleitungen/inhaltssprachen.md#4-eine-sprache-hinzufügen)).

## Prüfen

- Die Seite der Sprache unter **Setup > Sprachen** hat einen aufklappbaren Abschnitt **The locale**, der zeigt, wie
  Datum, Zahlen und Geldbeträge geschrieben werden; vergleichen Sie mit dem, was eine Sprecherin erwartet.
- Veröffentlichen Sie eine Testseite und prüfen Sie `<html lang="...">` auf der öffentlichen Website.
- Die Locale-Dateien sind durch Tests abgedeckt, die gültiges UTF-8 prüfen
  (`tests/tests/lib/ezlocale/expLocaleFilesTest.php`); führen Sie sie aus, wenn Sie Ihre Datei in einem Fork
  pflegen.

## Teilen

Eine durchgesehene Locale-Datei hilft allen anderen Nutzern der Sprache. Bieten Sie sie dem Projekt an (siehe
„Beiträge als öffentliche Stelle“ im Bereich Souveränität); andernfalls bewahren Sie sie in Ihrem eigenen Repository
auf, denn eine nur auf dem Server hinzugefügte Datei kann bei einem Update verloren gehen.

## Verwandte Seiten

- [Sprachen in Exponential](../../../sprachen.md)
- [Eine Inhaltssprache hinzufügen](eine-inhaltssprache-hinzufuegen.md)
- [Standards zur Internationalisierung](../../../standards/internationalisierung.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
