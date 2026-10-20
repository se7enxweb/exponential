---
title: Tag content with eztags
audience: government, editor
category: public-sector
order: 210
summary: For editors - add topic tags to a page with the Tags field of eztags, choose from suggestions in a closed vocabulary, ask for missing tags, and check the tag page that lists everything tagged.
keywords: tags, eztags, tagging, topics, autocomplete, tag tree, synonyms, closed vocabulary, tag page, tags/view, Tags dashboard
type: help
---
# Tag content with eztags

This page is for editors who tag pages with topics so visitors find related content. The `eztags` extension adds a
Tags field with autocomplete and a tag tree managed under **Tags** in the administration
([Tagging with eztags](../../../creators/website-creators/tagging-with-eztags.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [Pitfalls](#pitfalls)

## Before you start

- The page's class has a Tags field (for example **Topics**). Older classes may use **Keywords** (`ezkeyword`), which
  is free text without a tree.
- Know your site's tagging rules: which tags exist, whether editors may add new ones.

## Steps

1. Open the page, **Edit**.
2. In the Tags field, start typing a topic: suggestions from the tag tree appear. Choose one with the mouse or the
   keyboard.
3. Depending on how the field is set up, you may see a select list or a tree instead of a text field (edit views
   Default, Select or Tree); choose the tags there.
4. Add the two to five tags that really describe the page; not every word it mentions.
5. A tag is missing: if your role may add tags, the field offers to add it; otherwise ask the vocabulary owner.
6. **Send for publishing**.

## Check the result

- [ ] The page shows its tags as links (where the template shows them).
- [ ] A tag link opens the tag page (`/tags/view/...`), which lists your page.
- [ ] In the administration, **Tags** (`/tags/dashboard`) shows the tag with its content.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Creating near-duplicates ("Recycling", "recycling", "Re-cycling") | choose the suggestion; ask for synonyms instead |
| Too many tags | tag pages become noise |
| Tags only in one language | tags have translations; the vocabulary owner translates them |
| Using tags instead of the tree for the page's place | the tree decides the menu; tags add topics |

## Related pages

- [Publishing tasks](README.md)
- [Life events and topics](../../building/service-catalog/life-events-and-topics.md)
- [eztags](../../../../features/6.0/extensions/eztags.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
