---
title: PHP-8-Fehler, die alten Code stoppen
audience: developer
category: extension-development
order: 40
summary: Die Änderungen in PHP 8, die alten Erweiterungscode in fatale Fehler verwandeln - nackte Wörter als Konstanten, statische Aufrufe, falsche Argumenttypen, count() auf null, inkompatible Signaturen - und wie Sie jeden davon finden und beheben.
keywords: fataler Fehler, undefinierte Konstante, Signatur, Upgrade, PHP 8, fatal error, TypeError, Error, undefined constant, count, method_exists, implode, signature, ArgumentCountError, upgrade
type: help
---
# PHP-8-Fehler, die alten Code stoppen

Teil 4, Kapitel 4. In diesem Kapitel geht es um die Änderungen in PHP 8.0, die Code, der früher mit einer Warnung
oder stillschweigend lief, in einen `Error` oder `TypeError` verwandeln, der die Anfrage abbricht. Sie sind das
Erste, was Sie beheben, denn solange die Seite leer bleibt, lässt sich nichts anderes testen. Jedes Beispiel stammt
aus einer echten Erweiterung und nennt den Commit, der es behoben hat.

## Wie sich diese Fehler zeigen

Auf einer Entwicklungskopie mit eingeschalteter Debug-Ausgabe liefert ein fataler Fehler eine Meldung, eine Datei
und eine Zeile. Im Produktivbetrieb liefert er eine leere Seite oder die Fehlerseite des Kernels, und die Meldung
steht im PHP-Fehlerlog und in `var/log/error.log`. Die Meldung beginnt immer mit der Art des Fehlers:

| Meldung beginnt mit | Bedeutung |
|---|---|
| `Uncaught Error: Undefined constant` | ein nacktes Wort, das PHP 7 für einen String hielt |
| `Uncaught Error: Non-static method ... cannot be called statically` | ein statischer Aufruf einer Instanzmethode |
| `Uncaught Error: Call to undefined method` | die Methode existiert nicht (oft hat sie nie existiert, und die Zeile wurde vorher nie erreicht) |
| `Uncaught TypeError: ... must be of type ..., ... given` | eine eingebaute Funktion hat den falschen Typ erhalten |
| `Uncaught ArgumentCountError` | zu wenige Argumente für eine Funktion oder Methode |
| `Fatal error: Declaration of ... must be compatible with ...` | die Methode einer Kindklasse passt nicht zu ihrer Elternklasse |

## 1. Nackte Wörter sind keine Strings mehr

PHP 7 machte aus einer undefinierten Konstante den String ihres Namens, mit einer Warnung. PHP 8 bricht ab. Alte
`ezinfo.php`-Dateien sind die häufigste Fundstelle. Aus se7enxweb/expsite_installer, Commit 0c771fe:

```php
// before: Name, Version ... are undefined constants
return array( Name => "expsite_installer",
              Version => "1.0.1",
              info_url => "https://github.com/se7enxweb/expsite_installer" );

// after
return array( 'Name' => "expsite_installer",
              'Version' => "1.0.1",
              'Info_url' => "https://github.com/se7enxweb/expsite_installer" );
```

Beachten Sie die zweite Korrektur in denselben Zeilen: Der Schlüssel, den die Seite „Über“ liest, ist `Info_url`,
nicht `info_url`. Suchen Sie nach nackten Wörtern in Array-Schlüsseln und in Zugriffen der Form `$array[key]`:

```bash
grep -rnE '\$[A-Za-z_][A-Za-z0-9_]*\[\s*[A-Za-z_][A-Za-z0-9_]*\s*\]' --include='*.php' .
grep -rnE '^\s*(return array\(\s*)?[A-Za-z_][A-Za-z0-9_]*\s*=>' --include='*.php' .
```

Jeder Treffer ist eine Zeile zum Ansehen: Eine echte Konstante wie `PHP_EOL` oder eine Klassenkonstante ist in
Ordnung, ein Wort, das als String gemeint ist, braucht Anführungszeichen.

## 2. Statisch aufgerufene Instanzmethoden

PHP 7 erlaubte `ClassName::method()` auf einer Methode ohne `static`, mit einem Deprecation-Hinweis, und `$this` war
darin undefiniert. PHP 8 wirft eine Ausnahme. Aus se7enxweb/sevenx_themes_media, Commit f7d93c0, in einem
Template-Operator:

