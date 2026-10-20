---
title: Publish a council meeting agenda
audience: government, editor
category: public-sector
order: 20
summary: For editors in democratic services - publish the agenda and papers of a council or committee meeting on the meeting's page before the deadline your rules set, and check that visitors can open every paper.
keywords: council agenda, meeting agenda, committee, meeting papers, upload papers, relations, publish, deadline, democratic services
type: help
---
# Publish a council meeting agenda

This page is for editors in democratic services or a council secretariat who publish meeting agendas. It assumes the
site has a Meeting class (as proposed in [Content model for municipal and local government](../../levels/municipal-and-local/content-model.md))
or uses a Folder per meeting; the steps use the editing features described in
[Creating and editing content](../../../editing-content.md).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [Pitfalls](#pitfalls)

## Before you start

- The meeting exists in **Council and democracy > Meetings > <year>** (created with the year's calendar), or you
  know where to create it.
- You can edit there (a role with `content/edit` for the council section).
- The agenda text and the papers (PDF files, with readable file names) are final and approved for publication.
- You know the publication deadline your rules set.

## Steps

1. Open the meeting in the content tree and press **Edit**. If several languages exist, choose the language first.
2. In **Agenda**, write the items as a numbered list. For each item give its title and, where a paper exists, its
   name as it will appear in the papers list.
3. In **Papers**, press **Upload new** for each paper (each upload becomes a File object and is related), or
   **Add existing** if the papers were already uploaded to the media library.
4. Give each paper a clear title ("Item 4: Budget report") if the upload used the file name.
5. Set **Status** to *agenda published* if your Meeting class has it.
6. Press **Preview** to see the page as visitors will, then **Send for publishing**.

If the site holds meeting pages for approval, the version becomes pending; see
[Send a page for approval](send-a-page-for-approval.md).

## Check the result

- [ ] Sign out (or use a private window) and open the meeting page: the agenda and every paper link are there.
- [ ] Open two or three papers: they download (an access error means the files are in a section visitors cannot read).
- [ ] The meeting shows in the meetings list with the right date.
- [ ] People who follow the meetings folder get a notification at the next run ([Follow changes with notifications](follow-changes-with-notifications.md)).

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| Papers named `scan0034.pdf` | rename before upload; the name shows in the download address |
| Personal data in papers that should be exempt | check with the secretariat before upload; removing later leaves copies in versions |
| An inaccessible scanned PDF | ask the author for an accessible version; summarize the item on the page ([Upload a PDF report with an accessible summary](upload-a-pdf-report-with-an-accessible-summary.md)) |
| Publishing in the wrong language | choose the language before **Edit** |
| Late changes to the agenda | edit and add "Updated <date>: <change>" at the top; see [Correct a published notice and keep the history](correct-a-published-notice-and-keep-the-history.md) |

## Related pages

- [Publishing tasks](README.md)
- [Publish meeting minutes after approval](publish-meeting-minutes-after-approval.md)
- [Council meetings and local services](../../levels/municipal-and-local/council-meetings-and-local-services.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
