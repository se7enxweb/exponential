---
title: "Lesson 2.5: Links and design files with ezurl, ezroot, ezdesign and ezimage"
audience: trainer, developer
category: training
order: 25
summary: Learners build links that respect the siteaccess with ezurl, choose quoting and absolute addresses, point at files of the installation with ezroot, and find stylesheets and images through the design chain with ezdesign and ezimage. 40 minutes.
keywords: developers course, template language, ezurl, ezroot, ezdesign, ezimage, url_alias, siteaccess, quotes, absolute url, design chain, links
type: help
---
# Lesson 2.5: Links and design files with ezurl, ezroot, ezdesign and ezimage

A link written by hand, `<a href="/about">`, breaks as soon as a site is served below a path such as `/site/` or
in a second language. The URL operators build links and file addresses the kernel's way, so they keep working in
every siteaccess and design. This lesson teaches the four operators every pagelayout uses.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 2: The template language](README.md) |
| Duration | 40 minutes (15 presenting, 25 exercise) |
| Level | Beginner |
| Audience | Developers and template builders |
| Prerequisites | [Lesson 2.4](operators-wash-and-translations.md) |

## Learning objectives

After this lesson, learners can:

- build links to nodes with `{$node.url_alias|ezurl}` and to module views with `{'content/view/full/2'|ezurl}`;
- control quoting and absolute addresses with `ezurl( 'no' )` and `ezurl( 'no', 'full' )`;
- address a file of the installation with `ezroot`;
- find a stylesheet or image through the design chain with `ezdesign` and `ezimage`;
- explain why hand-written addresses break when the siteaccess changes.

## Trainer's notes

### How to present it

Source: [Templates and design, part 6](../../../../guides/templates-and-design.md#6-the-template-language-in-ten-minutes);
the operators are defined in `kernel/common/ezurloperator.php`.

1. **The problem** (3 minutes). Open the site at `/site/` and at another siteaccess address on the projector.
   The same page has different addresses; a link written by hand points at one of them only.
2. **`ezurl`** (4 minutes). It adds the siteaccess part of the address when needed. By default it prints the
   address **with double quotes**, so it is written without quotes in an attribute: `<a href={$url|ezurl}>`.
   `ezurl( 'no' )` prints no quotes (for example inside `concat`), `ezurl( 'no', 'full' )` an absolute address with
   the host, for feeds and e-mails.
3. **`url_alias`** (2 minutes). Every node has a readable address in `$node.url_alias`. Prefer it to
   `content/view/full/<id>` for links visitors see.
4. **`ezroot`** (2 minutes). A path from the installation root, without the siteaccess: `{'/'|ezroot}` is the home of
   the installation.
5. **`ezdesign` and `ezimage`** (4 minutes). `{'stylesheets/site.css'|ezdesign}` looks for the file in the design
   chain (module 3) and returns the address of the first match; `{'logo.png'|ezimage}` does the same in `images/`.
   Most stylesheets and scripts of a design are loaded through the ezjscore packer instead (Theme design course).

### Common questions

**Why are there quotes in my output?** That is `ezurl`'s default. Write `href={...|ezurl}` without your own quotes,
or use `ezurl( 'no' )` inside your own quotes.

**Do I need `wash` after `ezurl`?** No: `ezurl` produces an address; wash the link *text*.

**Which address should a mail or an RSS feed use?** An absolute one: `ezurl( 'no', 'full' )`.

### Pitfalls

- **Double quotes.** `href="{$url|ezurl}"` prints `href=""/about""`. Remove the outer quotes.
- **`ezurl` on a full address.** For links to other web sites, print the address as it is (washed).
- **`ezdesign` and a missing file.** When no design has the file, the operator still returns an address, which
  then answers "not found" in the browser. Check the network tab of the browser's developer tools.

## Hands-on exercise

Use the playground from lesson 2.1 and run it after each step.

1. Link to a module view in the three forms:

   ```
   {'content/view/full/2'|ezurl}
   {'content/view/full/2'|ezurl( 'no' )}
   {'content/view/full/2'|ezurl( 'no', 'full' )}
   ```

2. Link to the node with its readable address: `<a href={$node.url_alias|ezurl}>{$node.name|wash}</a>`.
3. Build a link list of the first three children:

   ```
   {foreach $node.children as $child max 3}
       <a href={$child.url_alias|ezurl}>{$child.name|wash}</a>
   {/foreach}
   ```

4. Print the installation root: `{'/'|ezroot}`.
5. Find a stylesheet and an image through the design chain:

   ```
   {'stylesheets/core.css'|ezdesign}
   {'logo.png'|ezimage}
   ```

6. Run the playground for the editor siteaccess with the standard option `-s` and compare the output of step 1:
   `php bin/php/ezexec.php -s editor extension/mysite_hello/scripts/play.php` (the folders of `settings/siteaccess/`
   name the siteaccesses of your installation).
7. Write a deliberately wrong link `<a href="{$node.url_alias|ezurl}">` and look at the doubled quotes.
8. Fix it and commit the template.

## Expected result

- Step 1 prints the address with quotes, without quotes, and with `http://` and the host.
- Step 3 prints three links with readable addresses.
- Step 5 prints addresses below `/design/<design>/` or `/extension/<name>/design/<design>/`, depending on where
  the first matching file was found.
- Step 6: the addresses differ when the other siteaccess is matched by a path or a host.
- Step 7 shows `href=""...""` in the output.
- The trainer asks each learner which form of `ezurl` belongs in an e-mail.

## Quiz

1. Why is `href={$url|ezurl}` written without quotes?
2. Which form gives an absolute address with the host?
   A. `ezurl` B. `ezurl( 'no' )` C. `ezurl( 'no', 'full' )` D. `ezroot`
3. What is the difference between `ezurl` and `ezroot`?
4. Where does `ezimage` look for `logo.png`?
5. Which attribute of a node gives its readable address?

<details>
<summary>Answers</summary>

1. `ezurl` prints the address with double quotes by default.
2. **C.** `ezurl( 'no', 'full' )`.
3. `ezurl` adds the siteaccess part of the address; `ezroot` gives a path from the installation root without it.
4. In the `images/` folders of the design chain, from the site design down to `standard`.
5. `url_alias`.

</details>

## Further reading

- [Templates and design](../../../../guides/templates-and-design.md): the design chain and overrides.
- [URLs and aliases](../../../../guides/urls-and-aliases.md): how readable addresses are made.
- [Safe redirects](../../../../features/6.0/safe-redirects.md): addresses that come from users.

## Related pages

- [Module 2: The template language](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
