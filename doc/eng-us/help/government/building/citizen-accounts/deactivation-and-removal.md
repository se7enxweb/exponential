---
title: Deactivation and removal
audience: government, admin, project-manager
category: public-sector
order: 70
summary: The three ways to end an account in Exponential - disable, deactivate and move, remove for good - what each keeps and deletes, mail preference erasure and audit pseudonymization on removal, and how to answer a request to delete an account.
keywords: deactivate, disable account, Deactivate and move, DeactivateAndMove, remove user, account removal, erasure, right to erasure, mail preferences, consent log, audit pseudonymization, user/deactivate
type: help
---
# Deactivation and removal

This page is for administrators and project managers who end accounts: staff who leave, public users who ask for
deletion, accounts nobody uses. Exponential offers three steps of increasing finality. Sources:
[Check user accounts](../../../../features/6.0/user-hygiene.md#deactivate-and-move) and
[Privacy standards](../../../../standards/privacy.md).

> **Note** This page describes features and common practice; it is not legal advice. Check the law that applies to
> your organization with your legal counsel.

## Contents

1. [Three ways to end an account](#three-ways-to-end-an-account)
2. [Deactivate and move](#deactivate-and-move)
3. [Removing an account](#removing-an-account)
4. [Answering a deletion request](#answering-a-deletion-request)
5. [Staff who leave](#staff-who-leave)

## Three ways to end an account

| Way | Where | Reversible | Keeps |
|---|---|---|---|
| **Disable** | **Users > Check user accounts**, or the account | yes (**Enable**) | everything; the account cannot sign in |
| **Deactivate and move** (opt-in) | **Users > Check user accounts**, the user's menu | yes (**Reactivate and move back**) | everything; the account is disabled and moved to a hidden group without roles |
| **Remove** | the user object's own **Remove** | no: user objects never go to the trash | content the user created stays |

## Deactivate and move

Off by default. Switch it on with `site.ini [UserSettings] DeactivateAndMove=enabled` (in `settings/override/` or the
administration siteaccess). The first use creates a hidden user group *Deactivated users* below the Users node, unless
`DeactivatedUserGroupNodeID` names a group of your own. The account keeps its content, versions, relations and the
roles assigned to the account itself; it loses the roles of the groups it was in, because it is no longer in them.
A role is never assigned to the deactivated group: the role assignment leaves it out and says so. The function is
`user/deactivate`.

Use it for staff who leave and for public accounts during a waiting period before removal.

## Removing an account

| What happens on removal | Source |
|---|---|
| The user object and account are deleted for good | [Check user accounts](../../../../features/6.0/user-hygiene.md) |
| Mail preferences and pending confirmations are removed; the consent log is anonymized (`expMailPreferences::eraseForRemovedAccount()`) | [Privacy standards](../../../../standards/privacy.md) |
| Audit records about the user are kept for their retention period and pseudonymized | [Audit trail](../../../../features/6.0/audit-trail.md) |
| Form submissions sent by the user stay in **Setup > Collected information** (they carry a user ID) | [Collected information](../../../../guides/collected-information.md) |
| Content created by the user stays | the content model |

Mail preferences can also be erased on request with `./console exp:mail:preferences erase --yes`
([Mail preferences for administrators](../../../../guides/mail-preferences-administrator.md)).

## Answering a deletion request

1. Confirm the request comes from the account holder (sign-in, or a check by a route you trust).
2. Export what you must give them if they also asked for their data: mail preferences and consent history
   (`mailpreferences/export` by the person, or `./console exp:mail:preferences export`), audit records about them
   (`./console exp:audit export --subject-user=<id>`) ([Privacy standards](../../../../standards/privacy.md)).
3. Check what else holds their data: form submissions, comments or other content they wrote, newsletter subscriptions
   in cjw_newsletter.
4. Remove the account (the user object's **Remove**); remove or anonymize the other items as your policy says.
5. Record that the request was handled, without copying the data into the record.

Exponential has no single "export all my data" function; the steps above cover the places it keeps personal data.
Under the [GDPR](https://eur-lex.europa.eu/eli/reg/2016/679/oj) in the EU, the right to erasure has exceptions, for
example for records you must keep; your data protection officer decides.

## Staff who leave

| When | Action |
|---|---|
| On the last day | **Disable** or **Deactivate and move**; end their sessions |
| Their content | change the owner to the team ([Changing owners](../../../../guides/changing-owners.md)) |
| Their approvals | check **Setup > Workflow processes** and approval settings for their name |
| Later, per policy | remove the account |

## Related pages

- [Citizen accounts](README.md)
- [Account hygiene](account-hygiene.md)
- [Mail preferences for administrators](../../../../guides/mail-preferences-administrator.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
