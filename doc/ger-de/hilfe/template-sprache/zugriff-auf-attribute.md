---
title: Zugriff auf Attribute
audience: developer, designer, app-creator
category: development
order: 100
summary: Wie $a.b und $a[b] aufgelöst werden - Arrays nach Schlüssel, Objekte über attributes(), hasAttribute() und attribute(), Felder und Funktionsattribute von eZPersistentObject, dynamische Schlüssel, numerische Indizes, Attribute nach einem Operator, der Fehler mit seiner Liste von Wahlmöglichkeiten und die Operatoren attribute und dump, um in einen Wert hineinzusehen - gezeigt an Knoten 90 von alpha.
keywords: Zugriff auf Attribute, Punktschreibweise, eckige Klammern, Attributprotokoll, hasAttribute, attribute(), attributes(), eZPersistentObject, function_attributes, Felder, data_map, Operator attribute, dump, Fehlersuche, attribute access, dot notation, brackets, attribute protocol, fields, debug
type: help
---
# Zugriff auf Attribute

`$node.parent.name` liest sich wie eine Kette von Array-Schlüsseln, aber `$node` ist ein PHP-Objekt. Die Template-Engine löst jeden Schritt
über ein kleines Protokoll auf, und dieses Protokoll macht Knoten, Inhaltsobjekte, Benutzer und jedes andere Objekt des Kernels in Templates
nutzbar. Der Code ist der Zweig `TYPE_ATTRIBUTE` von `elementValue()` in `lib/eztemplate/classes/eztemplate.php`. Die Beispiele nutzen
Knoten 90 von alpha, den Artikel „Privacy Policy“.

## Inhalt

