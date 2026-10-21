---
title: Regeln für Schichten und Benennung
audience: developer, admin
category: standards
order: 210
summary: Welche Schicht von Exponential von welcher abhängen darf, wofür jede Schicht da ist und wie neue Klassen, Module, Einstellungen, Templates, CSS-Klassen, Tabellen, Übersetzungskontexte und Befehle benannt werden.
keywords: Schichten, Schichtenmodell, Benennung, Namenskonventionen, Konventionen, exp-Präfix, Übersetzungskontext, Konsolenbefehl, Inline-CSS, layering, layers, lib, kernel, kernel/private, design, extension, naming, conventions, exp prefix, eZ, ezp, eZINI, eZHTTPTool, templates, fetch, inline CSS, CSP, nonce, AdminStylingRulesTest, translation context, console command
type: reference
---
# Regeln für Schichten und Benennung

Diese Seite richtet sich an Entwickler, die Code zu Exponential 6.0.x oder zu einer Extension hinzufügen, und an
Prüfer, die solche Änderungen kontrollieren. Sie beantwortet, wohin ein Stück Code gehört, welche Schichten es aufrufen
darf und wie es zu benennen ist. Jede Regel sagt **muss** (ein Review weist die Änderung zurück) oder **sollte** (der
erwartete Normalfall), zeigt, wie die Codebasis sie umsetzt, und listet die Stellen auf, die ihr noch nicht folgen.
Die Zahlen auf dieser Seite wurden im Oktober 2026 im Repository erhoben; sie sind Belege, keine Grenzen.

## Die Schichten

```text
 extension/<name>/          plugins: settings, modules, designs, classes, cronjobs
        |  may use everything below, through the documented extension points
 design/<design>/templates  presentation (.tpl), stylesheets, javascript
        |  reads data only through fetch functions and template operators
 kernel/<module>/           controllers: module.php, view entry files, function_definition.php
        |
 kernel/private/            internal API: ezpKernel, views, commands, cronjobs, services, REST, ezpEvent
        |
 kernel/classes/            domain: content, users, roles, datatypes, workflows, caches, persistence
        |
 lib/                       generic libraries: eZDB, eZINI, eZTemplate, eZFile, eZSys, eZModule, sessions
```

Eine Schicht darf die Schichten unter ihr aufrufen. Aufrufe nach oben sind die pro Schicht aufgeführten Ausnahmen.

### lib/: allgemeine Bibliotheken

**Zweck.** Infrastruktur, die nichts von Inhalten weiß: `lib/ezdb` (Datenbanktreiber), `lib/ezdbschema`,
`lib/ezutils` (`eZINI`, `eZSys`, `eZModule`, `eZExtension`, `eZHTTPTool`, `eZFunctionHandler`, `eZDebug`),
`lib/eztemplate` (`eZTemplate` und die Basisoperatoren), `lib/ezfile`, `lib/ezi18n`, `lib/ezimage`, `lib/ezlocale`,
`lib/ezsession`, `lib/ezsoap`, `lib/ezpdf`, `lib/ezdiff`, `lib/ezmath`.

**Regel.** Neuer Code in `lib/` **sollte nicht** von Klassen aus `kernel/` abhängen. Er **darf nicht** von einem Modul
oder einem Design abhängen.

**Stand der Regel: nicht überall erfüllt.** Die Bibliotheken wurden zusammen mit dem Kernel geschrieben, und mehrere
rufen ihn auf. Im Code gefundene Beispiele:

| Datei in `lib/` | Abhängigkeit vom Kernel |
|---|---|
| `lib/eztemplate/classes/eztemplateroleoperator.php` | `eZUser::currentUserID()`, `instanceof eZRole` |
| `lib/eztemplate/classes/eztemplatecacheblock.php` | `eZClusterFileHandler::instance()` (`kernel/classes/ezclusterfilehandler.php`) |
| `lib/ezutils/classes/ezexpiryhandler.php` | `eZClusterFileHandler::instance()` |
| `lib/ezutils/classes/ezdebug.php` | `eZUser::currentUserID()` |
| `lib/ezutils/classes/ezoperationmemento.php`, `ezhttppersistence.php` | `eZPersistentObject` (`kernel/classes/ezpersistentobject.php`) |
| `lib/ezdb/classes/expmongodb.php`, `lib/ezdbschema/classes/expmongoschema.php` | `eZPersistentObject`, `eZUser` |

