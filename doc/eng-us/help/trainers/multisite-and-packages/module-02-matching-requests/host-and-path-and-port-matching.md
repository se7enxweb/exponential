---
title: "Lesson 2.3: Host and path together, and matching by port"
audience: trainer, admin, developer
category: training
order: 23
summary: Learners map a host and a path segment to a siteaccess with HostUriMatchMapItems, order the entries correctly, and read how port matching and the installers' port defaults work. 50 minutes.
keywords: multisite and site packages, host_uri, HostUriMatchMapItems, HostUriMatchMethodDefault, port matching, PortAccessSettings, AccessPort, AdminAccessPort, EditorAccessPort, servervar, ordering
type: help
---
# Lesson 2.3: Host and path together, and matching by port

A brand with several languages usually keeps one host and gives each language a path: `www.example.com/de` and
`www.example.com/en`. The `host_uri` probe matches host and path together. This lesson sets it up for the second site
of the lab and then looks at the port probe, which installers use for local and container setups.

## At a glance

| | |
|---|---|
| Course | [Multisite and site packages](../README.md) |
| Module | [Module 2: Matching requests to siteaccesses](README.md) |
| Duration | 50 minutes (20 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Administrators and developers |
| Prerequisites | [Lesson 2.2](matching-by-host-name.md): `MatchOrder` contains `host`, `curl` is at hand |

## Learning objectives

After this lesson, learners can:

- write `HostUriMatchMapItems[]` entries (`host;uri;siteaccess[;method]`) and add `host_uri` to `MatchOrder`;
- order entries from the most specific to entries with an empty path;
- explain the port probe and `[PortAccessSettings]`;
- name the port, host and path defaults that `exp:install` and the kickstarter write for each access type.

## Trainer's notes

### How to present it

Sources: the comments of `settings/site.ini [SiteAccessSettings]` and `[PortAccessSettings]`,
[Siteaccess by host prefix](../../../../features/6.0/siteaccess-host-and-language-matching.md) and
[7.8 The editor siteaccess with exp:install](../../../../install/07-console-install.md#78-the-editor-siteaccess-with-expinstall).

1. **One entry, three fields** (5 minutes). `HostUriMatchMapItems[]=www.example.com;de;site_de` maps the host and
   the first path element to a siteaccess. A fourth field chooses the host method (`strict`, `start`, `end`,
   `part`); without it, `HostUriMatchMethodDefault` applies (`strict` as shipped). An empty path field matches the
   host alone, so `host_uri` can do pure host matching too; an empty host is not possible.
2. **Order** (5 minutes). The kernel searches `HostUriMatchMapItems[]` from top to bottom. The shipped comment says:
   place more specific entries at the top and entries with empty path parts at the bottom. Show what goes wrong in
   the other order: the entry without a path catches `/de` as well.
3. **The static cache** (2 minutes). Only `strict` matching is supported by the static cache for `host_uri`, because
   its rewrite rules need a strict host and path. The static cache is off by default on new installations.
4. **The port probe** (5 minutes). `[PortAccessSettings]` holds `port=siteaccess` lines (`settings/site.ini` ships
   `1337=user` and `1338=admin` as examples). The installers' port access type writes them: site `8080`, admin
   `8081`, editor `8082` by default (`--port`, `--admin-port`, `--editor-port` of `exp:install`).
5. **`servervar`** (3 minutes). The probe reads a server variable (`ServerVariableName`, `SITEACCESS` as shipped)
   that the web server sets. It is the choice when the web server already knows which site a request is for.

### Common questions

**When `host_uri` and when `host;uri`?** `host_uri` when the same path segment means different siteaccesses on
different hosts (`/de` on two brands). `host;uri` is enough when every siteaccess has its own host, or its own
unique path.

**Is port matching good for production?** The hosting guide recommends it for local work; production sites are
reached on 80 and 443 and use host or path matching.

**Can I mix probes?** Yes, `MatchOrder` takes any list, for example `uri;host;host_uri`. The first that finds an
available siteaccess wins.

**What happens to `/` on `www.example.com` with only `/de` and `/en` entries?** No probe matches, so the default
siteaccess answers, or the redirect to the language of lesson 2.4 sends the browser to a language.

### Pitfalls

- **Wrong order of entries.** An entry with an empty path above the specific ones catches every address of its host.
- **Overlapping probes.** With `uri` first, a path element that is a siteaccess name (`/site_b_ger`) is matched by
  the URI probe before `host_uri` is asked. Use segment names that are not siteaccess names (`de`, `en`) for
  `host_uri`.
- **Ports in containers.** A port mapped by a container (`8080` outside, `80` inside) is not the port PHP sees;
  match the port inside.

## Hands-on exercise

Use your training installation from lesson 2.2. Replace `http://127.0.0.1:8087` with your lab's address.

1. Add the `host_uri` probe at the end of the order:
   `php bin/php/console exp:ini set site.ini/SiteAccessSettings/MatchOrder 'uri;host;host_uri' global`.
2. Add two entries for one host, the specific one first:

   ```bash
   php bin/php/console exp:ini add 'site.ini/SiteAccessSettings/HostUriMatchMapItems[]' 'club.localhost;de;site_b_ger' global
   php bin/php/console exp:ini add 'site.ini/SiteAccessSettings/HostUriMatchMapItems[]' 'club.localhost;;site_b' global
   php bin/php/ezcache.php --clear-tag=ini
   ```

3. Test:

   ```bash
   curl -s -H 'Host: club.localhost' http://127.0.0.1:8087/ | grep -i '<title>'
   curl -s -H 'Host: club.localhost' http://127.0.0.1:8087/de | grep -i '<title>'
   ```

4. Read the entries in their order: `php bin/php/console exp:ini list site.ini/SiteAccessSettings global` and find
   `HostUriMatchMapItems`.
5. Swap the order: remove both entries with `exp:ini rem` and add them again with the empty-path entry first. Clear
   the INI cache and repeat step 3. Note what `/de` shows now.
6. Put the entries back in the right order and clear the INI cache.
7. Read the port examples the kernel ships: `php bin/php/console exp:ini list site.ini/PortAccessSettings default`.
8. Print what `exp:install` would write for port matching, without installing anything:

   ```bash
   php bin/php/console exp:install --print --access=port --port=8080 --admin-port=8081
   ```

   Find `Access`, `AccessPort`, `AdminAccessPort` and `EditorAccessPort` in the output.

## Expected result

- Step 3: `/` on `club.localhost` shows *Site B*, `/de` shows *Seite B* (the German siteaccess of lesson 1.4).
- Step 5: with the empty-path entry first, `/de` also lands in `site_b` (as the page `de`, usually "not found").
- Step 8 prints the configuration with `Access=port` and the ports `8080`, `8081` and `8082`, and installs nothing.
- The trainer checks by asking a learner to explain the result of step 5 with the shipped comment about ordering.

## Quiz

1. Write an entry that sends `www.example.com/en` to `site_en`.
2. Which entry must come last?
   A. `example.com;nor;example_nor` B. `example.com;;example` C. `example.com;eng;example_eng;start` D. any order
3. Which host method is the only one the static cache supports?
4. Where are port-to-siteaccess entries kept?
5. With `exp:install --access=port` and no other port option, which port does the editor get?

<details>
<summary>Answers</summary>

1. `HostUriMatchMapItems[]=www.example.com;en;site_en`, with `host_uri` in `MatchOrder`.
2. **B.** Entries with an empty path part go to the bottom, because the list is searched from the top
   (`settings/site.ini`).
3. `strict`.
4. In `site.ini [PortAccessSettings]`, one `port=siteaccess` line each.
5. `8082`, the public port plus 2
   ([7.8](../../../../install/07-console-install.md#78-the-editor-siteaccess-with-expinstall)).

</details>

## Further reading

- [Siteaccess by host prefix, and the redirect to the language](../../../../features/6.0/siteaccess-host-and-language-matching.md).
- [7. The console install](../../../../install/07-console-install.md#73-options): the access options.
- [Language switchers and addresses per language](../../../creators/website-creators/language-switcher-and-language-urls.md).
- [Static cache and Velocity](../../../creators/website-creators/static-cache-and-velocity.md).

## Related pages

- [Module 2: Matching requests to siteaccesses](README.md)
- [Multisite and site packages: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
