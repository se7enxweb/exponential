---
title: Schritt für Schritt von einer Erweiterung lernen
audience: user, developer, designer, app-creator
category: extension-development
order: 180
summary: Zwei echte Erweiterungen, Datei für Datei zerlegt - str_replace, ein Operator in seiner Form von 2005 mit einem PHP-8-Fehler und seiner einzeiligen Korrektur, und owsimpleoperator.
keywords: lernen, lesen, Beispiel, Template-Operator, Konstruktor, learn, read, example, str_replace, ezstr_replace, owsimpleoperator, template operator, eztemplateautoload, ExtensionAutoloadPath, operatorList, modify, constructor, PHP 8
type: help
---
# Schritt für Schritt von einer Erweiterung lernen

Teil 1, Kapitel 18. Der schnellste Weg, Erweiterungen zu verstehen, ist, eine kleine zu zerlegen. Dieses Kapitel liest
zwei echte Erweiterungen aus Ihrer Installation, Datei für Datei, in der Reihenfolge, in der der Kernel sie liest. Hier
begegnen Sie zum ersten Mal dem Mechanismus der Template-Operatoren;
[Template-Operatoren](../teil-3-erweiterungsentwickler/template-operatoren.md) behandelt ihn später vollständig.

## Erweiterung 1: str_replace

`extension/str_replace` fügt einen Template-Operator hinzu, `ezstr_replace`, der PHPs `str_replace()` umhüllt. Ihre
README datiert das erste Release auf April 2005, und der Code ist noch in dieser ursprünglichen Form, was ihn zu einem
perfekten Studienobjekt macht: klein, vollständig und alt genug, um eine Lektion über PHP 8 zu erteilen.

### Schritt 1: die Dateien auflisten

```bash
find extension/str_replace -type f -not -path '*/.git/*' | sort
```

```text
extension/str_replace/LICENSE
extension/str_replace/LICENSE.md
extension/str_replace/README.md
extension/str_replace/autoloads/eztemplateautoload.php
extension/str_replace/autoloads/str_replace_controloperator.php
extension/str_replace/composer.json
extension/str_replace/doc/readme.txt
extension/str_replace/ezinfo.php
extension/str_replace/settings/site.ini.append.php
```

Drei Dateien erledigen die Arbeit: eine Einstellung, eine Registrierung, eine Klasse.

### Schritt 2: die Einstellung, die sie einsteckt

`settings/site.ini.append.php`:

```ini
[TemplateSettings]
ExtensionAutoloadPath[]=str_replace
```

`ExtensionAutoloadPath[]` sagt der Template-Engine: „Lies auch `extension/str_replace/autoloads/eztemplateautoload.php`.“
Das ist der ganze Stecker. Prüfen Sie, wie die Engine ihn liest:
`grep -n "ExtensionAutoloadPath" lib/eztemplate/classes/eztemplate.php`.

### Schritt 3: die Registrierung

`autoloads/eztemplateautoload.php`:

```php
<?php
$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] =
  array( 'script' => 'extension/str_replace/autoloads/str_replace_controloperator.php',
         'class' => 'MyStrReplaceOperator',
         'operator_names' => array( 'ezstr_replace' ) );
```

Sie nennt die Datei, die Klasse und die Operatornamen. Die Engine registriert die Namen sofort, lädt die Klasse aber
erst, wenn ein Template einen davon verwendet (`eZTemplate::registerAutoloadOperators()` und
`loadAndRegisterOperators()` in `lib/eztemplate/classes/eztemplate.php`).

### Schritt 4: die Klasse

Die Klasse hat vier Methoden, die die Engine aufruft:

| Methode | Aufgerufen | Gibt zurück |
|---|---|---|
| `operatorList()` | wenn die Klasse geladen wird | die Operatornamen, die dieses Objekt behandelt |
| `namedParameterPerOperator()` | wenn die Klasse geladen wird | `true`: Die folgende Parameterliste gilt pro Operator |
| `namedParameterList()` | wenn ein Template den Operator aufruft | die Parameter jedes Operators: Name, Typ, Pflicht, Standardwert |
| `modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters )` | bei jedem Aufruf | nichts; sie schreibt das Ergebnis in `$operatorValue` |

