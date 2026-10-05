# Security Policy

## Supported Versions

Exponential 6.0 is the maintained line. Security fixes are made on the `main` branch (the 6.0.15 line, `dev-main`)
and published with the next 6.0.x release. A published tag is never changed: a fix for a problem in `v6.0.14` or
an earlier tag reaches you as the next release, not as a corrected copy of the old one.

| Version | Branch or tags | Supported |
| ------- | -------------- | ------------------ |
| 6.0.x, current line | `main` (`dev-main`), and the next tag cut from it | :white_check_mark: |
| 6.0.x, released tags (`v6.0.0` to `v6.0.14`) | | fixed by upgrading to the newest 6.0.x |
| 5.x, 4.x, 3.x and older | | :x: |

Hardening advice for a production site is in
[chapter 13 of the installation book](doc/install/13-security-hardening.md).

## Reporting a Vulnerability

Please report security issues privately by e-mail to security@se7enx.com, not in the public issue tracker or the
discussions. You can expect a first answer within one business day.

Include:

- the version (`composer show se7enxweb/exponential`, or the tag or commit you run) and the PHP version;
- the engine that serves the site (Exponential Velocity, FrankenPHP, Apache or nginx with PHP-FPM) and the database;
- what an attacker can do, and the steps to reproduce it.

Leave out passwords, secrets and personal data.

You will be told whether the report is accepted or declined, and kept informed while a fix is prepared. An accepted
issue is fixed on `main` and released with the next 6.0.x version.