```php
// before: classAttributeContent() is an instance method of eZSelectionType
$classContent = eZSelectionType::classAttributeContent( $classAttr );

// after: ask the class attribute for its content, which calls the datatype for you
$classContent = $classAttr->attribute( 'content' );
```

Die allgemeine Lösung ist, sich zuerst eine Instanz zu holen oder, wie hier, den übergeordneten Aufruf zu verwenden,
den der Kernel anbietet. Lesen Sie die Kernel-Methode, bevor Sie irgendetwas `static` hinzufügen: Wenn sie `$this`
verwendet, kann sie nicht statisch sein.

## 3. Eingebaute Funktionen prüfen ihre Argumenttypen

Die meisten eingebauten Funktionen werfen jetzt einen `TypeError`, wenn sie einen Typ erhalten, mit dem sie nichts
anfangen können. Die, auf die alter Code am häufigsten trifft:

| Aufruf | PHP 7 | PHP 8 | Lösung |
|---|---|---|---|
| `count( null )`, `count( 'text' )` | Warnung, lieferte 0 oder 1 | `TypeError` | `is_countable( $x ) ? count( $x ) : 0` |
| `method_exists( false, 'm' )` | lieferte false | `TypeError` | `is_object( $x ) && method_exists( $x, 'm' )` |
| `implode( $array, ', ' )` (Teile zuerst) | funktionierte | `TypeError` | `implode( ', ', $array )` |
| `array_merge( $a, null )` | Warnung, lieferte null | `TypeError` | `array_merge( $a, $b ?? array() )` |
| `in_array( $x, null )` | Warnung | `TypeError` | zuerst `is_array()` prüfen |
| `strlen( array() )` | Warnung | `TypeError` | herausfinden, warum ein Array angekommen ist |

Aus se7enxweb/explayouts_ui_api, Commit 2a95edc: Ein Block, dessen Definition keine Einstellungen hatte, ließ den
Handler bei `false`, und `method_exists()` brach die ganze Anfrage ab, sodass die Seitenleiste des Editors endlos
lud:

```php
// before
$itemViewTypes = method_exists( $handler, 'getItemViewTypes' ) ? $handler->getItemViewTypes() : array();

// after
$itemViewTypes = is_object( $handler ) && method_exists( $handler, 'getItemViewTypes' )
    ? $handler->getItemViewTypes()
    : array();
```

`implode()` mit den Argumenten in der alten Reihenfolge wurde bei der Prüfung für PHP 8.0 im OAuth-Modul des Kernels,
in ezstarrating und in swark gefunden ([PHP-8.0-Unterstützung](../../../../eng-us/bc/6.0/php-8.0-support.md)).

## 4. Zu wenige Argumente

Eine Benutzerfunktion mit weniger Argumenten aufzurufen, als sie verlangt, war eine Warnung; jetzt ist es ein
`ArgumentCountError`. Ebenso der Aufruf einer eingebauten Funktion ohne ein Argument, das sie braucht: `crypt()` ohne
Salt war einer der Befunde in der LDAP-Benutzerklasse des Kernels, behoben mit einem zufälligen Passwort aus
`random_bytes()`.

Auch das Gegenteil zählt: Eine Funktion, die in einem späteren PHP ein Argument hinzubekommen hat, ergibt auf dem
älteren einen `ArgumentCountError`. `fputcsv()` mit seinem `$eol`-Argument (hinzugefügt in PHP 8.1) bricht auf
PHP 8.0 ab, daher verzweigt der Kernel dort anhand von `PHP_VERSION_ID`.

## 5. Methoden, die nicht zu ihrer Elternklasse passen

PHP 8 prüft beim Laden der Klasse jede Methode, die eine Methode der Elternklasse überschreibt. Eine Abweichung ist
ein fataler Fehler, auch wenn die Methode nie aufgerufen wird. Zwei Beispiele aus der Prüfung für PHP 8.0:

| Erweiterung | Klasse und Methode | Problem | Lösung |
|---|---|---|---|
| syndication | `eZSyndicateType::onPublish()` | Parameter per Referenz, die der Elternklasse per Wert | `&` entfernt |
| ezstarrating | `ezsrRatingObjectTreeNode::makeObjectsArray()` | weniger Parameter als `eZContentObjectTreeNode::makeObjectsArray()` | Parameter hinzugefügt |

