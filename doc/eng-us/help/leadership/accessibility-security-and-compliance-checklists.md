---
title: Accessibility, security and compliance checklists
audience: project-manager, admin
category: leadership
order: 80
summary: Checklists for accessibility, the Content Security Policy, hardening and go-live, accounts, the audit trail and vulnerability reporting, with data protection as questions. Not legal advice.
keywords: accessibility, a11y, WCAG, right to left, security, hardening, Content Security Policy, CSP, audit trail, compliance, go-live, data protection, privacy, GDPR, consent, vulnerability, SECURITY.md, checklist, project manager, administrator
type: help
---
# Accessibility, security and compliance checklists

This page is for project managers who must show that a site is accessible, secure and handled in line with the rules
that apply to their organization, and for the administrators who do the work. It answers: what Exponential already
does in each area (with its source), what your project still has to do, and which questions you must answer
yourselves. Use the checklists in reviews and in acceptance.

> **Warning**
> This page is not legal advice. It names what the product does and which questions to ask. Which laws and standards
> apply to your organization, and whether a site meets them, is for your organization and its advisers to decide.

## Contents

1. [How to use these checklists](#how-to-use-these-checklists)
2. [Accessibility](#accessibility)
3. [Content Security Policy](#content-security-policy)
4. [Security hardening and go-live](#security-hardening-and-go-live)
5. [Accounts and access](#accounts-and-access)
6. [The audit trail](#the-audit-trail)
7. [Reporting vulnerabilities](#reporting-vulnerabilities)
8. [Data protection and privacy: questions to answer](#data-protection-and-privacy-questions-to-answer)
9. [Keeping it that way](#keeping-it-that-way)
10. [A review record to copy](#a-review-record-to-copy)

## How to use these checklists

- **Give every line an owner and a date.** A checklist without names is a wish list.
- **Ask for evidence**: a command output, a screenshot, a test report. Most lines name the command or page that
  shows the evidence.
- **Run them three times**: when planning (to scope the work), before launch (to accept), and on a schedule after
  launch (to keep it).
- **Record exceptions.** A line that is not met is accepted by someone with a reason, or it is a risk
  ([Risk management](risk-management.md)).

## Accessibility

### What the product does

| Area | What the sources say | Source |
|---|---|---|
| Administration (admin4 design) | WCAG rules checked by tests: contrast of the color tokens in light and dark mode (text 4.5:1), target sizes of at least 24 by 24 pixels (WCAG 2.5.8), every form field has a name, landmarks and names for screen readers | [Styling the administration](../../bc/6.0/admin4-styling.md) |
| Keyboard and screen readers | A skip link as the first Tab stop, landmarks for header, menus, breadcrumb and main content, the current page marked with `aria-current`, menus with roles and keyboard handling | [Admin 4 design](../../features/6.0/admin4-design.md) |
| Modes the browser asks for | Light and dark themes, Windows high contrast (forced colors), reduced motion | [Styling the administration](../../bc/6.0/admin4-styling.md) |
| Keyboard shortcuts | The administration and the help can be used with the keyboard alone | [Keyboard shortcuts](../keyboard-shortcuts.md) |
| Right-to-left languages | `dir="rtl"` in the administration and in content written in a right-to-left language | [Right to left](../../features/6.0/right-to-left.md) |
| Alternative text | Image fields can require an alternative text, per class; off until switched on | [Content model, section 9](../../guides/content-model-and-editing.md) |
| Missing alternative text | Exponential Intelligence has a "Missing alt text" report that queues suggestions for a subtree | [expai](../../features/6.0/extensions/expai.md) |
| The help | Instant search results are announced to screen readers | [The help system](../../features/6.0/help-system.md) |

> **Note**
> The sources describe tests for the **administration**. They do not state that any public site design meets a
> particular WCAG level. The accessibility of your public sites depends on your designs, templates and content, and
> is your project's work.

### Checklist: administration

- [ ] Editors use the admin4 design (or another administration design whose accessibility you have checked).
- [ ] The interface language of each editor is available at the level they need
      ([Languages, support levels](../../languages.md#2-support-levels)).
- [ ] Extensions you add to the administration follow the styling rules (no inline styles, shared components,
      tokens with dark values): `CONTRIBUTING.md`, [Styling the administration](../../bc/6.0/admin4-styling.md).
- [ ] Pages of your own extensions were checked in light and dark mode, at normal size and at 200% zoom, as the
      styling rules ask ([Styling the administration](../../bc/6.0/admin4-styling.md)).

### Checklist: public sites

- [ ] The accessibility standard and level that apply are named in the charter. (Which one applies is a question for
      your organization; the sources do not state one for public sites.)
- [ ] Alternative text is required on image fields of the classes visitors see.
- [ ] The "Missing alt text" report (if Exponential Intelligence is used) or a manual review finds no gaps.
- [ ] Templates set the page language and text direction for each siteaccess, including right-to-left ones.
- [ ] Color contrast, keyboard use, focus visibility, headings and form labels of the designs are tested with
      automated tools and by people, including screen reader users.
- [ ] Rich text produced by editors keeps a sensible heading order; editors know how
      ([Creating and editing content](../editing-content.md)).
- [ ] PDFs and other downloads offered to visitors are checked too.

## Content Security Policy

A Content Security Policy "tells the browser which scripts, stylesheets, images and connections a page may use"; with
a strict policy, an injected script does not run "even where a template forgets to escape it"
([Content Security Policy](../../features/6.0/content-security-policy.md)).

**The default**: `Mode=auto` sends a report-only policy on administration siteaccesses and none on the sites. Enforcing
is a decision per siteaccess, made after an audit.

### Checklist

- [ ] Decided: enforce on the administration? on which sites? by when?
- [ ] Templates audited: `AdminContentSecurityPolicyTest` run with every extension of the installation
      (`CSP_ALL_EXTENSIONS=1`), every failure assigned to an owner.
- [ ] Reports of real use collected for a few days (`ReportURI`, or the built-in `csp/report` view and the **CSP
      reports** page), every page of every extension opened, menus and dialogs included.
- [ ] Extension menu items with inline scripts converted, or `'unsafe-inline'` kept for that siteaccess on purpose
      (xrowextract, ezodf and ezmultiupload are named in the source as examples).
- [ ] Enforced first on a second administration siteaccess used by few people.
- [ ] Each loosened directive written down with its reason.
- [ ] Sites started in `report-only` before `enforce`; rich text and embedded players checked.
- [ ] Rollback known: `Mode=report-only` and an INI cache clear.

All steps: [Switching to enforce, step by step](../../features/6.0/content-security-policy.md#switching-to-enforce-step-by-step).

## Security hardening and go-live

The project follows "secure by default in the OpenBSD sense": a fresh installation is safe without extra work, every
part has only the rights it needs, and every change is audited; "your part is to keep them in force"
([Security hardening](../security-hardening.md)).

### What is on by default

From [Security hardening](../security-hardening.md) and [Security and audit](../../guides/security-and-audit.md):
strict rewrite rules, five security headers, `HttpOnly` and `SameSite` cookies, debug output off, form tokens against
cross-site request forgery, bcrypt password hashes, generated administrator passwords, the setup wizard closed on an
installed site, uploaded images accepted by their content and not by their name.

### Checklist for the project manager

Your administrator works through two detailed lists:
[the help's hardening checklist](../security-hardening.md#checklist) and
[13.16 Go-live checklist](../../install/13-security-hardening.md#1316-go-live-checklist). Your job is to see that
each line has an owner, a date and evidence. The lines most often forgotten:

- [ ] The generated administrator password changed and `var/log/initial-admin-password` removed.
- [ ] A personal administrator account per person; `admin` retired as a sign-in; a tested break-glass procedure.
- [ ] Two-step sign-in required for administrators ([Two-step sign-in](../../guides/two-factor-login.md)).
- [ ] `CheckValidity=false`; the administration on its own host, restricted by network or a second authentication.
- [ ] Debug output off; PHP `display_errors=Off`.
- [ ] HTTPS everywhere, HSTS when stable, `CookieSecure=true` on HTTPS-only sites.
- [ ] Security headers present: the `curl` check of [Security and audit, section 1](../../guides/security-and-audit.md).
- [ ] `settings/override/` out of version control, backed up as a secret.
- [ ] Code read-only for the site's user; workers and cronjobs never as root.
- [ ] Behind a proxy: `TrustedProxies[]` lists exactly your proxies.
- [ ] Backups off the machine, restore tested.
- [ ] An update routine: who watches releases, how updates are tested, who receives security mail.

A shield in front of the site, request-shield (a project of CJW Network), is documented as an option, first in
monitor mode, then enforced ([Security hardening](../security-hardening.md#a-shield-in-front-request-shield)).

## Accounts and access

- [ ] Roles hold only what people need; few administrators
      ([Roles and policies](../../guides/roles-and-policies.md)).
- [ ] `PreventPrivilegeEscalation` left `enabled` (the default), so nobody grants more than they hold.
- [ ] Registration open or closed on purpose: on new installations registering needs the `user/register` policy
      ([Roles and policies, section 8](../../guides/roles-and-policies.md)).
- [ ] Accounts reviewed regularly in **Users > Check user accounts**: never signed in, not seen for long, disabled,
      locked, orphaned, shared e-mail ([Check user accounts](../../features/6.0/user-hygiene.md)).
- [ ] `exp:user-login-check` reports every login belongs to one account
      ([Security and audit, section 7](../../guides/security-and-audit.md)).
- [ ] Sessions: logging out ends the session for good ([Security and audit](../../guides/security-and-audit.md),
      [Sessions](../../guides/sessions.md)).

## The audit trail

Exponential 6.0.15 records security-relevant events in a tamper-evident log, on by default: sign-ins and refusals,
content changes, settings writes, cache clears, deploys, orders and VAT changes. Every record carries the hash of the
one before it; `verify` tells you whether anything was edited or cut
([Audit trail](../../features/6.0/audit-trail.md)).

### Checklist

- [ ] The `frequent` cronjob group runs; it indexes, delivers sinks, evaluates alert rules and rotates and archives.
- [ ] Alert recipients checked (`./console exp:audit alerts recipients`); a person, not an unread mailbox.
- [ ] `settings/override/audit.ini.append.php` backed up: it holds the signing and pseudonym keys.
- [ ] The trail forwarded off the machine with a sink (syslog or a signed webhook), so a compromised machine cannot
      quietly rewrite its own history ([Security hardening](../security-hardening.md)).
- [ ] `exp:audit verify` run regularly; result recorded.
- [ ] Retention decided. Shipped: 90 live days and 730 archive days for content, access, system and commerce; reads
      are recorded only when switched on, 30 and 90 days.
- [ ] `OnWriteFailure` decided: `continue` (default, the site keeps working) or `refuse` (security-relevant actions
      are refused while the log cannot be written).
- [ ] `audit/read` and `audit/manage` given only to the people who need them (only Administrator has them in a new
      installation).

## Reporting vulnerabilities

- **Private reporting.** `SECURITY.md` asks for security issues to be reported privately by e-mail to the address it
  names, "not in the public issue tracker or the discussions", and promises "a first answer within one business day".
  A private advisory on GitHub is the other documented way ([Security hardening](../security-hardening.md#reporting-a-vulnerability)).
- **What to include**: the version, the PHP version, the engine that serves the site and the database, what an
  attacker can do and the steps to reproduce; leave out passwords, secrets and personal data (`SECURITY.md`).
- **How fixes arrive**: an accepted issue is fixed on `main` and released with the next 6.0.x version; a published tag
  is never changed (`SECURITY.md`).

Checklist:

- [ ] Your team knows where `SECURITY.md` is and who in your organization reports.
- [ ] Your own extensions have an owner who receives and handles reports about them.
- [ ] Someone follows the releases and the changelogs ([Release and upgrade planning](release-and-upgrade-planning.md)).

## Data protection and privacy: questions to answer

The sources describe privacy features of the product; whether they satisfy the rules that apply to you (for example
the GDPR in the European Union) is for your organization to decide. Use these as questions in a review with your
data protection officer.

### What the product does

| Area | What the sources say | Source |
|---|---|---|
| Audit trail | Addresses truncated, sessions hashed, user agents shortened, query values removed; passwords, tokens, repair keys and secrets never recorded | [Audit trail](../../features/6.0/audit-trail.md) |
| Log viewer | E-mail addresses, IP addresses, passwords, tokens, keys, session ids and cookies masked; unmasking needs `setup/unmasklogs` | [Log viewer](../../features/6.0/log-viewer.md#privacy-what-is-masked) |
| Request logs | Masked client addresses, a hash and a short part of the user agent, query values replaced | [Request logs](../../features/6.0/request-logs.md) |
| Mail | Essential mail always goes out; optional mail only to people who switched it on, with one-click unsubscribe; a consent log; a suppression list stored as salted hashes; export and erase per address | [13.13 Mail: consent and suppression](../../install/13-security-hardening.md#1313-mail-consent-and-suppression), [Mail preferences](../../guides/mail-preferences-administrator.md) |
| Writing assistant | A region (`us`, `eu`, `local`) and retention per provider; rules per section and class; a printable "EI: Summary" page for a data protection officer; prompts, answers and keys never recorded in the audit | [expai](../../features/6.0/extensions/expai.md) |
| Collected information | Form exports contain what visitors typed, often names and e-mail addresses | [Collected information](../../guides/collected-information.md) |
| Search statistics | Phrases visitors typed can contain personal data | [URLs and aliases, section 5](../../guides/urls-and-aliases.md) |

### Questions

- [ ] Which personal data does the site collect (accounts, forms, orders, newsletters, comments, search phrases)?
      Where is each stored?
- [ ] Who may read it in the administration? Is each such policy given only to those who need it?
- [ ] How long is each kept? (Audit retention, consent log retention, log rotation, form exports, backups.)
- [ ] How does the organization answer a request to see, export or erase a person's data? Which commands or pages
      are used (for mail: `exp:mail:preferences export` and `erase`)?
- [ ] Does the footer of optional mail name the sender and its postal address (`OrganisationName`,
      `OrganisationAddress`)? The source notes that "many jurisdictions require" an address.
- [ ] If Exponential Intelligence is switched on: which providers, which region, which retention, which sections and
      classes may be sent? Has the data protection officer read the EI: Summary page? It "sends nothing anywhere until
      you add a provider key" ([Who is the Exponential Foundation?](../who-is-the-exponential-foundation.md#exponential-intelligence)).
- [ ] Which outside services do templates load (fonts, maps, analytics, videos)? Does the Content Security Policy
      list them, and does the privacy notice?
- [ ] Are backups and exports stored where personal data belongs, and deleted when no longer needed?
- [ ] Who in the project is responsible for the privacy notice and cookie information on the sites?

## Keeping it that way

| How often | What | Source |
|---|---|---|
| Weekly | Read the log viewer's error digest and `security.log`; a sudden rise of one rule is worth a look | [Security hardening](../security-hardening.md) |
| Weekly | The weekly checklist of operating a site | [Operating a site](../../guides/operating-a-site.md) |
| Regularly | `exp:audit verify`; account review in Check user accounts | [Audit trail](../../features/6.0/audit-trail.md), [Check user accounts](../../features/6.0/user-hygiene.md) |
| At every update | File consistency check in **Setup > Upgrade check**; changelog and upgrade notes read | [The upgrade check](../../guides/upgrade-check.md), [13.15](../../install/13-security-hardening.md#1315-keeping-up-to-date) |
| At every change of a template or extension | Content Security Policy reports and accessibility checks of the changed pages | this page |

## A review record to copy

```text
Compliance review of <project name>        date: <date>        phase: <planning | pre-launch | periodic>
Reviewers: <names and roles>

| Area             | Checklist used                 | Lines met | Lines open | Exceptions accepted (by whom, why) | Evidence (link or file) |
|------------------|--------------------------------|-----------|------------|------------------------------------|-------------------------|
| Accessibility    | this page, public sites        | <n>       | <n>        | <...>                              | <...>                   |
| CSP              | this page                      | <n>       | <n>        | <...>                              | <...>                   |
| Hardening        | help checklist + 13.16         | <n>       | <n>        | <...>                              | <...>                   |
| Accounts         | this page                      | <n>       | <n>        | <...>                              | <...>                   |
| Audit trail      | this page                      | <n>       | <n>        | <...>                              | <...>                   |
| Data protection  | questions on this page         | <n>       | <n>        | <...>                              | <...>                   |

Next review: <date>        Open items moved to the risk register: <ids>
```

## Related pages

- [Security hardening](../security-hardening.md)
- [Chapter 13: Security hardening for production](../../install/13-security-hardening.md)
- [Security and audit](../../guides/security-and-audit.md)
- [Content Security Policy](../../features/6.0/content-security-policy.md)
- [The audit trail](../../features/6.0/audit-trail.md)
- [Quality gates and acceptance](quality-gates-and-acceptance.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
