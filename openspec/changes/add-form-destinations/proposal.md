# Proposal

Redmine #16741 (https://pm.team.in-mv.com/issues/16741), change 4 of 5.
Binding design source: `openspec/handoff/marketing-automation-16741.md`, sections 2 and 5.4.
Meant for upstream (se7enxweb). Depends on `add-workflow-outward-action-safeguards` and, for the webhook
destination, `add-workflow-webhooks-and-importers`; the series destination depends on
`add-newsletter-welcome-series`.

## Why

Forms are where a campaign meets its audience: a sign-up for an event, a download in exchange for an address, a
contact form. Exponential collects form submissions (information collection), but what happens next is mail to
an address or nothing. Connecting a form to a newsletter list or to an external tool needs custom code per site.

## What Changes

- **Destinations** for a form, each switched on alone, configured per content class or per form object:
  - subscribe the submitted address to a newsletter list, always through the list's double opt-in;
  - start the list's welcome series for it (`add-newsletter-welcome-series`), which implies the subscription;
  - send the submission to a webhook managed in the webhook admin of #16702, as a new event "form submitted"
    whose payload carries the form, the submission time and the mapped fields instead of instance and
    transition fields; signing, retries and the delivery log are those of the webhook admin.
- A field mapping per destination (which form field is the address, the first name, the last name; for the
  webhook, which fields are sent).
- Destinations run in the background after the submission is stored; a failing destination never loses the
  submission and never shows the visitor an error it caused. Failures are listed on the form's admin page.
- Destinations are outward and follow the safeguards of `add-workflow-outward-action-safeguards` (site-wide
  switch, bulk suppression, once per submission and destination).
- A **share link with a QR code** for a form, generated on the server.

## Capabilities

### New Capabilities
- `forms/destinations`: destinations of a form, field mapping, background delivery, failures, the share link.

### Modified Capabilities
- none.

## Impact

- The carrying extension is decided in design.md after the verification in handoff section 5.4 (whether the
  kernel's information collection offers a hook after storing a collection). A kernel change, if needed at all,
  must be justified there before anything else.
- The webhook admin gains the event type "form submitted".
- No change to existing forms until a destination is switched on.

## Out of scope

- A form builder; forms stay content classes with information-collector attributes.
- Starting a workflow instance from a submission (a submission is not a content version).
- Spam protection beyond what the site already has.
