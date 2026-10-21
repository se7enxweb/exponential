---
title: Security for integrations
audience: integrator, developer, admin
category: integration
order: 340
summary: The security rules an Exponential integration must follow - credentials never in Git, settings/override and the environment, least privilege for service accounts, transport security, input from other systems treated as untrusted, outbound calls, the Content Security Policy for third-party scripts, and audit and incident handling.
keywords: security, credentials, secrets, settings/override, gitignore, environment variables, least privilege, service account, HTTPS, TLS, input validation, SSRF, outbound calls, Content Security Policy, third-party scripts, analytics, audit, incident, key rotation
type: help
---
# Security for integrations

This page is for everyone who builds or operates an integration with Exponential. An integration opens a door: it
holds credentials for two systems, it moves data across a boundary, and it runs with rights nobody watches every
day. The general hardening of an installation is in [Security hardening](../security-hardening.md) and
[Security and audit](../../guides/security-and-audit.md); this page adds what is specific to integrations. The
checklist version is [Integration security checklist](integration-security-checklist.md).

## Contents

1. [Credentials](#credentials)
2. [Least privilege](#least-privilege)
3. [Transport](#transport)
4. [Data from other systems is untrusted](#data-from-other-systems-is-untrusted)
5. [Outbound calls](#outbound-calls)
6. [Third-party scripts in pages](#third-party-scripts-in-pages)
7. [Audit and incidents](#audit-and-incidents)

## Credentials

| Rule | How in Exponential |
|---|---|
| Never in Git | Keep secrets in `settings/override/<file>.ini.append.php` of each installation, and keep that directory (or at least those files) out of the project's repository with `.gitignore`; never in an extension's own `settings/` |
| Or in the environment | Read them with `getenv()` in your extension when the hosting provides a secret store as environment variables |
| Never in a URL | API keys sent in the address are refused by the REST layer; do the same in your own endpoints |
| Never in logs or packages | Mask them; packages travel |
| One credential per system, environment and purpose | A key for "ERP import, production" is not used by staging |
| Rotation planned | End dates on keys; a rotation procedure in the runbook ([Personal API keys](personal-api-keys-for-integrations.md#rotating-without-downtime)) |
| Encrypted at rest where the tool supports it | `xrowextract` encrypts destination secrets with a key in `settings/override/xrowextract-secrets.key`; `sevenx_authentication_2fa` encrypts authenticator secrets with `[TOTPSettings] SecretKey` |

`settings/override` outranks every siteaccess and extension setting, which is why it is the right place for secrets
and also why an override can silently change behavior on one server: document every override the integration needs.

## Least privilege

- **A service account per integration**, in a user group of its own, with a role that grants only what the
  integration does: read on the subtrees it exports, create limited by class and subtree for what it imports
  ([Service accounts, roles and policies](service-accounts-roles-and-policies.md)).
- **Scopes on keys**: a reading integration gets a key with the `read` scope only.
- **No administrator credentials** in any integration, ever. An import that "needs admin" usually needs one more
  policy.
- **Operating system user**: run scripts as the site's user, not as root.

## Transport

- HTTPS for every API, webhook and file transfer, in both directions. `rest.ini [Authentication] RequireHTTPS` makes
  the REST layer refuse plain HTTP.
- Verify certificates on outbound calls (the SOAP client and curl do by default; never switch it off to "make it
  work").
- LDAP over an encrypted connection (`ldaps://`); StartTLS is not available
  ([Login handlers](login-handlers-ldap-and-textfile.md)).
- SFTP rather than FTP; the `xrowextract` SFTP destination trusts host keys explicitly.

## Data from other systems is untrusted

Treat every value from another system like a form field from the internet:

- Validate types and lengths before use; cast ids with `(int)`; escape strings with `$db->escapeString()` in any SQL
  of your own.
- Convert rich text into the stored XML of `ezxmltext` with a small allowed set of elements; never pass HTML straight
  into templates.
- Refuse unsafe URL schemes; the `ezurl` datatype refuses script URLs on import, do the same in your own code.
- File paths for images and files must stay inside the installation's import directory; the `ezimage` datatype
  refuses paths outside the installation, its var and temporary directories.
- Limit sizes: a malicious or broken source can send a 2 GB "image".

## Outbound calls

When your integration calls addresses that come from data or settings (a webhook URL, an image URL from a feed):

- Allow only `https` (and `http` where unavoidable).
- Refuse private and loopback addresses unless they are expected, to avoid server-side request forgery.
- Set connect and total timeouts and a maximum response size.
- The kernel's link checker shows the pattern: `cronjob.ini [linkCheckSettings] AllowedPrivateHosts[]`.

## Third-party scripts in pages

Analytics, chat widgets and tag managers run in visitors' browsers. Exponential sends a Content Security Policy; a
script from another host must be allowed there, and inline scripts are not
([Content Security Policy](../../features/6.0/content-security-policy.md)). Add each third-party host deliberately,
load scripts from files of your design, and record the data protection consequences with the customer (not legal
advice).

## Audit and incidents

- The audit trail records API key use and refusals (`access.apikey.*`), `expservices` writes (`service.*`) and
  content changes. Review the integration's service account in the audit after the first runs.
- Feed security events to the customer's SIEM with the syslog or webhook sink
  ([Outgoing webhooks](outgoing-webhooks-from-the-audit-trail.md#sending-security-events-to-a-siem)).
- When a credential may be exposed: revoke it at once, make a new one, check the audit for its use, and tell the
  customer. Report vulnerabilities in Exponential privately to security@se7enx.com.

## Related pages

- [Security hardening](../security-hardening.md)
- [Security and audit](../../guides/security-and-audit.md)
- [Security in extensions](../extension-development/part-3-extension-developer/security-in-extensions.md)
- [Security responsibilities of partners](../partners/security-responsibilities.md)
- [Integration security checklist](integration-security-checklist.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
