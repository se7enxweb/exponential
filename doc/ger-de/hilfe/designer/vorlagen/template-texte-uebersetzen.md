---
title: Template-Texte übersetzen
audience: designer, developer
category: templates
order: 360
summary: Wie Texte in Templates zu Übersetzern und zurück gelangen - Kontexte und Quellen in i18n-Aufrufen, translation.ts-Dateien in der Theme-Erweiterung, TranslationExtensions, die Kataloge untranslated und Englisch, die Abdeckung prüfen, Caches und Rückfälle zwischen Sprachen.
keywords: Übersetzung, translation.ts, i18n-Texte, Templates übersetzen, TranslationExtensions, ezchecktranslation, Übersetzungs-Cache, Rückfallsprache, Theme-Übersetzung, Übersetzer, translation, i18n strings, translate templates, translation cache, fallback language, theme translation, translators
type: help
---
# Template-Texte übersetzen

Diese Seite ist für Designer, die die Texte eines Themes für die Übersetzung vorbereiten und die Übersetzungen pflegen. Für
das erste Mal Schritt für Schritt siehe [Tutorial: ein Theme internationalisieren](../theme-design/tutorial-ein-theme-internationalisieren.md).

## Inhalt

1. [Vom Template zur Übersetzung](#vom-template-zur-übersetzung)
2. [Wo Übersetzungen von Themes liegen](#wo-übersetzungen-von-themes-liegen)
3. [Gute Quelltexte schreiben](#gute-quelltexte-schreiben)
4. [Prüfen und Caches](#prüfen-und-caches)
5. [Rückfälle](#rückfälle)

## Vom Template zur Übersetzung

1. Ein Template gibt `{'Read more'|i18n( 'design/mytheme/line' )}` aus.
2. Die Übersetzungsdatei der Locale hat einen `<context>` namens `design/mytheme/line` mit einer `<message>`, deren `<source>`
   genau `Read more` ist.
3. Ihre `<translation>` wird angezeigt; ohne sie wird der Quelltext angezeigt.

## Wo Übersetzungen von Themes liegen

```
extension/mytheme/translations/ger-DE/translation.ts
extension/mytheme/translations/fre-FR/translation.ts
extension/mytheme/translations/untranslated/translation.ts   (optional: every source, empty translations, for translators)
```

Der Siteaccess liest sie, wenn `site.ini [RegionalSettings] TranslationExtensions[]` die Erweiterung auflistet. Das Theme media
liefert elf Kataloge in dieser Struktur mit; das Theme starter liefert `ger-DE`. Siehe
[Übersetzungen in einem Theme](../erweiterungen/uebersetzungen-in-einem-theme.md).

## Gute Quelltexte schreiben

- Ganze Sätze mit Platzhaltern, nie Bruchstücke, die im Template zusammengefügt werden.
- Eine Bedeutung pro Kontext: „Open“ (ein Verb auf einer Schaltfläche) und „Open“ (ein Zustand) in verschiedenen Kontexten.
- Ein Kommentar für Übersetzer, wenn der Text mehrdeutig ist (der zweite Parameter von `i18n`).
- Quelltexte in amerikanischem Englisch („color“, „license“), wie im Rest des Produkts.

## Prüfen und Caches

```bash
./console exp:ezchecktranslation ger-DE          # statistics for one translation
php bin/php/ezcache.php --clear-id=translation   # after changing a .ts file
xmllint --noout extension/mytheme/translations/ger-DE/translation.ts
```

`site.ini [RegionalSettings] TranslationCache=enabled` cacht Übersetzungen; `TranslationCheckMTime=enabled` (Standard) bemerkt
geänderte `.ts`-Dateien an ihrer Zeit.

## Rückfälle

Eine Locale kann bei fehlenden Texten auf eine andere zurückfallen (zum Beispiel eine regionale Variante auf die Hauptsprache);
`./console exp:i18n fallbacks` zeigt die Kette jeder Locale und wie viel vom Kernel und von jeder Erweiterung sie übersetzt
zeigt (`doc/eng-us/languages.md`, „Translation fallbacks“).

## Verwandte Seiten

- [Der Operator i18n](der-i18n-operator.md)
- [Sprachen in Exponential](../../../sprachen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
