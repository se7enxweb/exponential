---
title: "Lesson 2.2: Matching by host name"
audience: trainer, admin, developer
category: training
order: 22
summary: Learners give the second site a host name of its own with the host map, test it without DNS by sending the Host header, and use HostMatchMethod so that a test host reaches the same siteaccess. 55 minutes.
keywords: multisite and site packages, host matching, MatchOrder host, HostMatchType map, HostMatchMapItems, HostMatchMethod, strict, start, end, part, SiteURL, DNS, certificate, TrustedProxies
type: help
---
# Lesson 2.2: Matching by host name

A real multisite gives each brand its own host name: `www.example.com` for one site, `www.example.org` for the other.
The host probe chooses the siteaccess from the host the browser asks for. This lesson sets it up on the training
installation without touching DNS, and shows the host matching methods that let a test host such as
`www.example.com.test.local` reach the live site's siteaccess.

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Module | [Module 2: Matching requests to siteaccesses](README.md) |
| Duration | 55 minutes (20 presenting, 35 exercise) |
| Level | Intermediate |
| Audience | Administrators and developers |
| Prerequisites | [Lesson 2.1](matching-by-url-path.md); `curl` on the learner's computer or the lab server |

## Learning objectives

After this lesson, learners can:

- add the host probe to `MatchOrder` and map a host to a siteaccess with `HostMatchMapItems[]`;
- test host matching with `curl` and the `Host` header before any DNS entry exists;
- choose a host matching method (`strict`, `start`, `end`, `part`) for the whole map or for one entry;
- list what else a new host needs: `SiteURL`, DNS, the web server or Velocity, the certificate.

## Trainer's notes

### How to present it

