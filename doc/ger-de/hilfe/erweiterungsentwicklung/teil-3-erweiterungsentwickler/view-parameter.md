---
title: View-Parameter, ungeordnete Parameter und POST
audience: developer, app-creator
category: extension-development
order: 100
summary: Wie eine Adresse eine View erreicht - geordnete, ungeordnete und Benutzerparameter, wie eZModule::run() sie zerlegt, und das sichere Lesen von POST- und Sitzungswerten mit eZHTTPTool.
keywords: View-Parameter, ungeordnete Parameter, Benutzerparameter, Sitzung, Validierung, Typumwandlung, view parameters, params, unordered_params, user parameters, (offset), Params, UserParameters, eZHTTPTool, postVariable, hasPostVariable, getVariable, session, validation, cast
type: help
---
# View-Parameter, ungeordnete Parameter und POST

Teil 3, Kapitel 10. Eine View erhält ihre Eingaben aus drei Quellen: der **Adresse**, dem **POST-Rumpf** eines
Formulars und der **Sitzung**. Dieses Kapitel zeigt, wie jede davon ankommt, so wie `eZModule::run()` in
`lib/ezutils/classes/ezmodule.php` sie zerlegt, und wie Sie sie sicher lesen.

## Geordnete Parameter

```php
$ViewList['world'] = array( 'script' => 'world.php', 'params' => array( 'Name', 'Mood' ) );
```

`/hello/world/Ada/happy` ergibt `$Params['Name'] = 'Ada'` und `$Params['Mood'] = 'happy'`. Ein fehlender Parameter
ist nicht gesetzt; lesen Sie ihn mit `isset()`.

## Ungeordnete Parameter

```php
$ViewList['list'] = array( 'script' => 'list.php',
                           'params' => array(),
                           'unordered_params' => array( 'offset' => 'Offset', 'sort' => 'SortBy' ) );
```

Die URL nennt sie in beliebiger Reihenfolge nach den geordneten Parametern. Beide Schreibweisen funktionieren:

| Adresse | `$Params` |
|---|---|
| `/hello/list/offset/20` | `Offset = '20'`, `SortBy = false` |
| `/hello/list/(offset)/20/(sort)/name` | `Offset = '20'`, `SortBy = 'name'` |

Die erste Form wird mit den Pfadelementen abgeglichen; die zweite, in Klammern, ist ein **Benutzerparameter**, den der
Kernel auf den gleichnamigen ungeordneten Parameter abbildet. Ein Name am Ende der Adresse ohne Wert ergibt `false`,
„als wäre er nicht angegeben“.

## Benutzerparameter

Jedes Paar `/(name)/value` der Adresse wird außerdem in `$Params['UserParameters']` gesammelt, ob die View es
deklariert oder nicht. Inhalts-Views geben sie als `$view_parameters` an Templates weiter; so blättert `/(offset)/10`
durch einen Ordner. Der eZpedia-Artikel
["How to add view parameter support to your custom module view"](https://ezpedia.exponential.earth/en/solution/how_to_add_view_parameter_support_to_your_custom_module_view)
beschreibt denselben Mechanismus für ältere Versionen.

```php
<?php
$userParameters = isset( $Params['UserParameters'] ) ? $Params['UserParameters'] : array();
$offset = isset( $userParameters['offset'] ) ? (int)$userParameters['offset'] : 0;
$tpl = eZTemplate::factory();
$tpl->setVariable( 'view_parameters', $userParameters );
```

## Alles aus der Adresse ist nicht vertrauenswürdig

Behandeln Sie jeden Parameter wie Text, den ein Fremder eingetippt hat:

| Wert | Vorgehen |
|---|---|
| eine ID | `(int)$Params['ObjectID']`, dann abrufen und prüfen, ob sie existiert |
| ein Offset oder Limit | `(int)` und begrenzen: `max( 0, min( $offset, 10000 ) )` |
| ein Wort aus einer festen Liste | mit der Liste vergleichen: `in_array( $sort, array( 'name', 'published' ), true )` |
| freier Text, der auf einer Seite erscheint | im Template maskieren: `{$name\|wash}` |
| freier Text in SQL | nie direkt; verwenden Sie `eZPersistentObject` oder `$db->escapeString()` |

## POST-Variablen

Verwenden Sie `eZHTTPTool` (`lib/ezutils/classes/ezhttptool.php`):

```php
<?php
$http = eZHTTPTool::instance();

if ( $http->hasPostVariable( 'Comment' ) )
{
    $comment = trim( (string)$http->postVariable( 'Comment' ) );
}
$page = (int)$http->postVariable( 'Page', 1 );      // with a fallback value
$fromQuery = $http->getVariable( 'q', '' );           // ?q=... of the address
```

`postVariable( $var, $fallbackValue = null )` gibt den Ersatzwert zurück, wenn die Variable fehlt.

Ziehen Sie die Aktionen des Moduls (`single_post_actions`, `isCurrentAction()`, siehe [Module und Views](module-und-views.md))
dem Prüfen von Schaltflächennamen von Hand vor: Sie halten die View lesbar.

## Die Sitzung

```php
<?php
$http = eZHTTPTool::instance();
$http->setSessionVariable( 'MysiteHelloLast', $name );
$last = $http->hasSessionVariable( 'MysiteHelloLast' ) ? $http->sessionVariable( 'MysiteHelloLast' ) : null;
$http->removeSessionVariable( 'MysiteHelloLast' );
```

Stellen Sie Sitzungsschlüsseln den Namen Ihrer Erweiterung voran. Legen Sie keine großen Daten oder Objekte in der
Sitzung ab.

## Nach einem POST weiterleiten

Eine View, die etwas geändert hat, endet mit einer Weiterleitung (`$Module->redirectToView()` oder `redirectTo()`),
damit ein Neuladen der Seite das Formular nicht erneut sendet.

## Verwandte Seiten

- Zurück: [Module und Views](module-und-views.md)
- Weiter: [Richtlinienfunktionen und Zugriffsprüfungen](richtlinienfunktionen-und-zugriff.md)
- [eZHTTPTool: Requests, Sitzungen und Weiterleitungen](../teil-8-referenz/ezhttptool.md)
- [Sicherheit in Erweiterungen](sicherheit-in-erweiterungen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
