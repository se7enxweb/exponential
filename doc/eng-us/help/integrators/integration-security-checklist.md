---
title: Integration security checklist
audience: integrator, admin, developer
category: integration
order: 500
summary: A checklist for the security review of an Exponential integration before go-live and once a year - credentials, accounts and roles, transport, input handling, outbound calls, logging, audit, data protection and incident readiness - each item with the setting or page that backs it.
keywords: security review, checklist, credentials, secrets, service account, least privilege, HTTPS, TLS, input validation, SSRF, logging, audit, personal data, incident, key rotation, go-live
type: help
---
# Integration security checklist

This page is for whoever reviews an integration before it goes live, and again once a year. Each item names where to
check it. The reasons behind the items are on [Security for integrations](security-for-integrations.md).

## Contents

1. [Credentials](#credentials)
2. [Accounts and rights](#accounts-and-rights)
3. [Transport](#transport)
4. [Input and output](#input-and-output)
5. [Logging and audit](#logging-and-audit)
6. [Data protection](#data-protection)
7. [Incident readiness](#incident-readiness)
8. [Sign-off](#sign-off)

## Credentials

- [ ] No secret in the extension's repository or its history (search for `expk_`, `expt_`, `password`, `secret`, `key`).
- [ ] Secrets only in `settings/override/*.ini.append.php` (kept out of Git) or the environment.
- [ ] One credential per integration, environment and purpose; names say which.
- [ ] API keys limited to the scopes needed; end dates set; rotation date in the calendar
      ([Personal API keys](personal-api-keys-for-integrations.md)).
- [ ] OAuth client secrets only on servers; `oauth/authorize` only for the roles that need it.
- [ ] Credentials the integrator's staff knew are rotated at handover.

## Accounts and rights

- [ ] A service account per integration; no administrator role ([Service accounts](service-accounts-roles-and-policies.md)).
- [ ] Policies limited by subtree, class, section and language as far as possible.
- [ ] A forbidden call was tried and answered `403`.
- [ ] Scripts run as the site's operating system user, not root.
- [ ] LDAP: dedicated bind account with read rights only; `LDAPDebugTrace=disabled`.
- [ ] SSO handler: the trusted header can only come from the proxy; administrators sign in another way.

## Transport

- [ ] HTTPS on every API and webhook, both directions; `rest.ini [Authentication] RequireHTTPS` considered.
- [ ] Certificate verification on for every outbound call.
- [ ] SFTP instead of FTP; host keys trusted explicitly.
- [ ] LDAP over `ldaps://`.
- [ ] Endpoints that only other systems use are limited to their addresses at the web server, where possible.

## Input and output

- [ ] Every value from another system is validated (type, length, allowed characters); ids cast to integers.
- [ ] SQL of your own uses `(int)` and `$db->escapeString()`; no string concatenation of raw input.
- [ ] Rich text converted to an allowed set of elements; no raw HTML into templates.
- [ ] URLs: only `https`/`http`; unsafe schemes refused.
- [ ] File imports stay inside the import directory below `var/`; sizes limited.
- [ ] Outbound calls to addresses from data refuse private and loopback addresses; timeouts and size limits set.
- [ ] Endpoints of your own check access (`canRead()`, policies) and require `POST` and the form token for writes.
- [ ] Answers contain no passwords, tokens or personal data the caller does not need.

## Logging and audit

- [ ] One summary line per run in the integration's log; errors in `error.log` through `eZDebug::writeError()`.
- [ ] No secrets or full personal-data payloads in any log.
- [ ] The service account's activity checked in the audit after the first runs (`exp:audit tail`).
- [ ] Refusals (`access.apikey.use.failed`, `access.permission.refused`) watched.
- [ ] If required, security events sent to the SIEM through the syslog or webhook sink
      ([Outgoing webhooks](outgoing-webhooks-from-the-audit-trail.md)).

## Data protection

These items are practice, not legal advice.

- [ ] Every flow with personal data listed with its purpose, fields, recipients and retention.
- [ ] Processors (mail provider, AI provider, CDN, SaaS search) known to the customer and covered by agreements.
- [ ] Only needed fields are sent; exports deleted when no longer needed.
- [ ] Collected form data removed from Exponential when the retention ends.
- [ ] Test data anonymized when a production copy leaves production.

## Incident readiness

- [ ] The runbook says how to revoke each credential at once.
- [ ] The runbook says how to pause each flow (Setup > Cronjobs, Pause).
- [ ] Contacts for each other system and for security are listed.
- [ ] Vulnerabilities in Exponential are reported privately to security@se7enx.com.

## Sign-off

```text
Integration: ...............   Version: .........   Environment: production
Reviewed by: ...............   Date: .........
Open findings (id, severity, owner, due): ...............
Decision: go-live approved / approved with conditions / not approved
```

## Related pages

- [Security for integrations](security-for-integrations.md)
- [Hosting security review checklist](../hosting/checklists/security-review.md)
- [Accessibility, security and compliance checklists](../leadership/accessibility-security-and-compliance-checklists.md)
- [Integration go-live checklist](integration-go-live-checklist.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
