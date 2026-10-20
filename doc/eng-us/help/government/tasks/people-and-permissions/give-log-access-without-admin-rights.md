---
title: Give log access without admin rights
audience: government, admin, developer
category: public-sector
order: 160
summary: Step by step - let a developer, support engineer or operator read Setup > Log files, the error digest and log health with personal data masked, without full access, and decide separately about managing and unmasking logs.
keywords: log access, log viewer, setup/logs, setup/managelogs, setup/unmasklogs, masked logs, developer access, support engineer, error digest, log health, least privilege
type: help
---
# Give log access without admin rights

This task is for administrators. At the end, a colleague can read the installation's logs in the administration,
masked, without being an administrator. The log viewer's rights are three functions of the `setup` module
([Log viewer, "Who may use it"](../../../../features/6.0/log-viewer.md#who-may-use-it)).

## Contents
1. [Before you start](#before-you-start)
2. [Choose the rights](#choose-the-rights)
3. [Create and assign the role](#create-and-assign-the-role)
4. [Check it worked](#check-it-worked)
5. [Related pages](#related-pages)

## Before you start

- An administrator account.
- The person's need: reading (most cases), rotating or emptying logs (operations), or seeing unmasked e-mail and IP
  addresses (an incident investigation).

## Choose the rights

| Function | Allows | Give it when |
|---|---|---|
| `setup/logs` | open **Setup > Log files**, follow and search, the error digest and log health, download a range (when downloads are on) | reading logs |
| `setup/managelogs` | **Rotate now**, empty a file, remove an archived rotation, each only when switched on in `logviewer.ini [ActionSettings]` | operations duty |
| `setup/unmasklogs` | the **Show unmasked** switch | an investigation, for a limited time |

E-mail addresses, IP addresses, passwords, tokens, keys, session ids and cookies are masked by default; secret
setting values are never shown. Every view and action is audited.

## Create and assign the role

1. **Users > Roles and policies > New role** "Log reader".
2. **New policy**: `setup` / `logs`, **Grant full access** for that function. Add `setup` / `managelogs` only if
   chosen above.
3. **New policy**: `user` / `login`, SiteAccess: the administration siteaccess (unless another role gives it).
4. **Save**, then **Assign** to the person or their group.

## Check it worked

- Signed in as the person: the Setup menu shows **Log files**; **Error digest** and **Log health** open.
- Lines show masked addresses (`203.0.x.x`); there is no **Show unmasked** switch.
- Without `setup/managelogs`, no **Rotate now** button.

On the shell, logs are read with `./console exp:logs`, which checks no policy: shell access is a separate decision.

## Related pages
- [The log viewer](../../operations/monitoring/the-log-viewer.md)
- [Read the error digest](../operations/read-the-error-digest.md)
- [Give an auditor read access to the audit trail](give-an-auditor-read-access-to-the-audit-trail.md)
- [Least-privilege principles](../../operations/people-and-permissions/least-privilege-principles.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
