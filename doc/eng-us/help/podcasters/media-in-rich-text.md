---
title: Media in rich text
audience: podcaster
category: podcasting-streaming
order: 26
summary: How to put an episode, a video or an audio clip inside an article's text with the online editor's Embed object, what decides how it looks, why rich text cannot hold player iframes, and which old video options to avoid.
keywords: rich text, online editor, ezoe, TinyMCE, embed object, embed, audio in article, video in article, iframe, custom tag, video tag, Flash, ezxmltext
type: help
---
# Media in rich text

This page is for editors who want a player inside an article: a clip in a news story, the episode in a blog post, a
video in a guide. The rich text editor can embed an item of the site, and that is the way to do it.

## Contents

1. [Embed an item](#embed-an-item)
2. [How the embed looks](#how-the-embed-looks)
3. [Why there is no iframe button](#why-there-is-no-iframe-button)
4. [Options to avoid](#options-to-avoid)
5. [Alternatives](#alternatives)

## Embed an item

The online editor's toolbar has an embed group with **image** and **object** buttons (`extension/ezoe/settings/ezoe.ini
[EditorLayout] Buttons[embed]=image,object`); the help calls it **Embed object**
([Creating and editing content](../editing-content.md#rich-text)).

1. Upload or create the audio or video item first (an episode, a *Video* item, a *File* item).
2. Edit the article and place the cursor where the player belongs.
3. Press **Embed object**, find the item, choose it and insert it.
4. Publish the article.

The embed is a reference, not a copy. If the episode's audio is replaced later, the article shows the new file.

## How the embed looks

What appears in the article is the item's **embed view**, a template of the site's design. Whether that view shows a
player, a link or a teaser depends on the design:

- On installations with the media theme, the *Audio* and *NG Video* classes have embed templates of their own
  (`extension/sevenx_themes_media/design/media/override/templates/node/view/embed/embed_ng_audio.tpl` and the
  matching video files).
- For other classes, the design may show only the name and a link. Check one embed in the preview before you rely on
  it, and ask your designer for an embed view with a player if needed
  ([Template overrides](../creators/website-creators/template-overrides.md)).

## Why there is no iframe button

Rich text is stored as XML (`ezxmltext`), not as free HTML. The editor converts what you write into that XML, and
there is no element in it for an `<iframe>`: pasted embed code from a video service is not kept as a player. The
TinyMCE `media` plugin is shipped in `extension/ezoe/design/standard/javascript/tinymce8/plugins/` but is not enabled
in `ezoe.ini [EditorSettings] Plugins[]`, and enabling it would not change what the XML can store.

This is deliberate: it keeps scripts and third-party frames out of text that many editors can change.

## Options to avoid

| Option | Why to avoid it |
|---|---|
| The `video` custom tag of the ezwebin design | its template writes an `<object>`/`<embed>` with `application/x-shockwave-flash` (`extension/ezwebin/design/ezwebin/templates/content/datatype/view/ezxmltags/video.tpl` on installations with ezwebin): Flash, which current browsers do not run |
| Media fields with player type Flash, QuickTime, RealPlayer, Silverlight or Windows Media Player | plugin markup for players that no longer exist; an enforced Content Security Policy blocks `<object>` (`object-src 'none'`) |

## Alternatives

| Need | Use |
|---|---|
| A YouTube, Vimeo, Dailymotion or streaming player in a page | the **External video** block of the layouts editor ([Embedding a video or a stream](embedding-a-video-or-stream.md)) |
| A player on the episode page itself | the episode's media field ([Uploading audio](uploading-audio.md)) |
| A list of the latest episodes beside the text | a list block of the layouts editor, or the design's related content |
| A link to the episode | a normal link to the episode item, which follows it when it moves |

## Related pages

- [Embedding a video or a stream](embedding-a-video-or-stream.md)
- [Relations and reuse](../creators/website-creators/relations-and-reuse.md)
- [Creating and editing content](../editing-content.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
