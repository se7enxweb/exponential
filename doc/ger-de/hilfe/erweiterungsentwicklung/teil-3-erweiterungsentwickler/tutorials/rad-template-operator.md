---
title: "Mit RAD: ein Template-Operator in fünf Minuten"
audience: developer, app-creator, designer
category: extension-development
order: 20
summary: Mit dem Assistenten für Template-Extensions eine funktionierende Erweiterung mit einem Operator und einer Fetch-Funktion schreiben, jede Datei ansehen, die er schreibt, die eine Zeile des Operators ausfüllen, einschalten und verwenden - mit der echten Ausgabe des Assistenten.
keywords: RAD, Assistent für Template-Extensions, Template-Operator, Fetch-Funktion, Fetch-Alias, Template-Funktion, erzeugter Code, template extension wizard, setup/templateoperator, template operator, fetch function, fetch alias, template function, generated code, operatorTemplateHints, ExtensionAutoloadPath
type: help
---
# Mit RAD: ein Template-Operator in fünf Minuten

Tutorial 2. Der **Assistent für Template-Extensions** (`/setup/templateoperator`) schreibt Operatoren,
Template-Funktionen, Fetch-Funktionen und Fetch-Aliase. Dieses Tutorial erstellt einen Operator `mysite_greet` und eine
Fetch-Funktion `newest`. Die Dateiliste und die Code-Auszüge unten sind die echte Ausgabe des Assistenten: Sein
Generator (`expTemplateExtensionWizard::files()`) wurde mit denselben Eingaben ausgeführt, jede erzeugte PHP-Datei hat
`php -l` bestanden, und der Operator wurde, nachdem er ausgefüllt war, von der Template-Engine gerendert.

## 1. Den Assistenten öffnen

**Setup > RAD**, dann das Werkzeug unter „Template operator“, oder direkt `/setup/templateoperator`. Die Seite erklärt
die vier Arten, die er hinzufügen kann: „Ein Operator nimmt einen Wert und gibt einen zurück. Eine Funktion schreibt
ihre Ausgabe dort, wo sie steht, und kann einen Rumpf haben. Eine Fetch-Funktion liest etwas, mit vorheriger
Richtlinienprüfung. Ein Fetch-Alias ist ein Fetch mit bereits festgelegten Argumenten.“

## 2. Das Formular ausfüllen

| Feld | Wert |
|---|---|
| Operatoren | `mysite_greet` |
| Fetch-Funktionen | `newest` |
| Parameter | `name string required` und, in der nächsten Zeile, `greeting string` |
| Operatoren nehmen eine Eingabe | aus (der Operator bildet seinen Wert aus seinen Parametern) |
| Operatoren erzeugen eine Ausgabe | an |
| Name der Extension | `mysite_hello` |
| Klassenname | `mysiteHelloOperators` |
| Modul für die Fetches | `hello` |
| Titel | `Hello operators` |

Parameter stehen „einer pro Zeile: ein Name, dann ein Typ, dann das Wort required, falls er erforderlich ist“. **Was der
Compiler annehmen darf** legt die Template-Hinweise des Operators fest; lassen Sie die Voreinstellungen stehen, bis Sie
wissen, dass Ihr Operator rein ist.

## 3. Die Vorschau lesen

**Vorschau aktualisieren**. „Jede Datei, bevor sie geschrieben wird“ listet auf:

```text
.gitignore
LICENSE
README.md
autoloads/eztemplateautoload.php
autoloads/mysitehellooperators.php
classes/mysitehellooperatorsfunctioncollection.php
composer.json
doc/examples.php
extension.xml
ezinfo.php
modules/hello/function_definition.php
modules/hello/module.php
settings/module.ini.append.php
settings/site.ini.append.php
```

Öffnen Sie einige davon. `settings/site.ini.append.php` enthält die Zeile, die am häufigsten vergessen wird, mit einem
Kommentar, der sagt, warum:

