---
title: Fehlercodes und Modulergebnisse
audience: developer
category: extension-development
order: 330
summary: Die Fehlercodes des Kernels, die eine View mit handleError() zurückgibt - Zugriff verweigert, nicht gefunden, nicht verfügbar, verschoben, Formular-Token abgelehnt -, ihr HTTP-Status in error.ini und die Fehler-Templates.
keywords: Fehlercodes, Fehlerseite, Zugriff verweigert, nicht gefunden, HTTP-Status, Fehler-Templates, eZError, handleError, KERNEL_ACCESS_DENIED, KERNEL_NOT_FOUND, KERNEL_NOT_AVAILABLE, KERNEL_MOVED, KERNEL_FORM_TOKEN_REFUSED, error.ini, HTTPError, error templates, 404, 403, 401
type: help
---
# Fehlercodes und Modulergebnisse

Teil 8, Referenz 29. Eine View, die ihre Aufgabe nicht erfüllen kann, gibt statt eines Ergebnisses einen Fehler
zurück: `return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );`. Der Kernel zeigt dann die
Fehlerseite für diesen Code mit dem richtigen HTTP-Status an und fordert den Besucher (bei verweigertem Zugriff)
gegebenenfalls zur Anmeldung auf. Diese Seite führt die Codes auf und beschreibt, wie sie behandelt werden.

## Die Kernel-Codes (eZError, kernel/classes/ezerror.php)

| Konstante | Code | Verwenden, wenn | HTTP-Status (error.ini) |
|---|---|---|---|
| `KERNEL_ACCESS_DENIED` | 1 | der Benutzer dies nicht tun darf | 401; die Standardeinstellungen zeigen an seiner Stelle die Anmelde-View (`ErrorHandler[1]=embed`, `EmbedURL[1]=/user/login`) |
| `KERNEL_NOT_FOUND` | 2 | das Angefragte nicht existiert | 404 |
| `KERNEL_NOT_AVAILABLE` | 3 | es existiert, kann aber jetzt nicht angezeigt werden (falsche ID, fehlender Parameter, unveröffentlicht) | 404 |
| `KERNEL_MOVED` | 4 | es verschoben wurde | 301 |
| `KERNEL_LANGUAGE_NOT_FOUND` | 5 | die angefragte Übersetzung nicht existiert | |
| `KERNEL_FORM_TOKEN_REFUSED` | 6 | ein POST ohne gültiges Formular-Token kommt (der Kernel verwendet ihn) | 403 |
| `KERNEL_MODULE_NOT_FOUND` | 20 | es kein solches Modul gibt | 404 |
| `KERNEL_MODULE_VIEW_NOT_FOUND` | 21 | es keine solche View gibt | 404 |
| `KERNEL_MODULE_DISABLED` | 22 | das Modul abgeschaltet ist | 404 |
| `KERNEL_NO_DB_CONNECTION` | 50 | die Datenbank nicht erreichbar ist | |

Daneben gibt es Shop-Codes (`SHOP_*`) für die eigenen Fehler des Shop-Moduls.

## In einer View

```php
<?php
$Module = $Params['Module'];
$itemID = (int)$Params['ItemID'];
$item = myExtItem::fetch( $itemID );
if ( !$item instanceof myExtItem )
{
    return $Module->handleError( eZError::KERNEL_NOT_AVAILABLE, 'kernel' );
}
$access = eZUser::currentUser()->hasAccessTo( 'myext', 'manage' );
if ( $access['accessWord'] !== 'yes' )
{
    return $Module->handleError( eZError::KERNEL_ACCESS_DENIED, 'kernel' );
}
```

Geben Sie den Aufruf mit `return` zurück: Die View muss dort enden.

## Wie der Kernel die einzelnen Codes behandelt

`settings/error.ini [ErrorSettings-kernel]` entscheidet je Code:

| Einstellung | Bedeutung |
|---|---|
| `ErrorHandler[<code>]` | `displayerror` (das Fehler-Template anzeigen, der Standard), `redirect`, `rerun` oder `embed` (eine andere View anzeigen, etwa die Anmeldung) |
| `RedirectURL[<code>]`, `EmbedURL[<code>]` | wohin |
| `HTTPError[<code>]` | der gesendete HTTP-Status |

Die Fehlerseiten sind die Templates `error/kernel/<code>.tpl` in der Design-Kette; ein Theme kann sie überschreiben.

## Fehler, die keine Views sind

- **JSON-Antworten** (eine Ajax-View): Setzen Sie den HTTP-Status selbst und antworten Sie mit einem JSON-Body; geben
  Sie einem Skript keine HTML-Fehlerseite zurück.
- **Kommandozeilenskripte**: Schreiben Sie den Fehler mit `$cli->error()` und beenden Sie mit einem Exit-Code
  ungleich null ([eZScript und eZCLI](ezscript-und-ezcli.md)).
- **Fatale PHP-Fehler** enden in der allgemeinen Fehlerseite des Kernels; die Einzelheiten gehen ins Log, nie an den
  Besucher.

## Verwandte Seiten

- Zurück: [Verzeichnisse unter var/](verzeichnisse-unter-var.md)
- Weiter: [Die JavaScript-API von expui](expui-javascript-api.md)
- [eZModule und Modulergebnisse](ezmodule.md)
- [Fehler-Templates](../../../../eng-us/help/designers/templates/error-templates.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
