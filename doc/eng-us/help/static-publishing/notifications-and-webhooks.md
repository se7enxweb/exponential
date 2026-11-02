---
title: "Notifications and webhooks"
audience: admin, developer
category: administration
order: 21
summary: An e-mail when a publish of a static copy fails or succeeds, and a webhook - a signed JSON post - after every publish and rollback, for chat tools, monitoring and your own scripts.
keywords: static publishing, notification, notice, e-mail, email, failure, success, webhook, JSON, signature, HMAC, X-Exp-Static-Signature, NotifyEmail, WebhookSecret, exp-static-webhook/1, monitoring
type: help
---
# Notifications and webhooks

Publishing often runs without anyone watching: after content publishes or on a schedule. A target can tell you how it
went, by e-mail and by a webhook.

## What you'll have at the end

An e-mail when a publish fails (and, if you like, when one succeeds), and a post to an address of your choice after
every publish and rollback.

## E-mail

In the wizard, step 8, **Notify**:

- **When a publish fails**: on by default.
- **When a publish succeeds**: only a publish that changed something is reported.

The e-mail goes to `[StaticPublishSettings] NotifyEmail` in `staticcache.ini`, or to the site's administrator
address (`site.ini [MailSettings] AdminEmail`) when it is empty. It names the target, the run, its state, the push,
the tag, the public address and any errors, never a token or key.

## Webhook

Enter an `https://` address in **Webhook** (plain `http://` only to the server itself). After every publish and
rollback it receives a `POST` with a JSON body:

```json
{
  "schema": "exp-static-webhook/1",
  "event": "publish.done",
  "target": "github-public",
  "name": "Public site on GitHub Pages",
  "public_url": "https://owner.github.io/my-site/",
  "run_id": "20261101-030000-1a2b3c",
  "state": "done",
  "commit": "4f1c...",
  "tag": "static/github-public/12",
  "push_state": "pushed",
  "counts": { "pages": 120, "files": 840 },
  "errors": [],
  "at": "2026-11-01T03:01:12+00:00"
}
```

`event` is `publish.done`, `publish.failed`, `rollback.done` or `rollback.failed`.

To let the receiver check that the post is yours, store a random value in Setup > Secrets and put its **name** in
`[StaticPublishSettings] WebhookSecret`. Each post then has the header
`X-Exp-Static-Signature: sha256=<hex>`, the HMAC-SHA256 of the body with that value. Only the name is in the
settings; the value never leaves the secret store, and it is not exported with packages or migrations.

A webhook or e-mail that cannot be sent is written to the debug log; it never makes a publish fail.

## Next step

[Who may publish](who-may-publish-roles-and-policies.md).
