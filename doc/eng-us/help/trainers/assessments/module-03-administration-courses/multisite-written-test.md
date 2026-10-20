---
title: "Lesson 3.5: Multisite, written test"
audience: trainer, admin, developer, hosting
category: training
order: 35
summary: A twelve-question written test for the end of the Multisite and site packages course, on siteaccesses, matching by URL, host and port, SiteURL, trusted proxies, logs and caches per site, settings per site in extensions, cross-site links and when to use separate installations, with an answer key that cites the documentation. 25 minutes for learners.
keywords: assessments and practical exercises, multisite, written test, siteaccess, MatchOrder, HostMatchMapItems, HostMatchMethod, SiteURL, TrustedProxies, VarDir, INICacheDir, ezurlmultisite, site packages
type: help
---
# Lesson 3.5: Multisite, written test

This test closes the Multisite and site packages course. Its core is the siteaccess: what it is, how a request finds
one, and what has to agree (settings, web server, DNS, certificate) for a second site to work. It also asks when a
second installation is the better choice.

> **Note**
> This is the trainer's own assessment, not an official certification.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 3: Assessing the administration courses](README.md) |
| Duration | 40 minutes (5 briefing, 25 test, 10 going through the answers) |
| Level | Advanced |
| Audience | Learners at the end of the Multisite and site packages course |
| Prerequisites | The Multisite and site packages course; [Lesson 1.2](../module-01-designing-assessments/writing-written-tests.md) for the trainer |

## Learning objectives

Learners who pass this test have shown that they can:

- define a siteaccess and name the three usual ways of matching one;
- configure and check host matching, including behind a proxy;
- keep logs, caches and INI caches apart per site;
- place settings for all siteaccesses of a site inside an extension;
- choose between one installation with several siteaccesses and several installations.

## Trainer's notes

### How to run it

1. Hand out the questions without the key; 25 minutes.
2. Go through the answers with [10.2](../../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)
   and [Multisite on one installation](../../../hosting/provisioning/multisite-on-one-installation.md) on the projector.

### Common questions

**Our course did not cover cross-site links.** Drop question 10.

### Pitfalls

- **Learners who only used URL matching.** Questions 3 to 6 need host matching; make sure the course showed it, at
  least on the projector.

## The test

1. What is a siteaccess, in one sentence with its three parts?
2. What is the shipped `MatchOrder`, and what does it mean?
3. Name one advantage of host matching over URL matching for production.
4. You set up host matching, but both host names reach the same siteaccess. What do you check in `HostMatchMapItems[]`?
5. A test host `www.example.com.test.local` should reach the siteaccess of `www.example.com`. Which setting and value
   allow it, and what is the risk?
6. The site is behind a load balancer on another machine and treats HTTPS requests as plain HTTP. Which setting is
   missing?
7. What is `[SiteSettings] SiteURL` used for?
8. Several sites share one installation. Which setting makes each site cache its INI files in its own directory?
9. An extension serves the siteaccesses `example__eng` and `example__ger`. Where do settings go that apply to both?
10. A link from site A to an article of site B comes out with site A's path in front and leads nowhere. Which
    extension fixes this?
11. Two different customers want one site each. One installation with two siteaccesses, or two installations? Why?
12. After changing siteaccess settings, which cache do you clear?

<details>
<summary>Answers</summary>

1. One way into the installation: a name, a set of settings in `settings/siteaccess/<name>/`, and a rule for which
   requests it answers ([10.2](../../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).
2. `uri;host;port`: the ways a request is matched, tried in that order
   ([10.2](../../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).
3. A host name per siteaccess suits production and HTTPS certificates per host
   ([Multisite on one installation](../../../hosting/provisioning/multisite-on-one-installation.md#matching-by-url-host-or-port)).
4. The host name the browser sends must be exactly the one listed, without a port and in lower case
   ([10.2](../../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).
5. `HostMatchMethod=start` (or the method `start` on that one entry); `start` also takes hosts you did not mean, so
   use it only where the web server answers for your own hosts only
   ([Siteaccess host and language matching](../../../../features/6.0/siteaccess-host-and-language-matching.md)).
6. The load balancer's address in `[HTTPHeaderSettings] TrustedProxies[]`
   ([10.2](../../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).
7. The site's address, used in mails, feeds, sitemaps and by `exp:benchmark`
   ([10.2](../../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).
8. `[FileSettings] INICacheDir=site`
   ([Multisite on one installation](../../../hosting/provisioning/multisite-on-one-installation.md#logs-and-caches-per-site)).
9. In the extension's `settings/override__example/` (for the group) or `settings/override/` (for every siteaccess of
   the extension) ([Per-site settings inside extensions](../../../../features/6.0/multi-site-ini-overrides.md)).
10. `ezurlmultisite` ([Multisite cross-site links](../../../../features/6.0/multisite-cross-site-links.md)).
11. Two installations: different customers need operating-system isolation, separate load and separate backups
    ([Multisite on one installation](../../../hosting/provisioning/multisite-on-one-installation.md#one-installation-or-several)).
12. The INI cache: `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`
    ([10.2](../../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)).

</details>

## Scoring

- One point per question; question 1 needs all three parts, question 5 the setting and the risk.
- Pass: 8 of 12 points or more, with questions 4 and 11 correct.

## Further reading

- [10.2 Siteaccesses and site addresses](../../../../install/10-after-installing.md#102-siteaccesses-and-site-addresses)
- [Multisite on one installation](../../../hosting/provisioning/multisite-on-one-installation.md)
- [Per-site settings inside extensions](../../../../features/6.0/multi-site-ini-overrides.md) and
  [Multisite cross-site links](../../../../features/6.0/multisite-cross-site-links.md)

## Related pages

- [Lesson 3.6: Multisite, practical task](multisite-practical-task.md)
- [Module 3: Assessing the administration courses](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
