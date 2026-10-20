---
title: "Launch on Exponential Velocity"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1223
summary: On Velocity, settings changes need exp:velocity restart (as exp:ini says); plan it into the launch steps.
keywords: Velocity, launch, restart, settings, workers
type: help
---
# Launch on Exponential Velocity

Settings changed on launch day take effect on a Velocity-served site.

*Life cycle: Launch, example 24 of 33. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Layouts publishing does not need a restart.

## On the command line

1. After `exp:ini` writes, run `./console exp:velocity restart`, as its hint says: *Velocity workers keep the settings of their warm-up*.

## Which to choose

Batch settings changes, restart once.

## Check

- The changed menu shows on the Velocity address.

## Related pages

- [Header menu troubleshooting](../../header-menu-troubleshooting.md)
- [Launch examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
