---
title: "Story: editing content"
audience: press
category: press-media
order: 51
summary: A feature story for journalists about how editors work in Exponential - drafts, versions, previews at device width, the trash and the 6.0.15 additions - with printable facts, their sources, what not to claim and headline ideas.
keywords: story, feature story, editing, editors, drafts, versions, preview, trash, content model, admin4, press, journalists
type: help
---
# Story: editing content

This page is for journalists, editors and creators who want to write about what an editor's working day looks like in
Exponential. It tells the story in one paragraph, lists what is new and when, gives facts you can print with their
source, and warns you about the claims the sources do not support.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [What is new, and when](#what-is-new-and-when)
3. [Facts you can print](#facts-you-can-print)
4. [What not to claim](#what-not-to-claim)
5. [Angles and headline ideas](#angles-and-headline-ideas)
6. [Who to ask](#who-to-ask)
7. [Related pages](#related-pages)

## The story in one paragraph

Exponential is built around a content model that the site owner defines: "As a CMS its most notable feature is its
fully customizable and extendable content model" ([README](https://github.com/se7enxweb/exponential/blob/main/README.md)). Editors never change the live page
directly. Every edit is a draft, a copy of the published version, and the site keeps showing the published version
until the editor presses **Send for publishing**; older versions are kept and can be restored
([Publishing, versions and the trash](../publishing-and-versions.md)). The 6.0.15 development line adds a preview at
phone, tablet and desktop width, background jobs for very large moves and removals, bookmark folders and a redesigned
administration ([6.0.15 changelog](../../../changelogs/6.0/6.0.15.md)).

## What is new, and when

All dates below are dates of changes in the 6.0.15 line, which is **in development and not released**. Write "in the
development line", never "released on".

| What | When it was added | Source |
|---|---|---|
| Bookmark folders, in the bookmark page, the browse dialog and the online editor | 1 and 2 October 2026 | [6.0.15 changelog, 1 and 2 October](../../../changelogs/6.0/6.0.15.md#1-and-2-october-2026), [Bookmark folders](../../features/6.0/bookmark-folders.md) |
| Content jobs: large removes, copies and moves in the background, resumable | 2 October 2026 | [Content jobs](../../features/6.0/content-jobs.md) |
| The sub-items list with 129 registered columns, presets and CSV export | 1 and 2 October 2026 | [The sub-items list](../../features/6.0/subitems-table-options.md) |
| The admin4 design, light and dark | 2 October 2026 | [The admin4 design](../../features/6.0/admin4-design.md) |
| The version preview in the width of a device | 15 October 2026 | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#15-october-2026-the-version-preview-in-the-width-of-a-device) |
| Sub items reordered by drag and drop | 15 October 2026 | [6.0.15 changelog](../../../changelogs/6.0/6.0.15.md#15-october-2026-sub-items-reordered-by-drag-and-drop) |

## Facts you can print

| Fact | Source |
|---|---|
| Every edit starts as a draft; the published version stays visible until the draft is published. | [Publishing, versions and the trash](../publishing-and-versions.md) |
| Drafts are saved automatically every few minutes while an editor works. | [Creating and editing content](../editing-content.md#the-edit-form) |
| Removed items go to the trash first and can be restored. | [Publishing, versions and the trash](../publishing-and-versions.md#remove-and-restore) |
| The preview shows a draft at iPhone (390 x 844), iPad (820 x 1180), Desktop (1280 x 800) and Large Desktop (1920 x 1080) sizes. | [The admin4 design, What you get](../../features/6.0/admin4-design.md#what-you-get) |
| Two editors cannot overwrite each other's draft: a "Possible edit conflict" page lists the existing drafts first. | [Creating and editing content](../editing-content.md#when-someone-else-is-editing) |
| Big removes, copies and moves can run as a background job that resumes where it stopped. | [Content jobs](../../features/6.0/content-jobs.md) |
| Content approval and scheduled publication are among the main features. | [README, Main Exponential features](https://github.com/se7enxweb/exponential/blob/main/README.md#main-exponential-features) |
| A required alternative text for images can be switched on per class. | [Talking points: accessibility](../advocates/talking-points-accessibility.md#the-points) |

## What not to claim

- "Editors cannot break anything." Say instead: every change is a draft first, and older versions are kept.
- "6.0.15 brings X on date Y." 6.0.15 has no release date; describe the development line.
- "Real-time collaborative editing." The sources describe drafts and an edit-conflict page, not simultaneous editing.
- Any number of users, sites or editors working with it: the sources do not count them.

## Angles and headline ideas

- The safety net: drafts, versions and the trash as an editor's insurance.
- Seeing before publishing: the preview at device widths.
- Big jobs without timeouts: background content jobs for very large subtrees.

```text
"Draft first: how Exponential keeps the live page safe while editors work"
"Preview on a phone before you press Publish"
"Moving ten thousand pages without a timeout: content jobs in Exponential"
```

## Who to ask

| Question | Ask | Where |
|---|---|---|
| How a feature works, a demonstration | 7x, the company that leads development | info@se7enx.com, or the form at <https://exponential.earth/about/contact-us> (write "Press" in the subject) |
| Logos and the press kit | the address in the press kit's README | support@exponential.earth |
| How editors use it day to day | the community, in public | <https://share.exponential.earth/forums> |

To try it yourself, install your own copy with one command ([Story: install in one command](story-install-in-one-command.md)).

## Related pages

- [Talking points for editors](../advocates/talking-points-for-editors.md)
- [Story: the online editor, TinyMCE 8](story-online-editor-tinymce-8.md)
- [Story: content jobs](story-content-jobs.md)
- [Feature stories: index](feature-stories-index.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
