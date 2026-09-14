# RAD tools — security

## The rule

A RAD tool takes what somebody typed and writes it into a file that will be
**deployed and included**. That makes every field a code path, and every
generated file part of the attack surface of whatever installation it ends up
on — not only this one.

Two guards, applied where values are accepted rather than at each of the several
dozen places they are written:

| | |
|---|---|
| `expExtensionWizard::commentText()` | What is safe inside a generated PHP comment. |
| `expExtensionWizard::iniValue()` | What is safe on the right of an ini setting. |

`expExtensionWizard::text()` — which every free-text field goes through — calls
the first. A value that never enters the wizard cannot leave it.

Tests: `ai/bin/one/test_rad_security.php`.

---

## RAD-01 — A value cannot close the comment it is written inside

An eZ ini file is a PHP file whose whole body is one comment:

```php
<?php /* #?ini charset="utf-8"?
[ExtensionSettings]
ActiveExtensions[]=my_extension
*/ ?>
```

Generated PHP puts the title, author and summary into a doc comment. A value
containing `*/` **closes that comment**, and everything after it is no longer a
comment — it is PHP, in a file that will be included.

This was a real hole, not a theoretical one. Before the fix, an author of:

```
*/ ?><?php echo "OWNED"; /*
```

produced `ezinfo.php` and `<name>operators.php` that **executed** when included —
confirmed by including them and reading the output. The wizard is admin-only, so
it was privilege escalation from *may use the RAD tools* to *arbitrary PHP*; and
since the extension is a deliverable, the hole travelled with it to every
installation it was deployed on.

`commentText()` rewrites `*/` (and `**/`, and any run of stars before the slash)
and neutralises `<?` and `?>`. The test drives six payloads through both wizards,
lints every generated file, **includes it**, and asserts nothing the payload
asked for happens.

## RAD-02 — A value cannot become an ini setting of its own

A newline in a value ends the setting; what follows is read as another one. A
title of:

```
harmless
ActiveExtensions[]=evil
```

would otherwise add an active extension. `iniValue()` folds every line ending to
a space and bounds the length.

## RAD-03 — Generated PHP is inert whatever was typed

Belt and braces over RAD-01: every generated `.php` is linted **and executed** in
the test, for every payload, in both wizards. A file that parses but does
something is as bad as one that does not parse.

## RAD-04 — Nothing is written outside `extension/<name>`

- The name is held to `[a-z][a-z0-9_]{2,40}` — `../../../tmp/evil` becomes
  `tmp_evil`, `/etc/cron.d/evil` becomes `etc_cron_d_evil`, `..` becomes nothing.
- The resolved target is re-checked to sit under `extension/` before a byte is
  written.
- The test walks what landed on disk and asserts it is **exactly** what the
  preview showed — no more files, no fewer.
- Written files are `chmod 0644`; the umask of whatever ran the request is not a
  permission policy. The test asserts nothing is group- or world-writable.
- A database that is a file is confined the same way: `safePath()` refuses `..`,
  absolute paths outside the installation, and any path containing a null byte.

## RAD-05 — An existing extension is never written over

The write refuses when `extension/<name>` exists and says so. A wizard that can
overwrite a design somebody is using is not a wizard; it is an accident waiting
for a typed name. The archive is still offered, since handing somebody a copy to
compare against is harmless.

## RAD-06 — Generated templates escape what they print

Every `{$…}` in a generated template goes through `wash`, `ezurl`, `i18n` or
`l10n` — **including values the template itself made**, such as a loop counter or
a row-striping class. A rule with exceptions is one somebody has to reason about
every time they edit the file. The test parses each generated template and fails
on any printed value that is not escaped.

## RAD-07 — Generated views accept only what they offer

The generated admin module takes a table name, a sort column, a page size and an
offset from the address. Each is checked against what the extension itself
declares:

- the table against the generated registry — anything else is a proper 404, not
  a query;
- the sort column against the class definition;
- the page size against `25 / 50 / 250`;
- the offset clamped into the list and onto a page boundary.

The edit view writes only columns the table has, casting each value to the type
the column holds. Removing asks first and acts only on a confirmed POST.

POST protection is the `ezformtoken` extension, which validates a per-session
token on every POST from a logged-in user and injects the field into every form.
A generated module inherits it — and the generated README says so, because an
installation without that extension has unprotected POSTs everywhere, not only
here.

## RAD-08 — Secrets are not echoed back or written into the extension

- The external-database password is **never** rendered back into the form. A
  password in an input's `value` is a password in the page source, in the
  browser cache and in every proxy between.
- It is **not** written into the generated ini. The file carries `Password=` and
  a comment pointing at `settings/override/<name>.ini.append.php`, which belongs
  outside version control.

---

## What is deliberately not defended against

An administrator who may use the RAD tools may already run code on the server by
other means — writing a template, installing an extension, editing an ini. These
guards exist because a generated extension **leaves this installation**, and its
holes travel with it.

Connecting to an arbitrary host and port is the point of the external-database
option, so it is not restricted. On a host where that matters, the wizard should
be reachable by fewer people — `setup/setup` policy — rather than made useless.

## Running the tests

```sh
php ai/bin/one/test_rad_security.php --allow-root-user
```
