---
title: Ein eigener Template-Operator
audience: developer
category: examples
order: 40
summary: Zwei Template-Operatoren hinzufügen, exp_initials und exp_reading_time mit einem benannten Parameter, über autoloads/eztemplateautoload.php einer Erweiterung und eine Operatorklasse mit operatorList, namedParameterList und modify.
keywords: Template-Operator, eigener Operator, benannter Parameter, Operatorklasse, template operator, custom operator, eztemplateautoload.php, eZTemplateOperatorArray, operatorList, namedParameterPerOperator, namedParameterList, modify, ExtensionAutoloadPath, TemplateSettings
type: example
---
# Ein eigener Template-Operator

Ein Template-Operator ist eine Funktion, die ein Template mit einem senkrechten Strich aufruft:
`{$node.name|exp_initials}`. Sie fügen der Beispielerweiterung zwei hinzu:

- `exp_initials`: Aus *Ada Lovelace* wird *AL*;
- `exp_reading_time( words_per_minute )`: die Minuten, die das Lesen eines Textes dauert, mindestens 1; der Parameter
  ist optional und hat den Standardwert 200.

Voraussetzungen: die Erweiterung aus [Ein Erweiterungsgerüst](erweiterungsgeruest.md), aktiviert.

## 1. Die Operatoren registrieren

Die Template-Engine liest `autoloads/eztemplateautoload.php` jeder Erweiterung, die in
`site.ini [TemplateSettings] ExtensionAutoloadPath[]` genannt ist. Fügen Sie diese Zeile in
`settings/site.ini.append.php` der Erweiterung ein:

```ini
<?php /* #?ini charset="utf-8"?

[TemplateSettings]
# Look for autoloads/eztemplateautoload.php in this extension (template operators)
ExtensionAutoloadPath[]=expexample

*/ ?>
```

`extension/expexample/autoloads/eztemplateautoload.php` listet auf, welche Klasse welche Operatornamen beantwortet:

```php
<?php
/**
 * The template operators of the expexample extension. Read because site.ini [TemplateSettings]
 * ExtensionAutoloadPath[] names expexample.
 *
 * @copyright Copyright (C) 2026 Example Company
 * @license GNU General Public License v2.0 (or any later version)
 * @package expexample
 */

$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array( 'script' => 'extension/expexample/autoloads/expexampletemplateoperators.php',
                                    'class' => 'expExampleTemplateOperators',
                                    'operator_names' => array( 'exp_initials', 'exp_reading_time' ) );
```

Die Klasse wird erst geladen, wenn ein Template zum ersten Mal einen der Namen verwendet. Operatornamen teilen sich
einen Namensraum mit allen Operatoren des Kernels und anderer Erweiterungen: Ein eigenes Präfix (`exp_` oder das Ihrer
Erweiterung) vermeidet Konflikte. Die [Liste der Template-Operatoren](../template-operatoren/README.md) zeigt, welche
Namen vergeben sind.

## 2. Die Operatorklasse

`extension/expexample/autoloads/expexampletemplateoperators.php`:

```php
<?php
/**
 * Two template operators:
 *   {$node.name|exp_initials}                          "Ada Lovelace" -> "AL"
 *   {$text|exp_reading_time( 200 )}                    minutes to read at 200 words a minute (at least 1)
 *
 * @copyright Copyright (C) 2026 Example Company
 * @license GNU General Public License v2.0 (or any later version)
 * @package expexample
 */

class expExampleTemplateOperators
{
    /** The operator names this class answers, as listed in eztemplateautoload.php */
    public function operatorList()
    {
        return array( 'exp_initials', 'exp_reading_time' );
    }

    /** true: namedParameterList() is keyed by operator name */
    public function namedParameterPerOperator()
    {
        return true;
    }

    /** The parameters of each operator, in the order they are written in the template */
    public function namedParameterList()
    {
        return array(
            'exp_initials' => array(),
            'exp_reading_time' => array(
                'words_per_minute' => array( 'type' => 'integer',
                                             'required' => false,
                                             'default' => 200 ) )
        );
    }

    /**
     * @param eZTemplate $tpl
     * @param string $operatorName
     * @param array $operatorParameters
     * @param string $rootNamespace
     * @param string $currentNamespace
     * @param mixed $operatorValue the value the operator is applied to; replace it with the result
     * @param array $namedParameters
     */
    public function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace,
                            &$operatorValue, $namedParameters )
    {
        switch ( $operatorName )
        {
            case 'exp_initials':
                $initials = '';
                foreach ( preg_split( '/\s+/u', trim( (string)$operatorValue ), -1, PREG_SPLIT_NO_EMPTY ) as $word )
                    $initials .= mb_strtoupper( mb_substr( $word, 0, 1 ) );
                $operatorValue = $initials;
                break;

            case 'exp_reading_time':
                $perMinute = max( 1, (int)$namedParameters['words_per_minute'] );
                $words = str_word_count( strip_tags( (string)$operatorValue ) );
                $operatorValue = max( 1, (int)ceil( $words / $perMinute ) );
                break;
        }
    }
}
```

