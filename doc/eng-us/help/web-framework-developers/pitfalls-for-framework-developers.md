---
title: Ten pitfalls for framework developers
audience: web-framework-developer, developer
category: web-frameworks
order: 500
summary: The ten mistakes developers from Symfony, Laravel, Django, Rails and similar frameworks make most often in their first Exponential project, each with the symptom, the cause and the fix - from unescaped template output and stale caches to INI layer order, persistent workers and SQL on content tables.
keywords: pitfalls, mistakes, gotchas, troubleshooting, wash, escaping, caches, INI order, persistent workers, statics, autoloads, content tables, permissions, siteaccess, web framework developer
type: help
---
# Ten pitfalls for framework developers

This page is for developers in their first weeks with Exponential. Each pitfall comes from a habit that is right in
another framework and wrong here.

## Contents

1. [Printing without wash](#1-printing-without-wash)
2. [A new class is "not found"](#2-a-new-class-is-not-found)
3. [A setting does not take effect](#3-a-setting-does-not-take-effect)
4. [A template change does not show](#4-a-template-change-does-not-show)
5. [State leaks between requests](#5-state-leaks-between-requests)
6. [Writing SQL into content tables](#6-writing-sql-into-content-tables)
7. [A table for what should be content](#7-a-table-for-what-should-be-content)
8. [Checking permissions only in templates](#8-checking-permissions-only-in-templates)
9. [Forgetting the siteaccess on the command line](#9-forgetting-the-siteaccess-on-the-command-line)
10. [Comparisons with symbols](#10-comparisons-with-symbols)

## 1. Printing without wash

**Symptom:** text with `<` or `&` breaks the page, or a script runs. **Cause:** templates do not escape automatically.
**Fix:** `{$value|wash}` on everything from users or the database ([The view layer](templates-vs-twig-and-blade.md#five-differences-that-bite)).

## 2. A new class is "not found"

**Cause:** classes are found through generated class maps, not by PSR-4 for extension classes.
**Fix:** `php bin/php/ezpgenerateautoloads.php -e`, then deploy; `--kernel` for kernel classes
([Autoloads and class naming](../creators/app-creators/autoloads-and-class-naming.md)).

## 3. A setting does not take effect

**Causes:** the INI cache; or the value is overridden by a later layer, for example an extension's global settings win
over `settings/siteaccess/`. **Fix:** `./console exp:ini where <file>/<block>/<key> <siteaccess>`, then
`php bin/php/ezcache.php --clear-tag=ini` ([Configuration](configuration-ini-files.md#the-layers-and-their-order)).

## 4. A template change does not show

**Causes:** the view cache, a cache block, or the override cache for a new template file.
**Fix:** clear `template-override` after adding a template file, clear `content` and `template-block` when cached
output is stale; `exp:velocity deploy` does all of it in order ([Caching](caching-layers.md)).

## 5. State leaks between requests

**Symptom:** one user sees another user's data, or a listener runs twice, only under Velocity. **Cause:** persistent
workers keep static properties and attached listeners. **Fix:** no per-request state in statics; register listeners in
settings ([Servers and deployment](servers-and-deployment.md#rules-for-persistent-workers)).

## 6. Writing SQL into content tables

**Symptom:** a change does not appear, or appears without history, search or cache updates. **Cause:** content tables
are not your tables. **Fix:** `eZContentFunctions` or the operations, which run workflows and clear caches
([The content model](the-content-model-for-orm-users.md#reading-and-writing-from-code)).

## 7. A table for what should be content

**Symptom:** you are building an admin screen, a revisions table and a translations table. **Fix:** make it a content
class and get all three ([ORM](orm-and-ezpersistentobject.md#content-class-or-own-table)).

## 8. Checking permissions only in templates

**Cause:** hiding a link is not access control. **Fix:** name the function in the view's `functions`, check object
access in the view, filter lists in the query ([Authorization](authorization-roles-and-policies.md)).

## 9. Forgetting the siteaccess on the command line

**Symptom:** a command or cron part cannot find your module, setting or job type. **Cause:** your extension is active
only in a siteaccess. **Fix:** `-s <siteaccess>` ([Command line](cli-and-console-commands.md#siteaccess-user-and-root)).

## 10. Comparisons with symbols

**Symptom:** an `{if}` behaves randomly and the log shows a parser error. **Cause:** the template language has no infix
operators. **Fix:** `gt`, `lt`, `eq`, `ne`, `and`, `or` ([No infix comparison](../template-language/smarty-differences-in-depth.md#no-infix-comparison)).

## Related pages

- [Checklists for framework developers](checklists-for-framework-developers.md)
- [Common template mistakes](../designers/templates/common-template-mistakes.md)
- [When something goes wrong](../when-something-goes-wrong.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
