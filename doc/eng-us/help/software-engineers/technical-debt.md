---
title: Handling technical debt
audience: software-engineer, developer
category: software-engineering
order: 560
summary: How technical debt is found, recorded and reduced in Exponential - the known gaps sections of the standards, the PHPStan baseline, checks that fail only on new problems, deprecations without removal dates, plain-file code beside class-based code, and how to pay debt down in small, reviewable steps without breaking installations.
keywords: technical debt, legacy code, known gaps, baseline, PHPStan, deprecation, refactoring, modernization, class-based views, compatibility, incremental improvement, software engineer
type: help
---
# Handling technical debt

This page is for engineers who work in a code base with a long history: the code base was developed from 1999, first
under the name eZ Publish, a product its creator discontinued after almost 18 years, and Exponential continues it. Debt
is expected. What matters is that it is visible, that it does not grow, and that it is paid down without breaking
installations.

## Contents

1. [Where debt is recorded](#where-debt-is-recorded)
2. [Debt that cannot grow](#debt-that-cannot-grow)
3. [Examples on record](#examples-on-record)
4. [Paying it down](#paying-it-down)
5. [What not to do](#what-not-to-do)

## Where debt is recorded

| Record | What it holds |
|---|---|
| **Known gaps** sections of the standards in `doc/eng-us/standards/` | what the project does not meet yet, with evidence ([Kernel architecture, Known exceptions and debt](../../standards/architecture/kernel-architecture.md#known-exceptions-and-debt)) |
| `phpstan-baseline.neon` | the PHPStan findings the code had when the check was introduced |
| `@deprecated` tags in `kernel/` and `lib/` | APIs with a replacement, still working |
| Upgrade notes in `doc/eng-us/bc/6.0/` | changes that needed action, and their reasons |
| The issue tracker | individual problems |

## Debt that cannot grow

The project's checks are built so that old problems are tolerated and new ones are refused:

- PHPStan fails only on findings missing from the baseline.
- Code style fails only when a change adds violations to a file.
- The design tests of the administration have an empty allowlist; an entry may not be added to make a change pass.

A change may leave an old problem in place; it must not add one. See [Static analysis and code style](static-analysis-and-code-style.md).

## Examples on record

From the standards pages:

- `lib/` depends on kernel classes in several files, so it is "lower level than the kernel", not independent.
- A few view classes still read `$_POST` or `$_GET` directly.
- `ezpEvent` is documented as private API, yet extensions rely on it.
- Plain-file views, cronjob parts and commands still run beside the class-based form.
- INI switches mix `enabled`/`disabled` and `true`/`false`.
- Database updates are applied by hand; there is no migration runner.
- Some test files are in no suite and never run.
- `@deprecated` APIs inherited from the old code base have no removal date.

## Paying it down

1. **Small and separate.** Raise the PHPStan level or shrink the baseline in small separate changes; one refactoring
   per pull request, never mixed with a fix.
2. **Keep entry points.** When code moved into classes in 6.0.15, every view, script and cronjob part kept its path as
   a one-line call, so includes, cron entries and `--help` output did not change. Do the same.
3. **Deprecate, do not remove.** Mark a replaced API `@deprecated` with its replacement and keep it working; announce
   a removal in the changelog and an upgrade note ([Backward compatibility policy](backward-compatibility-policy.md)).
4. **Test before you move.** Add a test that pins the current behavior, then change the code.
5. **Update the record.** When a known gap closes, remove it from the standard's list in the same change.

## What not to do

- Do not regenerate the baseline to make a new finding pass.
- Do not rename existing `eZ*` identifiers, tables or settings to modernize them.
- Do not reformat whole files in a functional change: reviewers cannot see the change.
- Do not add type declarations to existing kernel signatures: extensions that override them break.

## Related pages

- [Kernel, lib and extensions: the layering](kernel-lib-and-extensions.md)
- [Why most changes are small](../extension-development/part-4-upgrading/why-most-changes-are-small.md)
- [Inventory an old extension](../extension-development/part-4-upgrading/inventory-an-old-extension.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
