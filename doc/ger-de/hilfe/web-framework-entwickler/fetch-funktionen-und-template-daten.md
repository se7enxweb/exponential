---
title: "Daten in Templates bringen: Fetch-Funktionen und Operatoren"
audience: web-framework-developer, developer
category: web-frameworks
order: 120
summary: Wo ein Framework View-Modelle aus dem Controller übergibt, können Exponential-Templates Daten auch selbst holen - Fetch-Funktionen, die in function_definition.php deklariert sind, Template-Operatoren zum Formatieren und Variablen, die die View setzt - mit den Regeln zu Berechtigungen und Performance, die beim Holen von Daten in Templates gelten.
keywords: fetch, Fetch-Funktion, fetch function, function_definition.php, call_method, Template-Operator, template operator, View-Modell, view model, View Composer, Twig-Erweiterung, Twig extension, Daten in Templates, eZFunctionHandler, eztemplateautoload, Web-Framework-Entwickler, web framework developer
type: help
---
# Daten in Templates bringen: Fetch-Funktionen und Operatoren

Diese Seite richtet sich an Entwickler, die einem Template alles, was es braucht, aus dem Controller übergeben.
Exponential unterstützt das (`$tpl->setVariable()` in Ihrer View), doch ein großer Teil seines eigenen Designs
funktioniert umgekehrt: Templates **holen** sich Daten mit `fetch( 'module', 'function', hash( ... ) )`. Deshalb kann ein
Template-Override für Inhalte Unterknoten, verwandte Objekte oder den aktuellen Benutzer ganz ohne PHP auflisten. Ihre
Erweiterung kann eigene Fetch-Funktionen und Template-Operatoren anbieten.

## Inhalt

1. [Drei Wege, auf denen Daten ein Template erreichen](#drei-wege-auf-denen-daten-ein-template-erreichen)
2. [Fetch-Funktionen](#fetch-funktionen)
3. [Eine Fetch-Funktion schreiben](#eine-fetch-funktion-schreiben)
4. [Template-Operatoren](#template-operatoren)
5. [Regeln für das Holen von Daten](#regeln-für-das-holen-von-daten)

## Drei Wege, auf denen Daten ein Template erreichen

| Weg | Entsprechung im Framework | Verwenden für |
|---|---|---|
| `$tpl->setVariable( 'items', $items )` in einer View | Variablen an `render()` übergeben | Daten Ihrer eigenen Modul-View |
| `fetch( 'expdemo', 'items', hash( 'limit', 5 ) )` im Template | View Composer, Twig-Funktionen auf Basis von Diensten, Template-Tags mit Abfragen | Daten, die ein Override oder ein gemeinsam genutztes Teil-Template überall braucht, wo es verwendet wird |
| Ein Operator: `$price\|expdemo_money` | Twig-Filter, Blade-Direktiven, Django-Template-Filter | Formatierung und kleine Berechnungen |

## Fetch-Funktionen

Die Module des Kernels bieten viele davon: `fetch( 'content', 'node', hash( 'node_id', 2 ) )`,
`fetch( 'content', 'list', hash( 'parent_node_id', 2, 'limit', 10 ) )`,
`fetch( 'user', 'current_user' )`, `fetch( 'user', 'has_access_to', hash( 'module', 'expdemo', 'function', 'manage' ) )`.
Jedes Modul deklariert sie in `function_definition.php`, das einen Fetch einer `call_method` und deren Parametern
zuordnet; `eZFunctionHandler` (`lib/ezutils/classes/ezfunctionhandler.php`) führt sie aus
([Fetch-Funktionen](../../standards/architektur/kernel-architektur.md#fetch-funktionen),
[Fetch-Funktionen: Daten in Templates lesen](../ersteller/anwendungsentwickler/fetch-funktionen.md)).

## Eine Fetch-Funktion schreiben

Aus [Eine eigene Fetch-Funktion schreiben](../ersteller/anwendungsentwickler/eine-eigene-fetch-funktion-schreiben.md):

```php
<?php
// extension/expdemo/modules/expdemo/function_definition.php
$FunctionList = array();
$FunctionList['items'] = array(
    'name' => 'items',
    'operation_types' => array( 'read' ),
    'call_method' => array( 'class' => 'expDemoFunctionCollection',
                            'method' => 'fetchItems' ),
    'parameter_type' => 'standard',
    'parameters' => array(
        array( 'name' => 'limit',  'type' => 'integer', 'required' => false, 'default' => 10 ),
        array( 'name' => 'offset', 'type' => 'integer', 'required' => false, 'default' => 0 ) ) );
```

Die Methode gibt `array( 'result' => $value )` zurück. Zurückgegebene Objekte sollten `eZPersistentObject`s sein oder
`attribute()` und `hasAttribute()` anbieten, damit Templates `$item.title` lesen können.

```text
{def $items = fetch( 'expdemo', 'items', hash( 'limit', 5 ) )}
{foreach $items as $item}<li>{$item.title|wash}</li>{/foreach}
{undef $items}
```

## Template-Operatoren

Ein Operator ist eine PHP-Klasse, die in `extension/<ext>/autoloads/eztemplateautoload.php` registriert ist und gefunden
wird, weil die Erweiterung in `site.ini [TemplateSettings] ExtensionAutoloadPath[]` aufgeführt ist
([Template-Operatoren schreiben](../ersteller/anwendungsentwickler/template-operatoren-schreiben.md),
[Funktionen oder Operatoren](../template-sprache/funktionen-und-operatoren.md)). Operatoren müssen escapen, was sie
erzeugen, oder Daten zurückgeben, die das Template mit `wash` behandelt.

## Regeln für das Holen von Daten

- **Berechtigungen werden von den Inhalts-Fetches angewendet** (`content/read` mit seinen Einschränkungen). Ihre eigenen
  Fetches müssen Berechtigungen selbst prüfen, denn ein Template kann sie von überall aufrufen.
- **Begrenzen Sie jede Abfrage.** Ein Fetch ohne `limit` auf einem großen Baum ist das häufigste Performance-Problem.
- **Denken Sie an die Caches.** Inhalts-Views werden gecacht; Daten aus Ihren eigenen Tabellen lassen sie nicht
  verfallen. Verwenden Sie einen `{cache-block}` mit `expiry` oder leeren Sie den Cache, wenn sich Ihre Daten ändern
  ([Caching](cache-ebenen.md)).
- **Halten Sie die Methode schlank.** Rufen Sie dieselbe Dienstklasse auf, die Ihre Views und Befehle verwenden, und
  testen Sie sie mit PHPUnit.

## Verwandte Seiten

- [Die View-Schicht](templates-im-vergleich-zu-twig-und-blade.md)
- [Muster: Fetch-Funktion](../../standards/muster/fetch-funktion.md)
- [Muster: Template-Operator](../../standards/muster/template-operator.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
