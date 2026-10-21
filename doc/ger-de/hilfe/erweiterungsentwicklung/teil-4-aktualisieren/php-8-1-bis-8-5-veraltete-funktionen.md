---
title: Veraltetes in PHP 8.1 bis 8.5
audience: developer
category: extension-development
order: 50
summary: Die Deprecations, die jedes PHP-Release von 8.1 bis 8.5 hinzugefügt hat und auf die alter Erweiterungscode trifft, wie sie in der Debug-Ausgabe erscheinen und für jede eine Lösung, die auch auf PHP 8.0 läuft.
keywords: veraltet, Deprecation, null an String-Funktionen, Rückgabetyp, deprecated, PHP 8.1, PHP 8.2, PHP 8.3, PHP 8.4, PHP 8.5, ReturnTypeWillChange, null to string functions, utf8_decode, curl_close, imagedestroy, http_response_header, implicitly nullable
type: help
---
# Veraltetes in PHP 8.1 bis 8.5

Teil 4, Kapitel 5. Eine Deprecation hält Ihren Code nicht an. Mit ihr teilt PHP Ihnen mit, dass eine spätere Version
es tun wird. Dieses Kapitel listet Version für Version die Deprecations auf, auf die alter Erweiterungscode am
häufigsten trifft, und für jede eine Lösung, die auf jedem PHP funktioniert, das Exponential 6 unterstützt, von 8.0
bis 8.5.

## Warum etwas beheben, das noch funktioniert

- Exponential 6 zeigt Deprecations in der Debug-Ausgabe. Eine Seite voller Deprecations verdeckt die Warnung, die Sie
  sehen müssen.
- Die nächste PHP-Version macht aus einigen davon Fehler, und zwar an dem Tag, an dem ein Hosting-Unternehmen seine
  Server aktualisiert.
- Unter Exponential Velocity, dem Anwendungsserver, schreibt ein langlaufender Worker dieselbe Deprecation bei jeder
  Anfrage, was die Logs schnell füllt.

## Wie Sie sie sehen

Bei eingeschalteter Debug-Ausgabe erscheinen Deprecations als Hinweise mit „Deprecated“ im Text. Auf der
Kommandozeile:

```bash
php -d error_reporting=-1 -d display_errors=1 bin/php/ezexec.php extension/myoldext/bin/php/myscript.php 2>&1 | grep -i deprecated
```

Filtern Sie nach Ihren eigenen Dateien: Der Kernel und die mitgelieferten Erweiterungen werden frei von Deprecations
gehalten, eine Zeile, die `extension/myoldext/` nennt, gehört also Ihnen.

## PHP 8.1

| Veraltet | Typischer alter Code | Lösung, die auch auf 8.0 läuft |
|---|---|---|
| `null` an einen nicht nullable Parameter einer eingebauten Funktion übergeben | `strlen( $row['title'] )`, wenn die Spalte `NULL` ist; `trim( $http->postVariable( 'X' ) )`, wenn die Variable fehlt | `strlen( (string)$row['title'] )` oder `$value ?? ''` |
| Rückgabetypen von Methoden, die eingebaute Interfaces implementieren | `function current()` in einer Klasse, die `Iterator` implementiert, `function offsetGet( $o )` in `ArrayAccess` | `#[\ReturnTypeWillChange]` über der Methode hinzufügen oder den echten Rückgabetyp (`mixed` gibt es seit 8.0) |
| `Serializable` ohne `__serialize()` / `__unserialize()` | Klassen, die nur `serialize()` und `unserialize()` implementieren | `__serialize()` und `__unserialize()` daneben hinzufügen |
| `strftime()`, `gmstrftime()` | Datumsformatierung in Template-Operatoren und Mails | `date()` oder `IntlDateFormatter` oder die Formatierung von `eZLocale` im Kernel |
| `FILTER_SANITIZE_STRING` | `filter_var( $x, FILTER_SANITIZE_STRING )` | stattdessen bei der Ausgabe escapen (`htmlspecialchars()` oder der Template-Operator `wash`) |
| Implizite verlustbehaftete Umwandlung einer Gleitkommazahl in einen ganzzahligen Schlüssel oder ein Argument | `$array[ $total / 2 ]` | `(int)( $total / 2 )` |

Der Kernel selbst hat in 6.0.11 `#[ReturnTypeWillChange]` an seinen SPL-Methoden erhalten
([PHP-8-Unterstützung](../../../bc/6.0/php8.md)). Ein Attribut ist für PHP 7 ein Kommentar und für 8.0
wirkungslos, daher ist es die sicherste Lösung:

```php
<?php
class myOldExtList implements Iterator
{
    private $items = array();
    private $position = 0;

    #[\ReturnTypeWillChange]
    public function current()
    {
        return $this->items[$this->position];
    }

    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->position;
    }

    #[\ReturnTypeWillChange]
    public function next()
    {
        $this->position++;
    }

    #[\ReturnTypeWillChange]
    public function rewind()
    {
        $this->position = 0;
    }

    #[\ReturnTypeWillChange]
    public function valid()
    {
        return isset( $this->items[$this->position] );
    }
}
```

## PHP 8.2

| Veraltet | Typischer alter Code | Lösung |
|---|---|---|
| Eine Eigenschaft anlegen, die die Klasse nicht deklariert | `$this->cache = array();` in einer Klasse ohne `public $cache;` | die Eigenschaft deklarieren; siehe [Dynamische Eigenschaften](dynamische-eigenschaften.md) |
| `utf8_encode()`, `utf8_decode()` | `strlen( utf8_decode( $s ) )`, um Zeichen zu zählen | `mb_strlen( $s, 'UTF-8' )`, `mb_convert_encoding()` |
| `"${var}"` in Strings | `"Hello ${name}"` | `"Hello {$name}"` |
| Callables in der Form `"self::method"`, `"parent::method"`, `array( 'static', 'm' )` | `call_user_func( 'self::helper' )` | `array( self::class, 'helper' )` oder `array( $this, 'helper' )` |

