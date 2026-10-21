---
title: Bundle worksheet
audience: bundler, partner
category: partnership
order: 500
summary: A worksheet to fill in once per bundle and review at every release - identity, base and versions, parts, licenses, installation, database and server, security, updates, support and partnership - with the page that explains each answer.
keywords: worksheet, template, bundle plan, decision record, versions, licenses, installer, database, web server, security, updates, support, checklist
type: help
---
# Bundle worksheet

This page is for bundlers who want every decision about a bundle in one place. Copy the blocks into your repository
(for example `docs/bundle.md`), fill them in and review them at each release with the
[Bundle release checklist](bundle-release-checklist.md).

## Contents

1. [Identity](#identity)
2. [Base and parts](#base-and-parts)
3. [Licenses](#licenses)
4. [Installation](#installation)
5. [Runtime](#runtime)
6. [Security](#security)
7. [Life after release](#life-after-release)

## Identity

```text
Bundle name:              <name>                     (Naming and branding a bundle)
Kind:                     container / image / panel / package / kit / site package / extensions / appliance / demo
Recipients:               <who>
Bundle version:           <version>
Maker and contact:        <your company>, <address>
Partner level / profile:  <level, profile, or none>
```

[Kinds of bundles](kinds-of-bundles.md), [Naming and branding a bundle](naming-and-branding-a-bundle.md).

## Base and parts

```text
Base:                     Exponential 6 / Legacy <line> / Nexus <line>
Exponential version:      <tag or commit>
Lock file checksum:       <sha256 of composer.lock>
Extensions added:         <list with versions>
Extensions removed:       <list with reasons>
Site package(s):          <name version checksum>
Package server:           public / mirror <URL> / shipped files
```

[Choosing the base](choosing-the-base.md), [Versions, tags and pinning](versions-tags-and-pinning.md),
[Site packages in a bundle](site-packages-in-a-bundle.md).

## Licenses

```text
Bundle license (own parts):  <SPDX>
Notice file:                 <path>
Third-party list:            <path>, generated with composer licenses on <date>
Source offer:                <URL or written offer>
Counsel reviewed:            <date>
```

[GPL obligations](gpl-obligations-when-distributing.md), [Third-party components](third-party-components-and-their-licenses.md).

## Installation

```text
Installer:                exp:install / kickstarter / setup wizard
Options or template:      <path>
Install at:               first boot / by the recipient
Already-installed test:   grep -q '^\[DatabaseSettings\]' settings/override/site.ini.append.php
Password hand-over:       <how>
Offline capable:          yes / no
```

[The three installers](the-three-installers.md), [First boot and install once](first-boot-and-install-once.md).

## Runtime

```text
PHP:                      <versions>, extensions checked with exp:velocity ext check
Database:                 <engine, version>
Web server / engine:      Velocity qbix / FrankenPHP / Apache / nginx + PHP-FPM
Site user / group:        <user> / <group>
Cron:                     <how>
Backups:                  <what, where>
```

[Database choices](database-choices-for-bundles.md), [Web server and engine choices](web-server-and-engine-choices.md),
[File permissions](file-permissions-and-var-ownership.md).

## Security

```text
File modes:               0770 / 0660
HTTPS:                    <how>
Admin access:             <URL path / host / port>, restrictions
Demo mode:                off (or: demo bundle, set for <processes>)
Secrets shipped:          none (checked on <date>)
```

[Security defaults of a bundle](security-defaults-of-a-bundle.md), [Demo mode in bundles](demo-mode-in-bundles.md).

## Life after release

```text
Update route:             <image / package / Git and Composer / ezupdate>
Database updates by:      <who>
Security release time:    <promise>
Support statement:        <path>
End of support:           <date or policy>
Upstream contributions:   <issues and pull requests sent>
```

[Updates and upgrade paths](updates-and-upgrade-paths.md), [Support boundaries](support-boundaries.md),
[Contributing fixes back](contributing-bundle-fixes-back.md).

## Related pages

- [Bundle release checklist](bundle-release-checklist.md)
- [Bundlers: start here](bundlers-start-here.md)
- [Partner application checklist](../partners/application-checklist.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
