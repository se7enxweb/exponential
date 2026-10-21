---
title: Konstruktoren alten Stils und statische Aufrufe
audience: developer
category: extension-development
order: 70
summary: PHP 8 führt eine Methode mit dem Namen ihrer Klasse nicht mehr als Konstruktor aus und bricht bei statisch aufgerufenen Instanzmethoden ab. Wie Sie beides in einer alten Erweiterung finden und beheben, ohne Code zu brechen, der die alten Namen aufruft.
keywords: Konstruktor, PHP-4-Konstruktor, Konstruktor alten Stils, statischer Aufruf, nicht statische Methode, Template-Operator, constructor, __construct, PHP 4 constructor, old-style constructor, static call, non-static method, Error, template operator, ezfind, upgrade
type: help
---
# Konstruktoren alten Stils und statische Aufrufe

Teil 4, Kapitel 7. PHP 4 hatte kein `__construct()`: Der Konstruktor war die Methode mit dem Namen der Klasse. PHP 5
und 7 haben das noch akzeptiert, PHP 8 nicht mehr. Viele in den 2000er-Jahren geschriebene Erweiterungen verwenden
die alte Form, und fast jeder ihrer Template-Operatoren tut es, weil die Beispiele des Kernels selbst es taten. Dieses
Kapitel zeigt, wie der Fehler aussieht, wie Sie jeden Fall finden und welche Lösung dieses Projekt verwendet. Die
zweite Hälfte behandelt seinen Verwandten, den statischen Aufruf einer Instanzmethode.

## Warum es gefährlich ist

Ein fehlender Konstruktor hält beim Erzeugen des Objekts nichts an. PHP 8 ruft die Methode einfach nicht auf, also
existiert das Objekt, aber seine Eigenschaften wurden nie gesetzt. Der Fehler tritt später an einer anderen Stelle
auf und zeigt auf die falsche Stelle.

Ein Template-Operator ist der klassische Fall. Das ist die Operatorklasse der Erweiterung str_replace
(se7enxweb/str_replace), so wie sie im Oktober 2026 aussieht:

```php
class MyStrReplaceOperator
{
    /*!
     Constructor
    */
    function MyStrReplaceOperator()
    {
        $this->Operators = array( 'ezstr_replace');
    }

    function operatorList()
    {
        return $this->Operators;
    }
    // ...
}
```

Unter PHP 8 lässt `new MyStrReplaceOperator()` die Eigenschaft `$this->Operators` ungesetzt, `operatorList()` liefert
`null`, und die Template-Engine meldet beim ersten Template, das den Operator verwendet, „Object problem with
operator“. Nichts in dieser Meldung erwähnt einen Konstruktor.

In se7enxweb/ezfind hat Commit 7ff9824 fünf Klassen mit demselben Problem behoben. Seine Nachricht listet die Symptome
auf, und sie zeigen, wie weit entfernt von der Ursache sie auftreten können:

| Klasse | Was unter PHP 8 schiefging |
|---|---|
| `eZFindResultNode` | das Lesen eines Knotens aus einem Suchergebnis endete mit einem Fehler |
| `eZFindResultObject` | der Konstruktor der Elternklasse lief mit den falschen Daten |
| `ezfSearchResultInfo` | Facetten, Fehler und der Response-Header einer Suche waren leer |
| `ezfUpdateSearchIndexSolr` | die Indexierung über die Kommandozeile brach sofort ab |
| `ezfModuleFunctionCollection` | ein leerer Konstruktor, zu einem echten gemacht |

## Jeden Fall finden

Eine Methode ist ein Konstruktor alten Stils, wenn ihr Name, ohne Beachtung der Groß- und Kleinschreibung, dem Namen
ihrer Klasse entspricht, in einer Klasse ohne `__construct()`. Diese Schleife gibt jede Klasse aus, die eine Methode
mit ihrem eigenen Namen hat:

```bash
cd extension/myoldext
grep -rhoE '^\s*(abstract\s+|final\s+)?class\s+[A-Za-z0-9_]+' --include='*.php' . | awk '{print $NF}' | sort -u | while read c; do
  grep -rliE "function\s+&?\s*$c\s*\(" --include='*.php' . | sed "s|^|$c: |"
done
```

Prüfen Sie jede Datei, die sie ausgibt. PHPCompatibility für PHP_CodeSniffer meldet dasselbe, falls Sie es bereits
verwenden ([Tests und CI für eine aktualisierte Erweiterung](tests-und-ci-fuer-eine-aktualisierte-erweiterung.md)).

## Die Lösung

Benennen Sie die Methode in `__construct()` um und behalten Sie den alten Namen als Methode, die den Konstruktor
aufruft, für Code, der den alten Namen direkt aufruft. Genau das hat se7enxweb/ezfind in Commit 7ff9824 gemacht:

```php
// before
function ezfSearchResultInfo( array $resultArray )
{
    $this->ResultArray = $resultArray;
}

// after
function __construct( array $resultArray )
{
    $this->ResultArray = $resultArray;
}

/**
 * The constructor under its old name, which PHP 8 no longer calls on "new"; kept for code that calls it.
 *
 * @deprecated Use the constructor
 */
function ezfSearchResultInfo( array $resultArray )
{
    $this->__construct( $resultArray );
}
```

