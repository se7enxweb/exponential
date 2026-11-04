# Exponential 6.0 erweitern

Diese Anleitung ist die Übersicht für Entwickler, die Exponential 6.0 erweitern, ohne den Kernel zu ändern. Sie behandelt:

- welcher Weg der Erweiterung zur Aufgabe passt und woraus eine Extension besteht;
- wie Sie eine anlegen, von Hand oder mit den RAD-Assistenten, und wie Sie sie einschalten;
- Module, Ansichten und Richtlinien;
- Templates und die Regeln der Administrationsdesigns;
- Datentypen, Workflow-Ereignisse, Cronjob-Teile und Konsolenbefehle;
- die Schnittstellen, das Audit-Protokoll, Geheimnisse, Übersetzungen und Hilfeseiten;
- die Website-Werkzeugleiste und Exponential Layouts;
- Tests, und wie Sie veröffentlichen.

Sie setzt eine funktionierende Installation aus [Exponential 6.0 installieren](INSTALL.md) voraus, konfiguriert wie in
[Exponential 6.0 konfigurieren](CONFIGURING.md). Jeder Abschnitt nennt die Dateien und Einstellungen, zeigt ein
funktionierendes Beispiel und verweist auf die Seite mit allen Einzelheiten. So bleibt diese Seite die Übersicht und
bleiben die tieferen Seiten die Referenz.

Jeder Befehl wird im Wurzelverzeichnis der Installation ausgeführt: dem Verzeichnis mit `index.php`, `autoload.php`,
`bin/` und `settings/`. Die Befehlskonsole ist `bin/php/console`; viele Installationen legen die Abkürzung `./console`
an. Wenn Sie ein Skript als `root` ausführen, ergänzen Sie `--allow-root-user`.

> **Ändern Sie nie den Kernel, um eine Funktion hinzuzufügen.** Alles Folgende liegt in `extension/<name>/`. Ein Update
> von Exponential ersetzt den Kernel und behält Ihre Arbeit. Muss eine Kernel-Klasse wirklich anders sein, bieten die
> RAD-Werkzeuge als letztes Mittel eine Kernel-Überschreibung an (Abschnitt 3), und die Änderung gehört als Pull-Request
> in das Projekt.

## Inhalt

