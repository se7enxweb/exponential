---
title: "Plan to measure the work"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1029
summary: Count the steps and time the waiting parts of typical changes, as on these pages, to compare the two ways on your own site.
keywords: measure, steps, timing, comparison, method
type: help
---
# Plan to measure the work

Decisions between the two ways based on your own numbers.

*Life cycle: Plan, example 30 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Count actions in the administration: clicks, choices, typed entries.

## On the command line

1. Time commands with the shell clock (`date +%s%N` before and after) and pages with `curl -w '%{time_total}'`; use `--dry-run` on live installations.

## Which to choose

Repeat each measurement three times, as in [Measured steps and times](../../measured-steps-and-times.md).

## Check

- Each measured task has steps and times written down.

## Related pages

- [Measured steps and times](../../measured-steps-and-times.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
