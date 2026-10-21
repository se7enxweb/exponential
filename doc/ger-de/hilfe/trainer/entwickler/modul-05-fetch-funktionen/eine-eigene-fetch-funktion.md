---
title: "Lektion 5.4: Eine eigene Fetch-Funktion"
audience: trainer, developer
category: training
order: 54
summary: Die Teilnehmenden fügen ihrer Erweiterung ein Modul hinzu, beschreiben eine Fetch-Funktion in function_definition.php, schreiben die Funktionssammlungsklasse, die das Ergebnis zurückgibt, registrieren das Modul und rufen den neuen Fetch aus einem Template auf. 60 Minuten.
keywords: entwickler, fetch-funktion, eigene fetch-funktion, function_definition.php, FunctionList, call_method, parameters, funktionssammlung, result, module.php, ExtensionRepositories, ModuleList, module.ini, subTreeByNodeID, autoloads, developers course, fetch function, function collection
type: help
---
# Lektion 5.4: Eine eigene Fetch-Funktion

Wenn ein Template Daten braucht, die kein Kernel-Fetch anbietet, oder wenn derselbe komplizierte Fetch in vielen
Templates vorkommt, schreiben Sie eine eigene Fetch-Funktion. Sie braucht drei kleine Dateien: ein Modul, eine
Beschreibung der Funktion und eine PHP-Klasse, die die Daten zurückgibt. In dieser Lektion schreiben die
Teilnehmenden `fetch( 'hello', 'latest', ... )`, das die neuesten Knoten einer Klasse zurückgibt.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 5: Fetch-Funktionen](README.md) |
| Dauer | 60 Minuten (20 Präsentation, 40 Übung) |
| Niveau | Experten |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Lektion 5.3](fetches-anderer-module.md); `mysite_hello` ist aktiv (Lektion 3.1) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- eine minimale `module.php` schreiben, damit ein Modul existiert;
- eine Fetch-Funktion in `function_definition.php` mit `call_method` und `parameters` beschreiben;
- eine Funktionssammlungsklasse schreiben, deren Methode `array( 'result' => ... )` zurückgibt;
- das Modul mit `ExtensionRepositories[]` und `ModuleList[]` in `module.ini` registrieren;
- die Autoloads neu erzeugen und den Fetch aus einem Template aufrufen.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: `kernel/content/function_definition.php` und eine echte Erweiterung, zum Beispiel
`extension/eztags/modules/tags/function_definition.php`.

1. **Drei Dateien** (4 Minuten). `modules/hello/module.php` (das Modul existiert), `modules/hello/function_definition.php`
   (wie der Fetch heißt und welche Parameter er annimmt) und eine Klasse in `classes/` (was er tut).
2. **Die Beschreibung** (5 Minuten). Gehen Sie den Block `$FunctionList['latest']` der Übung durch: `name`,
   `operation_types`, `call_method` mit `class` und `method`, `parameter_type => 'standard'` und jeden Parameter mit
   `name`, `type`, `required` und `default`. Die Parameter werden der Methode in der aufgeführten Reihenfolge
   übergeben.
3. **Die Klasse** (4 Minuten). Die Methode gibt `array( 'result' => $value )` zurück; das Template erhält `$value`.
   Ein Fehler wird als `array( 'error' => ... )` zurückgegeben, wie es die Sammlungen des Kernels tun.
4. **Registrierung** (4 Minuten). `settings/module.ini.append.php` der Erweiterung:
   `[ModuleSettings] ExtensionRepositories[]=mysite_hello` und `ModuleList[]=hello`. Danach die Autoloads.
5. **Wie man in PHP Knoten sucht** (3 Minuten). `eZContentObjectTreeNode::subTreeByNodeID( $params, $nodeID )`
   nimmt `ClassFilterType`, `ClassFilterArray`, `SortBy`, `Limit`, `Offset` und `Depth` an, wie der Template-Fetch.

### Häufige Fragen

**Darf eine Fetch-Funktion Daten ändern?** Sie sollte es nicht. Fetches dienen dem Lesen; Änderungen gehören in eine
Modul-View mit einem Formular und einer Berechtigungsprüfung (Modul 6).

**Prüft der Fetch Berechtigungen?** `subTreeByNodeID` beachtet standardmäßig die Leserechte des aktuellen Benutzers,
wie die Inhalts-Fetches.

**Kann ich die RAD-Werkzeuge verwenden?** Ja: **Setup** > **RAD**; der Assistent für Template-Erweiterungen
(`/setup/templateoperator`) schreibt Template-Operatoren und Fetch-Funktionen. Wer zuerst eine von Hand geschrieben
hat, kann den erzeugten Code lesen.

### Stolperfallen

- **`'result'` vergessen.** Wer das Array der Knoten direkt zurückgibt, liefert dem Template nichts Brauchbares.
- **Modul nicht registriert.** Ohne `ModuleList[]` und `ExtensionRepositories[]` schlägt der Fetch mit einer
  Fehlermeldung über ein unbekanntes Modul in der Debug-Ausgabe fehl.
