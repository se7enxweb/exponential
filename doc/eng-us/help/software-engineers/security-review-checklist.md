---
title: Security review checklist
audience: software-engineer, developer
category: software-engineering
order: 730
summary: A checklist for reviewing a change to Exponential or an extension for security - access control, input and output, SQL and shell, CSRF, files and uploads, redirects, secrets, logging and privacy, HTTP headers and CSP, long-lived workers and dependencies - each item with the rule it checks and where that rule is written.
keywords: security review, checklist, code review, access control, XSS, SQL injection, CSRF, upload, redirect, secrets, privacy, CSP, dependencies, OWASP, software engineer
type: help
---
# Security review checklist

This page is a checklist for the security part of a code review. Use it with the general
[Code review checklist](code-review-checklist.md). Each item names the rule it checks; the background is on
[Security engineering overview](security-engineering-overview.md).

## Contents

1. [Access control](#access-control)
2. [Input and output](#input-and-output)
3. [Database and shell](#database-and-shell)
4. [Forms, redirects and files](#forms-redirects-and-files)
5. [Secrets, logs and privacy](#secrets-logs-and-privacy)
6. [HTTP layer and browser](#http-layer-and-browser)
7. [Runtime and dependencies](#runtime-and-dependencies)

## Access control

- [ ] Every new view names its policy functions in `functions` of `module.php`.
- [ ] No new entry in `[RoleSettings] PolicyOmitList[]` or `AnonymousAccessList[]` without a reason written in the change.
- [ ] Access is decided by policies, never by comparing a user or group id.
- [ ] Fetches and lists apply the user's read permission (the content fetches do; custom SQL must not bypass them).
- [ ] A new policy function reaches existing installations through an installer or update script.

Source: [Modules, views and access control](modules-views-and-access-control.md).

## Input and output

- [ ] Request input is read through `eZHTTPTool`, not `$_GET` or `$_POST`.
- [ ] Input is validated where it enters (a datatype in `validateObjectAttributeHTTPInput()`).
- [ ] Every variable in a template is printed with `|wash`, or through an operator that escapes.
- [ ] HTML built in PHP (an operator, a view) is escaped there.
- [ ] Header values carry no CR or LF from input.

Source: [Kernel architecture](../../standards/architecture/kernel-architecture.md), [Datatype and input hardening](../../specifications/6.0/datatype-input-hardening.md).

## Database and shell

- [ ] Every string in SQL goes through `$db->escapeString()`; every number is cast with `(int)`.
- [ ] `ORDER BY` columns come from a whitelist.
- [ ] Shell arguments go through `escapeshellarg()`; better, no shell at all.

Source: [Security hardening of 6.0.13](../../bc/6.0/hardening.md), [Writing portable SQL](writing-portable-sql.md).

## Forms, redirects and files

- [ ] Every state-changing request is a POST with the form token (`ezformtoken`); no state change on GET.
- [ ] Redirect targets go through the redirect manager and the allowed hosts.
- [ ] Downloads go through `content/download`, which checks read access, or check it themselves.
- [ ] Uploads are checked for type and content; SVG goes through the sanitizer.
- [ ] Paths built from input cannot leave their directory.
- [ ] Outgoing requests to URLs from input refuse private, loopback and link-local addresses.

## Secrets, logs and privacy

- [ ] No credential, key or host-specific value in committed files; secrets live in `settings/override/` or the
      environment.
- [ ] No password, token, key or session id in a log, the audit trail, an error page or a debug message.
- [ ] New log lines follow the masking of the log viewer and the request logs (addresses, e-mail, query values).
- [ ] Security-relevant actions are recorded in the audit trail ([The audit trail](../../features/6.0/audit-trail.md)).

## HTTP layer and browser

- [ ] No inline script without the CSP nonce; no `on...=` attributes or `javascript:` URLs.
- [ ] No `<style>` blocks or `style=""` attributes in administration templates.
- [ ] Code that reads the client address or scheme uses `eZSys` (which respects `TrustedProxies[]`), not raw headers.
- [ ] Responses for signed-in users are not made cacheable by proxies.

Source: [HTTP security: headers, CSP, request rules and proxies](http-security-csp-and-proxies.md).

## Runtime and dependencies

- [ ] No per-request secret or user data left in statics or globals of a long-lived worker
      ([Concurrency, statics and long-lived processes](concurrency-statics-and-long-lived-processes.md)).
- [ ] A new Composer dependency is maintained, licensed compatibly with the GPL, and pinned to a released version
      ([Dependency management with Composer](dependency-management-with-composer.md)).
- [ ] A changed default that affects security is documented with how to undo it deliberately
      ([Backward compatibility policy](backward-compatibility-policy.md)).
- [ ] A vulnerability found during review is reported privately, as `SECURITY.md` says, not in the pull request.

## Related pages

- [Security engineering overview](security-engineering-overview.md)
- [Security review checklist for hosts](../hosting/checklists/security-review.md)
- [Reviewing other people's work](../extension-development/part-5-extend-fork-contribute/reviewing-other-peoples-work.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
