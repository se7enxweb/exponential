---
title: Symfony and the legacy bridge
audience: app-creator, developer
category: app-creation
order: 640
summary: When an application needs Symfony - the legacy bridge that runs the Exponential 6 kernel inside the Symfony platform, which line fits which platform, what stays in legacy, and how to plan a gradual move.
keywords: Symfony, legacy bridge, legacy-bridge, Twig, platform, migration, legacy_admin, ngsymfonytools, site-legacy-bundle, hybrid, app creator
type: help
---
# Symfony and the legacy bridge

This page is for application creators who consider Symfony for part of an application, or who maintain a project that
runs both. The legacy bridge is a Symfony bundle that loads the Exponential 6 kernel inside a Symfony platform
installation; one web application then serves the legacy admin and templates and the Symfony stack, on the same
database ([Legacy bridge](../../../features/6.0/legacy-bridge.md)).

## Contents

1. [When it is worth it](#when-it-is-worth-it)
2. [The pieces](#the-pieces)
3. [Which line for which platform](#which-line-for-which-platform)
4. [Planning a gradual move](#planning-a-gradual-move)

## When it is worth it

| Use the bridge when | Stay on the legacy kernel alone when |
|---|---|
| you move a site to the Symfony platform and editors must keep working | your application is built from extensions, modules and templates |
| you want Twig or Symfony routing for the public site, with the legacy admin for editors | you do not need Symfony features |
| you keep legacy extensions available in a platform project | one stack is easier to run and staff |

## The pieces

From the feature page: `se7enxweb/legacy-bridge` (the bundle), `se7enxweb/ibexa-legacy-bridge` (a port for a later
platform generation), `se7enxweb/site-legacy-bundle`, `se7enxweb/ngsymfonytools` and `se7enxweb/exponential-legacy-installer`.
The legacy side is reached through a siteaccess named `legacy_admin`. The specification is
[Legacy bridge bundle](../../../specifications/6.0/legacy-bridge-bundle.md).

## Which line for which platform

The bundle has one line per platform generation (the platform 2.5 line on `master`, later lines on their own branches);
the table with Symfony and PHP versions is on [Legacy bridge](../../../features/6.0/legacy-bridge.md#which-version-for-which-platform).
Check the product family before promising a stack: not every platform product carries the legacy kernel or the layouts
editor ([What Exponential is: the product family](../../leadership/what-exponential-is.md)).

## Planning a gradual move

1. Keep content classes, roles and editing in the legacy kernel.
2. Move public rendering page type by page type to Twig.
3. Keep business logic in services both sides can call.
4. Retire legacy templates only when nothing renders them.
5. Test both stacks on every release.

## Related pages

- [Architecture for application creators](architecture-for-app-creators.md)
- [Migrating from eZ Platform and Ibexa](../../../install/16-migrating-from-ez-platform-and-ibexa.md)
- [Platform site bundles](../../../features/6.0/platform-site-bundles.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
