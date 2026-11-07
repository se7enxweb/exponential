# Contributing to expcomponents: working on a fork of the kernel's libraries

This guide is for developers who want to fix or improve one of the **expcomponents**, Exponential's fork of the Zeta
Components (Apache License 2.0), the libraries the kernel uses for mail, archives, WebDAV, feeds, the REST layer and
more. It walks through a change from the clone to the release: getting the code, running the tests on several PHP
versions, the rules a change must keep, the commit message, the pull request, how releases and versions work and how
the forks stay in step with upstream. What the libraries are and why they are forked:
[Exponential Components (expcomponents)](../features/6.0/expcomponents.md).

Time: 30 minutes for the first setup; a small fix with its test is an afternoon.

## Contents

1. [Before you start](#before-you-start)
2. [Find the right repository](#find-the-right-repository)
3. [Clone and install](#clone-and-install)
4. [Run the tests](#run-the-tests)
5. [Run the tests on every supported PHP version](#run-the-tests-on-every-supported-php-version)
6. [Try a change inside an installation](#try-a-change-inside-an-installation)
7. [The rules a change keeps](#the-rules-a-change-keeps)
8. [License headers and notices](#license-headers-and-notices)
9. [Commit messages](#commit-messages)
10. [The ChangeLog](#the-changelog)
11. [Pull requests and review](#pull-requests-and-review)
12. [Releases and versions](#releases-and-versions)
13. [Syncing with upstream](#syncing-with-upstream)
14. [Giving a fix back upstream](#giving-a-fix-back-upstream)
15. [Security issues](#security-issues)
16. [Related pages](#related-pages)

## Before you start

You need:

- **git** and a **GitHub** account;
- **PHP** 7.4 or later on the command line, with the extensions the package's tests need (the archive tests, for
  example, need `bz2`, `posix` and `zlib`; GNU tar 1.34 is used to compare archives);
- **Composer** 2;
- for some packages a service: the mail tests can use an SMTP or IMAP server, the database tests a database; the
  package's `tests/` and its CI workflow say which.

Read the package's `README` or `docs/` folder once; the API you change is described there.

## Find the right repository

Each library is one repository: `https://github.com/se7enxweb/expcomponents-<name>`, where `<name>` is the package
name without `zetacomponents/`: `mail`, `archive`, `webdav`, `mvc-tools`, `console-tools` ... The table of all
packages is on the [feature page](../features/6.0/expcomponents.md#the-packages); which kernel files use which
package is in the [package reference](../specifications/6.0/expcomponents-packages.md).

If an error message names a class, the class prefix tells you the package: `ezcMail...` is mail, `ezcWebdav...` is
webdav, `ezcMvc...` is mvc-tools, `ezcConsole...` is console-tools, `ezcBase...` is base. In an installation,
`composer show --path se7enxweb/expcomponents-mail` prints where the installed copy is.

## Clone and install

Fork the repository on GitHub (button **Fork**), then:

```bash
git clone https://github.com/<you>/expcomponents-mail.git
cd expcomponents-mail
git remote add origin-se7enxweb https://github.com/se7enxweb/expcomponents-mail.git
git remote add upstream https://github.com/zetacomponents/Mail.git
composer update --no-interaction --prefer-dist
```

`composer update` (not `install`: the libraries ship no lock file) installs PHPUnit and `zetacomponents/unit-test`,
the test helpers all components share, into the clone's own `vendor/`. The default branch is `master`; create a
branch for your change:

```bash
git switch -c fix-smtp-timeout
```

## Run the tests

```bash
vendor/bin/phpunit                         # the whole suite, as phpunit.xml.dist defines it
vendor/bin/phpunit --filter SmtpTransport  # one test class or method
```

The suites are configured so that a deprecation fails the build (`convertDeprecationsToExceptions` or the PHPUnit
settings of the package), with `error_reporting=-1`. A test that prints a deprecation is a failing test, not noise.

The CI workflow of the package (`.github/workflows/tests.yml`, where the fork has one) also lints every source file
and fails on compile-time deprecations. Run the same check before you push:

```bash
for f in $(find src tests -name '*.php' -not -path 'tests/*/testfiles/*' -not -path 'tests/data/*'); do
    php -d error_reporting=-1 -l "$f" | grep -vi '^No syntax errors' ; done
```

It prints nothing when every file is clean.

## Run the tests on every supported PHP version

The kernel supports PHP 8.0 to 8.5 and runs on 7.4; its libraries must too. The CI of a fork runs the suite on each
version (7.4, 8.0, 8.1, 8.2, 8.3, 8.4 and 8.5) for every push and pull request. Locally, run at least the oldest and
the newest version you have:

```bash
php7.4 vendor/bin/phpunit
php8.5 vendor/bin/phpunit
```

The binary names depend on your system (`/opt/plesk/php/8.5/bin/php`, `/usr/bin/php8.5`, a container). If a version
is missing locally, push your branch to your own fork: the workflow runs there too.

What the PHP range means for code:

- no syntax newer than PHP 7.4 in `src/` (no `match`, no constructor property promotion, no enums, no `readonly`);
- no behavior that is deprecated on the newest PHP: explicitly nullable parameters (`?string $x = null`),
  `#[\ReturnTypeWillChange]` where an internal interface's method gains a return type, no `null` passed to string
  functions;
- union types and other PHP 8 features stay out until the kernel no longer runs on 7.4.

## Try a change inside an installation

To check a change against a real site before it is released, point a development installation at your branch with a
Composer path or VCS repository in its `composer.json`:

```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/<you>/expcomponents-mail" }
]
```

```bash
composer require --no-update se7enxweb/expcomponents-mail:dev-fix-smtp-timeout
composer update se7enxweb/expcomponents-mail
```

Then restart the PHP workers (PHP-FPM, or `./console exp:velocity restart`) and use the feature. Undo the two
changes in that installation's `composer.json` when you are done; never ship a `dev-` requirement.

## The rules a change keeps

1. **Class names stay.** No class, interface or constant is renamed or removed in a 1.x line. Every caller in the
   kernel, in extensions and in sites uses the `ezc` names.
2. **Public APIs stay compatible.** A public or protected method keeps its name, its parameters and their meaning. A
   new optional parameter at the end, a new method or a new option is fine; a changed default that callers can see
   needs a note in the `ChangeLog` and in the release notes.
3. **No namespaces are introduced into existing classes.** The libraries use the global namespace; they keep it.
4. **One concern per change.** A deprecation fix, a bug fix and a refactoring are separate commits.
5. **Every fix has a test** that fails without it, in the package's `tests/` folder, named as the package names its
   tests (`*_test.php`).
6. **No new runtime dependency** without discussion in the pull request. A component should stay installable on its own.
7. **The forks require each other by their fork names** (`se7enxweb/expcomponents-base ~1.8`), not by the upstream
   names. A new requirement on another component names its fork.
8. **`composer.json` `replace` stays** with `"zetacomponents/<name>": "self.version"`.

## License headers and notices

The libraries are licensed under the Apache License 2.0, and Apache 2.0 sets conditions for redistributing changed
code. Keep them, and the fork stays a fork anyone may use:

- **Never remove or change** the license header of a source file, the notice lines at the top of `LICENSE.txt`
  ("Zeta Components ...", "Apache Zeta Components ...", "Originally developed by eZ Systems as eZ Components ..."),
  `CREDITS`, or the `NOTICE` file every fork ships (the upstream notices and the fork's name).
- **Changes are recorded** (Apache 2.0, section 4(b), asks that changed files carry prominent notices that they were
  changed): each change is a commit with its own message and an entry in the package's `ChangeLog` (below), and the
  release notes list it. Ask the maintainers in the pull request if you think a file needs more than that.
- **New files** carry the same Apache 2.0 header as the files around them, with the Exponential project as the
  author of the new file.
- **Code from elsewhere** goes in only if its license is compatible with Apache 2.0 (MIT, BSD, Apache 2.0); GPL
  code cannot be copied into these libraries. Say where it comes from in the pull request.

The kernel that uses the libraries is GPL version 2 or later; how that combines with Apache 2.0 is explained on the
[feature page](../features/6.0/expcomponents.md#the-license). Not legal advice.

## Commit messages

The forks use the same message format as Exponential: a prefix, a sentence that says what changed, a blank line and a
body that says why.

```text
Updated: The SMTP transport gives up after the configured timeout when the server stops answering

A server that accepted the connection and then sent nothing kept the
transport waiting forever, because the read loop ignored the timeout of
ezcMailSmtpTransportOptions. It now checks the stream's timed_out flag
after each read and throws ezcMailTransportException.

- Test: a server stub that answers the greeting and then stays silent.
```

| Prefix | For |
|---|---|
| `Added:` | a new class, method, option or test helper |
| `Updated:` | a fix or a change of existing behavior |
| `Removed:` | something deleted (rare in a 1.x line) |
| `Renamed:` | something renamed (the package name, a file; never a public class) |

Write the message for a reader who has only the repository. No tool names, no links to private notes.

## The ChangeLog

Each package has a `ChangeLog` file at its root, newest release first. Add your entry under a heading for the next
release (the maintainers set the date when they release):

```text
1.10.3 - not released yet
^^^^^^^^^^^^^^^^^^^^^^^^^

- Fixed: The SMTP transport gives up after the configured timeout when the
  server stops answering.
```

The entries use `Fixed:`, `Added:`, `Changed:`, `Renamed:` and `Removed:` as the existing entries of the file do.

## Pull requests and review

1. Push your branch to your fork and open a pull request against `master` of
   `se7enxweb/expcomponents-<name>`.
2. Say what the change does, why, how you tested it (which PHP versions), and whether a caller can notice it.
3. The CI must be green on every PHP version of the matrix.
4. A maintainer reviews it. Changes that make the kernel need a newer release of the library are followed by a change
   of the kernel's `composer.json` constraint, in the kernel repository.

## Releases and versions

Releases are made by the maintainers of 7x and the Exponential Foundation; contributors do not tag. What every
contributor should know about them:

- **A published tag is permanent.** Once a tag is pushed, it is never moved, deleted or recreated, and a GitHub
  release is never edited to replace its files: Packagist and every installation that resolved the version keep
  the old contents. A mistake in a release is corrected by the next release, whose notes say what was wrong.
- **The next number is chosen from what is published**, not from memory: the maintainers list the existing tags and
  releases first:

  ```bash
  git fetch --tags
  git tag -l --sort=version:refname | tail -5    # sorted as versions, not as text
  gh release list --limit 10
  ```

  A plain text sort puts `1.5.10` before `1.5.9`; always sort as versions.
- **Only the last position counts up.** After `1.5.9` comes `1.5.10`, never `1.6.0`. Moving a higher position is a
  decision about what the release means, not a consequence of reaching nine.
- **Tags have no `v` prefix** (`1.10.2`), as upstream.
- **The fork's first release continues upstream's line:** upstream `1.10.1` is followed by the fork's `1.10.2`.
- **Before a tag:** the working tree is clean, the suite passes on every PHP version of the matrix, the `ChangeLog`
  has the release with its date, and `composer.json` is valid (`composer validate`).
- **Release notes** on GitHub list what changed, in the `ChangeLog`'s words.
- The kernel requires each fork in the tilde form (`~1.10.2`), so a patch release reaches installations with their
  next `composer update`, and a new minor line needs a change in the kernel.

## Syncing with upstream

The Zeta Components project still releases some packages. Its fixes are merged into the forks:

```bash
git fetch upstream
git log --oneline master..upstream/master     # what upstream has that the fork does not
git switch -c sync-upstream-2026-11
git merge upstream/master
vendor/bin/phpunit
```

- Resolve conflicts in favor of keeping both fixes; the fork's rename commit (package name, `replace`, homepage) stays.
- Name the merged upstream release or commit in the merge's message and in the `ChangeLog`.
- Run the suite on the whole PHP matrix; an upstream change may target only newer PHP versions.
- Open the pull request like any other change.

Before every fork release, the maintainers check how far the fork is behind upstream
(`git rev-list --left-right --count master...upstream/master` prints "ahead behind" counts) and merge what is missing.

## Giving a fix back upstream

A fix that is useful outside Exponential is offered to upstream as well: open a pull request against
`https://github.com/zetacomponents/<Name>` from a branch based on upstream's `master`, without the fork's rename
commit. The fork does not wait for upstream to accept it.

## Security issues

Do not open a public issue or a public pull request for a vulnerability. Report it privately by e-mail to
security@se7enx.com, as the kernel's `SECURITY.md` says. The fix is prepared in private and released with its notes.

## Related pages

- [Exponential Components (expcomponents)](../features/6.0/expcomponents.md)
- [Specification: the expcomponents packages](../specifications/6.0/expcomponents-packages.md)
- [Upgrade notes: the switch to expcomponents](../bc/6.0/expcomponents.md)
- [Contributing code](../help/community/contributing/contributing-code.md)
- [The CLA explained](../help/community/contributing/the-cla-explained.md)
