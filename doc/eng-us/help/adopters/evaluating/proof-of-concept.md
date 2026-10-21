---
title: A proof of concept for adopters
audience: adopter, project-manager, developer, businesses
category: adoption
order: 13
summary: How adopters run a short proof of concept with Exponential - choosing the hardest requirement, scope and time box, a sample of real content, what to measure, who should take part, and how to write up the result for the decision.
keywords: proof of concept, POC, pilot, hardest requirement, time box, sample content, measure, evaluation, decision
type: help
---
# A proof of concept for adopters

A proof of concept answers one question: can Exponential do the thing we are most worried about? It is not a first
version of the site.

## Contents

1. [Choose the question](#choose-the-question)
2. [Scope and time box](#scope-and-time-box)
3. [Run it](#run-it)
4. [Write up the result](#write-up-the-result)

## Choose the question

Pick the requirement that would end the evaluation if it failed. Examples:

| Worry | Proof of concept |
|---|---|
| Our content does not fit a standard CMS | Model three real content types and enter ten real items ([Content modeling first steps](../planning/content-modeling-first-steps.md)) |
| Our editors need to build pages | Build two landing pages with Exponential Layouts |
| We sell in several languages | One product in two languages with prices, through checkout ([Online stores](../../businesses/stores/README.md)) |
| Our apps must read the content | Read content through the REST API ([ezprestapi](../../../features/6.0/extensions/ezprestapi.md)) |
| Our old content must come over | Migrate a sample with URLs kept ([Migrating](../migrating/README.md)) |
| Our permissions are complex | Rebuild two roles and test them ([Roles and permissions for the rollout](../launching/roles-and-permissions-for-rollout.md)) |

## Scope and time box

- One to three questions, not ten.
- A fixed time box agreed in advance (an example assumption: one to two weeks of one developer's time); stop when it
  ends.
- A test installation on SQLite is usually enough ([Trying the demo and a test install](trying-the-demo-and-a-test-install.md)).
- Invented or anonymized content only.

## Run it

1. Write down the success criteria before you start.
2. Involve one future editor; their reaction is part of the result.
3. Note every step that needed the documentation, the forums or a workaround.
4. Measure what matters to you: time to build, steps for an editor, page speed.

## Write up the result

```text
Question:            <the requirement tested>
Success criteria:    <as agreed before>
Result:              <met / partly met / not met>
Evidence:            <screenshots, steps, measurements>
Workarounds needed:  <...>
Open questions:      <...>
Recommendation:      <continue / stop / test further>
```

The write-up goes into the scorecard and the decision record
([The evaluation scorecard](evaluation-scorecard.md), [Writing the decision](../deciding/writing-the-decision.md)).

## Related pages

- [Evaluating Exponential](README.md)
- [Risks and how to weigh them](risks-and-how-to-weigh-them.md)
- [Estimating and scoping with extensions](../../leadership/estimating-and-scoping-with-extensions.md)
- [Project templates](../../leadership/project-templates.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
