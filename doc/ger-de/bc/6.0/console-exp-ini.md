# Einstellungen auf der Kommandozeile ändern: `exp:ini`

Lesen Sie diese Seite, wenn Sie INI-Einstellungen von Hand oder aus Deploy-Skripten ändern oder Einstellungen aus
`settings/override` in eine Erweiterung verschieben möchten. Ab Exponential 6.0.15 liest und ändert ein Befehl die
Einstellungen jedes Geltungsbereichs (Scope) einer Installation: des globalen Overrides, jedes Siteaccess, jeder
Erweiterung und jedes Siteaccess einer Erweiterung. Er ändert nur die Zeilen der Einstellung, behält Kommentare,
Besitzer und Modus, sichert die Datei vorher und leert den INI-Cache.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Neuer Befehl `exp:ini` (`bin/php/ini.php`) mit den Aktionen `get`, `set`, `add`, `rem`, `clear`, `toggle`, `copy`, `move`, `move-all`, `where`, `list`, `scopes`, `actions`. Neue Registry `settings/ini.ini [IniCommandSettings]` für Aktionen und Scope-Provider, die Erweiterungen hinzufügen. |
| Wer betroffen ist | Niemand ist zu einer Änderung gezwungen. Skripte, die INI-Dateien mit `sed` oder `eZINI::save()` bearbeiten, können darauf umsteigen. Benutzer von Velocity: Eine Änderung wirkt dort erst nach `exp:velocity restart`. |
| Wie Sie es prüfen | `php bin/php/console exp:ini --help` |
| Wie Sie es beheben | Nichts zu beheben. Schreiben Sie Einstellungen mit `exp:ini set ... <scope>`; sehen Sie sich jede Änderung vorher mit `--dry-run` an. |

## Schnellstart

```bash
php bin/php/console exp:ini set site.ini/SiteSettings/SiteName "My site" global
php bin/php/console exp:ini rem site.ini/SiteSettings/SiteName siteaccess:admin
php bin/php/console exp:ini toggle site.ini/ContentSettings/ViewCaching global        # enabled <-> disabled
php bin/php/console exp:ini toggle site.ini/TemplateSettings/Debug siteaccess:admin   # true <-> false
php bin/php/console exp:ini where site.ini/SiteSettings/SiteName                      # which file wins
```

`php bin/php/ini.php ...` ist derselbe Befehl. Fügen Sie als root `--allow-root-user` hinzu, wie bei jedem Skript. Die
Beispiele unten schreiben kurz `exp:ini`.

Der Befehl verwendet nicht `eZINI::save()`, das die ganze Datei neu schreibt. Jede Datei wird Zeile für Zeile
geändert: Kommentare, Leerzeilen und die Reihenfolge der Datei bleiben, wie sie sind, und nur die Zeilen der geänderten
Einstellung ändern sich. Der Befehl ist so gebaut, wie Befehle ab 6.0.15 geschrieben werden
([cli-cronjob-und-view-abstraktionen.md](cli-cronjob-und-view-abstraktionen.md)). Seine Aktionen sind Klassen, und eine
Erweiterung kann eigene Aktionen und eigene Scopes hinzufügen. Die RAD-Übersicht zählt sie.

| Teil | Wo |
|---|---|
| Einstiegspunkt | `bin/php/ini.php` (`exp:ini` in der Konsole, beschrieben durch die `@description` seiner Klasse) |
| Befehl | `Exponential\Command\Kernel\Ini`, `kernel/private/classes/commands/ini.php` |
| Dispatcher, Kontext, Aktionen | `kernel/classes/ini/actions/`: `expIniCommand`, `expIniCommandContext`, `expIniAction`, `expIniActionBase`, `expIniAction<Name>`, `expIniMover`, `expIniActionRegistry` |
| Einstellungseditor (Lesen und Schreiben der Dateien) | `kernel/classes/ini/`: `expIniEditor`, `expIniScope`, `expIniLocator`, `expIniWriter`, die Scope-Provider |
| Registry | `settings/ini.ini` `[IniCommandSettings]` |