Warum den alten Namen behalten? Kindklassen und andere Erweiterungen rufen möglicherweise
`parent::ezfSearchResultInfo( ... )` oder `$this->ezfSearchResultInfo( ... )` auf. Ohne die Hülle werden daraus
fatale Fehler.

Für den Template-Operator oben ist dies die ganze Lösung:

```php
<?php
class MyStrReplaceOperator
{
    public $Operators;

    function __construct()
    {
        $this->Operators = array( 'ezstr_replace' );
    }

    /**
     * @deprecated Use the constructor
     */
    function MyStrReplaceOperator()
    {
        $this->__construct();
    }

    function operatorList()
    {
        return $this->Operators;
    }
}
```

Beachten Sie die deklarierte Eigenschaft `public $Operators;`: Ohne sie meldet PHP 8.2 eine dynamische Eigenschaft
([Dynamische Eigenschaften](dynamische-eigenschaften.md)).

### Klassen mit einer Elternklasse

Sehen Sie sich an, was der alte Konstruktor mit seiner Elternklasse gemacht hat. Es gibt drei Fälle:

| Der alte Konstruktor | Was der neue tut |
|---|---|
| rief `$this->ParentClass( ... )` oder `parent::ParentClass( ... )` auf | ruft `parent::__construct( ... )` mit denselben Argumenten auf |
| rief die Elternklasse überhaupt nicht auf | ruft sie ebenfalls nicht auf (in ezfind lässt `eZFindResultObject` das bewusst so) |
| existierte nicht; nur die Elternklasse hatte einen | nichts zu tun, das `__construct()` der Elternklasse läuft |

Wenn die Elternklasse eine Kernel-Klasse ist, ist ihr Konstruktor in Exponential 6 bereits `__construct()`.
Unterklassen von `eZPersistentObject` rufen zum Beispiel `parent::__construct( $row )` auf.

## Statische Aufrufe von Instanzmethoden

PHP 5 ließ Sie eine Instanzmethode als `ClassName::method()` aufrufen, mit einem Hinweis, solange sie `$this` nicht
anfasste. PHP 8 wirft einen `Error`:

```text
Uncaught Error: Non-static method eZSelectionType::classAttributeContent() cannot be called statically
```

Aus se7enxweb/sevenx_themes_media, Commit f7d93c0:

```php
// before
$classContent = eZSelectionType::classAttributeContent( $classAttr );

// after
$classContent = $classAttr->attribute( 'content' );
```

Es gibt drei Wege, einen solchen Aufruf zu beheben, in dieser Reihenfolge:

1. **Verwenden Sie die übergeordnete API.** Wie oben: Das Klassenattribut kennt seinen Datentyp und liefert seinen
   Inhalt.
2. **Holen Sie sich eine Instanz.** Viele Kernel-Klassen haben eine: `eZINI::instance()`, `eZDB::instance()`,
   `eZHTTPTool::instance()`, `eZLocale::instance()`, `eZTemplate::factory()`, oder einen Fetch:
   `eZContentObject::fetch( $id )`, `eZContentObjectTreeNode::fetch( $nodeID )`.
3. **Machen Sie Ihre eigene Methode statisch**, aber nur Ihre eigene und nur, wenn sie `$this` nicht verwendet.

Um Kandidaten zu finden, listen Sie die statisch aussehenden Aufrufe von Kernel-Klassen auf und prüfen Sie die
Deklaration jeder Methode:

```bash
grep -rnoE '\beZ[A-Za-z]+::[a-zA-Z_]+\s*\(' --include='*.php' extension/myoldext/ | sort -u
grep -n "function classAttributeContent" kernel/classes/datatypes/ezselection/ezselectiontype.php
```

Eine Deklaration ohne `static` bedeutet, dass der Aufruf eine Instanz braucht.

### Der umgekehrte Fall: $this in einer statischen Methode

Alter Code deklariert eine Methode manchmal als `static` und verwendet darin trotzdem `$this`, was funktionierte,
wenn die Methode zufällig auf einem Objekt aufgerufen wurde. PHP 8 bricht mit „Using $this when not in object
context“ ab. Entfernen Sie `static` oder ersetzen Sie `$this->` durch `self::` oder `static::`, wenn die Methode
wirklich statisch ist.

## Checkliste

- [ ] Jede Klasse mit einer Methode ihres eigenen Namens hat ein `__construct()`.
- [ ] Der alte Name bleibt als veraltete Hülle erhalten.
- [ ] Jeder neue Konstruktor ruft die Elternklasse genau dann auf, wenn der alte es tat.
- [ ] Jede in einem Konstruktor gesetzte Eigenschaft ist deklariert.
- [ ] Kein `ClassName::method()`-Aufruf einer Methode, die ohne `static` deklariert ist.

## Verwandte Seiten

- Zurück: [Entfernte PHP-Funktionen und ihr Ersatz](entfernte-php-funktionen.md)
- Weiter: [Nullable-Parameter und Typdeklarationen](nullable-parameter-und-typen.md)
- [Template-Operatoren](../teil-3-erweiterungsentwickler/template-operatoren.md)
- [Konstruktoren und Destruktoren](https://www.php.net/manual/en/language.oop5.decon.php) im PHP-Handbuch
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
