---
title: "Capstone: a complete extension from idea to release"
audience: developer, app-creator, designer
category: extension-development
order: 300
summary: All tutorials in one releasable extension: a plan, the full file tree, composer.json, README, tests, the checks before a release, version 0.1.0, publishing and announcing.
keywords: capstone, complete extension, release, plan, file tree, composer.json, README, tests, checklist, version, tag, Packagist, GitHub, announce
type: help
---
# Capstone: a complete extension from idea to release

Tutorial 23. The tutorials built `mysite_hello` piece by piece. This capstone turns it into something you can give to
other people: planned, documented, tested and released. Every step links the chapter that explains it.

## 1. The idea, written down

Before code, write five lines in `README.md`:

```text
# My Site Hello

Greets visitors, lets editors compose greetings, rates content, and sends a nightly digest.
For: site builders who want a friendly front page.
Requires: Exponential 6.0.14 or later, PHP 8.0 or later.
License: GNU General Public License v2.0 (or any later version).
```

If you cannot write these lines, the extension is not ready to be built. Check the
[catalogue](../../part-1-from-user-to-developer/the-se7enxweb-extension-catalogue.md) first: maybe it exists.

## 2. The complete tree

```text
extension/mysite_hello/
    README.md  LICENSE  composer.json  ezinfo.php  extension.xml  phpunit.xml.dist  .gitignore
    autoloads/eztemplateautoload.php                         tutorial 10
    classes/mysitehellooperators.php                          tutorial 10
    classes/mysitehellofunctioncollection.php                 tutorial 11
    classes/mysitehellogreeting.php                           tutorial 17
    classes/mysitehelloserverfunctions.php                    tutorial 18
    classes/runnable/cronjobs/hellodigest.php                 tutorial 16
    cronjobs/hellodigest.php                                  tutorial 16
    datatypes/mysiterating/mysiteratingtype.php              tutorial 14
    eventtypes/event/mysitepublishlog/mysitepublishlogtype.php tutorial 15
    modules/hello/module.php  world.php  compose.php  function_definition.php   tutorials 11 to 13
    design/standard/templates/hello/world.tpl                 tutorial 12
    design/standard/templates/content/datatype/{edit,view}/mysiterating.tpl   tutorial 14
    design/standard/templates/content/datatype/view/ezxmltags/mysite_note.tpl  tutorial 19
    design/standard/templates/workflow/eventtype/view/event_mysitepublishlog.tpl tutorial 15
    design/standard/javascript/mysite_hello.js                tutorial 18
    design/admin4/templates/hello/compose.tpl                 tutorial 13
    settings/site.ini.append.php  module.ini.append.php  design.ini.append.php  content.ini.append.php
    settings/workflow.ini.append.php  cronjob.ini.append.php  ezjscore.ini.append.php  exphelp.ini.append.php
    share/db_schema.dba                                       tutorial 17
    translations/ger-DE/translation.ts  translations/untranslated/translation.ts   tutorial 20
    doc/README.md  doc/compose.md                             tutorial 21
    tests/bootstrap.php  tests/unit/*.php                     tutorial 22
```

## 3. composer.json

```json
{
    "name": "your-organization/mysite_hello",
    "description": "Greetings, a rating field and a nightly digest for Exponential.",
    "type": "ezpublish-legacy-extension",
    "license": "GPL-2.0-or-later",
    "authors": [ { "name": "Your Organization", "role": "Author" } ],
    "require": {
        "php": ">=8.0",
        "se7enxweb/exponential": "^6.0.14",
        "se7enxweb/ezpublish-legacy-installer": "*"
    },
    "extra": { "ezpublish-legacy-extension-name": "mysite_hello" },
    "support": { "issues": "https://github.com/your-organization/mysite_hello/issues" }
}
```

`composer validate composer.json` must report no errors ([composer.json for an extension](../composer-json-for-extensions.md)).

## 4. The checks before a release

```bash
find extension/mysite_hello -name '*.php' -print0 | xargs -0 -n1 php -l | grep -v '^No syntax errors'
xmllint --noout extension/mysite_hello/extension.xml extension/mysite_hello/translations/*/translation.ts
(cd extension/mysite_hello && php ../../vendor/bin/phpunit)
php bin/php/checkclasses.php --quiet-ok
./console exp:checkcodestyle
```

Then by hand, on a development installation with debug output on: every page in light and dark mode, the Content
Security Policy console clean, the RAD health check (`/setup/rad`) without findings for your extension. The full list
is [Testing before a release](../../part-7-releasing/testing-before-a-release.md).

## 5. Version 0.1.0

Choose the number from what is already published (for a first release, nothing is), write it into `ezinfo.php`
(`'Version' => "0.1.0"`) and `extension.xml` (`<version>0.1.0</version>`), commit, then tag that commit:

```bash
git -C extension/mysite_hello commit -am "Updated: Version 0.1.0"
git -C extension/mysite_hello tag -a v0.1.0 -m "Version 0.1.0"
```

A pushed tag can never be moved or replaced; check before you push
([Tags and releases are permanent](../../part-7-releasing/tags-are-permanent.md)).

## 6. Publish

1. Create the repository on GitHub, push the branch and the tag.
2. Create a GitHub release for the tag with the changelog (`gh release create v0.1.0 --notes-file CHANGELOG.md`).
3. Submit the repository to Packagist and add the webhook
   ([Composer and Packagist](../../part-7-releasing/composer-and-packagist.md)).
4. Install it on a fresh installation with `composer require your-organization/mysite_hello:^0.1` to prove it works for
   someone else.

## 7. Announce and maintain

Announce it where people look ([Announcing a release](../../part-7-releasing/announcing-a-release.md)), answer issues, and
publish fixes as new versions. If it could serve everyone, offer it to the project
([Contributing back with pull requests](../../part-5-extend-fork-contribute/contributing-back-with-pull-requests.md)).

## Related pages

- Previous: [By hand: a PHPUnit test for your extension](by-hand-phpunit-test.md)
- Next: [Part 4: Upgrading an old extension to Exponential 6](../../part-4-upgrading/README.md)
- [Part 7: Releasing your extension](../../part-7-releasing/README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
