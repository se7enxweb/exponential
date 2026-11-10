# Proposal

Redmine #16741 (https://pm.team.in-mv.com/issues/16741), change 2 of 7.
Binding design source: `openspec/handoff/marketing-automation-16741.md`, sections 2 and 5.2.
Meant for upstream (se7enxweb). Depends on `add-workflow-outward-action-safeguards`.

## Why

When content is published, editors want it announced where their audience is: a Mastodon account, a Matrix
room, later Slack or LinkedIn. Each such integration built on its own ends up with its own retry logic, its own
log and its own way of deciding where to post. A post to a public channel cannot be taken back, so the decision
when to post matters more than for a mail.

## What Changes

- A new extension (proposed name `expchannels`) with **destinations**: a named target per provider (a Mastodon
  account, a Matrix room), configured in admin4; credentials referenced by name from a secrets override, never
  stored in the database.
- **Outbound posts**: one record per post and destination with the states pending, sent, failed and cancelled,
  the number of attempts and the provider's id of the sent message. Producers only create the record; delivery
  runs in expqueue workers with retries, and a cron part sweeps pending and failed records.
- **One poster per provider** behind one interface; the first two are Mastodon and Matrix, then Bluesky and LinkedIn
  (handoff 5.2 names what each needs). The text and image of a post come from the share metadata of
  `add-share-metadata` when that change is built.
- The workflow action `channel.post` (outward) posts a rendered text of the content to one or more destinations.
  It is the only producer in the first version; posting on publish without a workflow definition is done with a
  definition that has this action, not with a separate switch.
- **A person's approval causes a post**: a stage posts only when the instance has had a transition fired by a
  person since the content was last written by the AI service or a system user. A job or timer transition after
  such a person transition carries that approval forward (approve now, go live at a date, post when live).
- An admin page per destination with the log of its posts, a "send test post" button, and an on/off switch; a
  destination switched off cancels its pending posts.

## Capabilities

### New Capabilities
- `channels/destinations`: configuring, testing and switching destinations.
- `channels/outbound-posts`: the workflow action, creating, delivering, retrying and logging posts, and the
  approval rule.

### Modified Capabilities
- none (the action registers through the registry of `add-workflow-jobs-and-timers`).

## Impact

- New extension; tables for destinations and outbound posts on every supported database engine, installed like
  `expworkflow`'s schema.
- Requires expworkflow for anything to be posted; without it, destinations and their test post work.

## Out of scope

- Providers beyond Mastodon, Matrix, Bluesky and LinkedIn (later posters behind the same interface).
- Reading from channels (replies, likes, statistics).
- Posting without a workflow definition.
