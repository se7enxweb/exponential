---
title: Entfernte PHP-Funktionen und ihr Ersatz
audience: developer
category: extension-development
order: 60
summary: Die PHP-Funktionen, die alte Erweiterungen aufrufen und die PHP 7 und 8 entfernt haben - each(), create_function(), ereg(), split(), mysql_*(), Magic Quotes und andere - mit einem getesteten Ersatz für jede.
keywords: entfernte Funktionen, Ersatz, Upgrade, Magic Quotes, removed functions, each, create_function, ereg, split, mysql_query, money_format, get_magic_quotes_gpc, fgetss, mcrypt, replacement, upgrade
type: help
---
# Entfernte PHP-Funktionen und ihr Ersatz

Teil 4, Kapitel 6. Eine für PHP 5 geschriebene Erweiterung ruft möglicherweise Funktionen auf, die es nicht mehr gibt.
Das Ergebnis ist `Call to undefined function`, ein fataler Fehler, sobald die Zeile ausgeführt wird. Dieses Kapitel
listet die Funktionen auf, die alte Erweiterungen am häufigsten verwenden, die PHP-Version, die sie entfernt hat, und
einen Ersatz. Für jede Funktion der ersten Tabelle wurde geprüft, dass sie in PHP 8.0 fehlt.

## Sie finden

```bash
grep -rnE '\b(each|create_function|split|spliti|ereg|eregi|ereg_replace|eregi_replace|mysql_[a-z_]+|money_format|get_magic_quotes_gpc|get_magic_quotes_runtime|set_magic_quotes_runtime|fgetss|call_user_method|session_register|mcrypt_[a-z_]+|is_real|hebrevc|convert_cyr_string)\s*\(' --include='*.php' extension/myoldext/
```

Ein Treffer in einem Kommentar oder in einem String ist harmlos; ein Treffer, dem `->` oder `::` vorausgeht, ist eine
gleichnamige Methode, nicht die PHP-Funktion.

## Die Tabelle

| Entfernt | Seit | Ersatz |
|---|---|---|
| `each()` | 8.0 | `foreach` oder `key()` mit `current()` und `next()` |
| `create_function()` | 8.0 | eine anonyme Funktion `function( $a ) { ... }` |
| `money_format()` | 8.0 | `NumberFormatter` oder `eZLocale::instance()->formatCurrency()` aus dem Kernel |
| `get_magic_quotes_gpc()`, `get_magic_quotes_runtime()` | 8.0 | nichts: Magic Quotes wurden in 5.4 entfernt; das Ergebnis ist immer `false` |
| `fgetss()`, `gzgetss()` | 8.0 | `fgets()` mit `strip_tags()` |
| `hebrevc()`, `convert_cyr_string()`, `restore_include_path()`, `ezmlm_hash()`, `image2wbmp()` | 8.0 | selten verwendet; siehe das PHP-Handbuch |
| `is_real()` | 8.0 | `is_float()` |
| `ereg()`, `eregi()`, `ereg_replace()`, `eregi_replace()`, `split()`, `spliti()` | 7.0 | `preg_match()`, `preg_replace()`, `explode()`, `preg_split()` |
| `mysql_*()` | 7.0 | `eZDB` aus dem Kernel (siehe unten), niemals direkt `mysqli_*()` |
| `call_user_method()` | 7.0 | `call_user_func( array( $object, 'method' ) )` |
| `set_magic_quotes_runtime()` | 7.0 | nichts |
| `mcrypt_*()` | 7.2 | `openssl_encrypt()`, `sodium_crypto_secretbox()` |
| `session_register()` | 5.4 | `$_SESSION` oder `eZHTTPTool::instance()->setSessionVariable()` aus dem Kernel |

## each()

Das alte Idiom durchläuft ein Array Eintrag für Eintrag:

```php
// before
while ( list( $key, $value ) = each( $array ) )
{
    echo "$key=$value\n";
}

// after
foreach ( $array as $key => $value )
{
    echo "$key=$value\n";
}
```

Wo die Schleife wirklich vom internen Zeiger des Arrays abhängt (sie macht dort weiter, wo eine frühere Schleife
aufgehört hat), behalten Sie den Zeiger bei und ersetzen nur den Aufruf. Das war die Lösung in der Erweiterung
birthday bei der Prüfung für PHP 8.0:

```php
<?php
function myOldExtNextPair( array &$array )
{
    $key = key( $array );
    if ( $key === null )
    {
        return false;
    }
    $value = current( $array );
    next( $array );
    return array( $key, $value );
}
```

## create_function()

`create_function()` hat mit `eval()` eine Funktion aus einem String gebaut, und das ist auch der Grund, warum sie
entfernt wurde. Ersetzen Sie den String durch eine echte anonyme Funktion:

```php
// before
usort( $nodes, create_function( '$a,$b', 'return strcmp( $a->attribute( "name" ), $b->attribute( "name" ) );' ) );

// after
usort( $nodes, function( $a, $b ) {
    return strcmp( $a->attribute( 'name' ), $b->attribute( 'name' ) );
} );
```

