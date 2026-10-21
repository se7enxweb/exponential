---
title: Spam and security basics
audience: individual, customer, website-creator
category: website-creation
order: 1645
summary: The security and anti-spam basics one person can keep up on an Exponential site - the administrator account, passwords and two-step sign-in, form tokens and CAPTCHA for forms, comments and registration, what anonymous visitors may create, updates, logs, and what to do when something looks wrong.
keywords: security, spam, bots, CAPTCHA, reCAPTCHA, hCaptcha, ezformtoken, form token, registration spam, comment spam, passwords, two-factor, administrator, permissions, updates, logs, hardening, individual
type: help
---
# Spam and security basics

This page is for individuals who run a site alone and want the basics right without becoming security experts.
Most attacks on small sites are automated: bots that try common passwords, fill in forms and register fake accounts.
The defenses below stop most of them.

## Contents

1. [Your administrator account](#your-administrator-account)
2. [What visitors may create](#what-visitors-may-create)
3. [Forms, comments and registration](#forms-comments-and-registration)
4. [Updates](#updates)
5. [Watching the site](#watching-the-site)
6. [Hardening, briefly](#hardening-briefly)
7. [When something looks wrong](#when-something-looks-wrong)
8. [Checklist](#checklist)

## Your administrator account

- **Change the default administrator password** at once after installing
  ([Change the admin password](../../change-the-admin-password.md),
  [Default administrator password](../../default-administrator-password.md)).
- **A long, unique password** from a password manager.
- **Two-step sign-in** with an app ([Two-factor sign-in](two-factor-sign-in.md)).
- **A separate editor account** for daily writing, so the administrator account is used only for administration.
- **HTTPS** on every page ([Domain names and HTTPS](../setup/domain-names-and-https.md)).

## What visitors may create

Every form a visitor can submit is a way in for spam. Check **Users > Roles** > *Anonymous*: on the default site
package anonymous visitors may create *Comment* and *Review* objects in the Standard section
(`sevenx-multi-site-installer.php`). Remove what you do not use ([Visitor permissions](../../creators/website-creators/visitor-permissions.md),
[Comments on your blog](../blogs/comments-on-your-blog.md)).

## Forms, comments and registration

| Layer | Ships as | What it stops |
|---|---|---|
| Form tokens | `ezformtoken`, in the default installation | Forged posts sent from other sites |
| CAPTCHA field | `recaptcha` or `hcaptcha` datatype on the class; keys from the service | Most automated form spam |
| Signed-in only | Roles | Anonymous bots |
| Approval workflow | Workflows | Anything appearing before you look |
| Activation by mail | `VerifyUserType=email` | Fake registrations with made-up addresses |

Use CAPTCHA on public forms, comments and registration
([Spam protection for forms](../../creators/website-creators/spam-protection-for-forms.md),
[hcaptcha](../../../features/6.0/extensions/hcaptcha.md), [recaptcha](../../../features/6.0/extensions/recaptcha.md)).
A CAPTCHA sends visitors' browsers to the service; mention it in your privacy text.

## Updates

Most break-ins use holes that were already fixed. Apply security releases within days
([Updates for one person](updates-for-one-person.md)), and keep PHP and, on your own server, the operating system
current.

## Watching the site

| Look at | How often | Where |
|---|---|---|
| New user accounts | Weekly | **Users**; remove ones you do not recognize |
| New comments and form submissions | Weekly | The posts; collected information |
| Errors | Monthly, or when something is odd | The log viewers ([Logs for owners](../../customers/healthy/logs-for-owners.md)) |
| Who signed in | When in doubt | The audit trail ([Security and audit](../../../guides/security-and-audit.md)) |

## Hardening, briefly

The installation book has a chapter on security hardening
([Security hardening](../../security-hardening.md), [Chapter 13](../../../install/13-security-hardening.md)). For one
person, the essentials are: the administration on its own address, file permissions as the book describes, settings
files not readable from the web, and debug output off on the live site.

## When something looks wrong

1. Switch on maintenance mode if content was changed by someone else
   ([Maintenance mode](../../creators/website-creators/maintenance-mode.md)).
2. Change your password and check the administrator accounts.
3. Look at the audit trail and logs for what happened and when.
4. Restore from a backup taken before the problem if needed ([Backups for one person](backups-for-one-person.md)).
5. If you found a security problem in Exponential itself, report it privately, not in a public issue
   ([Private security reports](../../customers/healthy/private-security-reports.md)).

## Checklist

- [ ] Default password changed; two-step sign-in on.
- [ ] *Anonymous* role only creates what you want.
- [ ] Form tokens on; CAPTCHA on public forms, comments and registration.
- [ ] Security updates applied promptly.
- [ ] Weekly look at new accounts and comments.
- [ ] Debug output off on the live site.

## Related pages

- [Running it alone](README.md)
- [Security basics (customers)](../../customers/healthy/security-basics.md)
- [Security checklist for owners](../../customers/healthy/security-checklist-for-owners.md)
- [Security basics for businesses](../../businesses/security/security-basics-for-businesses.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