Insgesamt nennen 8 Dateien in `lib/` `eZUser`, 8 nennen `eZClusterFileHandler` und 4 nennen `eZPersistentObject`.
Betrachten Sie `lib/` als „tiefere Ebene als der Kernel“, nicht als unabhängigen Satz von Bibliotheken. Eine Änderung
**sollte keine** neuen Abhängigkeiten dieser Art hinzufügen; das Verschieben der bestehenden ist eine offene
Altlast.

### kernel/classes/: die Domäne

**Zweck.** Das Inhaltsmodell und alles, was dazugehört: `eZContentObject`, `eZContentObjectTreeNode`,
`eZContentClass`, `eZRole`, `eZPolicy`, `eZPersistentObject`, die Datentypen in `kernel/classes/datatypes/`, die
Workflow-Typen in `kernel/classes/workflowtypes/`, Benachrichtigungs-Handler, Caches (`eZCache`,
`eZContentCacheManager`), Cluster-Datei-Handler und neuere Subsysteme in Unterverzeichnissen (zum Beispiel
`kernel/classes/audit/`).

**Regel.** Domänenklassen **dürfen keine** Seiten rendern und keine Eingaben der Anfrage lesen; sie nehmen Werte als
Parameter entgegen und geben Objekte oder Arrays zurück. Sie **sollten** von einer Ansicht, einem Befehl und einem
Cronjob gleichermaßen erreichbar sein.

### `kernel/<module>/`: Controller

**Zweck.** `module.php` (Ansichten und Richtlinienfunktionen), `function_definition.php` (Template-Fetches), die
Einstiegsdateien der Ansichten und, bei älteren Modulen, der Code der Ansicht selbst.

**Regel.** Eine Ansicht liest Eingaben über `eZHTTPTool` (`eZHTTPTool::instance()`, `hasPostVariable()`,
`postVariable()`), verlässt sich bei POST-Anfragen auf die Formular-Token-Prüfung der Extension `ezformtoken`, ruft
die Domänenschicht auf und rendert über `eZTemplate::factory()`. Sie **darf** kein HTML selbst ausgeben, außer bei
Downloads und JSON-Antworten.

**Ausnahmen.** 185 der 255 Ansichtsklassen in `kernel/private/classes/views/` verwenden `eZHTTPTool::instance()`; 10
lesen noch direkt `$_POST` oder `$_GET` (zum Beispiel `kernel/private/classes/views/content/multiedit.php`,
`contentcheck/fixfiles.php`, `contentfinder/action.php`).

### kernel/private/: interne API

**Zweck.** Code, der keine öffentliche Extension-API ist oder den der Kernel vollständig selbst verantwortet:
`ezpKernel` und `ezpKernelWeb`, `ezpEvent`, die klassenbasierten Ansichten, Befehle und Cronjob-Teile
(`kernel/private/classes/{views,commands,cronjobs}/`, Namespaces `Exponential\View\Kernel`,
`Exponential\Command\Kernel`, `Exponential\Cronjob\Kernel`), gemeinsame Dienste (`kernel/private/classes/services/`,
Namespace `Exponential\Service`), die REST-Schicht (`kernel/private/rest/`, `kernel/private/api/`, OAuth in
`kernel/private/oauth/` und die Module `oauth` und `oauthadmin` in `kernel/private/modules/`), der Cluster-Datei-Handler
`eZDFSFileHandler` und Kernel-Schnittstellen (`kernel/private/interfaces/`).