Der interessante Teil von `modify()`:

```php
$subject = $namedParameters['subject'] !== null
    ? $namedParameters['subject']
    : $operatorValue;
$operatorValue = str_replace( $search, $replace, $subject );
```

`$operatorValue` ist zugleich Eingabe und Ausgabe: Wenn ein Template `{$text|ezstr_replace( 'a', 'b' )}` schreibt,
kommt das per Pipe übergebene `$text` in `$operatorValue` an, und was die Methode dort hinterlässt, ist das Ergebnis.

### Schritt 5: die Lektion über PHP 8

Die Klasse richtet ihre Operatorliste in einer Methode ein, die wie die Klasse heißt:

```php
function MyStrReplaceOperator()
{
    $this->Operators = array( 'ezstr_replace');
}
```

Das war in PHP 4 und PHP 5 ein Konstruktor. **PHP 8 behandelt eine Methode, die wie ihre Klasse heißt, nicht mehr als
Konstruktor**, also wird sie nie aufgerufen, `$this->Operators` bleibt `null`, und `operatorList()` gibt `null` zurück.
Unter PHP 8.5 mit der Testumgebung dieses Buchs gerendert, erzeugt ein Template, das `ezstr_replace` aufruft, eine
PHP-Warnung „foreach() argument must be of type array|object, null given“ in
`eZTemplate::registerOperatorsInternal()`, gefolgt vom Template-Fehler „Object problem with operator 'ezstr_replace'“,
und der Operator gibt nichts aus.

Die Korrektur ist eine Zeile, die Umbenennung der Methode in `__construct`:

```php
public function __construct()
{
    $this->Operators = array( 'ezstr_replace' );
}
```

Mit dieser Änderung gibt dasselbe Template für `{ezstr_replace( 'world', 'Exponential', 'Hello world' )}` den Text
`Hello Exponential` aus. Die Testumgebung dieses Buchs hat beide Fassungen gerendert, bevor diese Seite geschrieben
wurde. Das ist das häufigste PHP-8-Problem in alten Erweiterungen;
[Konstruktoren im alten Stil und statische Aufrufe](../teil-4-aktualisieren/alte-konstruktoren-und-statische-aufrufe.md) zeigt, wie
Sie jeden solchen Fall finden und korrigieren.

### Schritt 6: die Metadaten

`ezinfo.php` nennt die Erweiterung, die Version `1.0.0`, das Copyright von 7x und des ursprünglichen Autors (2005) und
die Lizenz. Ihre Methode `info()` ist nicht als `static` deklariert, was zwar noch funktioniert, aber von der Konvention
abweicht, die der Kernel am besten liest; siehe [ezinfo.php](../teil-3-erweiterungsentwickler/ezinfo-php.md).
`composer.json` deklariert das Paket `se7enxweb/str_replace` vom Typ `ezpublish-legacy-extension`, `GPL-2.0-or-later`,
und verlangt `"php": ">=5.0.0"`, ein weiteres Zeichen ihres Alters: Ein modernisiertes Paket nennt die PHP-Versionen,
mit denen es getestet wurde.

## Erweiterung 2: owsimpleoperator

`extension/owsimpleoperator` (von OPEN WIDE, laut README und `ezinfo.php` unter GPL 3 oder später) löst das Problem mit
dem Standardcode, das Sie gerade gesehen haben: Sie können einen Operator als einfache Methode schreiben, und Templates
dürfen PHP-Funktionen aufrufen, die Sie erlauben.

### Ihre Dateien

```bash
ls extension/owsimpleoperator/autoloads extension/owsimpleoperator/settings
```

```text
exampleoperator.php  eztemplateautoload.php  owezpautoloader.php  owsimpleoperator.php  phpfunctionoperator.php
owsimpleoperator.ini.append.php  site.ini.append.php
```

