---
title: "Lesson 8.1: Thinking like an attacker"
audience: trainer, admin
category: training
order: 81
summary: Learners name the attacks every public site meets, map them to the defenses Exponential has built in, check from outside that the web server hands out no private file, and work through the hardening checklist on a training installation. 40 minutes.
keywords: site administration, security hardening, attacks, secure by default, least privilege, request classifier, security.log, request rules, htaccess, outside check, go-live checklist
type: help
---
# Lesson 8.1: Thinking like an attacker

Almost every attack on a content management system is automated: scanners probe for known files, bots try leaked
passwords, scrapers count through every address. Exponential 6 is secure by default against most of it, and the
administrator's job is to keep those defaults in force and close what is left. This lesson opens the security module
with the attacker's view and a check every administrator can run in two minutes.

> **Warning**
> **`.htaccess` slows down every request.** When `.htaccess` files are enabled (`AllowOverride` anything but `None`), let alone
> used, Apache looks for them and reads and evaluates them on every request, in every directory of the path, and
> page loading times go up drastically ([measured](../../../../guides/setup-files-from-the-console.md#measured-htaccess-against-rules-in-the-virtual-host)). The only real fix is to switch the feature off for
> the virtual host: `AllowOverride None` in the HTTPS virtual host, and the rules in the virtual host's own
> configuration as mod_rewrite rules. That is far faster in production speed tests and well worth the time it takes
> to optimize a production configuration. Switch over right away: copy the rules of the
> [example HTTPS virtual host](../../../../examples/web-servers/apache-virtual-host.md) into your virtual host (with the static cache, also
> [`staticcache_offload.conf`](../../../../examples/staticcache_offload.conf)), set `AllowOverride None`, run `apachectl configtest`, reload Apache and
> test the site. The same rules for other servers: [nginx](../../../../examples/web-servers/nginx.md), [LiteSpeed](../../../../examples/web-servers/litespeed.md),
> [Caddy](../../../../examples/web-servers/caddy.md), [IIS](../../../../examples/web-servers/iis.md), [Velocity](../../../../examples/web-servers/velocity.md) (built in); where virtual host rules go in a control
> panel: [Plesk](../../../hosting/panels/plesk.md), [cPanel](../../../hosting/panels/cpanel.md), [DirectAdmin](../../../hosting/panels/directadmin.md), [ISPConfig](../../../hosting/panels/ispconfig.md).
> **On LiteSpeed or OpenLiteSpeed,** the server's own page cache can also answer anonymous visitors and crawlers without PHP: [Page caching with LiteSpeed and OpenLiteSpeed](../../../../guides/litespeed-page-cache.md).

> **Note**
> Run the outside check only against your own training installation. Probing a site you do not run is an attack.

## At a glance

| | |
|---|---|
| Course | [Site administration](../README.md) |
| Module | [Module 8: Security](README.md) |
| Duration | 40 minutes (20 presenting, 20 exercise) |
| Level | Intermediate |
| Audience | Site administrators, security officers |
| Prerequisites | [Module 7](../module-07-logs-and-the-audit-trail/README.md); a training installation served over HTTP and a terminal |

## Learning objectives

After this lesson, learners can:

- name four kinds of automated attack on a CMS and one built-in defense against each;
- explain the principles secure by default, least privilege and privilege separation with an example each;
- check from outside that dot files, settings and logs are never handed out;
- find the request classifier's verdicts in `security.log`;
- use the hardening checklist to find what is still open on an installation.

## Trainer's notes

### How to present it

Sources: [Security hardening](../../../security-hardening.md) and
[Chapter 13: Security hardening for production](../../../../install/13-security-hardening.md), section 13.2.

1. **Who attacks** (5 minutes). Botnets and scanners (`/wp-login.php`, `/.env`, `/.git/config`), credential attackers
   (leaked pairs, default passwords), access brokers who sell one working sign-in, scrapers and AI crawlers, and
   supply-chain attackers. None of it is personal.
2. **The principles** (5 minutes). Show the principles table of the hardening page: secure by default, least
   privilege, privilege separation, minimal attack surface, audit, fix forward. For each, one thing built in and one
   thing that is the administrator's part.
3. **What the web server must never hand out** (5 minutes). Only a fixed list of asset paths is served as files;
   everything else goes to `index.php`. Dot files and scripts below the root answer 404. Never serve the installation
   with a generic "serve the file if it exists" configuration.
4. **Watching** (3 minutes). `security.log` holds the classifier's verdicts; it classifies, it does not block.
   Blocking is the job of a shield in front, the web server, or request rules, which can allow, deny, redirect,
   rewrite or log a request after its URL alias is translated.
5. **Reporting** (2 minutes). Vulnerabilities are reported privately, as the hardening page says, never in a public
   issue.

### Common questions

**Is a 200 answer for `/settings/site.ini` a leak?** Not if the body is an HTML page of Exponential. It is a leak only
if the INI text comes back. Look at the body once.

**Can we hide `/content/view/full/123` addresses?** Yes, with a request rule that redirects system URLs to their alias;
see [Request rules](../../../../features/6.0/request-rules.md). Try it on a training site first; it has an audit-only
rule to see who would be affected.

**Does Exponential block attackers?** The classifier does not; the hardening page describes the shield and the web
server as the places that block.

### Pitfalls

- **Deleting rules to make something work.** A missing image is never a reason to remove a rule from `.htaccess`.
  Find the cause.
- **Testing the wrong host.** The outside check must use the address visitors use, through the same web server and
  proxy.
- **Scaring learners.** Keep the tone factual: the point is that most defenses are already on.

## Hands-on exercise

Use your own training installation, served for example with `exp:velocity start` (see
[Serving the site](../../../../install/08-serving-the-site.md)). Replace `http://127.0.0.1:8087` with its address.

1. In a terminal, run the outside check:

   ```bash
   for p in /.git/config /.env /settings/site.ini /composer.json /kernel/classes/ezini.php /var/log/error.log; do
     printf '%-32s %s\n' "$p" "$(curl -s -o /dev/null -w '%{http_code}' http://127.0.0.1:8087$p)"
   done
   ```

2. For `/settings/site.ini`, look at the body once: `curl -s http://127.0.0.1:8087/settings/site.ini | head -5`. It
   must be HTML, not INI text.
3. Request a typical probe address: `curl -s -o /dev/null -w '%{http_code}\n' http://127.0.0.1:8087/wp-login.php`.
4. In the administration, open **Setup > Log files** and open `security.log`. Find your probe and read its category
   and rule.
5. Read the checklist at the end of [Security hardening](../../../security-hardening.md#checklist). For each line,
   mark *done*, *open* or *not applicable* for your training installation.
6. Check two of the lines yourself: is debug output off (**Setup > Ini settings** or
   `php bin/php/console exp:ini get site.ini/DebugSettings/DebugOutput`), and does
   `var/log/initial-admin-password` still exist?
7. Preview what the request rules would decide for a system URL, changing nothing:

   ```bash
   php bin/php/ezrequestrules.php -s <site> --uri=content/view/full/2
   ```

8. Share your three most important open lines with the group.

## Expected result

- Step 1 shows `404` for every path, or `200` only where the body is an Exponential page (step 2).
- Step 4 shows a line with a category such as `probe/...` for the `/wp-login.php` request.
- Step 5 gives each learner a marked checklist; on a fresh training installation the password and file lines are
  usually open.
- The trainer collects the open lines on a flip chart; they are the agenda of the rest of the module.

## Quiz

1. Which of these is not served as a file by the shipped rules?
   A. An image below `storage/original/image` B. `/.env` C. A stylesheet of a design D. `/.well-known/` files
2. What does the request classifier do with a request it names an attack?
3. Name the principle behind "CLI scripts refuse to run as root without `--allow-root-user`".
4. A colleague wants to use nginx's "serve the file if it exists, else `index.php`". What do you answer?
5. Where do you report a vulnerability you found in Exponential?

<details>
<summary>Answers</summary>

1. **B.** Dot files answer 404; `/.well-known/` is the one exception
   ([Chapter 13, 13.2](../../../../install/13-security-hardening.md#132-what-the-web-server-must-never-hand-out)).
2. It writes it to `security.log` with its category and rule; it blocks nothing.
3. Least privilege.
4. No: such a configuration hands out every file that exists, including `settings/*.ini` and the SQLite database. Use
   the server block of chapter 8.
5. Privately, as [Reporting a vulnerability](../../../security-hardening.md#reporting-a-vulnerability) says; never in
   a public issue.

</details>

## Further reading

- [Security hardening](../../../security-hardening.md): every threat with its defense.
- [Chapter 13: Security hardening for production](../../../../install/13-security-hardening.md): the
  setting-by-setting reference and go-live checklist.
- [Request rules](../../../../features/6.0/request-rules.md): allow, deny, redirect or rewrite requests.
- [Hardening for hosts](../../../hosting/security/hardening-for-hosts.md) and
  [Request shield and request rules](../../../hosting/security/request-shield-and-request-rules.md).

## Related pages

- [Module 8: Security](README.md)
- [Site administration: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