**Regel.** Extensions **sollten** die dokumentierten Erweiterungspunkte verwenden (Einstellungen,
`[RunnableSettings]`, `[Event] Listeners[]`), statt Klassen aus `kernel/private` direkt aufzurufen; deren Signaturen
können sich zwischen Releases ändern. Arbeit, die eine Ansicht, ein Befehl und ein Cronjob-Teil gemeinsam nutzen,
gehört in eine Dienstklasse, nicht in einen von ihnen
([Befehle, Cronjob-Teile und Modulansichten als Klassen](../../../eng-us/bc/6.0/cli_cronjob_view_abstractions.md)).

### design/: Darstellung

**Zweck.** Templates, Stylesheets, Skripte und Bilder pro Design: `design/standard`, `design/admin`,
`design/admin3`, `design/admin4`, `design/admin4l`, `design/base`, `design/editor`, `design/plain` und weitere.

**Regeln.**

- **Templates fragen keine Datenbank ab.** Die Template-Sprache hat kein SQL-Konstrukt. Daten kommen aus
  Fetch-Funktionen (`kernel/<module>/function_definition.php`, ausgeführt von `eZFunctionHandler`) und
  Template-Operatoren, die Berechtigungen in PHP anwenden. Ein Template **darf kein** rohes Datenbank-Handle erhalten.
  Ausnahme in der Praxis: Ein Operator ist PHP und kann abfragen, daher werden Operatoren wie Ansichten geprüft
  (`eztemplateroleoperator.php` liest Rollen über `eZRole`).
- **Kein Inline-CSS in Templates der Administrationsoberfläche.** Kein `<style>`-Element und kein `style=""`-Attribut
  in den Templates von `admin`, `admin3`, `admin4` und `admin4l`; alles CSS liegt im Verzeichnis `stylesheets/` des
  Designs und wird über die Stylesheet-Einstellungen geladen, damit der Packer es bündelt. Farben, Radien, Abstände
  und Schriften kommen aus den Design-Tokens (`--a4-*`), und jedes Farb-Token hat einen Wert für den Dunkelmodus.
  Durchgesetzt von `tests/tests/design/AdminStylingRulesTest.php` (ohne Datenbank): `ALLOWLIST` ist leer, und der
  einzige dauerhafte Eintrag in `EXCEPTIONS` ist `design/admin/templates/pagelayout.tpl` (Spaltenbreiten, die
  angewendet werden, bevor die Seite gezeichnet wird). Der Test prüft außerdem, dass `stylesheets/modules/*.css` nur
  Tokens verwendet und dass `components.css` in `admin` und `admin4` identisch ist. Referenz:
  [Gestaltung der Administrationsoberfläche](../../../eng-us/bc/6.0/admin4-styling.md).
- **Kein Inline-Skript in der Administrationsoberfläche.** Die Administrationsoberfläche läuft unter einer Content
  Security Policy ohne `'unsafe-inline'` (`site.ini [ContentSecurityPolicy]`; in der Administrationsoberfläche
  standardmäßig nur meldend). Von `admin4` und `admin4l` gerenderte Templates enthalten kein Inline-`<script>` mit Code,
  kein `on...=`-Attribut und keine `javascript:`-URL; durchgesetzt von
  `tests/tests/design/AdminContentSecurityPolicyTest.php` und, für die mitgelieferten Extensions,
  `tests/tests/design/ExtensionInlineScriptsTest.php`. Skriptcode gehört nach `design/<design>/javascript/` (zum
  Beispiel `design/admin4/javascript/admin4.js`).
- **Nonce für die Skripte, die inline bleiben müssen.** Mit `[ContentSecurityPolicy] Nonce=enabled` erhält jede
  Anfrage eine Nonce, die den Direktiven aus `NonceDirectives[]` hinzugefügt wird (ausgeliefert: `script-src`); ein
  Template gibt sie mit dem Operator `csp_nonce()` aus (`expContentSecurityPolicyOperator`, registriert in
  `kernel/common/eztemplateautoload.php`): `<script nonce="{csp_nonce()}">`. Siehe
  [Content Security Policy](../../funktionen/6.0/content-security-policy.md).
