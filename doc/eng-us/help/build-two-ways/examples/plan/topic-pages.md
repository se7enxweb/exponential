---
title: "Plan topic pages"
audience: designer, website-creator, editor, admin, developer
category: build-two-ways
order: 1024
summary: Topics (class Topic: Running, Workout, Health, HIIT) have the layout Full / Topic; plan what a topic page lists.
keywords: topic, ng_topic, Full / Topic, tags, related content
type: help
---
# Plan topic pages

Each topic page lists the content about it.

*Life cycle: Plan, example 25 of 35. Worked out on the Fit & Healthy site of Exponential 6.0.15+.*

## Contents

1. [In the Layouts editor](#in-the-layouts-editor)
2. [With templates](#with-templates)
3. [Which to choose](#which-to-choose)
4. [Check](#check)

## In the Layouts editor

1. Full / Topic takes `ng_topic` at priority 120 and has 7 blocks; Running (node 163) also has a landing page layout mapped by Location.
2. Topic lists can use the **Topics** query type of dynamic collections (`content_by_topic`).

## With templates

1. In templates, lists by tag use the fetch functions of the tags extension (eztags).

## Which to choose

A dynamic collection with the Topics query keeps topic pages filled without code.

## Check

- A new article tagged with a topic shows on that topic page.

## Related pages

- [Manual and dynamic collections](../../manual-and-dynamic-collections.md)
- [Plan examples, every page](README.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
