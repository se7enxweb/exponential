# Proposal

Redmine #16702 (https://pm.team.in-mv.com/issues/16702), milestone 7 of 8.
Binding design source: `openspec/handoff/expworkflow-16702-spec.md`, sections 7.1, 7.2, 7.3, 8.4 (list
of replaceable extensions), 12.7.
Meant for upstream (se7enxweb). Depends on `add-workflow-review-options`.

## Why

External tools need signed, retried notifications of workflow events, and existing sites need a way
to move their ezapprove, ezwaituntildate, ezapprove2, OWEditorial and xrowworkflow/hideuntildate
setups over without rebuilding them by hand.

## What Changes

- Webhooks: admin pages `expworkflow/hooks` (list, edit, test, delivery log), policy
  `expworkflow/hooks`; job `expworkflow.webhook` per matching transition; JSON payload or
  `payload_tpl`; `X-Expworkflow-Signature: sha256=…` with the secret named by `secret_ref` from
  `expworkflow.ini [Webhook]` (never in the database); https only (http on loopback for tests), no
  redirects, retries with expqueue backoff, dead letter, `expwf_delivery` per attempt.
- Importers `./console ext:expworkflow:import --from=<source> [--dry-run] [-s <siteaccess>]` for
  `ezapprove`, `ezwaituntildate`, `ezapprove2`, `oweditorial`, `xrowworkflow`/`hideuntildate`; draft
  definitions only, never removing the old setup, reporting what cannot be expressed. **Verify** each
  source's settings against its own repository first. Policy `expworkflow/import`.
- Overview lists other workflow extensions still active, each with its importer.

## Capabilities

### New Capabilities
- `workflow/webhook`: subscription, payload, signing, delivery, retries, log.
- `workflow/import`: one importer per source, dry-run, unsupported-setting report.

### Modified Capabilities
- `workflow/overview`: list of replaceable extensions.

## Impact

Prior art read for ideas only where the fork states no license (section 7.3); code is written anew.

## Out of scope

Removing old setups automatically (section 11).
