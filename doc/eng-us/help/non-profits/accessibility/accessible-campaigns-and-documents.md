---
title: Accessible campaigns and documents
audience: non-profit, editor, sales-marketing, government
category: non-profits
order: 30
summary: Making a non-profit's fundraising appeals, campaign landing pages, annual publications, PDFs, videos, images with text and newsletters accessible on Exponential - what to check in a Layouts campaign page, how to publish documents with an HTML alternative, captions, contrast in campaign colors, and a pre-launch campaign test.
keywords: accessible campaigns, landing pages, Layouts, PDFs, documents, annual publications, captions, video, images of text, contrast, newsletter accessibility, campaign test, fundraising appeal
type: help
---
# Accessible campaigns and documents

This page is for communications staff and fundraisers. Campaigns and documents are where accessibility most often
slips: a striking image with the message baked in, a PDF annual review straight from a design program, a video without
captions. Each excludes people you want to reach.

## Contents

1. [Campaign pages](#campaign-pages)
2. [Images with text](#images-with-text)
3. [Documents and PDFs](#documents-and-pdfs)
4. [Video and audio](#video-and-audio)
5. [Newsletters](#newsletters)
6. [Social media previews](#social-media-previews)
7. [Pre-launch campaign test](#pre-launch-campaign-test)

## Campaign pages

Campaign pages are usually built with Exponential Layouts ([Campaigns and appeals](../communications/campaigns-and-appeals.md)).
The layout's blocks are rendered by your design's templates, so the template rules apply
([Accessibility for site builders](../../creators/website-creators/accessibility-for-site-builders.md)). Check in
particular:

| Check | Why |
|---|---|
| One `h1`, headings in order across blocks | Blocks added one by one can break the order |
| Button texts say what happens ("Donate to the winter appeal") | Several "Read more" buttons are meaningless out of context |
| Campaign colors meet contrast | Brand colors on photos often fail ([Color and contrast](../../government/accessibility/color-and-contrast.md)) |
| No auto-playing carousels, or a pause control | Motion distracts and can harm |
| Works with the keyboard | Every block, every button |

## Images with text

- Put the message in real text, not in the image.
- If an image must contain text (a poster), repeat the text on the page or in the alternative text
  ([Alternative text for images](../../government/accessibility/alternative-text-for-images.md)).
- Infographics need a text version: a short summary and the key figures in a list or table.

## Documents and PDFs

Annual publications, financial statements and reports are usually PDFs
([Annual publications as downloads](../transparency/annual-publications-as-downloads.md)).

| Do | Read |
|---|---|
| Produce tagged, accessible PDFs from the source program | [Documents and PDFs](../../government/accessibility/documents-and-pdfs.md) |
| Put the key content on an HTML page as well | [Annual publications page outline](../templates/annual-publications-page-outline.md) |
| Tell visitors the format and size before they click | [Files and downloads](../../creators/website-creators/files-and-downloads.md) |
| Avoid scanned images of text | Not readable by screen readers or search |

Exponential's **PDF export** (Setup > PDF export) can turn content into PDF files
([PDF export](../../../features/6.0/pdf-export.md)); check its result like any other PDF.

## Video and audio

- Captions for every video with speech; a transcript for audio
  ([Captions and media](../../government/accessibility/captions-and-media.md)).
- An embedded player from a video platform loads that platform's scripts; it needs a Content Security Policy entry
  and a mention in the privacy notice ([Maps and embeds](../../government/privacy/maps-and-embeds.md)).
- Podcasts can be published as RSS feeds ([RSS and podcast feeds](../../customers/growth/rss-and-podcast-feeds.md));
  publish the transcript with each episode.

## Newsletters

Newsletter editions in `cjw_newsletter` are written as content ([Supporter newsletter](../communications/supporter-newsletter-with-cjw-newsletter.md)).
The same rules apply: real text, alternative text on images, clear link texts, enough contrast, a "view in browser"
link to the archive.

## Social media previews

Title, description and image for sharing come from metadata ([Metadata and social sharing](../../creators/website-creators/metadata-and-social-sharing.md)).
Keep the message in the title and description, not only in the preview image.

## Pre-launch campaign test

Ten minutes before every campaign goes live:

- [ ] Keyboard: reach and use every button, including Donate.
- [ ] Zoom to 200%: nothing cut off.
- [ ] Automated checker on the page: no errors.
- [ ] Every image: alternative text or decorative.
- [ ] Every video: captions.
- [ ] Every document: accessible, or an HTML alternative, format and size shown.
- [ ] Colors: contrast checked.
- [ ] On a phone: readable and usable.

The general campaign checklist is [Campaign landing page checklist](../../customers/growth/campaign-landing-page-checklist.md).

## Related pages

- [Accessibility for non-profits](README.md)
- [Campaigns and landing pages (Businesses)](../../businesses/nonprofits/campaigns-and-landing-pages.md)
- [Accessible content for editors](../../government/accessibility/accessible-content-for-editors.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
