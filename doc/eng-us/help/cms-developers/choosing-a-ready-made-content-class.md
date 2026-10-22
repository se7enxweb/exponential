---
title: Choosing a ready-made content class
audience: cms-developer, admin
category: cms-development
order: 51
summary: Which of the content classes that site packages install fits which job - folder, article, image, file, link, frontpage, feedback form, comment, forum, gallery, event and the media classes - and what to check before you reuse one or build your own.
keywords: content classes, default classes, folder, article, image, file, link, frontpage, feedback form, comment, forum, forum topic, gallery, event calendar, media classes, plain text, rich text, reuse
type: help
---
# Choosing a ready-made content class

A site installed from a site package comes with a set of content classes ready for common jobs. Using one of them
saves templates and editor training, as long as its fields really match your content. This page groups the usual
classes by job and says what to look at before you choose. Which classes your site has, and their exact fields,
are listed under **Setup** > **Classes**; the classes of the `sevenx_multisite` packages are listed in
[sevenx_classes](../site-packages/sevenx-multisite/sevenx-classes.md).

## First decision: plain text or rich text

Every class mixes three kinds of text fields:

| Type | Datatype | Use it for |
|---|---|---|
| Text line | `ezstring` | titles, names, short labels: one line, no markup |
| Text block | `eztext` | plain text over several lines, such as a message a visitor types |
| Rich text | `ezxmltext` | formatted text with headings, links, lists, tables and embedded objects |

Plain text keeps the look in the hands of the design: the template decides how it appears, and editors cannot add a
different font or a stray link. Rich text gives editors structure, and the design still styles every element.
Fields that visitors fill in, such as comments and form messages, should be plain text. Links to other content
that survive moves and renames are only possible in rich text, because it stores the target by node or object, not
by address.

## Structure: folders and front pages

| Class | Container | Typical fields | Choose it for |
|---|---|---|---|
| Folder (`folder`) | yes | name, short name, summary, description, a switch whether to list its sub items | sections of the site and their landing pages |
| Frontpage (`frontpage`) | yes | name, a billboard (a related object), left, center, right and bottom columns of rich text | a start page in columns, on sites without the layouts editor |

The short name of a folder is used where space is tight, such as menus and breadcrumbs (the object name pattern
`<short_name|name>` takes the short name when it is filled in). The switch to list sub items lets an editor turn a
folder into a plain page. For new
sites, a page built with [Layouts](layouts-for-cms-developers.md) usually replaces the Frontpage class.

## Text content

| Class | Container | Typical fields | Choose it for |
|---|---|---|---|
| Article (`article`) | yes | title, short title, author, intro, body, image (a relation), a switch for comments | news and other dated texts |
| Article (main-page) and Article (sub-page) | main page yes | the main page has the article fields plus an index title; sub-pages below it hold the further parts | long texts read in parts |
| Documentation page | yes | a title and a body, can hold sub pages | manuals and handbooks in a tree |
| Infobox | no | header, image, rich text content, a link | small boxes in a column |
| Blog and Blog post | yes | a container for posts, and the post itself | blogs, with comments below each post |

The Article class is a container on purpose: comments are created below the article. Which classes an editor may
create below an article is a matter of the role's `content/create` policy with a **Class** and **ParentClass**
limitation, not of the class.

## Media

| Class | Datatype of the file | Choose it for |
|---|---|---|
| Image (`image`) | `ezimage` | pictures; each image has its own alternative text, which can be made required per class attribute |
| File (`file`) | `ezbinaryfile` | documents to download, such as PDF files |
| Video | `ezmedia` | video files played in the page |
| Flash, Quicktime, Real video, Windows media, Silverlight | `ezmedia` | old sites only: these players need browser plugins that current browsers no longer run |

Keep images and files in the media library (node 43, section Media) and relate or embed them where they are used.
One image object can then appear in many places, and replacing it updates all of them.

## Links

| Class | Typical fields | Choose it for |
|---|---|---|
| Link (`link`) | name, description, location (`ezurl`) | lists of addresses on other sites, with their own page and description |

An address stored in a Link object or in a rich text link goes through the link list, so it is checked for being
broken and can be corrected in one place ([How do I find and fix broken links?](../manual/how-do-i-find-and-fix-broken-links.md)).

## Visitors who write

| Class | Typical fields | Choose it for |
|---|---|---|
| Comment (`comment`) | subject, author, message | short replies below an article or blog post |
| Feedback form (`feedback_form`) | name, description, then the fields a visitor fills in (sender name, subject, message, e-mail) and a recipient address | contact forms |
| Forums, Forum, Forum topic, Forum reply | forum topic: subject, message, sticky, notify me about updates | discussion areas |
| Poll | name, description, and a question with options that visitors answer (an information collector) | a single vote |

The fields a visitor fills in on a feedback form are information collectors: their values are stored apart from
the object, listed under **Setup** > **Collected information**, and sent by e-mail. Which address gets the mail is
decided by the mail template of the design (`content/collectedinfomail/feedback.tpl` sets `email_receiver`); site
designs normally read it from the Recipient field. When the template gives no valid address, the mail goes to
`site.ini [InformationCollectionSettings] EmailReceiver`, or else to `[MailSettings] AdminEmail`.

A forum is a tree four levels deep: Forums holds Forum objects, a Forum holds topics, a topic holds its replies.
Replies are not nested below other replies. The forum template of the ezwebin design lists sticky topics first,
and "notify me about updates" subscribes the person who publishes the topic to the replies below it. Removing a forum removes all its
topics and replies with it.

## Calendars and galleries

| Class | Container | Choose it for |
|---|---|---|
| Event calendar and Event | calendar yes | dated events listed by month |
| Gallery | yes | a set of Image objects shown together |

## Before you reuse a class

- Read the class under **Setup** > **Classes**: its attributes, which are required, and its object name pattern.
- Check the templates your design has for it. A class without a full view template of your design falls back to a
  generic one.
- Rename fields in the class rather than misusing one: an "Author" field that holds a place name confuses every
  editor after you.
- When no class fits, create your own ([Creating a content class](creating-a-content-class.md)). A few clear classes
  are better than one class with optional fields for everything.

## Related pages

- [The content model](the-content-model.md)
- [Choosing datatypes](choosing-datatypes.md)
- [Designing a content model](designing-a-content-model.md)
- [Forms and information collection](forms-and-information-collection.md)
- [How do I add an attribute to a content class?](../manual/how-do-i-add-an-attribute-to-a-content-class.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
