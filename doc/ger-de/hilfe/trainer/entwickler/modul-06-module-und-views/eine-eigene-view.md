---
title: "Lektion 6.2: Eine eigene View"
audience: trainer, developer
category: training
order: 62
summary: Die Teilnehmenden fügen ihrem Modul hello eine View greet hinzu, mit einem geordneten Parameter, einem Template im Design der Erweiterung und einer Brotkrumennavigation, und öffnen sie unter ihrer eigenen Adresse. 55 Minuten.
keywords: entwickler, eigene view, module.php, ViewList, script, params, Params, Result, content, path, eZTemplate, design:, erweiterungsdesign standard, hello/greet, brotkrumennavigation, developers course, custom view, breadcrumb
type: help
---
# Lektion 6.2: Eine eigene View

Eine View gibt einer Erweiterung eine Seite mit eigener Adresse: einen Bericht, ein Formular, ein kleines Werkzeug.
In dieser Lektion fügen die Teilnehmenden dem Modul `hello` aus Lektion 5.4 die View `greet` hinzu. Sie übernimmt
einen Namen aus der Adresse, rendert ein Template aus dem Design der Erweiterung und zeigt es mit einer
Brotkrumennavigation innerhalb des Pagelayouts der Website an.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 6: Module und Views](README.md) |
| Dauer | 55 Minuten (15 Präsentation, 40 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Lektion 6.1](wie-module-und-views-arbeiten.md); das Modul `hello` aus [Lektion 5.4](../modul-05-fetch-funktionen/eine-eigene-fetch-funktion.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- eine View in `$ViewList` mit `script` und `params` deklarieren;
- geordnete und ungeordnete Parameter aus `$Params` lesen;
- ein Template mit `eZTemplate::factory()` und `design:` rendern und es in `$Result['content']` zurückgeben;
- die Brotkrumennavigation mit `$Result['path']` setzen;
- das Template der View in `design/standard/templates/` der Erweiterung ablegen, damit jeder Siteaccess es findet.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: eine echte View einer Erweiterung wie `extension/nxc_powercontent/modules/content/newcontent.php` (falls
vorhanden) und [Befehle, Cronjob-Teile und Modul-Views als Klassen](../../../../spezifikationen/6.0/ausfuehrbare-befehle-cronjobs-ansichten.md).

1. **Die Deklaration** (3 Minuten). Fügen Sie `$ViewList['greet']` in `modules/hello/module.php` hinzu: `script`
   nennt die Datei neben `module.php`, `params` nennt die geordneten Parameter.
2. **Das Skript** (5 Minuten). Gehen Sie `greet.php` aus der Übung durch: `$Params['Name']` lesen, in PHP nichts
   escapen (das Template escapt), Template-Variablen setzen, das Template holen, `$Result` füllen.
3. **Wo das Template liegt** (3 Minuten). `extension/mysite_hello/design/standard/templates/hello/greet.tpl`.
   `standard` gehört zur Kette jedes Siteaccess, daher funktioniert die View auf der Website und in der
   Administration. Das Design der Website kann sie trotzdem überschreiben.
4. **Klasse oder Skript** (2 Minuten). Die eigenen Views des Kernels sind Klassen, die
   `Exponential\Runnable\ModuleView` erweitern; für eine Erweiterung genügt ein einfaches Skript, und damit lässt
   es sich leichter beginnen.
5. **Zugriff** (2 Minuten). Ohne `functions` braucht ein Benutzer eine Richtlinie für das ganze Modul (`hello`,
   alle Funktionen). Administratoren haben alle Richtlinien, Besucher keine: Melden Sie sich für diese Lektion als
   Administrator auf der Website an. Lektion 6.3 fügt eine Richtlinienfunktion hinzu, damit eine Rolle nur diese
   View freigeben kann.

### Häufige Fragen

**Warum erscheint die Seite im Design der Website?** Das Pagelayout gibt `$module_result.content` aus; Ihre View
liefert nur ihren Teil der Seite.

**Wie gebe ich stattdessen JSON zurück?** Geben Sie das JSON aus und beenden Sie mit `eZExecution::cleanExit()`,
oder verwenden Sie die REST- oder Ajax-Mechanismen aus Modul 11. Mischen Sie nicht HTML-Pagelayout und JSON.

**Kann die View optionale Parameter annehmen?** Geordnete Parameter dürfen fehlen; prüfen Sie mit `isset()`.
Ungeordnete kommen als `/(name)/value`.

### Stolperfallen

- **Einen Parameter ohne Escapen ausgeben.** `$Params['Name']` stammt aus der Adresse; das Template muss ihn mit
  `|wash` ausgeben.
- **Template nicht gefunden.** Der Design-Ordner muss `design/standard/templates/` innerhalb einer Erweiterung sein,
  deren `design.ini.append.php` `DesignExtensions[]` enthält (Lektion 3.1); leeren Sie danach den Cache
  `template-override`.
- **Modul-Cache.** Für eine neue View in einem bestehenden Modul müssen eventuell die Caches `ini` und `global_ini`
  geleert werden.

## Praktische Übung

Auf der Entwicklungsinstallation, in `extension/mysite_hello`.

1. Fügen Sie die View in `modules/hello/module.php` hinzu:

   ```php
   <?php
   $Module = array( 'name' => 'hello' );
   $ViewList = array();
   $ViewList['greet'] = array(
       'script' => 'greet.php',
       'params' => array( 'Name' ),
       'unordered_params' => array( 'times' => 'Times' ) );
   $FunctionList = array();
   ```

2. Legen Sie `modules/hello/greet.php` an:

   ```php
   <?php
   /**
    * hello/greet: /hello/greet/<Name>/(times)/<n>
    */
   $name = isset( $Params['Name'] ) && $Params['Name'] !== '' ? $Params['Name'] : 'world';
   $times = isset( $Params['UserParameters']['times'] ) ? max( 1, min( 5, (int)$Params['UserParameters']['times'] ) ) : 1;

   $tpl = eZTemplate::factory();
   $tpl->setVariable( 'name', $name );
   $tpl->setVariable( 'times', $times );

   $Result = array();
   $Result['content'] = $tpl->fetch( 'design:hello/greet.tpl' );
   $Result['path'] = array( array( 'url' => false, 'text' => 'Hello' ) );
   ```

3. Legen Sie `design/standard/templates/hello/greet.tpl` an:

   ```
   <h1>Hello</h1>
   {for 1 to $times as $i}
       <p>Hello, {$name|wash}!</p>
   {/for}
   ```

4. Prüfen Sie die PHP-Dateien mit `php -l` und das Template mit `php bin/php/eztemplatecheck.php`.
5. Stellen Sie sicher, dass das Design der Erweiterung registriert ist (Lektion 3.1 hat
   `DesignExtensions[]=mysite_hello` registriert), und leeren Sie dann die Caches:
   `php bin/php/ezcache.php --clear-tag=ini`, `--clear-id=global_ini` und `--clear-id=template-override`.
6. Melden Sie sich als Administrator auf der Website an und öffnen Sie `/hello/greet` (mit dem Siteaccess-Teil,
   falls Ihre Website einen verwendet, zum Beispiel `/site/hello/greet`). Öffnen Sie die Seite danach abgemeldet in
   einem privaten Browserfenster.
7. Öffnen Sie `/hello/greet/Ada` und `/hello/greet/Ada/(times)/3`.
8. Entfernen Sie `|wash` kurz aus `greet.tpl` und besprechen Sie mit Ihrem Nachbarn, was ein Besucher in die Adresse
   schreiben könnte; setzen Sie `|wash` dann wieder ein. Jeder Wert, der aus der Adresse kommt, ist eine
   Benutzereingabe.
9. Committen Sie die drei Dateien.

## Erwartetes Ergebnis

- Schritt 6 zeigt „Hello“ und „Hello, world!“ innerhalb des Pagelayouts der Website, mit „Hello“ in der
  Brotkrumennavigation; abgemeldet verweigert die Website den Zugriff, weil die anonyme Rolle keine Richtlinie für
  das Modul `hello` hat.
- Schritt 7 begrüßt Ada einmal, dann dreimal.
- Schritt 8: Die Teilnehmenden können erklären, warum der Name im Template escapt werden muss.
- Der Trainer öffnet die View einer Person am Projektor und liest ihre `greet.php` mit der Gruppe.

## Quiz

1. Welcher Schlüssel eines Eintrags in `$ViewList` nennt die Datei der View?
2. Wie liest eine View den ungeordneten Parameter `/(times)/3`?
   A. `$Params['times']` B. `$Params['UserParameters']['times']` C. `$_GET['times']` D. `$Module->times`
3. Warum liegt das Template in `design/standard/templates/` der Erweiterung?
4. Wo muss der Name aus der Adresse escapt werden?
5. Was steuert `$Result['path']`?

<details>
<summary>Antworten</summary>

1. `script`.
2. **B.** `$Params['UserParameters']['times']`.
3. `standard` gehört zur Design-Kette jedes Siteaccess, daher findet die View ihr Template überall; ein Design der
   Website kann es trotzdem überschreiben.
4. Im Template, mit `|wash`, dort, wo er ausgegeben wird.
5. Die Brotkrumennavigation (den Pfad), die das Pagelayout anzeigt.

</details>

## Weiterführende Literatur

- [Modul-Override durch Erweiterungen](../../../../../eng-us/features/6.0/extension-module-override.md): Views und Templates von
  Modulen in Erweiterungen.
- [Befehle, Cronjob-Teile und Modul-Views als Klassen](../../../../spezifikationen/6.0/ausfuehrbare-befehle-cronjobs-ansichten.md):
  die Klassenform einer View.
- [RAD-Werkzeuge](../../../../../eng-us/features/6.0/rad-tools.md): Der Modul-Assistent schreibt Module mit Views.

## Verwandte Seiten

- [Modul 6: Module und Views](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