## ereg() und split()

Aus POSIX-Regulärausdrücken wurden PCRE-Ausdrücke. Fügen Sie Begrenzer hinzu und escapen Sie den Begrenzer, wenn er
im Muster vorkommt:

| Vorher | Nachher |
|---|---|
| `ereg( '^[0-9]+$', $s )` | `preg_match( '/^[0-9]+$/', $s )` |
| `eregi( 'abc', $s )` | `preg_match( '/abc/i', $s )` |
| `ereg_replace( '[^a-z]', '', $s )` | `preg_replace( '/[^a-z]/', '', $s )` |
| `split( ',', $s )` | `explode( ',', $s )` (ein einfacher String braucht keinen regulären Ausdruck) |
| `split( '[,;]', $s )` | `preg_split( '/[,;]/', $s )` |

`split()` mit einem einfachen Trennzeichen war die Lösung in enhancedezbinaryfile bei der Prüfung für PHP 8.0.

> **Warnung**
> Prüfen Sie, was `ereg()` zurückgegeben hat, bevor Sie sich auf den Ersatz verlassen. `ereg()` lieferte die Länge
> des Treffers oder `false`; `preg_match()` liefert `1`, `0` oder `false`. Code, der das Ergebnis mit einer Zahl
> verglichen hat, muss ein zweites Mal angesehen werden.

## mysql_*()

Eine Erweiterung, die über `mysql_query()` mit der Datenbank spricht, war schon zu ihrer Zeit falsch: Sie
funktionierte nur auf MySQL und nur zufällig auf der Verbindung des Kernels. Exponential 6 unterstützt mehrere
Datenbank-Engines, und der Ersatz ist die Datenbankschicht des Kernels, die die Verbindung der Website verwendet:

```php
<?php
$db = eZDB::instance();
$id = (int)$http->postVariable( 'ItemID' );
$name = $db->escapeString( $http->postVariable( 'Name' ) );

$rows = $db->arrayQuery( "SELECT id, name FROM myoldext_item WHERE id = $id" );
$db->query( "UPDATE myoldext_item SET name = '$name' WHERE id = $id" );
```

Ganzzahlen werden mit `(int)` umgewandelt, Strings laufen durch `escapeString()`. Für alles, was über ein paar
Abfragen hinausgeht, verwenden Sie stattdessen `eZPersistentObject`
([Persistente Objekte und Ihre eigenen Tabellen](../teil-3-erweiterungsentwickler/persistente-objekte-und-tabellen.md)).
Das ganze Thema steht in [Datenbankcode für PHP 8 und jede Engine](datenbankcode-fuer-php-8.md).

## Magic Quotes

Alter Code hat oft jede Eingabe in eine Prüfung wie diese gepackt:

```php
// before
$value = get_magic_quotes_gpc() ? stripslashes( $_POST['value'] ) : $_POST['value'];

// after
$value = $http->postVariable( 'value' );
```

Magic Quotes wurden in PHP 5.4 entfernt, der Zweig mit `stripslashes()` ist also seit über einem Jahrzehnt nicht
mehr gelaufen. Lesen Sie Eingaben über `eZHTTPTool` (`$http = eZHTTPTool::instance();`), wie es die Module des
Kernels bereits tun.

## mcrypt

`mcrypt` wurde in PHP 7.2 entfernt. Damit verschlüsselte Daten lassen sich mit OpenSSL nur für einige seiner
Verschlüsselungsverfahren und Modi entschlüsseln, und nur mit Sorgfalt. Planen Sie diese Änderung gesondert:
Schreiben Sie ein einmaliges Skript, das auf einer Installation mit PHP 7.1 mit der alten Methode entschlüsselt und
mit `sodium_crypto_secretbox()` oder `openssl_encrypt()` neu verschlüsselt. Die Quellen dieses Projekts enthalten
keine solche Migration, testen Sie sie also an einer Kopie der Daten.

## Wenn eine Bibliothek das Problem ist

Manchmal steckt die entfernte Funktion in einer Bibliothek von Dritten, die die Erweiterung mitliefert (ein alter
PDF-Generator, eine alte Mail-Bibliothek). Patchen Sie die Bibliothek nicht Zeile für Zeile. Suchen Sie nach einem
neueren Release derselben Bibliothek oder nach einem gepflegten Ersatz und aktualisieren Sie sie als Ganzes, mit ihrer
Lizenz. Halten Sie im Changelog der Erweiterung fest, auf welche Version Sie gewechselt sind und warum.

## Verwandte Seiten

- Zurück: [Veraltetes in PHP 8.1 bis 8.5](php-8-1-bis-8-5-veraltete-funktionen.md)
- Weiter: [Konstruktoren alten Stils und statische Aufrufe](alte-konstruktoren-und-statische-aufrufe.md)
- [PHP-8.0-Unterstützung](../../../bc/6.0/php-8.0-unterstuetzung.md)
- [Entfernte Erweiterungen und SAPIs](https://www.php.net/manual/en/migration70.removed-exts-sapis.php) im PHP-Handbuch
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
