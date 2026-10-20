---
title: "Lesson 7.3: From a static HTML site"
audience: trainer, admin, developer
category: training
order: 73
summary: Learners migrate a small static HTML site - read the files or crawl, group pages by template, extract and clean the content area into the simplified input, import images and documents, build the tree from the directories, and keep the .html addresses with aliases, wildcards or web server rules. 60 minutes.
keywords: migrating to exponential, static HTML, crawl, static site generator, content extraction, simplified input, XmlText, ZIP import, folder tree, .html addresses, redirects, url wildcard
type: help
---
# Lesson 7.3: From a static HTML site

A static site is the simplest source: every page is a file you can read. The work is in separating the content from
the layout repeated around it, and in keeping the addresses. This lesson is the most hands-on of the module: learners
take three small pages through the whole method.

## At a glance

| | |
|---|---|
| Course | [Migrating to Exponential](../README.md) |
| Module | [Module 7: From other systems](README.md) |
| Duration | 60 minutes (15 presenting, 45 exercise) |
| Level | Intermediate |
| Audience | Administrators, developers and website creators |
| Prerequisites | [Lesson 2.3](../module-02-mapping-content/urls-redirects-and-seo.md); an editor or administrator account on a training installation |

## Learning objectives

After this lesson, learners can:

- choose between reading the files, reading the generator's sources and crawling;
- find the content area of a page template and reduce it to the simplified input;
- turn a directory structure into a folder tree with remote ids from the old paths;
- keep `.html` addresses with one rule or one alias per page.

## Trainer's notes

### How to present it

Source: [Static HTML sites and Exponential](../../../../install/migration/static-html.md).

1. **Read or crawl** (3 minutes). Files of a hand-made site; the Markdown sources of a static site generator, not the
   generated HTML; a crawl (sitemap, links, server log) when only the live site exists.
2. **Extract** (5 minutes). Group pages by template; per template, find the content area (`<main>`, `<article>`, a
   content `div`) and the title, date, author and description. Drop navigation, scripts, inline styles and layout
   tables; keep headings, paragraphs, lists, tables, links and images: the simplified input the kernel parses into
   XmlText.
3. **Tree and media** (3 minutes). The directories are usually the navigation. Images and linked documents become
   image and file objects; a folder of them can come in through the upload page's ZIP importer, keeping its folder
   structure.
4. **Addresses** (4 minutes). Old addresses end in `.html` or are directories with `index.html`. One redirecting
   alias per page, or, when they follow a pattern, a URL wildcard or a web server rule that strips `.html`.

### Common questions

**Can Exponential make a static copy too?** Yes: `exp:makestaticcache` writes static pages of public siteaccesses
(guide, section 5); see lesson 8.1.

**What about pages built with tables for layout?** Drop the layout tables and keep the content; real data tables stay
tables.

**How many pages justify a script?** The guide does not set a number. For a handful, editors can paste the cleaned
content; past that, the script skeleton of lesson 3.3 with JSON rows.

### Pitfalls

- **Pasting whole pages.** Navigation and footers end up in every article.
- **Images left on the old server.** References break when the old site is switched off; import them.
- **Not checking what alias cleaning makes of `.html`.** Test one address before writing a hundred.

## Hands-on exercise

Use your own training installation. The "old site" is three pages the trainer hands out (or that learners write in
a text editor first): `about/index.html`, `about/team.html` and `products/widgets.html`, each with a header, a menu, a
`<main>` with a heading, two paragraphs and one image, and a footer.

1. Open each page in a text editor and mark the content area and the parts to keep (title, description).
2. Write the cleaned content of `about/team.html`: only headings, paragraphs, lists, links and the image reference.
3. Write the tree you will build: which folders, which pages, and the remote id of each, for example
   `html-/about/team.html`.
4. In the admin, below your course folder, create the folders **About** and **Products** (press **Create new** and
   choose **Folder**), and publish each.
5. Create the page **Team** below **About** as an article (or the class your training site uses for pages) and paste
   the cleaned content into it. Publish.
6. Open the URL alias page of the **Team** node (`/admin/content/urlalias/<node id>`). Under **Create new alias**, type
   `about/team.html` as **URL alias name**, keep **Alias should redirect to its destination** checked, and press
   **Create**. If the page reports that characters were changed, note what it made of the dot.
7. Request the old address and check status and destination (replace the host with your training site's address):

   ```bash
   curl -s -o /dev/null -w '%{http_code} %{redirect_url}\n' http://localhost/about/team.html
   ```

8. In your notes, write the URL wildcard or the web server rule that would cover all `.html` pages of a large static
   site at once, and the risk of using it.
9. Remove the alias you created in step 6 (select it and press **Remove selected**), so the training site stays clean.

## Expected result

- Step 2: content without menu, footer, scripts or inline styles.
- Steps 4 and 5: the folders and the page are published below the course folder.
- Step 6 and 7: the old address answers with a redirect (301) to the new node's address, or the learner noted how the
  alias was cleaned and what that means for the old address.
- Step 8: a wildcard or web server rule that strips `.html`, with the risk that it matches addresses that should not
  move.
- The trainer checks step 7 on two screens.

## Quiz

1. A site was built with a static site generator. What do you read: the generated HTML or the sources?
2. Which elements does the simplified input keep?
   A. Scripts and styles B. Headings, paragraphs, lists, tables, links and images C. Navigation menus D. Layout tables
3. Where does the folder tree of a static site usually come from?
4. Name two ways to keep a hundred `.html` addresses that follow one pattern.
5. Which tool brings a folder of images in while keeping its folder structure?

<details>
<summary>Answers</summary>

1. The sources (Markdown with front matter)
   ([static HTML guide, section 1](../../../../install/migration/static-html.md#1-read-the-files-or-crawl)).
2. **B** ([section 2](../../../../install/migration/static-html.md#2-extract-the-content)).
3. From the directory structure (section 2).
4. A URL wildcard or a web server rule that strips `.html`
   ([section 4](../../../../install/migration/static-html.md#4-addresses)).
5. The upload page's ZIP importer ([section 3](../../../../install/migration/static-html.md#3-import)).

</details>

## Further reading

- [Static HTML sites and Exponential](../../../../install/migration/static-html.md): the method in full.
- [JavaScript and Node.js apps](../../../../install/migration/javascript-apps.md): Markdown and MDX sources of static
  site generators.
- [Media and uploads](../../../media-and-uploads.md): uploading many files at once.

## Related pages

- [Module 7: From other systems](README.md)
- [Migrating to Exponential: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
