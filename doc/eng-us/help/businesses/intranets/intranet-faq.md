---
title: Intranet FAQ
audience: businesses, project-manager, admin
category: business-solutions
order: 110
summary: Short, sourced answers to the questions managers ask about running an intranet on Exponential - privacy of pages, departments, directory sign-in, two-step sign-in, documents, search, approval, notifications, sharing an installation with the public site, and records.
keywords: intranet FAQ, questions, private pages, departments, LDAP, Active Directory, SAML, two-factor, documents, search, approval, notifications, multisite, audit, mobile
type: help
---
# Intranet FAQ

This page is for managers and project leads who need quick answers. Each answer is short and links to the page with
the details. If your question is not here, the [Businesses FAQ](../businesses-faq.md) covers general questions.

## Contents

1. [Access and privacy](#access-and-privacy)
2. [Sign-in](#sign-in)
3. [Content and documents](#content-and-documents)
4. [Publishing and communication](#publishing-and-communication)
5. [Operations and records](#operations-and-records)
6. [Related pages](#related-pages)

## Access and privacy

**Can the whole intranet require a sign-in?**
Yes. A siteaccess with `site.ini [SiteAccessSettings] RequireUserLogin=true` sends every visitor who is not signed in
to the login page; only the views in `AnonymousAccessList[]` stay open
([What visitors may see](../../creators/website-creators/visitor-permissions.md#siteaccess-settings)).

**Can departments have pages that other departments cannot see?**
Yes. Put each department's content in its own section and give `content/read` for that section only to the groups
that may read it ([Departments, roles and sections](departments-roles-and-sections.md)).

**Can each department edit only its own pages?**
Yes. Assign one Editor role to each department's group with a Section limitation
([Sections, 13.2](../../../guides/sections.md#132-an-intranet-with-departments)).

**Can the intranet and our public website run in one installation?**
Yes, as separate siteaccesses that share content, users and operations. Keep intranet content in sections that the
public Anonymous role cannot read, and test logged out
([Setting up several sites in one installation](../../creators/website-creators/multisite-setup.md)).

**Can we allow access only from the office?**
Network limits are set in the web server or a VPN in front of the site, not in Exponential
([Restricting the administration, Network restrictions](../../hosting/security/restricting-the-administration.md#network-restrictions)).

## Sign-in

**Can staff sign in with their company directory account?**
Yes, through LDAP. The kernel ships an LDAP login handler configured in `settings/ldap.ini`; it is off until added to
`LoginHandler[]` ([Directory sign-in and LDAP](directory-sign-in-and-ldap.md)).

**Is there SAML or OpenID Connect single sign-on?**
Not in the repository. There is an extension point for single sign-on handlers (`SingleSignOnHandlerArray[]`) and for
custom login handlers; a handler for your identity provider would be an extension project
([What is not built in](directory-sign-in-and-ldap.md#what-is-not-built-in)).

**Is two-step sign-in available?**
Yes, through the `sevenx_authentication_2fa` extension: authenticator app codes or e-mail codes, opt-in or required
for everyone ([Two-factor and sign-in](two-factor-and-sign-in.md#two-step-sign-in)).

**What happens after several wrong passwords?**
Nothing by default: `MaxNumberOfFailedLogin=0` means no lock. Set a value for the intranet
([Two-factor and sign-in](two-factor-and-sign-in.md#locking-an-account-after-failed-sign-ins)).

## Content and documents

**Can a document link be forwarded to someone who should not see it?**
They still cannot download it. Every download through `content/download` checks that the person may read the object
([Documents and file libraries](documents-and-file-libraries.md#downloads-respect-permissions)).

**Do we keep old versions of a policy?**
Yes. Every save is a version; earlier published versions are archived and can be compared and restored
([Versions of an object](../../../guides/content-history.md)).

**Will search show staff things they may not read?**
No. Search results follow permissions ([Intranet search](intranet-search.md)).

**Can search look inside PDF and Word files?**
Yes, when the text extraction tools named in `settings/binaryfile.ini` are installed, or with Solr
([Intranet search, Searching inside documents](intranet-search.md#searching-inside-documents)).

**Can staff mount the library as a network drive?**
No. Exponential has no WebDAV server; files go in through multi-upload or the edit form
([Documents and file libraries, Network drives](documents-and-file-libraries.md#network-drives-webdav)).

## Publishing and communication

**Can texts be approved before they appear?**
Yes, with an Approve event triggered before publishing, or with review states
([Approval workflows](approval-workflows.md)).

**Can an announcement go live at a set time?**
The workflow event *Wait until date* holds publishing back until a date field of the class; it needs the workflow
cronjob ([News and announcements](news-and-announcements.md#publishing-at-a-set-time)).

**How are staff told about new content?**
They follow folders and get mail at once or as a digest ([Notifications for staff](notifications-for-staff.md)).

**Can we fix a typo without mailing everyone again?**
Yes, with *Publish without notification* for editors who have the policy
([News and announcements](news-and-announcements.md#corrections-without-a-second-mail)).

## Operations and records

**Can we see who changed or removed something?**
Yes. The audit trail records content, access and system events in a tamper-evident log that is on by default
([The audit trail](../../../features/6.0/audit-trail.md)).

**What must run in the background?**
Cronjobs, above all the `frequent` group with the workflow, notification and audit parts
([Cronjobs](../../../guides/cronjobs.md)).

**How do we remove people who left?**
Disable or deactivate their accounts on **Users > Check user accounts**; removing an account is final, so disabling is
the safer first step ([Check user accounts](../../../features/6.0/user-hygiene.md)).

**What does an intranet cost to run?**
See the [worked example for an intranet](../costs/worked-example-intranet.md), whose inputs are example assumptions
for you to replace.

**Do staff need an app on their phones?**
No app is needed to read the intranet; it is a website. The authenticator app is only needed if two-step sign-in with
app codes is used ([Mobile and headless](../mobile-and-headless/README.md)).

## Related pages

- [Intranets](README.md)
- [Intranet rollout checklist](intranet-rollout-checklist.md)
- [Extranet FAQ](../extranets/extranet-faq.md)
- [Businesses FAQ](../businesses-faq.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