Um so einen Fehler zu beheben, öffnen Sie die Elternklasse im Kernel und übernehmen Sie ihre Signatur genau:
dieselben Parameter, dieselben Standardwerte, dieselben Referenzzeichen. Sie dürfen am Ende optionale Parameter
hinzufügen; Sie dürfen keine entfernen.

## 6. Methoden, die es nie gab

Alter Code ruft manchmal eine Methode auf, die nicht existiert, in einem Zweig, der nie erreicht wurde oder nur mit
einer durch `@` verschluckten Warnung. PHP 8 macht den Zweig fatal. Aus se7enxweb/explayouts_site_api, Commit
031d380:

```php
// before: eZURLAliasML::fetchByNodeID() does not exist
return '/' . eZURLAliasML::fetchByNodeID( $value->mainLocationId() );

// after
$node = $value->mainLocationId() > 0 ? eZContentObjectTreeNode::fetch( (int)$value->mainLocationId() ) : null;
return $node instanceof eZContentObjectTreeNode ? '/' . $node->attribute( 'url_alias' ) : '';
```

Wann immer Sie in altem Code einen Kernel-Klassennamen sehen, prüfen Sie, ob die Methode existiert, bevor Sie ihr
vertrauen:

```bash
grep -n "function fetchByNodeID" kernel/classes/ezurlaliasml.php
```

## 7. Entfernte Funktionen

Der Aufruf einer Funktion, die PHP entfernt hat (`each()`, `create_function()`, `split()`, `ereg()`,
`mysql_query()` ...), ergibt `Call to undefined function`. Diese haben ein eigenes Kapitel:
[Entfernte PHP-Funktionen und ihr Ersatz](entfernte-php-funktionen.md).

## 8. Konstruktoren, die nicht aufgerufen werden

Dies ist überhaupt kein Fehler, und genau das macht es schlimmer: PHP 8 führt eine Methode mit dem Namen ihrer Klasse
nicht mehr als Konstruktor aus, also wird das Objekt erzeugt, ohne eingerichtet zu werden, und der Fehler tritt
später an einer anderen Stelle auf. Siehe [Konstruktoren alten Stils und statische Aufrufe](alte-konstruktoren-und-statische-aufrufe.md).

## Ein Test, der die meisten findet

Führen Sie jeden Einstiegspunkt einmal mit eingeschalteter Debug-Ausgabe aus, wobei jeder Fehler gemeldet wird:

```bash
php -d error_reporting=-1 -d display_errors=1 bin/php/ezexec.php extension/myoldext/bin/php/somescript.php
```

Öffnen Sie Webseiten jeweils mit eingeschalteter Debug-Ausgabe. Für Klassen findet ein kleines Skript, das sie nur
lädt, Signaturabweichungen sofort:

```php
<?php
// extension/myoldext/tests/load_all_classes.php: run with php bin/php/ezexec.php
$autoload = include 'var/autoload/ezp_extension.php';
foreach ( $autoload as $class => $file )
{
    if ( strpos( $file, 'extension/myoldext/' ) === 0 && !class_exists( $class ) && !interface_exists( $class ) )
    {
        echo "Not loaded: $class ($file)\n";
    }
}
echo "Done\n";
```

Das Laden einer Klasse kompiliert alle ihre Methoden, daher erscheint hier jeder „must be compatible“-Fehler, ob die
Methode je aufgerufen wird oder nicht.

## Verwandte Seiten

- Zurück: [Bestandsaufnahme einer alten Erweiterung](bestandsaufnahme-einer-alten-erweiterung.md)
- Weiter: [Veraltetes in PHP 8.1 bis 8.5](php-8-1-bis-8-5-veraltete-funktionen.md)
- [PHP-8.0-Unterstützung](../../../../eng-us/bc/6.0/php-8.0-support.md)
- [Inkompatible Änderungen in PHP 8.0](https://www.php.net/manual/en/migration80.incompatible.php) im PHP-Handbuch
- [Erweiterungen debuggen](../teil-3-erweiterungsentwickler/erweiterungen-debuggen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
