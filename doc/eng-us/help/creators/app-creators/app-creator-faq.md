---
title: "Application creators: frequently asked questions"
audience: app-creator, developer
category: app-creation
order: 650
summary: Short answers to the questions application creators ask most - classes not found, changes that do not show under Velocity, permissions, settings, forms refused, JSON, cron, queues, tests and where to ask.
keywords: FAQ, questions, troubleshooting, class not found, Velocity restart, access denied, form token, 403, settings not used, cron, queue, tests, app creator
type: help
---
# Application creators: frequently asked questions

Short answers with the page that explains each one.

## My new class is not found

Regenerate the autoloads (`php bin/php/ezpgenerateautoloads.php -e`) and restart Velocity's workers
([Autoloads and class naming](autoloads-and-class-naming.md)).

## My change works under Apache but not on Velocity

Workers keep classes and settings in memory: `./console exp:velocity deploy` ([Deploying with Velocity](deploying-with-velocity.md)).

## My view says "access denied"

The user lacks a function listed in the view's `functions`; add the policy to a role ([Policy functions and limitations](policy-functions-and-limitations.md)).

## My form post gets a 403

The form token is missing: post from a template-rendered form with `method="post"`, or send the token from your script
([Forms, CSRF protection and HTTP input](forms-csrf-and-http-input.md)).

## My setting is ignored

Another file wins: `./console exp:ini where <file>/<block>/<key> <siteaccess>`, then clear `--clear-tag=ini`
([Settings and INI files](settings-and-ini-files.md)).

## My new template is not used

Clear `template-override` and `template`; check the override rule with template path comments
([Template overrides](../website-creators/template-overrides.md)).

## How do I return JSON?

An ezjscore server function, or the REST API for other programs ([JSON endpoints with ezjscore](json-endpoints-with-ezjscore.md)).

## My cronjob script is not found

The extension is active only in some siteaccesses: run with `-s <siteaccess>`, and check `ExtensionDirectories[]` in
`cronjob.ini` ([Cronjobs for applications](cronjobs-for-applications.md)).

## Where does slow work go?

A queue job, a cron part or a content job ([Background work](background-work-patterns.md)).

## How do I test without a database?

Keep logic in plain classes and test them with PHPUnit ([DB-less PHPUnit tests](db-less-phpunit-tests.md)).

## Where do I ask or report?

The help's **Report an error** page and [When something goes wrong](../../when-something-goes-wrong.md); the community
places are on [Who is the Exponential Foundation?](../../who-is-the-exponential-foundation.md).

## Related pages

- [Application creators: start here](app-creators-start-here.md)
- [The application creator's reading path](app-creators-reading-path.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
