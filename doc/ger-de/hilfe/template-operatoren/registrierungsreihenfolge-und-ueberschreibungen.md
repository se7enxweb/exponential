---
title: Registrierungsreihenfolge und Überschreibungen
audience: developer, designer, app-creator
category: development
order: 500
summary: Wie die Template-Engine entscheidet, welche Klasse einen Operatornamen bedient - die Reihenfolge, in der sie die Dateien eztemplateautoload.php liest, "die letzte Registrierung gewinnt", die 21 Namen, die auf alpha zwei Klassen registrieren, und die Regel des verzögerten Ladens, nach der eine Klasse mitten in einer Anfrage einen Namen zurückholen kann - belegt mit auf alpha ausgegebenen Beispielen.
keywords: Operatorregistrierung, Überschreibung, Reihenfolge, letzte gewinnt, verzögertes Laden, Namenskonflikt, operator registration, override, eztemplateautoload.php, AutoloadPathList, ExtensionAutoloadPath, ActiveExtensions, last wins, lazy loading, operator clash, swark, ezurlmultisite, substr
type: help
---
# Registrierungsreihenfolge und Überschreibungen

Operatornamen sind global: ein Name, eine Klasse, für jedes Template eines Siteaccess. Wenn zwei Autoload-Dateien
denselben Namen registrieren, hat die Template-Engine Regeln dafür, welche ihn bedient, und eine dieser Regeln
überrascht. Diese Seite erklärt sie anhand von `lib/eztemplate/classes/eztemplate.php` und zeigt sie auf alpha. Für
die praktische Frage, wie Sie einen Kernel-Operator ersetzen, siehe [Kernel-Operatoren überschreiben](kernel-operatoren-ueberschreiben.md).

## Inhalt

