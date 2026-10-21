---
title: Testing against a demo
audience: mobile-app-developer, app-creator, developer
category: mobile-apps
order: 810
summary: Probe any Exponential installation, a public demo included, with three anonymous requests before you build against it - what the answers mean, what a demo in demo mode allows, why a public demo may not have every API active, and what never to do on a shared demo.
keywords: demo, public demo, probe, demo mode, EXP_DEMO_MODE, REST 401, 404, expsession::whoami, ezjsc::time, demo.exponential.earth, shared demo, testing, mobile app
type: help
---
# Testing against a demo

This page is for app developers who want to try the APIs before they have an installation of their own, and for
everyone who points an app at a server for the first time. It starts with a probe that works on any installation.

## Contents

1. [Probe before you build](#probe-before-you-build)
2. [What the answers mean](#what-the-answers-mean)
3. [What a public demo allows](#what-a-public-demo-allows)
4. [Rules for shared demos](#rules-for-shared-demos)

## Probe before you build

Three anonymous requests, no credential needed:

```bash
# 1. the REST content API
curl -s -o /dev/null -w '%{http_code}\n' https://example.com/api/ezp/v1/content/node/2
# 2. ezjscore's call interface
curl -s 'https://example.com/ezjscore/call/ezjsc::time?ContentType=json'
# 3. the remote services
curl -s 'https://example.com/ezjscore/call/expsession::whoami?ContentType=json'
```

## What the answers mean

| Probe | Answer | Meaning |
|---|---|---|
| 1 | `401` with `WWW-Authenticate: OAuth realm='Exponential REST'` | REST is active and wants a credential (the default) |
| 1 | `404` | `ezprestapi` is not active, or the path differs |
| 1 | `200` | REST is active with authentication switched off |
| 2 | `{"error_text":"","content":...}` | ezjscore answers |
| 3 | an envelope with `"anonymous":true` | `expservices` is active |
| 3 | `error_text` "Not a valid ezjscServerRouter argument" | `expservices` is not active there |

Two examples of how this looked on 20 October 2026: the demonstration server used throughout the guides answered
probe 1 with `401` and probe 3 with the anonymous envelope; the public demo at `https://demo.exponential.earth/`
answered probe 1 with `404` and probe 3 with "Not a valid ezjscServerRouter argument". Installations change, so run
the probe yourself rather than trusting these lines.

## What a public demo allows

A public demonstration site publishes an administrator sign-in so that anyone can try the administration. Such a demo is meant to
run in **demo mode** (`EXP_DEMO_MODE=1`): nobody can change or reset an administrator's password, and the web services
refuse it too with `403`; everything else works as on any site ([Demo mode](../../demo-mode.md),
[Demo mode FAQ](../../demo-mode-faq.md)). Demos are rebuilt from
scratch on a schedule, so everything you create there disappears
([Running a demo site](../../designers/distribution/demo-sites.md)).

What that means for app work:

- Fine for reading content and for exploring the administration (classes, roles, the API keys and oAuth admin pages).
- Not a place for keys or OAuth clients you rely on: they vanish at the next rebuild, and anyone with the published
  sign-in can see the oAuth admin pages.
- Node ids can differ after a rebuild; never hard-code them from a demo.

## Rules for shared demos

- Use throwaway accounts and throwaway keys; revoke what you made when you are done.
- Do not load-test a public demo.
- Do not store personal data in it.
- Do not point a published app at a demo.

For everything beyond a first look, install your own: [A local test installation](a-local-test-installation.md).

## Related pages

- [Demo mode FAQ](../../demo-mode-faq.md)
- [Your first request](../rest-api/your-first-request.md)
- [Testing apps against Exponential](README.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
