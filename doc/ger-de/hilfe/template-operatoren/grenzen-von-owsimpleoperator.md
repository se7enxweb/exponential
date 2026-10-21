---
title: "OW Simple Operator: Grenzen und Risiken"
audience: developer, app-creator
category: development
order: 920
summary: Was OW Simple Operator nicht tut und wo es Probleme macht - die Pipe-Form, die ein Argument verliert, null-Argumente, die weggelassen werden, keine Parameterprüfung, keine Optimierung beim Kompilieren, Namenskonflikte, Caching und die Sicherheitsregel für erlaubte PHP-Funktionen - mit den Fehlern, auf alpha gerendert.
keywords: Grenzen, Risiken, Pipe-Form, Namenskonflikt, Sicherheit, Parameterprüfung, owsimpleoperator, limitations, risks, ArgumentCountError, pipe form, null, PermittedFunctionList, security, cache-block, operatorTemplateHints, name clash
type: help
---
# OW Simple Operator: Grenzen und Risiken

OW Simple Operator spart Code, indem es jedem Operator dieselbe generische Definition gibt. Aus dieser generischen
Definition kommen auch seine Grenzen. Alles Folgende ist aus `extension/owsimpleoperator/autoloads/owsimpleoperator.php`
und `autoloads/eztemplateautoload.php` gelesen und, wo ein Beispiel gezeigt wird, auf alpha gerendert.

## Inhalt