- **Ausgaben werden maskiert.** Jede Variable aus Inhalten oder Eingaben wird mit `|wash` ausgegeben (oder mit einem
  Operator, der maskiert).

### extension/: Plugins

**Zweck.** Alles, was nicht der Kernel ist: `extension/<name>/` mit `ezinfo.php`, `extension.xml`, `settings/`,
`modules/`, `design/`, `classes/`, `autoloads/`, `cronjobs/`, `translations/`, `bin/php/`.

**Regel.** Eine Extension **darf keine** Änderungen an Kernel-Dateien erfordern. Sie wird in
`site.ini [ExtensionSettings] ActiveExtensions[]` oder `ActiveAccessExtensions[]` eingeschaltet, und ihre Klassen
werden nach `php bin/php/ezpgenerateautoloads.php -e` gefunden. Klassenbasierter Code von Extensions verwendet die
Namespaces `Exponential\Command\Extension\<Ext>`, `Exponential\Cronjob\Extension\<Ext>` und
`Exponential\View\Extension\<Ext>\<Module>`.

## Übergreifende Regeln

- **Einstellungen werden nur über `eZINI` gelesen.** `eZINI::instance( 'file.ini' )->variable( 'Section', 'Setting' )`.
  Kein Code in `kernel/`, `lib/` oder `bin/` ruft `parse_ini_file()` oder `parse_ini_string()` auf. Einstellungen
  werden mit `./console exp:ini` oder `eZINI::save()` geschrieben, nie durch Zeichenkettenersetzung in einem Skript
  ([exp:ini](../../../eng-us/bc/6.0/console-exp-ini.md)).
- **Debug-Ausgabe über `eZDebug`.** `eZDebug::writeError()`, `writeWarning()`, `writeNotice()`; kein `echo` oder
  `var_dump` bleibt im Kernel-Code zurück.
