---
title: "Make a redesign faster"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1431
summary: Fewer blocks per layout and cached pages: measure before and after.
keywords: performance, redesign, blocks count, measure, cache
type: help
---
# Make a redesign faster

The new home page renders no slower than the old one.

*Life cycle: Redesign, example 32 of 34. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [Measure](#measure)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Count the blocks of the layout (`grep -c 'ngl-block '` on the page); Landing Page / Home has 28.

## Measure

1. Time the first render after a template clear and the cached answer, as on [Measured steps and times](../../measured-steps-and-times.md).

## Which to choose

Measure; do not guess.

## Check

- Both numbers written down before and after.

## Related pages

- [Measured steps and times](../../measured-steps-and-times.md)
- [Redesign examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
