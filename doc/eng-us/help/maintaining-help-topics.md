---
title: Keeping the help up to date: reviews, e-books and DITA
audience: editor, admin
category: publishing
order: 40
summary: The list of help topics overdue for review, the e-books that are rebuilt when a topic is released, and the DITA export of the help.
keywords: help topic, review, review date, overdue, release, e-book, EPUB, PDF, DITA, export, manual
context: exphelp/dashboard, exphelp/manual
type: help
---
# Keeping the help up to date: reviews, e-books and DITA

This page is for the people who write and release the help of a site: help editors and release managers. The help
topics and manuals are content of the site, written in the administration and set to **Draft**, **Review** and
**Released** (see [help maintained as content](../features/6.0/help-content.md)). Three things help keep them right.

## Topics overdue for review

Every topic has a **review date**: the day someone should read it again and check that it still matches the site.
A topic can also wait too long in **Review**. The start page of the help (**Help** in the top bar) lists both under
**Help topics overdue for review**:

- **Review date passed**: the topic's review date is over, whatever its state.
- **In Review for ... days without a change**: the topic has been in Review longer than the site allows (14 days unless
  your administrator changed it) and nobody has changed it since.

You see a topic in the list only when you can do something about it. As a help editor, you see the topics in Review.
As a release manager, you also see the Released topics whose review date has passed. When nothing is overdue, the
list is not shown at all.

To deal with a topic:

1. Click **Edit** or **Set state** next to the topic.
   The topic opens in the administration.
2. If the topic is Released, set its state back to **Review** (release managers only).
3. Edit the topic: check the text against the site, and set a new **Review date**.
4. Send it on: a release manager sets it to **Released** again.

The person who wrote a change is not the person who releases it.

## E-books of the manuals

Each manual of the help is also an e-book, as EPUB and PDF. You find the e-books on the manual's page in the help,
under **E-books**: the file, its version, its size and when it was last built.

You do not have to build them. Each time a topic, chapter or manual is set to **Released**, the e-books of every
manual that contains it are built again. The new book is saved as a new version of the same file, so the earlier
editions stay in its version history. A book of a manual for the public contains only public topics, whoever
releases.

**Download EPUB** and **Download PDF** next to the manual's title make a book on the spot, with only what you may
read. The stored e-books under **E-books** are the editions for everyone who may read the manual.

## Manuals made from the help pages

Most manuals in the list of manuals are made from the pages of this help: one manual for each group of pages (for
editors, for administrators, for the press ...), one for the user manual, one for each book, and one each for the
installation guide, the guides, the features, the contributor notes, the standards and the specifications. Each
folder of pages is a chapter, each page a topic. Where a page is translated into German, the topic has the German
translation, and the German e-book of the manual uses it.

These manuals are made again from the pages each time a new version of Exponential is installed. So:

- **Do not correct these topics in the administration.** A change made there is replaced by the page at the next
  update. Correct the page instead, or report the mistake (**Report an error** in the help).
- You recognize them by their remote ID, which starts with `help-gen-`, and by their help ID, which starts with
  `doc.` (`doc.help.editors.publishing` is the page `help/editors/publishing.md`).
- The topics, chapters and manuals you write yourself are never changed by the update.

Your administrator runs the update with `./console exp:help:content build-manuals -s admin --release`, and builds
the e-books with `./console exp:help:content ebooks -s admin` (add `--language=ger-DE` for the German edition). See
[help maintained as content](../features/6.0/help-content.md#manuals-from-the-markdown-help).

## The help as DITA

DITA is the standard format for technical documentation. Other documentation tools, translation services and print
shops can read it.

- **Download DITA** on a manual's page gives a zip file with the manual and its released topics.
- **Download all as DITA** on the list of manuals gives every released manual, topic and glossary entry.

Only Released topics you may read are in a download. Your administrator can also write the export into a directory
of the server, or into a git repository that keeps the history of the help, with
`./console exp:help:content dita`.

## Learn more

- [Help maintained as content](../features/6.0/help-content.md)
- [The help style guide](../specifications/6.0/help-style-guide.md)
- [Publishing, versions and the trash](publishing-and-versions.md)

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](partners/partners-start-here.md) and [How to apply](partners/how-to-apply.md).
<!-- exp-partner-block:end -->
