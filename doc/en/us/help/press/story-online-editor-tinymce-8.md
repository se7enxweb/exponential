---
title: "Story: the online editor, TinyMCE 8"
audience: press
category: press-media
order: 52
summary: A feature story about the rich text editor of Exponential - TinyMCE 8.9.2 as an opt-in second engine next to TinyMCE 3, identical stored XML, link checks and upload safety - with printable facts, sources and headline ideas.
keywords: story, TinyMCE 8, TinyMCE 3, online editor, ezoe, rich text, editor engine, upload from URL, link checks, press
type: help
---
# Story: the online editor, TinyMCE 8

This page is for journalists and creators who write about the rich text editor of Exponential. It explains what
changed in October 2026, what stays the same, and which facts you can print with a source.

## Contents

1. [The story in one paragraph](#the-story-in-one-paragraph)
2. [What is new, and when](#what-is-new-and-when)
3. [Facts you can print](#facts-you-can-print)
4. [What not to claim](#what-not-to-claim)
5. [Angles and headline ideas](#angles-and-headline-ideas)
6. [Who to ask](#who-to-ask)
7. [Related pages](#related-pages)

## The story in one paragraph

The online editor of Exponential got a second engine. "Exponential 6.0.15 ships TinyMCE 8.9.2 as a second engine of
the eZ Online Editor. TinyMCE 3.5.12 stays the default, and nothing changes for existing installations. The new engine
writes the same XHTML that the ezoe input handler reads, so stored XML is identical with both engines and editors can
switch back and forth" ([The online editor on TinyMCE 8](../../features/6.0/online-editor-tinymce8.md)). The story is
one of a careful upgrade: a modern editor offered next to the old one, with no content conversion and a switch below
the editor so each editor can choose. ("eZ Online Editor" is the name of the `ezoe` extension, a code identifier.)

## What is new, and when

The 6.0.15 line is in development and not released; the dates are dates of the changes.

| What | When | Source |
|---|---|---|
| TinyMCE 8.9.2 as an opt-in second engine, with the ezoe dialogs and browser tests | 2 October 2026 | [The online editor on TinyMCE 8](../../features/6.0/online-editor-tinymce8.md), [6.0.15 changelog](../../changelogs/6.0/6.0.15.md#1-and-2-october-2026) |
| The engine page `/ezoe/engine` | 11 October 2026 | [6.0.15 changelog](../../changelogs/6.0/6.0.15.md#11-october-2026-the-online-editors-engine-page) |
| The engine switch as one control | 15 October 2026 | [6.0.15 changelog](../../changelogs/6.0/6.0.15.md#15-october-2026-the-online-editors-engine-switch-is-one-control) |
| A clearer message for a dead link | 15 October 2026 | [6.0.15 changelog](../../changelogs/6.0/6.0.15.md#15-october-2026-the-online-editors-message-for-a-dead-link) |
| Online editor hardening | 16 October 2026 | [6.0.15 changelog](../../changelogs/6.0/6.0.15.md#16-october-2026-online-editor-hardening) |

## Facts you can print

| Fact | Source |
|---|---|
| TinyMCE 8.9.2 is opt-in; TinyMCE 3.5.12 stays the default. | [TinyMCE 8 feature page](../../features/6.0/online-editor-tinymce8.md) |
| Both engines store identical XML, so editors can switch back and forth. | same |
| An editor can choose the engine personally, without a settings change. | [TinyMCE 8, Try it in one minute](../../features/6.0/online-editor-tinymce8.md#try-it-in-one-minute) |
| Links to removed content and script links are checked when the text is saved. | [TinyMCE 8, Link checks](../../features/6.0/online-editor-tinymce8.md#link-checks-when-the-text-is-saved) |
| Upload refuses executable file names such as `shell.php.jpg`. | [TinyMCE 8, Security](../../features/6.0/online-editor-tinymce8.md#security) |
| "Upload from a URL" fetches only public `http` and `https` addresses, with at most 3 redirects, each checked again. | [TinyMCE 8, Upload from a URL](../../features/6.0/online-editor-tinymce8.md#upload-from-a-url) |
| A change of engine is recorded in the audit trail. | [TinyMCE 8, Security](../../features/6.0/online-editor-tinymce8.md#security) |
| The bundled TinyMCE 8.9.2 is used under the GPL (version 2 or later). | [TinyMCE 8, Tests](../../features/6.0/online-editor-tinymce8.md#tests) |

## What not to claim

- "Exponential switched to TinyMCE 8." It is opt-in; TinyMCE 3 stays the default.
- "Content has to be migrated." The sources say the stored XML is identical.
- "Exponential made TinyMCE." TinyMCE is a separate project; Exponential bundles and integrates it.
- A test count as current: the feature page gives 267 tests "when written (not re-counted for this page)". Quote it
  with that caveat or leave it out.

## Angles and headline ideas

- Upgrading without a migration: two engines, one storage format.
- Choice for editors: each person picks the editor they like.
- Security in the editor: link checks and upload rules a newsroom would care about.

```text
"Two editors, one format: Exponential adds TinyMCE 8 without touching stored content"
"Old editor or new? In Exponential, each editor decides"
"Why a rich text editor checks every link when you press Save"
```

## Who to ask

| Question | Ask | Where |
|---|---|---|
| Background and a demonstration | 7x | info@se7enx.com, or <https://exponential.earth/about/contact-us> |
| Technical detail in public | the community | <https://share.exponential.earth/forums> |
| A security concern about the editor | privately, never in public | security@se7enx.com ([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)) |

## Related pages

- [Story: editing content](story-editing-content.md)
- [ezoe and TinyMCE 8 (upgrade notes)](../../bc/6.0/ezoe-tinymce8.md)
- [Feature stories: index](feature-stories-index.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
