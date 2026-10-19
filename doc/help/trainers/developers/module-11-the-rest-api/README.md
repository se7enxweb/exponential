---
title: "Module 11: The REST API"
audience: trainer, developer
category: training
order: 110
summary: The eleventh module of the Developers course. Learners map the REST interface and its settings, call it with a personal API key from curl, reading and writing content at v1 and v2, and write a REST provider of their own. 2 hours 45 minutes.
keywords: developers course, module 11, REST, API, ezprestapi, v1, v2, personal API key, apikey/create, curl, OAuth2, REST provider, rest.ini
type: help
---
# Module 11: The REST API

Apps and other systems reach Exponential through its REST interface. This module maps the interface, has learners
call it with a personal API key and write content through it, and ends with a provider of their own that serves the
extension's data under its own prefix.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Duration | 2 hours 45 minutes (with one 10-minute break) |
| Lessons | 3 |
| Level | Intermediate to advanced |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 11.1: The REST API at a glance](the-rest-api-at-a-glance.md) | 40 minutes | Read providers, routes, versions and `rest.ini` |
| [Lesson 11.2: Calling the API with a personal key](calling-the-api-with-a-key.md) | 55 minutes | Allow keys, make one, read and write with `curl`, revoke it |
| [Lesson 11.3: A REST provider of your own](a-rest-provider-of-your-own.md) | 60 minutes | Routes, view controller, controller; `/api/mysite/v1/greetings` |

## Preparing this module

- `ezprestapi` must be active on the training installations; check with `exp:ini get
  site.ini/ExtensionSettings/ActiveExtensions`.
- Each learner needs an administrator account and a test user with an e-mail address at `example.invalid`.
- Know the address and port of each learner's installation for the `curl` lines (the examples use
  `http://127.0.0.1:8087`).
- Rehearse lesson 11.3 completely on your own installation; keep the finished classes to hand out.
- Remind learners never to commit or paste keys; they revoke their keys at the end of lesson 11.2.

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 11.1 | 40 |
| Lesson 11.2 | 55 |
| Break | 10 |
| Lesson 11.3 | 60 |
| **Total** | **165** |

## Related pages

- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
