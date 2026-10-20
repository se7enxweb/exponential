---
title: "Lesson 12.1: The test suites"
audience: trainer, developer
category: training
order: 121
summary: Learners find the kernel's PHPUnit suites in phpunit.xml, run a DB-less suite and a single test file, read the result, and explain which suites need a database or a live installation. 40 minutes.
keywords: developers course, tests, PHPUnit, phpunit.xml, testsuite, DB-less, database group, tests/tests, tests/bootstrap.php, security suite, design suite, runtests.php, test-map.json, CI
type: help
---
# Lesson 12.1: The test suites

The kernel has hundreds of test files, and they are the best examples of how its classes are meant to be used.
Learners who change code also need to know which tests to run before they share the change. This lesson shows where
the tests are, how they are grouped, and how to run them without a database.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 12: Tests and quality](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Intermediate |
| Audience | Developers |
| Prerequisites | [Lesson 1.2](../module-01-the-development-installation/a-development-installation-with-git.md): a Git installation with development dependencies |

## Learning objectives

After this lesson, learners can:

- find the suites in `phpunit.xml` and the tests in `tests/tests/`;
- run one suite with `vendor/bin/phpunit --testsuite <name>` and one test file by its path;
- explain that the default run excludes the `database` group and needs no database;
- name the suites that need a live installation or servers;
- read PHPUnit's summary line.

## Trainer's notes

### How to present it

Source: [Testing and continuous integration](../../../../standards/testing-and-ci.md).

1. **Where** (3 minutes). `phpunit.xml` in the root defines the suites; `tests/tests/` holds the tests, organized
   like the code (`kernel/`, `lib/`, `extension/`, `design/`, `bin/`). `tests/bootstrap.php` loads the autoloaders
   and the test toolkit; it does not boot the kernel.
2. **Suites** (4 minutes). Examples: `security`, `kernel-classes`, `lib` (nothing needed); `design` (files only);
   `kernel-search` (database tests roll back); `cjw_newsletter`, `ezoe` (a live installation, skipped without one);
   `mongodb-live` (running servers).
3. **Without a database** (3 minutes). `vendor/bin/phpunit` runs every suite with the `database` group excluded.
   `tests/runtests.php --dsn <dsn> --db-per-test` runs against a throwaway database, as CI's database job does.
4. **CI** (3 minutes). GitHub Actions runs the suites on PHP 8.1 to 8.5 for every push and pull request to `main`;
   a red run blocks.
5. **Only the tests of a change** (2 minutes). `tests/test-map.json` maps source files to the tests that cover them;
   the full run stays the check before a release.

### Common questions

**How long does the whole run take?** Many minutes on a busy machine; that is why the tests of a change are run
first. Run the full suite before a large pull request.

**Do tests change my installation?** DB-less tests do not; database tests need a throwaway database. Never point
`runtests.php` at a database you care about.

**Where are tests of extensions?** Some extensions have suites in `phpunit.xml`; an extension of your own keeps its
tests in its own repository (lesson 12.2).

### Pitfalls

- **Running on a shared server at full speed.** Use `nice -n 15` and one suite at a time.
- **Missing development dependencies.** `vendor/bin/phpunit` exists only after a `composer install` with development
  packages.
- **Reading only the dots.** Read the summary line and every `F`, `E`, `W` or `S` above it.

## Hands-on exercise

On the development installation, from its root folder.

1. List the suites: `grep -o '<testsuite name="[^"]*"' phpunit.xml`.
2. Count the test files: `find tests/tests -name "*Test.php" | wc -l`.
3. Run the security suite: `nice -n 15 vendor/bin/phpunit --testsuite security --no-coverage`.
4. Run one test file by its path, for example a test of the template engine: find one with
   `ls tests/tests/lib/eztemplate/ | head` and run it with `vendor/bin/phpunit <path>`.
5. Find which tests cover the class `eZINI` through the map: `grep -n "ezini" tests/test-map.json | head`.
6. Read one test of `eZINI` and write down one method call you did not know.
7. Read the exclusions of the default run: `grep -n -A16 "<groups>" phpunit.xml`.

## Expected result

- Step 1 lists more than twenty suites.
- Step 3 ends with `OK (<n> tests, <m> assertions)`.
- Step 4 ends with `OK` or explains a skipped test with `S`.
- Step 7 shows the excluded groups, among them `database`.
- The trainer asks each learner which suite they would run after changing a datatype.

## Quiz

1. Which file defines the test suites?
2. Which group does the default run exclude?
   A. `security` B. `database` C. `design` D. `lib`
3. How do you run only the `security` suite?
4. What do live-installation suites do when no installed site is present?
5. What is `tests/test-map.json` for?

<details>
<summary>Answers</summary>

1. `phpunit.xml` in the installation root.
2. **B.** `database` (with `mail-live`, `mongodb-live` and `network-live`).
3. `vendor/bin/phpunit --testsuite security`.
4. They skip each class with a reason.
5. It maps source files to the tests that cover them, so the tests of a change can be run first.

</details>

## Further reading

- [Testing and continuous integration](../../../../standards/testing-and-ci.md).
- [Quality checks](../../../../features/6.0/quality-checks.md): hooks, manifest, code style, PHPStan, the tests of a
  change.
- [Continuous integration](../../../../bc/6.0/ci.md).

## Related pages

- [Module 12: Tests and quality](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
