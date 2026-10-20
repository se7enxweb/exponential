# Configuration managed by the deployment (ManagedByDeployment)

Since 6.0.15. One switch per siteaccess, off by default:

```ini
# settings/siteaccess/<admin siteaccess>/site.ini.append.php, or an extension's
# versioned settings; never written from the admin
[ConfigurationSettings]
ManagedByDeployment=enabled
```

With it enabled, the configuration of that siteaccess comes from the deployment (version control) only, and the
admin interface does not change it on the server. Without it the INI editor, the toolbar and menu editors and the
template editor write `*.ini.append.php` and `.tpl` files into the release directory, where version control does not
see them and the next deployment drops them.

## What it locks

In a web request, with the switch enabled:

- **Views.** The views in `expManagedConfiguration::LOCKED_VIEWS` answer like a view switched off in
  `[SiteAccessRules]` (by default a 404, see `DisabledModuleResult`), whatever the roles say:
  `settings/edit`, `setup/settingstoolbar`, the toolbar and menu editors (`visual/` and `setup/` toolbar,
  toolbarlist, menuconfig), the template editor (templateedit, templatecreate), the setup wizard (`setup/init`),
  extension activation (`setup/extensions`), the code generators (`setup/rad`, `setup/radsurvey`, kerneloverride,
  modulewizard, datatype, templateoperator, workflowevent and the *extension wizards) and
  `package/upload|install|uninstall|create|export`. The list lives in the class, not in an INI file, so a settings
  file cannot weaken it.
- **Menus and links.** Menus, tabs and the dashboard hide those views through the `fetch( 'user', 'can_open' )`
  check, and the admin and admin4 templates hide the edit, create, import and export links that lead to them.
- **Writers.** `eZINI::save()` returns false and `expIniEditor::save()` throws a refusal (`expIniException`
  REFUSED). This also stops writers the list does not know: extension modules, ezjscore calls, the debug bar
  settings, the audit and mail sender settings pages, views added later.
- **Setup wizard.** It does not start, whatever `[SiteAccessSettings] CheckValidity` says.
- **Packages.** `package/compare` still compares but offers no import; the package list and view offer no removal.

## What stays open

- Read-only views: `settings/view`, the template and package lists, `setup/cache`, `setup/info`, the logs.
- Command line scripts and the deployment itself (`cli` and `phpdbg` SAPIs), including `exp:ini` and
  `bin/php/*.php`.
- Key material the code generates itself on first use (the audit keys, the mail preference secret): it is runtime
  state, not configuration, and is still written (`expIniEditor::save()` option `generatedSecret`).

## Web servers

A web request is any SAPI but `cli` and `phpdbg`, and also a CLI process that serves the web: Velocity's workers
(`QBIX_SERVER_VERSION`, `QBIX_WORKER`) and any CLI process with a request method and a `Host` header
(`expManagedConfiguration::isWebServerInCli()`). So PHP-FPM, Apache, FrankenPHP, the built-in server and Velocity
all refuse the same way.

## Limits

- Installing a package from the command line still writes to the database, and packages can carry content.
- File permissions on the release directory remain the stronger measure. This switch is a guard at application
  level, not a replacement for them.

## Compatibility

`settings/site.ini` ships `ManagedByDeployment=disabled`, and a missing setting counts as disabled, so existing
installations behave as before. Templates that override the changed admin templates keep working; they only keep
showing links that the switch then answers with the disabled page.

## Tests

`tests/tests/kernel/classes/expManagedConfigurationTest.php` (no database): every locked view exists, each is
locked with the switch and open without it, the read-only views stay open, `eZINI::save()` and
`expIniEditor::save()` refuse in a web request (also in a CLI web server) and write on the command line, generated
keys are still written, and `settings/site.ini` ships the switch disabled.
