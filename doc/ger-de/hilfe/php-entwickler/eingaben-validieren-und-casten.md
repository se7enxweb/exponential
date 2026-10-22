---
title: Eingaben validieren und casten
audience: php-developer, developer
category: php-development
order: 360
summary: Wie PHP-Code in Exponential aus nicht vertrauenswürdigen Eingaben sichere Werte macht - Lesen über eZHTTPTool und $Params, Casts mit (int) und (float) samt Bereichen, Allow-Listen mit striktem in_array, Regeln für Stringlänge und Zeichen, filter_var für E-Mail-Adressen und URLs, Dateien und das Melden von Validierungsfehlern an Benutzer.
keywords: Eingabevalidierung, Casten, Allow-Liste, Validierungsfehler, nicht vertrauenswürdige Eingabe, input validation, casting, int cast, allow list, in_array strict, eZHTTPTool, postVariable, getVariable, hasPostVariable, Params, unordered parameters, filter_var, validation errors, eZInputValidator, untrusted input, null handling
type: help
---
# Eingaben validieren und casten

Diese Seite richtet sich an PHP-Entwickler, die Werte aus Anfragen, Einstellungen, Importen oder der Datenbank
übernehmen. Der eigene Weg des Kernels ist in jeder View sichtbar: über die Hilfsmittel des Kernels lesen, den Wert in
den Typ umwandeln, den er haben muss, ihn gegen das Erlaubte prüfen und ihn erst dann verwenden. Da der Code-Stil keine
Typdeklarationen kennt, sind es diese Casts, die einem Wert seinen Typ geben
([Warum der Code keine Typdeklarationen hat](warum-keine-typdeklarationen.md)).

## Inhalt

