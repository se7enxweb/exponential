# Help standards: what the help takes from DITA, IEC/IEEE 82079-1, ISO/IEC/IEEE 26514, ISO 3864-2, EPUB Accessibility and WCAG

Status: 19 October 2026. Pull request #288 ("Help system plan, style guide and standards") announced this page as
the third of its specifications, next to the [plan](help-system.md) and the [style guide](help-style-guide.md), but
the file was not part of the pull request when it was merged. This page is written from the two merged pages and
from what Exponential 6.0.15 actually does ([help maintained as content](../../features/6.0/help-content.md)).

Who should read it: whoever writes or reviews help topics, and whoever has to answer the question "which standard
does your documentation follow?".

**No conformance is claimed.** Each standard below is used as a source of rules: the help adopts some of them,
deliberately does some things differently, and leaves the rest out. Where a rule is checked by the validator
(`./console exp:help:content check`), the code is named; everything else is a rule of the review.

## Contents

1. [In short](#in-short)
2. [DITA 1.3](#dita-13)
3. [IEC/IEEE 82079-1:2019](#iecieee-82079-12019)
4. [ISO/IEC/IEEE 26514:2022](#isoiecieee-265142022)
5. [ISO 3864-2:2016](#iso-3864-22016)
6. [EPUB Accessibility 1.1](#epub-accessibility-11)
7. [WCAG 2.2 AA](#wcag-22-aa)
8. [Related pages](#related-pages)

## In short

| Standard | Used for | How far |
|---|---|---|
| DITA 1.3 (OASIS) | the information model: topic types, maps, filter attributes, `shortdesc` | the model as content classes; no DITA XML is stored. An export to DITA is in progress |
| IEC/IEEE 82079-1:2019 | content rules of instructions: notices where the action happens, prerequisites, result, minimalism | adopted for structure and notices; the hazard signal words are not used |
| ISO/IEC/IEEE 26514:2022 | design of user information: audiences, task orientation, review, maintenance | adopted as the editorial process (states, roles, review date); no formal documentation plan per release |
| ISO 3864-2:2016 | safety signs and signal words | only to say why DANGER, WARNING and CAUTION are not used |
| EPUB Accessibility 1.1 | the e-books of manuals | the metadata and structure are written; the books are not checked with Ace or epubcheck by Exponential |
| WCAG 2.2 AA | the help in the administration and the e-books | the help follows the design rules of the admin interface; there is no audit |

## DITA 1.3

**Adopted**

- **Exactly one topic type per topic**: concept, task, reference, troubleshooting (DITA 1.3 added troubleshooting).
  They are the content classes `help_concept`, `help_task`, `help_reference` and `help_troubleshooting`
  (`expHelpContentSchema::classes()`). The fields follow the DITA elements: a task has `context`, `prereq`, `steps`,
  `result` and `postreq`; a troubleshooting topic `condition`, `cause` and `remedy`.
- **`shortdesc`** on every topic, one sentence (validator: `shortdesc.sentences`, `shortdesc.too-long`).
- **Maps**: a manual (`help_manual`, the bookmap) and its chapters (`help_section`) list topics in an ordered
  relation list (`topicrefs`); every topic exists once, in the topic pool (validator: `map.target`, `map.cycle`,
  `map.duplicate`).
- **`related-links`** as the relation list `related`; **figures** as their own objects (`help_screenshot`) with alt
  text, which DITA writes as `<fig>` with `<image><alt>`.
- **Filter attributes**: `audience` and `platform` as fields, validity as `version_from` and `version_to` (DITA
  `rev`/`props`).
- **A stable `id`**: the `help_id`, which is also the DITA id and the file name in an export (style guide section 7;
  validator: `help-id.invalid`, `help-id.duplicate`).

**Different**

- **No DITA XML is stored.** The text fields hold Markdown (`eztext`), which the help renders safely; DITA's
  inline elements (`uicontrol`, `codeph` ...) are written as Markdown (bold for an interface label, backticks for
  code). An export turns the content into DITA; the content is never edited as XML.
- **No DITAVAL in the content.** The audience of a reader is decided by Exponential permissions (sections and the
  object state "Released"), not by filtering one document. An edition per audience is what the reader may read.
- **No conref and no keys.** Reuse is by relation: a topic is referred to, never copied in part.
- **A glossary entry** (`help_glossary_entry`) is an editorial tool with the synonyms to avoid, not a DITA
  `glossentry` that is published.

**How far**: the model and the validator. The DITA bookmap export, and storing the e-books as file objects, are being
added separately ([help maintained as content](../../features/6.0/help-content.md#compared-with-the-specifications)).

## IEC/IEEE 82079-1:2019

The standard for information for use of products. The help takes its rules for the structure of instructions and
for notices; it is written for products that can harm people, which web software cannot.

**Adopted**

- **Prerequisites before the steps, the result after them**: the fields `prereq` and `result` of a task (validator:
  `task.steps-elsewhere` when steps stand in them).
- **One action per step, numbered steps, the result of a step after it** (style guide section 4; validator:
  `task.steps-list` for exactly one numbered list, `task.too-many-steps` for more than 9, `task.sub-steps`).
- **Notices where the action happens** (style guide section 5): an Attention or Data protection notice stands in the
  step, first, before the action. Checked since 6.0.15: `notice.position` (such a notice in `context`, `prereq`,
  `result` or `postreq`), `notice.before-action` (after the action in its step), `notice.per-step` (more than one
  notice in one step), `notice.too-long` (more than three sentences, outside the steps).
- **A graded marking of notices**: three levels, Attention (consequences that cannot be undone), Data protection
  (who sees which data) and Note. In the help viewer they are callouts with their own title (`> **Attention:** ...`,
  `> **Data protection:** ...`, `:::data-protection`).
- **Information kept current**: the review date (`review_date`, validator: `review-date.past`) and the owner of each
  topic.
- **Consistent terminology**: the glossary decides, the validator reports the word to avoid (`glossary.avoid`).

**Different**

- **No safety signal words.** DANGER, WARNING and CAUTION are for hazards to persons (see ISO 3864-2); the help
  reports them (`notice.signal-word`). Deleting a folder is not an electric shock.
- **No safety chapter and no collected notices.** 82079-1 allows safety information in a separate section; the help
  forbids it, so a notice is read where it matters.

**How far**: the structure and notice rules above. The standard's requirements on product identification, the
information for disposal, and its conformance process do not apply to the help of a web application and are not
followed.

## ISO/IEC/IEEE 26514:2022

The standard for the design and development of information for users of software.

**Adopted**

- **Audience analysis**: the audiences are a configured list (`exphelp.ini [ContentSettings] Audiences[]`), every
  topic names its audience, and visibility follows it (style guide section 8).
- **Task orientation and minimalism**: topic types, "action first", nothing self-evident, what can go wrong in its
  own troubleshooting topic (style guide sections 1, 2 and 4).
- **Review and approval**: the object states Draft, Review, Released, and the rule that the author does not release
  their own topic (the roles "Help editor" and "Help release").
- **Context-sensitive help**: a topic opened by its `help_id` from the admin interface or another client
  (`exphelp/context/<help_id>`), with the validator checking that the help_ids clients use exist
  (`client.help-id-unknown`, `client.help-id-not-released`).
- **Navigation and search**: the manual's table of contents, the help's search and the related topics.

**Different**

- **No documentation plan per release** as a separate document. What a release changes in the help is in the
  changelog of the release; the review date and the validity fields keep each topic's state.
- **Usability testing of the documentation** is left to the site; the help has no "Was this helpful?" yet.

**How far**: the editorial process and the delivery channels. The standard's process for planning, estimating and
managing a documentation project is not followed.

## ISO 3864-2:2016

The standard for safety labels on products, which defines the signal words DANGER, WARNING and CAUTION and the
severity they stand for.

**Adopted**: nothing as a rule. The standard is the reason the help does **not** use its signal words: they mean
death or serious injury (DANGER, WARNING) and minor injury (CAUTION). Used for software, they lose their meaning for
the case in which they matter. The validator reports them (`notice.signal-word`).

**How far**: not applied. The help has no safety labels, colours or pictograms of the standard.

## EPUB Accessibility 1.1

The e-books of manuals (`./console exp:help:content export --format=epub`, **Download EPUB**).

**Adopted**

- **Accessibility metadata** in the package document: `schema:accessMode` (textual, visual),
  `schema:accessModeSufficient` (textual), `schema:accessibilityFeature` (structuralNavigation, tableOfContents,
  alternativeText), `schema:accessibilityHazard` (none) and `schema:accessibilitySummary`
  (`expHelpContentExport::opf()`).
- **Structure**: a navigation document with the nested contents, headings in order, the language of the book
  (`xml:lang`, `lang`), and every figure with its alt text and caption.
- **Alt text required** for every figure, at most 150 characters (validator: `figure.alt-missing`,
  `figure.alt-too-long`).

**Different**

- The book carries `dcterms:conformsTo` "EPUB Accessibility 1.1 - WCAG 2.2 Level AA", but **Exponential does not
  check the book**: epubcheck and Ace by DAISY are not shipped. A site that publishes the books runs both on the file
  before it does; without that run the claim in the metadata is not verified.
- No page list or page breaks: the books have no printed edition to refer to.

**How far**: the metadata and structure the standard asks for are written; the evaluation is the publisher's.

## WCAG 2.2 AA

The help in the administration (`exphelp`) and the e-books.

**Adopted**

- **Text alternatives** for figures (above), **headings and landmarks** in the viewer, the page's language on the
  article (`lang`), **keyboard** use of the navigation, search and contents, and callouts marked with `role="note"`.
- **Contrast and dark mode** through the tokens of the admin design: every colour of the help has a light and a dark
  value (the admin styling rules of the project).

**Different**: nothing is done differently on purpose.

**How far**: the help follows the design rules of the admin interface, which aim at WCAG 2.2 AA; it has not been
audited against the success criteria, and no conformance is claimed.

## Related pages

- [Help system plan](help-system.md)
- [Help style guide](help-style-guide.md)
- [Help maintained as content](../../features/6.0/help-content.md)
- [The help classes as a package](help-content-package.md)
