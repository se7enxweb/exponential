---
title: Dynamische Eigenschaften
audience: developer
category: extension-development
order: 90
summary: PHP 8.2 markiert Eigenschaften, die eine Klasse nicht deklariert, als veraltet. Wie der Kernel sie bei persistenten Objekten behandelt, wie Sie sie in Ihren eigenen Klassen finden und wann Sie sie deklarieren, ein Array verwenden oder sie erlauben.
keywords: dynamische Eigenschaften, Eigenschaften deklarieren, veraltet, persistentes Objekt, dynamic properties, PHP 8.2, AllowDynamicProperties, declare properties, eZPersistentObject, __get, __set, stdClass, deprecated, upgrade
type: help
---
# Dynamische Eigenschaften

Teil 4, Kapitel 9. In PHP vor 8.2 konnten Sie jede Eigenschaft auf jedem Objekt setzen: `$this->cache = array();`
hat `cache` angelegt, wenn die Klasse es nicht deklarierte. PHP 8.2 meldet das als veraltet:

```text
Deprecated: Creation of dynamic property myOldExtHandler::$cache is deprecated
```

Alte Erweiterungen tun das überall, weil Code für PHP 4 und PHP 5 Eigenschaften oft nur in Kommentaren aufgeführt
hat. Dieses Kapitel erklärt, was der Kernel bereits für Sie erledigt und wie Sie Ihre eigenen Klassen korrigieren.

## Was der Kernel bereits erledigt

Drei Kernel-Klassen erlauben dynamische Eigenschaften bewusst, mit dem PHP-Attribut `#[AllowDynamicProperties]`:

| Klasse | Warum |
|---|---|
| `eZPersistentObject` (`kernel/classes/ezpersistentobject.php`) | die Felder einer Zeile werden namentlich als Eigenschaften gesetzt |
| `eZWorkflowProcess` | dasselbe, für Workflow-Prozesse |
| `eZINI` (`lib/ezutils/classes/ezini.php`) | alter Code setzt Eigenschaften auf Einstellungsobjekten |

`eZPersistentObject` geht noch weiter: Es hat die Methoden `__get()`, `__set()`, `__isset()` und `__unset()`, die
nicht deklarierte Felder in einem geschützten Array halten, sodass eine Unterklasse, die sich auf dynamische Felder
verlässt, die Deprecation nicht auslöst. Der Kernel hat das in 6.0.4 eingeführt
([PHP-8-Unterstützung](../../../../eng-us/bc/6.0/php8.md)).

Eine Ihrer Klassen, die `eZPersistentObject` erweitert, braucht also meist nichts. Jede andere Klasse schon.

## Sie finden

PHP meldet sie erst, wenn die Zeile ausgeführt wird. Um sie vorher zu finden, vergleichen Sie, was eine Klasse setzt,
mit dem, was sie deklariert. Das ist ein grober, aber nützlicher Anfang:

```bash
cd extension/myoldext
for f in $(grep -rlE '^\s*class\s' --include='*.php' .); do
  set=$(grep -oE '\$this->[A-Za-z_][A-Za-z0-9_]*\s*=[^=]' "$f" | sed -E 's/\$this->([A-Za-z0-9_]+).*/\1/' | sort -u)
  for p in $set; do
    grep -qE "(public|protected|private|var)\s+(\??[A-Za-z_\\]+\s+)?\\\$$p\b" "$f" || echo "$f: \$$p"
  done
done
```

Es gibt jedes `$this->x = ...` aus, dessen `x` dieselbe Datei nicht deklariert. Eine in einer Elternklasse
deklarierte Eigenschaft erscheint ebenfalls; prüfen Sie diese gegen die Elternklasse.

Der sichere Weg ist, die Erweiterung unter PHP 8.2 oder neuer mit eingeschalteter Debug-Ausgabe laufen zu lassen und
jede Seite, jede View und jedes Skript einmal zu verwenden.

## Lösung 1: die Eigenschaft deklarieren (die übliche Lösung)

