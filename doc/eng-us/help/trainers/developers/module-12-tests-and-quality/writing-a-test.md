---
title: "Lesson 12.2: Writing a test"
audience: trainer, developer
category: training
order: 122
summary: Learners write a DB-less PHPUnit test for their template operator, run it with the kernel's bootstrap, make it fail on purpose, and decide what deserves a test in an extension. 50 minutes.
keywords: developers course, PHPUnit, test case, TestCase, assertSame, DB-less test, tests/bootstrap.php, extension tests, test first, regression test, mysite_initials
type: help
---
# Lesson 12.2: Writing a test

A test is the cheapest way to know a change did not break something, and the best documentation of what code is
meant to do. In this lesson learners write a small PHPUnit test for the template operator of lesson 7.5, run it with
the kernel's test bootstrap, and see it catch a mistake.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 12: Tests and quality](README.md) |
| Duration | 50 minutes (15 presenting, 35 exercise) |
| Level | Intermediate |
| Audience | Developers |
| Prerequisites | [Lesson 12.1](the-test-suites.md) and [Lesson 7.5](../module-07-extensions/a-template-operator.md) |

## Learning objectives

After this lesson, learners can:

- write a test class extending `PHPUnit\Framework\TestCase` with test methods and assertions;
- test code without a database by calling it directly;
- run one test file with `vendor/bin/phpunit --bootstrap tests/bootstrap.php <file>`;
- read a failure message and fix the code or the test;
- choose what deserves a test: behavior others rely on, and every bug fixed.

## Trainer's notes

### How to present it

Sources: [Testing and continuous integration](../../../../standards/testing-and-ci.md) and a kernel test of a
template operator, for example in `tests/tests/kernel/common/`.

1. **Anatomy** (4 minutes). A class named `<Something>Test`, extending `TestCase`; public methods starting with
   `test`; each prepares input, calls the code and asserts the result with `assertSame()`, `assertTrue()`,
   `assertCount()` and others.
2. **DB-less** (3 minutes). The operator is plain PHP: call `modify()` with a value and parameters, read the value
   back. No kernel boot, no database, milliseconds per test.
3. **Where tests live** (3 minutes). The kernel's tests are in `tests/tests/`. An extension of your own keeps its
   tests in its own repository, for example `extension/mysite_hello/tests/`, and runs them with the kernel's
   bootstrap.
4. **Red, then green** (3 minutes). Break the code on purpose and watch the test fail with a clear message. A test
   that never failed has not proven anything.
5. **What to test** (2 minutes). Behavior other code relies on, edge cases (empty input, multibyte text), and every
   bug: write the test that shows the bug before fixing it.

### Common questions

**What about code that needs the database?** The kernel's toolkit has `ezpDatabaseTestCase` for tests tagged
`@group database`, run against a throwaway database with `tests/runtests.php`.

**Should I test templates?** Templates are checked with `eztemplatecheck.php`; logic belongs in PHP, where it can be
tested.

**How many assertions per test?** One behavior per test method; several assertions are fine when they describe one
behavior.

### Pitfalls

- **Tests that depend on each other.** Each test must work alone and in any order.
- **Tests that need the live site.** Use fixtures and stand-ins; never write tests that change real content.
- **Testing the implementation, not the behavior.** Assert the output, not private details, so refactoring does not
  break the test.

## Hands-on exercise

On the development installation, from its root folder.

1. Create `extension/mysite_hello/tests/MysiteInitialsTest.php`:

   ```php
   <?php
   /**
    * Tests of the template operator mysite_initials.
    */
   use PHPUnit\Framework\TestCase;

   require_once __DIR__ . '/../autoloads/mysitehellooperators.php';

   class MysiteInitialsTest extends TestCase
   {
       private function initials( $text, $separator = '' )
       {
           $operator = new mysiteHelloOperators();
           $value = $text;
           $operator->modify( null, 'mysite_initials', array(), '', '', $value, array( 'separator' => $separator ) );
           return $value;
       }

       public function testTwoWords()
       {
           $this->assertSame( 'AL', $this->initials( 'Ada Lovelace' ) );
       }

       public function testSeparatorAndLowerCase()
       {
           $this->assertSame( 'G.B.H', $this->initials( 'grace brewster hopper', '.' ) );
       }

       public function testEmptyText()
       {
           $this->assertSame( '', $this->initials( '   ' ) );
       }

       public function testMultibyte()
       {
           $this->assertSame( 'ÉÖ', $this->initials( 'élan öl' ) );
       }
   }
   ```

2. Run it: `vendor/bin/phpunit --bootstrap tests/bootstrap.php extension/mysite_hello/tests/MysiteInitialsTest.php`.
3. Break the operator on purpose: in `autoloads/mysitehellooperators.php` replace `mb_strtoupper( mb_substr(` with
   `strtoupper( substr(` (keep the brackets), and run the test again.
4. Read which test fails and why; restore the code and run again.
5. Add a test of your own for two spaces between words (`'Ada  Lovelace'`). Decide what the result should be, then
   run it.
6. Commit the test.

## Expected result

- Step 2: `OK (4 tests, 4 assertions)`.
- Step 3: `testMultibyte` fails: byte functions cut the multibyte letters, and the failure shows expected and actual
  values.
- Step 4: all tests pass again.
- Step 5: a fifth test passes with `AL` (the split on `\s+` handles several spaces).
- The trainer asks each learner what their new test protects against.

## Quiz

1. Which class does a PHPUnit test extend?
2. Which assertion compares type and value?
   A. `assertEquals()` B. `assertSame()` C. `assertTrue()` D. `assertCount()`
3. Why is the operator test fast?
4. When should you write a test for a bug?
5. Where does an extension of your own keep its tests?

<details>
<summary>Answers</summary>

1. `PHPUnit\Framework\TestCase` (or a toolkit class such as `ezpDatabaseTestCase` for database tests).
2. **B.** `assertSame()`.
3. It calls plain PHP without booting the kernel or a database.
4. Before fixing it: the test shows the bug, then proves the fix.
5. In its own repository, for example `extension/mysite_hello/tests/`.

</details>

## Further reading

- [Testing and continuous integration](../../../../standards/testing-and-ci.md).
- [Quality checks](../../../../features/6.0/quality-checks.md).
- [Coding standards](../../../../standards/coding-standards.md).

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
