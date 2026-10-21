---
title: Security checklist
audience: cms-developer
category: cms-development
order: 43
summary: For CMS developers, the security work of a site project as checklists. It covers the content model and roles, templates and output, forms and uploads, your own code, APIs, configuration and deployment, and the go-live hardening. Each item links to the page that explains it.
keywords: security, checklist, hardening, roles, least privilege, wash, escaping, XSS, CSRF, ezformtoken, form token, content security policy, CSP, SQL injection, eZDB escapeString, uploads, API keys, RequireHTTPS, debug output, two-factor, audit trail, request rules
type: help
---
# Security checklist

This page is for developers who build sites and must hand them over secure. Exponential is secure by default in many
places, and a project can still undo that with one careless role, template or setting. Go through these lists during
development and again before launch; the hardening guide explains the attacks behind each item
([Security hardening](../security-hardening.md)).

## Contents

1. [Content model and access](#content-model-and-access)
2. [Templates and output](#templates-and-output)
3. [Forms and uploads](#forms-and-uploads)
4. [Your own code](#your-own-code)
5. [APIs](#apis)
6. [Configuration and deployment](#configuration-and-deployment)
7. [Accounts](#accounts)
8. [Go-live hardening](#go-live-hardening)
9. [Watching and reporting](#watching-and-reporting)

## Content model and access

- [ ] The Anonymous role reads only what visitors may read (`content/read` on the public sections), and nothing else
      beyond `user/login` and what the site's forms need.
- [ ] Members-only content is in its own section, read by a role assigned to the members' group, not just hidden.
- [ ] Editor roles are limited by class and subtree; no editor role has `setup/*`, `role/*` or `user/*` beyond need.
- [ ] Roles are assigned to groups; members and staff are in different groups.
- [ ] Access was tested with test accounts at `example.invalid`, and `./console exp:accesscheck` explains every
      surprise ([Sections, roles and policies](sections-roles-and-policies.md)).

## Templates and output

- [ ] Every value you print yourself goes through `|wash()`; datatype output comes from `attribute_view_gui`
      ([Wash and escaping](../designers/templates/wash-and-escaping.md)).
- [ ] No `<style>` blocks, `style=""` attributes, inline scripts or `javascript:` links in templates, so a Content
      Security Policy can be enforced ([CSP-safe design](../designers/theme-design/csp-safe-design.md),
      [Content Security Policy](../../features/6.0/content-security-policy.md)).
- [ ] Editors cannot store markup: rich text has no tag for scripts; custom tags instead of pasted HTML
      ([Rich text](rich-text.md)).
- [ ] No visitor-specific data in shared caches ([Caching for CMS developers](caching-for-cms-developers.md#visitor-specific-output)).

## Forms and uploads

- [ ] `ezformtoken` is active and every form keeps the hidden fields the original template prints.
- [ ] Public forms have a captcha datatype; collected answers are removed on a schedule
      ([Forms and information collection](forms-and-information-collection.md)).
- [ ] Upload types are limited to what editors need; file attributes have a maximum size.
- [ ] Self-registration is on only where needed, with e-mail verification ([Users and registration](users-and-registration.md)).

## Your own code

- [ ] Every module view declares a policy function and checks it; writes need POST and the form token.
- [ ] SQL goes through `eZPersistentObject` or `eZDB` with values escaped (`escapeString()`) and IDs cast to integers
      ([eZDB](../extension-development/part-8-reference/ezdb.md)).
- [ ] Redirects go only to addresses of the site ([Safe redirects](../../features/6.0/safe-redirects.md)).
- [ ] Content is created through the kernel API, as a dedicated user with exactly the rights needed
      ([Content from PHP](content-from-php.md)).
- [ ] The book's rules are met: [Security in extensions](../extension-development/part-3-extension-developer/security-in-extensions.md).

## APIs

- [ ] `rest.ini [Authentication] RequireAuthentication=enabled` (shipped) and `RequireHTTPS=enabled`.
- [ ] One account per client, with a role limited to what it needs; API keys with scopes and an expiry
      ([The REST API](the-rest-api.md), [Personal API keys](../../guides/api-keys.md)).
- [ ] Keys and tokens live in environment variables or secret stores, never in repositories.

## Configuration and deployment

- [ ] Debug output off on staging and live, or limited to your address; PHP `display_errors=Off`.
- [ ] `settings/override/` is not in any repository and not copied between machines.
- [ ] Code is read-only for the site's user; workers and cronjobs never run as root.
- [ ] `ManagedByDeployment=enabled` where configuration comes from version control ([Deploying a site](deploying-a-site.md)).
- [ ] The rewrite rules of the web server keep `settings/`, `var/` internals and source files from being served
      ([Security hardening, chapter 13](../../install/13-security-hardening.md)).

## Accounts

- [ ] The installer's administrator password changed and `var/log/initial-admin-password` removed
      ([Change the admin password](../change-the-admin-password.md)).
- [ ] A personal administrator account per person; two-factor sign-in for administrators
      ([Two-factor sign-in](../../guides/two-factor-login.md)).
- [ ] Password rules decided (`MinPasswordLength`, `WeakPasswordCheck`, `[PasswordSettings]`).
- [ ] Demo mode off (`EXP_DEMO_MODE` not set) ([Demo mode](../demo-mode.md)).

## Go-live hardening

- [ ] HTTPS everywhere, HSTS, secure cookies.
- [ ] The administration on its own host or restricted by network.
- [ ] Request rules or the request shield in front, first monitoring, then enforcing
      ([Request rules](../../features/6.0/request-rules.md)).
- [ ] The setting-by-setting list: [Go-live checklist](../../install/13-security-hardening.md#1316-go-live-checklist).

## Watching and reporting

- [ ] Audit alerts go to a person and the trail leaves the machine ([Security and audit](../../guides/security-and-audit.md)).
- [ ] Logs are read weekly; backups are off the machine and a restore was tested.
- [ ] Updates of Exponential and its extensions are followed and tested.
- [ ] Your team knows how to report a vulnerability ([Reporting a vulnerability](../security-hardening.md#reporting-a-vulnerability)).

## Related pages

- [Security standards](../../standards/security.md)
- [Security review checklist for hosts](../hosting/checklists/security-review.md)
- [Project launch checklist](project-launch-checklist.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
