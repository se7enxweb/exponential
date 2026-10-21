---
title: Glossary for learners
audience: student
category: learning
order: 51
summary: The words a learner meets first in Exponential, each explained in one or two sentences with the page that teaches it - from administration, attribute and cache to siteaccess, template, version and zone - plus the learning words of this folder (study site, study project, rubric, Student level).
keywords: glossary, terms, words, definitions, vocabulary, content class, node, object, siteaccess, override, cache, layout, policy, role, learner
type: help
---
# Glossary for learners

The words you meet first, in plain language, with the page that teaches each. The complete list is the
[Glossary](../../glossary.md); many definitions here follow it.

## Contents

1. [Product words](#product-words)
2. [Learning words](#learning-words)

## Product words

| Word | Meaning | Learn more |
|---|---|---|
| **Administration** | Where you work on the site: the `admin` siteaccess | [A tour of the administration](../admin-tour.md) |
| **Always available** | A flag that shows an object in its main language where no listed language exists | [Content languages](../../guides/content-languages.md#6-main-language-and-always-available) |
| **Attribute** | One field of a content class, with a datatype | [Understanding the content model](understanding-the-content-model.md) |
| **Block** | One part of a layout zone: a title, rich text, an image, a list | [The layouts editor](../layouts-editor.md) |
| **Cache** | Stored results that make pages faster | [Understanding caches](understanding-caches.md) |
| **CLA** | The agreement needed before code is merged into the kernel; assigns copyright to 7x | [The CLA for students](the-cla-for-students.md) |
| **Console** | `php bin/php/console`, which runs the installation's commands (`exp:...`) | [Glossary](../../glossary.md#c) |
| **Content class** | A type of content (Article, Folder) as a list of attributes | [project 1](project-01-a-content-class-of-your-own.md) |
| **Content object** | One piece of content, with versions and translations | [Understanding the content model](understanding-the-content-model.md) |
| **Content tree** | The hierarchy of nodes in the administration | [Understanding the content model](understanding-the-content-model.md#objects-and-nodes) |
| **Datatype** | The kind of an attribute: Text line, XML block, Image ... | [The content model and editing content](../../guides/content-model-and-editing.md) |
| **Design** | A folder of templates, stylesheets, scripts and images | [Learning path: designer](learning-path-designer.md) |
| **Draft** | An unpublished version you are still working on | [Publishing, versions and the trash](../publishing-and-versions.md) |
| **Extension** | A folder that adds features without changing the kernel | [project 8](project-08-your-first-extension.md) |
| **Fetch** | A template function that reads data, such as a list of children | [Learning the template language](learning-the-template-language.md) |
| **INI settings** | Configuration files; you override them with `.ini.append.php` files | [Understanding siteaccesses and settings](understanding-siteaccesses-and-settings.md) |
| **Kernel** | The core of Exponential, the repository `se7enxweb/exponential` | [Installing from Git clones for study](installing-from-git-clones-for-study.md) |
| **Layout** | The plan of a page in Exponential Layouts, with zones | [project 4](project-04-a-page-with-layouts.md) |
| **Main language** | The language an object was created in, or chosen later | [Languages](../languages.md) |
| **Mapping (rule)** | Decides on which pages a layout is used | [The layouts editor](../layouts-editor.md) |
| **Node (location)** | A place of an object in the content tree; one object can have several | [Understanding the content model](understanding-the-content-model.md) |
| **Override** | A template that replaces another for some pages, chosen by a rule | [project 3](project-03-a-template-override.md) |
| **Package** | Content, classes and design shipped as an `.ezpkg` archive | [Site packages for learners](site-packages-for-learners.md) |
| **Pagelayout** | The outer template that wraps every page | [Glossary](../../glossary.md#p) |
| **Policy** | One permission inside a role | [project 9](project-09-roles-for-a-restricted-editor.md) |
| **Role** | A named set of policies, assigned to users or groups | [project 9](project-09-roles-for-a-restricted-editor.md) |
| **Section** | A label on content that selects a design and permissions | [Sections](../../guides/sections.md) |
| **Shared layout** | A layout whose zones other layouts link to, such as a header | [The layouts editor](../layouts-editor.md) |
| **Site package** | The content and design set installed at setup | [Site packages for learners](site-packages-for-learners.md) |
| **Siteaccess** | One way of reaching the installation (`site`, `admin`, `editor`) with its own settings | [Understanding siteaccesses and settings](understanding-siteaccesses-and-settings.md) |
| **SQLite** | The database in one file; the default of `exp:install` | [Database support](../database-support.md) |
| **Template** | A `.tpl` file in the template language | [Learning the template language](learning-the-template-language.md) |
| **Trash** | Where removed items wait to be restored or purged | [Publishing, versions and the trash](../publishing-and-versions.md) |
| **Velocity** | Exponential's application server; the `php` engine serves a study site | [Installing a study site in one command](installing-a-study-site-in-one-command.md#serve-the-site) |
| **Version** | One saved state of an object: draft, published or archived | [Understanding the content model](understanding-the-content-model.md#versions-and-translations) |
| **wash** | The template operator that escapes text for HTML | [Learning the template language](learning-the-template-language.md) |
| **Zone** | An area of a layout that holds blocks | [The layouts editor](../layouts-editor.md) |

## Learning words

| Word | Meaning in this folder | Page |
|---|---|---|
| **Study site** | An installation of your own for practice, never a live site | [Your first site: three ways](your-first-site-three-ways.md) |
| **Learning path** | An order of help pages for one role | [Choosing a learning path](choosing-a-learning-path.md) |
| **Study project** | A graded practice project on your study site | [Study projects: overview](study-projects-overview.md) |
| **Rubric** | The criteria and checks a project is graded with | [Grading your own work](grading-your-own-work.md) |
| **Capstone** | The final project that joins the others | [project 10](project-10-capstone-a-complete-small-site.md) |
| **Study journal** | Your notes, one entry per session | [Keeping a study journal](keeping-a-study-journal.md) |
| **Student level** | The $25-a-month level of the Exponential Partner Program | [The Student partner level](the-student-partner-level.md) |
| **Certificate of completion** | A trainer's statement that you passed their assessment; not an official certification (those are planned, not available yet) | [Certifications and certificates](certifications-and-certificates.md) |
| **Demo mode** | A server setting for public demos in which administrator passwords cannot be changed | [Demo mode FAQ](../demo-mode-faq.md) |

## Related pages

- [Glossary](../../glossary.md)
- [Site packages glossary](../site-packages/glossary.md)
- [Partner glossary](../partners/partner-glossary.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
