---
title: Templates von Modulansichten
audience: developer, designer, app-creator
category: development
order: 300
summary: Wie eine Modulansicht ihre Templates rendert - die PHP-Seite (eZTemplate::factory(), setVariable(), fetch( 'design:...' )), das Array $Result, das eine Ansicht zurückgibt, und wie der Kernel es dem Pagelayout als $module_result zusammen mit den anderen Variablen des Pagelayouts übergibt - belegt durch nur lesendes Ausführen von content/view auf einer Entwicklungsinstallation.
keywords: Modulansicht, $Result, $module_result, Pagelayout, eZTemplate::factory, setVariable, fetch design, ezpKernelWeb, Ansichts-Template, module.php, Kernel, module view, pagelayout, view template, kernel
type: help
---
# Templates von Modulansichten

Jede Seite einer Website mit Exponential ist das Ergebnis einer **Modulansicht**: `content/view/full/96` führt die Ansicht `view` des Moduls
`content` mit den Parametern `full` und `96` aus. Die Ansicht ist PHP; das HTML kommt aus Templates. Diese Seite folgt einer Anfrage vom PHP
der Ansicht zu ihrem Template und weiter zum Pagelayout, mit dem Kernel-Code, der jeden Schritt ausführt.

## Inhalt

1. [Die zwei Templates einer Seite](#die-zwei-templates-einer-seite)
2. [Die PHP-Seite einer Ansicht](#die-php-seite-einer-ansicht)
3. [Was content/view zurückgibt](#was-contentview-zurückgibt)
4. [Von $Result zu $module_result](#von-result-zu-module_result)
5. [Das Pagelayout wählen](#das-pagelayout-wählen)
6. [Variablen, die ein Template einer Knotenansicht bekommt](#variablen-die-ein-template-einer-knotenansicht-bekommt)
7. [Verwandte Seiten](#verwandte-seiten)

## Die zwei Templates einer Seite

1. Die Ansicht rendert **ihr eigenes Template** (für `content/view` ist das `node/view/<view mode>.tpl` oder das passende Override) und legt das
   HTML in `$Result['content']` ab.
2. Der Kernel rendert dann das **Pagelayout** (`pagelayout.tpl` des Designs des Siteaccess), das `{$module_result.content}` in den Rahmen der
   Seite setzt: Kopf, Menüs, Fußzeile.

Das Ansichts-Template kennt den Inhalt; das Pagelayout kennt die Seite. Sie verständigen sich über `$module_result` (aus `$Result` der Ansicht)
und die [persistente Variable](die-persistente-variable-von-innen.md).

## Die PHP-Seite einer Ansicht

Die `module.php` eines Moduls gibt seine Ansichten an; zum Beispiel `kernel/section/module.php`:

```php
$ViewList['view'] = array(
    'functions' => array( 'view or assign' ),
    'script' => 'view.php',
    'ui_context' => 'view',
    'default_navigation_part' => 'ezsetupnavigationpart',
    'params' => array( 'SectionID' ),
    'unordered_params' => array( 'offset' => 'Offset' ) );
```

Das Skript der Ansicht (`kernel/section/view.php`, dessen Code in `kernel/private/classes/views/section/view.php` liegt) tut, was jede Ansicht
tut: Es holt die Template-Engine, setzt Variablen, holt ein Template über die Ressource `design:` und gibt `$Result` zurück:

```php
$tpl = \eZTemplate::factory();
$tpl->setVariable( "view_parameters", $viewParameters );
$tpl->setVariable( "section", $section );

$Result = array();
$Result['content'] = $tpl->fetch( "design:section/view.tpl" );
$Result['path'] = array( array( 'url' => 'section/list',
                                'text' => \ezpI18n::tr( 'kernel/section', 'Sections' ) ),
                         array( 'url' => false,
                                'text' => $section->attribute('name') ) );
```

- `eZTemplate::factory()` gibt die eine gemeinsame Engine der Anfrage zurück, mit allen aus den Autoload-Listen geladenen Operatoren und
  Funktionen und registrierter Ressource `design:` (`lib/eztemplate/classes/eztemplate.php`).
- `setVariable( $name, $value )` legt eine Variable in den Wurzel-Namensraum; in `section/view.tpl` ist sie `$section`.
- `fetch( 'design:section/view.tpl' )` findet die Datei über die Design-Ressource (zuerst Overrides, dann die Design-Basen; siehe
  [Template-Ressourcen und Overrides](template-ressourcen-und-overrides.md)) und gibt den gerenderten Text zurück.
- `$Result['path']` wird zur Brotkrumennavigation; jeder Eintrag hat `text` und `url` (false für die aktuelle Seite).

Eine eigene Ansicht in einer Erweiterung folgt demselben Muster; der Durchgang steht in
[Eigene Module und Ansichten](../ersteller/anwendungsentwickler/eigene-module-und-ansichten.md).

## Was content/view zurückgibt

Um ein echtes `$Result` zu sehen, führte ein nur lesendes CLI-Skript auf einer Entwicklungsinstallation die Ansicht so aus, wie der Kernel es tut, mit für diesen
Prozess im Speicher ausgeschaltetem Content-View-Cache und ausgeschalteter Template-Kompilierung (es wurde keine Cache-Datei geschrieben), und
gab die Struktur des zurückgegebenen Arrays aus:

```php
$ini = eZINI::instance();
$ini->setVariable( 'ContentSettings', 'ViewCaching', 'disabled' );   // in memory only
$module = eZModule::findModule( 'content' );
$result = $module->run( 'view', array( 'full', 96 ), false, array() );
```

Ausgabe auf einer Entwicklungsinstallation (die Listen `path` und `title_path` sind hier auf ihren ersten und letzten Eintrag gekürzt):

```text
exit status: 1 (STATUS_OK)
$Result: array(11)
  content: string (7085 bytes)
  view_parameters: array(5)
    offset: false
    year: false
    month: false
    day: false
    namefilter: false
  path: array(4)
    0: array(4)
      text: 'Websites'
      url: '/content/view/full/2'
      url_alias: ''
      node_id: 2
    3: array(4)
      text: 'Why Should You Choose Strength Training?'
      url: false
      url_alias: false
      node_id: 96
  title_path: array(4)
    0: array(4)
      text: 'Websites'
      url: '/content/view/full/2'
      url_alias: ''
      node_id: 2
    3: array(3)
      text: 'Why Should You Choose Strength Training?'
      url: false
      url_alias: false
  section_id: 1
  node_id: 96
  navigation_part: 'ezcontentnavigationpart'
  content_info: array(25)
    object_id: 94
    node_id: 96
    parent_node_id: 92
    class_id: 44
    class_identifier: 'ng_article'
    remote_id: 'media-o-958'
    node_remote_id: 'media-n-729'
    offset: false
    viewmode: 'full'
    navigation_part_identifier: 'ezcontentnavigationpart'
    node_depth: 4
    url_alias: 'fitness/why-should-you-choose-strength-training'
    current_language: 'eng-US'
    language_mask: 2
    main_node_id: 96
    main_node_url_alias: false
    persistent_variable: false
    class_group: false
    state: array(2)
      2: 1
      47: 80
    state_identifier: array(2)
    section_identifier: 'standard'
    parent_class_id: 45
    parent_class_identifier: 'ng_landing_page'
    parent_node_remote_id: 'media-n-721'
    parent_object_remote_id: 'media-o-950'
  template_list: array(11)
    0: string (96 bytes)
    1: string (81 bytes)
    2: string (77 bytes)
    3: string (75 bytes)
    4: string (88 bytes)
    5: string (98 bytes)
    6: string (68 bytes)
    7: string (98 bytes)
    8: string (93 bytes)
    9: string (66 bytes)
    10: string (75 bytes)
  cache_ttl: -1
  is_default_navigation_part: false
```

Die Schlüssel werden einzeln in der [Referenz des Modulergebnisses](referenz-des-modulergebnisses.md) erklärt. Zwei Einzelheiten:
`is_default_navigation_part` fügt `eZModule::run()` hinzu (`lib/ezutils/classes/ezmodule.php`): `false`, wenn die Ansicht `navigation_part`
selbst gesetzt hat, wie `content/view` es tut, `true`, wenn `run()` es aus `default_navigation_part` der Ansicht ergänzt hat (wie im
Fehlerergebnis unten); und `template_list` nennt die genutzten Templates, die der Content-View-Cache speichert, um zu wissen, wann eine
Template-Änderung den Cache veralten lässt.

Ein nicht vorhandener Knoten gibt stattdessen das Ergebnis der Fehleransicht zurück (`kernel/private/classes/views/error/view.php`):

```text
content/view/full/99999999
exit status: 2 (other)
$Result: array(9)
  content: string (1391 bytes)
  path: array(2)
    0: array(2)
      text: 'Error'
      url: false
    1: array(2)
      text: 'kernel (3)'
      url: false
  errorCode: '404'
  errorMessage: 'Not Found'
  errorType: 'kernel'
  errorNumber: 3
  responseHeaders: array(2)
    0: 'HTTP/1.1 404 Not Found'
    1: 'Status: 404 Not Found'
  is_default_navigation_part: true
  navigation_part: 'ezcontentnavigationpart'
```

Der Exit-Status ist `eZModule::STATUS_FAILED` (2); `STATUS_OK` ist 1.

## Von $Result zu $module_result

Der Web-Kernel (`ezpKernelWeb`, `kernel/private/classes/ezpkernelweb.php`) führt die Ansicht in seiner Dispatch-Schleife aus und dann, bevor
er das Pagelayout rendert:

1. setzt er `ui_context` (wenn die Ansicht es nicht getan hat), `ui_component`, `is_mobile_device` und `mobile_device_alias` am Ergebnis,
   `path` auf `false`, wenn die Ansicht keinen angegeben hat, und `uri` auf die URI der Anfrage;
2. ruft er `$tpl->setVariable( "module_result", $moduleResult )` auf;
3. setzt er die anderen Variablen des Pagelayouts:

| Variable | Wert |
|---|---|
| `$module_result` | `$Result` der Ansicht, mit den oben ergänzten Schlüsseln |
| `$site` | `uri`, `redirect`, `meta` (`[SiteSettings] MetaDataArray`, mit einer aus dem Pfad gebildeten Beschreibung, wenn keine gesetzt ist), `version` (die Version von Exponential), `page_title` (der Titel des Moduls) |
| `$current_user` | der aktuelle `eZUser` (false, wenn der Siteaccess kein Benutzerobjekt braucht) |
| `$anonymous_user_id` | `[UserSettings] AnonymousUserID` |
| `$access_type` | der aktuelle Siteaccess (Name, Typ) |
| `$warning_list` | für die Seite gesammelte Warnungen, oder false |
| `$navigation_part` | das `eZNavigationPart` von `$module_result.navigation_part` (Standard `ezcontentnavigationpart`) |
| `$uri_string`, `$requested_uri_string` | die URI, die lief, und die, die der Besucher angefragt hat |
| `$ui_context`, `$ui_component` | Kopien der Schlüssel des Ergebnisses |

4. holt er das Pagelayout und gibt es aus. Davor entfernt er jede in der Engine verbliebene Variable `$node`, sodass ein Pagelayout nie
   versehentlich das `$node` des Ansichts-Templates sieht.

## Das Pagelayout wählen

- Standardmäßig holt der Kernel `design:pagelayout.tpl`.
- Eine Ansicht kann `$Result['pagelayout']` setzen: einen Template-Namen (der Kernel fügt `design:` hinzu, außer der Name hat schon eine
  Ressource mit `:`), oder `false` für überhaupt kein Pagelayout; dann ist `$Result['content']` die ganze Antwort. Die Benutzeransichten
  `login`, `register`, `activate`, `success` und `forgotpassword` nutzen so `loginpagelayout.tpl`, und `content/treemenu` setzt `false`.
- Die Ansicht `layout/set/<style>` setzt `$Result['pagelayout']` aus dem `PageLayout` dieses Stils in `layout.ini` und fügt den Override-Schlüssel
  `layout` hinzu, sodass URLs wie `/layout/set/print/content/view/full/96` mit einem anderen Pagelayout gerendert werden.

## Variablen, die ein Template einer Knotenansicht bekommt

`eZNodeviewfunctions::generateNodeViewData()` (`kernel/classes/eznodeviewfunctions.php`) setzt diese, bevor es
`design:node/view/<view mode>.tpl` holt:

| Variable | Wert |
|---|---|
| `$node` | der Knoten (`eZContentObjectTreeNode`) |
| `$viewmode` | der Ansichtsmodus, etwa `full` |
| `$language_code` | die in der URL verlangte Sprache, oder false |
| `$view_parameters` | die [Ansichtsparameter](ansichtsparameter.md) |
| `$collection_attributes`, `$validation` | für Formulare zur Informationssammlung |
| `$persistent_variable` | `false`; ein Template kann sie setzen (siehe [Die persistente Variable, von innen](die-persistente-variable-von-innen.md)) |
| `$node_path` | dieselbe Liste wie `$Result['path']` |

In der URL angegebene Benutzerparameter werden außerdem als eigene Variablen gesetzt (der Teil `_custom` der Ansichtsparameter), sodass
`/(color)/red` eine Variable `$color` im Knoten-Template anlegt.

## Verwandte Seiten

- [Referenz des Modulergebnisses](referenz-des-modulergebnisses.md)
- [Ansichtsparameter](ansichtsparameter.md)
- [Die persistente Variable, von innen](die-persistente-variable-von-innen.md)
- [Modulergebnis und content_info](../designer/vorlagen/module-result-und-content-info.md)
- [Eigene Module und Ansichten](../ersteller/anwendungsentwickler/eigene-module-und-ansichten.md)
- [Ergebnisse von Ansichten und das Pagelayout](../ersteller/anwendungsentwickler/ergebnisse-von-ansichten-und-das-pagelayout.md)
- [Referenz der Moduldefinition](../ersteller/anwendungsentwickler/referenz-der-moduldefinition.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