1. [Punkte und eckige Klammern](#punkte-und-eckige-klammern)
2. [Wie ein Schritt aufgelöst wird](#wie-ein-schritt-aufgelöst-wird)
3. [Felder und Funktionsattribute](#felder-und-funktionsattribute)
4. [Dynamische Schlüssel](#dynamische-schlüssel)
5. [Indizes](#indizes)
6. [Attribute nach einem Operator](#attribute-nach-einem-operator)
7. [Ein fehlendes Attribut](#ein-fehlendes-attribut)
8. [In einen Wert hineinsehen: attribute und dump](#in-einen-wert-hineinsehen-attribute-und-dump)
9. [Die eigene Klasse lesbar machen](#die-eigene-klasse-lesbar-machen)
10. [Verwandte Seiten](#verwandte-seiten)

## Punkte und eckige Klammern

`.name` und `['name']` sind derselbe Zugriff. In eckigen Klammern darf jeder Ausdruck stehen, eckige Klammern sind also die Form für berechnete
Schlüssel; nach einem Punkt nur ein Name, eine Zahl oder eine Variable.

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 90 ) )}
[{$node.name}] [{$node['name']}] [{$node.object.id}] [{$node.parent.name}]
[{$node.data_map.title.content}] [{$node['data_map']['title'].data_type_string}]
```

Ausgabe auf alpha:

```text
[Privacy Policy] [Privacy Policy] [88] [Fit & Healthy]
[Privacy Policy] [ezstring]
```

## Wie ein Schritt aufgelöst wird

Für jeden Schritt nimmt die Engine den Schlüssel (er muss eine Zahl, eine Zeichenkette oder ein boolescher Wert sein, sonst
`Cannot use type ... for attribute lookup`) und dann:

| Der Wert ist | Der Schritt tut |
|---|---|
| ein Array | `array_key_exists( key )`, dann `$value[key]` |
| ein Objekt mit `attribute()` und `hasAttribute()` | `hasAttribute( key )`, dann `attribute( key )` |
| ein Objekt ohne diese Methoden | Fehler `Cannot retrieve attribute of object(...), no attribute functions available` |
| alles andere | kein Wert |

Öffentliche Eigenschaften von PHP werden nicht gelesen: Nur das Attributprotokoll zählt.

## Felder und Funktionsattribute

Objekte des Kernels leiten sich von `eZPersistentObject` ab (`kernel/classes/ezpersistentobject.php`). Ihre `definition()` führt **Felder**
(Datenbankspalten) und **Funktionsattribute** (Namen, die Methoden zugeordnet sind) auf, und `attributes()`, `hasAttribute()` und
`attribute()` bedienen beide:

- ein Feld gibt das Mitglied zurück, das die Spalte hält, ohne Abfrage;
- ein Funktionsattribut ruft seine Methode auf, zum Beispiel ruft `children_count` eine Methode auf, die in der Datenbank zählt, `data_map`
  lädt die Felder des Objekts, `url_alias` baut die URL.

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 90 ) )}
field:    node_id={$node.node_id} depth={$node.depth} path_string={$node.path_string}
function: children_count={$node.children_count} class_identifier={$node.class_identifier} url_alias={$node.url_alias}
object field: published={$node.object.published} section_id={$node.object.section_id}
object function: class_name={$node.object.class_name} main_node_id={$node.object.main_node_id}
```

Ausgabe auf alpha:

```text
field:    node_id=90 depth=3 path_string=/1/2/89/90/
function: children_count=0 class_identifier=ng_article url_alias=privacy-policy
object field: published=1674667522 section_id=1
object function: class_name=NG Article main_node_id=90
```

Dieser Unterschied zählt für die Geschwindigkeit: Ein Funktionsattribut in einer Schleife führt seine Methode in jedem Durchlauf aus. Lesen
Sie es einmal mit `{def}` in eine Variable, wenn Sie es mehrfach brauchen.

## Dynamische Schlüssel

Eine Variable in eckigen Klammern, oder nach einem Punkt, liefert den Schlüssel:

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 90 ) )
     $field = 'node_id'
     $keys = array( 'name', 'depth', 'class_identifier' )}
[{$node[$field]}] [{$node.$field}]
{foreach $keys as $k}[{$k}={$node[$k]}]{/foreach}
```

Ausgabe auf alpha:

```text
[90] [90][name=Privacy Policy][depth=3][class_identifier=ng_article]
```

## Indizes

Listen werden nach Position gelesen, mit einer Zahl nach dem Punkt oder einem beliebigen Ausdruck in eckigen Klammern:

```tpl
{def $list = array( 'zero', 'one', 'two' ) $i = 2}
[{$list.0}] [{$list[1]}] [{$list[$i]}] [{$list[$i|dec]}]
```

Ausgabe auf alpha:

```text
[zero] [one] [two] [one]
```

## Attribute nach einem Operator

Ein Punkt oder eckige Klammern dürfen auf einen Operatoraufruf folgen, sodass sich das Ergebnis eines Fetch oder eines Array-Operators direkt
lesen lässt:

```tpl
[{fetch( 'content', 'node', hash( 'node_id', 89 ) ).name}]
[{fetch( 'content', 'node', hash( 'node_id', 89 ) ).children|count}]
[{array( 'x', 'y', 'z' )|reverse.0}]
```

Ausgabe auf alpha:

```text
[Fit & Healthy]
[16]
[z]
```

## Ein fehlendes Attribut

Ein Schlüssel, der nicht existiert, protokolliert einen Fehler und ergibt keinen Wert. Bei Arrays und Objekten führt die Meldung die
vorhandenen Schlüssel auf (die ersten 25, bei Objekten aus `attributes()`), was sie zum schnellsten Weg macht herauszufinden, was ein Wert
bietet:

```tpl
{def $map = hash( 'red', 1, 'green', 2 )}
[{$map.blue}]
```

Ausgabe auf alpha:

```text
[]
```

Von der Template-Engine protokolliert:

```text
ERROR : No such attribute for array(2): blue
Choose one of following: red, green
```

## In einen Wert hineinsehen: attribute und dump

Der Operator `attribute` (`eZTemplateAttributeOperator`) durchläuft einen Wert und führt seine Schlüssel und Typen auf, mit Werten, wenn der
erste Parameter `show` ist. Seine Parameter sind `show_values` (`show` oder etwas anderes), `max_val` (Ebenen, in die er absteigt, Standard 2
bei `attribute` und 1 bei `dump`) und `format` (`html` oder `text`, Standard aus `template.ini` [AttributeOperator] DefaultFormatter, also
`html`). `dump` ist derselbe Operator, mit standardmäßig angezeigten Werten.

```tpl
{hash( 'title', 'Hello', 'sizes', array( 'S', 'M' ) )|attribute( show, 2, text )}
```

Ausgabe auf alpha:

```text
title (string='Hello')
sizes (array=Array(2))
    0 (string='S')
    1 (string='M')
```

```tpl
{hash( 'title', 'Hello', 'count', 3 )|dump( show, 1, text )}
```

Ausgabe auf alpha:

```text
title (string='Hello')
count (integer=3)
```

Auf einem Objekt führt er jedes Attribut auf, das das Objekt bietet; so lassen sich die Attributnamen eines Knotens auf Ihrer eigenen
Installation lesen. Die ersten 12 Zeilen für Knoten 90 (der Operator gibt Text zurück, die üblichen Zeichenketten- und Array-Operatoren können
ihn also zuschneiden):

```tpl
{def $node = fetch( 'content', 'node', hash( 'node_id', 90 ) )
     $list = $node|attribute( hide, 1, text )}
{$list|explode( "\n" )|extract_left( 12 )|implode( "\n" )}
```

Ausgabe auf alpha:

```text
node_id (integer)
parent_node_id (integer)
main_node_id (integer)
contentobject_id (integer)
contentobject_version (integer)
contentobject_is_published (integer)
depth (integer)
sort_field (integer)
sort_order (integer)
priority (integer)
modified_subnode (integer)
path_string (string)
```

Entfernen Sie den Operator wieder, bevor das Template live geht: Er ruft jedes Funktionsattribut des Objekts auf.

## Die eigene Klasse lesbar machen

Jedes PHP-Objekt wird in Templates lesbar, wenn es die drei Methoden hat. Eine Klasse, die `eZPersistentObject` erweitert, bekommt sie aus
ihrer Definition. Eine einfache Klasse setzt sie selbst um:

```php
class expExampleBox
{
    public function attributes()
    {
        return array( 'title', 'items' );
    }

    public function hasAttribute( $name )
    {
        return in_array( $name, $this->attributes() );
    }

    public function attribute( $name )
    {
        switch ( $name )
        {
            case 'title': return $this->title;
            case 'items': return $this->loadItems();
        }
        return null;
    }
}
```

Diese Skizze wurde nicht auf alpha ausgeführt; sie zeigt die Form, auf die die Engine prüft (`method_exists( $value, 'attribute' )` und
`method_exists( $value, 'hasAttribute' )`).

## Verwandte Seiten

- Zurück: [Namensräume](namensraeume.md)
- Weiter: [Operatoren in Ausdrücken](operatoren-in-ausdruecken.md)
- [Attribute und Punktschreibweise](../../../en/us/help/designers/templates/attributes-and-dot-notation.md)
- [Knoten, Objekte und Attribute](../../../en/us/help/designers/templates/nodes-objects-attributes.md)
- [attribute](../../../en/us/examples/template-operators/attribute.md) und [dump](../../../en/us/examples/template-operators/dump.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
