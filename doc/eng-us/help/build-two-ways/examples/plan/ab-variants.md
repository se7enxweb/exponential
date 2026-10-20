---
title: "Plan A/B variants of a page"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1018
summary: The Query parameter condition exists, but the resolver remembers its answer per path, so one address cannot show two layouts at once; plan variants on two addresses.
keywords: A/B test, variant, query parameter, resolver cache, two URLs, experiment
type: help
---
# Plan A/B variants of a page

Compare two versions of a landing page.

*Life cycle: Plan, example 19 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Tested on a copy: a mapping *Path prefix `showcase` + Query parameter `variant`* (priority 3000) and the regular Showcase mapping. Whichever address was requested first decided the layout of both `/showcase` and `/showcase?variant=b` for the next hour (114 blocks either way, or 5 either way).
2. So plan two pages with their own address (for example `/offer-a` and `/offer-b`), each with a Location or Path mapping to its own layout, and split the traffic outside (campaign links, newsletter halves).

## With templates

1. Two full view templates, chosen by `override.ini` per node or remote ID, are the template way to the same.

## Which to choose

Separate addresses keep page caches and the resolver correct.

## Check

- Each variant address shows its own layout in a private window.

## Related pages

- [Which layout does a page get?](../../layout-mappings.md)
- [Known editor behaviors](../../known-editor-behaviors.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
