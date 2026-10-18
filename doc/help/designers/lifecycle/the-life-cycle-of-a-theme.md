---
title: "The life cycle of a theme"
audience: designer, project-manager
category: theme-lifecycle
order: 20
summary: The stages a theme passes through - development, first release, active maintenance, security-only maintenance, end of life, archive or handover - what each stage promises users, and how to announce a change of stage.
keywords: life cycle, stages, active maintenance, security only, end of life, archive, handover, support status
type: help
---
# The life cycle of a theme

This page is for designers planning how long and how intensively they will maintain a theme. Users plan their sites
around your theme; telling them which stage it is in is part of the work.

## The stages

| Stage | You do | Users can expect |
|---|---|---|
| Development | Build, no stable release | Nothing yet; `dev-main` only |
| Active maintenance | New features, compatibility, fixes, audits | Regular releases |
| Security-only maintenance | Security and critical compatibility fixes | Releases only when needed |
| End of life | Nothing new | The last release stays installable |
| Archived or handed over | The repository is read-only, or someone else maintains it | A pointer to the successor, if any |

## Step by step: state the stage

1. Add a "Status" line near the top of the README: "Status: actively maintained" (or another stage).
2. Keep the support policy current ([A support policy for your theme](support-policy-for-your-theme.md)).
3. When the stage changes, say so in a release note, the README and the project page on
   projects.exponential.earth ([Announcing end of life](announcing-end-of-life.md)).

## Real examples

The themes 7x maintains are in active maintenance: their release lists show releases in close succession in October
2026, for example `cjw_themes_jumper` 0.1.2 to 0.1.6 between 11 and 17 October, covering Open Graph tags, a security
fix, accessibility and the removal of inline scripts.

## Related pages

- [End of life](end-of-life.md)
- [Handing a theme over](handover-to-a-new-maintainer.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