1. [Die Pipe-Form verliert ein Argument](#die-pipe-form-verliert-ein-argument)
2. [Null-Argumente werden weggelassen](#null-argumente-werden-weggelassen)
3. [Keine Parameterprüfung](#keine-parameterprüfung)
4. [Kein Code zur Kompilierzeit](#kein-code-zur-kompilierzeit)
5. [Namenskonflikte](#namenskonflikte)
6. [Gecachte Ausgabe](#gecachte-ausgabe)
7. [Sicherheit erlaubter PHP-Funktionen](#sicherheit-erlaubter-php-funktionen)
8. [Wann Sie stattdessen eine vollständige Operatorklasse schreiben](#wann-sie-stattdessen-eine-vollständige-operatorklasse-schreiben)

## Die Pipe-Form verliert ein Argument

`OWSimpleOperator::modify()` behandelt einen gepipten Wert so:

```php
// If there are an operator value, we switch it with our parameter
if ( $operator_value !== null )
{
    array_pop( $method_arguments );
    array_unshift( $method_arguments, $operator_value );
}
```

Bei einer **Klassenmethode** hat die Argumentliste immer zehn Einträge (einen je deklariertem Parameter), sodass der
entfernte Eintrag der zehnte, unbenutzte ist, und `{1|example_sum( 2 )}` funktioniert wie `example_sum( 1, 2 )`
([Beispiel](eigene-operatoren-mit-owsimpleoperator.md#die-operatoren-verwenden)).

Bei einer **PHP-Funktion** enthält die Liste nur die Argumente, die Sie angegeben haben (siehe den nächsten Abschnitt),
sodass der entfernte Eintrag Ihr letztes Argument ist. `{'This is a great example!'|str_replace( 'great', 'very good' )}`
ruft `str_replace( 'This is a great example!', 'great' )` auf, und PHP wirft einen `ArgumentCountError`; siehe
[das Beispiel auf der ersten Seite](owsimpleoperator.md#calling-a-php-function-operator). Bei einer Funktion mit
optionalen Parametern gibt es keinen Fehler, sondern ein falsches Ergebnis, was schlimmer ist. Regel: Rufen Sie
PHP-Funktionsoperatoren in der Aufrufform mit allen Argumenten in den Klammern auf, und pipen Sie nur in Funktionen mit
genau einem Argument (`{$text|str_rot13}`).

Ein gepipter Wert `null` zählt als überhaupt kein gepipter Wert (die Prüfung ist `$operator_value !== null`), daher ruft
`{$missing|str_rot13}` mit einem nicht gesetzten Wert `str_rot13()` ohne Argumente auf.

## Null-Argumente werden weggelassen

Bei PHP-Funktionen kopiert `modify()` nur die Argumente, die gesetzt sind:

```php
foreach( $named_parameters as $index => $parameter )
{
   if( isset( $parameter ) )
   {
       $method_arguments[$index] = $parameter;
   }
}
```

und ruft dann `call_user_func_array()` mit dieser Liste auf. Ein Argument mit dem Wert `null` wird nicht übergeben, und
jedes folgende Argument rückt eine Stelle nach vorn in den falschen Parameter der PHP-Funktion. Stellen Sie sicher, dass
die übergebenen Werte gesetzt sind (`first_set()` liefert einen Ausweichwert, siehe
[Steueroperatoren](steueroperatoren.md)).

Bei Klassenmethoden geschieht das Gegenteil: Alle zehn Werte werden übergeben, sodass ein weggelassener Parameter als
`null` ankommt und nie Ihren PHP-Standardwert erhält ([Optionale Argumente](eigene-operatoren-mit-owsimpleoperator.md#optionale-argumente)).

## Keine Parameterprüfung

`namedParameterList()` deklariert für jeden Operator zehn Parameter vom Typ `mixed`, keiner erforderlich, Standard `null`.
Die Template-Engine meldet für diese Operatoren daher nie ein fehlendes oder falsches Argument; PHP tut es, zur Laufzeit,
und die Template-Engine fängt die Fehler von PHP nicht ab:

```tpl
{str_rot13()}
```

Ausgabe auf alpha:

```text
(no output)
```

Von der Template-Engine protokolliert:

```text
EXCEPTION ArgumentCountError: str_rot13() expects exactly 1 argument, 0 given
```

Operatoren der Template-Bibliothek prüfen ihre Parameter selbst und protokollieren stattdessen eine Template-Warnung, zum
Beispiel `Missing required parameter: start` von `substr` in `eZTemplateStringsOperator`
([PHP-Zeichenkettenfunktionen](php-zeichenkettenfunktionen-umwandeln.md)).

Steht ein Name in `owsimpleoperator.ini`, ist aber weder eine Methode der Klasse noch eine PHP-Funktion, schreibt
`modify()` `The method "<name>" doesn't exists.` in die Debug-Ausgabe und gibt `null` zurück.

## Kein Code zur Kompilierzeit

`OWSimpleOperator` hat kein `operatorTemplateHints()`. Der Template-Compiler (`eZTemplateCompiler::operatorHint()`) hat
dann keine Hinweise für diese Operatoren und kann sie nicht in Inline-PHP umwandeln, daher rufen kompilierte Templates das
Operatorobjekt zur Laufzeit auf, jedes Mal. Die Operatoren der Template-Bibliothek deklarieren Hinweise, und viele von
ihnen werden in einfaches PHP kompiliert (siehe [Die Schnittstelle der Operatorklasse](referenz-der-operatorklassen.md) und
[Der Template-Compiler](../template-sprache/der-template-compiler.md)). Für einen Operator, der wenige Male pro Seite
aufgerufen wird, spielt das keine Rolle; in einer Schleife über Hunderte von Einträgen kann es das.

## Namenskonflikte

Jede PHP-Funktion, die Sie erlauben, und jede Methode, die Sie registrieren, wird zu einem globalen Operatornamen. Auf
alpha wird `str_replace` sowohl von der Erweiterung swark als auch von owsimpleoperator registriert; die
`eztemplateautoload.php` von owsimpleoperator wird später gelesen und gewinnt. Schlimmer ist ein erlaubter Name, der einem
Operator einer Klasse mit mehreren Operatoren entspricht (zum Beispiel `strlen` von `eZTemplateStringsOperator`): Die
Klasse der Template-Bibliothek registriert beim Laden alle ihre Namen erneut und holt sich den Namen mitten in einer
Anfrage zurück ([Registrierungsreihenfolge und Überschreibungen](registrierungsreihenfolge-und-ueberschreibungen.md#eine-geladene-klasse-holt-sich-ihre-namen-zurück)).
Verwenden Sie Namen, die kein anderer Operator hat.

## Gecachte Ausgabe

Operatorergebnisse landen in der Ausgabe wie jeder andere Text und werden daher im View-Cache und in Cache-Blöcken
gespeichert. `{time()}` in einer gecachten vollständigen Ansicht zeigt die Zeit, zu der der Cache geschrieben wurde, nicht
die Zeit der Anfrage. Das README der Erweiterung sagt dasselbe: Verwenden Sie `cache-block` mit den richtigen Schlüsseln
oder dem richtigen Ablauf, oder halten Sie solche Aufrufe aus gecachten Templates heraus
([Template-Caches](../template-sprache/template-caches.md)).

## Sicherheit erlaubter PHP-Funktionen

Ein Template kann eine erlaubte Funktion mit jedem Argument aufrufen. Templates werden von Designern geschrieben, über den
Template-Editor der Administration bearbeitet und von Erweiterungen mitgeliefert; behandeln Sie `PermittedFunctionList[]`
daher wie Code:

- führen Sie nur Funktionen ohne Seiteneffekte auf, die nichts außerhalb ihrer Argumente lesen (die Regel in der
  mitgelieferten Einstellungsdatei);
- nie Funktionen für Dateien, Prozesse, Netzwerk, Konfiguration oder Code-Auswertung (`file_get_contents`, `exec`,
  `getenv`, `ini_get`, `call_user_func`, ... der Test der Erweiterung listet die auf, die sie ablehnt);
- maskieren Sie, was ein Operator zurückgibt, wenn es Benutzerinhalte enthält (`|wash`), denn nichts maskiert es für Sie.

## Wann Sie stattdessen eine vollständige Operatorklasse schreiben

Schreiben Sie eine gewöhnliche Operatorklasse ([Einen Operator Schritt für Schritt anlegen](einen-operator-schritt-fuer-schritt-anlegen.md)),
wenn Sie eines davon brauchen:

- erforderliche Parameter mit klaren Template-Warnungen oder typisierte Parameter;
- einen Operator, der auf seiner gepipten Eingabe arbeitet und außerdem mehrere Parameter nimmt;
- Optimierung zur Kompilierzeit (`operatorTemplateHints()`);
- Registrierung in Ihrer eigenen Erweiterung, ohne `owsimpleoperator.ini` anzufassen;
- keine Abhängigkeit davon, dass eine dritte Erweiterung aktiv ist.

OW Simple Operator bleibt eine gute Wahl für kleine Helfer mit einem oder zwei Argumenten, die Sie in der Aufrufform
aufrufen.

## Verwandte Seiten

- [OW Simple Operator: PHP-Funktionen als Template-Operatoren](owsimpleoperator.md)
- [Eigene Operatoren mit OW Simple Operator](eigene-operatoren-mit-owsimpleoperator.md)
- [Registrierungsreihenfolge und Überschreibungen](registrierungsreihenfolge-und-ueberschreibungen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
