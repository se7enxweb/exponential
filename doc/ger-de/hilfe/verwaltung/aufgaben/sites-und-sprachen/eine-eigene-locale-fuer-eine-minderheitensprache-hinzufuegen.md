---
title: Eine eigene Locale für eine Minderheitensprache hinzufügen
audience: government, admin, developer
category: public-sector
order: 400
summary: Aufgabe für Administratoren - eine Sprache ohne Locale-Datei hinzufügen, indem Sie share/locale/<code>.ini aus einer verwandten Locale schreiben, sie als individuelle Inhaltssprache hinzufügen und von einem Sprecher prüfen lassen.
keywords: eigene Locale, Minderheitensprache, Regionalsprache, Locale-Datei, individuelle Übersetzung, Sprachcode, custom locale, minority language, share/locale, locale file, Custom translation, language code, ContentLanguage
type: help
---
# Eine eigene Locale für eine Minderheitensprache hinzufügen

Diese Aufgabe richtet sich an Administratoren und Entwickler. Hintergrund:
[Eigene Locales für nicht gelistete Sprachen](../../sprachen/eigene-locales-fuer-nicht-gelistete-sprachen.md) und
[Sprachen in Exponential, Abschnitt 7](../../../../../eng-us/languages.md#7-adding-a-locale).

## Bevor Sie beginnen

- Prüfen Sie, dass es für die Sprache wirklich keine Datei gibt: die Liste in
  [Sprachen in Exponential](../../../../../eng-us/languages.md#3-every-locale) und deren Abschnitt 6 der fehlenden Sprachen.
- Ein Sprecher, der die Namen der Tage und Monate liefern und die Formate prüfen kann.

## Schritte

1. Wählen Sie den Code: drei Buchstaben (ISO 639-2/T, oder ISO 639-3, wenn es keinen gibt), ein Bindestrich, das
   zweibuchstabige Land.
2. Kopieren Sie eine verwandte Locale-Datei nach `share/locale/<code>.ini` und ändern Sie die Namen (`LanguageName`,
   `InternationalLanguageName`, `Country`), `[HTTP] ContentLanguage`, `TextDirection` bei Schreibrichtung von rechts
   nach links, die Formate und die Namen der Tage und Monate. UTF-8, erste Zeile `#?ini charset="utf-8"?`.
3. Halten Sie die Datei in Ihrem Repository (und bieten Sie sie dem Projekt an), damit Updates sie nicht verlieren.
4. **Setup > Sprachen**, **Sprache hinzufügen**, **Individuell**: **Name der spezifischen Übersetzung** und
   **Örtlichkeit der spezifischen Übersetzung** (der Code); **Sprache hinzufügen**.

## Prüfen

Die Seite der Sprache unter **Setup > Sprachen**, Abschnitt **The locale**, zeigt Datumsangaben und Zahlen so, wie der
Sprecher es erwartet; eine Testseite zeigt auf der öffentlichen Site das richtige `lang`.

## Probleme

| Problem | Lösung |
|---|---|
| *There is no locale ... in share/locale* | Dateiname oder Ort falsch, oder nicht auf jeden Webknoten ausgerollt |
| Das Formular lehnt den Code ab | er muss die Form `xxx-YY` haben |

## Verwandte Seiten

- [Minderheiten- und Regionalsprachen](../../sprachen/minderheiten-und-regionalsprachen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
