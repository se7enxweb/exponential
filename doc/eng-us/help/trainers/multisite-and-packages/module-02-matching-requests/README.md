---
title: "Module 2: Matching requests to siteaccesses"
audience: trainer, admin, developer
category: training
order: 20
summary: The second module of Multisite and site packages. Learners match requests to siteaccesses by URL path, host name, host and path together and port, and send visitors of the bare address to their language. 3 hours 45 minutes.
keywords: multisite and site packages, module 2, MatchOrder, uri, host, host_uri, port, HostMatchMapItems, HostMatchMethod, HostUriMatchMapItems, DefaultHostUriMatchMapItems, PathPrefix, language redirect
type: help
---
# Module 2: Matching requests to siteaccesses

A siteaccess is only useful when requests reach it. This module teaches every way the kernel matches a request to a
siteaccess, from the first part of the path to host names, host and path together, ports, and finally the redirect
that sends visitors of `example.com/` to their language. All exercises use `curl` with a `Host` header, so no DNS
entries are needed.

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Duration | 3 hours 45 minutes (with two 10-minute breaks) |
| Lessons | 4 |
| Level | Intermediate to advanced |

## Lessons

| Lesson | Duration | What learners do |
|---|---|---|
| [Lesson 2.1: Matching by URL path](matching-by-url-path.md) | 50 minutes | Predict which siteaccess answers an address; set `PathPrefix` |
| [Lesson 2.2: Matching by host name](matching-by-host-name.md) | 55 minutes | Map a host to `site_b`, test with `curl`, use `HostMatchMethod=start` |
| [Lesson 2.3: Host and path together, and matching by port](host-and-path-and-port-matching.md) | 50 minutes | Map host and path with `HostUriMatchMapItems[]`, see why order matters, print the port defaults |
| [Lesson 2.4: The redirect to the visitor's language](the-redirect-to-the-language.md) | 50 minutes | Set up `DefaultHostUriMatchMapItems[]` and read the 302 and its cache headers |

## Preparing this module

- Every learner has finished module 1: the siteaccesses `site_b` and `site_b_ger` answer at `/site_b` and
  `/site_b_ger`.
- `curl` is installed where learners work (their computer or the lab server), and they know their lab's address.
- The exercises change `MatchOrder` and the host maps in the global override. That is fine with one installation per
  learner; on a shared training server, do lessons 2.2 to 2.4 as a demonstration on the projector instead.
- Note the commands that undo each lesson (`exp:ini rem`, `exp:ini set ... MatchOrder uri`), in case a learner wants
  a clean state, or reset the lab as in
  [Lesson 2.6 of Train the trainer](../../train-the-trainer/module-02-building-the-lab/resetting-the-lab.md).
- If labs run on Exponential Velocity, show once that every settings change needs
  `php bin/php/console exp:velocity restart`.

## Timing plan

| Part | Minutes |
|---|---|
| Lesson 2.1 | 50 |
| Lesson 2.2 | 55 |
| Break | 10 |
| Lesson 2.3 | 50 |
| Break | 10 |
| Lesson 2.4 | 50 |
| **Total** | **225** |

## Related pages

- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