```php
// before
class myOldExtHandler
{
    function __construct()
    {
        $this->cache = array();
        $this->ini = eZINI::instance( 'myoldext.ini' );
    }
}

// after
class myOldExtHandler
{
    /** @var array */
    protected $cache = array();

    /** @var eZINI */
    protected $ini;

    function __construct()
    {
        $this->ini = eZINI::instance( 'myoldext.ini' );
    }
}
```

Wählen Sie die Sichtbarkeit bewusst:

- `public`, wenn Templates sie lesen. Die Template-Engine liest Objekte über `attribute()` und `hasAttribute()`
  (siehe [Template-Operatoren](../teil-3-erweiterungsentwickler/template-operatoren.md)),
  aber einige alte Klassen geben öffentliche Eigenschaften direkt preis.
- `protected` für alles andere. Eine Unterklasse kann sie trotzdem verwenden.

Alter PHP-4-Code deklariert Eigenschaften mit `var`. `var` funktioniert weiterhin und bedeutet `public`; Sie können
es stehen lassen oder zur Klarheit `public` schreiben.

## Lösung 2: ein Array für Daten, die wirklich dynamisch sind

Wenn die Namen aus Daten stammen (die Spalten einer Importdatei, die Felder eines Formulars), legen Sie nicht für
jeden Namen eine Eigenschaft an. Halten Sie sie in einem deklarierten Array:

```php
<?php
class myOldExtRecord
{
    /** @var array<string, mixed> */
    protected $fields = array();

    public function set( string $name, $value ): void
    {
        $this->fields[$name] = $value;
    }

    public function get( string $name )
    {
        return $this->fields[$name] ?? null;
    }

    public function attributes(): array
    {
        return array_keys( $this->fields );
    }

    public function hasAttribute( $name ): bool
    {
        return array_key_exists( $name, $this->fields );
    }

    public function attribute( $name )
    {
        return $this->get( $name );
    }
}
```

Mit den letzten drei Methoden können Templates die Felder als `$record.title` lesen, so wie die Template-Engine
Kernel-Objekte liest.

## Lösung 3: sie erlauben, wenn es sein muss

Für eine Klasse, deren ganzer Entwurf von dynamischen Eigenschaften abhängt und die Sie jetzt nicht ändern können,
markieren Sie sie:

```php
#[AllowDynamicProperties]
class myOldExtLegacyContainer
{
}
```

Unter PHP 8.0 und 8.1 wird ein Attribut, das PHP nicht kennt, ignoriert, daher läuft das überall. Betrachten Sie es
als Notiz an sich selbst: Die Klasse braucht trotzdem eine echte Lösung.

## Objekte anderer Klassen

Bei der Deprecation geht es um die Klasse des Objekts, nicht darum, wer die Eigenschaft setzt. Eine Eigenschaft auf
einem Kernel-Objekt aus Ihrer Erweiterung zu setzen, ist dasselbe Problem:

```php
// deprecated: eZContentObjectTreeNode does not declare myoldextScore
// (eZContentObjectTreeNode extends eZPersistentObject, so in Exponential 6 this lands in its backing array)
$node->myoldextScore = 5;
```

Bei persistenten Objekten bleibt das in Exponential 6 zufällig ruhig, aber es ist zerbrechlich: Der Wert ist kein
Feld, wird nicht gespeichert und kann verschwinden, wenn das Kernel-Objekt erneut geholt wird. Halten Sie Ihre eigenen
Daten in Ihrer eigenen Struktur, zum Beispiel in einem Array mit der Knoten-ID als Schlüssel.

`stdClass` und Objekte aus `json_decode()` erlauben dynamische Eigenschaften per Definition und sind nicht betroffen.

## Verwandte Seiten

- Zurück: [Nullable-Parameter und Typdeklarationen](nullable-parameter-und-typen.md)
- Weiter: [Datenbankcode für PHP 8 und jede Engine](datenbankcode-fuer-php-8.md)
- [Persistente Objekte und Ihre eigenen Tabellen](../teil-3-erweiterungsentwickler/persistente-objekte-und-tabellen.md)
- [Deprecate dynamic properties](https://www.php.net/manual/en/migration82.deprecated.php) im Migrationsleitfaden zu PHP 8.2
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
