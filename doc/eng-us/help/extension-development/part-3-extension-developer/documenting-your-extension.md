---
title: Documenting your extension, including help pages
audience: developer, app-creator, designer
category: extension-development
order: 370
summary: Document an extension where people look: README.md and INSTALL.md on top, the rest as Markdown in doc/, in English; the help center shows it while the extension is active, with front matter, featured pages and context help for your views.
keywords: documentation, README, doc folder, changelog, help center, exphelp, ExtensionDocDirs, front matter, context help, ContextHelp Map, expHelpContextProviderInterface, featured, GFDL
type: help
---
# Documenting your extension, including help pages

Part 3, chapter 37, the last of the anatomy. An extension nobody understands is an extension nobody uses. This chapter
lists the documents an extension should have and shows how to put your documentation into the administration's help
center, next to the kernel's, including the "?" help button of your module's pages.

## The documents

The convention: `README.md` and `INSTALL.md` on top of `extension/<extensionName>/`, every other page as Markdown
(`.md`) in `extension/<extensionName>/doc/`, in English. Old extensions shipped plain text (`README`, `doc/*.txt`),
which still shows; new and updated ones use Markdown. The full reference, with a directory tree and a plain-text
document converted to Markdown: [Documenting an extension](../../../guides/documenting-an-extension.md).

| File | Holds |
|---|---|
| `README.md` | what it does in one paragraph, requirements, installation, activation, settings, a first example, license |
| `INSTALL.md` | the installation step by step |
| `doc/` | longer guides as Markdown pages: settings reference, templates and their variables, upgrade notes |
| `CHANGELOG.md` or `doc/changelogs/` | what changed in each release ([Changelogs](../part-7-releasing/changelogs.md)) |
| `LICENSE` | the license text |
| `ezinfo.php`, `extension.xml` | name, version, license, website ([ezinfo.php](ezinfo-php.md)) |

Write for the reader who installs it next year on a newer version: name the versions you tested, the settings with
their defaults, and every command with its expected output.

## Your doc folder in the help center

The help center (`/exphelp/dashboard` in the administration) shows the documentation of every active extension on its
own (`[IndexSettings] ActiveExtensionDocs=enabled`, the default): the files of `extension/mysite_hello/doc/` that the
extension's Git repository tracks, and its `README*`, `INSTALL*`, `CHANGELOG*`, `LICENSE*`, `USAGE*` and `COPYING*` on
top. Their pages get the id `ext/mysite_hello/<path below doc>` and appear under **Extensions**. Only tracked files are
served; nothing else of the extension is ever read.

An extension that is not active can be shown too, when the installation names it in `settings/exphelp.ini`:

```ini
[IndexSettings]
ExtensionDocDirs[]=mysite_hello
```

See [Documenting an extension](../../../guides/documenting-an-extension.md) and
[The help system](../../../features/6.0/help-system.md#documentation-of-the-extensions).

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

The kernel's documentation has a page per shipped extension in `doc/eng-us/features/6.0/extensions/`. If your extension is
offered to the project, a short page there (what it does, settings, what changed per release) helps every
administrator; propose it with a pull request ([Contributing back with pull requests](../part-5-extend-fork-contribute/contributing-back-with-pull-requests.md)).

## License your documentation

Code is usually GPL; documentation is better under a documentation license. This help uses the GNU FDL 1.3, which
lets anyone copy, translate and improve it ([Free documentation](../../free-documentation.md)). State the license of
your `doc/` in its README.

## Related pages

- Previous: [Performance in extensions](performance-in-extensions.md)
- Next: [Tutorials: build real extensions step by step](tutorials/README.md)
- [The help system](../../../features/6.0/help-system.md), [Help system specification](../../../specifications/6.0/help-system.md)
- [The help system for extensions](../part-8-reference/help-system-for-extensions.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
