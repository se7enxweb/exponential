---
title: Examples
audience: developer, admin
category: examples
order: 0
summary: Working examples for Exponential 6. Web server configurations that were checked, a cookbook of complete tasks, and more than two thousand reference pages generated from the code, covering template operators, fetch functions, module views, policies, INI settings, datatypes, workflow events, cronjobs, console commands and extension points.
keywords: examples, example, cookbook, reference, web server, htaccess, nginx, template operator, fetch, module, view, policy, INI, setting, datatype, workflow, cronjob, console, command, extension point
featured: true
type: example
---
# Examples

This section has examples you can copy into a real installation. There are three kinds:

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](web-servers/nginx.md), [LiteSpeed](web-servers/litespeed.md),
> [Caddy](web-servers/caddy.md), [IIS](web-servers/iis.md), [Velocity](web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../help/hosting/panels/plesk.md), [cPanel](../help/hosting/panels/cpanel.md), [DirectAdmin](../help/hosting/panels/directadmin.md), [ISPConfig](../help/hosting/panels/ispconfig.md).

- **[Web server examples](web-servers/README.md)**: complete configurations for Exponential Velocity, Apache
  (`.htaccess` and virtual host), nginx, Caddy, FrankenPHP, LiteSpeed, IIS and PHP-FPM. Each one routes requests
  the same way as the shipped `.htaccess_root`. Each was syntax-checked, and most were also tested on a scratch
  server.
- **[Cookbook](cookbook/README.md)**: complete tasks from start to finish. A content class, a template override,
  your own template operator, datatype, module, workflow event and cronjob, an extension skeleton, JSON calls,
  several sites in one installation, translations, caching and deploying with Velocity.
- **Reference examples, generated from the code**: one page per item, each with an example derived from the
  definition the code ships. The list is below.

The rules for writing code (naming, file layout, security, templates without inline styles or scripts) are in
[Standards and patterns](../standards/README.md). This section shows them in use and does not repeat them.

## Web servers and hosting

| Page | What it shows |
|---|---|
| [Web server examples](web-servers/README.md) | The overview: which server, which file, and how it was checked |
| [Apache .htaccess](web-servers/apache-htaccess.md) | [`htaccess`](htaccess): the shipped rules, explained one by one |
| [Apache virtual host](web-servers/apache-virtual-host.md) | [`ezpublish.conf`](ezpublish.conf): HTTPS, HTTP/2, PHP-FPM and the rules in server context |
| [nginx](web-servers/nginx.md) | A server block with the same routing, HTTPS and headers |
| [Caddy](web-servers/caddy.md) | Caddy with PHP-FPM, routed like the generated FrankenPHP configuration |
| [FrankenPHP](web-servers/frankenphp.md) | Run by Velocity, or on its own |
| [LiteSpeed and OpenLiteSpeed](web-servers/litespeed.md) | `.htaccess` under LiteSpeed, and a virtual host for OpenLiteSpeed |
| [IIS](web-servers/iis.md) | `web.config` with URL Rewrite |
| [PHP-FPM](web-servers/php-fpm.md) | A pool for one site |
| [Velocity](web-servers/velocity.md) | `velocity.ini` overrides, the `/etc/vc` tree and a systemd unit |
| [Demo mode](demo-mode/README.md) | `EXP_DEMO_MODE=1` for a public demo: `.htaccess`, virtual host, PHP-FPM pool, nginx, a systemd drop-in, a crontab, a container environment and a rebuild script |

`staticcache_offload.conf` adds rules that serve pages from the static cache directly. See
[Apache .htaccess](web-servers/apache-htaccess.md).

The shipped `.htaccess_root` (or `.htaccess_root_static`) and `config.php-RECOMMENDED` are put into place with
`./console exp:setup:htaccess --apply` and `./console exp:setup:config --apply`, and `config.php` is edited with
`./console exp:config:set`: see [Setting up .htaccess and config.php from the console](../guides/setup-files-from-the-console.md)
and the generated pages [exp:setupfiles](commands/setupfiles.md) and [exp:configphp](commands/configphp.md).

## Reference examples generated from the code

| Area | What each page has |
|---|---|
| [Template operators](template-operators/README.md) | The parameters (type, required, default), an example call, and the first use in a shipped template |
| [Template functions](template-functions/README.md) | The class, inner functions, how it finds its template, and the first use in a shipped template |
| [Fetch functions](fetch-functions/README.md) | `fetch( 'module', 'function', hash( ... ) )` with every parameter, the same call in PHP, and a real use |
| [Module views](module-views/README.md) | The address with ordered and unordered parameters, the policy functions it needs, and a link to it |
| [Policy functions and limitations](policies/README.md) | Each module's policy functions, their limitations, the views that check them, and examples |
| [INI settings](ini-settings/README.md) | One page per block: each setting with the comment of the shipped file, its default, and an override |
| [Datatypes](datatypes/README.md) | The identifier, what it stores, templates that use it, and the shipped view and edit templates |
| [Workflow event types and triggers](workflow-events/README.md) | Each event type, the triggers it accepts, and every trigger point |
| [Cronjob scripts and parts](cronjobs/README.md) | What each script does, its parts, and how to run and schedule it |
| [Console commands](commands/README.md) | Every `./console exp:*` command with its help text, options and aliases |
| [Extension points](extension-points/README.md) | INI lists of handlers, classes and directories an extension can add to |

