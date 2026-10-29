---
title: Eigene Operatoren mit OW Simple Operator
audience: developer, app-creator
category: development
order: 910
summary: Template-Operatoren als einfache öffentliche Methoden einer Klasse schreiben, die OWSimpleOperator erweitert, jede Methode in owsimpleoperator.ini registrieren, optionale Argumente, die Pipe-Form und was geschieht, wenn die Klasse stattdessen auf dem üblichen Weg registriert wird; jedes Beispiel auf einer Entwicklungsinstallation gerendert.
keywords: eigener Operator, Template-Operator-Klasse, Erweiterung, optionale Argumente, owsimpleoperator, OWSimpleOperator, PermittedClassOperatorList, ClassOperators, ExampleOperator, example_sum, custom operator, template operator class, extension
type: help
---
# Eigene Operatoren mit OW Simple Operator

Mit OW Simple Operator (`extension/owsimpleoperator`) ist ein Operator eine öffentliche Methode. Sie schreiben die Methode,
tragen sie in `owsimpleoperator.ini` ein, und die Basisklasse `OWSimpleOperator` liefert `operatorList()`,
`namedParameterList()`, `namedParameterPerOperator()` und `modify()`. Wann die Basisklasse genügt und wann eine
vollständige Operatorklasse die bessere Wahl ist, lesen Sie unter [OW Simple Operator: Grenzen und Risiken](grenzen-von-owsimpleoperator.md).

## Inhalt

