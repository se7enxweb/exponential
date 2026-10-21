---
title: CLI scripts and ezexec for integrations
audience: integrator, developer
category: integration
order: 150
summary: Running integration code on the Exponential server from the command line - quick scripts with bin/php/ezexec.php, proper commands with eZScript in your extension, how the console finds them, siteaccess and user, exit codes, dry runs and running as the site user.
keywords: CLI, command line, ezexec, ezexec.php, eZScript, eZCLI, bin/php/console, ext:, exit code, siteaccess, dry-run, allow-root-user, import script, server-side integration
type: help
---
# CLI scripts and ezexec for integrations

This page is for integration developers whose code runs **on the Exponential server**: imports, exports, repairs,
one-off migrations. On the command line the whole PHP API is available, there is no request timeout, and the run can
be scheduled, logged and monitored. Two ways exist: `bin/php/ezexec.php` for a quick script, and a command of your
extension for anything you keep.

## Contents

1. [ezexec: a quick script with the kernel loaded](#ezexec-a-quick-script-with-the-kernel-loaded)
2. [A command in your extension](#a-command-in-your-extension)
3. [Siteaccess, user and environment](#siteaccess-user-and-environment)
4. [Exit codes, dry runs and output](#exit-codes-dry-runs-and-output)
5. [Commands that ship with Exponential and help integrations](#commands-that-ship-with-exponential-and-help-integrations)

## ezexec: a quick script with the kernel loaded

`php bin/php/ezexec.php <script.php>` does the bootstrap (settings, database, extensions, modules) and then
includes your file. The script finds `$cli` (an `eZCLI`) and `$script` (an `eZScript`) ready to use:

```php
<?php
// var/tmp/count_imported.php: how many objects carry our remote id prefix?
$db = eZDB::instance();
$rows = $db->arrayQuery( "SELECT COUNT(*) AS n FROM ezcontentobject WHERE remote_id LIKE 'erp-product-%'" );
$cli->output( 'Imported products: ' . (int)$rows[0]['n'] );
```

```bash
php bin/php/ezexec.php var/tmp/count_imported.php -s <siteaccess>
```

If the included file returns something other than `1` (a plain file returns `1`), the exit code is `1`. Use
`ezexec` for inspection and one-off fixes; anything that runs twice belongs in your extension as a command.

## A command in your extension

The console (`bin/php/console`) finds scripts at run time: `extension/<ext>/bin/php/<name>.php` becomes the command
`ext:<ext>:<name>`, with the one-line description taken from the `@description` tag of the first docblock.

```php
#!/usr/bin/env php
<?php
/**
 * @description Import products from the ERP export file (--file, --dry-run)
 */
require_once 'autoload.php';

$cli = eZCLI::instance();
$script = eZScript::instance( array( 'description' => "Import products from the ERP export file.\n",
                                     'use-session' => false,
                                     'use-modules' => true,
                                     'use-extensions' => true ) );
$script->startup();
$options = $script->getOptions( '[file:][dry-run]', '',
                                array( 'file' => 'The export file to read',
                                       'dry-run' => 'Report what would change, change nothing' ) );
$script->initialize();

if ( !$options['file'] || !is_readable( $options['file'] ) )
{
    $cli->error( 'Give a readable --file' );
    $script->shutdown( 1 );
}

$importer = new myIntegrationProductImport( $cli, (bool)$options['dry-run'] );
$ok = $importer->run( $options['file'] );

$script->shutdown( $ok ? 0 : 1 );
```

Save it as `extension/myintegration/bin/php/importproducts.php`, make it executable, and run it:

```bash
php bin/php/console ext:myintegration:importproducts --file=var/import/products.csv --dry-run -s <siteaccess>
php extension/myintegration/bin/php/importproducts.php --help
```

Keep the work in a class (`myIntegrationProductImport` above) so the same code runs from the command, a cronjob part
and a test. The body of such an importer is on [Writing an import script](writing-an-import-script.md).

## Siteaccess, user and environment

| Topic | What to do |
|---|---|
| Siteaccess | Pass `-s <siteaccess>`; settings of that siteaccess and its access extensions apply. Without it, `DefaultAccess` is used |
| User | A script runs as the anonymous user until you make another user current with `eZUser::setCurrentlyLoggedInUser( $user, $user->attribute( 'contentobject_id' ) )`; use a service account ([how](service-accounts-roles-and-policies.md)) |
| Operating system user | Run as the user the web server runs as; files written as root may not be writable by the site afterwards. As root, scripts refuse to run unless `--allow-root-user` is given |
| Credentials of the other system | Read them from the environment or from your extension's INI in `settings/override`, never from the command line, where they end up in the shell history and the process list |
| Debug output | `-d` prints the debug output at the end; `--logfiles` writes log files |

## Exit codes, dry runs and output

- Exit `0` on success and non-zero on failure, so cron, deployments and monitoring can react.
- Offer `--dry-run` for every script that changes data, and say what would change.
- Respect `-q`: `$cli->output()` prints nothing with it; use `$cli->error()` for errors, which always print.
- For long loops, show progress with `$script->resetIteration( $total )` and `$script->iterate( $cli, $status, $text )`.
- Never print passwords, keys or personal data; mask them.

## Commands that ship with Exponential and help integrations

| Command | Use in an integration |
|---|---|
| `php bin/php/ezcsvimport.php --class=<class> <node> <file>` | Import objects of one class from CSV (`--creator`, `--storage-dir`) |
| `php bin/php/ezcsvexport.php --storage-dir=<dir> <node>` | Export a subtree to one CSV file per class |
| `php bin/php/updatesearchindex.php` | Rebuild the search index after a bulk change |
| `php bin/php/ezcache.php --clear-id=<ids>` | Clear caches after a bulk change ([which](caching-after-imports.md)) |
| `php bin/php/expcontentjob.php list` | Run, list, resume and cancel content jobs (large removes, copies, moves) |
| `php bin/php/ezsubtreeremove.php` | Remove subtrees by node id, to the trash unless `--ignore-trash` |
| `php bin/php/console exp:logs` | Search and follow the log files |
| `php bin/php/console exp:audit tail` | Read the audit trail |
| `php bin/php/maintenance.php on\|off\|status` | Take the site offline for a migration window |

`php bin/php/console list` shows every command of the installation.

## Related pages

- [CLI scripts and console commands](../extension-development/part-3-extension-developer/cli-scripts-and-console-commands.md)
- [eZScript and eZCLI](../extension-development/part-8-reference/ezscript-and-ezcli.md)
- [Exponential Console](../../bc/6.0/console.md)
- [Cronjobs for integrations](cronjobs-for-integrations.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
