---
title: Kernel-Operatoren überschreiben
audience: developer, app-creator
category: development
order: 510
summary: Wie Sie aus einer Erweiterung heraus ändern, was ein Operator des Kernels oder der Template-Bibliothek tut, so wie es die Erweiterung ezurlmultisite für ezurl macht - die Klasse ableiten, der der Name gehört, die Unterklasse für alle ihre Namen registrieren, den Code zur Übersetzungszeit für das Geänderte entfernen - mit jedem Fehler und dem funktionierenden Muster, auf alpha interpretiert und kompiliert ausgegeben.
keywords: Operator überschreiben, Kernel-Operator, Operator erweitern, Unterklasse, Ableitung, Template-Compiler, override operator, kernel operator, extend operator, eZURLOperator, ezurlmultisite, eZTemplateStringOperator, subclass, operatorTemplateHints, element-transformation, ExtensionAutoloadPath, ActiveExtensions
type: help
---
# Kernel-Operatoren überschreiben

Manchmal ist der Operator des Kernels fast richtig: `ezurl` soll andere Websites kennen, `upcase` soll einer
Hausregel folgen. Den Kernel zu bearbeiten kommt nicht in Frage (ein Upgrade ersetzt die Datei), also ersetzt eine
Erweiterung den Operator. Diese Seite zeigt das eine Muster, das sich bewährt, warum die einfacheren Versuche
scheitern und welche Risiken bleiben. Lesen Sie zuerst [Registrierungsreihenfolge und Überschreibungen](registrierungsreihenfolge-und-ueberschreibungen.md),
dort stehen die Regeln dahinter.

## Inhalt

