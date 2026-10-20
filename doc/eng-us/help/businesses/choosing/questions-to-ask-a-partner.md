---
title: Questions to ask a partner
audience: businesses, project-manager, partner, hosting
category: business-solutions
order: 40
summary: A copy-ready list of questions for interviews and requests for proposal with implementation, hosting and support partners for an Exponential site - experience, approach, hosting, security, data protection, updates, support, costs and exit - with what a good answer contains.
keywords: questions, partner interview, request for proposal, RFP, vendor questionnaire, due diligence, hosting questions, security questions, support, SLA, exit, references
type: help
---
# Questions to ask a partner

This page is for whoever interviews companies that will build, host or support an Exponential site. Copy the lists
into your request for proposal or interview notes. After each group, "a good answer" says what to listen for, based
on what the project's documentation makes possible.

## Contents

1. [How to use the questions](#how-to-use-the-questions)
2. [Experience and team](#experience-and-team)
3. [Approach and the content model](#approach-and-the-content-model)
4. [Hosting and operations](#hosting-and-operations)
5. [Security and data protection](#security-and-data-protection)
6. [Updates and the long term](#updates-and-the-long-term)
7. [Support and response](#support-and-response)
8. [Costs](#costs)
9. [Ownership and exit](#ownership-and-exit)

## How to use the questions

- Send the same questions to every candidate, in writing.
- Ask for written answers; then interview to go deeper.
- Score the answers with the [Vendor evaluation scorecard](../templates/vendor-evaluation-scorecard.md).
- Skip groups that do not apply (a pure design agency need not answer hosting questions).

## Experience and team

```text
1. Which Exponential sites have you built or run? May we see them, with the customers' permission?
2. May we call one of those customers?
3. Who exactly will work on our project, and what is their experience with Exponential?
4. Are you part of the Exponential Partner Program? Which profile and level?
5. Have you contributed fixes, extensions or documentation to the project?
```

A good answer names real, visible work and people, and offers a reference call. It does not quote customers who have
not agreed ([Case studies and content marketing](../../business/case-studies-and-content-marketing.md) explains why
consent matters).

## Approach and the content model

```text
6. How do you plan the content model (classes, sections, roles) with us?
7. Which of our needs will you meet with built-in features or existing extensions, and which with custom code?
8. Where will custom code live: in extensions and template overrides, or in changed kernel files?
9. How will you test accessibility?
10. How do you hand over to our editors and administrators?
```

A good answer reuses built-in features ([Estimating and scoping with extensions](../../leadership/estimating-and-scoping-with-extensions.md)),
keeps custom code in extensions, and plans workshops ([Content modeling workshops](../../leadership/content-modeling-workshops.md)).

## Hosting and operations

```text
11. Which hosting model, PHP version, database and engine (Velocity or a web server) do you propose, and why?
12. What do backups include, how often, how long kept, and when was the last restore test?
13. How is the site monitored from outside, and who is told when it is down?
14. How are cron jobs and outgoing mail set up?
15. How do you handle planned work: maintenance windows, maintenance mode?
```

A good answer matches [Choosing hosting](choosing-hosting.md), names a restore test date, and covers everything in
[Backups: what to back up](../../hosting/operations/backups-what-to-back-up.md).

## Security and data protection

```text
16. Who applies security updates for Exponential, extensions, PHP and the operating system, and within how long?
17. Will two-step sign-in be required for administrators?
18. How is the administration protected (host name, network limits)?
19. Where are the servers and backups located? Who can access our data?
20. Which contract do you offer for processing personal data on our behalf?
21. How do you help us answer requests to see, export or delete a person's data?
22. What happens, and who tells whom, if there is a security incident?
```

A good answer names a time frame for applying released fixes ([Security updates and disclosure](../security/security-updates-and-disclosure.md)),
uses two-step sign-in ([User accounts and passwords](../security/user-accounts-and-passwords.md)) and knows the
mail and audit tools for data requests ([GDPR basics](../security/gdpr-basics.md)).

## Updates and the long term

```text
23. How do you keep a site on the current 6.0.x release? How often do you update?
24. How do you test an update before it reaches the live site?
25. How do you plan PHP upgrades?
26. What happens to our site if your company stops working with Exponential?
```

A good answer describes a staging copy, the upgrade check and small, frequent updates
([Budgeting for upgrades and maintenance](../costs/budgeting-for-upgrades-and-maintenance.md)).

## Support and response

```text
27. Which response times do you commit to, per severity, in writing?
28. Are they business hours or around the clock? In which time zone?
29. How do we report a problem, and how do we follow it?
30. Do you escalate to the Exponential project or 7x when needed? How?
```

A good answer has severities and times ([Support tiers](../../hosting/business/support-tiers.md)) and does not promise
what depends on others ([SLA options](../../hosting/business/sla-options.md#security-updates)).

## Costs

```text
31. What is the one-time cost, and what is included?
32. What are the monthly or yearly costs, and what is included?
33. How are changes outside the scope priced?
34. Which third-party costs should we expect (payment provider, mail service, AI provider, CAPTCHA)?
```

A good answer is in writing and separates one-time, recurring and third-party costs, so you can fill in the
[ROI worksheet template](../templates/roi-worksheet-template.md). Note that no license fee should appear for
Exponential itself (`README.md`, "License").

## Ownership and exit

```text
35. Who owns the templates, extensions and design you create for us?
36. Do we hold administrator access and the hosting account?
37. How do we get the code, database and files if we leave? In which format and how fast?
38. Will you cooperate with a new provider during a handover?
```

A good answer gives you access and copies at any time. The GPL lets you take the software anywhere
([Licensing explained for buyers](../../business/licensing-for-buyers.md)); make sure the contract does the same for
your own work and data.

## Related pages

- [Choosing a partner](choosing-a-partner.md)
- [Contracts and SLA basics](contracts-and-slas-basics.md)
- [Pre-sales support (for partners)](../../partners/pre-sales-support.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
