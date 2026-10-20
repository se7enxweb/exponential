# Exponential CMS Contributors Guidelines

Thank you for considering contributing to Exponential CMS! We appreciate the time and effort you invest in making this project better.

## Copyright assignment (CLA)

**7x and the Exponential Foundation require a signed Contributor Licensing Agreement (CLA) for every code
contribution to the core kernel of Exponential, before that contribution can be merged.** The core kernel is the
repository [se7enxweb/exponential](https://github.com/se7enxweb/exponential): `kernel/`, `lib/`, `bin/`, `design/`,
`settings/`, `doc/`, the extensions shipped inside it, and everything else in this repository.

- **Every form of submission counts**: a GitHub pull request, a patch, code attached to or pasted into an issue, and
  code sent by mail or any other way. The agreement defines a submission as "any form of electronic, verbal, or
  written communication sent to 7x or its representatives that is explicitly meant to share the Contribution with
  7x", including source code control systems and issue tracking systems (CLA, §3).
- **Required before the merge.** A pull request from someone without a signed CLA on file is reviewed, but it is
  not merged until the CLA has been received.
- **Once is enough.** "The CLA will be signed once for all your future contributions". You sign again only "in the
  case where you would represent a new legal entity".

What the agreement does: the project calls this requirement the copyright assignment, but legally the agreement is
a **license, not a transfer**. You keep the copyright of your code. You grant 7x "and to recipients of software
distributed by 7x a perpetual, worldwide, non-exclusive, no-charge, royalty-free, irrevocable copyright license to
reproduce, prepare derivative works of, publicly display, publicly perform, sub-license, and distribute Your
Contributions" (§4), and a matching patent license (§5). The agreement itself is made with 7x. Read the whole
agreement before you sign it; this summary does not replace it and is not legal advice.

### How to sign

1. Read the [Contributor Licensing Agreement page](https://share.se7enx.com/community-project/contributor-licensing-agreement-cla)
   and download the agreement it links to (*7X CONTRIBUTOR LICENSING AGREEMENT*, version 1.0.1, PDF).
2. Fill in section 2 (the copyright owner, or the legal entity authorized by the copyright owner) and the signature
   block, and sign it.
3. Deliver it to 7x "by regular mail, by emailing a scan of the signed copy, or by fax". The agreement gives the
   e-mail address community@se7enx.com and the postal address; if you send it by e-mail, make sure it arrived.
4. Mention in your pull request (or patch, issue or mail) that your CLA is signed; the
   [pull request template](.github/PULL_REQUEST_TEMPLATE.md) has a checkbox for it.

**Individuals and companies** sign the same agreement. For a company, "the entity making a Contribution and all
other entities that control, are controlled by, or are under common control with that entity are considered to be a
single Contributor" (§3). If your employer has rights to what you write, you need its permission, its waiver, or a
separate agreement between your employer and 7x (§6).

### What it does not cover

The CLA concerns code **submitted back to the project**. It does not affect:

- **your own custom extensions**, site packages, designs and themes that you write, keep and license yourself,
  whether you run them privately or publish them in your own repository. The CLA page says: "if you have written a
  contribution and want to make it available on GitHub (https://github.com), you do not need to sign the CLA. You
  are free to just upload your contribution to the site."
- **a site you build and run with Exponential.** Using, changing and running Exponential is governed by the GPL
  (see below), not by the CLA.
- **anything you mark as not a contribution**: the agreement excludes "communication that is conspicuously marked or
  otherwise designated in writing by You as "Not a Contribution."" (§3).

The CLA page names no repositories. It lists the kinds of code it needs a signed CLA for when "you want 7x to
include" it in the project: "complete/incomplete extensions [...], patches, templates, PHP files, configuration
files, documentation files, translations". So an extension you **hand over to 7x for inclusion** in the project (for
example to be maintained in the se7enxweb organization) is covered in the same way; an extension you only publish
yourself is not.

### FAQ

**Do I give away my copyright?** No. You keep it and grant 7x a non-exclusive license (§4), so you may still use
and license your own code yourself.

**Why is it needed?** "Before we can consider the contributions you provide to us, we must agree on what rights you
are offering to us" (preamble of the agreement), and "We cannot accept your contributions unless we have a signed
Contributor Licensing Agreement from you".

**Is a one-line fix a contribution?** The CLA page says that for "small patches or bugfixes via our bugsystem, in
most cases we do not need you to sign a CLA", because "authors cannot claim copyright for small fixes". For pull
requests to the kernel repository, sign the CLA anyway: a merge needs a CLA on file, and you sign only once.

**I contribute for my employer.** Then the company is usually the copyright owner: the company signs, or you
confirm that it has given you permission or waived its rights (§6).

**I only build extensions for my own sites or customers.** Nothing to sign. The CLA starts to matter only when you
submit code back to the kernel, or hand code to 7x to include in the project.

**I already signed the CLA for an earlier project of 7x.** One signature covers "all your future contributions";
you sign again only for a new legal entity.

**Does the CLA replace the GPL?** No. Your contribution is released under the GPL like the rest of Exponential (see
[Licensing](#licensing)); the CLA is the additional permission that lets 7x accept and distribute it.

## Licensing of code improvements to Exponential
Exponential is licensed under the GPLv2 or later license, commonly known as the GNU General Public License, version 2 or any later version, grants users the freedom to modify and distribute software. When it comes to code improvements, contributors must ensure that any modifications made to the project are also released under the same GPLv2 or later license. This means that enhancements, bug fixes, or any changes to the codebase must be open-sourced, allowing others to benefit from and build upon these improvements. The "or later" clause allows the project to upgrade to future versions of the GPL, providing flexibility and ensuring alignment with the principles of free software. It's essential for contributors to understand and comply with these licensing requirements to maintain the open nature of the project.

## Code of Conduct

Please make sure to review our [Code of Conduct](CODE_OF_CONDUCT.md) before contributing. All contributors are expected to adhere to it.

## Getting Started

1. Fork the repository and clone your fork to your local machine.

```bash
git clone https://github.com/your-username/exponential.git
```

2. Install the project dependencies.

```bash
composer install
```

3. Set up the development environment.

4. Create a feature branch for your contribution.

```bash
git checkout -b feature/your-feature
```

## Making Changes

1. Always create a new branch for your work to keep the main branch clean.

2. Follow the [PSR standards](https://www.php-fig.org/psr/) and our [coding style](doc/en/us/standards/coding-standards.md).

3. Write clear, concise commit messages whose subject starts with what the change does: `Added: `, `Updated: `,
   `Fixed: `, `Removed: ` or `Renamed: `, e.g. `Fixed: The redirect keeps the query string on the own host`. The
   commit-msg hook of `.githooks/` (`make hooks`) and CI check it; see
   [the quality checks](doc/en/us/features/6.0/quality-checks.md).

4. Keep your pull request focused. If you have multiple contributions, submit multiple pull requests.

5. Templates and stylesheets of the administration designs (and of extensions' admin designs) follow
   [Styling the administration](doc/en/us/bc/6.0/admin4-styling.md): no `<style>` block or `style=""` attribute, the
   shared `.a4-*` components and `--a4-*` tokens with dark values, module CSS in `stylesheets/modules/`. Its code
   review checklist applies to every such pull request; `tests/tests/design/AdminStylingRulesTest.php` checks it.

## Submitting Changes

1. Ensure all tests pass.

2. Update the documentation if necessary.

3. Keep the file manifest current: every commit that adds, changes or removes a file updates its line in
   `share/filelist.md5` (`make manifest-fix`). Run `make hooks` once in your clone, and the pre-commit hook checks
   it, the PHP syntax and the code style for every commit. When CI still finds a stale line, its summary shows the
   lines to change; see [Pull requests](doc/en/us/features/6.0/quality-checks.md#pull-requests).

4. Push your changes to your fork.

```bash
git push origin feature/your-feature
```

5. Make sure your signed CLA is on file with 7x ([Copyright assignment (CLA)](#copyright-assignment-cla)).

6. Submit a pull request to the main repository and tick the CLA checkbox of the pull request template.

## Code Reviews

All contributions will be reviewed before merging. Be open to feedback and be willing to make changes if requested. Code reviews are essential to maintain code quality. A contribution to the kernel is merged only once its author's signed CLA has been received.

## Issues and Discussions

Feel free to open an issue for bug reports, feature requests, or general discussions. Follow the provided templates to provide necessary details.

## Licensing

By contributing, you agree that your contributions will be licensed under the [GNU General Public License, version 2.0 or later](LICENSE). Make sure that your contributions comply with this license. In addition, a contribution to the core kernel needs your signed Contributor Licensing Agreement ([Copyright assignment (CLA)](#copyright-assignment-cla)).

Thank you for contributing to Exponential CMS!

---