1. [Der Schnellstart: eine funktionierende Extension in zehn Minuten](#1-der-schnellstart-eine-funktionierende-extension-in-zehn-minuten)
2. [Den Weg der Erweiterung wählen](#2-den-weg-der-erweiterung-wählen)
3. [Eine Extension anlegen, von Hand oder mit den RAD-Werkzeugen](#3-eine-extension-anlegen-von-hand-oder-mit-den-rad-werkzeugen)
4. [Einschalten: Aktivierung, Autoloads, Tabellen](#4-einschalten-aktivierung-autoloads-tabellen)
5. [Module, Ansichten und Richtlinien](#5-module-ansichten-und-richtlinien)
6. [Templates und Design](#6-templates-und-design)
7. [Inhalte, Workflows und Arbeit im Hintergrund](#7-inhalte-workflows-und-arbeit-im-hintergrund)
8. [Konsolenbefehle](#8-konsolenbefehle)
9. [Schnittstellen, Audit, Geheimnisse, Übersetzungen und Hilfe](#9-schnittstellen-audit-geheimnisse-übersetzungen-und-hilfe)
10. [Die Website-Werkzeugleiste und Exponential Layouts](#10-die-website-werkzeugleiste-und-exponential-layouts)
11. [Tests und CI](#11-tests-und-ci)
12. [Veröffentlichen](#12-veröffentlichen)
13. [Fehlerbehebung](#13-fehlerbehebung)

## 1. Der Schnellstart: eine funktionierende Extension in zehn Minuten

Der schnellste Weg zu einer funktionierenden Extension sind die RAD-Werkzeuge. Sie brauchen eine Anmeldung als
Administrator (die Richtlinie `setup/setup`).

1. Öffnen Sie **Setup > RAD** (`/setup/rad`). Die Seite listet jede Stelle, an der Exponential erweitert werden kann,
   in sieben Gruppen.
2. Wählen Sie das Werkzeug der gewünschten Stelle, zum Beispiel **Modul** (`/setup/moduleextension`).
3. Geben Sie den Namen der Extension ein (Kleinbuchstaben, Ziffern und Unterstriche, zum Beispiel `myhello`) und was
   erzeugt werden soll.
4. Drücken Sie **Vorschau aktualisieren**. Der Assistent listet jede Datei, die er unter `extension/myhello` schreiben
   würde, bevor er eine schreibt.
5. Drücken Sie **In extension/ anlegen**. Ein Assistent überschreibt nie eine vorhandene Extension und schreibt nichts
   außerhalb von `extension/<name>`.

Dann schalten Sie sie ein und machen ihre Klassen bekannt:

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' myhello global --allow-root-user
php bin/php/ezpgenerateautoloads.php -e
./console exp:velocity deploy --allow-root-user
```

Erwartet: `exp:velocity deploy` endet mit PASS oder SKIP für jeden Schritt. **Setup > Extensions**
(`/setup/extensions`) listet die neue Extension mit ihrer Version, und die Ansicht des Moduls antwortet unter
`/<modul>/<ansicht>`.

Wenn Sie lieber zuerst jede Datei von Hand sehen, baut die Anleitung [Extensions](anleitungen/extensions.md) in Abschnitt
6.1 dieselbe Art Extension Datei für Datei.

## 2. Den Weg der Erweiterung wählen

Wählen Sie den kleinsten Weg, der die Aufgabe erfüllt. Jeder funktioniert über Updates hinweg weiter.

| Sie wollen | Der Weg | Wo er liegt |
|---|---|---|
| Einen anderen Wert einer Einstellung | Eine Überschreibung der Einstellungen | `settings/override/` oder `settings/siteaccess/<siteaccess>/` (siehe [Konfigurieren](CONFIGURING.md)) |
| Ein anderes Aussehen einer Seite | Eine Template-Überschreibung in einem Design | das `design/<design>/override/templates/` einer Extension, mit `override.ini.append.php` |
| Ein eigenes Theme | Eine Design-Extension | `extension/<name>/design/<design>/`, eingetragen in `DesignExtensions[]` |
| Neue Seiten, Inhaltstypen, Operatoren, Befehle, Aufgaben | Eine vollständige Extension | `extension/<name>/` |
| Dasselbe, geteilt mit anderen Installationen | Ein Composer-Paket dieser Extension | ein eigenes Repository, Typ `ezpublish-legacy-extension` |

Eine Extension ist ein Ordner mit dem Namen der Extension. Die Teile, die sie haben kann:

| Teil | Was er tut | Referenz |
|---|---|---|
| `extension.xml` | Name, Version, Lizenz, Website und Abhängigkeiten; die Info-Seite liest sie zuerst | [Metadaten von Extensions](spezifikationen/6.0/extension-metadaten.md) |
| `ezinfo.php` | Dieselben Angaben für ältere Leser: die Klasse `<name>Info` mit einer statischen `info()` | [Metadaten von Extensions](spezifikationen/6.0/extension-metadaten.md) |
| `settings/` | `.ini.append.php`-Dateien, die die Einstellungen des Kernels ergänzen, und eigene `.ini`-Dateien | [Ablageorte der Überschreibungen](spezifikationen/6.0/ini-override-platzierungen.md) |
| `design/` | Templates, Stylesheets, Skripte und Bilder, ein Verzeichnis je Design | [Templates und Design](anleitungen/templates-und-design.md) |
| `modules/` | Module und ihre Ansichten | Abschnitt 5 |
| `classes/` | PHP-Klassen, gefunden über das Autoload-Array | Abschnitt 4 |
| `autoloads/` | Template-Operatoren und -Funktionen | Abschnitt 6 |
| `cronjobs/`, `bin/php/` | Cronjob-Teile und Konsolenskripte | Abschnitte 7 und 8 |
| `translations/` | Ein Katalog je Sprache, `translations/<locale>/translation.ts` | Abschnitt 9 |
| `share/db_schema.dba` | Die eigenen Tabellen der Extension, auf jedem Datenbanksystem | Abschnitt 4 |
| `doc/` | Ihre Seiten in der Hilfe | Abschnitt 9 |

Mehr als ein Verzeichnis für Extensions ist möglich, zum Beispiel für die eigenen Extensions eines Anbieters:
[Zusätzliche Extension-Verzeichnisse](funktionen/6.0/zusaetzliche-extension-verzeichnisse.md).

## 3. Eine Extension anlegen, von Hand oder mit den RAD-Werkzeugen

**Von Hand.** Zwei Dateien machen eine Extension, die das System erkennt: `ezinfo.php` und `extension.xml`, mit
derselben Version in beiden. Die Anleitung [Extensions](anleitungen/extensions.md), Abschnitt 6.1, schreibt eine
vollständige mit einem Modul, einer Ansicht und einem Template und zeigt das erwartete Ergebnis jedes Schritts.

**Mit den RAD-Werkzeugen.** **Setup > RAD** (`/setup/rad`) kennt 68 Erweiterungspunkte, 64 davon mit einem Werkzeug,
das eine vollständige Extension schreibt. Die Assistenten und ihre Adressen:

| Sie wollen | Öffnen Sie |
|---|---|
| Ein Design (Templates, Stylesheets, Überschreibungen) | `/setup/designextension` |
| Template-Operatoren, Fetch-Funktionen, Template-Funktionen | `/setup/templateoperator` |
| Einen Handler, der einen Teil des Systems ersetzt (Anmeldung, E-Mail, Sitzung, Suche, Mehrwertsteuer und Versand im Shop, REST-Filter ...) | `/setup/handlerextension/<art>` |
| Eine Extension, die nur Einstellungen trägt | `/setup/settingsextension` |
| Eine Inhaltsklasse, als Skript geschrieben | `/setup/contentextension` |
| Einen Datentyp | `/setup/datatype` |
| Ein Workflow-Ereignis | `/setup/workflowevent` |
| Ein Modul mit Ansichten, oder ein Modul über vorhandenen Datenbanktabellen | `/setup/moduleextension`, `/setup/modulewizard` |
| Eine ersetzte Kernel-Klasse (das letzte Mittel) | `/setup/kerneloverride` |

Nach dem Anlegen zählt **Setup > RAD > Übersicht der Erweiterungspunkte** (`/setup/radsurvey`) ihre Registrierungen,
und die Prüfung auf `/setup/rad` listet alles, was eingerichtet ist, aber nicht funktionieren kann, mit dem Weg zur
Behebung.

Referenz: [RAD-Werkzeuge](funktionen/6.0/rad-werkzeuge.md), die Liste aller
[Erweiterungspunkte](bc/6.0/rad-erweiterungspunkte.md) mit ihren Einstellungen und je einem Beispiel, und
[Sicherheit der RAD-Werkzeuge](bc/6.0/rad-sicherheit.md).

## 4. Einschalten: Aktivierung, Autoloads, Tabellen

Eine Extension tut nichts, bis sie eingeschaltet ist.

- **Für die ganze Installation:** `[ExtensionSettings] ActiveExtensions[]` in `settings/override/site.ini.append.php`.
- **Für einen Siteaccess:** `ActiveAccessExtensions[]` in `settings/siteaccess/<siteaccess>/site.ini.append.php`.
- **In der Administration:** **Setup > Extensions** schaltet Extensions ein und aus und zeigt Probleme.

Die Reihenfolge der Liste ist die Rangfolge: Eine früher geladene Extension gewinnt bei Einstellungen und Templates
gegen eine spätere. Mit `ExtensionOrdering=enabled` entscheiden zuerst die in `extension.xml` angegebenen
Abhängigkeiten ([Ladereihenfolge](funktionen/6.0/ladereihenfolge-der-extensions.md)).

```bash
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' myhello global --dry-run --allow-root-user
./console exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' myhello global --allow-root-user
```

**Autoloads.** Klassen in `classes/` werden über ein Autoload-Array gefunden. Erzeugen Sie es neu, sobald Sie eine
Klasse hinzufügen oder umbenennen:

```bash
php bin/php/ezpgenerateautoloads.php -e
```

**Ausrollen.** `./console exp:velocity deploy --allow-root-user` führt dann jeden Schritt, den eine PHP-Änderung
braucht, in der richtigen Reihenfolge aus: die Autoloads, den INI-Cache, die Template-Caches, ein Neuladen von
PHP-FPM, einen Neustart von Velocity und die Inhalts-Caches. Es meldet PASS, FAIL oder SKIP je Schritt und hält beim
ersten Fehler an. Ergänzen Sie `--packer`, wenn sich Stylesheets oder Skripte geändert haben, und `--kernel`, wenn eine
Kernel-Klasse hinzugekommen ist. Einzelheiten: [Eine PHP-Änderung ausrollen](bc/6.0/velocity-engines.md) und
[Ausrollen](anleitungen/bereitstellung.md).

**Tabellen.** Eine Extension, die Tabellen braucht, liefert sie in `share/db_schema.dba`, einmal, für jedes
Datenbanksystem (MySQL und MariaDB, PostgreSQL, SQLite, Oracle und MongoDB).

- **Neue Installation:** Der Einrichtungsschritt legt die fehlenden Tabellen jeder aktiven Extension an.
- **Bestehende Installation:** Der Upgrade-Schritt `6.0.15-active-extension-tables` (`./console exp:upgrade`) tut
  dasselbe, nachdem Sie eine Extension eingeschaltet haben.

Fehlende Tabellen werden angelegt; nichts wird gelöscht oder geändert. Einzelheiten:
[Extensions einer neuen Installation](funktionen/6.0/erweiterungen-einer-neuen-installation.md).

Änderungen an den Tabellen einer bereits installierten Extension sind eigene Upgrade-Schritte:
[Upgrade-Schritte für Extensions](anleitungen/upgrade-schritte-fuer-erweiterungen.md).

## 5. Module, Ansichten und Richtlinien

Ein Modul ist ein Ordner `modules/<modul>/` mit einer `module.php`, die seine Ansichten auflistet. Eine Ansicht gibt
den Inhalt der Seite zurück. Das Modul wird gefunden, sobald sich die Extension in `settings/module.ini.append.php`
einträgt (`[ModuleSettings] ExtensionRepositories[]=<name>`).

```php
<?php
$Module = array( 'name' => 'hello' );

$ViewList = array();
$ViewList['world'] = array( 'script' => 'world.php',
                            'functions' => array( 'read' ),
                            'params' => array( 'Name' ),
                            'unordered_params' => array( 'page' => 'Page' ) );

$FunctionList = array();
$FunctionList['read'] = array();
?>
```

- **Ansichtsparameter, nie die Abfragezeichenkette.** Eine Ansicht liest ihre Eingaben aus ihren Parametern: den
  geordneten (`/hello/world/Ada`) und den benannten (`/hello/world/Ada/(page)/2`), in `$Params`. Lesen Sie nie
  `$_GET`, und bauen Sie nie Links mit `?schluessel=wert`. Ein Formular sendet per POST, und die Ansicht antwortet mit
  einer Weiterleitung (303) auf die Adresse mit Ansichtsparametern. So hat jeder Zustand einer Seite seine eigene
  Adresse. So arbeiten seit 6.0.15 auch die Seiten der Administration
  ([Suchlinks verwenden Ansichtsparameter](bc/6.0/suchlinks-mit-view-parametern.md),
  [Ansichtsparameter](hilfe/erweiterungsentwicklung/teil-3-erweiterungsentwickler/view-parameter.md)).
- **Zugriff.** Eine Ansicht ohne `'functions'` hat keine Zugriffskontrolle: Jeder, der die Website erreicht, kann sie
  öffnen. Deklarieren Sie eine Funktion in `$FunctionList`, nennen Sie sie an der Ansicht und vergeben Sie sie unter
  **Benutzer > Rollen und Richtlinien**. Eine eigene Einschränkung für die Inhaltsfunktionen ist ein
  Einschränkungs-Handler
  ([Richtlinien-Einschränkungen von Extensions](funktionen/6.0/handler-fuer-inhaltseinschraenkungen.md)). Siehe
  [Rollen und Richtlinien](anleitungen/rollen-und-richtlinien.md) und [Anfrageregeln](funktionen/6.0/anfrageregeln.md).
- **Ansichten als Klassen.** Neue Ansichten werden als Klassen geschrieben (`Exponential\Runnable\ModuleView`), und die
  einfachen Ansichtsdateien funktionieren weiter:
  [Befehle, Cronjob-Teile und Modulansichten als Klassen](bc/6.0/cli-cronjob-und-view-abstraktionen.md).
- **Ein Kernel-Modul** kann durch ein Modul gleichen Namens in einer Extension ersetzt werden:
  [Eine Extension kann ein Kernel-Modul ersetzen](funktionen/6.0/modulueberschreibung-durch-extensions.md).

**Die linken Menüs.** Ein eigener Link in einem Menü der Administration gehört in die `settings/menu.ini.append.php`
der Extension:

```ini
[Leftmenu_setup]
Links[hello]=hello/world
LinkNames[hello]=Hello
PolicyList_hello[]=hello/read
```

Der Link wird nur Benutzern gezeigt, die jede Richtlinie seiner `PolicyList_<schluessel>[]` haben. Einige
Einstellungen der Menüabschnitte platzieren Links:

- `LeadLinks[]` nennt Links, die vor den Gruppen stehen;
- `TrailLinks[]` nennt Links, die das Menü abschließen;
- `CollapseAfter=<n>` zeigt die ersten n Links und legt den Rest unter **Mehr**;
- `LinkIcons[]` und `LinkDescriptions[]` geben einem Link seinen Platz im Megamenü von admin4.

Referenz: [Das Design admin4](funktionen/6.0/admin4-design.md).

## 6. Templates und Design

- **Überschreibungen.** Eine Template-Überschreibung wählt für einen Treffer (Klasse, Knoten, Bereich, Ansichtsmodus
  ...) ein anderes Template. Sie wird in der `override.ini.append.php` der Siteaccess-Einstellungen der Extension
  eingetragen. Welche Überschreibung gewinnt: [Reihenfolge der Template-Überschreibungen](funktionen/6.0/reihenfolge-der-template-ueberschreibungen.md).
- **Operatoren, Fetch-Funktionen und Template-Funktionen.** Sie werden in `autoloads/eztemplateautoload.php` und in
  `settings/template.ini.append.php` eingetragen. Der RAD-Assistent `/setup/templateoperator` schreibt alle drei.
  Referenz: [Template-Operatoren](anleitungen/smarty-templates-operatoren.md) und
  [Template-Engines](anleitungen/template-engines.md).
- **Pagelayout-Hooks.** Das sind Stellen im Pagelayout, an denen eine Extension Markup ergänzt, ohne es zu
  überschreiben: [Pagelayout-Hooks](funktionen/6.0/seitenlayout-hooks.md).
- **Icons.** Eine Extension kann ein eigenes Icon-Theme mitbringen:
  [Icon-Themes in Extensions](funktionen/6.0/icon-themes-in-extensions.md).

**Die Regeln der Administrationsdesigns.** Sie gelten für jedes Administrationsdesign (admin, admin3, admin4, admin4l)
und für die Administrationsseiten von Extensions:

- Kein `<style>`-Block und kein `style=""`-Attribut in einem Template. Alles CSS gehört in das `stylesheets/` des
  Designs, geladen über die Einstellungen, damit der Packer es bündelt.
- Verwenden Sie zuerst die gemeinsamen Komponenten: `.a4-card`, `.a4-btn`, `.a4-table`, `.a4-toolbar`,
  `.a4-filterbar` und die übrigen.
- Farben, Radius, Abstände und Schriften kommen nur aus den Tokens des Designs (`--a4-*`). Jedes neue Token bekommt in
  derselben Änderung einen Wert für den dunklen Modus.
- Prüfen Sie jede Seite im hellen und im dunklen Modus, bei 1440 px und bei 960 px mit 200 % Zoom.

Der Test `tests/tests/design/AdminStylingRulesTest.php` setzt die ersten Regeln in der CI durch. Die vollständige
Schnittstelle der Komponenten und Tokens: [Gestaltung von admin4](bc/6.0/admin4-gestaltung.md).

## 7. Inhalte, Workflows und Arbeit im Hintergrund

| Sie wollen | Wie | Referenz |
|---|---|---|
| Einen eigenen Feldtyp | Einen Datentyp: eine Klasse, die `eZDataType` erweitert, gefunden über `content.ini.append.php` (`[DataTypeSettings] ExtensionDirectories[]=<name>`); RAD `/setup/datatype` | [Datentypen](anleitungen/datentypen.md) |
| Dass beim Veröffentlichen, vor einer Anmeldung, an der Kasse ... etwas geschieht | Ein Workflow-Ereignis, an einen Auslöser gebunden; RAD `/setup/workflowevent` | [Workflows](anleitungen/workflows.md) |
| Regelmäßige Arbeit im Hintergrund | Einen Cronjob-Teil: `cronjobs/<teil>.php`, eingetragen in `cronjob.ini.append.php` (`ExtensionDirectories[]=<name>` und `[CronjobPart-<teil>]`) | [Cronjobs](anleitungen/cronjobs.md) |
| Inhaltsklassen, die mit der Extension ausgeliefert werden | Eine Inhaltsklasse, als Skript geschrieben; RAD `/setup/contentextension` | [Erweiterungspunkte](bc/6.0/rad-erweiterungspunkte.md) |

Einen Cronjob-Teil einer Extension listet `./console list cron` als `cron:<teil>`, und er läuft mit
`php runcronjobs.php <teil>`.

## 8. Konsolenbefehle

Skripte einer Extension werden automatisch gefunden und zu Konsolenbefehlen:

```
extension/<name>/bin/php/<skript>.php   → ext:<name>:<skript>
extension/<name>/bin/shell/<skript>.sh  → ext:<name>:sh:<skript>
```

```bash
./console list ext:myhello
./console ext:myhello:report --help
```

Neue Befehle werden als Klassen geschrieben (`Exponential\Runnable\Command`). Dann sind Hilfe, Optionen, Ausgabe und
Rückgabewerte dieselben wie bei den Befehlen des Kernels. Referenz:

- [Befehle, Cronjob-Teile und Modulansichten als Klassen](bc/6.0/cli-cronjob-und-view-abstraktionen.md);
- der Vertrag in [Befehle, Cronjob-Teile und Ansichten als Klassen](spezifikationen/6.0/ausfuehrbare-befehle-cronjobs-ansichten.md);
- [Die Konsole](bc/6.0/konsole.md).

## 9. Schnittstellen, Audit, Geheimnisse, Übersetzungen und Hilfe

- **GraphQL und REST.** Eine Extension ergänzt das GraphQL-Schema mit einer Klasse, die `expGraphQLSchemaExtension`
  implementiert, eingetragen in `[GraphQLSettings] SchemaExtensionList[]` ihrer `settings/graphql.ini.append.php`:
  Wurzelabfragen, Felder eines Orts und die Typen ihrer Datentypen ([GraphQL](funktionen/6.0/graphql.md#schema-erweiterungen)).
  Die REST-Schnittstelle ist die Extension ezprestapi ([REST API v1 und v2](anleitungen/rest-api-v1-und-v2.md)).
  Andere Systeme über diese Schnittstellen anzubinden, statt sie zu erweitern, beschreibt
  [Mit Exponential 6.0 integrieren](INTEGRATING.md).
- **Audit-Protokoll.** Eine Extension, die eigene Ereignisse aufzeichnet, registriert einen Zweig der Taxonomie:
  - den Zweig: `audit.ini.append.php` `[AuditEventSettings] Branches[<name>]=<klasse>`, eine Klasse, die
    `expAuditTaxonomyBranch` implementiert;
  - auf Wunsch eine eigene Quelle (`[AuditSourceSettings] Sources[]`).

  Referenz: [Das Audit-Protokoll](funktionen/6.0/pruefprotokoll.md) und [die Audit-Anleitung](bc/6.0/audit.md).
- **Geheimnisse.** Speichern Sie nie ein Passwort oder einen Schlüssel in einer Einstellung. Behalten Sie den Namen
  eines Geheimnisses und fragen Sie den Geheimnisspeicher nach seinem Wert: `expSecrets::get( $name )`, oder
  `expSecrets::required( $name, $what )`, wenn das Werkzeug ohne ihn nicht arbeiten kann. Ein Administrator trägt den
  Wert unter **Setup > Geheimnisse** ein. Referenz: [Der Geheimnisspeicher](funktionen/6.0/geheimnisspeicher.md).
- **Übersetzungen.**
  - Jeder Text eines Templates läuft über `|i18n( '<kontext>' )`, jeder Text in PHP über `ezpI18n::tr()`.
  - Die Extension liefert `translations/<locale>/translation.ts` und trägt sich in `site.ini.append.php` ein
    (`[RegionalSettings] TranslationExtensions[]=<name>`).
  - Die Administration ist in 156 Sprachen übersetzt. Eine Extension des Produkts liefert mindestens Englisch und
    Deutsch.

  Referenz: [Übersetzungen und Sprachen](funktionen/6.0/uebersetzungen-und-sprachen.md), [Sprachen](sprachen.md).
- **Hilfeseiten.** Die Seiten in `extension/<name>/doc/` und ihre `README*`, `CHANGELOG*`, `LICENSE*` und `INSTALL*`
  erscheinen in dieser Hilfe, sobald die Extension aktiv ist, mit der Seitenkennung `ext/<name>/<pfad>`. Gezeigt werden
  nur Dateien, die das Repository der Extension verfolgt oder in seiner `share/filelist.md5` auflistet. Referenz:
  [Das Hilfesystem](funktionen/6.0/hilfesystem.md).

## 10. Die Website-Werkzeugleiste und Exponential Layouts

- **Die Website-Werkzeugleiste (expwt).** Eine Extension ergänzt ein Panel, eine Serveraktion, einen hervorgehobenen
  Bereich oder eine Suchquelle in ihrer eigenen `settings/expwt.ini.append.php`, unter `[RegistrySettings]`
  (`PanelList[]`, `ActionList[]`) und je einem Block `[Panel_<id>]` oder `[Action_<extension>.<name>]`:
  - ein Panel nennt seine Beschriftung, sein Skript im `design/<design>/javascript/` der Extension und wer es sieht
    (eine expwt-Funktion und Kernel-Richtlinien);
  - eine Aktion nennt ihre Klasse, die `expwtActionHandler` implementiert und die Kernel-Richtlinie dessen prüft, was
    sie ändert;
  - die Werkzeugleiste gewährt selbst nie etwas.

  Der vollständige Vertrag steht im Kopf von `extension/expwt/classes/expwtregistry.php`.
- **Exponential Layouts.** Layout-Typen, Zonen und Blockdefinitionen sind Einstellungen der
  `explayouts.ini.append.php` (`[LayoutType_*]`, `[BlockDefinition_*]`), und das Aussehen eines Blocks ist ein
  Template im Design der Extension. Referenz: [Layouts](bc/6.0/layouts.md).

## 11. Tests und CI

- **Schreiben Sie Tests, die keine Datenbank brauchen.** Die Testsuite des Kernels führt die meisten Tests ohne
  Datenbank aus: einfache Arrays hinein, einfache Arrays heraus, wo nötig mit einer nachgebildeten Datenbankklasse. Die
  CI führt sie bei jedem Push aus, auf PHP 8.1 bis 8.5, und prüft Syntax und Start auf PHP 7.4 und 8.0.
- **Prüfen Sie vor dem Push:** `php -l` für jede geänderte PHP-Datei, `xmllint --noout extension.xml`, dann die Tests
  Ihrer Extension.
- **Die CI des Kernels**, wie Sie einen Fehler auf Ihrem Rechner nachstellen, und die Regeln für Commit-Nachrichten:
  [CI](bc/6.0/ci.md). PHPUnit-Versionen und Attribute: [PHPUnit 13](bc/6.0/phpunitv13.md).

## 12. Veröffentlichen

Eine Veröffentlichung ist eine Nummer in den Dateien der Extension, ein Commit und ein Tag. **Ein veröffentlichter Tag
ist endgültig.** Packagist liest ihn einmal und speichert ihn, darum wird ein Tag nie verschoben, gelöscht oder neu
erstellt. Ein Fehler wird mit der nächsten Version behoben.

1. **Wählen Sie die Nummer nach dem, was schon veröffentlicht ist:** `git fetch --tags`, dann
   `git tag -l 'v*' --sort=version:refname | tail -5`, und prüfen Sie die GitHub-Releases. Sortieren Sie nach
   Version, nicht nach Namen: Nach `0.1.9` kommt `0.1.10`.
2. **Schreiben Sie sie in beide Dateien** (`'Version'` in `ezinfo.php`, `<version>` in `extension.xml`, ohne das `v`)
   und in `composer.json`, wenn sie eine Version hat. Liefert die Extension eine `share/filelist.md5`, erneuern Sie sie
   im selben Commit.
3. **Committen Sie, und taggen Sie dann diesen Commit**, pushen Sie den Branch und den Tag und legen Sie das Release an.
4. **Prüfen Sie Packagist.** Erscheint die neue Version nicht, fehlt der Webhook des Repositorys zu Packagist.

Website-Pakete schalten Extensions nur ein; sie tragen keine Kopien von Extensions mehr
([Website-Pakete ohne Extension-Pakete](bc/6.0/seitenpakete-ohne-erweiterungspakete.md)). Der ganze Ablauf einer
Veröffentlichung: [Extensions](anleitungen/extensions.md), Abschnitt 7, und [Pakete](anleitungen/pakete.md).

## 13. Fehlerbehebung

| Symptom | Ursache und Behebung |
|---|---|
| `Class '...' not found` | Die Autoloads sind veraltet: `php bin/php/ezpgenerateautoloads.php -e`, dann `./console exp:velocity deploy`. `php bin/php/checkclasses.php --quiet-ok` nennt jede Klasse, die sich nicht laden lässt. |
| Die Adresse des Moduls antwortet „Modul nicht gefunden“ | Die Extension ist nicht aktiv, oder in `settings/module.ini.append.php` fehlt `ExtensionRepositories[]=<name>`. |
| Die Seite ist leer oder zeigt das Layout ohne Inhalt | Das Template wird nicht gefunden: Prüfen Sie den Pfad unter `design/<design>/templates/` und `DesignExtensions[]=<name>`, und leeren Sie nach einer neuen `.tpl` den Cache der Template-Überschreibungen. |
| Es funktioniert unter Apache, aber nicht auf dem Port von Velocity | Die Worker von Velocity behalten die Klassen, die sie beim Start geladen haben: Rollen Sie mit `./console exp:velocity deploy` aus (es startet Velocity neu). |
| Eine Einstellung der Extension wirkt nicht | Eine Extension weiter vorn in der Liste oder `settings/override/` setzt sie ebenfalls: `./console exp:ini where <datei>/<block>/<einstellung>` nennt den Gewinner. |
| Die Tabellen der Extension fehlen | Führen Sie `./console exp:upgrade` aus; der Schritt `6.0.15-active-extension-tables` legt sie aus `share/db_schema.dba` an. |
| Ihr Link fehlt im Menü | Dem Benutzer fehlt eine Richtlinie aus `PolicyList_<schluessel>[]`, oder das Menü nutzt `CollapseAfter` und der Link liegt unter **Mehr**. |
| Ein neues Stylesheet oder Skript wirkt nicht | Rollen Sie mit `--packer` aus und laden Sie die Seite ohne den Browser-Cache neu. |
| Die Info-Seite zeigt keine Version | `ezinfo.php` und `extension.xml` stimmen nicht überein, oder `extension.xml` fehlt `<metadata><version>`. |
| Die Prüfung auf `/setup/rad` meldet ein Problem | Sie nennt die Einstellung und die Klasse, die nicht funktionieren kann; korrigieren Sie den Klassennamen oder erzeugen Sie die Autoloads neu. |

Mehr:

- [Extensions](anleitungen/extensions.md), die Schritt-für-Schritt-Anleitung zum Finden, Bauen und Veröffentlichen von
  Extensions;
- das Buch [Extension-Entwicklung](hilfe/erweiterungsentwicklung/README.md);
- [Eine Installation reparieren](bc/6.0/reparatur.md);
- [Mit Exponential 6.0 integrieren](INTEGRATING.md), um andere Systeme über die Schnittstellen anzubinden, statt den Code zu erweitern.
