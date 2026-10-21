---
title: "Von Hand: ein Template-Operator"
audience: developer, app-creator, designer
category: extension-development
order: 110
summary: mysite_hello von Hand zwei Template-Operatoren hinzufügen - der Anschluss, die Registrierung und die Klasse -, sie mit php -l prüfen, einschalten und verwenden, mit der Ausgabe, die die Template-Engine erzeugt hat.
keywords: Tutorial, von Hand, Template-Operator, Registrierung, Anschluss, tutorial, by hand, template operator, eztemplateautoload.php, ExtensionAutoloadPath, operatorList, namedParameterList, modify, mysite_greet, mysite_initials
type: help
---
# Von Hand: ein Template-Operator

Tutorial 10. Sie fügen der Erweiterung `mysite_hello` aus dem vorigen Tutorial zwei Operatoren hinzu:
`mysite_greet( name, greeting )` und `$text|mysite_initials`. Jede Datei hat `php -l` bestanden; das Template am Ende
wurde von der Template-Engine mit diesen Dateien gerendert.

## 1. Der Anschluss

Ergänzen Sie `extension/mysite_hello/settings/site.ini.append.php`:

```php
<?php /* #?ini charset="utf-8"?

[TemplateSettings]
ExtensionAutoloadPath[]=mysite_hello

*/ ?>
```

## 2. Die Registrierung

`extension/mysite_hello/autoloads/eztemplateautoload.php`:

```php
<?php
$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array(
    'script' => 'extension/mysite_hello/classes/mysitehellooperators.php',
    'class' => 'mysiteHelloOperators',
    'operator_names' => array( 'mysite_greet', 'mysite_initials' )
);
```

## 3. Die Klasse

`extension/mysite_hello/classes/mysitehellooperators.php`:

```php
<?php
/**
 * Template operators of mysite_hello:
 *   {mysite_greet( 'Ada' )}            -> Hello, Ada!
 *   {mysite_greet( 'Ada', 'Welcome' )} -> Welcome, Ada!
 *   {'Ada Lovelace'|mysite_initials}   -> AL
 */
class mysiteHelloOperators
{
    public function operatorList()
    {
        return array( 'mysite_greet', 'mysite_initials' );
    }

    public function namedParameterPerOperator()
    {
        return true;
    }

    public function namedParameterList()
    {
        return array(
            'mysite_greet' => array(
                'name'     => array( 'type' => 'string', 'required' => true, 'default' => '' ),
                'greeting' => array( 'type' => 'string', 'required' => false, 'default' => 'Hello' )
            ),
            'mysite_initials' => array()
        );
    }

    public function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        switch ( $operatorName )
        {
            case 'mysite_greet':
                $operatorValue = $namedParameters['greeting'] . ', ' . $namedParameters['name'] . '!';
                break;

            case 'mysite_initials':
                $initials = '';
                foreach ( preg_split( '/\s+/u', trim( (string)$operatorValue ) ) as $word )
                {
                    if ( $word !== '' )
                        $initials .= mb_strtoupper( mb_substr( $word, 0, 1 ) );
                }
                $operatorValue = $initials;
                break;
        }
    }
}
```

## 4. Prüfen und einschalten

```bash
php -l extension/mysite_hello/autoloads/eztemplateautoload.php
php -l extension/mysite_hello/classes/mysitehellooperators.php
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

## 5. Verwenden

Setzen Sie Folgendes in ein beliebiges Template, das Sie öffnen können, zum Beispiel einen Override der Startseite:

```text
[{mysite_greet( 'Ada' )|wash}] [{mysite_greet( 'Ada', 'Welcome' )|wash}] [{'Ada Lovelace'|mysite_initials}] [{'émile zola'|mysite_initials}]
```

Es ergibt:

```text
[Hello, Ada!] [Welcome, Ada!] [AL] [ÉZ]
```

## 6. Wenn es nicht funktioniert

| Meldung in der Debug-Ausgabe | Abhilfe |
|---|---|
| `Operator 'mysite_greet' is not registered` | Schritt 1 fehlt, die Erweiterung ist inaktiv oder der INI-Cache wurde nicht geleert |
| `Object problem with operator 'mysite_greet'` | die Klasse konnte nicht erzeugt werden: Prüfen Sie den Pfad unter `script` und ob `operatorList()` die Namen zurückgibt |
| es wird nichts ausgegeben | `modify()` hat `$operatorValue` für diesen Operator nicht gesetzt |

## 7. Committen

```bash
git -C extension/mysite_hello add autoloads classes settings
git -C extension/mysite_hello commit -m "Added: The template operators mysite_greet and mysite_initials"
```

## Verwandte Seiten

- Zurück: [Von Hand: die kleinste Erweiterung in fünf Minuten](von-hand-minimale-erweiterung.md)
- Weiter: [Von Hand: eine Fetch-Funktion](von-hand-fetch-funktion.md)
- [Template-Operatoren](../template-operatoren.md), [Mit RAD: ein Template-Operator in fünf Minuten](rad-template-operator.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