The help lists these pages in folders. A folder opens in the navigation once you are on one of its pages; its
overview page links to every page in it. They rank below the written guides in the search, so a question finds the
explanation first and the reference second.

### What "derived from the code" means

- **Read from the definitions.** Names, parameters, types, defaults, policy functions, limitations, INI values and
  their comments, and option lists are read from the files the kernel loads, such as `eztemplateautoload.php`,
  `function_definition.php`, `module.php`, `operation_definition.php`, the shipped INI files, the datatype and
  workflow event classes, `cronjob.ini` and the command classes. Each page names its sources at the bottom.
- **Placeholders.** Values in an example call, such as `1` for an integer or `'text'` for a string, only show the
  form. Each page says so.
- **Heuristics.** Some facts are guesses and are marked as such: the storage columns a datatype uses (found by
  reading its class) and the extension points (found by the setting's name).
- **First use in a shipped template.** This is the first match in the templates of the repository, in path order.
  It is real code, but not always the simplest example.
- **Only the shipped code.** Only files tracked in the repository are read: the kernel, its libraries and the
  bundled extensions. Other installed extensions, local overrides and the database are left out.
  This holds for settings a source asks for while it is read (a `module.php` that lists the values of an INI
  setting, the PHP operators of `template.ini`): they come from `settings/` and the bundled extensions' settings,
  never from an override, a siteaccess, the INI cache or an extension outside the repository, so an installation,
  a git worktree and CI get the same pages.
  The object state limitations are the one place where the kernel would read the database. Their pages say where
  those limitations come from instead.

## How the generated pages are made

They come from one command, run from the installation root:

```bash
./console exp:help:examples                 # write every area; only changed files are written
./console exp:help:examples --list          # the areas and how many pages each has
./console exp:help:examples --check         # change nothing; exit 1 if a page is missing, different or stale
./console exp:help:examples --area=datatypes,cronjobs
./console exp:help index --rebuild          # then rebuild the help index
```

The same command writes the German pages with `--language=ger-de`, into `doc/ger-de/beispiele/`, and lists them in
`doc/ger-de/.paths.json`:

```bash
./console exp:help:examples --language=ger-de
./console exp:help:examples --language=ger-de --check
./console exp:help:examples --language=ger-de --worklist=var/tmp/todo.json
```

The output is deterministic. The same sources always produce the same files: input is sorted, and pages contain no
dates and no host names. Run the command after a change to an operator, a fetch function, a view, a policy, an INI
file, a datatype, a workflow event, a cronjob or a command, then commit the changed pages with that change.
`--check` in a review or CI job catches pages that were not regenerated.

A translation reads its text from `doc/<language>/.examples/`: `strings.json` holds the fixed text of the pages
(headings, labels, sentences) with their `{placeholders}`, and `descriptions/*.json` the comments and descriptions
quoted from the sources, by item (such as `ini:settings/site.ini [Block] Setting`), each with the English text it
translates. A translation is used only while its English text is the source's; the worklist of `--worklist` lists the
texts to translate again. Code, INI keys and values, commands and identifiers are never translated. The English pages
do not depend on any translation.

Every generated page has `generated: exp:help:examples` in its front matter. The command only replaces or removes
pages with that marker, inside the areas listed above. It never touches the hand-written pages: this one, the web
server examples and the cookbook. To change a generated page, change its source and run the command again. Edits to
the page itself are lost the next time the command runs.

The code is `kernel/classes/help/exphelpexamplesgenerator.php` (class `expHelpExamplesGenerator`), the translation
`kernel/classes/help/exphelpexamplestranslation.php` (class `expHelpExamplesTranslation`), and the command is `kernel/private/classes/commands/exphelpexamples.php`. To add an area, give the class an `area<Name>()` method and
an entry in `AREAS`.

## Help settings for this section

`settings/exphelp.ini`:

- The category `examples`: `[CategorySettings] Categories[]`, `CategoryNames[examples]`, `CategoryIcons[examples]`.
- The page type `generated` for the generated pages: `[TypeSettings] TypeNames[generated]`. It ranks lower in the
  search through `[SearchSettings] TypeBoost[generated]`, and stays off the dashboard's audience cards through
  `[DashboardSettings] AudienceExcludeTypes[]`.
- The folded folders of the navigation: `[NavigationSettings] FoldedFolders[]`.

## Related pages

- [Standards and patterns](../standards/README.md): the rules the examples follow.
- [Serving the site](../install/08-serving-the-site.md) and [Security hardening](../install/13-security-hardening.md).
- [Templates and design](../guides/templates-and-design.md), [Roles and policies](../guides/roles-and-policies.md),
  [Workflows](../guides/workflows.md), [Cronjobs](../guides/cronjobs.md).
- [The help system](../features/6.0/help-system.md).
- [Free documentation](../help/free-documentation.md): this page is free to copy, translate and improve.
