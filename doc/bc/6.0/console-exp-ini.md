# Changing settings from the command line: `exp:ini`

From Exponential 6.0.15 one command reads and changes the INI settings of every scope of an installation:
the global override, each siteaccess, each extension and each extension siteaccess.

```bash
./bin/php/console exp:ini set site.ini/SiteSettings/SiteName "My site" global
./bin/php/console exp:ini rem site.ini/SiteSettings/SiteName siteaccess:admin
./bin/php/console exp:ini toggle site.ini/ContentSettings/ViewCaching global        # enabled <-> disabled
./bin/php/console exp:ini toggle site.ini/TemplateSettings/Debug siteaccess:admin   # true <-> false
```

`php bin/php/ini.php ...` is the same command. As root, add `--allow-root-user`, as for every script.

The command does not use `eZINI::save()`, which writes the whole file again. Each file is changed line by
line: comments, blank lines and the order of the file stay as they are, and only the lines of the changed
setting change. The command works the way commands are written from 6.0.15 on
([cli_cronjob_view_abstractions.md](cli_cronjob_view_abstractions.md)). Its actions are classes, and an
extension can add its own and its own scopes. The RAD survey counts them.

| Part | Where |
|---|---|
| Entry point | `bin/php/ini.php` (`exp:ini` in the console, described by the `@description` of its class) |
| Command | `Exponential\Command\Kernel\Ini`, `kernel/private/classes/commands/ini.php` |
| Dispatcher, context, actions | `kernel/classes/ini/actions/`: `expIniCommand`, `expIniCommandContext`, `expIniAction`, `expIniActionBase`, `expIniAction<Name>`, `expIniMover`, `expIniActionRegistry` |
| Settings editor (reading and writing the files) | `kernel/classes/ini/`: `expIniEditor`, `expIniScope`, `expIniLocator`, `expIniWriter`, the scope providers |
| Registry | `settings/ini.ini` `[IniCommandSettings]` |

