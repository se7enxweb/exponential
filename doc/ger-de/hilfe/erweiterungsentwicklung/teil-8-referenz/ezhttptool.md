---
title: "eZHTTPTool: Requests, Sessions und Weiterleitungen"
audience: developer
category: extension-development
order: 100
summary: POST- und GET-Variablen lesen, Werte in der Session halten und mit eZHTTPTool weiterleiten - postVariable() mit Ersatzwert, sessionVariable(), redirect() - und die Modulweiterleitungen, die Views bevorzugen.
keywords: Request, Session, Weiterleitung, POST-Variablen, GET-Variablen, Ersatzwert, eZHTTPTool, instance, postVariable, hasPostVariable, getVariable, hasGetVariable, variable, setSessionVariable, sessionVariable, hasSessionVariable, removeSessionVariable, redirect, redirectTo, request
type: help
---
# eZHTTPTool: Requests, Sessions und Weiterleitungen

Teil 8, Referenz 10. Mit `eZHTTPTool` (`lib/ezutils/classes/ezhttptool.php`) lesen Kernel-Code und Erweiterungen den
Request und die Session. Verwenden Sie es statt `$_POST`, `$_GET` und `$_SESSION`: Es kümmert sich um den Start der
Session, protokolliert fehlende Variablen und hält Ihren Code testbar.

```php
<?php
$http = eZHTTPTool::instance();
```

## Request-Variablen

| Methode | Gibt zurück |
|---|---|
| `hasPostVariable( $var )`, `hasGetVariable( $var )`, `hasVariable( $var )` | ob sie gesendet wurde (`hasVariable` prüft POST, dann GET) |
| `postVariable( $var, $fallbackValue = null )` | den POST-Wert oder den Ersatzwert |
| `getVariable( $var, $fallbackValue = null )` | den GET-Wert oder den Ersatzwert |
| `variable( $var, $fallbackValue = null )` | POST, sonst GET, sonst den Ersatzwert |

Fehlt die Variable und ist der Ersatzwert `null`, schreiben diese Methoden „Undefined post variable“ in die
Debug-Ausgabe. Übergeben Sie für optionale Variablen einen Ersatzwert:

```php
<?php
$http = eZHTTPTool::instance();
$name = trim( (string)$http->postVariable( 'Name', '' ) );
$page = max( 1, (int)$http->getVariable( 'page', 1 ) );
$ids = array_map( 'intval', (array)$http->postVariable( 'SelectedIDArray', array() ) );
if ( $http->hasPostVariable( 'StoreButton' ) )
{
    // the form's Store button was pressed
}
```

Jeder Wert aus dem Request ist nicht vertrauenswürdig: Wandeln Sie Zahlen um, prüfen Sie Zeichenketten und maskieren
Sie bei der Ausgabe ([Sicherheit in Erweiterungen](../teil-3-erweiterungsentwickler/sicherheit-in-erweiterungen.md)). In
Modul-Views prüft der Kernel bei POST außerdem das Formular-Token ([ezformtoken und CSRF](ezformtoken-und-csrf.md)).

## Session

| Methode | Verwendung |
|---|---|
| `setSessionVariable( $name, $value )` | einen Wert für die Session dieses Benutzers speichern |
| `sessionVariable( $name, $fallbackValue = null )` | ihn lesen |
| `hasSessionVariable( $name, $forceStart = true )` | ob er existiert |
| `removeSessionVariable( $name )` | ihn entfernen |

Versehen Sie die Namen mit dem Namen Ihrer Erweiterung als Präfix (`MyExtLastFilter`). Halten Sie Sessions klein: Sie werden
bei jedem Request dieses Benutzers gelesen.

## Weiterleitungen

Leiten Sie in einer Modul-View über das Modul weiter; der Kernel führt die Weiterleitung dann zum richtigen Zeitpunkt
aus:

```php
return $Module->redirectTo( '/myext/list' );
```

`eZModule` hat außerdem `redirectToView( $viewName, $parameters )` und `redirect( $moduleName, $viewName, $parameters )`
([eZModule und Modulergebnisse](ezmodule.md)).

Außerhalb eines Moduls (in einem Skript, einem Handler) sendet `eZHTTPTool::redirect( $path, $parameters = array(), $status = false, ... )`
die Weiterleitung sofort. Leiten Sie nie auf eine Adresse aus dem Request weiter, ohne zu prüfen, dass sie auf Ihrer
eigenen Website liegt; der Kernel beschränkt die Hosts für Weiterleitungen (`eZModule::allowedRedirectHosts()`).

## Andere Websites abrufen

`eZHTTPTool::getDataByURL( $url, $justCheckURL = false, $userAgent = false, $forceGetRequest = false )` ruft eine URL
ab. Für alles, was über ein einfaches GET hinausgeht, verwenden Sie cURL direkt, mit Timeouts.

## Verwandte Seiten

- Zurück: [eZUser und Zugriffsprüfungen](ezuser-und-zugriff.md)
- Weiter: [eZSys und eZExtension](ezsys-und-ezextension.md)
- [View-Parameter](../teil-3-erweiterungsentwickler/view-parameter.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
