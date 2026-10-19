---
title: Documenting your extension, including help pages
audience: developer, app-creator, designer
category: extension-development
order: 370
summary: Document an extension where people look, and put it into the admin help center with ExtensionDocDirs, front matter, featured pages and context help for your views.
keywords: documentation, README, doc folder, changelog, help center, exphelp, ExtensionDocDirs, front matter, context help, ContextHelp Map, expHelpContextProviderInterface, featured, GFDL
type: help
---
# Documenting your extension, including help pages

Part 3, chapter 37, the last of the anatomy. An extension nobody understands is an extension nobody uses. This chapter
lists the documents an extension should have and shows how to put your documentation into the administration's help
center, next to the kernel's, including the "?" help button of your module's pages.

## The documents

| File | Holds |
|---|---|
| `README.md` | what it does in one paragraph, requirements, installation, activation, settings, a first example, license |
| `doc/` | longer guides: settings reference, templates and their variables, upgrade notes |
| `CHANGELOG.md` or `doc/changelogs/` | what changed in each release (Changelogs) |
| `LICENSE` | the license text |
| `ezinfo.php`, `extension.xml` | name, version, license, website ([ezinfo.php](ezinfo-php.md)) |

Write for the reader who installs it next year on a newer version: name the versions you tested, the settings with
their defaults, and every command with its expected output.

## Your doc folder in the help center

The help center (`/exphelp/dashboard` in the administration) shows the git-tracked files of `doc/`. An extension's own
`doc/` is shown when the installation opts in, in `settings/exphelp.ini`:

```ini
[IndexSettings]
ExtensionDocDirs[]=mysite_hello
```

The help then reads `extension/mysite_hello/doc`, the files tracked by the extension's own Git repository, and gives
their pages the id `ext/mysite_hello/<path below doc>`. Only tracked files are served; nothing outside the folder is
ever read. See [The help system](../../../features/6.0/help-system.md).

## Front matter

Help pages are Markdown with a small front matter block. The keys this book uses:

```text
---
title: Greetings: settings and templates
audience: developer, admin
category: development
order: 10
summary: One sentence, at most 220 characters, shown in lists and search results.
keywords: greeting, mysite_hello, settings
type: help
---
# Greetings: settings and templates
```

`audience` takes the help's audience ids (editor, admin, developer, user and the others the dashboard lists),
`category` one of its categories; `featured: true` puts a page on the dashboard; `hidden: true` keeps it out of lists
and search. The help renders Markdown safely: raw HTML is escaped, so write Markdown only.

## Context help for your module

The "?" button in the administration's header opens "help for this page", chosen by `exphelp.ini [ContextHelp]`.
Your extension registers its own views in `settings/exphelp.ini.append.php`:

```ini
[ContextHelp]
Map[hello]=ext/mysite_hello/README.md
Map[hello/admin]=ext/mysite_hello/admin.md#clearing-greetings
```

Keys are `module/view`, `module`, or with a design prefix (`admin4l:hello/admin`); the target is a page id with an
optional heading anchor. An entry whose page the help does not serve is skipped, so the next key answers. For help that
depends on more than the view, implement `expHelpContextProviderInterface`
(`kernel/classes/help/exphelpcontextproviderinterface.php`), whose one method
`helpContext( $module, $view, $design )` returns `"path[#anchor]"` or `null`, and list the class in
`[ContextHelp] Providers[]`.

## A page in the kernel's catalogue

The kernel's documentation has a page per shipped extension in `doc/features/6.0/extensions/`. If your extension is
offered to the project, a short page there (what it does, settings, what changed per release) helps every
administrator; propose it with a pull request (Contributing back with pull requests).

## License your documentation

Code is usually GPL; documentation is better under a documentation license. This help uses the GNU FDL 1.3, which
lets anyone copy, translate and improve it ([Free documentation](../../free-documentation.md)). State the license of
your `doc/` in its README.

## Related pages

- Previous: [Performance in extensions](performance-in-extensions.md)
- Next: [Tutorials: build real extensions step by step](tutorials/README.md)
- [The help system](../../../features/6.0/help-system.md), [Help system specification](../../../specifications/6.0/help-system.md)
- The help system for extensions
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
