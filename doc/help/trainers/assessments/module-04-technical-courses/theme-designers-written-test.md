---
title: "Lesson 4.3: Theme designers, written test"
audience: trainer, designer
category: training
order: 43
summary: A thirteen-question written test for the end of the Theme design course, on theme extensions, registering a design, the design settings, loading order, overrides, template path comments, inline code and the Content Security Policy, and version numbers, with an answer key that cites the designers' pages. 25 minutes for learners.
keywords: assessments and practical exercises, theme design, written test, theme extension, DesignExtensions, SiteDesign, AdditionalSiteDesignList, loading order, extends, override, template path comments, CSP, inline scripts, version number
type: help
---
# Lesson 4.3: Theme designers, written test

This test closes the Theme design course. It checks that a designer can keep a theme where it belongs (in its own
extension), make the installation find it, make it win over a base theme where wanted, and ship it without inline
code and with a correct version number.

> **Note**
> This is the trainer's own assessment, not an official certification.

## At a glance

| | |
|---|---|
| Course | [Assessments and practical exercises](../README.md) |
| Module | [Module 4: Assessing the technical courses](README.md) |
| Duration | 40 minutes (5 briefing, 25 test, 10 going through the answers) |
| Level | Intermediate |
| Audience | Learners at the end of the Theme design course |
| Prerequisites | The Theme design course; [Lesson 1.2](../module-01-designing-assessments/writing-written-tests.md) for the trainer |

## Learning objectives

Learners who pass this test have shown that they can:

- explain why a theme lives in an extension and what it contains;
- register a design and choose it for one siteaccess;
- predict which theme wins when two provide the same template;
- find which template wrote a piece of markup;
- prepare a theme for a Content Security Policy and number its releases.

## Trainer's notes

### How to run it

1. Hand out the questions without the key; 25 minutes.
2. Go through the answers with the designers' pages on the projector.

### Common questions

**Is `DesignExtensions[]` the extension name or the design name?** The extension (folder) name; question 4 checks it,
and it is the most common reason a theme is not found.

### Pitfalls

- **Learners who edited `design/standard`.** Use question 1 to discuss why that is lost at the next upgrade.

## The test

1. Give two reasons not to put a theme's files into the installation's own `design/` folders.
2. Name four things a minimal theme extension contains.
3. Which file of the theme registers its design, and with which setting?
4. Is the value of that setting the extension name or the design name?
5. Why should a theme not set `SiteDesign` globally?
6. Two active themes both have `pagelayout.tpl`. Which one is used?
7. Your theme should win over `base_theme`. What do you declare in your `extension.xml`?
8. How do you find out which template file wrote a piece of markup on a page?
9. Why must template path comments never stay on for a production siteaccess?
10. Where does the Content Security Policy come from, and what is it on sites as shipped?
11. Name two kinds of inline code you remove from a theme before a site switches the policy on.
12. A per-element value (a progress percentage) was set with `style=""`. Where does it go instead?
13. Your theme's last release was `1.0.9`. What is the next patch release?

<details>
<summary>Answers</summary>

1. Two of: the next upgrade overwrites them, nobody can tell your changes from the product's, it cannot be shared or
   sold separately, the file consistency check lists every changed file
   ([What a theme extension is](../../../designers/extensions/what-a-theme-extension-is.md)).
2. Four of: `design/<name>/templates/` with `pagelayout.tpl`, `stylesheets/`, `javascript/`, `images/`,
   `settings/design.ini.append.php`, `ezinfo.php`, `extension.xml`, `LICENSE`, `README.md`
   ([What a theme extension is](../../../designers/extensions/what-a-theme-extension-is.md)).
3. `settings/design.ini.append.php` with `[ExtensionSettings] DesignExtensions[]=<extension>`
   ([Registering the design](../../../designers/extensions/registering-the-design.md)).
4. The extension (folder) name; every `design/<name>/` below it is then available
   ([Registering the design](../../../designers/extensions/registering-the-design.md)).
5. It would change the administration and every other site too
   ([The design settings a theme ships](../../../designers/extensions/design-settings-a-theme-ships.md)).
6. The one of the extension loaded earlier
   ([Loading order and which theme wins](../../../designers/extensions/loading-order-for-themes.md)).
7. `<extends><extension name="base_theme" /></extends>` in `<dependencies>`, so your theme loads before it
   ([Loading order and which theme wins](../../../designers/extensions/loading-order-for-themes.md)).
8. Switch on template path comments and search the page source for `START`
   ([Templates and design](../../../../guides/templates-and-design.md#3-see-which-template-wrote-which-markup)).
9. They add markup, with template file paths, to every template; they are for development, never for a production
   siteaccess or for feeds
   ([Templates and design](../../../../guides/templates-and-design.md#3-see-which-template-wrote-which-markup)).
10. From `site.ini [ContentSecurityPolicy]`; off on sites, report-only on the administration
    ([Removing inline scripts and styles](../../../designers/lifecycle/removing-inline-scripts-and-styles.md)).
11. Two of: `<script>` blocks, event attributes such as `onclick=`, `javascript:` links, `style=""` attributes
    ([Removing inline scripts and styles](../../../designers/lifecycle/removing-inline-scripts-and-styles.md)).
12. In a `data-*` attribute or a custom property set from a class in the stylesheet
    ([Removing inline scripts and styles](../../../designers/lifecycle/removing-inline-scripts-and-styles.md)).
13. `1.0.10`: only the last position counts up
    ([Only the last position counts](../../../designers/distribution/only-the-last-position-counts.md)).

</details>

## Scoring

- One point per question; question 2 needs four items for the point.
- Pass: 9 of 13 points or more, with questions 3 and 4 correct.

## Further reading

- [Theme extensions: start here](../../../designers/extensions/theme-extensions-start-here.md)
- [Loading order and which theme wins](../../../designers/extensions/loading-order-for-themes.md)
- [Removing inline scripts and styles](../../../designers/lifecycle/removing-inline-scripts-and-styles.md)

## Related pages

- [Lesson 4.4: Theme designers, practical task](theme-designers-practical-task.md)
- [Module 4: Assessing the technical courses](README.md)
- [Assessments and practical exercises: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
