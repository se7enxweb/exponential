---
title: Formulare, CSRF und HTTP-Eingaben
audience: app-creator, developer
category: app-creation
order: 140
summary: Formulare in Ansichten sicher verarbeiten - Eingaben mit eZHTTPTool und Modulaktionen lesen, Formular-Tokens aus ezformtoken, POST-Redirect-GET, Validierungsmeldungen, sichere Weiterleitungen, Uploads mit eZHTTPFile und JSON-POSTs aus Skripten.
keywords: Formulare, CSRF, Formular-Token, Eingabe, Validierung, Upload, sichere Weiterleitung, Anwendungsentwickler, forms, form token, ezformtoken, eZHTTPTool, postVariable, hasPostVariable, POST redirect GET, validation, eZHTTPFile, upload, safe redirect, app creator
type: help
---
# Formulare, CSRF und HTTP-Eingaben

Diese Seite richtet sich an Anwendungsentwickler, deren Ansichten Eingaben entgegennehmen. Drei Regeln: Lesen Sie
Eingaben über die Hilfsklassen des Kernels, lassen Sie jeden POST durch das Formular-Token schützen, und prüfen Sie
alles, bevor Sie es verwenden.

## Inhalt

1. [Eingaben lesen](#eingaben-lesen)
2. [Formular-Tokens](#formular-tokens)
3. [POST, Weiterleitung, GET](#post-weiterleitung-get)
4. [Validierungsfehler anzeigen](#validierungsfehler-anzeigen)
5. [Uploads](#uploads)
6. [POSTs aus Skripten](#posts-aus-skripten)

## Eingaben lesen

```php
$http = eZHTTPTool::instance();
if ( $http->hasPostVariable( 'Title' ) )
    $title = trim( (string)$http->postVariable( 'Title' ) );
$page = $http->hasGetVariable( 'page' ) ? max( 1, (int)$http->getVariable( 'page' ) ) : 1;
```

Für Schaltflächen deklarieren Sie Aktionen in `module.php` und verwenden `$Module->isCurrentAction()` und
`actionParameter()` ([Eigene Module und Ansichten](eigene-module-und-ansichten.md#formulare-verarbeiten-aktionen)). Lesen Sie
niemals `$_POST` oder `$_GET` direkt; unter Velocity bereitet der Kernel sie für jede Anfrage auf.

## Formular-Tokens

`ezformtoken` fügt jedem Formular mit `method="post"` in der Ausgabe ein verstecktes Token hinzu und weist
POST-Anfragen ohne gültiges Token ab: mit einem 403 und einer Zeile in `var/log/warning.log` (`site.ini [HTMLForms]`).
Ihre Formulare sind geschützt, solange sie von Templates gerendert werden und an dieselbe Website senden. Setzen Sie
`[HTMLForms] Secret` für jede Installation eigens. Ist ein Token abgelaufen, sehen Besucher die Seite für abgelaufene
Formulare ([Seite für abgelaufene Formulare](../../../../eng-us/features/6.0/form-expired-page.md)).

JavaScript, das mit `fetch()` sendet, muss das Token ebenfalls mitschicken; lesen Sie es aus der Seite (die Skripte
von ezjscore und der Administration tun das), statt die Prüfung abzuschalten.

## POST, Weiterleitung, GET

Leiten Sie nach einem erfolgreichen POST auf eine GET-Seite weiter, damit ein Neuladen die Aktion nicht wiederholt:

```php
if ( $Module->isCurrentAction( 'Store' ) && $errors === array() )
{
    $item->store();
    return $Module->redirectTo( '/expdemo/item/' . (int)$item->attribute( 'id' ) );
}
```

Weiterleitungen auf eine Adresse aus der Anfrage (eine `RedirectURI`) müssen auf der Website bleiben; verwenden Sie
die Prüfungen des Kernels für sichere Weiterleitungen ([Sichere Weiterleitungen](../../../../eng-us/features/6.0/safe-redirects.md)).

## Validierungsfehler anzeigen

Sammeln Sie Fehler je Feld, rendern Sie das Formular mit den vom Benutzer eingegebenen Werten erneut, und zeigen Sie
die Fehler neben den Feldern sowie in einer Zusammenfassung oben an:

```php
$tpl->setVariable( 'errors', $errors );   // array( 'title' => 'Enter a title' )
$tpl->setVariable( 'values', array( 'title' => $title ) );
```

## Uploads

```php
if ( eZHTTPFile::canFetch( 'File' ) )
{
    $file = eZHTTPFile::fetch( 'File' );
    // check $file->attribute( 'mime_type' ) and size against an allow list before storing
}
```

Für Inhalte legen Sie besser ein Objekt der Klasse `file` oder `image` an, das Speicherung, Clustering und die
Bereinigung von SVG übernimmt ([Inhalte im Code anlegen](inhalte-im-code-anlegen.md)).

## POSTs aus Skripten

Maschinelle Clients verwenden keine HTML-Formulare: Geben Sie ihnen die REST-API mit API-Schlüsseln oder OAuth oder
expservices mit Tokens, die statt Formular-Tokens ihre eigene Authentifizierung haben ([Die REST-API](rest-api.md),
[OAuth und API-Schlüssel](oauth-und-api-schluessel.md)).

## Verwandte Seiten

- [Eingabeprüfung und Escaping](eingabepruefung-und-escaping.md)
- [Sicherheit für Anwendungen](sicherheit-fuer-anwendungen.md)
- [Sicherheitsstandard](../../../../eng-us/standards/security.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
