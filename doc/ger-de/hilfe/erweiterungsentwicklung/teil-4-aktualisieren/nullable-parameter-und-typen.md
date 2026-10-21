---
title: Nullable-Parameter und Typdeklarationen
audience: developer
category: extension-development
order: 80
summary: Beheben Sie die häufigste Deprecation von PHP 8.4, implizit nullable Parameter, und fügen Sie altem Code sicher Typdeklarationen hinzu, mit Beispielen aus expsite_api, expquery_translator und ezfind.
keywords: Nullable-Parameter, implizit nullable, Typdeklarationen, optionaler vor erforderlichem Parameter, Standardwert null, Union-Typen, nullable, ?Type, implicitly nullable, PHP 8.4, type declarations, optional before required, default null, mixed, union types, upgrade
type: help
---
# Nullable-Parameter und Typdeklarationen

Teil 4, Kapitel 8. Dieses Kapitel behandelt die Deprecation, die Sie in altem Code unter PHP 8.4 und neuer am
häufigsten sehen werden, den implizit nullable Parameter, und seinen nahen Verwandten, einen optionalen Parameter vor
einem erforderlichen. Außerdem gibt es Regeln, wie Sie altem Code Typdeklarationen hinzufügen, ohne Aufrufer zu
brechen.

## Die Deprecation

```text
Deprecated: myOldExtHelper::render(): Implicitly marking parameter $node as nullable is deprecated,
the explicit nullable type must be used instead
```

Sie stammt von einem Parameter, der eine Klasse oder einen Typ und `null` als Standardwert hat:

```php
function render( eZContentObjectTreeNode $node = null )
```

PHP hat hier wegen des Standardwerts schon immer `null` erlaubt. PHP 8.4 möchte, dass Sie das im Typ ausdrücken.

## Die Lösung

Fügen Sie ein Fragezeichen hinzu:

```php
function render( ?eZContentObjectTreeNode $node = null )
```

Das ist alles. `?Type` funktioniert seit PHP 7.1, die Lösung läuft also auf jedem PHP, das Exponential 6 unterstützt,
und Aufrufer sind nicht betroffen: Sie konnten vorher `null` übergeben und können es weiterhin.

Aus se7enxweb/expsite_api, Commit 0f708f4:

```php
// before
public function __construct( eZContentObject $object = null )

// after
public function __construct( ?eZContentObject $object = null )
```

Die Commit-Nachricht nennt den Grund, warum es wichtig war: Die alte Form wurde „bei jeder Anfrage gemeldet, die
eines erzeugt (die Suchseite tut es)“. se7enxweb/expquery_translator hat in Commit 8ddf68d dieselbe Änderung an 35
Parametern in 27 Dateien vorgenommen und sie mit `php -l` unter PHP 8.0 und 8.5 sowie mit einer Abfrage durch den
Parser auf beiden Versionen überprüft.

## Jeden Fall finden

```bash
grep -rnE '[(,]\s*(array|callable|string|int|float|bool|iterable|object|self|[A-Z][A-Za-z0-9_\\]+)\s+&?\$[A-Za-z0-9_]+\s*=\s*null' --include='*.php' extension/myoldext/
```

Jeder Treffer ohne `?` vor dem Typ und ohne `|null` in einem Union-Typ muss behoben werden. Die eigene Syntaxprüfung
von PHP 8.4 meldet sie ebenfalls:

```bash
find extension/myoldext -name '*.php' -print0 | xargs -0 -n1 php8.4 -d error_reporting=-1 -l 2>&1 | grep -i nullable
```

(Verwenden Sie statt `php8.4` den Pfad eines PHP-8.4- oder PHP-8.5-Binarys auf Ihrem System.)

## Interfaces und Elternklassen

Wenn der Parameter zu einer Methode gehört, die ein Interface implementiert oder eine Methode der Elternklasse
überschreibt, beheben Sie zuerst das Interface oder die Elternklasse, dann jede Implementierung. In
se7enxweb/expquery_translator haben sich die abstrakte Methode
`Visitor::visit( Node $node, ?Visitor $subVisitor = null, $options = null )` und alle ihre Implementierungen im
selben Commit geändert. PHP akzeptiert ein Kind, das großzügiger ist als die Elternklasse, daher bleibt bei einer
Änderung im selben Commit jeder Schritt ladbar.

