---
title: Integration discovery worksheet
audience: integrator, project-manager
category: integration
order: 480
summary: A worksheet to fill in with the customer at the start of an integration project - the installation, the systems around it, every data flow with direction, timing, volume, keys and owners, identity, constraints, and the open questions - ready to copy.
keywords: discovery, worksheet, workshop, requirements, data flows, system landscape, questionnaire, integration inventory, scoping, template
type: help
---
# Integration discovery worksheet

This page is for integrators running the discovery workshop of an integration project. Copy the worksheet, fill it in
with the customer, and keep it as the input for the interface specifications and the estimate. Each question points
to the page that explains why it matters.

## Contents

1. [How to use it](#how-to-use-it)
2. [Part 1: the installation](#part-1-the-installation)
3. [Part 2: the systems around it](#part-2-the-systems-around-it)
4. [Part 3: one block per data flow](#part-3-one-block-per-data-flow)
5. [Part 4: identity and access](#part-4-identity-and-access)
6. [Part 5: constraints and open questions](#part-5-constraints-and-open-questions)

## How to use it

- Fill it in a workshop with the customer's project lead, the owners of the other systems and an operator.
- Ask for **samples**: a real export file, a real API answer, real form submissions. Discovery without real data
  underestimates data quality problems every time.
- Mark every answer that is a guess; it becomes an assumption in the statement of work.

## Part 1: the installation

```text
Exponential version (Setup > System information or composer show): ...........
Distribution (Legacy / Nexus): ...........   Database engine: ...........   PHP: ...........
Served by (Apache + PHP-FPM / Velocity / FrankenPHP): ...........
Siteaccesses involved: ...........
Extensions active that matter (ezprestapi, expservices, xrowextract, syndication, sevenx_authentication_2fa ...):
...........
Environments available (development / staging / production), and who hosts them: ...........
Who may deploy code, and how (Git, Composer, a pipeline): ...........
```

Why: [Integration surfaces at a glance](integration-surfaces-at-a-glance.md#checking-what-an-installation-has-active).

## Part 2: the systems around it

```text
System         Product and version   Owner (team, contact)   API / export available   Test instance?   Docs?
CRM            ...                   ...                     ...                      ...              ...
ERP            ...
PIM / DAM      ...
Identity       ...
Search         ...
Mail           ...
Payment        ...
Analytics      ...
Other CMS      ...
```

Why: [What integrators do with Exponential](what-integrators-do-with-exponential.md).

## Part 3: one block per data flow

```text
Flow name: ...........................................   Priority: must / should / could
Direction: into Exponential / out of Exponential / both
Master of the data: ...........   Fields owned by editors: ...........
Started by: Exponential (push) / the other system (push) / a schedule (pull)
Timing: nightly / hourly / within minutes / on event       Acceptable delay: ...........
Volume: records in total ...........  changes per day ...........  peak ...........
Record key at the source: ...........   remote_id scheme: ...........
Content: plain fields / rich text / media / relations / languages (which) ...........
Removals at the source: remove / hide / state / keep
Errors: what happens with an invalid record? who is told?
Personal data in the flow: yes / no, which ...........
Sample received: yes / no (file, date) ...........
Surface proposed: ...........   Credential proposed: ...........
```

Why: [Push and pull, batch and real time](integration-patterns-push-pull-batch-real-time.md),
[Idempotent imports](idempotent-imports-keyed-by-remote-id.md), [Choosing credentials](choosing-credentials-for-an-integration.md).

## Part 4: identity and access

```text
Who signs in to the site?  staff ......  members ......  partners ......  public ......
Where do their accounts live? directory (LDAP, Active Directory) / identity provider (SAML, OIDC) / social / Exponential
Password typed on the site acceptable? yes / no
Groups and roles derived from the directory? which mapping ...........
Leavers: how is Exponential told? ...........
Apps acting for users (OAuth)? ...........
```

Why: [Login handlers](login-handlers-ldap-and-textfile.md), [Single sign-on](single-sign-on-and-social-login.md).

## Part 5: constraints and open questions

```text
Security requirements (network zones, allowed protocols, IP allowlists, SIEM): ...........
Data protection (processors, regions, retention): ...........
Availability needed per flow, maintenance windows: ...........
Deadlines and fixed dates: ...........
Budget frame: ...........
Open questions (owner, due date):
1 ...........
2 ...........
```

Why: [Security for integrations](security-for-integrations.md), [Estimating](estimating-integration-work.md).

## Related pages

- [Interface specification template](interface-specification-template.md)
- [Integration project playbook](integration-project-playbook.md)
- [Content modeling workshops](../leadership/content-modeling-workshops.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