1. [Die Klasse](#die-klasse)
2. [Die Methoden registrieren](#die-methoden-registrieren)
3. [Die Operatoren verwenden](#die-operatoren-verwenden)
4. [Optionale Argumente](#optionale-argumente)
5. [Registrieren Sie die Klasse nicht auf dem üblichen Weg](#registrieren-sie-die-klasse-nicht-auf-dem-üblichen-weg)
6. [Checkliste](#checkliste)

## Die Klasse

Die Erweiterung liefert ein Beispiel mit, `extension/owsimpleoperator/autoloads/exampleoperator.php`:

```php
class ExampleOperator extends OWSimpleOperator
{
    /*!
     * Return the sum of two numbers
     */
    public function example_sum( $number1, $number2 )
    {
        return ( $number1 + $number2 );
    }
}
```

Eine eigene Klasse liegt in Ihrer Erweiterung, zum Beispiel `extension/myextension/autoloads/c11exampletextoperators.php`.
Die Beispiele auf dieser Seite verwenden diese Klasse:

```php
class C11ExampleTextOperators extends OWSimpleOperator
{
    /*!
     * The initials of a name: "Fit & Healthy" -> "FH"
     */
    public function initials( $text )
    {
        $letters = '';
        foreach ( preg_split( '/[^\p{L}\p{N}]+/u', (string)$text, -1, PREG_SPLIT_NO_EMPTY ) as $word )
            $letters .= mb_strtoupper( mb_substr( $word, 0, 1 ) );
        return $letters;
    }

    /*!
     * Reading time in minutes for a text, at $wordsPerMinute (default 200)
     */
    public function reading_minutes( $text, $wordsPerMinute = null )
    {
        $wordsPerMinute = $wordsPerMinute ? (int)$wordsPerMinute : 200;
        $words = str_word_count( strip_tags( (string)$text ) );
        return max( 1, (int)ceil( $words / $wordsPerMinute ) );
    }
}
```

Der Methodenname ist der Operatorname. Wählen Sie daher Namen, die kein anderer Operator verwendet (siehe
[Registrierungsreihenfolge und Überschreibungen](registrierungsreihenfolge-und-ueberschreibungen.md)); ein Präfix wie der Name Ihrer
Erweiterung ist die sichere Gewohnheit. Die Methode gibt den Wert zurück; sie gibt nie etwas mit echo aus.

## Die Methoden registrieren

Jeder Operator ist eine Zeile `[ClassOperators] PermittedClassOperatorList[]` in `owsimpleoperator.ini`, in der Form
`Klassendatei;Klassenname;Methode`:

```ini
<?php /* #?ini charset="utf-8"?

[ClassOperators]
PermittedClassOperatorList[]=extension/myextension/autoloads/c11exampletextoperators.php;C11ExampleTextOperators;initials
PermittedClassOperatorList[]=extension/myextension/autoloads/c11exampletextoperators.php;C11ExampleTextOperators;reading_minutes

*/ ?>
```

Legen Sie dies in `extension/myextension/settings/owsimpleoperator.ini.append.php` ab (Ihre Erweiterung muss aktiv sein;
die INI-Dateien aller aktiven Erweiterungen werden zusammengeführt) oder in die Einstellungen eines Siteaccess.
Beginnen Sie Ihre Liste nicht mit einer leeren Zeile `PermittedClassOperatorList[]`: In einem INI-Append leert diese
die bis dahin gesammelte Liste und entfernt die Operatoren, die andere Erweiterungen registriert haben.

`extension/owsimpleoperator/autoloads/eztemplateautoload.php` macht aus diesen Zeilen je Klasse einen Eintrag in
`$eZTemplateOperatorArray` (`script`, `class`, `operator_names`). Leeren Sie nach einer Änderung den INI- und den
Template-Cache (`php bin/php/ezcache.php --clear-tag=ini --clear-tag=template --allow-root-user`, oder auf einer Entwicklungsinstallation
`./console exp:velocity deploy --allow-root-user`). Ihre Klasse braucht keinen Eintrag in den Autoload-Arrays: Der
Pfad in `script` lädt sie. `OWSimpleOperator` selbst braucht einen, und der Installationsschritt der Erweiterung
(`php bin/php/ezpgenerateautoloads.php -e`) erzeugt ihn.

## Die Operatoren verwenden

Auf einer Entwicklungsinstallation nicht mit einer Datei ausgeführt: Die Beispiele auf dieser Seite hat ein Skript gerendert, das die beiden
Zeilen oben (und die Zeile für das mitgelieferte `ExampleOperator`) nur im Speicher und nur für diesen einen Prozess in
`owsimpleoperator.ini` eingetragen hat, mit der Klassendatei in einem Arbeitsordner. In die Einstellungen der
Installation wurde nichts geschrieben.

Beide Aufrufformen funktionieren für Klassenoperatoren:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 89 ) )}
{example_sum( 1, 2 )}
{1|example_sum( 2 )}
{initials( 'Fit & Healthy' )}
{'Bold Agency'|initials}
{$node.name|wash} -> {$node.name|initials}
```

Ausgabe:

```text
3
3
FH
BA
Fit &amp; Healthy -> FH
```

In der Pipe-Form wird der gepipte Wert zum ersten Argument der Methode, und die Argumente in den Klammern folgen ihm:
`{1|example_sum( 2 )}` ruft `example_sum( 1, 2 )` auf.

## Optionale Argumente

`OWSimpleOperator::namedParameterList()` deklariert für jeden Operator zehn optionale Parameter mit dem Standardwert
`null`, und `modify()` übergibt alle zehn an Ihre Methode. Ein Argument, das das Template weglässt, kommt daher als
`null` an, nie als Ihr PHP-Standardwert. Deshalb sagt das README der Erweiterung: "If an operator argument is optional,
you have to set the default value to null." Geben Sie dem Parameter den Standardwert `null` und wählen Sie den
eigentlichen Standardwert im Methodenrumpf, wie `reading_minutes()` es tut:

```tpl
{def $text = 'one two three four five six seven eight nine ten'}
{reading_minutes( $text )} {reading_minutes( $text, 4 )} {$text|reading_minutes( 4 )}
```

Ausgabe:

```text
1 3 3
```

Zehn Wörter bei den standardmäßigen 200 Wörtern pro Minute ergeben aufgerundet 1, bei 4 Wörtern pro Minute 3, in beiden
Aufrufformen. Eine Methode, die mehr als zehn Argumente braucht, muss in ihrer Klasse `$max_operator_parameter` erhöhen.

## Registrieren Sie die Klasse nicht auf dem üblichen Weg

Eine Unterklasse von `OWSimpleOperator` muss in `owsimpleoperator.ini` registriert werden. `OWSimpleOperator::operatorList()`
liest nicht die `autoloads/eztemplateautoload.php` Ihrer Erweiterung: Es ermittelt den Ordner der **Eltern**klasse
(über `OWEzpAutoloader::get_class_folder_path()`), also `extension/owsimpleoperator/autoloads/`, bindet die dortige
`eztemplateautoload.php` ein und sucht darin nach einem Eintrag mit Ihrem Klassennamen. Diese Datei kennt nur die
Klassen, die in `owsimpleoperator.ini` aufgeführt sind.

Auf dem üblichen Weg registriert, mit einem Eintrag in `$eZTemplateOperatorArray` in einer anderen Erweiterung und
nichts in `owsimpleoperator.ini` (hier gerendert, indem der Eintrag im Speicher an der Engine registriert wurde):

```tpl
{initials( 'Fit & Healthy' )}
{'Bold Agency'|initials}
```

Ausgabe:

```text

Bold Agency
```

Von der Template-Engine protokolliert:

```text
ERROR : Object problem with operator 'initials' 
ERROR : Object problem with operator 'initials'
```

Die Klasse wird geladen, aber ihr `operatorList()` kommt leer zurück, sodass die Engine für den Namen kein Objekt findet:
Die Aufrufform gibt nichts aus, und die Pipe-Form reicht ihre Eingabe unverändert durch. `operatorList()` schreibt
außerdem `There are no operators registered for the class "C11ExampleTextOperators".` in die Debug-Ausgabe.

## Checkliste

- Die Klasse erweitert `OWSimpleOperator`; jeder Operator ist eine öffentliche Methode, die ihr Ergebnis zurückgibt.
- Optionale Parameter haben den Standardwert `null`.
- Jede Methode hat eine Zeile in `[ClassOperators] PermittedClassOperatorList[]` mit dem richtigen Dateipfad,
  Klassennamen und Methodennamen, und davor steht keine leere Zeile `[]`.
- Der Methodenname ist auf keinem Siteaccess, der ihn verwendet, bereits ein Operatorname.
- Caches geleert und den Operator in beiden Aufrufformen ausprobiert.
- Ausgabe, die Benutzerinhalte enthält, wird im Template maskiert (`|wash`), denn Operatorergebnisse werden es nicht.

## Verwandte Seiten

- [OW Simple Operator: PHP-Funktionen als Template-Operatoren](owsimpleoperator.md)
- [OW Simple Operator: Grenzen und Risiken](grenzen-von-owsimpleoperator.md)
- [Einen Operator Schritt für Schritt anlegen](einen-operator-schritt-fuer-schritt-anlegen.md)
- [Die Schnittstelle der Operatorklasse](referenz-der-operatorklassen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