Die vier Methoden sind das, was die Engine von einem Operatorobjekt abfragt (`lib/eztemplate/classes/eztemplate.php`):

| Methode | Liefert |
|---|---|
| `operatorList()` | die Namen, die das Objekt beantwortet |
| `namedParameterPerOperator()` | `true`, wenn `namedParameterList()` nach Operatornamen geschlüsselt ist |
| `namedParameterList()` | je Operator: Parametername => `type`, `required`, `default`; das Template übergibt sie nach Position |
| `modify( ... &$operatorValue, $namedParameters )` | schreibt das Ergebnis in `$operatorValue` |

`$operatorValue` ist der Wert vor dem senkrechten Strich (`null`, wenn der Operator als Funktion aufgerufen wird,
`{exp_initials()}`). Geben Sie Text unmaskiert zurück: Das Template entscheidet mit `|wash`, ob er HTML-maskiert wird.

## 3. Verwenden

```tpl
<p class="byline">
    <span class="initials">{$node.object.owner.name|exp_initials}</span>
    {'%minutes min read'|i18n( 'extension/expexample', '', hash( '%minutes', $node.data_map.body.content|exp_reading_time ) )}
</p>
<p>{$node.data_map.body.content|exp_reading_time( 120 )} min at a slow pace</p>
```

## 4. Bekannt machen und prüfen

```bash
php -l extension/expexample/autoloads/eztemplateautoload.php
php -l extension/expexample/autoloads/expexampletemplateoperators.php
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/ezcache.php --clear-tag=template --allow-root-user
```

Die Operatorklasse steht nicht im Autoload-Array; sie wird über den Pfad in `script` eingebunden. Unter Velocity
starten Sie ihn neu (`./console exp:velocity restart --allow-root-user` oder das ganze
[Deployment](velocity-deployment.md)): Seine Worker lesen die Template-Einstellungen und die Operatorliste beim Start.

Laden Sie eine Seite neu, die den Operator verwendet. Ein unbekannter Operator wird in der Debug-Ausgabe als
*Operator 'exp_initials' is not registered* gemeldet: Die Einstellung wird nicht gelesen (Erweiterung nicht aktiv,
INI-Cache), oder der Name in `eztemplateautoload.php` weicht von `operatorList()` ab.

Für diese Seite geprüft (2026-10-18): `php -l` für beide PHP-Dateien; die Klasse hat dieselben Methoden mit denselben
Signaturen wie `extension/ezjscore/autoloads/ezjscaccesstemplatefunctions.php`.

## Häufige Fehler

| Symptom | Ursache |
|---|---|
| *Operator ... is not registered* | `ExtensionAutoloadPath[]` fehlt, die Erweiterung ist inaktiv, oder der INI-Cache |
| Der Parameter hat immer den Standardwert | `namedParameterPerOperator()` gibt `false` zurück, obwohl die Liste nach Operator geschlüsselt ist, oder der Parametername weicht ab |
| HTML erscheint als Text oder Text als HTML | maskieren Sie im Template mit `\|wash`, nicht im Operator |
| Ein aufwendiger Operator verlangsamt jede Seite | speichern Sie sein Ergebnis zwischen (`cache-block`, [Caching](caching.md)) oder berechnen Sie es einmal in der Ansicht |

## Abgeleitet aus

- `extension/ezjscore/autoloads/eztemplateautoload.php` und `ezjscaccesstemplatefunctions.php`
- `lib/eztemplate/classes/eztemplate.php` (`ExtensionAutoloadPath`, `AutoloadPathList`, das Laden der Operatoren)
- `extension/ezjscore/settings/site.ini.append.php`

## Verwandte Seiten

- [Kochbuch](README.md)
- [Template-Operatoren](../template-operatoren/README.md): jeder Operator mit seinen Parametern
- [Template-Operatoren für Zeichenketten](../../funktionen/6.0/template-operatoren-fuer-zeichenketten.md)
- [Templates und Design](../../anleitungen/templates-und-design.md), [Ein Template-Override](template-override.md)
- [RAD-Werkzeuge](../../funktionen/6.0/rad-werkzeuge.md): `/setup/templateoperator` schreibt ein Gerüst für einen
  Operator
- Die Programmierregeln und der Musterkatalog: [die Standards](../../standards/README.md)
- [Freie Dokumentation](../../hilfe/freie-dokumentation.md): Diese Seite darf frei kopiert, übersetzt und verbessert
  werden.
