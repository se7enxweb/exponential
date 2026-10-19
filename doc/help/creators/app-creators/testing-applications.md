---
title: Testing applications
audience: app-creator, developer
category: app-creation
order: 490
summary: A testing strategy for applications on Exponential - unit tests without a database, tests against a throwaway database, template and link checks, browser checks with a test user, accessibility, and what to run before each push.
keywords: testing, PHPUnit, unit tests, integration tests, database tests, template check, browser tests, accessibility, CSP, test user, quality gates, app creator
type: help
---
# Testing applications

This page is for application creators who want to change code without fear. The kernel's own tests and gates are the
model: most tests run without a database, a few against a throwaway one, and checks of templates, links and the browser
complete the picture ([Testing and CI standard](../../../standards/testing-and-ci.md)).

## Contents

1. [The layers](#the-layers)
2. [Unit tests without a database](#unit-tests-without-a-database)
3. [Tests with a database](#tests-with-a-database)
4. [Template, settings and link checks](#template-settings-and-link-checks)
5. [Browser checks](#browser-checks)
6. [Before every push](#before-every-push)

## The layers

| Layer | Tool | Catches |
|---|---|---|
| Syntax | `php -l`, `eztemplatecheck.php` | parse errors in PHP and templates |
| Unit | PHPUnit, no database | logic of services, validators, handlers |
| Integration | PHPUnit with `tests/runtests.php --dsn ...` | SQL, persistent objects, content operations |
| Rendered pages | HTTP requests, browser automation | templates, permissions, redirects |
| Accessibility and CSP | an automatic accessibility checker, the CSP report log | barriers, inline code |

## Unit tests without a database

Write the logic as plain classes (services) the view, command and cronjob call; test those with PHPUnit
([DB-less PHPUnit tests](db-less-phpunit-tests.md)). The kernel runs its whole default suite without a database:
`vendor/bin/phpunit` excludes the `database` group.

## Tests with a database

`tests/runtests.php --dsn <dsn> --db-per-test` runs the suites against a throwaway database, as CI's database job does.
Mark such tests `@group database`. Never point tests at a database that holds real content.

## Template, settings and link checks

```bash
php bin/php/eztemplatecheck.php -s<siteaccess> extension/expdemo/design/   # every template of your extension
./console exp:ini where expdemo.ini/ListSettings/DefaultLimit <siteaccess>  # settings resolve as expected
./console exp:help check                                                    # links in your extension's docs, when indexed
```

## Browser checks

- Use a dedicated test user created for the run and removed afterwards; never a shared administrator account, whose
  preferences belong to someone.
- Check every page type of your extension logged out, as a restricted user and as an editor.
- Check the browser console for errors and the CSP report log (`var/log/csp.log`) for blocked code
  ([Content Security Policy for your code](content-security-policy.md)).
- Run an accessibility checker on each page ([Testing your site's accessibility](../website-creators/testing-accessibility.md)).

## Before every push

- [ ] `php -l` of changed PHP files; template check of changed templates.
- [ ] Unit tests of the changed code pass.
- [ ] No inline scripts or styles added.
- [ ] Autoloads regenerated if classes changed; caches cleared; the feature tried by hand.
- [ ] CI configured for the repository ([Continuous integration for extensions](ci-for-extensions.md)).

## Related pages

- [DB-less PHPUnit tests](db-less-phpunit-tests.md)
- [Quality checks](../../../features/6.0/quality-checks.md)
- [Quality gates and acceptance](../../leadership/quality-gates-and-acceptance.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
