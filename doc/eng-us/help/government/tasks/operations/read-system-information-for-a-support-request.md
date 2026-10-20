---
title: Read system information for a support request
audience: government, admin, hosting
category: public-sector
order: 230
summary: Step by step - prepare a support request to your partner or the project - download the system information report (secrets left out), add the error reference and log lines, and say which server answered.
keywords: support request, system information, report, Download report, exp:system:info, health checks, support partner, ticket, error reference
type: help
---
# Read system information for a support request

This task is for administrators who ask a support partner or the community for help. At the end the request holds
everything a supporter needs and nothing that should stay private. Reference:
[System information, section 6](../../../../guides/system-information.md#6-the-report-for-a-support-request).

## Contents
1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [What the report never contains](#what-the-report-never-contains)
4. [Related pages](#related-pages)

## Before you start

- `setup/system_info` (administrators), or shell access.

## Steps

1. Open **Setup > System information** (`/setup/info`) through the server where the problem happens (Apache, a
   Velocity port): the banner names which server answered, and each has its own PHP and caches.
2. Read the **Health checks**: a failure there may already be the answer.
3. **Download report (.txt)** (or **Download as JSON**, **Copy report as text**). **Measure sizes** adds directory and
   database sizes if relevant.
4. Add the error reference and the matching log lines ([Find an error by its reference](find-an-error-by-its-reference.md)),
   copied from the log viewer, which masks personal data.
5. Describe: what you did, what you expected, what happened, since when, what changed recently.

On the shell: `./console exp:system:info --allow-root-user` (the command-line PHP, not the web server's; OPcache off
there is normal).

## What the report never contains

Passwords, keys, tokens, session ids, credentials in addresses and full server paths are left out
([System information, section 7](../../../../guides/system-information.md#7-what-is-never-shown)). Still check the
text before sending, and never paste citizens' data into a support ticket; send screenshots only of test content.

Who supports Exponential: [Partners start here](../../../partners/partners-start-here.md) and [Who is 7x](../../../who-is-7x.md).

## Related pages
- [System information and health checks](../../operations/monitoring/system-information-and-health-checks.md)
- [When something goes wrong (help)](../../../when-something-goes-wrong.md)
- [System information guide](../../../../guides/system-information.md)
- [Support tiers (hosting)](../../../hosting/business/support-tiers.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
