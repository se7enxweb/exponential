# RAD tools: security

Read this page if you let administrators use the RAD tools (the extension and module wizards under `setup/rad`), or
if you deploy extensions they generated. A RAD tool takes what somebody typed and writes it into files that will be
**deployed and included**. Every field is therefore a code path, and every generated file is part of the attack
surface of every installation it ends up on, not only this one. This page lists the eight guards (RAD-01 to RAD-08)
and what they mean for you.

## In short

| | |
|---|---|
| What changed | Values typed into the wizards can no longer break out of comments or INI lines, write outside `extension/<name>`, overwrite an extension, or leak a database password. Generated templates escape output and generated views validate their input. |
| Who is affected | Anyone who generated an extension with the wizards before the fix (RAD-01 was a real hole). |
| How to check | Review generated `ezinfo.php` and `<name>operators.php` files for code after a closing `*/`. |
| How to fix | Regenerate older extensions with the current wizard, or review their comments by hand. Keep the `ezformtoken` extension active. |

## Two guards at the door

The values are checked where they are accepted, not at each of the several dozen places they are written
(`kernel/setup/expextensionwizard.php`):

| Guard | What it makes safe |
|---|---|
| `expExtensionWizard::commentText()` | What may stand inside a generated PHP comment. |
| `expExtensionWizard::iniValue()` | What may stand on the right of an INI setting. |

`expExtensionWizard::text()`, which every free-text field goes through, calls the first. A value that never enters
the wizard cannot leave it.

## RAD-01: a value cannot close the comment it is written in

An Exponential INI file is a PHP file whose whole body is one comment:

```php
<?php /* #?ini charset="utf-8"?
[ExtensionSettings]
ActiveExtensions[]=my_extension
*/ ?>
```

Generated PHP puts the title, author and summary into a doc comment. A value containing `*/` **closes that
comment**, and everything after it is PHP, in a file that will be included.

This was a real hole. Before the fix, an author of:

```
*/ ?><?php echo "OWNED"; /*
```

produced `ezinfo.php` and `<name>operators.php` files that **executed** when included; this was confirmed by
including them and reading the output. The wizard is admin-only, so it was privilege escalation from "may use the
RAD tools" to "arbitrary PHP". And because the extension is a deliverable, the hole travelled with it to every
installation it was deployed on.

`commentText()` rewrites `*/` (and `**/`, and any run of stars before the slash) and neutralises `<?` and `?>`. The
test drives six payloads through both wizards, lints every generated file, **includes it**, and asserts that nothing
the payload asked for happens.

## RAD-02: a value cannot become an INI setting of its own

A newline in a value ends the setting; what follows is read as another one. A title of:

```
harmless
ActiveExtensions[]=evil
```

would otherwise add an active extension. `iniValue()` folds every line ending to a space and limits the length.

## RAD-03: generated PHP is inert, whatever was typed

A second line of defence behind RAD-01: in the test, every generated `.php` file is linted **and executed**, for every
payload, in both wizards. A file that parses but does something is as bad as one that does not parse.

## RAD-04: nothing is written outside `extension/<name>`

- The name is held to `[a-z][a-z0-9_]{2,40}`: `../../../tmp/evil` becomes `tmp_evil`, `/etc/cron.d/evil` becomes
  `etc_cron_d_evil`, `..` becomes nothing.
- The resolved target is checked again to lie under `extension/` before a byte is written.
- The test walks what landed on disk and asserts it is **exactly** what the preview showed: no more files, no fewer.
- Written files get `chmod 0644`; the umask of whatever ran the request is not a permission policy. The test asserts
  nothing is group- or world-writable.
- A database that is a file is confined the same way: `safePath()` (`kernel/setup/expmoduleextensionwizard.php`)
  refuses `..`, absolute paths outside the installation, and any path containing a null byte.

## RAD-05: an existing extension is never overwritten

The write refuses, and says so, when `extension/<name>` exists. A wizard that can overwrite a design somebody is
using is an accident waiting for a typed name. The archive is still offered, because handing somebody a copy to
compare with is harmless.

## RAD-06: generated templates escape what they print

Every `{$…}` in a generated template goes through `wash`, `ezurl`, `i18n` or `l10n`, **including values the template
made itself**, such as a loop counter or a row-striping class. A rule with exceptions is one somebody has to reason
about every time they edit the file. The test parses each generated template and fails on any printed value that is
not escaped.

## RAD-07: generated views accept only what they offer

The generated admin module takes a table name, a sort column, a page size and an offset from the address. Each is
checked against what the extension itself declares:

- the table against the generated registry; anything else is a proper 404, not a query;
- the sort column against the class definition;
- the page size against `25 / 50 / 250`;
- the offset is clamped into the list and onto a page boundary.

The edit view writes only columns the table has, casting each value to the column's type. Removing asks first and
acts only on a confirmed POST.

POST protection comes from the `ezformtoken` extension, which validates a per-session token on every POST from a
logged-in user and adds the field to every form. A generated module inherits it, and the generated README says so,
because an installation without that extension has unprotected POSTs everywhere, not only here.

## RAD-08: secrets are not shown again or written into the extension

- The external-database password is **never** rendered back into the form. A password in an input's `value` is a
  password in the page source, in the browser cache and in every proxy in between.
- It is **not** written into the generated INI file. The file carries `Password=` and a comment pointing to
  `settings/override/<name>.ini.append.php`, which belongs outside version control.

## What is deliberately not defended against

An administrator who may use the RAD tools can already run code on the server by other means: writing a template,
installing an extension, editing an INI file. These guards exist because a generated extension **leaves this
installation**, and its holes travel with it.

Connecting to any host and port is the point of the external-database option, so it is not restricted. On a host
where that matters, let fewer people reach the wizard (the `setup/setup` policy) instead of making it useless.

## Running the tests

The RAD security checks run on the installation's own code and write nothing outside `var/tmp`. They cover the points
above: path validation, escaping in generated templates and the handling of the external-database password. Ask the
maintainers for the test script of the release you run.

## Related pages

- [RAD tools](../../features/6.0/rad-tools.md)
- [RAD extension points](rad-extension-points.md) and [RAD extension surface](rad-extension-surface.md)
- [Specification: the 6.0.13 security and stability hardening](../../specifications/6.0/security-hardening-6.0.13.md)
- [Specification: the August 2026 security patches](../../specifications/6.0/security-hardening-2026-08.md)
- [Security defaults of September 2026](../../specifications/6.0/security-defaults-2026-09.md)
- [Datatype and input hardening (27 September 2026)](../../specifications/6.0/datatype-input-hardening.md)
- [February 2026](../../history/2026/2026-02.md)
