---
title: A local test installation
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 820
summary: Install Exponential locally on SQLite in one command for app development, check that the REST API and the remote services are active (and activate ezprestapi if not), prepare a test user, role, key and OAuth client, reach it from an emulator or a phone, and reset it between test runs.
keywords: local installation, exp:install, SQLite, test installation, ezprestapi, ActiveExtensions, test user, API key, OAuth client, emulator, 10.0.2.2, phone, reset, development, mobile app
type: help
---
# A local test installation

This page is for app developers who want an Exponential installation of their own to develop and test against. With
SQLite it needs no database server and installs in minutes.

## Contents

1. [Install](#install)
2. [Check the APIs](#check-the-apis)
3. [Prepare it for the app](#prepare-it-for-the-app)
4. [Reach it from a device](#reach-it-from-a-device)
5. [Reset between runs](#reset-between-runs)

## Install

From a checkout of Exponential with its requirements met
([Requirements](../../../install/02-requirements.md)):

```bash
./console exp:install
```

That installs the multisite package on SQLite in `eng-US` with the siteaccesses `site` and `admin` and prints the
addresses and the login ([Installing Exponential in one command](../../../features/6.0/install-in-one-command.md)). A
fuller development setup, with a web server and development settings, is in
[Setting up a development environment](../../creators/app-creators/development-environment.md).

## Check the APIs

Run the three probes of [Testing against a demo](testing-against-a-demo.md#probe-before-you-build) against your local
address. If REST answers `404`, activate `ezprestapi` the way the guide describes
([Migrating from ezprestapiprovider](../../../guides/rest-api-v1-and-v2.md#6-migrating-from-ezprestapiprovider)):

```ini
# settings/override/site.ini.append.php
[ExtensionSettings]
ActiveExtensions[]=ezprestapi
```

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/ezpgenerateautoloads.php -e
```

Then reload PHP-FPM, or restart Velocity if it serves the site. If the remote services are missing, the same
procedure with `ActiveExtensions[]=expservices` applies
([Remote services and apps, section 1](../../../guides/remote-services-and-apps.md#1-check-that-it-answers-1-minute)).

## Prepare it for the app

1. **Content:** a folder for the app with a few items of each class the app shows; give the folder a remote id the
   app's configuration uses ([Node ids, remote ids and deep links](../content-modeling/node-ids-remote-ids-and-deep-links.md)).
2. **A role and a user** exactly like production's integration user
   ([Permissions for app users](../publishing-workflow/permissions-for-app-users.md)).
3. **A personal API key** of that user for tests (`apikey/create` in a role first)
   ([Personal API keys for tools](../auth/personal-api-keys-for-tools.md)).
4. **An OAuth client** in **Setup > oAuth admin**, with the redirect address of your development back end, and
   `oauth/authorize` for the test users ([OAuth2 authorization code flow](../auth/oauth2-authorization-code-flow.md)).
5. **An expservices token** of the test user, if the app uses the remote services
   ([expservices tokens in apps](../auth/expservices-tokens-in-apps.md)).

## Reach it from a device

- The Android emulator reaches the host computer at `10.0.2.2`; the iOS simulator shares the host's network, so
  `localhost` works there.
- A real phone needs the computer's address on the local network and a server that listens on it.
- Use HTTPS as in production where the platform requires it for app traffic; a local certificate or a tunnel are the
  usual ways. Keep plain HTTP exceptions out of release builds.

## Reset between runs

- Keep a copy of the SQLite database file after preparing it, and copy it back before a test run, so every run starts
  from the same content and ids.
- Or script the preparation with the remote services (`expnode::create` with fixed `remote_id`s) and run it after a
  fresh install.

## Related pages

- [Testing apps against Exponential](README.md)
- [Testing applications](../../creators/app-creators/testing-applications.md)
- [Getting started](../../getting-started.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
