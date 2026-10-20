---
title: "Module 12: Tests and quality"
audience: trainer, developer
category: training
order: 120
summary: The twelfth module of the Developers course. Learners run the kernel's DB-less PHPUnit suites, write a test for their own template operator, and run the checks CI applies to every change: syntax, file manifest, commit messages, hooks, code style, PHPStan and the tests of a change. 2 hours 25 minutes.
keywords: developers course, module 12, tests, PHPUnit, phpunit.xml, DB-less, writing a test, quality checks, checkmanifest, git hooks, commit messages, phpstan, phpcs, CI
type: help
---
# Module 12: Tests and quality

Before learners share code, they learn to prove it works and to pass the checks the project applies to every
change. This module runs the kernel's test suites, has learners write a test of their own, and goes through the
checks and hooks that keep the main branch green.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Duration | 2 hours 25 minutes (with one 10-minute break) |
| Lessons | 3 |
| Level | Intermediate |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 12.1: The test suites](the-test-suites.md) | 40 minutes | Find the suites, run the `security` suite and one test file |
| [Lesson 12.2: Writing a test](writing-a-test.md) | 50 minutes | Test `mysite_initials`, break the code, see the test fail |
| [Lesson 12.3: Checks before you share a change](checks-before-you-share-a-change.md) | 45 minutes | Hooks, manifest, commit messages, syntax and the tests of a change |

## Preparing this module

- The installations need the development dependencies (`vendor/bin/phpunit` exists after `composer install`).
- Lesson 12.3 needs the kernel as a Git clone with the remote `origin`; learners commit only on their own branch and
  never push in this module.
- On a shared training server, run tests with `nice -n 15` and one suite at a time.
- `make devtools` downloads PHP_CodeSniffer and PHPStan; run it before the class if the connection is slow.

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 12.1 | 40 |
| Lesson 12.2 | 50 |
| Break | 10 |
| Lesson 12.3 | 45 |
| **Total** | **145** |

## Related pages

- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
