# Proposal

Redmine #16741 (https://pm.team.in-mv.com/issues/16741), change 3 of 7.
Binding design source: `openspec/handoff/marketing-automation-16741.md`, sections 2 and 5.3.
Meant for upstream (se7enxweb). The code lands in the package `se7enxweb/cjw_newsletter`; this change is the plan.
No dependency for the series itself; the workflow action needs the action registry of
`add-workflow-jobs-and-timers` and the safeguards of `add-workflow-outward-action-safeguards`.

## Why

cjw_newsletter sends editions to whole lists, on demand or on a schedule. What it cannot do is greet a new
subscriber: send a welcome mail right after the subscription becomes active, a second one a few days later, a
third after two weeks. Sites do this by hand today, or not at all.

## What Changes

- A **welcome series** per newsletter list: an ordered list of steps, each an edition with a delay after the
  previous step (the first step's delay counts from entering the series).
- A subscriber **enters** the series when the subscription to that list becomes approved, the state in which the
  list sends to it: after confirmation on a list that approves automatically, or after an administrator's
  approval. A pending or only confirmed subscription does not enter. Entering is idempotent.
- While a subscription is not approved (unsubscribed, removed, blacklisted, soft- or hard-bounced) no step is
  sent; the subscriber's progress is kept, and if the subscription becomes approved again the series continues
  with the next step not yet received.
- Each step is sent through the existing send path, so the list's skin, language, tracking setting, statistics
  and privacy rules apply unchanged; each subscriber receives each step at most once.
- Optionally an **audience**: a segment of the list (`add-newsletter-segments`). A subscriber enters only as a
  member, and a member who leaves the segment gets no further step; the progress is kept, as for a subscription
  that is not approved.
- Admin pages: the series of a list with its steps, the number of subscribers at each step, and a "send this
  step to me" test. A series can be paused and resumed.
- A workflow action `newsletter.series.start` (outward) that subscribes an address to a list through the list's
  double opt-in and lets it enter the list's series once the subscription is approved.

## Capabilities

### New Capabilities
- `newsletter/welcome-series`: the series, entering, pausing and continuing it, sending its steps, testing, and
  the workflow action.

### Modified Capabilities
- none in this repository's specs.

## Impact

- Package `se7enxweb/cjw_newsletter`: new tables for series, steps and each subscriber's progress, added by the
  package's schema upgrade (as for 4.2.0); a cron part or queue job that sends due steps; probably a new extension
  point when a subscription becomes approved (handoff 5.3).
- This repository: the `composer.json` constraint once the release exists; the change is archived then.

## Out of scope

- Branches inside a series (a series is linear; branching belongs to workflow definitions).
- New statistics; the existing open and click statistics count the steps like any edition.
