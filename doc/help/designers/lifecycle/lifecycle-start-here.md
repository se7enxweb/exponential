---
title: "The maintenance life cycle: start here"
audience: designer, developer
category: theme-lifecycle
order: 10
featured: true
summary: The map for keeping a theme alive after its first release - following Exponential releases, upgrade testing, per-release accessibility, CSP and performance audits, security fixes, deprecation, end of life, handover, forking, rebranding and contributing back.
keywords: maintenance, life cycle, compatibility, upgrade testing, audits, security, deprecation, end of life, handover, fork, contribute back
type: help
---
# The maintenance life cycle: start here

This section is for designers who have released a theme and now carry it. A theme lives as long as Exponential, PHP and
browsers keep moving; each of them changes something every year. Maintenance is what keeps your users' sites working.

## The cycle in one picture

1. **Watch** what changes: Exponential releases, PHP versions, libraries, browsers.
2. **Test** your theme against each change in an upgrade test environment.
3. **Audit** the release: accessibility, Content Security Policy, look in light and dark and at zoom, performance.
4. **Fix and release** a new version, security fixes first.
5. **Communicate**: changelog, release notes, deprecations, and one day the end of life or a handover.

## A reading order

1. [The life cycle of a theme](the-life-cycle-of-a-theme.md), [A support policy for your theme](support-policy-for-your-theme.md).
2. Compatibility: [Following Exponential releases](following-exponential-releases.md),
   [Reading a changelog as a designer](reading-a-changelog-as-a-designer.md), [A compatibility matrix](compatibility-matrix.md),
   [PHP version compatibility](php-version-compatibility.md).
3. Testing: [An upgrade test environment](upgrade-test-environment.md), [Upgrade testing, step by step](upgrade-test-step-by-step.md).
4. Audits: [Accessibility](accessibility-audit-per-release.md), [CSP](csp-audit-per-release.md),
   [Visual regression](visual-regression-checks.md), [Performance](performance-audit-per-release.md).
5. Security: [Security fixes in themes](security-fixes-in-themes.md), [Handling security reports](handling-security-reports.md),
   [Publishing a security release](publishing-a-security-release.md).
6. Change and endings: [A deprecation policy](deprecation-policy.md), [Breaking changes](breaking-changes.md),
   [End of life](end-of-life.md), [Handing a theme over](handover-to-a-new-maintainer.md).
7. Forks and community: [Forking a theme](forking-a-theme.md), [Rebranding a fork, fairly](rebranding-a-fork.md),
   [Contributing back upstream](contributing-back-upstream.md).

## Every page of this section

| Page | What it answers |
|---|---|
| [The life cycle of a theme](the-life-cycle-of-a-theme.md) | The stages from first release to archive |
| [A support policy for your theme](support-policy-for-your-theme.md) | Which versions you maintain |
| [Following Exponential releases](following-exponential-releases.md) | Where changes are announced |
| [Reading a changelog as a designer](reading-a-changelog-as-a-designer.md) | What matters to a theme |
| [A compatibility matrix](compatibility-matrix.md) | Theme versions against Exponential and PHP |
| [PHP version compatibility](php-version-compatibility.md) | PHP 8.0 and later |
| [Declaring compatibility](declaring-compatibility.md) | Where users read it |
| [An upgrade test environment](upgrade-test-environment.md) | A safe place to test |
| [Upgrade testing, step by step](upgrade-test-step-by-step.md) | The routine |
| [Comparing your overrides with the standard templates](comparing-overrides-with-the-standard.md) | Finding drift |
| [JavaScript library upgrades](javascript-library-upgrades.md) | jQuery 4, no YUI |
| [Removing inline scripts and styles](removing-inline-scripts-and-styles.md) | Getting CSP-ready |
| [An accessibility audit for every release](accessibility-audit-per-release.md) | The routine |
| [A CSP audit for every release](csp-audit-per-release.md) | The routine |
| [Visual regression checks](visual-regression-checks.md) | Light, dark, zoom |
| [A performance audit for every release](performance-audit-per-release.md) | The routine |
| [Security fixes in themes](security-fixes-in-themes.md) | Typical problems and fixes |
| [Handling security reports](handling-security-reports.md) | From report to fix |
| [Publishing a security release](publishing-a-security-release.md) | Fix, release, describe |
| [Keeping dependencies up to date](dependency-updates.md) | Libraries and extensions |
| [A deprecation policy](deprecation-policy.md) | How you retire things |
| [Deprecating a setting or a template](deprecating-a-setting-or-template.md) | Step by step |
| [Breaking changes and the next major version](breaking-changes.md) | When users must act |
| [End of life](end-of-life.md) | Stopping maintenance well |
| [Announcing end of life](announcing-end-of-life.md) | What to say |
| [Archiving a repository](archiving-a-repository.md) | Read-only, still installable |
| [Handing a theme over](handover-to-a-new-maintainer.md) | A new maintainer |
| [Transferring the repository and the Packagist package](transferring-repository-and-packagist.md) | Moving ownership |
| [Adopting an abandoned theme](adopting-an-abandoned-theme.md) | Taking over |
| [Forking a theme](forking-a-theme.md) | When and how |
| [Rebranding a fork, fairly](rebranding-a-fork.md) | Names, notices, credit |
| [Credits and attribution](credits-and-attribution.md) | Who to name |
| [Contributing back upstream](contributing-back-upstream.md) | Shared growth |
| [Pull requests to someone else's theme](pull-request-etiquette.md) | Good pull requests |
| [Keeping a fork in sync](keeping-a-fork-in-sync.md) | Upstream changes |
| [A maintenance calendar](maintenance-calendar.md) | The yearly rhythm |
| [Checklist: one maintenance cycle](lifecycle-checklist.md) | The routine as a list |

## Related pages

- [Release and upgrade planning](../../leadership/release-and-upgrade-planning.md)
- [Distributing a theme: start here](../distribution/distribution-start-here.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
