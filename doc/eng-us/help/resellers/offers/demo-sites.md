---
title: Demo sites for resellers
audience: reseller, partner, hosting, sales-marketing
category: reselling
order: 250
summary: How a reseller runs demo sites for prospects - a private demo you control, or a public demo with a published sign-in in demo mode (the environment variable EXP_DEMO_MODE=1), choosing the site package, rebuilding on a schedule, what demo mode does and does not protect, and a demo site checklist.
keywords: demo, demo site, public demo, demo mode, EXP_DEMO_MODE, published password, sevenx_multisite, site package, rebuild, prospects, sales demo, trial
type: help
---
# Demo sites for resellers

This page is for sales staff and system administrators at resellers who need a site to show prospects. A demo you
control wins more deals than screenshots. There are two kinds, and they need different care.

## Contents

1. [Private demo or public demo](#private-demo-or-public-demo)
2. [Building a demo](#building-a-demo)
3. [Demo mode for a public demo](#demo-mode-for-a-public-demo)
4. [What demo mode does not protect](#what-demo-mode-does-not-protect)
5. [Demo site checklist](#demo-site-checklist)

## Private demo or public demo

| | Private demo | Public demo |
|---|---|---|
| Who signs in | Your sales staff, or one prospect with a personal account | Anyone, with a published administrator sign-in |
| Password | Secret, strong | Published |
| Demo mode | Off | On: `EXP_DEMO_MODE=1` |
| Rebuild | Before important demos | On a schedule, every few hours |
| Use for | Guided demos, tailored to a prospect | "Try it yourself" on your website |

## Building a demo

1. Choose a site package with demo content: `sevenx_multisite` ("7x MultiSite Default Installation") or `sevenx_site`;
   the `_clean` variants have no demo articles and images
   ([Site packages for hosts](../../hosting/provisioning/site-packages.md)).
2. Install it on its own system user, pool and database, never next to customer sites
   ([Install in one command](../../../features/6.0/install-in-one-command.md)).
3. Add your own touches: your logo in the design, a page about your services, an example from the prospect's sector.
4. Prepare the talk track: [Demo scripts](../../business/demo-scripts.md) has 5, 15 and 45 minute demonstrations
   with a setup checklist and what can go wrong.
5. For a press or public demo, see [Setting up a demo installation](../../press/setting-up-a-demo-installation.md).

## Demo mode for a public demo

A public demo publishes its administrator sign-in so anyone can try the administration. **Demo mode** keeps it
usable for the next visitor: nobody can change or reset the password of an administrator
([Demo mode](../../demo-mode.md)).

| Rule | Detail |
|---|---|
| How it is switched on | Only by the environment variable `EXP_DEMO_MODE=1` for the demo's PHP processes; no INI setting, no request parameter, no administration page |
| Only the exact value `1` | `0`, `true`, `yes`, `on` and an empty value leave it off |
| Every process | Web PHP, Exponential Velocity, cron and console commands each need it |
| What visitors see | A yellow **Demo mode** banner on every page of the administration |
| Installing | With the variable set, the installers accept the published demo password (`publish` or `publishing`) for the new administrator |
| Checking | `demo/mode` lines in `var/log/security.log`, `system.demo.mode` audit events |

How to set it for PHP-FPM, Apache, LiteSpeed, Velocity, cron and containers:
[Demo mode for hosts](../../hosting/security/demo-mode-for-hosts.md). Never set it server-wide, in a provisioning
template or anywhere a customer site could inherit it.

## What demo mode does not protect

- Demo mode only protects administrator passwords. A visitor signed in as an administrator can still create accounts,
  change roles and settings, install packages and upload files. Rebuild the demo on a schedule.
- Other published sign-ins (an editor, a member) are not protected.
- Keep real data, real mail addresses and secrets out of the demo, and send its mail to a file or a sink.
- If the variable is lost after a restart, demo mode is off and the only sign is the missing banner.

Source: [Demo mode for hosts](../../hosting/security/demo-mode-for-hosts.md#risks-to-tell-the-demos-owner). Answers to
common questions: [Demo mode FAQ](../../demo-mode-faq.md).

## Demo site checklist

- [ ] Own system user, pool and database; no customer data.
- [ ] Private demo: strong password, demo mode off. Public demo: `EXP_DEMO_MODE=1` for every process.
- [ ] Banner visible on the administration of a public demo.
- [ ] Rebuild scheduled; the build script does not write the variable into `config.php` or `settings/`.
- [ ] Mail goes to a file or a sink.
- [ ] Production machines checked: `grep -r EXP_DEMO_MODE` over pools, virtual hosts, service units and crontabs
      finds nothing.
- [ ] Talk track and recovery steps ready ([Demo scripts](../../business/demo-scripts.md)).

## Related pages

- [Demo mode](../../demo-mode.md), [Demo mode FAQ](../../demo-mode-faq.md)
- [Demo mode problems](../../hosting/runbooks/demo-mode-problems.md)
- [Sales process for resellers](../sales/sales-process.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
