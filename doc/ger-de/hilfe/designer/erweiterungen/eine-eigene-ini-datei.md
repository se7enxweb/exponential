---
title: "Einem Theme eine eigene Einstellungsdatei geben"
audience: designer, developer
category: theme-extensions
order: 160
summary: Einem Theme eine eigene Einstellungsdatei (mytheme.ini) mit dokumentierten Standardwerten geben, sie in Templates mit ezini() und in PHP mit eZINI lesen und Installationen sicher überschreiben lassen.
keywords: eigene ini, Einstellungsdatei, ezini, eZINI, Theme-Optionen, Standardwerte, cjw_jumper.ini, Override, own ini, settings file, theme options, defaults, override
type: help
---
# Einem Theme eine eigene Einstellungsdatei geben

Diese Seite ist für Designer, die in ihrem Theme Optionen wollen (ein Farbschema, die Zahl der Teaser auf der Startseite,
Standardwerte für Open Graph), ohne Nutzer Templates bearbeiten zu lassen.

## Ein echtes Beispiel

`cjw_themes_jumper` liefert `settings/cjw_jumper.ini.append.php` mit. Sein Release 0.1.2 liest zum Beispiel die Klassen,
die für Open Graph als Artikel gelten, aus `cjw_jumper.ini [OpenGraph] ArticleClasses`.

## Schritt für Schritt

1. Legen Sie `extension/mytheme/settings/mytheme.ini.append.php` mit einem dokumentierten Block an:

   ```php
   <?php /* #?ini charset="utf-8"?

   [FrontPage]
   # How many teasers the front page shows (a whole number from 1 to 12)
   TeaserCount=6

   */ ?>
   ```

   Eine Installation ohne Ihr Theme hat überhaupt keine `mytheme.ini`, also ist die Append-Datei der einzige Ort, an dem
   die Standardwerte stehen. Kommentieren Sie jede Einstellung.

2. Lesen Sie sie in einem Template mit dem Operator `ezini`:

   ```
   {def $count = ezini( 'FrontPage', 'TeaserCount', 'mytheme.ini' )}
   ```

   Siehe [Der Operator ezini](../../../../eng-us/help/designers/templates/ezini-operator.md).

3. Lesen Sie sie in PHP (einem Operator oder einem Modul des Themes):

   ```php
   $count = (int) eZINI::instance( 'mytheme.ini' )->variable( 'FrontPage', 'TeaserCount' );
   ```

4. Leeren Sie den INI-Cache und prüfen Sie:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ./console exp:ini where mytheme.ini/FrontPage/TeaserCount --allow-root-user
   ```

5. Sagen Sie Nutzern im README, wie sie den Wert überschreiben: in `settings/override/mytheme.ini.append.php` oder in einem
   Siteaccess-Ordner, nie in der eigenen Datei des Themes (ein Update würde sie überschreiben).

## Bewährte Praxis

- Prüfen Sie, was Sie lesen: Zahlen umwandeln, mit einer Liste erlaubter Werte vergleichen, auf den Standardwert
  zurückfallen.
- Lesen Sie nie Geheimnisse aus der Einstellungsdatei eines Themes; Geheimnisse gehören in den Override-Ordner der
  Installation.
- Wenn Sie eine Einstellung umbenennen oder entfernen, lesen Sie eine Zeit lang weiter den alten Namen
  ([Eine Einstellung oder ein Template abkündigen](../lebenszyklus/eine-einstellung-oder-ein-template-abkuendigen.md)).

## Verwandte Seiten

- [Einstellungsdateien in einer Erweiterung](einstellungsdateien-in-einer-erweiterung.md)
- [Einstellungen auf der Kommandozeile ändern](../../../../eng-us/features/6.0/exp-ini-command.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
