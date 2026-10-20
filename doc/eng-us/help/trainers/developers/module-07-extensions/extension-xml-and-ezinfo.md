---
title: "Lesson 7.2: extension.xml, ezinfo.php and a license"
audience: trainer, developer
category: training
order: 72
summary: Learners give their extension its identity: ezinfo.php and extension.xml with name, version, copyright, license and website that agree, a LICENSE file, and a check on the About page. 45 minutes.
keywords: developers course, ezinfo.php, extension.xml, metadata, version, license, LICENSE, copyright, info_url, About page, xmllint, version fields in sync, GPL
type: help
---
# Lesson 7.2: extension.xml, ezinfo.php and a license

An extension tells the system who it is in two small files, `ezinfo.php` and `extension.xml`. The About page, the
Extensions page and the upgrade checks read them, and anyone who finds the extension on a server learns from them
which release it is and under which license it may be used. In this lesson learners give `mysite_hello` its
identity and a license.

## At a glance

| | |
|---|---|
| Course | [Developers](../README.md) |
| Module | [Module 7: Extensions](README.md) |
| Duration | 45 minutes (15 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Developers |
| Prerequisites | [Lesson 7.1](the-anatomy-of-an-extension.md) |

## Learning objectives

After this lesson, learners can:

- write `ezinfo.php` with a static `info()` returning `Name`, `Version`, `Copyright`, `License` and `Info_url`;
- write `extension.xml` with a `<metadata>` block that agrees with `ezinfo.php`;
- add a `LICENSE` file whose name matches the license line;
- check both files with `php -l` and `xmllint --noout` and on the About page (`/ezinfo/about`);
- explain why version fields are raised before a release tag, never after.

## Trainer's notes

### How to present it

Sources: [ezinfo.php: what the About page reads](../../../designers/extensions/ezinfo-php.md),
[extension.xml: the packaged description](../../../designers/extensions/extension-xml.md) and
[Specification: extension metadata](../../../../specifications/6.0/extension-metadata.md).

1. **`ezinfo.php`** (4 minutes). The class is the folder name followed by `Info`, `info()` is `static`, every key is
   a quoted string. Show `extension/cjw_themes_jumper/ezinfo.php` if installed.
2. **`extension.xml`** (3 minutes). `<software><metadata>` with `<name>`, `<version>`, `<copyright>`,
   `<license>`, `<info_url>`. The About page reads `extension.xml` first when it has `<metadata>`, so both files must
   agree.
3. **Values never to write** (2 minutes). `//autogentag//` or `dev-master` as a version; a license that differs
   from the `LICENSE` file; a website that is not yours.
4. **The license** (3 minutes). Code that builds on the kernel is usually under the GNU GPL, version 2 or later; the
   license line reads "GNU General Public License v2.0 (or any later version)". Choosing a license is the author's
   decision; [Licensing choices](../../../designers/distribution/licensing-choices-overview.md) explains the
   options.
5. **Versions and tags** (3 minutes). A release raises the version in both files in the commit the tag points at.
   A pushed tag is permanent; a mistake is fixed by the next version, never by moving a tag.

### Common questions

**Does a private extension need a license?** A license file says how others may use the code; for an extension
that never leaves your company, the copyright line is still worth keeping correct.

**What else carries a version?** A `version` in `composer.json` if you keep one, and a version header in a
`share/filelist.md5` if the extension has one; they move together.

**Which version number for a first release?** Your choice; the designers' distribution pages explain the
convention that only the last position counts on (`1.0.9` is followed by `1.0.10`).

### Pitfalls

- **A non-static `info()` or unquoted keys.** The About page shows dashes.
- **Different values in the two files.** The About page shows the `extension.xml` values, which may be the stale
  ones.
- **Copying a license line from another extension.** It must match your own `LICENSE` file.

## Hands-on exercise

On the development installation, in `extension/mysite_hello`.

1. Create `ezinfo.php`:

   ```php
   <?php
   class mysite_helloInfo
   {
       public static function info()
       {
           return array(
               'Name'      => "Mysite Hello",
               'Version'   => "0.1.0",
               'Copyright' => "Copyright (C) 2026 <your name>",
               'License'   => "GNU General Public License v2.0 (or any later version)",
               'Info_url'  => "https://example.invalid/mysite_hello",
           );
       }
   }
   ```

2. Create `extension.xml` with the same values:

   ```xml
   <?xml version="1.0" encoding="utf-8" ?>
   <software>
       <metadata>
           <name>Mysite Hello</name>
           <version>0.1.0</version>
           <copyright>Copyright (C) 2026 your name</copyright>
           <license>GNU General Public License v2.0 (or any later version)</license>
           <info_url>https://example.invalid/mysite_hello</info_url>
       </metadata>
   </software>
   ```

3. Add a `LICENSE` file with the full text of the GNU GPL version 2, for example a copy of
   `extension/ezformtoken/LICENSE`, which ships in the kernel repository.
4. Check: `php -l extension/mysite_hello/ezinfo.php` and `xmllint --noout extension/mysite_hello/extension.xml`.
5. Open the About page, `/ezinfo/about` in the administration (heading "Exponential information"), and find the
   row of `mysite_hello` under **Extensions**.
6. Change the version in `extension.xml` only to `0.1.1`, reload the About page, and see which value wins. Put both
   files back to the same version.
7. Commit the three files with the message `Added: Extension metadata and license`.

## Expected result

- Step 4: `No syntax errors detected` and no output from `xmllint`.
- Step 5: a row with the name, version, license and website.
- Step 6: the About page shows `0.1.1`, the `extension.xml` value.
- The trainer checks one learner's About page row.

## Quiz

1. What is the class name in `ezinfo.php` of the extension `mysite_hello`?
2. Which file does the About page read first when both have values?
   A. `ezinfo.php` B. `extension.xml` C. `composer.json` D. `LICENSE`
3. Name two values that must never be written as a version.
4. When is the version raised for a release: before or after the tag?
5. Which command checks that `extension.xml` is well formed?

<details>
<summary>Answers</summary>

1. `mysite_helloInfo`.
2. **B.** `extension.xml`, when it has a `<metadata>` block.
3. `//autogentag//` and `dev-master`.
4. Before: in the commit the tag points at. A pushed tag is permanent.
5. `xmllint --noout extension/mysite_hello/extension.xml`.

</details>

## Further reading

- [ezinfo.php](../../../designers/extensions/ezinfo-php.md) and [extension.xml](../../../designers/extensions/extension-xml.md).
- [Keeping every version field in sync](../../../designers/extensions/keeping-version-fields-in-sync.md).
- [The LICENSE file](../../../designers/extensions/license-file.md) and
  [Licensing choices](../../../designers/distribution/licensing-choices-overview.md).
- [A pushed tag is permanent](../../../designers/distribution/a-pushed-tag-is-permanent.md).

## Related pages

- [Module 7: Extensions](README.md)
- [Developers: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