Aus se7enxweb/sevenx_dse, Commit b02ff09:

```php
// before
return strlen($string) != strlen(utf8_decode($string));

// after
return strlen($string) != mb_strlen($string, "UTF-8");
```

## PHP 8.3

| Veraltet | Typischer alter Code | Lösung |
|---|---|---|
| `get_class()` und `get_parent_class()` ohne Argument | `get_class()` innerhalb einer Methode | `static::class`, `self::class` oder `get_class( $this )` |
| Inkrementieren und Dekrementieren nicht numerischer Strings | `$letter++`, um von „a“ zu „b“ zu kommen | `str_increment()`, wo PHP es hat (ab 8.3), sonst `chr( ord( $letter ) + 1 )` |

## PHP 8.4

| Veraltet | Typischer alter Code | Lösung |
|---|---|---|
| Implizit nullable Parameter | `function f( eZContentObject $o = null )` | `?eZContentObject $o = null`; siehe [Nullable-Parameter und Typdeklarationen](nullable-parameter-und-typen.md) |
| Die Konstante `E_STRICT` | `error_reporting( E_ALL \| E_STRICT )` | `error_reporting( E_ALL )` (der Kernel verwendet sie seit 6.0.8 nicht mehr) |
| `trigger_error()` mit `E_USER_ERROR` | ein Skript mit einer Fehlermeldung beenden | eine Ausnahme werfen oder den Fehler schreiben und `exit` |

Der implizit nullable Parameter ist mit Abstand der häufigste Fall; se7enxweb/expquery_translator, Commit 8ddf68d,
hat dafür 35 Parameter in 27 Dateien geändert.

## PHP 8.5

| Veraltet | Typischer alter Code | Lösung, die auch auf 8.0 läuft |
|---|---|---|
| `curl_close()`, `curl_share_close()` | ein cURL-Handle schließen | nichts: Seit PHP 8.0 ist das Handle ein Objekt, das von selbst freigegeben wird |
| `imagedestroy()` | ein GD-Bild freigeben | nichts, aus demselben Grund |
| `finfo_close()`, `xml_parser_free()` | eine Ressource freigeben | nichts, aus demselben Grund |
| Die Variable `$http_response_header` | Header nach `file_get_contents()` auf eine URL lesen | `http_get_last_response_headers()`, wo es existiert |
| Der Backtick-Operator | `` `ls` `` | `shell_exec( 'ls' )`, besser gar keine Shell |
| Nicht kanonische Casts | `(integer)`, `(boolean)`, `(double)` | `(int)`, `(bool)`, `(float)` |

Diese Funktionen tun seit PHP 8.0 nichts mehr, unter PHP 7 haben sie aber Speicher freigegeben. Wenn Ihre Erweiterung
irgendwo noch auf PHP 7 laufen muss, behalten Sie den Aufruf nur für diese Versionen. Aus se7enxweb/cjw_tmv, Commit
0bfa27b:

```php
// before
curl_close( $ch );

// after
if ( PHP_VERSION_ID < 80000 ) curl_close( $ch ); // no effect since PHP 8.0, deprecated in 8.5
```

Bei einer Erweiterung, die ohnehin PHP 8.0 oder neuer braucht, entfernen Sie den Aufruf einfach.

Die Response-Header, aus se7enxweb/sevenx_dse, Commit dbeacb3 (gekürzt):

```php
$responseHeaders = function_exists( 'http_get_last_response_headers' )
    ? ( http_get_last_response_headers() ?: array() )
    : ( get_defined_vars()['http_response_header'] ?? array() );
```

`get_defined_vars()` liest die alte Variable, ohne sie im Code zu nennen, daher meldet PHP 8.5 sie nicht, und die
Zeile funktioniert weiterhin auf PHP 8.0 bis 8.4.

## Eine Richtlinie für Ihre Erweiterung

1. Wählen Sie das älteste PHP, das Sie unterstützen. Für eine Erweiterung von Exponential 6 ist das 8.0, es sei denn,
   Sie haben einen Grund, mehr zu verlangen. Tragen Sie es in `composer.json` ein ([composer.json modernisieren](composer-json-modernisieren.md)).
2. Verwenden Sie keine Syntax, die neuer als diese Version ist: unter 8.0 keine Enums, kein `readonly`, kein `never`,
   keine First-Class-Callable-Syntax.
3. Rufen Sie eine Funktion, die neuer als diese Version ist, nur hinter `function_exists()` auf, mit einem Fallback.
4. Beheben Sie Deprecations jeder neueren Version, sobald sie auftreten: Zu diesem Zeitpunkt sind sie immer billig.

Der Kernel folgt denselben Regeln ([PHP-8.0-Unterstützung](../../../bc/6.0/php-8.0-unterstuetzung.md)).

## Verwandte Seiten

- Zurück: [PHP-8-Fehler, die alten Code stoppen](php-8-fehler-die-alten-code-stoppen.md)
- Weiter: [Entfernte PHP-Funktionen und ihr Ersatz](entfernte-php-funktionen.md)
- [PHP-8-Unterstützung](../../../bc/6.0/php8.md)
- Die Migrationsleitfäden des PHP-Handbuchs: [8.1](https://www.php.net/manual/en/migration81.deprecated.php),
  [8.2](https://www.php.net/manual/en/migration82.deprecated.php),
  [8.3](https://www.php.net/manual/en/migration83.deprecated.php),
  [8.4](https://www.php.net/manual/en/migration84.deprecated.php)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
