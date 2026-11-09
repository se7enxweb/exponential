# Proposal

Redmine #16741 (https://pm.team.in-mv.com/issues/16741), change 6 of 7.
Binding design source: `openspec/handoff/marketing-automation-16741.md`, sections 2 and 5.6.
Meant for upstream (se7enxweb). The code lands in the package `se7enxweb/cjw_newsletter`; this change is the plan.
No dependency for segments in sends; a segment as the audience of a welcome series needs
`add-newsletter-welcome-series`.

## Why

cjw_newsletter already addresses people well inside a mail: interests, conditional parts, placeholders, a block of
articles per interest, a language per subscriber. What it cannot do is choose *who* gets a send beyond a whole list:
virtual lists filter only by address and salutation. Campaigns need groups such as "picked the interest Events",
"organisation is set", "subscribed in the last 30 days" or "did not open the last three editions". The data for most
of this is already there, the last one only for subscribers who consented to statistics.

## What Changes

- **Segments**: a named, saved selection of the subscribers of one list, built from conditions combined with all/any:
  - subscription: subscribed or approved since, language, output format;
  - profile: salutation, first name, last name, organisation, the four custom fields;
  - interests: picked or not picked;
  - behavior: opened or clicked an edition of the list in the last N sends or days, clicked a link to a given
    content object or tag, did not open any of the last N sends.
- **Behavior conditions use only what the statistics may keep**: per-person opens and clicks exist only for
  subscribers who consented to newsletter statistics, and only within the retention period. A behavior condition
  never matches a subscriber without that consent; the segment page says how many subscribers have not consented.
- **No data is not "did not open"**: a behavior condition counts only sends that were tracked per person, went to
  the subscriber after the consent, and whose data is still kept; test and SMS sends never count. A "did not open
  the last N sends" condition does not match a subscriber for whom fewer than N such sends are known.
- A segment shows its current size and a sample of members to editors with the right to see subscribers.
- A segment can be chosen in the **send form** instead of the whole list, and as the **audience of a welcome
  series** (`add-newsletter-welcome-series`).
- A send to a segment goes to its members whose subscription is approved when the mail queue is made; later changes
  to the segment do not change a send that is already queued.
- The existing virtual lists keep working; the design decides whether they become segments.

## Capabilities

### New Capabilities
- `newsletter/segments`: defining, previewing and using segments, and the consent rule for behavior conditions.

### Modified Capabilities
- none in this repository's specs.

## Impact

- Package `se7enxweb/cjw_newsletter`: a table for segments and their conditions, added by the package's schema
  upgrade; the send form, the series and the queue creation learn to take a segment.
- This repository: the `composer.json` constraint once the release exists; the change is archived then.

## Out of scope

- Segments of website visitors or of site users who are not subscribers.
- Scores, predictions or AI-chosen audiences.
- Segments across several lists (a send goes to one list today; a later change if needed).