Alle Beispiele unten wurden ausgeführt, entweder auf einer temporären Kopie eines Einstellungsbaums (`--root`, siehe
[Tests](#tests)) oder auf einer Referenzinstallation mit `--dry-run`. Die Ausgabe ist so gezeigt, wie sie ausgegeben
wurde, mit ersetzten installationsspezifischen Namen.

## Einstellungen

Eine Einstellung ist `<file>/<Block>/<Variable>`, und die Endung `.ini` ist optional. Jede Aktion nimmt außerdem diese
Formen an:

| Form | Beispiel |
|---|---|
| Schrägstriche | `site.ini/SiteSettings/SiteName` |
| Doppelpunkt und Punkt | `site.ini:SiteSettings.SiteName` oder `site:SiteSettings.SiteName` |
| im Stil von eZINI, in Anführungszeichen oder als drei Wörter | `"site.ini [SiteSettings] SiteName"`, `site.ini [SiteSettings] SiteName` |
| ein Array | `.../ActiveExtensions[]` (add, rem mit einem Wert, clear) |
| ein Hash-Eintrag | `.../RelatedSiteAccessList[admin]` (set, get, rem) |

Beginnt ein Wert mit `-`, setzen Sie `--` davor. Alles nach `--` wird so genommen, wie es ist:
`exp:ini set site.ini/MySettings/Offset -- -1 global`.

## Scopes

| Scope | Geschriebene Datei |
|---|---|
| `global` (oder `override`) | `settings/override/<file>.ini.append.php` |
| `siteaccess:<sa>`, oder nur `<sa>` | `settings/siteaccess/<sa>/<file>.ini.append.php` |
| `extension:<ext>` | `extension/<ext>/settings/<file>.ini.append.php` |
| `extension:<ext>:siteaccess:<sa>` | `extension/<ext>/settings/siteaccess/<sa>/<file>.ini.append.php` |
| `default` | `settings/<file>.ini`, die eigenen Standardwerte des Kernels: abgelehnt, außer mit `--allow-default` |
| der Scope eines Providers | was ein in `ini.ini` registrierter Scope-Provider hinzufügt (siehe [Einen Scope hinzufügen](#einen-scope-hinzufügen)) |

Ein unbekannter Siteaccess wird mit dem Exit-Code 3 abgelehnt. Eine Erweiterung, die noch nicht existiert, ist ein
Scope ohne Verzeichnis. Ein Schreiben dorthin wird abgelehnt, außer durch `move --create-extension`.

```
$ exp:ini scopes
global                                       global               settings/override
default                                      default              settings  (not writable)
siteaccess:admin                             siteaccess           settings/siteaccess/admin
siteaccess:site                              siteaccess           settings/siteaccess/site
extension:fixtureext                         extension            extension/fixtureext/settings
extension:fixtureext:siteaccess:admin        extension-siteaccess extension/fixtureext/settings/siteaccess/admin
6 scopes; providers: expIniCoreScopeProvider, expIniExtensionScopeProvider
```

## Die Aktionen

`exp:ini --help` führt sie auf, und `exp:ini help <action>` (oder `exp:ini <action> --help`) erklärt eine davon.
`exp:ini actions` führt auch die Aktionen auf, die Erweiterungen registrieren.

| Aktion | Tut |
|---|---|
| `get <setting> [scope]` | der wirksame Wert, oder der Wert, den die Datei eines Scopes angibt |
| `set <setting> <value> <scope>` | setzt eine einfache Variable oder einen Hash-Eintrag und legt bei Bedarf Datei und Block an |
| `add <setting>[] <value> <scope>` | hängt an ein Array an; ein Wert, den die Datei bereits aufführt, wird nicht zweimal hinzugefügt |
| `rem <setting> <scope>`, `rem <setting>[] <value> <scope>` | entfernt eine Variable, einen Array-Wert oder einen Hash-Eintrag (Alias `remove`) |
| `clear <setting>[] <scope>` | schreibt die Zurücksetzungszeile `Variable[]` des Arrays |
| `toggle <setting> <scope>` | enabled/disabled, true/false, yes/no, on/off, 1/0, unter Beibehaltung der Schreibweise der Einstellung |
| `copy <setting> <from> <to>` | kopiert einen Wert, oder ein ganzes Array als dessen Zurücksetzungszeile gefolgt von den Werten |
| `move <file>[/<Block>] <from> <to>` | verschiebt Blöcke zwischen Scopes, zum Beispiel in eine Erweiterung ([siehe unten](#einstellungen-in-eine-erweiterung-verschieben)) |
| `move-all <from> <to>` | verschiebt jede Datei eines Scopes |
| `where <setting> [siteaccess]` | jede Datei, die sie setzt, in der Ladereihenfolge, und der wirksame Wert |
| `list <file>[/<Block>] [scope]` | Blöcke, oder die Variablen eines Blocks |
| `scopes [kind]`, `actions` | was diese Installation hat |

### set, get, rem

```
$ exp:ini set site.ini/SiteSettings/SiteName "My site" global
Set site.ini/SiteSettings/SiteName in global: settings/override/site.ini.append.php
Backup: var/backup/ini/20261002-120814/settings/override/site.ini.append.php
INI cache: not cleared (--root): clear the ini cache of that installation
Done

$ exp:ini get site.ini/SiteSettings/SiteName global
My site

$ exp:ini set site.ini/SiteSettings/SiteName "Admin" siteaccess:admin --dry-run
--- a/settings/siteaccess/admin/site.ini.append.php
+++ b/settings/siteaccess/admin/site.ini.append.php
@@ -2,4 +2,7 @@

 [TemplateSettings]
 Debug=true
+
+[SiteSettings]
+SiteName=Admin
 */ ?>
Dry run: set site.ini/SiteSettings/SiteName in siteaccess:admin, nothing written

$ exp:ini rem site.ini/SiteSettings/SiteName siteaccess:admin
Remove site.ini/SiteSettings/SiteName in siteaccess:admin: settings/siteaccess/admin/site.ini.append.php
...
$ exp:ini rem site.ini/SiteSettings/SiteName siteaccess:admin
Not found: site.ini/SiteSettings/SiteName is not set in siteaccess:admin          (exit 2)
```

`rem` ändert nur die Datei des Scopes, den es erhält. Der Wert, den andere Dateien angeben, bleibt, und `where` zeigt
ihn. Ein `rem` legt nie eine Datei an. Es lässt den Blockkopf stehen, auch wenn der Block leer ist.
`rem <file>/<Block> <scope>` entfernt einen Block, der keine Einstellungen mehr hat. Einer, der noch Einstellungen hat,
wird abgelehnt, außer mit `--force`.

Diese Beispiele liefen auf einem temporären Wurzelverzeichnis, daher wurde der INI-Cache nicht geleert. Auf einer
Referenzinstallation gab der umkehrbare Smoke-Test (`set`, `toggle`, `rem` von `site.ini/ExpIniSmokeTestB/Probe` in
`global`, dann `rem site.ini/ExpIniSmokeTestB global`) nach jedem Schreiben Folgendes aus:

```
INI cache: cleared tag ini: 5 caches
Hint: PHP-FPM reads the change on its next request (ini cache cleared); Velocity workers keep the settings of their warm-up: exp:velocity restart to apply it there.
```

Danach war `settings/override/site.ini.append.php` Byte für Byte, was sie vorher gewesen war, mit demselben Besitzer und
Modus.

### toggle

```
$ exp:ini toggle site.ini/DebugSettings/DebugOutput global
site.ini/DebugSettings/DebugOutput: Enabled -> Disabled
$ exp:ini toggle site.ini/TemplateSettings/Debug siteaccess:admin
site.ini/TemplateSettings/Debug: true -> false
$ exp:ini toggle site.ini/DebugSettings/Mode global
Refused: Cannot toggle 'sometimes' (enabled/disabled, true/false, yes/no, on/off, 1/0)   (exit 3)
```

Setzt die Datei des Scopes die Variable nicht, wird der wirksame Wert umgekehrt und in diesen Scope geschrieben.

### Arrays und Hashes

```
$ exp:ini add site.ini/ExtensionSettings/ActiveExtensions[] myext global
Add to site.ini/ExtensionSettings/ActiveExtensions[] in global: settings/override/site.ini.append.php
$ exp:ini add site.ini/ExtensionSettings/ActiveExtensions[] myext global
Nothing to change: site.ini/ExtensionSettings/ActiveExtensions[] in global already has that value
$ exp:ini rem site.ini/ExtensionSettings/ActiveExtensions[] myext global
$ exp:ini set site.ini/SiteAccessSettings/RelatedSiteAccessList[admin] admin siteaccess:site
$ exp:ini clear site.ini/SiteAccessSettings/AvailableSiteAccessList[] siteaccess:site
$ exp:ini copy site.ini/SiteSettings/SiteURL global siteaccess:admin
Copy site.ini/SiteSettings/SiteURL from global in siteaccess:admin: settings/siteaccess/admin/site.ini.append.php
```

### where und list

`where` führt jede Datei auf, die eine Variable setzt, in der Reihenfolge, in der eZINI sie lädt. Ein einfacher Wert
nimmt den letzten. Arrays summieren sich, sofern eine Datei sie nicht zurücksetzt. Auf einer Referenzinstallation:

```
$ exp:ini where site.ini/SiteSettings/SiteName
site.ini/SiteSettings/SiteName (load order of siteaccess site)
 1. settings/site.ini                                            scope default
      SiteName=Exponential
 2. settings/siteaccess/site/site.ini.append.php                 scope siteaccess:site
      SiteName=Fit & Healthy
In effect:
      SiteName=Fit & Healthy
```

Eine Erweiterung, die in `extension/` liegt, aber für den Siteaccess nicht aktiv ist, gehört nicht zur
Ladereihenfolge, daher liest niemand ihre Einstellungen. `where` führt ihre Dateien trotzdem nach der Ladereihenfolge
auf, damit ein dort geschriebener Wert nicht für einen gehalten wird, den niemand setzt. `Scripts` und `Scripts[]`
nennen dasselbe Array. Setzen nur solche Dateien die Variable, endet `where` mit 2, weil nichts wirksam ist:

```
$ exp:ini where cronjob.ini/CronjobPart-publishing/Scripts[]
cronjob.ini/CronjobPart-publishing/Scripts (load order of siteaccess site)
Not loaded for this siteaccess:
 1. extension/mysite_settings/settings/cronjob.ini.append.php scope extension:mysite_settings (extension not active for this siteaccess)
      Scripts[]=staticcache_cleanup.php
      Scripts[]=indexcontent.php
      Scripts[]=contentjobs.php
Not in effect: only files this siteaccess does not load set cronjob.ini/CronjobPart-publishing/Scripts
```

Die JSON-Ausgabe führt diese Dateien unter `notLoaded` auf (Pfad, Scope, Grund, Wert).

Der Siteaccess ist der aktuelle oder der mit `-s` oder als zweites Argument angegebene (`where <setting> admin`).
`where` liest die Ladereihenfolge über eZINI, daher beschreibt es die Installation, in der der Befehl läuft, und lehnt
`--root` ab. `list` ohne Scope zeigt die wirksamen Einstellungen, mit einem Scope die Datei dieses Scopes:

```
$ exp:ini list site.ini/DatabaseSettings global
[DatabaseSettings]
Password=********
1 variable in [DatabaseSettings] of site.ini in global
```

### Geheimnisse

Werte von Variablen, die wie ein Geheimnis benannt sind, werden in `get`, `list`, `where`, in Diffs und in der
JSON-Ausgabe als `********` gezeigt: Password, Passwd, Passphrase, Secret, Token, Salt, Credential, PrivateKey, ApiKey
und Namen, die auf `Key` enden, aber nicht SortKey, CacheKey und ähnliche. `--show-secrets` zeigt sie. Geschrieben
werden sie so, wie sie angegeben sind. Eine Regel entscheidet, was als Geheimnis gilt, `expIniEditor::isSecret()`,
sodass Editor und Befehl immer übereinstimmen.

## Optionen

| Option | Tut |
|---|---|
| `--dry-run` | gibt den Diff aus (bei einem Verschieben beide Diffs) und schreibt nichts; auch auf `default` erlaubt |
| `--json` | gibt ein Objekt aus: `ok`, `code`, `action`, `message`, `warnings`, `data` (Scope, Pfad, Diff, Sicherung, Summen, ...) |
| `--show-secrets` | zeigt geheime Werte |
| `--allow-default` | erlaubt das Schreiben von `settings/<file>.ini` |
| `--no-clear-cache` | leert den INI-Cache nach einem Schreiben nicht |
| `--no-backup` | schreibt ohne Sicherungskopie |
| `--no-create` | lehnt das Anlegen einer fehlenden Datei ab (Exit 2); `rem` legt nie eine an |
| `--root=<dir>` | arbeitet auf dem Einstellungsbaum eines anderen Installationsverzeichnisses (eine Kopie, ein Checkout) |
| `--only=<V,...>`, `--keep-target`, `--force`, `--create-extension`, `--activate` | move und move-all (siehe unten) |
| `--files=<f,...>` | move-all: nur diese Dateien |
| `-s <siteaccess>` | der Siteaccess, dessen Einstellungen wirksam sind (get, where, list) |

## Exit-Codes

| Code | Bedeutung |
|---|---|
| 0 | erledigt, oder nichts zu ändern (die Ausgabe sagt, was) |
| 1 | Aufruffehler: unbekannte Aktion (die registrierten werden aufgeführt), fehlendes oder überzähliges Argument, fehlerhafte Einstellung |
| 2 | nicht gefunden: `rem` einer Variable, die die Datei des Scopes nicht setzt, eine fehlende Datei mit `--no-create`, `get` einer nicht gesetzten Variable, `where` einer Variable, die keine geladene Datei setzt |
| 3 | abgelehnt: der Scope default, ein unbekannter Siteaccess, ein Wert, den `toggle` nicht umkehren kann, ein Verschieben, das einen wirksamen Wert ändern würde |
| 4 | Schreiben fehlgeschlagen (die Datei hat sich auf der Festplatte geändert, seit sie gelesen wurde, oder konnte nicht geschrieben werden) |

## Wie Dateien geschrieben werden

- **Zeile für Zeile.** Nur die Zeilen der geänderten Einstellung ändern sich. Eine neue Variable kommt ans Ende ihres
  Blocks, und ein neuer Block kommt ans Ende der Datei. Eine neue `.ini.append.php`-Datei beginnt mit
  `<?php /* #?ini charset="utf-8"?` und endet mit `*/ ?>`, wie die Dateien in `settings/override`.
- **Besitz.** Der Befehl läuft meist als root, während PHP-FPM als Benutzer der Website läuft, und eine root gehörende
  Einstellungsdatei macht die Website kaputt. Eine geänderte Datei behält Besitzer, Gruppe und Modus. Unter dem Benutzer
  der Website (nicht root) ausgeführt, erhält die neue Datei die alte Gruppe, wenn der Benutzer ihr angehört; wenn
  nicht (oder das chgrp verweigert wird), wird die ursprüngliche Datei unter einer Sperre an Ort und Stelle neu
  geschrieben, was Besitzer, Gruppe und Modus behält, aber nicht atomar ist, und eine Warnung sagt das. Eine neue Datei
  oder ein neues Verzeichnis erhält Besitzer und Gruppe ihres Einstellungsverzeichnisses.
- **Atomar.** Die Datei wird in eine temporäre Datei im selben Verzeichnis geschrieben und dann umbenannt. Hat sich die
  Datei auf der Festplatte geändert, nachdem der Befehl sie gelesen hat, wird das Schreiben mit Exit 4 abgelehnt.
- **Zeilen `[Include ...]`** (eine Einstellungsdatei, die andere Dateien liest, siehe
  [die Anleitung zur Seite der Einstellungen](../../anleitungen/einstellungsseite.md#eine-große-einstellungsdatei-mit-include--aufteilen))
  bleiben, wie sie geschrieben sind. Sie werden nicht aufgelöst: `get`, `where` und `list` zeigen die Einstellungen der
  Datei selbst, und eine neue Einstellung der Gruppe vor einer solchen Zeile kommt in diese Gruppe, über die Zeile.
- **Sicherungen.** Vor jedem Schreiben wird die Datei nach `var/backup/ini/<timestamp>/<path>` kopiert.
  Sicherungsdateien haben den Modus 0600, weil sie Geheimnisse enthalten können. `--no-backup` überspringt die Kopie.
- **Nach einem Schreiben** wird der Cache-Tag ini geleert (was `exp:cache ini` und `ezcache.php --clear-tag=ini` tun),
  und ein Hinweis sagt, wo die Änderung wirkt. `config.php` setzt `EZP_INI_FILEMTIME_CHECK=false`, daher sieht PHP-FPM
  eine geänderte Datei erst, wenn der INI-Cache geleert ist. Velocity-Worker behalten das eZINI ihres Vorwärmens, daher
  sieht Velocity die Änderung nach `exp:velocity restart`. Mit `--root` wird der Cache dieser Installation nicht
  geleert.

## Einstellungen in eine Erweiterung verschieben

Einstellungen, die sich in `settings/override` oder `settings/siteaccess/<sa>` angesammelt haben, können in die
Einstellungen einer Erweiterung umziehen, sodass sie mit der Erweiterung wandern. Die Erweiterung kann eine vorhandene
sein oder eine, die `--create-extension` für das Verschieben anlegt.

```bash
exp:ini move <file>/<Block> <from> <to>      # one block, with its comments and blank lines, in order
exp:ini move <file> <from> <to>              # every block of the file
exp:ini move-all <from> <to> [--files=site,content]   # every file of a scope
exp:ini move <file>/<Block> <from> <to> --only=SiteURL,SiteName   # some variables of a block
```

- **Zuerst wird das Ziel geschrieben, dann verlässt der Block die Quelle.** Enthält die Quelle danach nichts mehr,
  behält sie ihre Datei und ihren Rahmen. Dateien werden nie gelöscht.
- **Zusammenführen.** Hat der Zielblock bereits eine Variable mit einem anderen Wert, gewinnt standardmäßig der Wert der
  Quelle, mit `--keep-target` der Wert des Ziels. Jeder Konflikt wird ausgegeben. Eine Variable mit demselben Wert in
  beiden Dateien wird nicht zweimal geschrieben.
- **Eine Transaktion pro Dateipaar, geprüft gegen die wirksamen Werte.** Vor dem Schreiben liest der Befehl den
  wirksamen Wert jeder verschobenen Variable für jeden Siteaccess. Er liest sie in dieser Installation über eZINI und
  unter `--root` über die Dateikette des Editors. Nach dem Schreiben liest er sie erneut. Weicht ein Wert ab, werden
  beide Dateien wiederhergestellt, wie sie waren. Eine Zieldatei, die das Verschieben angelegt hat, wird nach
  `var/backup/ini/<stamp>-rollback/` beiseitegelegt. Das Verschieben wird dann mit Exit 3 abgelehnt, und die Meldung
  nennt die Datei, die jetzt gewinnt. `--force` behält ein solches Verschieben.
- **Ladereihenfolge.** Die Einstellungen einer Erweiterung werden vor `settings/siteaccess/<sa>` geladen, und das vor
  `settings/override`. Ein Wert, der aus dem Override in eine Erweiterung verschoben wird, kann daher gegen eine
  Siteaccess-Datei verlieren. Die Prüfung fängt das ab:

```
$ exp:ini move site.ini/SiteSettings global extension:fixtureext
Refused and rolled back: site.ini [SiteSettings] SiteName in effect for siteaccess site would change from
"Json" to "Site siteaccess" (settings/siteaccess/site/site.ini.append.php wins: it loads after
extension/fixtureext/settings/site.ini.append.php). Both files are as they were; --force keeps such a move   (exit 3)

$ exp:ini move site.ini/DebugSettings global extension:fixtureext --dry-run
--- a/extension/fixtureext/settings/site.ini.append.php
+++ b/extension/fixtureext/settings/site.ini.append.php
@@ -6,4 +6,9 @@
 Map[b]=2
+
+[DebugSettings]
+DebugOutput=Disabled
+Level=1
+Mode=sometimes
 */ ?>
--- a/settings/override/site.ini.append.php
+++ b/settings/override/site.ini.append.php
@@ -12,9 +12,4 @@
 Password=********
-
-[DebugSettings]
-DebugOutput=Disabled
-Level=1
-Mode=sometimes
 */ ?>
Dry run: would move 1 block, 3 variables, 1 file

$ exp:ini move site.ini/DebugSettings global extension:fixtureext
Moved 1 block, 3 variables: settings/override/site.ini.append.php -> extension/fixtureext/settings/site.ini.append.php
Moved 1 block, 3 variables, 1 file

$ exp:ini move site.ini/SiteSettings global siteaccess:site --only=SiteURL
Moved 1 block, 1 variable, 1 file
```

Ein Probelauf gibt beide Diffs und die Zusammenfassung aus. Er kann die wirksamen Werte nicht vergleichen, weil nichts
geschrieben wird. Der Vergleich geschieht, wenn das Verschieben ausgeführt wird.

### Ein durchgearbeitetes Beispiel: der Admin-Siteaccess in eine Erweiterung der Website

Das Folgende verschiebt alles in `settings/siteaccess/admin` in eine neue Erweiterung `mysite`, in deren
Siteaccess-Verzeichnis, auf einer temporären Kopie:

```
$ exp:ini move-all siteaccess:admin extension:mysite:siteaccess:admin
Refused: the extension mysite does not exist (no extension/mysite): add --create-extension to create it
(extension.xml, ezinfo.php, settings/), and --activate to activate it                                    (exit 3)

$ exp:ini move-all siteaccess:admin extension:mysite:siteaccess:admin --create-extension
Created extension/mysite/ (extension.xml, ezinfo.php, settings/); it has no classes, so no autoloads to generate
Refused: the extension mysite is not active, so nothing moved into it would be read. Activate it with:
exp:ini add site.ini/ExtensionSettings/ActiveAccessExtensions[] mysite siteaccess:admin (or add --activate)   (exit 3)

$ exp:ini move-all siteaccess:admin extension:mysite:siteaccess:admin --create-extension --activate
Activated mysite: ActiveAccessExtensions[] in settings/siteaccess/admin/site.ini.append.php
Moved 1 block, 1 variable: settings/siteaccess/admin/content.ini.append.php -> extension/mysite/settings/siteaccess/admin/content.ini.append.php (the source holds nothing more; its file stays)
Warning: ActiveAccessExtensions of [ExtensionSettings] stay in settings/siteaccess/admin/site.ini.append.php: an extension cannot activate itself
Moved 2 blocks, 2 variables: settings/siteaccess/admin/site.ini.append.php -> extension/mysite/settings/siteaccess/admin/site.ini.append.php
Moved 3 blocks, 3 variables, 2 files
```

Auf einer Referenzinstallation gab derselbe Befehl mit `--dry-run` die Diffs von 10 Dateien aus und
`Dry run: would move 60 blocks, 187 variables, 10 files`. Er schrieb nichts.

- `--create-extension` legt `extension/<ext>/` mit `settings/` an (und `settings/siteaccess/<sa>/`, wenn das Ziel es
  braucht), eine `extension.xml` und eine `ezinfo.php` (`<ext>Info`, `public static function info()`, Version 1.0.0,
  License „GNU General Public License v2.0 (or any later version)“, Copyright Exponential Foundation and 7x). Der Name
  muss aus `a-z`, `0-9` und `_` bestehen. Die neuen Verzeichnisse und Dateien erhalten Besitzer und Gruppe von
  `extension/`. Eine neue Erweiterung hat keine Klassen, daher gibt es keine Autoloads zu erzeugen.
- `--activate` fügt die Erweiterung über den Editor hinzu: zu `ActiveExtensions[]` in `global` bei einem
  Erweiterungsziel oder zu `ActiveAccessExtensions[]` des Siteaccess bei einem Ziel im Siteaccess einer Erweiterung.
  Ohne `--activate` wird das Verschieben abgelehnt, solange die Erweiterung inaktiv ist, und die Meldung nennt genau den
  Befehl `exp:ini add ...`, der sie aktiviert.
- `ActiveExtensions` und `ActiveAccessExtensions` ziehen nie in eine Erweiterung um, weil sich eine Erweiterung nicht
  selbst aktivieren kann. Der Rest ihres Blocks zieht um.
- `move-all` hält bei der ersten Datei an, deren Verschieben abgelehnt wird. Die vorher verschobenen Dateien bleiben
  verschoben.

## Den Befehl erweitern

### Eine Aktion hinzufügen

Eine Aktion ist eine Klasse, die `expIniAction` implementiert: `name()`, `description()`, `usage()` und
`run( expIniCommandContext $c )`, das den Exit-Code zurückgibt. `expIniActionBase` nimmt die ersten drei aus
Klassenkonstanten und hat die Prüfungen, die mehrere Aktionen machen: `refuseKind()` (eine Einstellung einer Art, die
die Aktion nicht annimmt), `hasValue()` (ein Wert, den ein Array schon hat), `isList()` und `valueLines()` (ein Wert als
INI-Zeilen). Der Kontext liefert die Argumente (`setting()`, `fileAndBlock()`, `shift()`, `noMoreArguments()`), die
Scopes (`scope()`, `writeScope()`), den Editor (`editor( $scope, $file )`, den Tests mit `setEditorFactory()` ersetzen
können), die Ausgabe (`line()`, `data()`, `warn()`, `finish()`, `printDiff()`, `counted()`, `--json` wird für Sie
behandelt), das Maskieren von Geheimnissen (`display()`, `maskText()`) und den Schreibschritt, den jede schreibende
Aktion teilt: `commit()`, das nichts zu ändern, den Diff des Probelaufs, Sicherung, Speichern, Leeren des Caches und den
Hinweis abdeckt. `expIniActionRegistry::describe()` und `classProblem()` sagen, was eine Aktion ist und ob eine
registrierte Klasse funktionieren kann; `exp:ini actions` und die RAD-Übersicht verwenden beide.

Die Aktion `copy` des Kernels (`kernel/classes/ini/actions/expiniactioncopy.php`) ist das durchgearbeitete Beispiel.
Sie ist als eingebaute Aktion registriert und wird daher mit den anderen getestet:

```php
class expIniActionCopy extends expIniActionBase
{
    const NAME = 'copy';
    const DESCRIPTION = 'Copy a setting from one scope to another (a whole array as reset line + values)';
    const USAGE = "<file>/<Block>/<Variable>[<key>] <from-scope> <to-scope>\n\n  exp:ini copy site.ini/SiteSettings/SiteName global siteaccess:admin";

    public function run( expIniCommandContext $c )
    {
        $setting = $c->setting();
        $from = $c->scope( $c->shift( 'from-scope' ) );
        $to = $c->writeScope( $c->shift( 'to-scope' ) );
        $c->noMoreArguments();

        $value = $c->editor( $from, $setting['file'] )->get( $setting['block'], $setting['variable'] );
        if ( $value === null )
            return $c->finish( expIniCommandContext::EXIT_NOT_FOUND, 'Not found: ...' );

        $editor = $c->editor( $to, $setting['file'] );
        $editor->set( $setting['block'], $setting['variable'], (string)$value );   // arrays: clearArray() + add()
        return $c->commit( $editor, $to, $setting['file'], 'copy ...' );
    }
}
```

Eine Erweiterung registriert ihre Aktion in ihrer eigenen `extension/<ext>/settings/ini.ini.append.php` und erzeugt
dann die Autoloads der Erweiterungen neu (`php bin/php/ezpgenerateautoloads.php -e`):

```ini
[IniCommandSettings]
Actions[count]=iniActionFixtureCount
ActionAliases[n]=count
# an empty value switches an action off, here the built-in copy
Actions[copy]=
```

Das ist die Fixture der Tests (`tests/tests/kernel/classes/ini/command/fixtures/extension/iniactionfixture/`). Damit
gibt `exp:ini count site.ini global` die Zahl der Blöcke aus, `exp:ini n` ist dieselbe Aktion, und `exp:ini copy` ist
eine unbekannte Aktion. Derselbe Name wie eine eingebaute Aktion ersetzt die eingebaute. Eine Registrierung, deren
Klasse fehlt oder `expIniAction` nicht implementiert, wird von `exp:ini actions` und der RAD-Übersicht gemeldet. Sie
auszuführen ist ein Aufruffehler.

### Einen Scope hinzufügen

Ein Scope-Provider implementiert `expIniScopeProvider` mit `scopes( $root )`, das `expIniScope`-Objekte zurückgibt. Die
eingebauten sind `expIniCoreScopeProvider` (global, default, Siteaccesses) und `expIniExtensionScopeProvider`
(Erweiterungen und ihre Siteaccess-Verzeichnisse). Der Provider der Tests fügt ein Verzeichnis hinzu, das jeder Knoten
eines Clusters teilt:

```php
class iniActionFixtureScopeProvider implements expIniScopeProvider
{
    public function scopes( $root )
    {
        return array( new expIniScope( 'shared', 'shared', 'settings/shared', $root, 'Shared by every node',
                                       array( 'policyWritable' => true ) ) );
    }
}
```

```ini
[IniCommandSettings]
ScopeProviders[]=iniActionFixtureScopeProvider
```

Danach schreibt `exp:ini set site.ini/SiteSettings/SiteName Cluster shared` die Datei
`settings/shared/site.ini.append.php`. Haben zwei Scopes denselben Namen, gewinnt der erste, sodass ein Provider keinen
eingebauten Scope übernehmen kann.

## RAD

Die Übersicht der Erweiterungspunkte (Setup > RAD, `setup/radsurvey`) hat einen Abschnitt **Actions and scopes of
exp:ini** (`/setup/radsurvey/(show)/inicommand`). Er führt jede Aktion und jeden Scope-Provider mit seiner Klasse auf,
ob sie zum Kernel oder zu einer Erweiterung gehört, und jede Registrierung, die nicht funktionieren kann. Die
Zusammenfassung zeigt die Zahl der Aktionen und Provider (13 Aktionen und 2 Scope-Provider auf der
Referenzinstallation). Setup > RAD erwähnt sie neben der Übersicht, und der Katalogeintrag *exp:ini action or settings
scope* erklärt den Mechanismus mit einem Beispiel. Jede Registrierung ist eine Zeile von `ini.ini`, die eine Klasse
nennt, daher zählt die Übersicht sie bereits zu den *Einstellungen, die eine Klasse nennen*. Um sie nicht doppelt zu
zählen, rechnet die Gesamtsumme der Übersicht sie nicht noch einmal hinzu. Die Zählungen sind `ini_actions`,
`ini_actions_registered`, `ini_scope_providers` und `ini_command_broken` (`expRADSurvey::iniCommand()`).

## Tests

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/ini/command/
```

| Test | Deckt ab |
|---|---|
| `IniCommandTest` | jede Aktion als echter Prozess ausgeführt (`proc_open php bin/php/ini.php ...`) auf einem temporären Wurzelverzeichnis unter `var/tmp/ini/b/` (`--root`): Hilfe, Aufruffehler, zeilenerhaltendes Schreiben, Anlegen von Dateien und Blöcken, add/clear, rem, toggle, get, where und get des wirksamen Werts (in dieser Installation, nur lesend), list, scopes, actions, jede Art von Scope, abgelehnte Scopes, Probelauf, JSON für Erfolg und jeden Fehlschlag, Maskieren von Geheimnissen, Sicherungen, copy, die Schreibweisen von Einstellungen |
| `IniMoveTest` | move und move-all: ein Block mit seinen Kommentaren, zwischen allen Arten von Scopes, eine ganze Datei, move-all und `--files`, `--only`, Konflikte beim Zusammenführen mit und ohne `--keep-target`, abgelehnte Verschiebungen Byte für Byte zurückgenommen, `--force`, Anlegen und Aktivieren einer Erweiterung (globale und Siteaccess-Ziele), Probelauf und JSON-Summen |
| `IniCommandExtensionTest` | die Registry aus `settings/ini.ini`, die `ini.ini.append.php` einer Erweiterung, die eine Aktion und einen Alias hinzufügt und eine eingebaute ausschaltet, die über den Dispatcher ausgeführte Aktion, der Scope eines Scope-Providers aufgeführt und beschrieben, fehlerhafte Registrierungen, der Kontext |
| `RadSurveyIniCommandTest` | die Gruppe und die Zählungen der Übersicht, die Gesamtsumme, der Katalog, die View und die Templates |

Die eigenen Tests der Engine (Zeilenerhalt bei jeder echten INI-Datei, Besitz, atomares Schreiben, die
Ladereihenfolge) stehen in `tests/tests/kernel/classes/ini/` (`expIniEditorTest`, `expIniRoundTripTest`,
`expIniLocatorTest`).

## Verwandte Seiten

- [eZINI behält beim Speichern die Kommentare](ezini-behaelt-kommentare.md)
- [Einstellungen auf der Kommandozeile ändern: exp:ini](../../funktionen/6.0/exp-ini-befehl.md)
- [Juni 2026, zweite Hälfte (16. bis 30. Juni)](../../geschichte/2026/2026-06b.md)
- [Die Leiste Exp Debug](debug-leiste.md) (schreibt über dieselbe Engine)
- [Exponential-Konsole](konsole.md)
- [Spezifikation: Verzeichnisse und Platzierungen von INI-Overrides](../../spezifikationen/6.0/ini-override-platzierungen.md)