1. [Das Muster](#das-muster)
2. [Ein echter Fall: ezurlmultisite](#ein-echter-fall-ezurlmultisite)
3. [Schritt für Schritt](#schritt-für-schritt)
4. [Warum es scheitert, nur einen Namen zu registrieren](#warum-es-scheitert-nur-einen-namen-zu-registrieren)
5. [Den Code zur Übersetzungszeit entfernen, den Sie ändern](#den-code-zur-übersetzungszeit-entfernen-den-sie-ändern)
6. [Reihenfolge, Siteaccesses und Velocity](#reihenfolge-siteaccesses-und-velocity)
7. [Risiken und wie Sie sie klein halten](#risiken-und-wie-sie-sie-klein-halten)

## Das Muster

1. Ermitteln Sie die Klasse, der der Name gehört, und **alle** Namen, die sie registriert (die `operator_names`
   ihres Eintrags in der `eztemplateautoload.php`, die sie registriert, oder ihre `operatorList()`).
2. Schreiben Sie in Ihrer Erweiterung eine Unterklasse. Überschreiben Sie `modify()` für die Namen, die Sie ändern,
   und rufen Sie für alles andere `parent::modify()` auf.
3. Wenn Sie ändern, was ein Operator tut, entfernen Sie den Code dieses Operators zur Übersetzungszeit in
   `operatorTemplateHints()`.
4. Registrieren Sie die Unterklasse in `autoloads/eztemplateautoload.php` Ihrer Erweiterung für **jeden** Namen
   der Elternklasse.
5. Stellen Sie sicher, dass die Datei Ihrer Erweiterung nach derjenigen gelesen wird, die die Elternklasse
   registriert hat (normalerweise ist das so: Kernel und Template-Bibliothek werden vor jeder Erweiterung gelesen).

Weil Ihre Klasse jeden Namen beantwortet, wird die Elternklasse auf diesem Siteaccess nie geladen und kann sich
daher auch keinen Namen zurückholen.

## Ein echter Fall: ezurlmultisite

Die Erweiterung ezurlmultisite (auf alpha aktiv) sorgt dafür, dass `ezurl` Links auf die richtige Website erzeugt,
wenn Inhalte zu einem anderen beteiligten Siteaccess gehören. Ihre `autoloads/eztemplateautoload.php`:

```php
$eZTemplateOperatorArray[] = array(
    'script' => 'extension/ezurlmultisite/classes/ezurlmultisiteurloperator.php',
    'class' => 'eZURLMultisiteURLOperator',
    'operator_names' => array( 'ezurl', 'ezroot', 'ezdesign', 'ezimage', 'exturl',
                               'ezsys', 'ezhttp', 'ezhttp_hasvariable', 'ezini', 'ezini_hasvariable' )
);
```

Das sind genau die zehn Namen, die `kernel/common/eztemplateautoload.php` für `eZURLOperator` registriert. Die
Klasse in `extension/ezurlmultisite/classes/ezurlmultisiteurloperator.php` folgt dem Muster (gekürzt):

```php
class eZURLMultisiteURLOperator extends eZURLOperator
{
    public function operatorTemplateHints()
    {
        $hints = parent::operatorTemplateHints();
        // ezurl is decided at run time: which siteaccess a link belongs to
        // depends on the siteaccess the compiled template is shown in.
        if ( isset( $hints[$this->URLName] ) )
        {
            $hints[$this->URLName]['element-transformation'] = false;
            unset( $hints[$this->URLName]['element-transformation-func'] );
        }
        return $hints;
    }

    public function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, &$operatorValue, $namedParameters, $placement )
    {
        if ( $operatorName === $this->URLName )
        {
            // ... multisite handling, then return
        }
        parent::modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace, $operatorValue, $namedParameters, $placement );
    }
}
```

Ihr Doc-Kommentar nennt den Grund, alle zehn Namen zu registrieren: "so the template engine never creates the kernel
object next to this one (it registers an object for all the names of its operatorList() the first time one of
them runs). No kernel override and no EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE are needed." (Damit die Template-Engine das
Kernel-Objekt nie neben diesem anlegt; sie registriert ein Objekt für alle Namen seiner `operatorList()`, sobald einer
davon zum ersten Mal läuft. Weder eine Kernel-Überschreibung noch `EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE` sind nötig.)
Außerdem fügt sie `ezurl` einen Parameter hinzu, indem sie `namedParameterList()` ausgehend von der Liste der
Elternklasse erweitert.

## Schritt für Schritt

Die folgenden Beispiele überschreiben `upcase`, einen der fünfzehn Namen von `eZTemplateStringOperator`
(`lib/eztemplate/classes/eztemplatestringoperator.php`: `upcase`, `downcase`, `count_words`, `count_chars`, `trim`,
`break`, `wrap`, `shorten`, `pad`, `upfirst`, `upword`, `simplify`, `wash`, `chr`, `ord`). Die Überschreibung fügt
eine sichtbare Markierung `[override]` hinzu, damit die Ausgabe zeigt, welche Klasse geantwortet hat.

Die Unterklasse, `extension/myextension/autoloads/c11stringoperatoroverride.php`:

```php
class C11StringOperatorOverride extends eZTemplateStringOperator
{
    function operatorTemplateHints()
    {
        $hints = parent::operatorTemplateHints();
        unset( $hints['upcase'] ); // no inline PHP for upcase: compiled templates call modify() below
        return $hints;
    }

    function modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace,
                     &$operatorValue, $namedParameters, $placement = false )
    {
        if ( $operatorName === 'upcase' )
        {
            $operatorValue = '[override] ' . mb_strtoupper( (string)$operatorValue );
            return;
        }
        parent::modify( $tpl, $operatorName, $operatorParameters, $rootNamespace, $currentNamespace,
                        $operatorValue, $namedParameters, $placement );
    }
}
```

Die Registrierung, `extension/myextension/autoloads/eztemplateautoload.php`:

```php
<?php
$eZTemplateOperatorArray = array();
$eZTemplateOperatorArray[] = array(
    'script' => 'extension/myextension/autoloads/c11stringoperatoroverride.php',
    'class' => 'C11StringOperatorOverride',
    'operator_names' => array( 'upcase', 'downcase', 'count_words', 'count_chars', 'trim', 'break', 'wrap', 'shorten',
                               'pad', 'upfirst', 'upword', 'simplify', 'wash', 'chr', 'ord' ) );
```

und `extension/myextension/settings/site.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[TemplateSettings]
ExtensionAutoloadPath[]=myextension

*/ ?>
```

Auf alpha nicht als installierte Erweiterung ausgeführt: Die folgenden Ausgaben hat ein Skript erzeugt, das
denselben Eintrag nur im Speicher und nur für diesen Prozess bei der Template-Engine registriert hat, mit der Klasse
in einem Arbeitsordner (die Arbeitskopie hat einen zusätzlichen statischen Schalter, der die Hints behält und im
Abschnitt über den Code zur Übersetzungszeit verwendet wird). Das Template verwendet `upcase`, dann `wash`, dann
noch einmal `upcase`:

```tpl
{'fit & healthy'|upcase}
{'Fit & Healthy'|wash}
{'fit & healthy'|upcase}
```

Ausgabe auf alpha:

```text
[override] FIT & HEALTHY
Fit &amp; Healthy
[override] FIT & HEALTHY
```

Ausgabe auf alpha mit dem Template-Compiler (in einen Arbeitsordner kompiliert):

```text
[override] FIT & HEALTHY
Fit &amp; Healthy
[override] FIT & HEALTHY
```

Beide Aufrufe von `upcase` beantwortet die Überschreibung, im interpretierten wie im kompilierten Template, und
`wash` funktioniert weiterhin: Die Unterklasse reicht es an `modify()` der Elternklasse weiter.

## Warum es scheitert, nur einen Namen zu registrieren

Die Unterklasse (oder irgendeine Klasse) nur für `upcase` zu registrieren, scheint zu genügen und funktioniert in
einem schnellen Test:

```tpl
{'fit & healthy'|upcase}
```

Ausgabe auf alpha:

```text
[override] FIT & HEALTHY
```

Es hört auf zu funktionieren, sobald die Anfrage irgendeinen anderen Operator von `eZTemplateStringOperator`
verwendet. Das Laden dieser Klasse registriert sie wieder für alle fünfzehn Namen, `upcase` eingeschlossen:

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

Ausgabe auf alpha mit dem Template-Compiler (in einen Arbeitsordner kompiliert):

```text
FIT & HEALTHY
Fit &amp; Healthy
FIT & HEALTHY
```

Im kompilierten Template wird nicht einmal die erste Zeile überschrieben: Der Compiler hat beim Kompilieren die
Bibliotheksklasse für `wash` geladen und für beide Aufrufe von `upcase` den Inline-Code der Bibliothek geschrieben.

## Den Code zur Übersetzungszeit entfernen, den Sie ändern

Viele Operatoren der Bibliothek deklarieren `element-transformation`-Hints: Wenn ein Template kompiliert wird,
schreibt die Operatorklasse PHP für den Aufruf direkt in das kompilierte Template (für `upcase` einen Aufruf von
`mb_strtoupper()`), und `modify()` wird dafür nie wieder aufgerufen. Eine Unterklasse erbt diese Hints. Ändert sie
`modify()`, behält aber die Hints, wirkt die Überschreibung in interpretierten Templates und wird in kompilierten
stillschweigend übergangen. Dieselbe Unterklasse mit den beibehaltenen Hints der Bibliothek, für alle fünfzehn
Namen registriert:

```tpl
{'fit & healthy'|upcase}
{'Fit & Healthy'|wash}
{'fit & healthy'|upcase}
```

Ausgabe auf alpha:

```text
[override] FIT & HEALTHY
Fit &amp; Healthy
[override] FIT & HEALTHY
```

Ausgabe auf alpha mit dem Template-Compiler (in einen Arbeitsordner kompiliert):

```text
FIT & HEALTHY
Fit &amp; Healthy
FIT & HEALTHY
```

Alpha kompiliert wie jede Standardinstallation Templates (`[TemplateSettings] TemplateCompile=enabled` in
`settings/site.ini`), daher bekommen Besucher das kompilierte Ergebnis. Entfernen Sie den Hint-Eintrag jedes
Operators, den Sie ändern (`unset( $hints['upcase'] )`), oder setzen Sie `element-transformation` auf `false`, wie
es ezurlmultisite tut. Behalten Sie die Hints der Namen, die Sie nur an die Elternklasse weiterreichen.

## Reihenfolge, Siteaccesses und Velocity

- Erweiterungen werden nach dem Kernel und der Template-Bibliothek gelesen, daher ist die Registrierung eines
  Kernel-Operatornamens durch eine Erweiterung immer die spätere. Registrieren zwei Erweiterungen dieselben Namen,
  gewinnt diejenige, deren `site.ini.append.php` später zusammengeführt wird: Das folgt
  `[ExtensionSettings] ActiveExtensions[]` und den `ActiveAccessExtensions[]` des Siteaccess. Vermeiden Sie, darauf
  angewiesen zu sein.
- Eine Überschreibung, die eine nur auf einem Siteaccess aktive Erweiterung (eine Access-Erweiterung) registriert,
  gilt nur dort. Prüfen Sie jeden Siteaccess, der die Templates ausgibt, auch die Administration, wenn sie eine
  Vorschau von Inhalten zeigt.
- Leeren Sie nach dem Hinzufügen der Registrierung die INI- und Template-Caches (kompilierte Templates behalten den
  alten Inline-Code, bis sie neu kompiliert werden): `php bin/php/ezcache.php --clear-tag=ini,template --allow-root-user`.
  Unter Velocity behalten die Worker die Operatorregistrierungen ihres Warm-ups: Führen Sie auf alpha
  `./console exp:velocity deploy --allow-root-user` aus, das diese Caches leert und Velocity neu startet.

## Risiken und wie Sie sie klein halten

- **Upgrades**: Ihre Unterklasse hängt von Interna der Elternklasse ab (`$this->URLName`, dem Hint-Array, der
  Signatur von `modify()`). Lesen Sie nach einem Kernel-Upgrade die Änderungen der Elternklasse und führen Sie Ihre
  Templates interpretiert und kompiliert aus.
- **Neue Namen in der Elternklasse**: Fügt ein Kernel-Release der Elternklasse einen Operator hinzu, führt Ihre
  Registrierung ihn nicht auf; die Elternklasse wird für diesen Namen geladen und holt sich alle Namen zurück.
  Vergleichen Sie Ihre `operator_names` in einem Test mit der `operatorList()` der Elternklasse (`array_diff()` der
  beiden muss leer sein).
- **Zwei Erweiterungen überschreiben dieselbe Klasse**: Nur eine Registrierung kann gewinnen. Führen Sie beide zu
  einer Unterklasse zusammen.
- **Versteckte Änderungen**: Eine Überschreibung ändert, was jedes Template auf dem Siteaccess bekommt, auch die
  Templates anderer Erweiterungen. Bevorzugen Sie einen neuen Operator mit eigenem Namen
  ([Einen Operator Schritt für Schritt anlegen](einen-operator-schritt-fuer-schritt-anlegen.md)), wenn nur Ihre eigenen
  Templates das neue Verhalten brauchen.
- **Keine Überschreibung von Kernel-Klassen**: Sie brauchen dafür weder `EZP_AUTOLOAD_ALLOW_KERNEL_OVERRIDE` noch
  eine Kopie der Kernel-Klasse; die Operatorregistrierung ist der unterstützte Einstiegspunkt.

## Verwandte Seiten

- [Registrierungsreihenfolge und Überschreibungen](registrierungsreihenfolge-und-ueberschreibungen.md)
- [Die Schnittstelle der Operatorklasse](referenz-der-operatorklassen.md)
- [Operatoren registrieren](operatoren-registrieren.md)
- [URL-Operatoren](url-operatoren.md): `ezurl` und seine Geschwister, wie ezurlmultisite sie bedient
- [Der Template-Compiler](../template-sprache/der-template-compiler.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