All the examples below were run, either on a temporary copy of a settings tree (`--root`, see
[Tests](#tests)) or on this installation with `--dry-run`. Paths and timestamps are as they printed.

## Settings

A setting is `<file>/<Block>/<Variable>`, and the `.ini` suffix is optional. Every action also takes these
forms:

| Form | Example |
|---|---|
| slashes | `site.ini/SiteSettings/SiteName` |
| colon and dot | `site.ini:SiteSettings.SiteName` or `site:SiteSettings.SiteName` |
| eZINI style, quoted or as three words | `"site.ini [SiteSettings] SiteName"`, `site.ini [SiteSettings] SiteName` |
| an array | `.../ActiveExtensions[]` (add, rem with a value, clear) |
| a hash entry | `.../RelatedSiteAccessList[admin]` (set, get, rem) |

If a value starts with `-`, put `--` before it. Everything after `--` is taken as it is:
`exp:ini set site.ini/MySettings/Offset -- -1 global`.

## Scopes

| Scope | File written |
|---|---|
| `global` (or `override`) | `settings/override/<file>.ini.append.php` |
| `siteaccess:<sa>`, or just `<sa>` | `settings/siteaccess/<sa>/<file>.ini.append.php` |
| `extension:<ext>` | `extension/<ext>/settings/<file>.ini.append.php` |
| `extension:<ext>:siteaccess:<sa>` | `extension/<ext>/settings/siteaccess/<sa>/<file>.ini.append.php` |
| `default` | `settings/<file>.ini`, the kernel's own defaults: refused unless `--allow-default` |
| a provider's scope | whatever a scope provider registered in `ini.ini` adds (see [Adding a scope](#adding-a-scope)) |

An unknown siteaccess is refused with exit code 3. An extension that does not exist yet is a scope with no
directory. A write to it is refused, except by `move --create-extension`.

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

## The actions

`exp:ini --help` lists them, and `exp:ini help <action>` (or `exp:ini <action> --help`) explains one.
`exp:ini actions` also lists the actions that extensions register.

| Action | Does |
|---|---|
| `get <setting> [scope]` | the value in effect, or the value one scope's file gives |
| `set <setting> <value> <scope>` | sets a plain variable or a hash entry, creating the file and the block if needed |
| `add <setting>[] <value> <scope>` | appends to an array; a value the file already lists is not added twice |
| `rem <setting> <scope>`, `rem <setting>[] <value> <scope>` | removes a variable, one array value or one hash entry (alias `remove`) |
| `clear <setting>[] <scope>` | writes the array reset line `Variable[]` |
| `toggle <setting> <scope>` | enabled/disabled, true/false, yes/no, on/off, 1/0, keeping the case the setting is written in |
| `copy <setting> <from> <to>` | copies a value, or a whole array as its reset line followed by the values |
| `move <file>[/<Block>] <from> <to>` | moves blocks between scopes, for example into an extension ([below](#moving-settings-into-an-extension)) |
| `move-all <from> <to>` | moves every file of a scope |
| `where <setting> [siteaccess]` | every file that sets it, in load order, and the value in effect |
| `list <file>[/<Block>] [scope]` | blocks, or the variables of a block |
| `scopes [kind]`, `actions` | what this installation has |

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

`rem` changes only the file of the scope it is given. The value that other files give stays, and `where`
shows it. A `rem` never creates a file. It leaves the block header in place, even when the block is empty.
`rem <file>/<Block> <scope>` removes a block that has no settings left. One that still has settings is
refused, unless `--force` is given.

These examples ran on a temporary root, so the ini cache was not cleared. On this installation the
reversible smoke test (`set`, `toggle`, `rem` of `site.ini/ExpIniSmokeTestB/Probe` in `global`, then
`rem site.ini/ExpIniSmokeTestB global`) printed the following after each write:

```
INI cache: cleared tag ini: 5 caches
Hint: PHP-FPM reads the change on its next request (ini cache cleared); Velocity workers keep the settings of their warm-up: exp:velocity restart to apply it there.
```

Afterwards `settings/override/site.ini.append.php` was byte for byte what it had been, with the same owner
and mode (`alpha:psaserv 0666`).

### toggle

```
$ exp:ini toggle site.ini/DebugSettings/DebugOutput global
site.ini/DebugSettings/DebugOutput: Enabled -> Disabled
$ exp:ini toggle site.ini/TemplateSettings/Debug siteaccess:admin
site.ini/TemplateSettings/Debug: true -> false
$ exp:ini toggle site.ini/DebugSettings/Mode global
Refused: Cannot toggle 'sometimes' (enabled/disabled, true/false, yes/no, on/off, 1/0)   (exit 3)
```

If the scope's file does not set the variable, the value in effect is flipped and written to that scope.

### Arrays and hashes

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

### where and list

`where` lists every file that sets a variable, in the order eZINI loads them. A plain value takes the last
one. Arrays add up unless a file resets them. On this installation:

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

An extension that is in `extension/` but not active for the siteaccess is not part of the load order, so
nothing reads its settings. `where` still lists its files after the load order, so that a value written
there is not taken for one that nobody sets. `Scripts` and `Scripts[]` name the same array. If only such files
set the variable, `where` exits with 2, because nothing is in effect:

```
$ exp:ini where cronjob.ini/CronjobPart-publishing/Scripts[]
cronjob.ini/CronjobPart-publishing/Scripts (load order of siteaccess site)
Not loaded for this siteaccess:
 1. extension/sevenx_alpha_settings/settings/cronjob.ini.append.php scope extension:sevenx_alpha_settings (extension not active for this siteaccess)
      Scripts[]=staticcache_cleanup.php
      Scripts[]=indexcontent.php
      Scripts[]=contentjobs.php
Not in effect: only files this siteaccess does not load set cronjob.ini/CronjobPart-publishing/Scripts
```

The JSON output lists these files under `notLoaded` (path, scope, reason, value).

The siteaccess is the current one, or the one given with `-s` or as the second argument
(`where <setting> admin`). `where` reads the load order through eZINI, so it describes the installation the
command runs in, and it refuses `--root`. `list` without a scope shows the settings in effect, and with a
scope it shows that scope's file:

```
$ exp:ini list site.ini/DatabaseSettings global
[DatabaseSettings]
Password=********
1 variable in [DatabaseSettings] of site.ini in global
```

### Secrets

Values of variables named like a secret are shown as `********` in `get`, `list`, `where`, in diffs and in
the JSON output: Password, Passwd, Passphrase, Secret, Token, Salt, Credential, PrivateKey, ApiKey, and names
ending in `Key`, but not SortKey, CacheKey and similar. `--show-secrets` shows them. They are written as
given. One rule decides what counts as a secret, `expIniEditor::isSecret()`, so the editor and the command
always agree.

## Options

| Option | Does |
|---|---|
| `--dry-run` | prints the diff (both diffs for a move) and writes nothing; also allowed on `default` |
| `--json` | prints one object: `ok`, `code`, `action`, `message`, `warnings`, `data` (scope, path, diff, backup, totals, ...) |
| `--show-secrets` | shows secret values |
| `--allow-default` | allows writing `settings/<file>.ini` |
| `--no-clear-cache` | does not clear the ini cache after a write |
| `--no-backup` | writes without a backup copy |
| `--no-create` | refuses to create a missing file (exit 2); `rem` never creates one |
| `--root=<dir>` | works on the settings tree of another installation root (a copy, a checkout) |
| `--only=<V,...>`, `--keep-target`, `--force`, `--create-extension`, `--activate` | move and move-all (below) |
| `--files=<f,...>` | move-all: only these files |
| `-s <siteaccess>` | the siteaccess whose settings are in effect (get, where, list) |

## Exit codes

| Code | Meaning |
|---|---|
| 0 | done, or nothing to change (the output says which) |
| 1 | usage error: unknown action (the registered ones are listed), missing or extra argument, malformed setting |
| 2 | not found: `rem` of a variable the scope's file does not set, a missing file with `--no-create`, `get` of an unset variable, `where` of a variable no loaded file sets |
| 3 | refused: the default scope, an unknown siteaccess, a value `toggle` cannot flip, a move that would change a value in effect |
| 4 | write failed (the file changed on disk since it was read, or could not be written) |

## How files are written

- **Line by line.** Only the lines of the changed setting change. A new variable goes at the end of its
  block, and a new block goes at the end of the file. A new `.ini.append.php` file starts with
  `<?php /* #?ini charset="utf-8"?` and ends with `*/ ?>`, like the files in `settings/override`.
- **Ownership.** The command usually runs as root, while PHP-FPM runs as the site user, and a root-owned
  settings file breaks the site. A changed file keeps its owner, group and mode. Run as the site user (not root), the new file is given the old group when the user belongs to it; when it does not (or the chgrp is refused), the original file is rewritten in place under a lock, which keeps owner, group and mode but is not atomic, and a warning says so. A new file or directory
  gets the owner and group of its settings directory.
- **Atomic.** The file is written to a temporary file in the same directory, then renamed. If the file
  changed on disk after the command read it, the write is refused with exit 4.
- **Backups.** Before each write the file is copied to `var/backup/ini/<timestamp>/<path>`. Backup files
  are mode 0600, because they can hold secrets. `--no-backup` skips the copy.
- **After a write**, the ini cache tag is cleared (what `exp:cache ini` and `ezcache.php --clear-tag=ini`
  do), and a hint says where the change takes effect. `config.php` sets `EZP_INI_FILEMTIME_CHECK=false`, so
  PHP-FPM sees a changed file only once the ini cache is cleared. Velocity workers keep the eZINI of their
  warm-up, so Velocity sees the change after `exp:velocity restart`. With `--root` this installation's cache
  is not cleared.

## Moving settings into an extension

Settings that have collected in `settings/override` or `settings/siteaccess/<sa>` can move into an
extension's settings, so they travel with the extension. The extension can be an existing one, or one that
`--create-extension` creates for the move.

```bash
exp:ini move <file>/<Block> <from> <to>      # one block, with its comments and blank lines, in order
exp:ini move <file> <from> <to>              # every block of the file
exp:ini move-all <from> <to> [--files=site,content]   # every file of a scope
exp:ini move <file>/<Block> <from> <to> --only=SiteURL,SiteName   # some variables of a block
```

- **The target is written first, then the block leaves the source.** If the source then holds nothing
  more, it keeps its file and wrapper. Files are never deleted.
- **Merging.** If the target block already has a variable with a different value, the source's value wins
  by default, and with `--keep-target` the target's value wins. Each conflict is printed. A variable with
  the same value in both files is not written twice.
- **One transaction per file pair, checked against the values in effect.** Before the write, the command
  reads the value in effect of every moved variable for every siteaccess. It reads them through eZINI on
  this installation, and through the editor's chain of files under `--root`. It reads them again after the
  write. If any value differs, both files are put back as they were. A target file that the move created is
  moved aside to `var/backup/ini/<stamp>-rollback/`. The move is then refused with exit 3, and the message
  names the file that now wins. `--force` keeps such a move.
- **Load order.** An extension's settings load before `settings/siteaccess/<sa>`, and that loads before
  `settings/override`. So a value moved out of the override into an extension can lose to a siteaccess
  file. The check catches this:

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

A dry run prints both diffs and the summary. It cannot compare the values in effect, because nothing is
written. The comparison happens when the move is made.

### Worked example: the admin siteaccess into a site extension

The following moves everything in `settings/siteaccess/admin` into a new extension `mysite`, in its
siteaccess directory, on a temporary copy:

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

On this installation the same command with `--dry-run` printed the diffs of 10 files and
`Dry run: would move 60 blocks, 187 variables, 10 files`. It wrote nothing.

- `--create-extension` creates `extension/<ext>/` with `settings/` (and `settings/siteaccess/<sa>/` when
  the target needs it), an `extension.xml` and an `ezinfo.php` (`<ext>Info`, `public static function
  info()`, Version 1.0.0, License "GNU General Public License v2.0 (or any later version)", Copyright 7x &
  Exponential Foundation). The name must be made of `a-z`, `0-9` and `_`. The new directories and files get
  the owner and group of `extension/`. A new extension has no classes, so there are no autoloads to
  generate.
- `--activate` adds the extension through the editor: to `ActiveExtensions[]` in `global` for an extension
  target, or to `ActiveAccessExtensions[]` of the siteaccess for an extension siteaccess target. Without
  `--activate`, the move is refused while the extension is inactive, and the message gives the exact
  `exp:ini add ...` command that activates it.
- `ActiveExtensions` and `ActiveAccessExtensions` never move into an extension, because an extension cannot
  activate itself. The rest of their block moves.
- `move-all` stops at the first file whose move is refused. The files moved before it stay moved.

## Extending the command

### Adding an action

An action is a class implementing `expIniAction`: `name()`, `description()`, `usage()` and
`run( expIniCommandContext $c )`, which returns the exit code. `expIniActionBase` takes the first three from
class constants, and has the checks several actions make: `refuseKind()` (a setting of a kind the action does
not take), `hasValue()` (a value an array already has), `isList()` and `valueLines()` (a value as INI lines).
The context gives the arguments (`setting()`, `fileAndBlock()`, `shift()`,
`noMoreArguments()`), the scopes (`scope()`, `writeScope()`), the editor (`editor( $scope, $file )`, which
tests can replace with `setEditorFactory()`), the output (`line()`, `data()`, `warn()`, `finish()`,
`printDiff()`, `counted()`, `--json` handled for you), the masking of secrets (`display()`, `maskText()`),
and the write step every writing action shares: `commit()`, which covers nothing to change, the dry-run diff,
backup, save, cache clear and hint. `expIniActionRegistry::describe()` and `classProblem()` say what an action
is and whether a registered class can work; `exp:ini actions` and the RAD survey both use them.

The kernel's `copy` action (`kernel/classes/ini/actions/expiniactioncopy.php`) is the worked example. It is
registered as a built-in, so it is tested with the others:

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

An extension registers its action in its own `extension/<ext>/settings/ini.ini.append.php`, then
regenerates the extension autoloads (`php bin/php/ezpgenerateautoloads.php -e`):

```ini
[IniCommandSettings]
Actions[count]=iniActionFixtureCount
ActionAliases[n]=count
# an empty value switches an action off, here the built-in copy
Actions[copy]=
```

That is the fixture of the tests (`tests/tests/kernel/classes/ini/command/fixtures/extension/iniactionfixture/`).
With it, `exp:ini count site.ini global` prints the number of blocks, `exp:ini n` is the same action, and
`exp:ini copy` is an unknown action. The same name as a built-in replaces the built-in. A registration
whose class is missing, or does not implement `expIniAction`, is reported by `exp:ini actions` and the RAD
survey. Running it is a usage error.

### Adding a scope

A scope provider implements `expIniScopeProvider` with `scopes( $root )`, which returns `expIniScope`
objects. The built-ins are `expIniCoreScopeProvider` (global, default, siteaccesses) and
`expIniExtensionScopeProvider` (extensions and their siteaccess directories). The tests' provider adds a
directory that every cluster node shares:

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

After that, `exp:ini set site.ini/SiteSettings/SiteName Cluster shared` writes
`settings/shared/site.ini.append.php`. When two scopes have the same name, the first one wins, so a provider
cannot take over a built-in scope.

## RAD

The extension point survey (Setup > RAD, `setup/radsurvey`) has a section **Actions and scopes of exp:ini**
(`/setup/radsurvey/(show)/inicommand`). It lists every action and every scope provider with its class,
whether it is the kernel's or an extension's, and every registration that cannot work. The summary shows
the number of actions and providers, here 13 actions and 2 scope providers. Setup > RAD mentions them next
to the survey, and the catalogue entry *exp:ini action or settings scope* explains the mechanism with an
example. Each registration is a line of `ini.ini` naming a class, so the survey already counts it among the
*settings that name a class*. To avoid counting it twice, the survey's total does not add it again. The
counts are `ini_actions`, `ini_actions_registered`, `ini_scope_providers` and `ini_command_broken`
(`expRADSurvey::iniCommand()`).

## Tests

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/ini/command/
```

| Test | Covers |
|---|---|
| `IniCommandTest` | every action run as a real process (`proc_open php bin/php/ini.php ...`) on a temporary root built under `var/tmp/ini/b/` (`--root`): help, usage errors, line-preserving writes, file and block creation, add/clear, rem, toggle, get, where and get in effect (on this installation, read only), list, scopes, actions, every scope kind, refused scopes, dry run, JSON for success and every failure, secrets masking, backups, copy, the setting syntaxes |
| `IniMoveTest` | move and move-all: a block with its comments, between all scope kinds, a whole file, move-all and `--files`, `--only`, merge conflicts with and without `--keep-target`, refused moves rolled back byte for byte, `--force`, creating and activating an extension (global and siteaccess targets), dry run and JSON totals |
| `IniCommandExtensionTest` | the registry from `settings/ini.ini`, an extension's `ini.ini.append.php` adding an action and an alias and switching a built-in off, the action run through the dispatcher, a scope provider's scope listed and written, broken registrations, the context |
| `RadSurveyIniCommandTest` | the survey's group and counts, the total, the catalogue, the view and the templates |

The engine's own tests (line preservation on every real INI file, ownership, atomic writes, the load order)
are in `tests/tests/kernel/classes/ini/` (`expIniEditorTest`, `expIniRoundTripTest`, `expIniLocatorTest`).
