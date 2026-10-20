---
title: Patterns in kernel/classes/help
audience: developer
category: standards
order: 492
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/help with its design patterns.
keywords: kernel/classes/help, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/help

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/help` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (31)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expHelp` | class | [Utility (static helper)](utility.md) | `kernel/classes/help/exphelp.php:11` | The help system's shared helpers (doc/features/6.0/help-system.md): its settings, whether it is on, the install root it reads from, and the URLs of i… |
| `expHelpAllowlist` | class | [Factory and static factory methods](factory.md) | `kernel/classes/help/exphelpallowlist.php:29` | The files the help system may show (doc/features/6.0/help-system.md, "Which files are shown"). |
| `expHelpAudience` | class | [Factory and static factory methods](factory.md) | `kernel/classes/help/exphelpaudience.php:29` | Who a help page is for, which section of the help it belongs to and what kind of page it is (doc/features/6.0/help-system.md, "Audiences, categories… |
| `expHelpContentExport` | class | – | `kernel/classes/help/content/exphelpcontentexport.php:21` | E-books of a manual of help maintained as content (doc/features/6.0/help-content.md, "E-books"): EPUB 3 (written here, pure PHP with ZipArchive) and… |
| `expHelpContentInstaller` | class | – | `kernel/classes/help/content/exphelpcontentinstaller.php:23` | Creates the content model of help maintained as content on an installed site, idempotently: the eight help_* classes (class group "Help"), the sectio… |
| `expHelpContentProvider` | class implements expHelpContextProviderInterface, expHelpContextPageInterface | [INI plugin point (extension point)](ini-plugin-point.md), [Singleton / instance()](singleton.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/help/content/exphelpcontentprovider.php:19` | Help maintained as content, delivered into the help system (exphelp): the topics the current user may see, rendered with the help's Markdown renderer… |
| `expHelpContentRepository` | class | [Singleton / instance()](singleton.md) | `kernel/classes/help/content/exphelpcontentrepository.php:15` | Reads help maintained as content out of the database into plain arrays (the shapes expHelpContentValidator describes), without any permission filter:… |
| `expHelpContentSchema` | class | [Utility (static helper)](utility.md) | `kernel/classes/help/content/exphelpcontentschema.php:20` | The content model of help maintained as content (doc/specifications/6.0/help-system.md, doc/features/6.0/help-content.md): the eight help_* content c… |
| `expHelpContentSeeder` | class | – | `kernel/classes/help/content/exphelpcontentseeder.php:15` | Creates the initial glossary, topics, chapters and manuals from a data file (share/help/seed-glossary.json, share/help/seed-topics.json). |
| `expHelpContentValidator` | class | – | `kernel/classes/help/content/exphelpcontentvalidator.php:23` | The machine-checkable rules of the help style guide (doc/specifications/6.0/help-style-guide.md), on plain arrays: no database. |
| `expHelpContextPageInterface` | interface | [Interface (contract)](interface.md) | `kernel/classes/help/exphelpcontextpageinterface.php:16` | A context help provider (expHelpContextProviderInterface) that also serves pages which are not files below doc/: help topics maintained as content, w… |
| `expHelpContextProviderInterface` | interface | [Interface (contract)](interface.md) | `kernel/classes/help/exphelpcontextproviderinterface.php:15` | A class that answers "help for this page" in code, for what one Map[] entry per view cannot say (a view whose help depends on its parameters, an exte… |
| `expHelpContextRegistry` | class | [Factory and static factory methods](factory.md), [Registry](registry.md), [Singleton / instance()](singleton.md) | `kernel/classes/help/exphelpcontextregistry.php:44` | Context help: which help page answers "help for this page", for a module, view and design. |
| `expHelpDocumentIndex` | class | [Singleton / instance()](singleton.md) | `kernel/classes/help/exphelpdocumentindex.php:34` | The index of the help pages (doc/features/6.0/help-system.md, "The index"): one entry per document of the allowlist (expHelpAllowlist: git-tracked fi… |
| `expHelpExamplesGenerator` | class | [Builder](builder.md) | `kernel/classes/help/exphelpexamplesgenerator.php:25` | Writes the generated reference examples below doc/examples/ (doc/examples/README.md, "How the generated pages are made"): one Markdown page per templ… |
| `expHelpFrontMatter` | class | [Utility (static helper)](utility.md) | `kernel/classes/help/exphelpfrontmatter.php:28` | The front matter of a help page (doc/features/6.0/help-system.md, "Front matter"): a block at the very top of a Markdown file between two lines of "-… |
| `expHelpFunctionCollection` | class | [Fetch function (Query Object, Facade)](fetch-function.md), [Utility (static helper)](utility.md) | `kernel/classes/help/exphelpfunctioncollection.php:10` | The exphelp fetch functions (kernel/exphelp/function_definition.php). |
| `expHelpHighlighter` | class | [Registry](registry.md), [Utility (static helper)](utility.md) | `kernel/classes/help/exphelphighlighter.php:29` | Server-side syntax highlighting of the code blocks of the help pages (exphelp, doc/features/6.0/help-system.md), without JavaScript or an external li… |
| `expHelpIssueReport` | class | – | `kernel/classes/help/exphelpissuereport.php:18` | Reporting an error (exphelp/report, doc/features/6.0/help-system.md "Reporting an error"): where to report it (the trackers of [IssueTrackers], the r… |
| `expHelpLicense` | class | [Utility (static helper)](utility.md) | `kernel/classes/help/exphelplicense.php:17` | The license of the documentation the help shows (exphelp.ini [License], doc/README-LICENSE.md). |
| `expHelpLinkCheck` | class | – | `kernel/classes/help/exphelplinkcheck.php:25` | Checks the links and images of the help pages (doc/features/6.0/help-system.md, "Checking the documentation"; "./console exp:help check"). |
| `expHelpLinks` | class | [Utility (static helper)](utility.md) | `kernel/classes/help/exphelplinks.php:12` | The link and image resolvers the viewer gives expHelpMarkdownRenderer (doc/features/6.0/help-system.md): a relative link to an allowed page becomes i… |
| `expHelpMarkdownRenderer` | class | [Factory and static factory methods](factory.md) | `kernel/classes/help/exphelpmarkdownrenderer.php:50` | Renders the Markdown of the documentation (doc/**.md) to the HTML of the help viewer (exphelp/viewer, doc/features/6.0/help-system.md): CommonMark bl… |
| `expHelpNavigation` | class | – | `kernel/classes/help/exphelpnavigation.php:20` | The navigation tree of the help (doc/features/6.0/help-system.md, "Viewer"): every page the index serves, in its folders, as nested lists. |
| `expHelpPdf` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/help/exphelppdf.php:18` | A help page as a PDF file (doc/features/6.0/help-system.md, "Print and PDF"): the view exphelp/pdf/ and `./console exp:help pdf ` ask here. |
| `expHelpPdfCanvas` | class extends Cpdf | – | `kernel/classes/help/exphelppdfcanvas.php:18` | The page surface of a help page's PDF (doc/features/6.0/help-system.md, "Print and PDF"): the kernel's own PDF library (lib/ezpdf, Cpdf) with the few… |
| `expHelpPdfFontSubset` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/help/exphelppdffontsubset.php:17` | Cuts a TrueType font down to the glyphs a PDF uses (doc/features/6.0/help-system.md, "Print and PDF"). |
| `expHelpPdfWriter` | class | – | `kernel/classes/help/exphelppdfwriter.php:28` | Lays a rendered help page out as a PDF (doc/features/6.0/help-system.md, "Print and PDF"). |
| `expHelpReadPolicyGrant` | class | [Utility (static helper)](utility.md) | `kernel/classes/help/exphelpreadpolicygrant.php:22` | Plans and applies the grant of the policy exphelp/read (bin/php/addexphelpreadpolicy.php): reading the help system (doc/features/6.0/help-system.md). |
| `expHelpRecordRenderer` | class | – | `kernel/classes/help/exphelprecordrenderer.php:36` | Plain text pages that are lists of records (doc/release-overview, doc/branch-overview: the release and branch history kept from the original SVN repo… |
| `expHelpSearch` | class | [Factory and static factory methods](factory.md) | `kernel/classes/help/exphelpsearch.php:34` | expHelpSearch: the full-text search of the help system (doc/features/6.0/help-system.md, "Search"). |
