---
title: "Measure the launch pages"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1228
summary: Time the main pages cached and uncached, as on the test copy, and keep the numbers.
keywords: performance, launch, timing, curl, page cache
type: help
---
# Measure the launch pages

A baseline to compare after later changes.

*Life cycle: Launch, example 29 of 33. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [On the command line](#on-the-command-line)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Compare layouts with many blocks (the home page) and with few.

## On the command line

1. `curl -s -o /dev/null -w '%{time_total}\n' https://www.example.com/` several times; the first after a cache clear compiles templates.

## Which to choose

Write the numbers into the site's documentation.

## Check

- Cached pages answer in milliseconds.

## Related pages

- [Measured steps and times](../../measured-steps-and-times.md)
- [Launch examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
