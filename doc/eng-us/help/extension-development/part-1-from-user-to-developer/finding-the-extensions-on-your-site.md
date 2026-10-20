---
title: Finding the extensions installed on your site
audience: user, developer, designer, app-creator, admin
category: extension-development
order: 70
summary: Setup > Extensions, Info > About and the command line: what is installed, and which extension is behind a page, a template, a setting or an operator.
keywords: installed extensions, setup extensions, about, ezinfo, extension.xml, exp:ini scopes, extension_counts, which extension, design extensions, module repositories
type: help
---
# Finding the extensions installed on your site

Part 1, chapter 7. Before you look elsewhere for ideas, look at your own installation: it probably already carries
dozens of extensions, some of them switched off. This chapter shows three ways to list them, and how to find out
which extension is behind a page, a template or a setting.

## Way 1: Setup > Extensions

Open **Setup > Extensions** (`/setup/extensions`). The page has one list: the active extensions first, in loading
order and numbered, then the inactive ones by name. Each card shows the name, version, description, license and
website (from `extension.xml`, else `ezinfo.php`), whether the folder is a Git checkout, the declared dependencies
(**Requires**, **Uses**, **Extends**) and **Needed by**. **Details** adds the author, the designs siteaccesses use,
the last change and downloads.

The filter above the list (**All**, **Active**, **Inactive**, **Problems**) and the sort are kept in the address, so
`/setup/extensions/(show)/active` is a bookmark of the active ones. Everything on the page is explained in
[The Extensions page](../../../guides/extensions-page.md).

> **Tip**
> **Inactive** is where ideas hide. An extension that is on disk but switched off was usually installed for a
> reason; read its description before you build the same thing again.

## Way 2: Info > About

**Info > About** (`/ezinfo/about`) lists the extensions active in the current siteaccess, in the order the kernel
loads them, with the version and license each states. The counts on the dashboard, on About and on the update
dashboard all come from one function, `eZExtension::extensionCounts()`, and each page says what it counts (active
here, active anywhere, on disk).

## Way 3: the command line

```bash
ls extension/
./console exp:ini get site.ini/ExtensionSettings/ActiveExtensions --allow-root-user
./console exp:ini scopes extension --allow-root-user
```

The last command lists one scope per extension and marks the inactive ones, for example
`extension:autonotifications   extension   extension/autonotifications/settings  (inactive extension)`.

## Which extension is behind it?

### A page (module)

The address `/<module>/<view>` names a module. Extensions declare where their modules are with
`ExtensionRepositories[]` in `[ModuleSettings]` of `module.ini`:

```bash
./console exp:ini where module.ini/ModuleSettings/ExtensionRepositories --allow-root-user
```

Each line names the extension that added a module repository. Then `ls extension/<ext>/modules/` shows its modules.

### A template

Turn on `ShowTemplatePathComments` (see [Your first hour as a developer](your-first-hour-as-a-developer.md)); a path
under `extension/<ext>/design/...` names the extension. The list of extensions whose designs are searched:

```bash
./console exp:ini where design.ini/ExtensionSettings/DesignExtensions --allow-root-user
```

### A setting

```bash
./console exp:ini where <file>/<Block>/<Variable> --allow-root-user
```

lists every file that sets it; files under `extension/<ext>/settings/` name the extension.

### A template operator

Extensions register template operators through `ExtensionAutoloadPath[]` in `[TemplateSettings]` of `site.ini`,
which points the template system at their `autoloads/eztemplateautoload.php`:

```bash
./console exp:ini where site.ini/TemplateSettings/ExtensionAutoloadPath --allow-root-user
grep -l "'my_operator'" extension/*/autoloads/eztemplateautoload.php
```

### A datatype or a workflow event

```bash
./console exp:ini where content.ini/DataTypeSettings/ExtensionDirectories --allow-root-user
./console exp:ini where workflow.ini/EventSettings/ExtensionDirectories --allow-root-user
```

## Read an extension's own files

Once you know the extension, its folder answers the rest:

| File | Tells you |
|---|---|
| `extension.xml` | Name, version, license, website, dependencies |
| `ezinfo.php` | The same for older extensions, plus copyright lines that credit earlier authors |
| `README.md`, `doc/` | What it does, how to install it, its settings |
| `composer.json` | The package name on Packagist, PHP requirement, license, authors |
| `LICENSE`, `LICENSE.md`, `doc/LICENSE` | The license text you must respect when you reuse the code |
| `settings/*.ini.append.php` | Every setting it adds or changes, usually with comments |
| `git -C extension/<ext> log --oneline` | Its history, if it is a clone |

Many extensions shipped with the distribution also have a page in the documentation:
[the extension catalogue](../../../features/6.0/extensions/README.md) and
[what a new installation comes with](../../../features/6.0/default-extension-distribution.md).

## From templates

A template can read the same counts the dashboard shows:

```text
{def $counts = fetch( 'setup', 'extension_counts', hash( 'all_siteaccesses', true() ) )}
```

Without `all_siteaccesses` the kinds that read every siteaccess's settings are left out, which keeps it fast; see
[The Extensions page](../../../guides/extensions-page.md).

## Exercise

1. Open `/setup/extensions/(show)/inactive` and pick one inactive extension.
2. Read its `README.md` or `extension.xml`. What does it add: a module, a datatype, operators, a design?
3. Find one setting it ships: `ls extension/<ext>/settings/`.

You now know one more building block that is already on your disk.

## Related pages

- Previous: [Reading the code you already have](reading-the-code-you-have.md)
- Next: [The se7enxweb extension catalogue](the-se7enxweb-extension-catalogue.md)
- [The Extensions page](../../../guides/extensions-page.md)
- [Extension list: sort, inspect and download](../../../features/6.0/extension-list-and-downloads.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