- **Übersetzungen über `ezpI18n::tr( $context, $text )`** in PHP und `i18n( $context )` in Templates.
- **Persistente Worker.** Code muss davon ausgehen, dass er in einem langlebigen Velocity-Worker laufen kann (siehe
  [Kernel-Architektur](kernel-architektur.md#server-velocity-php-fpm-und-frankenphp)).

## Namenskonventionen

### Das Präfix exp für neuen Code, bestehende Bezeichner bleiben

Das Produkt heißt Exponential; die Codebasis hat ihre Bezeichner von eZ Publish 4 geerbt. Die Regeln:

- **Neue** Klassen, Module, Template-Operatoren, Templates, Einstellungsdateien und Übersetzungskontexte **sollten**
  `exp` / `Exp` verwenden: `expAudit` (`kernel/classes/audit/expaudit.php`), `expContentSecurityPolicy`
  (`kernel/private/classes/expcontentsecuritypolicy.php`), `expInfo` (`lib/ezutils/classes/expinfo.php`),
  `expMongoDB`, das Modul `exphelp` und `settings/exphelp.ini`. Es gibt mehr als 330 Klassendeklarationen mit dem
  Präfix `exp` in `kernel/` und `lib/`.
- **Bestehende** Bezeichner `eZ*` und `ezp*` (`eZContentObject`, `eZINI`, `ezpKernel`), Tabellennamen, Dateipfade
  und Namen von Einstellungen **dürfen nie umbenannt werden**: Extensions und Websites hängen von ihnen ab. Die
  Umbenennung von 2026 hat nur Text geändert, den Menschen lesen
  ([Das Produkt heißt Exponential](../../funktionen/6.0/umbenennung-in-exponential.md)).
- Text für Menschen (UI-Texte, Dokumentation, Commit-Nachrichten) verwendet den Namen Exponential.
- **`sevenx` und `exp` sind beides Präfixe von 7x.** `sevenx` ist das offizielle öffentliche PHP-Code-Präfix von 7x
  (ein Bezeichner kann nicht mit einer Ziffer beginnen, daher kann „7x“ keines sein); Extensions und Klassen von 7x
  verwenden `sevenx` oder `exp`, wie 7x es braucht; `exp` wird bewusst eingesetzt, um die Marke Exponential aufzubauen
  und die Zuständigkeit dem Projekt Exponential und der Exponential Foundation zuzuordnen. Die vollständige Regel:
  [Namenspräfixe: sevenx und exp](../namenspraefixe-sevenx-und-exp.md).

**Ausnahmen (hinzugekommen, bevor die Regel einheitlich angewendet wurde).** Kernel-Module, die 2026 ohne das Präfix
hinzukamen: `apikey`, `audit`, `contentcheck`, `contentfinder`, `csp`, `mailpreferences`, `uiswitch` (nur `exphelp`
hat es). Die Template-Operatoren `csp_nonce` und `audit_label` (während `exp_environment` es hat). Die Klasse
`ezpRepairQueue` (`lib/ezutils/classes/ezprepairqueue.php`) verwendet das alte Präfix `ezp`. Diese bleiben, wie sie
sind: Sie jetzt umzubenennen wäre selbst ein Bruch.

### Klassen und Dateien

- Eine Klasse pro Datei; der Dateiname ist der Klassenname in Kleinbuchstaben: `expContentSecurityPolicy` in
  `expcontentsecuritypolicy.php`, `ezpKernelWeb` in `ezpkernelweb.php`.
- Ausführbare Klassen mit Namespace folgen stattdessen dem Pfad: `Exponential\View\Kernel\Content\View` in
  `kernel/private/classes/views/content/view.php`.
- Nach dem Hinzufügen oder Umbenennen einer Klasse erzeugen Sie die Klassenzuordnungen neu
  (`php bin/php/ezpgenerateautoloads.php -e` für Extensions, `-k` für den Kernel mit `.autoloadignore` oder
  `--exclude` für Arbeitskopien innerhalb des Wurzelverzeichnisses).

### Extensions

Verzeichnisnamen unter `extension/` in Kleinbuchstaben. Neue Extensions **sollten** mit `exp` beginnen; die im
Repository mitgelieferten Extensions behalten ihre Namen: `ezjscore`, `ezoe` und `ezformtoken` die historischen,
`expservices` bereits mit dem neuen Präfix. `Info_url` und `<info_url>` zeigen auf das öffentliche Repository der
Extension ([Extension-Metadaten](../../spezifikationen/6.0/extension-metadaten.md)).

### INI-Dateien, Abschnitte und Einstellungen

- Dateinamen in Kleinbuchstaben: `settings/<name>.ini`; Überschreibungen `<name>.ini.append.php`.
- Abschnitte in CamelCase, meist mit `Settings` am Ende: `[SiteAccessSettings]`, `[DataTypeSettings]`,
  `[HelpSettings]`, `[DeploySettings]`; dynamische Abschnitte verwenden einen Bindestrich: `[CronjobPart-frequent]`.
- Einstellungen in CamelCase: `MatchOrder`, `AvailableDataTypes[]`, `ExtensionDirectories[]`; Arrays enden auf `[]`
  (`List[]`), Hashes verwenden `Name[key]=value`.
- Schalter verwenden `enabled`/`disabled` (`ViewCaching=enabled`); einige ältere Einstellungen verwenden
  `true`/`false` (`ForceVirtualHost=false`). Eine neue Einstellung **sollte** `enabled`/`disabled` verwenden.
- Jede neue Einstellung erhält in der ausgelieferten Datei einen Kommentar darüber und einen sicheren Standardwert.

### Templates

- `design/<design>/templates/<module>/<view>.tpl` für Modulansichten (`design/admin4/templates/exphelp/viewer.tpl`).
- Wiederverwendbare Teile in einem Unterverzeichnis `parts/` (`design/admin4/templates/exphelp/parts/`);
  seitenweite Teile als `page_<name>.tpl` (`page_header.tpl`, `page_mainarea.tpl`).
- Datentyp-Templates unter `content/datatype/view/` und `content/datatype/edit/`, benannt nach dem Datentyp-String.
- Überschreibungen unter `override/templates/`, zugeordnet in `override.ini`.

### CSS und JavaScript

- CSS-Klassen der Administrationsoberfläche verwenden das Design-Präfix: `.a4-*` in `admin4` (`.a4-card`, `.a4-btn`,
  `.a4-table`), mit einem zusätzlichen Segment für ein Modul (`.a4-help-*` in
  `design/admin4/stylesheets/modules/help.css` und `admin4.css`). Keine modulspezifischen Präfixe außerhalb des
  Design-Präfixes.
- Tokens sind CSS Custom Properties `--a4-*`; Modul-CSS liegt in `design/admin4/stylesheets/modules/<group>.css`
  (`content`, `help`, `people`, `setup`, `structure`, `workflow`).
- Skriptdateien werden nach ihrem Zweck in Kleinbuchstaben mit `_` oder `-` benannt (`a4_mega_menu.js`,
  `exphelp.js`).

### Datenbanktabellen

Kernel-Tabellen behalten das Präfix `ez` (118 der Tabellen in `share/db_schema.dba`). Tabellen, die 2026 hinzukamen,
verwenden `exp`: `expaudit_cursor`, `expaudit_event`, `expaudit_file`, `expmail_category`, `expmail_consent_log`,
`expmail_pending`, `expmail_preference`, `expmail_suppression`, `expbookmark_folder`, `expapikey`. Namen sind
kleingeschrieben mit `_`. Spalten, die zu bestehenden `ez`-Tabellen hinzukommen, folgen dem Stil der Tabelle (zum
Beispiel `ezcontentobject_trash.trashed_by`).

### Übersetzungskontexte

Kontexte sind der Pfad des Codes, der sie verwendet: `kernel/<module>` für PHP-Ansichten
(`ezpI18n::tr( 'kernel/exphelp', 'Search' )`) und `design/<design>/<module>/<view>` für Templates. Die
Übersetzungsdateien sind `share/translations/<locale>/translation.ts` (dazu `share/translations/untranslated/`); neue
Texte **müssen** dem Kontext hinzugefügt werden, den der Code verwendet, damit `exp:i18n` und die Übersetzungstests sie
finden.

### Konsolenbefehle

- `bin/php/<name>.php` ist automatisch `exp:<name>`; ein Skript fügt mit `@alias` in seinem Docblock lesbare Namen
  hinzu, mit `:` zwischen den Wörtern: `exp:help` (`bin/php/exphelp.php`), `exp:help:content`, `exp:access:check`,
  `exp:mail:status`. Velocity ist `exp:velocity` mit Unterbefehlen (`exp:velocity deploy`, `exp:velocity cache clear`).
- Skripte von Extensions heißen `ext:<ext>:<name>`; Cronjob-Teile `cron:<part>`.
- Befehlsnamen, Optionen und Exit-Codes sind Teil des Kompatibilitätsversprechens
  ([Kompatibilität, Releases und Deployment](kompatibilitaet-releases-deployment.md)).

## Bekannte Lücken

- `lib/` hängt mindestens in den oben aufgeführten Dateien von Kernel-Klassen ab.
- Sieben Kernel-Module von 2026 und zwei Operatoren tragen nicht das Präfix `exp`.
- Zehn Ansichtsklassen lesen Superglobals direkt.
- INI-Schalter mischen `enabled`/`disabled` und `true`/`false`.
- Die Regeln für Designs werden nur für die Designs der Administrationsoberfläche durch Tests durchgesetzt;
  Website-Designs werden nicht auf Inline-CSS geprüft.

## Verwandte Seiten

- [Kernel-Architektur](kernel-architektur.md)
- [Katalog der Entwurfsmuster](../muster/README.md)
- [Kompatibilität, Releases und Deployment](kompatibilitaet-releases-deployment.md)
- [Gestaltung der Administrationsoberfläche](../../../eng-us/bc/6.0/admin4-styling.md), [Content Security Policy](../../funktionen/6.0/content-security-policy.md)
