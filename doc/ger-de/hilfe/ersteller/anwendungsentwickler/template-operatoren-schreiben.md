---
title: Template-Operatoren schreiben
audience: app-creator, developer
category: app-creation
order: 170
summary: Eigene Template-Operatoren hinzufügen - die Autoload-Registrierungsdatei, die Operator-Klasse mit operatorList, namedParameterList und modify, Eingabe per Pipe und per Parameter, Maskierung und wann ein Operator das falsche Werkzeug ist.
keywords: Template-Operator, Registrierung, Operator-Klasse, Templates, Anwendungsentwickler, template operator, eztemplateautoload.php, eZTemplateOperatorArray, operatorList, namedParameterList, modify, operatorValue, ExtensionAutoloadPath, templates, app creator
type: help
---
# Template-Operatoren schreiben

Diese Seite richtet sich an Anwendungsentwickler, die Templates einen neuen Operator geben, etwa
`{$price|expdemo_money( 'EUR' )}`. Ein Operator ist eine PHP-Klasse, die in `autoloads/eztemplateautoload.php` der
Erweiterung registriert ist. Das RAD-Werkzeug **Template-Erweiterung** (`/setup/templateoperator`) schreibt Ihnen einen
funktionierenden Operator ([RAD-Werkzeuge](rad-werkzeuge.md)).

## Inhalt

1. [Registrierung](#registrierung)
2. [Die Operator-Klasse](#die-operator-klasse)
3. [Eingabe und Ausgabe](#eingabe-und-ausgabe)
4. [Regeln](#regeln)
5. [Wann Sie keinen Operator schreiben sollten](#wann-sie-keinen-operator-schreiben-sollten)

## Registrierung

```ini
# extension/expdemo/settings/site.ini.append.php
<?php /* #?ini charset="utf-8"?
[TemplateSettings]
ExtensionAutoloadPath[]=expdemo
*/ ?>
```

```php
<?php
// extension/expdemo/autoloads/eztemplateautoload.php
$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array(
    'script' => 'extension/expdemo/autoloads/expdemotemplateoperators.php',
    'class' => 'expDemoTemplateOperators',
    'operator_names' => array( 'expdemo_money' ) );
```

Die mitgelieferte Erweiterung `ezstarrating` registriert ihre Operatoren auf dieselbe Weise
(`extension/ezstarrating/autoloads/eztemplateautoload.php`).

## Die Operator-Klasse

```php
<?php
class expDemoTemplateOperators
{
    function operatorList()
    {
        return array( 'expdemo_money' );
    }

    function namedParameterPerOperator()
    {
        return true;
    }

    function namedParameterList()
    {
        return array( 'expdemo_money' => array(
            'currency' => array( 'type' => 'string', 'required' => false, 'default' => 'EUR' ) ) );
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )
    {
        switch ( $operatorName )
        {
            case 'expdemo_money':
                $operatorValue = number_format( (float)$operatorValue, 2 ) . ' ' . $namedParameters['currency'];
                break;
        }
    }
}
```

## Eingabe und Ausgabe

- Der per Pipe übergebene Wert (`$price|expdemo_money`) kommt in `$operatorValue` an; setzen Sie ihn auf das Ergebnis.
- Benannte Parameter kommen in `$namedParameters` an, mit angewendeten Standardwerten.
- Ein Operator, der ohne Eingabe verwendet wird (`{expdemo_money( 12 )}`), erhält `null` als `$operatorValue`;
  deklarieren Sie den Wert als Parameter, wenn er so verwendet wird.

## Regeln

- Geben Sie Werte zurück, kein HTML: Lassen Sie das Template sie ausgeben und mit `wash` maskieren. Wenn Sie doch HTML
  zurückgeben müssen, maskieren Sie jeden Teil der Eingabe mit `htmlspecialchars()`.
- Halten Sie Operatoren schnell und frei von Nebenwirkungen; sie laufen bei jedem Rendern und innerhalb von
  Cache-Blöcken.
- Stellen Sie neuen Operatornamen den Namen Ihrer Erweiterung voran (`expdemo_...`), damit sie nie kollidieren.
- Generieren Sie die Autoloads neu und leeren Sie die Template-Caches, nachdem Sie einen hinzugefügt haben.

## Wann Sie keinen Operator schreiben sollten

| Bedarf | Besser |
|---|---|
| Daten lesen | eine Fetch-Funktion ([Eine eigene Fetch-Funktion schreiben](eine-eigene-fetch-funktion-schreiben.md)) |
| Zeichenketten formatieren | die mitgelieferten String-Operatoren ([String-Template-Operatoren](../../../../eng-us/features/6.0/string-template-operators.md)) |
| Rechteprüfungen | [Template-Operatoren für Rollen und Richtlinien](../../../../eng-us/features/6.0/role-and-policy-template-operators.md) |
| Markup, das in vielen Templates wiederverwendet wird | ein `{include}` eines Template-Teils |

## Verwandte Seiten

- [Template-Funktionen und Includes](template-funktionen-und-includes.md)
- [Muster für Template-Operatoren](../../../../eng-us/standards/patterns/template-operator.md)
- [Templates und Design](../../../anleitungen/templates-und-design.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
