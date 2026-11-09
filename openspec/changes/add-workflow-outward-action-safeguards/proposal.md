# Proposal

Redmine #16741 (https://pm.team.in-mv.com/issues/16741), change 1 of 7.
Binding design source: `openspec/handoff/marketing-automation-16741.md`, sections 2 and 5.1; builds on the
workflow designer, Redmine #16702 (https://pm.team.in-mv.com/issues/16702), `openspec/handoff/expworkflow-16702-spec.md`
sections 3, 6.6, 7.1 and 7.4.
Meant for upstream (se7enxweb). Depends on `add-workflow-jobs-and-timers`, `add-workflow-admin-ui`,
`add-workflow-integrations` and, for webhook deliveries, `add-workflow-webhooks-and-importers`.

## Why

The action registry of #16702 lets a workflow definition call webhooks and, with the later changes of #16741,
post to channels and start newsletter series. Those deliveries reach people or systems outside the site. A
definition that is still being drawn, a test on a copy of the site, an import of a thousand objects or a retried
job must not mail customers or post in public. Today nothing in the plan distinguishes such deliveries from
internal ones like `hide` or `notify`.

## What Changes

- An action declares whether it is **outward** (reaches people or systems outside the site). The built-in
  `webhook` action is outward, and so is every delivery of a subscribed webhook (#16702 section 7.1), with or
  without an action. `notify`, `hide` and `unhide` are not outward: `notify` addresses only users of the site.
- A site-wide switch allows outward delivery; it is off on a new installation. While it is off, outward
  deliveries are recorded in the instance history as "not delivered (outward delivery off)" and the workflow
  continues.
- A **test run** of a definition on a chosen content object, by a user with the design right: it never changes
  the real object and never involves the publish gate; outward actions that send mail deliver to the testing
  user only, all other outward deliveries are recorded with the payload they would send and are not sent.
- **Bulk suppression**: operations that create or publish many objects at once (imports, restores, content jobs)
  run with outward deliveries suppressed; the history records the suppression.
- **Idempotency** of outward deliveries per instance, action and recipient or destination, independent of the
  queue's job idempotency.
- **Fan-out**: an outward job action with many recipients records each failure and continues; for the job action
  of a stage's `on_enter` the `failed` transition fires only when every recipient failed.
- The definition editor shows outward actions with a distinct mark, and the overview lists the definitions that
  contain them.

## Capabilities

### New Capabilities
- `workflow/outward-actions`: the outward flag, the site-wide switch, test runs, bulk suppression, idempotency and
  fan-out of outward deliveries.

### Modified Capabilities
- `workflow/action`: an action declares whether it is outward (the registry itself is unchanged).

## Impact

- `expworkflow` only. No kernel change expected; the bulk-suppression signal is decided in design.md after the
  verification in section 5.1 of the handoff.
- Every later outward action of #16741 (`channel.post`, `newsletter.series.start`) and the form destinations get
  the safeguards without code of their own.

## Out of scope

- Posting to channels → `add-channel-posts`.
- Newsletter series → `add-newsletter-welcome-series`.
- Rate limits per recipient across workflows (a later change if needed).
