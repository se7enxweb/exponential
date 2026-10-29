---
title: The CLA for students
audience: student
category: learning
order: 44
summary: What the Contributor Licensing Agreement means for a student - when it is required (code contributions to the core kernel), what you give up (the copyright is assigned to the Exponential Foundation and 7x), what it does not cover (your own extensions, sites and work you only publish yourself), how to sign, and questions about employers, schools and small fixes.
keywords: CLA, contributor licensing agreement, copyright, assignment, 7x, kernel, pull request, own extension, GPL, student, school, employer, sign
type: help
---
# The CLA for students

Before you send code to the Exponential kernel, read this page. It explains the Contributor Licensing Agreement
(CLA) in plain words for learners. It summarizes `CONTRIBUTING.md`; the agreement itself is what counts, and this
page is not legal advice ([Copyright assignment (CLA)](../../../../CONTRIBUTING.md#copyright-assignment-cla)).

## Contents

1. [In one paragraph](#in-one-paragraph)
2. [When you need it](#when-you-need-it)
3. [When you do not](#when-you-do-not)
4. [How to sign](#how-to-sign)
5. [Questions students ask](#questions-students-ask)

## In one paragraph

7x and the Exponential Foundation require a signed CLA for every code contribution to the core kernel of
Exponential, the repository [se7enxweb/exponential](https://github.com/se7enxweb/exponential), before it can be
merged. "By signing the CLA and submitting a contribution to the core kernel, you assign (transfer) the copyright in
that contribution to the Exponential Foundation and 7x. They become the copyright owners." You give up control: the Exponential Foundation and 7x may use, change, relicense and
distribute the contribution in any way, including commercially, without asking or paying you, and you cannot withdraw
it. **Your own custom extensions are not affected.**

## When you need it

- A pull request, patch, or code in an issue or mail to the core kernel: `kernel/`, `lib/`, `bin/`, `design/`,
  `settings/`, `doc/`, the extensions shipped inside it, and everything else in that repository.
- A pull request from someone without a signed CLA on file is reviewed, but not merged until the CLA has been
  received.
- An extension you **hand over to 7x for inclusion** in the project is covered the same way.

Note that `doc/` is part of the kernel repository. Documentation fixes there are contributions to that repository.

## When you do not

- Your own extensions, site packages, designs and themes that you write, keep and license yourself, whether private or
  published in your own repository. The CLA page says: "if you have written a contribution and want to make it
  available on GitHub (https://github.com), you do not need to sign the CLA."
- A site you build and run with Exponential; that is governed by the GPL.
- Anything you mark in writing as "Not a Contribution".
- For "small patches or bugfixes via our bugsystem, in most cases we do not need you to sign a CLA" (CLA page). For
  pull requests to the kernel, `CONTRIBUTING.md` advises to sign anyway, since a merge needs a CLA on file and you
  sign only once.

## How to sign

From `CONTRIBUTING.md`:

1. Read the [CLA page](https://share.se7enx.com/community-project/contributor-licensing-agreement-cla) and download
   the agreement it links to.
2. Fill in section 2 (the copyright owner) and the signature block, and sign.
3. Deliver it to 7x by mail, a scan by e-mail, or fax, as the agreement says; if by e-mail, make sure it arrived.
4. Mention in your pull request that your CLA is signed; the pull request template has a checkbox.

"The CLA will be signed once for all your future contributions."

## Questions students ask

**I am a student. Who owns what I write?** Usually you. But if you write it as part of a job or an internship, your
employer may have rights to it; then you need its permission, its waiver, or the company signs (as `CONTRIBUTING.md`
summarizes the agreement). If your school or course has rules on intellectual property for course work, read
them or ask your teacher before you assign copyright.

**Can I keep my extension under my own name?** Yes. Publish it in your own repository; the CLA does not apply. The
GPL still applies when you hand an extension to others ([Contribute extensions back](../contribute-extensions-back.md)).

**Can I take a contribution back later?** No. You cannot withdraw it or set conditions on it.

**Does the CLA replace the GPL?** No. Your contribution is released under the GPL like the rest of Exponential; for the
kernel, the CLA additionally assigns its copyright to the Exponential Foundation and 7x.

**Do I need it for a translation or a documentation fix?** They are contributions to the kernel repository, and
`CONTRIBUTING.md` lists "documentation files, translations" among the kinds of code the CLA page names. Sign it, or ask
in your pull request.

## Related pages

- [Contributing as a student](contributing-as-a-student.md)
- [Develop with Git](../develop-with-git.md#contribute-a-change-with-a-pull-request)
- [Your first contribution, step by step](your-first-contribution-step-by-step.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
