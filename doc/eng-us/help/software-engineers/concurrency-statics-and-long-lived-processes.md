---
title: Concurrency, statics and long-lived processes
audience: software-engineer, developer
category: software-engineering
order: 340
summary: How to write Exponential code that is correct when one PHP process serves many requests and many processes serve one site - static caches and singletons, idempotent registrations, exit and cleanExit, class_exists guards after a deploy, database connections and transactions, file races between users and nodes, and how to test for these bugs.
keywords: concurrency, statics, static properties, singleton, long-lived process, persistent worker, Velocity, idempotent, registrations, exit, cleanExit, class_exists, race condition, file locking, warm-up, software engineer
type: help
---
# Concurrency, statics and long-lived processes

This page is for engineers who write or review PHP for Exponential. Two facts make some classic PHP habits unsafe:
under Velocity one process serves many requests, and on any server many processes (and sometimes several machines)
serve one site at the same time. The rules here come from [Kernel architecture](../../standards/architecture/kernel-architecture.md#servers-velocity-php-fpm-and-frankenphp)
and [the worker pool specification](../../specifications/6.0/velocity-worker-pool.md).

## Contents

1. [Two kinds of sharing](#two-kinds-of-sharing)
2. [Statics and singletons](#statics-and-singletons)
3. [Registrations must be idempotent](#registrations-must-be-idempotent)
4. [Ending a request](#ending-a-request)
5. [Code that changes under a running worker](#code-that-changes-under-a-running-worker)
6. [Resources the application must manage](#resources-the-application-must-manage)
7. [Races between processes](#races-between-processes)
8. [Testing for these bugs](#testing-for-these-bugs)

## Two kinds of sharing

| Sharing | Where | Typical bug |
|---|---|---|
| In time: one process, many requests | Velocity persistent workers, the warm-up | a value from request A appears in request B |
| In space: many processes, one site | every server, several web nodes, cronjobs beside web requests | two processes write the same file or row |

## Statics and singletons

The kernel uses static caches and singletons widely (`eZINI::instance()`, `eZDB::instance()`, per-class caches).
Velocity restores static class properties to their start-up snapshot after each request, so a static cache does not
leak between requests in a normal worker. Two cases still go wrong:

- **State set during the warm-up** becomes every worker's baseline. A warm-up resets user-class statics to their
  defaults and keeps only configuration and type registries.
- **State the snapshot cannot see**: globals named in `keepGlobals`, registries filled once with `include_once`,
  resources held in statics (connections, file handles).

The rule for kernel code: **no per-request data in statics without a reset.** Prefer passing values as parameters,
and keep request-scoped caches keyed on something that changes per request.

## Registrations must be idempotent

A listener, handler or callback attached once per request in a long-lived worker accumulates: the second request runs
it twice. Register in a way that is a no-op when the registration exists already, and name listeners as
`Class::method` strings so duplicates are detectable. This applies to `ezpEvent` listeners, template operator
registrations and autoloaders ([Events, extension points and plugins](events-extension-points-and-plugins.md)).

## Ending a request

- Do not use `exit` for flow control. Kernel code ends a request through `eZExecution::cleanExit()`.
- Under Velocity, `exit` and `die` are rewritten to end the request, not the worker.
- Do not wrap a download followed by `cleanExit()` in a `catch ( Exception )` block that also catches the
  request-ending signal: the page is appended to the download and the output is doubled.

## Code that changes under a running worker

Workers inherit the classes the warm-up loaded. After a deploy:

- a **new class** named in a shipped INI file is unknown to a worker started before the deploy; guard its use with
  `class_exists()` where the kernel loads it, as `ezpKernelWeb` does;
- a **changed class** reaches the workers only after `exp:velocity restart` or `exp:velocity deploy`;
- a **changed template** or stylesheet needs no restart, only the template caches and Velocity's response cache.

## Resources the application must manage

| Resource | What to do |
|---|---|
| Database transaction state | commit or roll back before the request ends; never leave a transaction open |
| Database connection in the warm-up | close it before the fork, so workers do not share a socket |
| File handles, stream contexts, cURL handles | close them in the request that opened them |
| Signal handlers | do not install them in request code |

## Races between processes

- **Files under `var/`** may be written by different operating system users (the web server user, the user that runs
  cron or the console, Velocity's worker user). Create files with the installation's file modes and test with mixed
  users ([File modes](../../bc/6.0/file-modes.md)).
- **Cache files** go through `eZClusterFileHandler`, which handles concurrent generation on shared storage.
- **Cronjob parts** must be safe to run twice and to be interrupted.
- **SQLite** serializes writers with a lock file; long write transactions block other writers.

## Testing for these bugs

- Run a test twice in the same process: state left behind by the first run shows up in the second.
- Under Velocity, request several distinct URLs in a row and check that each returns its own content.
- A test that passes alone and fails in the whole run shares state with another test; run the two files together to
  find the pair ([CI gates](ci-gates.md)).
- Watch the Velocity log for workers replaced over the memory ceiling: a leak.

## Related pages

- [Velocity persistent workers](velocity-persistent-workers.md)
- [The request lifecycle](request-lifecycle.md)
- [Performance in extensions](../extension-development/part-3-extension-developer/performance-in-extensions.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