Der Stecker ist dasselbe `ExtensionAutoloadPath[]=owsimpleoperator` in `site.ini.append.php`. Der Unterschied liegt in
der Registrierung: `eztemplateautoload.php` baut `$eZTemplateOperatorArray` **aus Einstellungen** auf:

- `[PHPFunctions] PermittedFunctionList[]` in `owsimpleoperator.ini` listet PHP-Funktionen auf, die Templates als
  Operatoren aufrufen dürfen. Die mitgelieferte Liste umfasst `time`, `mktime`, `getdate`, `str_replace` und
  `str_rot13`.
- `[ClassOperators] PermittedClassOperatorList[]` listet Tripel `file;class;method` auf; jede Methode wird zu einem
  Operator.

### Ein Operator als Methode

`autoloads/exampleoperator.php`:

```php
<?php
class ExampleOperator extends OWSimpleOperator
{
    public function example_sum( $number1, $number2 )
    {
        return ( $number1 + $number2 );
    }
}
```

Die Basisklasse `OWSimpleOperator` implementiert `operatorList()`, `namedParameterList()` und `modify()` für Sie: Sie
ruft die Methode, die wie der Operator heißt, mit den Argumenten des Templates auf und verwendet den Rückgabewert. Um
das Beispiel einzuschalten, würden Sie in Ihren eigenen Einstellungen Folgendes hinzufügen:

```ini
[ClassOperators]
PermittedClassOperatorList[]=extension/owsimpleoperator/autoloads/exampleoperator.php;ExampleOperator;example_sum
```

### Die PHP-Funktionsoperatoren ausprobieren

Auf einer Installation, auf der `owsimpleoperator` aktiv ist (prüfen Sie es mit
`./console exp:ini get site.ini/ExtensionSettings/ActiveExtensions`), kann ein Template schreiben:

```text
[{str_rot13( 'Exponential' )}] [{str_replace( 'world', 'there', 'Hello world' )}]
```

was `[Rkcbaragvny] [Hello there]` ergibt. Diese Ausgabe hat die Template-Engine des Kernels erzeugt, als diese Seite
geschrieben wurde.

> **Warnung**
> Jede Funktion in `PermittedFunctionList[]` kann von jedem Template mit beliebigen Argumenten aufgerufen werden.
> Führen Sie nur Funktionen ohne Nebenwirkungen auf, die nichts außerhalb ihrer Argumente lesen. Eine Funktion wie
> `file_get_contents` würde einem Template erlauben, jede Datei zu lesen, die der Webserver lesen kann; die
> mitgelieferte Einstellungsdatei sagt genau das in ihrem Kommentar.

## Was Sie gelernt haben

| Konzept | Wo Sie es gesehen haben |
|---|---|
| Eine Erweiterung steckt sich ein, indem sie etwas zu einer Einstellung hinzufügt | `ExtensionAutoloadPath[]` |
| Operatoren werden über ihren Namen registriert und beim ersten Gebrauch geladen | `eztemplateautoload.php` |
| Der Vertrag eines Operators: `operatorList`, `namedParameterList`, `modify` | `MyStrReplaceOperator` |
| `$operatorValue` ist Eingabe und Ausgabe | `modify()` |
| PHP 8 ignoriert Konstruktoren im alten Stil | `function MyStrReplaceOperator()` |
| Eine Basisklasse kann Standardcode überflüssig machen | `OWSimpleOperator` |
| Einstellungen können erweitern, was Templates tun dürfen, also halten Sie sie eng | `PermittedFunctionList[]` |

## Verwandte Seiten

- Zurück: [Eine alte Erweiterung bewerten, bevor Sie sie verwenden](eine-alte-erweiterung-bewerten.md)
- Weiter: [Debug-Ausgabe und Logs für Entwickler](debug-ausgabe-und-logs-fuer-entwickler.md)
- [Template-Operatoren](../teil-3-erweiterungsentwickler/template-operatoren.md)
- [Von Hand: ein Template-Operator](../teil-3-erweiterungsentwickler/tutorials/von-hand-template-operator.md)
- [owsimpleoperator im Erweiterungskatalog](../../../../eng-us/features/6.0/extensions/owsimpleoperator.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
