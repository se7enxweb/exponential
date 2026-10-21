---
title: "Template-Operatoren in einem Theme"
audience: designer, developer
category: theme-extensions
order: 200
summary: Einem Theme einen Template-Operator hinzufügen - die Operator-Klasse, autoloads/eztemplateautoload.php, ExtensionAutoloadPath[] und das Autoload-Array - und die eine Datei beachten, die der Design-Assistent nicht schreibt.
keywords: Template-Operator, Autoloads, eztemplateautoload.php, ExtensionAutoloadPath, TemplateSettings, Operator-Klasse, RAD, Assistent für Template-Operatoren, template operator, operator class, template operator wizard
type: help
---
# Template-Operatoren in einem Theme

Diese Seite ist für Designer, die in ihren Templates ein kleines Stück PHP brauchen: einen Datumsbereich formatieren,
eine Farbe aus einer Einstellung wählen, einen Text kürzen. Ein Template-Operator ist der saubere Weg; Logik, die in
viele Templates kopiert wird, ist es nicht.

## Die drei Teile

| Teil | Datei | Gelesen von |
|---|---|---|
| Die Operator-Klasse | `autoloads/<name>operators.php` (ein beliebiger eigener Klassenname) | Dem Autoload-Array |
| Die Registrierung | `autoloads/eztemplateautoload.php` | Der Template-Engine, die genau diese Datei aus jedem ihr genannten Pfad einbindet |
| Der Pfad | `site.ini [TemplateSettings] ExtensionAutoloadPath[]=<extension>` in `settings/site.ini.append.php` | Der Template-Engine (`eZTemplate::factory()`) |

## Der schnelle Weg: der Assistent für Template-Operatoren

**Setup > RAD > Template extension** (`/setup/templateoperator`) schreibt alle drei Teile, einschließlich
`autoloads/eztemplateautoload.php` („über die Operatoren und Funktionen wirklich registriert werden“). Verwenden Sie ihn,
wenn das Theme mehr als einen Operator braucht.

## Vorsicht beim Design-Assistenten

Der [Design-Erweiterungs-Assistent](der-design-erweiterungs-assistent.md) hat einen Teil **Template-Operatoren**, der die
Operator-Klasse (`autoloads/<name>operators.php`, mit einem Beispieloperator `<name>_excerpt`) und die Zeile
`ExtensionAutoloadPath[]` schreibt und ab Exponential 6.0.15 auch `autoloads/eztemplateautoload.php`, das den Operator
registriert. Ältere Assistenten schrieben diese Datei **nicht**. Ohne sie registriert die Engine den Operator nie, und ein
Template, das ihn verwendet, meldet einen unbekannten Operator. Bei einem Theme, das vor 6.0.15 erzeugt wurde, fügen Sie
die Datei wie in Schritt 2 unten hinzu.

## Schritt für Schritt von Hand

1. Schreiben Sie die Klasse `extension/mytheme/autoloads/mythemeoperators.php` mit `operatorList()`,
   `namedParameterPerOperator()`, `namedParameterList()` und `modify()` (das Beispiel des Design-Assistenten ist ein guter
   Anfang).
2. Schreiben Sie `extension/mytheme/autoloads/eztemplateautoload.php`:

   ```php
   <?php
   $eZTemplateOperatorArray = array();
   $eZTemplateOperatorArray[] = array(
       'script'         => 'extension/mytheme/autoloads/mythemeoperators.php',
       'class'          => 'mythemeOperators',
       'operator_names' => array( 'mytheme_excerpt' ),
   );
   ```

3. Registrieren Sie den Pfad in `extension/mytheme/settings/site.ini.append.php`:

   ```php
   <?php /* #?ini charset="utf-8"?

   [TemplateSettings]
   ExtensionAutoloadPath[]=mytheme

   */ ?>
   ```

4. Prüfen Sie die PHP-Dateien und erzeugen Sie die Autoloads neu:

   ```bash
   php -l extension/mytheme/autoloads/mythemeoperators.php
   php -l extension/mytheme/autoloads/eztemplateautoload.php
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   php bin/php/ezcache.php --clear-id=template --allow-root-user
   ```

5. Verwenden Sie ihn in einem Template: `{$node.name|mytheme_excerpt( 20 )}`. Laden Sie den PHP-Dienst neu, der die
   Website ausliefert.

## Bewährte Praxis

- Stellen Sie Operatornamen den Namen Ihres Themes voran; ein kurzer allgemeiner Name kann mit dem Operator einer anderen
  Erweiterung kollidieren.
- Maskieren Sie die Ausgabe im Template (`|wash`), nicht im Operator, außer der Operator gibt absichtlich HTML zurück.
- Halten Sie Operatoren frei von Schreibzugriffen auf die Datenbank; sie laufen, während eine Seite gezeichnet wird.

## Verwandte Seiten

- [Operatoren im Überblick](../templates/operatoren-ueberblick.md)
- [RAD-Werkzeuge](../../../../eng-us/features/6.0/rad-tools.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
