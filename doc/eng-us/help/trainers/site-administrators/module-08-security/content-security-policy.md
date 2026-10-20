---
title: "Lesson 8.5: Content Security Policy"
audience: trainer, admin
category: training
order: 85
summary: Learners explain what a Content Security Policy protects against, read the policy header of the administration and a site, collect violation reports in csp.log, and move one siteaccess from report-only to enforce and back on a training installation. 40 minutes.
keywords: site administration, Content Security Policy, CSP, report-only, enforce, Mode=auto, Directives, csp.log, csp/report, ReportEndpoint, browser developer console, XSS
type: help
---
# Lesson 8.5: Content Security Policy

A Content Security Policy tells the browser which scripts, stylesheets, images and connections a page may use. With
a strict policy, a script injected into a page through a stored or reflected value does not run, even where a template
forgets to escape it. Exponential sends a report-only policy on the administration by default and none on the sites.
This lesson shows administrators how to read the policy, collect what it would block and enforce it safely.

> **Note**
> An enforced policy can break pages that use inline code. Enforce only on your training installation, one siteaccess
> at a time, and know the way back before you start.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 8: Security](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Intermediate to advanced |
| Audience | Site administrators; developers welcome |
| Prerequisites | [Lesson 8.1](thinking-like-an-attacker.md); [Lesson 7.2](../module-07-logs-and-the-audit-trail/the-log-viewer.md); a browser with developer tools; a shell |

## Learning objectives

After this lesson, learners can:

- explain the difference between `report-only` and `enforce`, and what `Mode=auto` does;
- read the policy header a page sends, in the browser or with `curl`;
- point the reports at the kernel's own collector and read them in **Setup > Log files**;
- enforce the policy on one siteaccess and roll it back;
- loosen one directive per siteaccess when a real need is found.

## Trainer's notes

### How to present it

Source: [Content Security Policy](../../../../features/6.0/content-security-policy.md), sections "Settings",
"Turning it on" and "Reports".

1. **What it is for** (3 minutes). A policy such as `script-src 'self'` without `'unsafe-inline'` means that an
   injected `<script>` does not run. It is a second line of defense behind escaping in templates.
2. **The modes** (3 minutes). `disabled`; `report-only` (the browser reports what it would block and blocks nothing);
   `enforce` (the browser blocks); `auto`, the default: report-only on administration siteaccesses, none on the
   others.
3. **The directives** (2 minutes). `site.ini [ContentSecurityPolicy] Directives[]`, one directive per entry, all
   `'self'` by default with `object-src 'none'`. `AdditionalSources[]` adds a host to a directive without restating
   the rest.
4. **Reports** (3 minutes). With `ReportEndpoint=enabled` (or `ReportURI=/csp/report`), the browser posts reports to
   the kernel's `csp/report` view, which writes one masked line per violation to `var/log/csp.log`. The log viewer
   lists it as **Content Security Policy reports**; the **Error digest** groups it by directive.
5. **The safe path to enforce** (4 minutes). Work in report-only, collect reports from real use, enforce one
   siteaccess first, loosen only what you must, clear the INI cache and reload PHP. To go back, set
   `Mode=report-only` and clear the INI cache again.

### Common questions

**Which pages break when enforcing?** Templates with inline code, usually from extensions or own designs. The
feature page describes a test that lists them by extension.

**Can I allow a map service?** Yes: `AdditionalSources[]=script-src https://maps.example.com` (with the real host),
per siteaccess, instead of `'unsafe-inline'` for everything.

**Does the classic admin design work with an enforced policy?** Designs listed in `ReportOnlyDesigns[]` (by default
`admin`, `admin2` and `admin_classic`) keep a report-only policy even when the siteaccess enforces.

### Pitfalls

- **Enforcing everything at once.** One broken administration locks out everyone who needs to fix it. One
  siteaccess, ideally a second administration siteaccess only you use.
- **Forgetting the INI cache and the workers.** The header does not change until every PHP process reads the new
  setting.
- **Expecting reports without opening the pages.** Handler attributes are reported only when they run: open menus and
  dialogs too.

## Hands-on exercise

Use your own training installation, served for example on `http://127.0.0.1:8087`; replace the address and the
siteaccess names with yours.

1. Read the policy settings in effect:

   ```bash
   php bin/php/console exp:ini get site.ini/ContentSecurityPolicy/Mode
   ```

2. Read the header of an administration page and of a public page:

   ```bash
   curl -s -D - -o /dev/null http://127.0.0.1:8087/<admin siteaccess>/user/login | grep -i content-security
   curl -s -D - -o /dev/null http://127.0.0.1:8087/ | grep -i content-security
   ```

3. Open the administration in the browser with the developer console open. Look for messages marked *Report Only*
   while you open the dashboard, a content page and a dialog.
4. Point the reports at the kernel's collector for the administration siteaccess. In
   `settings/siteaccess/<admin siteaccess>/site.ini.append.php` add (keep a copy of the file first):

   ```ini
   [ContentSecurityPolicy]
   ReportEndpoint=enabled
   ```

5. Clear the INI cache (`php bin/php/ezcache.php --clear-tag=ini`); on Velocity, deploy as your trainer shows.
6. Use the administration for five minutes: content pages, the online editor, menus and dialogs.
7. Open **Setup > Log files**, then **Content Security Policy reports** in the navigator. Read one line: the
   directive, what was blocked and `disposition=report`.
8. Open **Error digest** and find the group of `csp.log`.
9. Enforce on the administration siteaccess: add `Mode=enforce` to the same block, clear the INI cache and repeat
   step 2. Open a few pages and note anything that no longer works.
10. Roll back: set `Mode=report-only` (or remove the line), clear the INI cache, and check the header again.

## Expected result

- Step 2: the administration page sends `Content-Security-Policy-Report-Only`; the public page sends no policy with
  `Mode=auto`.
- Step 7 shows lines starting with `csp/<directive>` and `disposition=report`, if anything would have been blocked;
  no line at all is a good result too.
- Step 9: the header is now `Content-Security-Policy`; reports show `disposition=enforce`.
- Step 10: the header is report-only again.
- The trainer checks that every learner ended with the rollback done.

## Quiz

1. What does `Mode=auto` send on a public siteaccess?
   A. An enforced policy B. A report-only policy C. No policy D. The policy of the administration
2. Where does the kernel write the violation reports it collects?
3. What is the first thing to do before enforcing a policy?
4. How do you roll back an enforced policy?
5. A page needs scripts from one external host. What is better than adding `'unsafe-inline'`?

<details>
<summary>Answers</summary>

1. **C.** `auto` is report-only on administration siteaccesses and disabled on all others
   ([Content Security Policy, Settings](../../../../features/6.0/content-security-policy.md#settings)).
2. `var/log/csp.log`, listed in the log viewer as **Content Security Policy reports**.
3. Work in report-only and collect the reports of real use.
4. Set `Mode=report-only` (or `disabled`) and clear the INI cache again; reload PHP or deploy Velocity.
5. Add the host to that directive with `AdditionalSources[]`, for that siteaccess only.

</details>

## Further reading

- [Content Security Policy](../../../../features/6.0/content-security-policy.md): every setting, the reports and
  enforcing with a nonce.
- [Chapter 13, 13.9 Security headers and HTTPS](../../../../install/13-security-hardening.md#139-security-headers-and-https).
- [Content Security Policy for hosts](../../../hosting/security/content-security-policy.md).

## Related pages

- [Module 8: Security](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