1. [Eingaben lesen](#eingaben-lesen)
2. [Zahlen](#zahlen)
3. [Auswahlwerte](#auswahlwerte)
4. [Text](#text)
5. [E-Mail-Adressen und URLs](#e-mail-adressen-und-urls)
6. [Arrays aus der Anfrage](#arrays-aus-der-anfrage)
7. [Fehler an den Benutzer melden](#fehler-an-den-benutzer-melden)
8. [Eine Checkliste](#eine-checkliste)

## Eingaben lesen

```php
$http = eZHTTPTool::instance();
$title = $http->hasPostVariable( 'Title' ) ? (string)$http->postVariable( 'Title' ) : '';
$page  = $http->hasGetVariable( 'page' ) ? (int)$http->getVariable( 'page' ) : 1;
$id    = isset( $Params['ItemID'] ) ? (int)$Params['ItemID'] : 0;
```

| Quelle | Lesen mit |
|---|---|
| POST | `$http->hasPostVariable()`, `$http->postVariable( $name, $fallback )` |
| GET | `$http->hasGetVariable()`, `$http->getVariable( $name, $fallback )` |
| beides | `$http->variable( $name, $fallback )` |
| View-Parameter | `$Params['Name']` (`params` und `unordered_params` aus `module.php`) |
| Formular-Schaltflächen | `$Module->isCurrentAction()`, `$Module->actionParameter()` |
| Uploads | `eZHTTPFile::canFetch()`, `eZHTTPFile::fetch()` |

Jede davon gibt Strings, Arrays oder `null` zurück. Unter Velocity bereitet der Kernel die Anfrage pro Worker-Anfrage
vor; wer `$_POST` und `$_GET` direkt liest, umgeht das.

## Zahlen

```php
$id     = (int)$Params['ItemID'];                                   // ids
$limit  = max( 1, min( 100, (int)$http->getVariable( 'limit', 20 ) ) );   // bounded
$offset = max( 0, (int)$http->getVariable( 'offset', 0 ) );
$amount = (float)str_replace( ',', '.', (string)$http->postVariable( 'Amount', '0' ) );
```

- `(int)` macht aus allem eine Ganzzahl (`'12abc'` wird 12, `'abc'` wird 0): Prüfen Sie den Bereich nach dem Cast.
- Eine ID von `0` bedeutet „nicht angegeben“: Behandeln Sie sie als nicht gefunden.
- Begrenzen Sie jedes Limit: Ein unbegrenztes `limit` ist eine Dienstverweigerung (Denial of Service).

## Auswahlwerte

```php
$sort = (string)$http->getVariable( 'sort', 'created' );
if ( !in_array( $sort, array( 'created', 'title', 'amount' ), true ) )
    $sort = 'created';
```

Übergeben Sie immer `true` als drittes Argument von `in_array()`: Ohne es ist der Vergleich lose, sodass `'1e1'` zu
`'10'` passt, `null` zu `''` und `true` zu jedem nicht leeren String. Verwenden Sie Allow-Listen für alles, was zu einem
Spaltennamen, einer Sortierrichtung, einem Template-Namen, einem Klassennamen oder einem Dateinamen wird.

## Text

```php
$title = trim( (string)$http->postVariable( 'Title', '' ) );
if ( $title === '' )
    $errors['title'] = ezpI18n::tr( 'extension/acmereport', 'Enter a title.' );
else if ( mb_strlen( $title, 'UTF-8' ) > 200 )
    $errors['title'] = ezpI18n::tr( 'extension/acmereport', 'The title can have at most 200 characters.' );
```

- Casten Sie zuerst zu String (`(string)`), damit `null` und Arrays nie String-Funktionen erreichen (eine Deprecation
  in PHP 8.1 und ein `TypeError` bei Arrays).
- Zählen Sie Zeichen mit `mb_strlen()`, nicht Bytes.
- Bewahren Sie Text so, wie der Benutzer ihn geschrieben hat; maskieren Sie bei der Ausgabe, nicht beim Speichern.
- Für Bezeichner (einen Slug, einen Code) prüfen Sie gegen ein Muster: `preg_match( '/^[a-z0-9_-]{1,64}$/', $code )`.

## E-Mail-Adressen und URLs

```php
$email = trim( (string)$http->postVariable( 'Email', '' ) );
$validEmail = filter_var( $email, FILTER_VALIDATE_EMAIL ) !== false;

$url = trim( (string)$http->postVariable( 'Website', '' ) );
$scheme = strtolower( (string)parse_url( $url, PHP_URL_SCHEME ) );
$validUrl = filter_var( $url, FILTER_VALIDATE_URL ) !== false && in_array( $scheme, array( 'http', 'https' ), true );
```

Erlauben Sie für URLs, die Sie verlinken oder abrufen, nur `http` und `https`. Weiterleitungsziele haben ihre eigenen
Regeln ([Sicherheit in PHP-Code](sicherheit-in-php-code.md#weiterleitungen)).

## Arrays aus der Anfrage

Ein Feld kann als Array ankommen (`Title[]=x`), selbst wenn Ihr Formular einen String sendet. Prüfen Sie den Typ:

```php
$ids = $http->postVariable( 'SelectedIDs', array() );
$ids = is_array( $ids ) ? array_values( array_filter( array_map( 'intval', $ids ) ) ) : array();
```

## Fehler an den Benutzer melden

Sammeln Sie Fehler pro Feld, rendern Sie das Formular mit den Werten neu, die der Benutzer eingegeben hat, und zeigen
Sie die Fehler neben den Feldern und in einer Zusammenfassung oben an. Datentypen melden stattdessen über den Validator
des Kernels: `$objectAttribute->setValidationError( ... )` und `return eZInputValidator::STATE_INVALID;`
([Einen Datentyp schreiben](einen-datentyp-schreiben.md)).

## Eine Checkliste

- [ ] Jeder Wert über `eZHTTPTool`, `$Params` oder die Aktionen des Moduls gelesen.
- [ ] Jede Zahl gecastet und begrenzt.
- [ ] Jeder Auswahlwert mit striktem Vergleich gegen eine Allow-Liste geprüft.
- [ ] Jeder String gecastet, getrimmt und in Zeichen auf seine Länge geprüft.
- [ ] Jedes Array vor der Verwendung mit `is_array()` geprüft.
- [ ] Fehler dem Benutzer angezeigt, ohne rohe Eingaben wiederzugeben.

## Verwandte Seiten

- [Eingabevalidierung und Ausgabemaskierung](../ersteller/anwendungsentwickler/eingabepruefung-und-escaping.md)
- [eZHTTPTool-Referenz](../erweiterungsentwicklung/teil-8-referenz/ezhttptool.md)
- [Härtung der Eingaben von Datentypen](../../spezifikationen/6.0/datentyp-eingabehaertung.md)
- [Freie Dokumentation](../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
