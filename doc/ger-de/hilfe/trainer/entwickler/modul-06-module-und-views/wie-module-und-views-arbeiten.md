---
title: "Lektion 6.1: Wie Module und Views funktionieren"
audience: trainer, developer
category: training
order: 61
summary: Die Teilnehmenden lesen eine module.php, ordnen einer Adresse ihr Modul, ihre View sowie geordnete und ungeordnete Parameter zu, verfolgen eine View bis zu ihrem Skript oder ihrer Klasse und sehen, wie das Ergebnis ins Pagelayout gelangt. 45 Minuten.
keywords: entwickler, modul, view, module.php, ViewList, params, unordered_params, script, functions, default_navigation_part, single_post_actions, Result, pagelayout, ModuleView, kernel/private/classes/views, adresse, parameter, developers course, modules and views
type: help
---
# Lektion 6.1: Wie Module und Views funktionieren

Jede Seite, die keine Inhaltsseite ist, ist eine Modul-View: `/user/login`, `/setup/cache`, `/content/edit/12`.
Selbst Inhaltsseiten sind die View `content/view`. Diese Lektion erklärt, wie aus einer Adresse ein Aufruf einer View
wird, was eine `module.php` deklariert und wie eine View ihr HTML an das Pagelayout übergibt. Sie bereitet Lektion 6.2
vor, in der die Teilnehmenden eine eigene View schreiben.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 6: Module und Views](README.md) |
| Dauer | 45 Minuten (25 Präsentation, 20 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Lektion 1.1](../modul-01-die-entwicklungsinstallation/die-landkarte-fuer-entwickler.md) und [Modul 5](../modul-05-fetch-funktionen/README.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- eine Adresse in Siteaccess, Modul, View, geordnete Parameter und ungeordnete Parameter zerlegen;
- Einträge von `$ViewList` in einer `module.php` lesen: `script`, `params`, `unordered_params`, `functions`,
  `single_post_actions`;
- den Code einer Kernel-View in `kernel/private/classes/views/<module>/<view>.php` finden;
- das Array `$Result` beschreiben, das eine View zurückgibt, und wie das Pagelayout `$module_result.content` anzeigt.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Den Code lesen, den Sie bereits haben, Technik 2](../../../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/den-vorhandenen-code-lesen.md#technik-2-von-einer-adresse-zum-code)
und [Befehle, Cronjob-Teile und Modul-Views als Klassen](../../../../spezifikationen/6.0/ausfuehrbare-befehle-cronjobs-ansichten.md).

1. **Eine Adresse** (4 Minuten). `/admin/content/view/full/2/(offset)/20`: Siteaccess `admin`, Modul `content`,
   View `view`, geordnete Parameter `full` und `2` (in `module.php` mit den Namen `ViewMode` und `NodeID`),
   ungeordneter Parameter `offset` mit dem Wert `20`.
2. **`module.php`** (6 Minuten). Zeigen Sie `$ViewList['view']` in `kernel/content/module.php`:

   ```php
   $ViewList['view'] = array(
       'functions' => array( 'read' ),
       'default_navigation_part' => 'ezcontentnavigationpart',
       'script' => 'view.php',
       'params' => array( 'ViewMode', 'NodeID' ),
       'unordered_params' => array( 'language' => 'Language',
                                    'offset' => 'Offset', ... ) );
   ```

   `functions` nennt die Richtlinie, die ein Benutzer braucht (`content/read`); `single_post_actions` ordnet einer
   gedrückten Schaltfläche einen Aktionsnamen zu. `$FunctionList` am Ende listet die Richtlinienfunktionen und ihre
   Einschränkungen auf.
3. **Skript oder Klasse** (4 Minuten). `kernel/section/view.php` besteht aus einer Zeile: `return
   \Exponential\View\Kernel\Section\View::main( __FILE__, get_defined_vars() );`. Der Code ist nach
   `kernel/private/classes/views/section/view.php` umgezogen. Views von Erweiterungen können einfache Skripte oder
   Klassen sein; beides funktioniert.
4. **`$Result`** (4 Minuten). Eine View setzt `$Result['content']` (das HTML, meist aus einem Template) und
   `$Result['path']` (die Brotkrumennavigation). Das Pagelayout gibt den Inhalt mit `{$module_result.content}` aus.
5. **Navigationsteile** (2 Minuten). `default_navigation_part` entscheidet, welcher Reiter der Administration
   hervorgehoben wird.

### Häufige Fragen

**Woher kommen die `$Params`?** Der Modul-Runner füllt `$Params` mit den benannten geordneten Parametern und
`$Params['UserParameters']` mit den ungeordneten.

**Kann eine Erweiterung einem Kernel-Modul eine View hinzufügen?** Ja, mit Bedacht: [Modul-Override durch Erweiterungen](../../../../../eng-us/features/6.0/extension-module-override.md)
erklärt, wie eine Erweiterung Views und Templates eines Moduls bereitstellt oder überschreibt.

**Warum wurden die Kernel-Views zu Klassen?** Damit man sie über Einstellungen und Ereignisse ersetzen und auf sie
reagieren kann, ohne Kernel-Dateien zu bearbeiten (siehe die Spezifikation oben).

### Stolperfallen

- **Logik in der einzeiligen Einstiegsdatei suchen.** Folgen Sie dem Klassennamen nach `kernel/private/classes/views/`.
- **Modulname und Ordner verwechseln.** Der Ordnername unter `modules/` ist der Modulname in der Adresse.
- **Den Siteaccess-Teil vergessen.** Auf einer Website, die über den Pfad erkannt wird, ist der erste Teil der Adresse
  der Siteaccess, nicht das Modul.

## Praktische Übung

Auf der Entwicklungsinstallation, in einer Shell.

1. Zeigen Sie den Eintrag `view` an: `grep -n "ViewList\['view'\]" -A10 kernel/content/module.php`.
2. Suchen Sie den Eintrag für `/user/login`: `grep -n "ViewList\['login'\]" -A10 kernel/user/module.php`. Notieren
   Sie sein Skript, seine Parameter und ob er `functions` nennt.
3. Öffnen Sie die Einstiegsdatei `kernel/user/login.php` und verfolgen Sie die aufgerufene Klasse bis zu ihrer Datei
   unterhalb von `kernel/private/classes/views/`.
4. Zählen Sie die View-Klassen des Kernels: `find kernel/private/classes/views -name "*.php" | wc -l`.
5. Listen Sie die Richtlinienfunktionen des Moduls section auf: `grep -n "FunctionList\[" kernel/section/module.php`.
6. Öffnen Sie im Browser `/content/view/full/2/(offset)/10` auf Ihrer Website und in der Administration; achten Sie
   darauf, wie sich der Siteaccess-Teil unterscheidet.
7. Finden Sie heraus, wo das Standard-Design das Ergebnis ausgibt (`pagelayout.tpl` bindet `page_mainarea.tpl` ein):
   `grep -rn "module_result.content" design/standard/templates/page_mainarea.tpl`.
8. Schreiben Sie die fünf Teile der Adresse `/admin/content/view/full/2/(offset)/20` auf Papier.

## Erwartetes Ergebnis

- Schritt 2: die View `login` mit ihrem Skript und ihren Post-Aktionen.
- Schritt 3: Die Teilnehmenden nennen die Klassendatei, zum Beispiel `kernel/private/classes/views/user/login.php`.
- Schritt 7: die Zeile, in der das Pagelayout das Ergebnis der View ausgibt.
- Schritt 8: Siteaccess `admin`, Modul `content`, View `view`, Parameter `full` und `2`, ungeordnet `offset=20`.
- Der Trainer bespricht Schritt 8 mit der ganzen Gruppe.

## Quiz

1. Welcher Teil von `/content/view/full/2` ist die View?
2. Welcher Schlüssel von `$ViewList` nennt die Richtlinie, die ein Benutzer für die View braucht?
   A. `script` B. `params` C. `functions` D. `default_navigation_part`
3. Was wird innerhalb einer View aus `/(offset)/20`?
4. Welcher Schlüssel von `$Result` enthält das HTML der View?
5. Wo liegt der Code der Kernel-View `section/view` seit Exponential 6?

<details>
<summary>Antworten</summary>

1. `view` (das Modul ist `content`).
2. **C.** `functions`, zum Beispiel `array( 'read' )`.
3. Ein ungeordneter Parameter: `$Params['UserParameters']['offset']` und `$view_parameters.offset` in den Templates
   von Inhalts-Views.
4. `$Result['content']`.
5. In `kernel/private/classes/views/section/view.php`, Klasse `Exponential\View\Kernel\Section\View`.

</details>

## Weiterführende Literatur

- [Den Code lesen, den Sie bereits haben](../../../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/den-vorhandenen-code-lesen.md).
- [Befehle, Cronjob-Teile und Modul-Views als Klassen](../../../../spezifikationen/6.0/ausfuehrbare-befehle-cronjobs-ansichten.md).
- [Modul-Override durch Erweiterungen](../../../../../eng-us/features/6.0/extension-module-override.md).

## Verwandte Seiten

- [Modul 6: Module und Views](README.md)
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
