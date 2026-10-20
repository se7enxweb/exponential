---
title: "Lesson 2.2: Getting the code for a class"
audience: trainer, admin, developer
category: training
order: 22
summary: Choose how learners get the Exponential code, a Git clone with Composer for developer courses, composer create-project for editor and administrator courses, and pin every learner to the same release. 40 minutes.
keywords: git clone, composer install, composer create-project, release, tag, version, training lab, se7enxweb, preferred-install, source
type: help
---
# Lesson 2.2: Getting the code for a class

There are three ways to put Exponential on a machine, and they suit different courses. A developer course wants Git
clones with their history; an editor course only needs a working site. This lesson shows each way and how to make
sure every learner has exactly the same version, which is what keeps exercises reproducible.

## At a glance

| | |
|---|---|
| Course | [Train the trainer](../README.md) |
| Module | [Module 2: Building the training lab](README.md) |
| Duration | 40 minutes (10 presenting, 30 exercise) |
| Level | Intermediate |
| Audience | Trainers who prepare labs |
| Prerequisites | [Lesson 2.1](requirements-for-a-training-machine.md); Git and Composer on the machine |

## Learning objectives

After this lesson, learners can:

- choose between `git clone` with `composer install` and `composer create-project` for a given course;
- install a chosen release instead of the development line;
- list the newest release tags in the right order;
- check that the extensions arrived as Git clones when they should.

## Trainer's notes

### How to present it

1. Show the three ways from [Getting the code, 3.2](../../../../install/03-getting-the-code.md#32-three-ways-to-get-the-code)
   (5 minutes): A. `git clone` and `composer install` (recommended for building websites), B.
   `composer create-project` (a site you only run, a quick evaluation), C. `composer require` into an empty directory.
2. Recommend per course (3 minutes): Developers, Theme design and Layouts development: way A. Editors, Site
   administration, Hosting: way B with a fixed release.
3. Show how to pin a release (2 minutes): `composer create-project se7enxweb/exponential:v6.0.14 exponential`, or
   for a clone `git switch -c my-site v6.0.14` before `composer install`.

### Common questions

**`main` or a release?** For a course, a release: every learner sees the same thing, and your rehearsal matches. Use
`main` only for a course about contributing.

**Why is `git status` full of untracked extensions?** The extensions are separate Git repositories cloned into
`extension/<name>` by Composer; they are not part of the kernel's repository. That is expected.

**How do I see the newest release?** `git tag -l 'v6.*' --sort=version:refname | tail -3`. Always sort by version:
by name, `v6.0.10` comes before `v6.0.8`.

### Pitfalls

- **A slow network.** The full clone has the whole history (more than 27,000 commits). For a class on a slow network,
  prepare the code in advance on each machine or on a shared drive.
- **Mixed versions.** One learner on `main`, the rest on a release: exercises differ. Check with `git describe --tags`.
- **Running as root.** Files created by `root` cannot be changed by the web server later. Install as a normal user.

## Hands-on exercise

Prepare the code for a developer course (way A) and for an editor course (way B) on one machine.

1. For way A, once per user account: `composer config --global 'preferred-install.se7enxweb/*' source`.
2. `git clone https://github.com/se7enxweb/exponential.git dev-course`, then `cd dev-course`.
3. List the newest releases: `git fetch origin --tags` and `git tag -l 'v6.*' --sort=version:refname | tail -3`.
   Choose the newest one.
4. `git switch -c training <the tag>` and then `composer install`.
5. Check: `ls -d extension/*/.git | wc -l` prints the number of extensions installed as clones; `git describe --tags`
   names the release.
6. For way B, in another directory: `composer create-project se7enxweb/exponential:<the tag> editor-course`.
7. Write the two exact command sequences on your trainer sheet, with the tag you used.

## Expected result

- `dev-course` is a clone on the branch `training` made from the chosen release, with extensions as Git clones.
- `editor-course` holds the same release, installed from archives.
- The trainer sheet has the two command sequences with one fixed tag.

## Quiz

1. Which way of getting the code is recommended for developer courses?
2. Why should every learner in a course use the same release?
3. Why is `git tag -l 'v6.*' | tail -1` a wrong way to find the newest release?
4. After `composer install` in a clone, `git status` lists many untracked `extension/` directories. Is that a problem?

<details>
<summary>Answers</summary>

1. `git clone` with `composer install` (way A).
2. So that every exercise gives the same result as the trainer's rehearsal.
3. It sorts by name, so `v6.0.10` comes before `v6.0.8`; use `--sort=version:refname`
   ([Update with Git](../../../update-with-git.md)).
4. No. The extensions are separate repositories and are not part of the kernel's repository.

</details>

## Further reading

- [Getting the code](../../../../install/03-getting-the-code.md): all three ways in detail.
- [Develop with Git](../../../develop-with-git.md): working with the clones.
- [Update with Git](../../../update-with-git.md): keeping a lab up to date between courses.

## Related pages

- [Module 2: Building the training lab](README.md)
- [Train the trainer: course overview](../README.md)
- [Trainers: start here](../../trainers-start-here.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
