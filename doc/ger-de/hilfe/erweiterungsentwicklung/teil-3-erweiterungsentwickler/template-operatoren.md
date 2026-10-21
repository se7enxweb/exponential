---
title: Template-Operatoren
audience: developer, app-creator, designer
category: extension-development
order: 130
summary: Templates neue Operatoren geben - die Registrierung, die vier Methoden, die die Engine aufruft, benannte Parameter und weitergeleitete Eingaben, ein vollständiges geprüftes Beispiel, Tests und die Stolperfallen von PHP 8.
keywords: Template-Operator, Registrierung, benannte Parameter, Pipe, Test, PHP 8, template operator, eztemplateautoload.php, eZTemplateOperatorArray, ExtensionAutoloadPath, operatorList, namedParameterList, namedParameterPerOperator, modify, operatorValue, named parameters, pipe
type: help
---
# Template-Operatoren

Teil 3, Kapitel 13. Ein **Template-Operator** ist eine Funktion, die Templates aufrufen können:
`{mysite_greet( 'Ada' )}` oder `{$name|mysite_initials}`. Er ist das kleinste nützliche Stück PHP, das eine
Erweiterung hinzufügen kann, und ein guter Einstieg. Dieses Kapitel baut eine Klasse mit zwei Operatoren; die Klasse und
das Template unten wurden von der Template-Engine des Kernels gerendert, bevor diese Seite geschrieben wurde.

## Drei Teile

| Teil | Datei |
|---|---|
| Der Anschluss | `settings/site.ini.append.php`: `[TemplateSettings] ExtensionAutoloadPath[]=mysite_hello` |
| Die Registrierung | `autoloads/eztemplateautoload.php`: welche Klasse welche Operatornamen verarbeitet |
| Die Klasse | `classes/mysitehellooperators.php` |

## Der Anschluss

```php
<?php /* #?ini charset="utf-8"?

[TemplateSettings]
ExtensionAutoloadPath[]=mysite_hello

*/ ?>
```

## Die Registrierung

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

Die Engine (`eZTemplate::registerAutoloadOperators()`, `lib/eztemplate/classes/eztemplate.php`) registriert die Namen
sofort und lädt die Klasse erst, wenn ein Template zum ersten Mal einen davon verwendet. Statt `operator_names` kann
eine Definition `operator_names_function` angeben, eine Funktion, die die Namen zurückgibt.

## Die Klasse

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

| Methode | Aufgerufen | Gibt zurück |
|---|---|---|
| `operatorList()` | wenn die Klasse geladen wird | die Operatornamen, die dieses Objekt verarbeitet |
| `namedParameterPerOperator()` | wenn die Klasse geladen wird | `true`: `namedParameterList()` ist nach Operatornamen geschlüsselt |
| `namedParameterList()` | wenn ein Aufruf kompiliert wird | die Parameter jedes Operators, in Reihenfolge: `type`, `required`, `default` |
| `modify( ... &$operatorValue, $namedParameters )` | bei jedem Aufruf | nichts: das Ergebnis wird in `$operatorValue` geschrieben |

`$operatorValue` ist die Eingabe eines weitergeleiteten Aufrufs (`{$text|mysite_initials}` übergibt `$text`) und die
Ausgabe jedes Aufrufs. `$namedParameters` enthält die Parameter unter den Namen, die Sie deklariert haben, mit
eingesetzten Standardwerten.

## Das Ergebnis

Das Template

```text
[{mysite_greet( 'Ada' )}] [{mysite_greet( 'Ada', 'Welcome' )}] [{'Ada Lovelace'|mysite_initials}] [{'émile zola'|mysite_initials}]
```

ergibt

```text
[Hello, Ada!] [Welcome, Ada!] [AL] [ÉZ]
```

Beachten Sie die `mb_`-Funktionen: Namen sind UTF-8, und `strtoupper()` würde das `é` zerstören.

## Maskieren ist Aufgabe des Templates

Ein Operator gibt einen Wert zurück; das Template entscheidet, wie er ausgegeben wird. Geben Sie reinen Text zurück und
lassen Sie die Templates ihn mit `|wash` behandeln, es sei denn, Ihr Operator soll HTML erzeugen; dann maskieren Sie
jeden Wert, den Sie in dieses HTML einsetzen, mit `htmlspecialchars()`.

## Machen Sie ihn rein

Die besten Operatoren hängen nur von ihren Parametern ab: keine Schreibzugriffe auf die Datenbank, keine Ausgabe, keine
Sitzung. Dann lassen sie sich gefahrlos aus gecachten Templates aufrufen und leicht testen. Ein Operator, der Daten
braucht (einen Fetch, eine Einstellung), darf sie lesen, sollte aber nie etwas ändern; Änderungen gehören in eine
Modul-View.

## Häufige Fehler

| Symptom | Ursache |
|---|---|
| `Operator 'x' is not registered` | der Anschluss fehlt, die Erweiterung ist nicht aktiv oder der INI-Cache wurde nicht geleert |
| `Object problem with operator 'x'` | die Klasse konnte nicht erzeugt werden oder `operatorList()` gab nichts zurück: ein Konstruktor alten Stils (eine Methode mit dem Namen der Klasse) ist in PHP 8 kein Konstruktor |
| Der Operator gibt nichts zurück | `modify()` hat `$operatorValue` nicht zugewiesen |
| Ein Parameter hat immer den Standardwert | er steht für diesen Operator nicht in `namedParameterList()` |

## Testen

Da die Klasse nichts außer ihren Parametern braucht, kann ein PHPUnit-Test `modify()` direkt aufrufen:

```php
<?php
class mysiteHelloOperatorsTest extends PHPUnit\Framework\TestCase
{
    public function testInitials()
    {
        $operators = new mysiteHelloOperators();
        $value = 'Ada Lovelace';
        $operators->modify( null, 'mysite_initials', array(), '', '', $value, array() );
        $this->assertSame( 'AL', $value );
    }
}
```

Siehe [Tests für Erweiterungen](tests-fuer-erweiterungen.md).

## Einfacher: owsimpleoperator

Wenn Sie viele kleine Operatoren haben, können Sie mit der Erweiterung `owsimpleoperator` jeden als einfache Methode
schreiben; siehe [Von einer Erweiterung lernen, Schritt für Schritt](../teil-1-vom-benutzer-zum-entwickler/schritt-fuer-schritt-von-einer-erweiterung-lernen.md).

## RAD

**Assistent für Template-Extensions** (`/setup/templateoperator`) schreibt Operatoren, Fetch-Funktionen, Attribut-Operatoren und
Template-Funktionen; siehe [Mit RAD: ein Template-Operator in fünf Minuten](tutorials/rad-template-operator.md).

## Verwandte Seiten

- Zurück: [View-Ergebnisse, der Pfad und das Pagelayout](view-ergebnisse-und-das-pagelayout.md)
- Weiter: [Fetch-Funktionen](fetch-funktionen.md)
- [Von Hand: ein Template-Operator](tutorials/von-hand-template-operator.md)
- [Eingebaute Template-Operatoren](../teil-8-referenz/referenz-der-template-operatoren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
