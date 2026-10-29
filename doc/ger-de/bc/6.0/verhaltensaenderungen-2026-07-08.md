# Verhaltensänderungen vom Juli und August 2026 (Entwicklung von 6.0.15)

Lesen Sie diese Seite, bevor Sie eine Installation auf die Änderungen aktualisieren, die zwischen dem 1. Juli und dem
31. August 2026 vorgenommen wurden. Sie ist die Checkliste für das Upgrade dieses Zeitraums: was kaputtgehen kann, die
neuen Einstellungen und die Korrekturen, die Ihnen auffallen könnten. Das Release 6.0.15 hatte damals noch keinen Tag;
alles Folgende gehört zur Linie 6.0.15. Für September und Oktober siehe
[Verhaltensänderungen vom 16. bis 30. September 2026](verhaltensaenderungen-2026-09b.md) und
[Verhaltensänderungen vom Oktober 2026](verhaltensaenderungen-2026-10.md). Die Geschichten der Monate sind
[Juli](../../geschichte/2026/2026-07.md) und [August](../../geschichte/2026/2026-08.md).

## Kurze Checkliste

1. Stellen Sie sicher, dass jedes Benutzerkonto eine Zeile in `ezuser_setting` hat
   ([wie Sie die finden, die keine haben](../../spezifikationen/6.0/sicherheitshaertung-2026-08.md#f-06-konten-finden-die-sich-nicht-anmelden-können)).
2. Wenn Sie die Erweiterungen `bciconextensions` oder `bciconextensions_share_icons` verwenden, entfernen Sie sie aus
   `ActiveExtensions[]`.
3. Wenn Sie Templates haben, die sich auf selbstschließende XHTML-Tags oder `type="text/css"` verlassen, lesen Sie
   „HTML5-Markup in den Kernel-Designs“.
4. Prüfen Sie Ihre `override.ini`-Dateien, wenn manche Blöcke eine `Priority` haben und andere nicht.
5. Erzeugen Sie die Autoloads neu und leeren Sie alle Caches:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/ezcache.php --clear-all --allow-root-user
   ```

## Änderungen, die etwas kaputtmachen können

### Konten ohne Zeile in `ezuser_setting` können sich nicht anmelden (16. August)

**Was sich geändert hat.** Bei der Anmeldung behandelte `kernel/classes/datatypes/ezuser/ezuser.php` einen Benutzer
ohne Einstellungszeile als aktiviert. Jetzt wird er als deaktiviert behandelt. In der Administration angelegte Konten
haben die Zeile immer; Konten, die per SQL, Paketen oder Migrationen angelegt wurden, vielleicht nicht.

**So prüfen und beheben Sie es.** Finden und reparieren Sie solche Konten mit den Abfragen in der
[Sicherheitsspezifikation](../../spezifikationen/6.0/sicherheitshaertung-2026-08.md#f-06-konten-finden-die-sich-nicht-anmelden-können).

### Die Reihenfolge der Overrides folgt `Priority` (12. Juli)

**Was sich geändert hat.** Die Template-Engine sortiert Override-Blöcke nach dem Schlüssel `Priority` von
`override.ini`: Die niedrigste Zahl wird zuerst geprüft, und ein Block ohne `Priority` kommt zuletzt. Ein im
[Template-Editor](../../funktionen/6.0/template-editor-ueberschreibungen.md) angelegter Block erhält `Priority=0`.

**Wer betroffen ist.** Mischen Sie von Hand geschriebene Blöcke ohne `Priority` mit Blöcken aus dem Editor, gewinnen
jetzt die Blöcke des Editors.

**So beheben Sie es.** Fügen Sie den von Hand geschriebenen Blöcken `Priority` hinzu, um die gewünschte Reihenfolge
festzulegen.

### HTML5-Markup in den Kernel-Designs (18. Juli)

**Was sich geändert hat.** Die Templates von `design/standard` und das von PHP in `lib` und `kernel` geschriebene
Markup (der Template-Operator `nl2br`, die Debug- und Statistikausgabe, der Web-Einstiegspunkt des Kernels), in
`ezjscore` und in `ezoe` bestehen jetzt HTML-Validatoren: `<br />` wurde zu `<br>`, `<input ... />` zu `<input ...>`,
und `<style type="text/css">` zu `<style>`. Browser behandeln beide Formen gleich.

**Wer betroffen ist.** Nur Code, der Markup byte-genau vergleicht (Snapshot-Tests) oder es als XML nachbearbeitet. Ihre
eigenen Overrides werden vom Kernel nicht geändert.

### jQuery 3.7.1 in ezjscore (16. August)

Seiten, die `ezjsc::jquery` laden, erhielten jQuery 3.7.1 und jQuery Migrate 3.4.1 (stummgeschaltet). Code, der noch
das Verhalten von jQuery 1.x braucht, funktioniert meist über Migrate. Im Oktober wurde das durch jQuery 4 abgelöst;
siehe den [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md) und [YUI-Entfernung](yui-entfernung.md).

### Kickstart und der Setup-Assistent

- `kickstart.ini` wird nicht mehr aktiv ausgeliefert. Das Beispiel ist `kickstart.ini--example`, die Vorlage
  `kickstart.ini-dist`. Wenn Sie sich auf ein ausgeliefertes `kickstart.ini` verlassen haben, kopieren Sie das Beispiel
  nach `kickstart.ini` und bearbeiten Sie es. Siehe [Kickstarter](../../funktionen/6.0/kickstarter-cli.md).
- Eine Kickstart-Installation legt immer die Siteaccesses `site` und `admin` an.
- Eine neue Installation verwendet standardmäßig `eng-US` und die Exponential-Inhalte aus `share/db_data.dba`;
  bestehende Installationen werden nicht angefasst. Siehe
  [Standardwerte einer sauberen Installation](../../funktionen/6.0/standardwerte-einer-sauberen-installation.md).

### Der Autoload-Generator

`kernel/private/classes/ezautoloadgenerator.php` vergleicht reale Pfade, und sein Ausschlussfilter (`var`, `settings`,
`bin`, `autoload`, `tmp`, `lib/ezc` ...) endet jetzt an einer Verzeichnisgrenze: Ein Ordner namens `settings_backup`
wird vom Filter `settings` nicht mehr übersprungen. Die Protokollzeile „Scan complete“ ist weg, sodass der Generator so
still bleibt wie vorgesehen. Fehlt eine Klasse, nachdem Sie Erweiterungsordner umbenannt oder verlinkt haben, führen Sie
`php bin/php/ezpgenerateautoloads.php -e` erneut aus.

## Neue Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich | Bedeutung |
|---|---|---|---|---|---|
| `site.ini` | `[TemplateSettings]` | `ShowTemplatePathComments` | `disabled` | global/Siteaccess | START/STOP-Kommentare, die jedes Template nennen. [Funktion](../../funktionen/6.0/template-pfad-kommentare.md). |
| `site.ini` | `[ExtensionSettings]` | `AdditionalExtensionDirectories[]` | leer | global | Weitere Wurzelverzeichnisse für Erweiterungen. [Funktion](../../funktionen/6.0/zusaetzliche-extension-verzeichnisse.md). |
| `site.ini` | `[SiteSettings]` | `EzInstallationName`, `ProductionInstallationList[]` | nicht gesetzt | global | Filter nach dem Namen der Installation. [Funktion](../../funktionen/6.0/installationsname-in-seiten.md). |
| `ezscript.ini` | `[eZScriptSettings]` | `RootDelay` | `10` | global | Sekunden, die ein als root mit `--allow-root-user` ausgeführtes Skript wartet, damit Sie Strg-C drücken können. `0` entfernt die Wartezeit. |
| `icon.ini` | `[ExtensionSettings]` | `IconExtensions[]` | leer | global/Siteaccess | Erweiterungen, deren Ordner `icons/` durchsucht wird. [Funktion](../../funktionen/6.0/icon-themes-in-extensions.md). |
| `menu.ini` | `[Leftmenu_design]` | `Links[template_editor]` | `visual/templatelist` | global | Der Template-Editor im Menü Design. |
| `transform.ini` | `[search]` | `Commands[]=lowercase` | hinzugefügt | global | Die Umwandlung in Kleinbuchstaben in der Gruppe search. |

Beispiel: keine Wartezeit für root in einem automatisierten Deployment.

```ini
# settings/override/ezscript.ini.append.php
[eZScriptSettings]
RootDelay=0
```

## Korrekturen, die Ihnen auffallen könnten

- **Ausgabe der Konsole:** Jeder Eintrag von `bin/php/console` zeigt eine Beschreibung; `ezpm` ist ein Konsolenbefehl
  ([ezpm](../../funktionen/6.0/ezpm-paketmanager-cli.md)).
- **PHP 8.0 bis 8.5 (PHP 7.4 ohne Support nutzbar):** String-Offsets mit geschweiften Klammern (Datentyp ISBN13, WebDAV-Backend);
  `#[AllowDynamicProperties]` an `eZINI`, `eZPersistentObject` und `eZWorkflowProcess` (PHP 8.2); nullable
  Array-Typhinweise (`generateUrl`, `fetchRelatedObjects`); ein Integer-Cast für die nächste Bestellnummer (PHP 8.4);
  der entfernte Aufruf von `imagedestroy()` (PHP 8.5); Absicherungen gegen Null-Werte in `eZSiteAccess::load`,
  `eZCharsetInfo`, `eZMail`, den GD-Farbfunktionen, `eZLocale`, dem Content-Cache-Manager, der XHTML-Ausgabe
  eingebetteter Bilder und den URL-Alias-Caches.
- **INI-Dateien:** Das Speichern im Round-Trip-Modus lässt jeden nicht berührten Schlüssel so, wie er auf der
  Festplatte stand, einschließlich bloßer Zurücksetzungszeilen `Key[]`. Aktivieren von Erweiterungen und Installieren
  von Paketen formatieren nicht mehr unbeteiligte Einstellungen um (16. August, `0a0f4f876b`). Siehe
  [eZINI behält Kommentare](ezini-behaelt-kommentare.md).
- **Der Debug-Handler** beachtet `error_reporting` (`3ce2a1e76c`). Die Übersetzungsdatei eng-US wird nicht gesucht,
  was das Rauschen „missing translation“ beseitigt (`e3c8d078f4`).
- **SQL der Berechtigungen:** Eine leere Liste von Klassen- oder Sektionseinschränkungen erzeugt kein ungültiges SQL
  mehr (`acb8e002c5`).
- **Importiertes Ibexa-Docbook:** `ezembed`, `para` mit einer CSS-Klasse und `literallayout` werden in XHTML
  gerendert; die Whitelist der Klassen wird nur für Tags des eZXML-Schemas durchgesetzt (`5857c5baa8`, `d7bebcb39b`).
- **Bildaliase:** Eine fehlende Quelldatei ist ein Hinweis, der das Objekt, das Attribut, den fehlenden Alias und die
  vorhandenen Aliase nennt, kein fataler Fehler (`52277db85f`).
- **Fehler „Objekt nicht gefunden“** nennen die ID sowie Datei und Zeile des Aufrufers (`c7a5eac571`).
- **SQLite:** Debug-Meldungen der Verbindung sind lesbar und erscheinen nur bei eingeschalteter SQL-Ausgabe; der
  Schema-Generator erzeugt `INTEGER PRIMARY KEY AUTOINCREMENT` und keine zweite Primärschlüssel-Einschränkung
  (`dfba7274e8`, `24c03393db`, `9678728ccc`).
- **MongoDB:** Der Treiber meldet Fehler über `eZDebug::writeError()` mit dem Präfix `expMongoDB`, nicht über
  `error_log` (`888297a0ca`).
- **Paketwerkzeuge:** siehe [ezpm](../../funktionen/6.0/ezpm-paketmanager-cli.md) zur Installationssperre,
  zur verzögerten Indizierung und zu gebündelten Commits.

## Kleinere Änderungen, erklärt

Jede davon hat einen Commit im [Ledger](../../geschichte/aenderungsverzeichnis/exponential-root.md) und eine kleine, sichtbare
Wirkung.

| Änderung | Was Sie sehen | Was zu tun ist |
|---|---|---|
| Beschreibungen der Skripte in der Konsole (`4ce57a7224`) | `./console list` zeigt eine Zeile pro Skript. Die Konsole liest sie aus dem Schlüssel `'description'` des Arrays, das an `eZScript::instance()` übergeben wird (Strategie 1 im Kommentarblock von `bin/php/console`), oder aus einem Docblock-Tag `@description`. | Geben Sie Ihren eigenen Skripten einen Schlüssel `'description'` (oder `// @description ...`), dann erscheinen sie in der Liste. Prüfen: `./console list ezpm`. |
| `RootDelay` (`11095f902f`) | Ein als root-Benutzer des Betriebssystems mit `--allow-root-user` ausgeführtes Skript gibt „With great power comes great responsibility.“ aus und wartet `RootDelay` Sekunden, damit Sie Strg-C drücken können (`kernel/classes/ezscript.php`). | `0` in `ezscript.ini [eZScriptSettings]` entfernt die Wartezeit in Deployments; Standard `10`. |
| Tab Layouts in der Knotenansicht der Administration (`8df2654e3f`) | Die Fenstersteuerung von `design/admin3` erhält einen zusätzlichen Tab neben Anordnung; `design/admin/javascript/node_tabs.js` kennzeichnet die Fenstersteuerung mit der Klasse `layouts-active`, solange der Tab `node-tab-layouts` offen ist. Den Tab selbst liefern die Layout-Erweiterungen. | Nichts; siehe [Exponential Layouts](layouts.md). |
| Rücknahme beim Skript zur Aktualisierung der Nice URLs (`9c4946075d`) | `bin/php/updateniceurls.php` ist wieder auf seiner früheren, stabilen Version, nachdem sich eine während der Tests vorgenommene Änderung als instabil erwiesen hat. | Nichts. |
| Autoload-Generator (`f46c08c18e`, `62c14b36dd`) | Reale Pfade werden verglichen, und der Ausschlussfilter endet an einer Verzeichnisgrenze; die Zeile „Scan complete“ ist weg. | Oben unter „Der Autoload-Generator“ beschrieben. |
| Templates des Themes media (`c013093b52`) | Templates des Themes media sichern `false` und fehlende Werte ab; das Template für den Google Tag Manager lässt sich parsen. | Nichts; das Theme ist seit dem 6. August ein gewöhnliches Verzeichnis des Repositorys. |

## Composer und Erweiterungen

Die `composer.json` im Wurzelverzeichnis hat sich wie folgt geändert (Vorschläge stehen nur im Block `suggest` und
werden nie installiert):

| Datum | Änderung |
|---|---|
| 12. Juli | `git_manager ~2.0.1` verlangt |
| 19. Juli | Vorschläge für `owsimpleoperator`, `adminaid`, die Erweiterung für private Nachrichten, `hcaptcha`, `syndication` |
| 20. Juli | `sevenx_dse ~1.0.0` und `syndication ~1.2.0` verlangt; `sevenx_valkey_cache` vorgeschlagen |
| 21. Juli | `powercontent` verlangt |
| 30. Juli | die explayouts-Pakete (12), die expsite-Pakete, `expquery-translator` und `sevenx-themes-media`, alle `~1.0.0`, als Laufzeitanforderungen deklariert |
| 31. Juli | `powercontent ~2.0.0`; `exppayment-stripe` aus den Anforderungen entfernt |
| 15. August | `exp_enhanced_link ~1.0.1` (das Theme media braucht es) |
| 22. August | `eztags ~2.3.3` und `cjw_newsletter ~4.0.0.0` verlangt |
| 31. August | `ngclasslist ~1.1` verlangt (der Datentyp der Klasse `ng_menu_item`) |

Die Tabelle ist die Geschichte von Juli und August. Die Versionsbedingungen haben sich seitdem weiterentwickelt (in der
`composer.json` vom 2. Oktober 2026 zum Beispiel `git_manager ~2.0.14`, `eztags ~2.4.11`, `cjw_newsletter ~4.1.16`).
Die aktuellen listen Sie so auf:

```bash
grep -n '"se7enxweb/' composer.json
```

Führen Sie nach dem Übernehmen dieser Änderungen Composer selbst so aus, wie Sie es immer tun, dann
`php bin/php/ezpgenerateautoloads.php -e`.

## Hinweis zur README

Der Kopf der README erklärt, dass eine heruntergeladene Kopie von Exponential 6.0 `composer install` braucht, bevor sie
läuft (`04d17780ae`, `84ebe2f59f`).

## Verwandte Seiten

- [Sicherheitsspezifikation vom August 2026](../../spezifikationen/6.0/sicherheitshaertung-2026-08.md) und die
  [CI-Spezifikation](../../spezifikationen/6.0/kontinuierliche-integration.md)
- [Konsole](konsole.md), [Unterstützung für PHP 8](php8.md), [Kickstarter-CLI](kickstartercli.md), [YUI-Entfernung](yui-entfernung.md)
- Funktionen des Zeitraums: [ezpm](../../funktionen/6.0/ezpm-paketmanager-cli.md),
  [Template-Editor](../../funktionen/6.0/template-editor-ueberschreibungen.md),
  [Kommentare mit Template-Pfaden](../../funktionen/6.0/template-pfad-kommentare.md),
  [Kickstarter](../../funktionen/6.0/kickstarter-cli.md), [Standardwerte einer sauberen Installation](../../funktionen/6.0/standardwerte-einer-sauberen-installation.md),
  [Ein Benutzerpasswort zurücksetzen](../../funktionen/6.0/benutzerpasswort-zuruecksetzen.md),
  [Redis- und Valkey-Caches](../../funktionen/6.0/valkey-cache-hooks.md),
  [Erweiterungsliste](../../funktionen/6.0/extension-liste-und-downloads.md),
  [Zusätzliche Erweiterungsverzeichnisse](../../funktionen/6.0/zusaetzliche-extension-verzeichnisse.md),
  [Icon-Themes](../../funktionen/6.0/icon-themes-in-extensions.md),
  [Name der Installation](../../funktionen/6.0/installationsname-in-seiten.md),
  [jQuery 4 und YUI](../../funktionen/6.0/jquery4-und-entfernung-von-yui.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Verhaltensänderungen vom 16. bis 30. September 2026 (nächste)](verhaltensaenderungen-2026-09b.md)
