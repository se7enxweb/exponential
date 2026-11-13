---
title: DDoS and probe floods
audience: government, admin, hosting
category: public-sector
order: 80
summary: Runbook for a public website under a flood of requests - telling probes from real load with security.log and the error digest, what Exponential does and does not block, keeping the site up with caches, request rules and the request shield in front of the kernel (part of the default installation since 6.0.15), and working with your network provider.
keywords: DDoS, denial of service, probe flood, bot traffic, scanner, security.log, threshold, 404 rate, request-shield, exp_request_shield, Setup > Request shield, WAF, request rules, static cache, Velocity response cache, rate limiting, incident
type: help
---
# DDoS and probe floods

This runbook is for administrators when a public website is slowed or overwhelmed by requests: an automated scanner,
a botnet, or a burst of interest after a public announcement (which is not an attack and must not be blocked).

## Contents
1. [What Exponential does and does not do](#what-exponential-does-and-does-not-do)
2. [Look at the traffic](#look-at-the-traffic)
3. [Keep the site up](#keep-the-site-up)
4. [The request shield](#the-request-shield)
5. [Block in front](#block-in-front)
6. [Large attacks](#large-attacks)
7. [Related pages](#related-pages)

## What Exponential does and does not do

- The request classifier **classifies** probes, injections, traversal, scanners and 404 bursts into `security.log`;
  it **blocks nothing** ([Request logs](../../../../features/6.0/request-logs.md)).
- Request rules (`requestrules.ini`) can answer chosen requests early, for example "not found"
  ([Request rules](../../../../features/6.0/request-rules.md)).
- The request shield (`exp_request_shield`, part of the default installation since 6.0.15) **blocks** before the
  kernel starts: it refuses scanners and system URLs, slows floods with a browser check and bans repeat offenders for
  a while ([The request shield](#the-request-shield)).
- Bandwidth and volumetric attacks are the job of what is in front of the server: the web server, a reverse proxy, a
  CDN or your network provider ([Security hardening, request-shield](../../../security-hardening.md#a-shield-in-front-request-shield)).

## Look at the traffic

```bash
./console exp:logs --tail=var/log/security.log --lines=100
./console exp:logs --search='threshold/404-rate' --file=var/log/security.log
```

**Setup > Log files**, **Error digest**, log "Security (request classifier)", groups the lines by category and rule;
`404.log` grouped by reason shows what is being asked for. The web server's access log (or Velocity's
`var/vc/qbix/log/site-access.log`) shows request rates per client. Masked addresses in Exponential's logs are enough to
see patterns; your network team has the full addresses in their own logs.

| Pattern | Likely |
|---|---|
| Many `probe/*` and 404s from few networks | a scanner; harmless if nothing answers 200 |
| `injection/*` with status 200 | look closer with a developer |
| Real pages, many clients, after an announcement | genuine load: scale and cache, do not block |
| Huge volume of anything | a denial-of-service attack: your provider |

## Keep the site up

- **Caches in front of PHP**: under Velocity the response cache serves pages without PHP
  ([Velocity response cache](../../../../features/6.0/velocity-response-cache.md)); the static cache lets the web
  server answer public pages as files ([Static cache](../availability/static-cache.md)).
- **Warm** the most visited pages after a cache clear: `php bin/php/preload.php --apply --siteaccess=site --allow-root-user`.
- **Maintenance mode** with a short message is a last resort for a site that cannot cope; it answers before settings
  or the database are touched.

## The request shield

Since Exponential 6.0.15 every new installation has request-shield (by the CJW Network) in front of the kernel, through
the extension `exp_request_shield` and one hook line in `config.php`; an upgraded installation has it once that line is
added ([Upgrading to the request shield](../../../../bc/6.0/request-shield.md)). It runs before the HTTP cache's early
exit and answers the same under PHP-FPM and Velocity, and it **enforces as installed**:

- scanners, internal files, system URLs and stray form posts are refused, at a few microseconds each;
- past 120 pages a minute a client gets the invisible browser check, and ten refusals in ten minutes keep its address
  out for an hour (verified search engines are never banned);
- the sign-in and the administration's forms ask for the browser check.

During a flood:

1. Open **Setup > Request shield**: the dashboard shows the decisions per hour and the rules that decided most; the
   **Log** page filters by period and decision. `var/request-shield/log/shield.log` is the same log on disk.
2. If real visitors are refused (an announcement, a page alias that looks like a system URL), switch to **Monitor**
   while you look, add an exception to the site's rule file in `settings/request-shield/`, check it with
   `php extension/exp_request_shield/bin/request-shield check settings/request-shield/request-shield.rules`, and go
   back to **Enforce**.
3. A network that keeps coming back can be refused with one more rule in the same file; the servers read a changed
   rule file within seconds, without a restart.
4. Never take the shield out during an attack. If it must stop at once, create `var/request-shield/PAUSED` (or press
   **Pause**) and remove the file afterwards.

Details: [Request shield](../../../../features/6.0/request-shield.md),
[Running the request shield](../../../../guides/request-shield.md).

## Block in front

What the shield cannot hold, give to whoever runs the layer in front: paths that only scanners ask for, user agents of
attack tools, networks with floods. A flood that fills the line never reaches PHP, so block it at the edge.

## Large attacks

Volumetric attacks are handled by network providers and their protection services. Know before an incident who your
provider's contact is and what protection your contract includes; government networks often have a central service
for this. Report the attack to your security team or national authority as your rules require.

## Related pages
- [Request logs: 404 and security](../monitoring/request-logs-404-and-security.md)
- [Capacity planning](../availability/capacity-planning.md)
- [Slow site (hosting runbook)](../../../hosting/runbooks/slow-site.md)
- [Request shield and request rules (hosting)](../../../hosting/security/request-shield-and-request-rules.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