1. [Die Reihenfolge, in der die Dateien gelesen werden](#die-reihenfolge-in-der-die-dateien-gelesen-werden)
2. [Die letzte Registrierung gewinnt](#die-letzte-registrierung-gewinnt)
3. [Die auf alpha doppelt registrierten Namen](#die-auf-alpha-doppelt-registrierten-namen)
4. [Eine geladene Klasse holt sich ihre Namen zurück](#eine-geladene-klasse-holt-sich-ihre-namen-zurück)
5. [Kompilierte Templates](#kompilierte-templates)
6. [Funktionen und Fetch-Funktionen](#funktionen-und-fetch-funktionen)
7. [Regeln](#regeln)

## Die Reihenfolge, in der die Dateien gelesen werden

`eZTemplate::factory()` baut die Liste der zu lesenden Ordner aus zwei Einstellungen der `site.ini` auf und ruft dann
`autoload()` auf, das `eztemplateautoload.php` aus jedem Ordner in dieser Reihenfolge einbindet:

1. `[TemplateSettings] AutoloadPathList[]`: in `settings/site.ini` die Template-Bibliothek
   (`lib/eztemplate/classes/`), der Kernel (`kernel/common/`), `lib/ezpdf/classes/` und
   `kernel/private/eztemplate/`;
2. `[TemplateSettings] ExtensionAutoloadPath[]`: ein Eintrag pro Erweiterung, erweitert zu
   `extension/<name>/autoloads/`. Jede Erweiterung fügt ihre eigene Zeile in ihrer `settings/site.ini.append.php`
   hinzu, die Reihenfolge ist also die, in der das INI-System diese Dateien zusammenführt, und die folgt der
   Reihenfolge von `[ExtensionSettings] ActiveExtensions[]` (und der `ActiveAccessExtensions[]` des Siteaccess).

Auf dem Siteaccess `site` von alpha liest die Engine 23 Dateien: die vier oben genannten, dann
`bcwebsitestatistics, ezwt, ezwebin, ezoe, eztags, ezstarrating, ezurlmultisite, sevenx_themes_media, powercontent,
recaptcha, explayouts_ui, explayouts, enhancedezbinaryfile, swark, owsimpleoperator, cjw_newsletter, xrowmetadata,
ezjscore, expui` (bcwebsitestatistics fügt seinen Ordner selbst zu `AutoloadPathList[]` hinzu, die anderen nutzen
`ExtensionAutoloadPath[]`). Der Siteaccess `admin` liest mehr (die Erweiterung expai fügt dort ihre Operatoren
hinzu), daher kann derselbe Name auf verschiedenen Siteaccesses von verschiedenen Klassen bedient werden.

## Die letzte Registrierung gewinnt

Jede Datei setzt `$eZTemplateOperatorArray`, eine Liste von Einträgen wie

```php
$eZTemplateOperatorArray[] = array( 'script' => 'extension/myext/autoloads/myoperators.php',
                                    'class' => 'myOperators',
                                    'operator_names' => array( 'my_one', 'my_two' ) );
```

und `registerAutoloadOperators()` speichert den Eintrag unter jedem Namen: `$this->Operators[$operatorName] = $operatorDefinition;`.
Eine spätere Datei, die denselben Operator nennt, überschreibt den Eintrag einfach. Nichts warnt davor.

Zu diesem Zeitpunkt ist noch nichts geladen: Die Engine weiß nur, welche Klasse sie für einen Namen laden soll.
Die Klassendatei wird eingebunden und das Objekt angelegt, wenn ein Template zum ersten Mal einen ihrer Namen
verwendet (`loadAndRegisterOperators()`).

## Die auf alpha doppelt registrierten Namen

Dieses kurze Skript listet jeden Namen auf, den mehr als eine Datei registriert, mit den Klassen in Lesereihenfolge
(speichern Sie es an beliebiger Stelle und führen Sie es mit `php bin/php/ezexec.php -s site <file>` aus):

```php
<?php
// Which eztemplateautoload.php registers each operator name, in the order the engine reads them.
$owners = array();
foreach ( eZTemplate::factory()->autoloadPathList() as $path )
{
    $eZTemplateOperatorArray = array();
    if ( !file_exists( $path . 'eztemplateautoload.php' ) )
        continue;
    include $path . 'eztemplateautoload.php';
    foreach ( $eZTemplateOperatorArray as $definition )
    {
        $names = isset( $definition['operator_names'] ) ? $definition['operator_names']
                                                        : call_user_func( $definition['operator_names_function'] );
        foreach ( $names as $name )
            $owners[$name][] = $definition['class'] ?? $definition['function'];
    }
}
foreach ( $owners as $name => $classes )
    if ( count( $classes ) > 1 )
        echo str_pad( $name, 20 ), implode( ' -> ', $classes ), "\n";
```

Seine Ausgabe auf dem Siteaccess `site` von alpha:

```text
nl2br               eZTemplateNl2BrOperator -> eZPHPOperatorInit
image               eZTemplateImageOperator -> sevenxThemesMediaOperators
trim                eZTemplateStringOperator -> eZTemplateStringOperator
ltrim               eZTemplateStringsOperator -> SwarkLTrimOperator
rtrim               eZTemplateStringsOperator -> SwarkRTrimOperator
strip_tags          eZTemplateStringsOperator -> eZPHPOperatorInit
strpos              eZTemplateStringsOperator -> SwarkStrPosOperator
strrpos             eZTemplateStringsOperator -> SwarkStrRPosOperator
substr              eZTemplateStringsOperator -> SwarkSubStrOperator
ezurl               eZURLOperator -> eZURLMultisiteURLOperator
ezroot              eZURLOperator -> eZURLMultisiteURLOperator
ezdesign            eZURLOperator -> eZURLMultisiteURLOperator
ezimage             eZURLOperator -> eZURLMultisiteURLOperator
exturl              eZURLOperator -> eZURLMultisiteURLOperator
ezsys               eZURLOperator -> eZURLMultisiteURLOperator
ezhttp              eZURLOperator -> eZURLMultisiteURLOperator
ezhttp_hasvariable  eZURLOperator -> eZURLMultisiteURLOperator
ezini               eZURLOperator -> eZURLMultisiteURLOperator
ezini_hasvariable   eZURLOperator -> eZURLMultisiteURLOperator
json_encode         SwarkJSONEncodeOperator -> ezjscEncodingTemplateFunctions
str_replace         SwarkStrReplaceOperator -> PHPFunctionOperator
```

Drei Arten von Überschneidung zeigen sich:

- **Gewollte, vollständige Überschreibungen**: Die Erweiterung ezurlmultisite ersetzt alle zehn Namen des
  Kernel-Operators `eZURLOperator` durch eine Unterklasse. Das ist das sichere Muster, erklärt unter
  [Kernel-Operatoren überschreiben](kernel-operatoren-ueberschreiben.md).
- **Zufällige Konflikte zwischen Erweiterungen und der Bibliothek**: swark registriert eigene Versionen von `ltrim`,
  `rtrim`, `strpos`, `strrpos`, `substr`, `json_encode` und `str_replace`, die Template-Bibliothek und ezjscore
  registrieren dieselben Namen, und das Media-Theme registriert ein eigenes `image`. Das sind die gefährlichen, wie
  der nächste Abschnitt zeigt.
- **Durch Einstellungen gesteuerte Namen**: `nl2br` (aus der `template.ini.append.php` von cjw_newsletter) und
  `strip_tags` (aus der von ezmultiupload) stehen außerdem in `template.ini` `[PHP] PHPOperatorList[]`, woraus der
  Kernel Operatoren macht, die `eZTemplatePHPOperator` bedient (angelegt von der Funktion `eZPHPOperatorInit`).
  (`trim` steht zweimal in der eigenen Liste der Bibliothek; das ist dieselbe Klasse.)

## Eine geladene Klasse holt sich ihre Namen zurück

Wenn die Engine eine Operatorklasse lädt, registriert `registerOperatorsInternal()` das Objekt unter **jedem**
Namen, den seine `operatorList()` zurückgibt:

```php
foreach( $operatorObject->operatorList() as $operatorName )
{
    $this->Operators[$operatorName] = $operatorObject;
}
```

Wird also eine Klasse mit vielen Namen geladen, nachdem eine andere Klasse einen dieser Namen übernommen hat, holt
sie sich den Namen zurück, ganz gleich, was die Autoload-Reihenfolge besagte. Welche Klasse einen konfliktbehafteten
Namen bedient, hängt daher davon ab, welche Operatoren die Anfrage vorher verwendet hat. Bei `substr`: Die Version
von swark behandelt eine Länge von `0` als "bis zum Ende", der `eZTemplateStringsOperator` der Bibliothek gibt `0`
an PHP weiter, das eine leere Zeichenkette zurückgibt. Auf alpha mit einer frischen Engine ausgegeben:

```tpl
[{'abcdef'|substr( 2, 0 )}]
```

Ausgabe auf alpha:

```text
[cdef]
```

Derselbe Ausdruck in einem Template, vor und nach einer Verwendung von `strlen` (einem Operator von
`eZTemplateStringsOperator`):

```tpl
[{'abcdef'|substr( 2, 0 )}]
{'abc'|strlen}
[{'abcdef'|substr( 2, 0 )}]
```

Ausgabe auf alpha:

```text
[cdef]
3
[]
```

Das erste `substr` bediente `SwarkSubStrOperator` (die letzte Registrierung); `strlen` lud
`eZTemplateStringsOperator`, der alle seine sechzig Namen registrierte, darunter `substr`, und das zweite `substr`
bediente die Bibliothek. Auf einer echten Seite kann das "Vorher" jedes Template sein, das früher in der Anfrage
lief: das Pagelayout, ein eingebundenes Menü, ein anderer Block. Das Ergebnis eines Templates kann sich ändern, weil
sich ein anderer Teil der Seite geändert hat.

Dasselbe gilt für einen einzelnen überschreibenden Namen aus einer Erweiterung. Eine Erweiterung, die nur `upcase`
registriert (einen der fünfzehn Namen von `eZTemplateStringOperator`), gewinnt in der ersten Zeile und verliert,
nachdem `wash` gelaufen ist, ein Operator derselben Bibliotheksklasse:

```tpl
{'fit & healthy'|upcase}
{'Fit & Healthy'|wash}
{'fit & healthy'|upcase}
```

Ausgabe auf alpha:

```text
[override] FIT & HEALTHY
Fit &amp; Healthy
FIT & HEALTHY
```

(Ausgegeben mit der im Speicher bei der Engine registrierten Überschreibung; die Markierung `[override]` zeigt nur,
welche Klasse geantwortet hat.) Fast jede Seite verwendet `wash`, eine solche Überschreibung würde also in einem
Test-Template funktionieren und auf echten Seiten scheitern.

## Kompilierte Templates

Alpha läuft mit `[TemplateSettings] TemplateCompile=enabled`, der Standardeinstellung. Trifft der Compiler auf einen
Operator, lädt er zur Übersetzungszeit die Klasse des Operators, um nach ihren Hints zu fragen, und mit Hints kann er
die Arbeit des Operators direkt in das erzeugte PHP schreiben (siehe
[Die Schnittstelle der Operatorklasse](referenz-der-operatorklassen.md#operatortemplatehints)). Zwei Folgen:

- Über das Ergebnis kann die Klasse entscheiden, die geladen ist, wenn das Template **kompiliert** wird, nicht die,
  die den Namen zur Laufzeit bedienen würde. Das obige Beispiel mit `upcase`, vom Compiler in einen Arbeitsordner
  ausgegeben, liefert in allen drei Zeilen `FIT & HEALTHY`: Der Compiler hatte `eZTemplateStringOperator` für `wash`
  geladen und dessen Inline-Code für `upcase` verwendet;
- eine Überschreibung, die die Hints der Bibliothek erbt, behält in kompilierten Templates den Inline-Code der
  Bibliothek, siehe [Kernel-Operatoren überschreiben](kernel-operatoren-ueberschreiben.md#den-code-zur-übersetzungszeit-entfernen-den-sie-ändern).

## Funktionen und Fetch-Funktionen

Template-Funktionen (`$eZTemplateFunctionArray`) werden genauso registriert, die letzte Registrierung gewinnt, und
eine geladene Funktionsklasse registriert alle Namen ihrer `functionList()`. Auf alpha ist kein Funktionsname doppelt
registriert.

Fetch-Funktionen folgen einer anderen Regel: `fetch( 'content', ... )` verwendet die erste
`content/function_definition.php`, die in den Modul-Repositorys gefunden wird, und die Repositorys der Erweiterungen
werden vor dem Kernel durchsucht. Siehe [Fetch-Funktionen: wie sie arbeiten](wie-fetch-funktionen-arbeiten.md).

## Regeln

- Geben Sie jedem Operator einen Namen, den kein anderer Operator hat: Ein Präfix Ihrer Erweiterung (`exp_`,
  `myext_`) ist die Gewohnheit.
- Prüfen Sie vor der Wahl eines Namens das [Operatorenverzeichnis](operatorenverzeichnis.md) und führen Sie das obige Skript
  auf jedem Siteaccess aus, der den Namen verwenden wird.
- Überschreiben Sie einen Operator des Kernels oder der Bibliothek nur, indem Sie **alle** Namen seiner Klasse durch
  eine Unterklasse ersetzen, wie es [Kernel-Operatoren überschreiben](kernel-operatoren-ueberschreiben.md) erklärt.
- Verlassen Sie sich nicht auf die Reihenfolge von `ActiveExtensions[]`, um einen Konflikt zwischen zwei
  Erweiterungen zu entscheiden: Sie bestimmt nur die erste Registrierung, und verzögertes Laden kann sie rückgängig
  machen.

## Verwandte Seiten

- [Kernel-Operatoren überschreiben](kernel-operatoren-ueberschreiben.md)
- [Operatoren registrieren](operatoren-registrieren.md)
- [Die Schnittstelle der Operatorklasse](referenz-der-operatorklassen.md)
- [Operatorenverzeichnis](operatorenverzeichnis.md)
- [OW Simple Operator: Grenzen und Risiken](grenzen-von-owsimpleoperator.md#namenskonflikte)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
