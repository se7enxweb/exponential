---
title: "By hand: help pages for your extension"
audience: developer, app-creator, designer
category: extension-development
order: 220
summary: Put an extension's documentation into the admin help center: doc/ pages with front matter, the ExtensionDocDirs opt-in, context help for its views, and checking with exp:help.
keywords: tutorial, by hand, help pages, help center, exphelp, ExtensionDocDirs, front matter, context help, ContextHelp, Map, ext/, exp:help check, documentation
type: help
---
# By hand: help pages for your extension

Tutorial 21. Your extension's documentation can live in the same help center as the kernel's, searchable and linked
from the "?" button of your module's pages. This tutorial adds two pages to `mysite_hello` and connects them. The
mechanisms come from `settings/exphelp.ini` and the help classes in `kernel/classes/help/`.

## 1. Write the pages

`extension/mysite_hello/doc/README.md`:

```text
---
title: My Site Hello
audience: admin, developer
category: development
order: 10
summary: What the mysite_hello extension adds: greetings, a fetch function, a rating field, a digest.
keywords: mysite_hello, greeting, rating, digest
type: help
---
# My Site Hello

This extension greets visitors on /hello/world/<name>, lets editors compose greetings ...
```

`extension/mysite_hello/doc/compose.md`, with a heading `## Composing a greeting` and the steps for editors.

Front matter keys and their meaning are described in [Documenting your extension](../documenting-your-extension.md).
Markdown only: the help escapes raw HTML.

## 2. Commit them

The help serves only files tracked by Git (it reads the repository's index), so commit the pages in the extension's
repository:

```bash
git -C extension/mysite_hello add doc
git -C extension/mysite_hello commit -m "Added: Help pages for mysite_hello"
```

## 3. Opt in

The installation decides which extensions' docs it shows, in its `settings/override/exphelp.ini.append.php` (or a site
extension):

```ini
[IndexSettings]
ExtensionDocDirs[]=mysite_hello
```

The pages get the ids `ext/mysite_hello/README.md` and `ext/mysite_hello/compose.md`.

## 4. Context help

Your extension registers the "?" target of its views in `extension/mysite_hello/settings/exphelp.ini.append.php`:

```ini
[ContextHelp]
Map[hello]=ext/mysite_hello/README.md
Map[hello/compose]=ext/mysite_hello/compose.md#composing-a-greeting
```

The anchor is the heading as the viewer makes it (lower case, spaces to dashes). An entry whose page is not served is
skipped, so the module entry answers instead.

## 5. Rebuild and check

```bash
php bin/php/ezcache.php --clear-tag=ini
./console exp:help index
./console exp:help check
```

`exp:help check` reports broken links and images in the help's pages. Then open the help center and search for
"mysite_hello", and press "?" on `/hello/compose`.

## Related pages

- Previous: [By hand: translating an extension](by-hand-translation.md)
- Next: [By hand: a PHPUnit test for your extension](by-hand-phpunit-test.md)
- [The help system for extensions](../../part-8-reference/help-system-for-extensions.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
