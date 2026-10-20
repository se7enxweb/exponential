---
title: "Übersetzungen in einem Theme"
audience: designer, developer
category: theme-extensions
order: 210
summary: Die Oberflächentexte eines Themes in mehreren Sprachen mitliefern - translations/<locale>/translation.ts, TranslationExtensions[], der Operator i18n mit dem eigenen Kontext des Themes - und die Dateien vollständig halten.
keywords: Übersetzungen, translation.ts, i18n, TranslationExtensions, RegionalSettings, Locale, ger-DE, untranslated, Sprache, translations, locale, language
type: help
---
# Übersetzungen in einem Theme

Diese Seite ist für Designer, deren Theme eigene Texte zeigt („Weiterlesen“, „Diese Website durchsuchen“,
Schaltflächenbeschriftungen) und in mehr als einer Sprache funktionieren soll. Die Seite der Templates erklärt
[Template-Texte übersetzen](../../../../eng-us/help/designers/templates/translating-template-strings.md); hier geht es um die Dateien in der Erweiterung.

## Die Dateien

```
extension/mytheme/translations/
├── untranslated/translation.ts     every source string, no translations (a template for translators)
├── ger-DE/translation.ts
└── fre-FR/translation.ts
```

`sevenx_themes_simple` liefert `eng-US`, `esl-ES`, `esl-MX`, `ger-DE`, `heb-IL`, `lat-VA`, `tlh-QO` und `untranslated`
mit; `sevenx_themes_media` fügt weitere hinzu, zum Beispiel `fre-FR`, `ita-IT`, `nor-NO` und `pol-PL`.

## Schritt für Schritt

1. Kennzeichnen Sie in den Templates jeden Text mit einem Kontext Ihres Themes:

   ```
   {'Read more'|i18n( 'extension/mytheme' )}
   ```

2. Legen Sie `translations/ger-DE/translation.ts` im Format von Qt Linguist an, mit einem `<context>` namens
   `extension/mytheme` und einer `<message>` pro Text.
3. Registrieren Sie die Erweiterung für Übersetzungen in `extension/mytheme/settings/site.ini.append.php`:

   ```php
   <?php /* #?ini charset="utf-8"?

   [RegionalSettings]
   TranslationExtensions[]=mytheme

   */ ?>
   ```

4. Prüfen Sie das XML jeder Datei: `xmllint --noout extension/mytheme/translations/*/translation.ts` (keine Ausgabe
   erwartet).
5. Leeren Sie die Caches: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` und
   `php bin/php/ezcache.php --clear-id=template,translation --allow-root-user`.
6. Öffnen Sie einen Siteaccess mit der Locale `ger-DE` und prüfen Sie die Texte.

## Bewährte Praxis

- Führen Sie einen Kontext pro Theme, damit Übersetzer alles an einem Ort finden.
- Liefern Sie `untranslated/translation.ts` mit jedem Quelltext mit, damit eine neue Sprache mit einer vollständigen
  Liste beginnt.
- Wiederholen Sie dieselbe Nachricht nie zweimal in einem Kontext.
- Eine Sprache, die von rechts nach links läuft (`heb-IL`, `ara-SA`), verlangt, dass auch das Layout folgt
  ([Rechts-nach-links-Designs](../../../../eng-us/help/designers/theme-design/right-to-left-designs.md)).

## Verwandte Seiten

- [Sprachen](../../sprachen.md)
- [Tutorial: ein Theme internationalisieren](../../../../eng-us/help/designers/theme-design/tutorial-internationalize-a-theme.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