- **Klasse nicht gefunden.** Führen Sie nach dem Hinzufügen der Klasse `php bin/php/ezpgenerateautoloads.php -e` aus.

## Praktische Übung

Auf der Entwicklungsinstallation, in `extension/mysite_hello`.

1. Legen Sie `modules/hello/module.php` an:

   ```php
   <?php
   $Module = array( 'name' => 'hello' );
   $ViewList = array();
   $FunctionList = array();
   ```

2. Legen Sie `modules/hello/function_definition.php` an:

   ```php
   <?php
   $FunctionList = array();
   $FunctionList['latest'] = array(
       'name' => 'latest',
       'operation_types' => array( 'read' ),
       'call_method' => array( 'class' => 'mysiteHelloFunctionCollection',
                               'method' => 'fetchLatest' ),
       'parameter_type' => 'standard',
       'parameters' => array( array( 'name' => 'class_identifier',
                                     'type' => 'string',
                                     'required' => true ),
                              array( 'name' => 'limit',
                                     'type' => 'integer',
                                     'required' => false,
                                     'default' => 5 ) ) );
   ```

3. Legen Sie `classes/mysitehellofunctioncollection.php` an:

   ```php
   <?php
   /**
    * Fetch functions of the hello module: {fetch( 'hello', 'latest', hash( 'class_identifier', 'article' ) )}
    */
   class mysiteHelloFunctionCollection
   {
       /**
        * The newest published nodes of one class, newest first.
        *
        * @param string $classIdentifier
        * @param int $limit
        * @return array array( 'result' => eZContentObjectTreeNode[] )
        */
       public static function fetchLatest( $classIdentifier, $limit )
       {
           $nodes = eZContentObjectTreeNode::subTreeByNodeID(
               array( 'ClassFilterType'  => 'include',
                      'ClassFilterArray' => array( $classIdentifier ),
                      'SortBy'           => array( 'published', false ),
                      'Limit'            => (int)$limit ),
               2 );
           return array( 'result' => $nodes ? $nodes : array() );
       }
   }
   ```

4. Prüfen Sie alle drei mit `php -l`.
5. Registrieren Sie das Modul:

   ```bash
   php bin/php/console exp:ini add module.ini/ModuleSettings/ExtensionRepositories mysite_hello extension:mysite_hello
   php bin/php/console exp:ini add module.ini/ModuleSettings/ModuleList hello extension:mysite_hello
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-tag=ini
   ```

6. Rufen Sie den Fetch im Spielplatz auf:
   `{foreach fetch( 'hello', 'latest', hash( 'class_identifier', 'folder', 'limit', 3 ) ) as $n}{$n.name|wash}; {/foreach}`.
7. Rufen Sie ihn mit `'note'` und mit einer Klasse auf, die es nicht gibt; vergleichen Sie.
8. Lassen Sie `class_identifier` weg und führen Sie ihn mit `--debug=warning,error` aus; lesen Sie die Meldung über den
   erforderlichen Parameter.
9. Committen Sie die vier Dateien.

## Erwartetes Ergebnis

- Schritt 6 gibt bis zu drei Ordnernamen aus, die neuesten zuerst.
- Schritt 7: die Notizen aus Lektion 4.1 und nichts für die unbekannte Klasse.
- Schritt 8: Die Debug-Ausgabe zeigt den Fehler "Missing parameter 'class_identifier' for function 'latest' in module
  'hello'".
- Der Trainer prüft die `module.ini.append.php` einer Person und die Ausgabe des Spielplatzes.

## Quiz

1. Aus welchen drei Dateien besteht eine Fetch-Funktion?
2. Was muss die Methode einer Funktionssammlung zurückgeben?
   A. Den Wert selbst B. `array( 'result' => $value )` C. `true` D. Ein Template
3. In welcher Reihenfolge werden die Parameter der Methode übergeben?
4. Welche zwei Einstellungen registrieren ein Erweiterungsmodul?
5. Warum sollte eine Fetch-Funktion keine Daten ändern?

<details>
<summary>Antworten</summary>

1. `modules/<module>/module.php`, `modules/<module>/function_definition.php` und die Klasse in `classes/`.
2. **B.** `array( 'result' => $value )`.
3. In der Reihenfolge, in der die Parameter in `function_definition.php` aufgeführt sind.
4. `module.ini [ModuleSettings] ExtensionRepositories[]` und `ModuleList[]`.
5. Fetches laufen, während Seiten gezeichnet werden, oft aus Caches und ohne Formulare oder Berechtigungsprüfungen
   für Änderungen; Änderungen gehören in Modul-Views.

</details>

## Weiterführende Literatur

- [RAD-Werkzeuge](../../../../../eng-us/features/6.0/rad-tools.md): der Assistent für Template-Erweiterungen.
- [Erweiterungspunkte](../../../../bc/6.0/rad-erweiterungspunkte.md): Fetch-Funktionen unter den Erweiterungspunkten
  für Templates.
- [Erweiterungsentwicklung: das Buch](../../../erweiterungsentwicklung/README.md): Teil 3 behandelt Fetch-Funktionen
  ausführlich.

## Verwandte Seiten

- [Modul 5: Fetch-Funktionen](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