Wenn die Elternklasse eine Kernel-Klasse ist, sehen Sie sich die aktuelle Signatur im Kernel an und übernehmen Sie
sie. Die Methoden des Kernels selbst wurden zwischen 6.0.8 und 6.0.11 für PHP 8.4 und 8.5 geändert
([PHP-8-Unterstützung](../../../bc/6.0/php8.md)), daher kann ein Override, der gegen einen älteren Kernel
geschrieben wurde, heute von seiner Elternklasse abweichen.

## Optionale Parameter vor erforderlichen

Die zweite Deprecation dieser Familie ist ein Standardwert an einem Parameter, auf den ein erforderlicher folgt:

```php
// a default that can never be used: $handlerParameters is required after it
protected function buildBoostFunctions( $boostFunctions = null, &$handlerParameters )
```

PHP 8.0 meldet das als veraltet, weil der Standardwert nie greifen kann. Aus se7enxweb/ezfind, Commit 4134cbb:

```php
// after: the default removed, the only caller passes both
protected function buildBoostFunctions( $boostFunctions, &$handlerParameters )
```

Prüfen Sie jeden Aufrufer, bevor Sie den Standardwert entfernen. Wenn ein Aufrufer das erste Argument weglässt, war
er bereits kaputt.

## Typdeklarationen zu altem Code hinzufügen

Typdeklarationen lassen Fehler dort erscheinen, wo sie gemacht werden, statt drei Aufrufe später. Es lohnt sich, sie
einer alten Erweiterung hinzuzufügen, aber nur dort, wo Sie jeden Aufrufer kontrollieren.

| Wo | Typen hinzufügen? |
|---|---|
| Private und geschützte Methoden Ihrer eigenen Klassen | ja |
| Öffentliche Methoden, die andere Erweiterungen aufrufen könnten | vorsichtig: ein strengerer Typ bricht Aufrufer, die etwas anderes übergeben haben |
| Methoden, die Kernel-Methoden überschreiben | nein: übernehmen Sie die Signatur der Elternklasse genau |
| `modify()` von Template-Operatoren, Methoden von Datentypen, `execute()` von Workflows | nein: Der Kernel ruft sie mit der Signatur der Basisklasse auf |
| Rückgabetypen von Methoden, die PHP-Interfaces implementieren (`Iterator`, `ArrayAccess`, `Countable`) | ja, oder `#[\ReturnTypeWillChange]` ([Veraltetes in PHP 8.1 bis 8.5](php-8-1-bis-8-5-veraltete-funktionen.md)) |

Typen, die Sie unter PHP 8.0, dem ältesten PHP von Exponential 6, verwenden können: skalare Typen, `?Type`,
Union-Typen (`int|string`), `mixed`, `static` als Rückgabetyp. Typen, die Sie unter 8.0 nicht verwenden können:
`never`, Intersection-Typen, `null` oder `false` als eigenständiger Typ, `readonly`-Eigenschaften, Enums.

Werte aus dem Kernel sind oft `false`, wo neuerer Code `null` verwenden würde. `eZContentObject::fetch()` liefert
`null`, wenn nichts gefunden wird, aber viele ältere Kernel-Funktionen liefern `false`. Ein Rückgabetyp
`?eZContentObject` an Ihrer eigenen Hülle ist falsch, wenn sie ein solches `false` durchreicht. Prüfen Sie, was die
Kernel-Methode zurückgibt, bevor Sie den Typ deklarieren:

```php
<?php
class myOldExtObjectLoader
{
    /**
     * @return eZContentObject|null
     */
    public static function load( int $objectID ): ?eZContentObject
    {
        $object = eZContentObject::fetch( $objectID );
        return $object instanceof eZContentObject ? $object : null;
    }
}
```

Die `instanceof`-Prüfung macht aus dem, was der Kernel zurückgegeben hat, den Typ, den Sie versprochen haben.

## Verwandte Seiten

- Zurück: [Konstruktoren alten Stils und statische Aufrufe](alte-konstruktoren-und-statische-aufrufe.md)
- Weiter: [Dynamische Eigenschaften](dynamische-eigenschaften.md)
- [Veraltetes in PHP 8.1 bis 8.5](php-8-1-bis-8-5-veraltete-funktionen.md)
- [Typdeklarationen](https://www.php.net/manual/en/language.types.declarations.php) im PHP-Handbuch
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