```ini
[TemplateSettings]
# The extension whose autoloads/eztemplateautoload.php is read. Without this
# line the classes are never loaded and every name below is an unknown
# operator, reported in the debug output and nowhere else.
ExtensionAutoloadPath[]=mysite_hello
```

`autoloads/mysitehellooperators.php` hat ein ordentliches `__construct()`, `operatorList()`,
`namedParameterPerOperator()`, `namedParameterList()`, `operatorTemplateHints()` und ein `modify()`, das noch nichts
tut, jeweils mit einem Kommentar. Der erzeugte Code tut absichtlich nichts, wie die README sagt: Ein Template, das heute
einen davon verwendet, gibt aus, was es vorher ausgegeben hat, und deshalb lässt sich die Erweiterung gefahrlos
installieren, bevor sie ausgeschrieben ist.

## 4. Anlegen

**In extension/ anlegen**. Wenn der Webserver dort nicht schreiben darf, wählen Sie **Als ZIP herunterladen** und
entpacken es selbst nach `extension/mysite_hello`.

## 5. Den Operator schreiben

In `autoloads/mysitehellooperators.php` liest der `modify()`-Fall für `mysite_greet` die Parameter und endet mit einem
Hinweis: „This operator takes no input, so set the value rather than change it“ (dieser Operator nimmt keine Eingabe,
setzen Sie also den Wert, statt ihn zu ändern). Fügen Sie eine Zeile hinzu:

```php
case 'mysite_greet':
{
    $name = $namedParameters['name'];
    $greeting = $namedParameters['greeting'];

    $operatorValue = ( $greeting ? $greeting : 'Hello' ) . ', ' . $name . '!';
} break;
```

## 6. Einschalten

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_hello global
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-all
```

## 7. Verwenden

```text
[{mysite_greet( 'Ada' )|wash}] [{mysite_greet( 'Ada', 'Welcome' )|wash}]
```

ergibt `[Hello, Ada!] [Welcome, Ada!]`.

> **Hinweis**
> Operatorparameter werden **nach Position** übergeben, in der Reihenfolge der Parameterliste. Die erzeugte `README.md`
> und `doc/examples.php` zeigen Aufrufe in der Form `{mysite_greet( name='text', greeting='text' )}`; diese Form nimmt
> die Template-Engine für Operatoren nicht an (sie meldet „Extra characters found in expression“). Schreiben Sie
> stattdessen `{mysite_greet( 'text', 'text' )}`.

## 8. Die Fetch-Funktion

`classes/mysitehellooperatorsfunctioncollection.php` enthält `fetchNewest()`, das `array( 'result' => array() )`
zurückgibt, mit der Warnung „Every argument arrives exactly as a template wrote it, which means it may have come from a
url“ (jedes Argument kommt genau so an, wie ein Template es geschrieben hat, und kann daher aus einer URL stammen).
Füllen Sie es aus wie in
[Fetch-Funktionen](../fetch-funktionen.md); das
Modul, seine `function_definition.php` und die Registrierung in `module.ini.append.php` sind bereits geschrieben.

## Was Sie für fünf Minuten bekommen haben

Eine vollständige, dokumentierte und lizenzierte Erweiterung mit Metadaten, Composer-Datei, README und Beispielen; einen
korrekt angeschlossenen Operator; eine Fetch-Funktion in ihrem eigenen Modul. Vergleichen Sie sie mit der von Hand
geschriebenen aus [Von Hand: ein Template-Operator](von-hand-template-operator.md).

## Verwandte Seiten

- Zurück: [Der Rundgang durch die RAD-Werkzeuge: Setup > RAD](rundgang-durch-die-rad-werkzeuge.md)
- Weiter: [Mit RAD: ein Datentyp](rad-datentyp.md)
- [Template-Operatoren](../template-operatoren.md), [Fetch-Funktionen](../fetch-funktionen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