Sources: [Siteaccess by host prefix, and the redirect to the language](../../../../features/6.0/siteaccess-host-and-language-matching.md)
and [Multisite on one installation](../../../hosting/provisioning/multisite-on-one-installation.md#matching-by-url-host-or-port).

1. **The host map** (5 minutes). Show the example:

   ```ini
   # settings/override/site.ini.append.php
   [SiteAccessSettings]
   MatchOrder=host;uri
   HostMatchType=map
   HostMatchMapItems[]
   HostMatchMapItems[]=www.example.com;site
   HostMatchMapItems[]=www.example.org;site_org
   HostMatchMapItems[]=admin.example.com;admin
   ```

   `HostMatchType` also knows `element`, `text` and `regexp`; the map is what multisites use.
2. **How hosts are compared** (5 minutes). Every method compares host names as DNS does: without regard to case and
   to a trailing dot, without the port of the request unless the entry names one, and a name written in Unicode as
   the ASCII form a browser sends (when PHP has the intl extension).
3. **The methods** (5 minutes):

   | Method | Matches when the host ... | Example for `www.example.com` |
   |---|---|---|
   | `strict` | is the listed host (default) | `www.example.com` |
   | `start` | begins with it | `www.example.com.test.local` |
   | `end` | ends with it | `preview.www.example.com` |
   | `part` | contains it | `a.www.example.com.b` |

   `HostMatchMethod` sets it for the whole map; a third field sets it for one entry
   (`HostMatchMapItems[]=admin.example.com;admin;start`). `start` and `part` also take hosts you did not mean, so use
   them only where the web server answers for your own hosts. The static cache only knows `strict`.
4. **Beyond the setting** (5 minutes). Each siteaccess sets its own `[SiteSettings] SiteURL`. The host needs a DNS
   entry, a place in the web server or Velocity configuration and in the certificate. Behind a proxy or load
   balancer, list it in `site.ini [HTTPHeaderSettings] TrustedProxies[]`, or the site sees the proxy's scheme and
   address.

### Common questions

**Does the order of `MatchOrder` matter here?** Yes. With `uri;host`, an address whose first element is a siteaccess
name (`/admin`) is matched by the URI probe on every host; everything else falls through to the host map. With
`host;uri`, a listed host always wins.

**Do I need one map entry per language?** With host matching alone, yes, one host per siteaccess. For one host with
a path per language, use `host_uri` (lesson 2.3).

**Can the port be part of an entry?** Yes, an entry may name one (`example.com:8080`); otherwise the port of the
request is ignored.

**Why `start` for test systems?** So that `www.example.com.test.local` reaches the same siteaccess as
`www.example.com` without a second entry per test host.

### Pitfalls

- **Shared lab.** The host map and `MatchOrder` live in the global override. On a shared training server, let one
  person change them on the projector instead.
- **Browsers and `.localhost`.** Many current browsers send names ending in `.localhost` to the own computer; if a
  learner's does not, use `curl` only.
- **Forgetting the INI cache.** Each change needs `--clear-tag=ini` (and a Velocity restart when Velocity serves the
  lab).

## Hands-on exercise

Use your training installation from lesson 2.1. Replace `http://127.0.0.1:8087` with your lab's address.

1. Read the current probes: `php bin/php/console exp:ini get site.ini/SiteAccessSettings/MatchOrder`.
2. Add the host probe after the URI probe, keeping the URI probe first:

   ```bash
   php bin/php/console exp:ini set site.ini/SiteAccessSettings/MatchOrder 'uri;host' global --dry-run
   php bin/php/console exp:ini set site.ini/SiteAccessSettings/MatchOrder 'uri;host' global
   ```

3. Map a host to the second site:

   ```bash
   php bin/php/console exp:ini add 'site.ini/SiteAccessSettings/HostMatchMapItems[]' 'site-b.localhost;site_b' global
   php bin/php/ezcache.php --clear-tag=ini
   ```

4. Test without DNS:

   ```bash
   curl -s -H 'Host: site-b.localhost' http://127.0.0.1:8087/ | grep -i '<title>'
   curl -s http://127.0.0.1:8087/ | grep -i '<title>'
   ```

5. Try a test host: `curl -s -H 'Host: site-b.localhost.training' http://127.0.0.1:8087/ | grep -i '<title>'`. Note
   which site answers.
6. Make the entry match by the start of the host:

   ```bash
   php bin/php/console exp:ini rem 'site.ini/SiteAccessSettings/HostMatchMapItems[]' 'site-b.localhost;site_b' global
   php bin/php/console exp:ini add 'site.ini/SiteAccessSettings/HostMatchMapItems[]' 'site-b.localhost;site_b;start' global
   php bin/php/ezcache.php --clear-tag=ini
   ```

7. Repeat step 5.
8. Repeat step 4 with the header `Host: SITE-B.localhost` and with `Host: site-b.localhost:8087`.
9. If your browser opens `http://site-b.localhost:8087/`, open it and compare with the main site.
10. Write down the three things a real host would also need before going live.

## Expected result

- Step 4: the first command prints the title of *Site B*, the second the title of the main site.
- Step 5 answers with the default siteaccess; after step 6, step 7 answers with *Site B*.
- Step 8: both variants reach *Site B*, because case and the request's port are ignored.
- Step 10: a DNS entry, the web server or Velocity configuration, and the certificate (plus `SiteURL`).
- The trainer checks by running step 7 on one learner's lab on the projector.

## Quiz

1. Which setting maps `www.example.org` to the siteaccess `site_org`?
2. An entry is `www.example.com;site;start`. Which host does it match?
   A. `preview.www.example.com` B. `www.example.com.test.local` C. `example.com` D. `www.example.org`
3. Why is `part` risky on a server that answers for any host?
4. A browser asks for `WWW.Example.com.` (capitals and a trailing dot). Does a `strict` entry for
   `www.example.com` match?
5. Name two things besides the host map that a new host name needs.

<details>
<summary>Answers</summary>

1. `HostMatchMapItems[]=www.example.org;site_org` in `[SiteAccessSettings]`, with `HostMatchType=map` and `host` in
   `MatchOrder`.
2. **B.** `start` matches a host that begins with the entry
   ([Siteaccess by host prefix](../../../../features/6.0/siteaccess-host-and-language-matching.md#the-host-map-by-the-start-of-a-host)).
3. It also takes hosts you did not mean (`a.www.example.com.b`); use it only where the web server answers for your own
   hosts.
4. Yes: hosts are compared without regard to case and to the trailing dot.
5. Any two of: `SiteURL` of the siteaccess, a DNS entry, the web server or Velocity configuration, the certificate,
   `TrustedProxies[]` behind a proxy.

</details>

## Further reading

- [Siteaccess by host prefix, and the redirect to the language](../../../../features/6.0/siteaccess-host-and-language-matching.md).
- [Multisite on one installation](../../../hosting/provisioning/multisite-on-one-installation.md): host matching for
  hosts.
- [Going live: hosts, HTTPS and siteaccesses](../../../creators/website-creators/going-live-hosts-https.md).
- [8. Serving the site](../../../../install/08-serving-the-site.md): web server and Velocity configuration.

## Related pages

- [Module 2: Matching requests to siteaccesses](README.md)
- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
