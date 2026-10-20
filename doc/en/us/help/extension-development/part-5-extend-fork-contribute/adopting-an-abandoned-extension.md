---
title: Adopting an abandoned extension
audience: developer, app-creator
category: extension-development
order: 130
summary: How to tell whether an extension is really abandoned, how to ask its author for maintenance or a hand-over, and how to take it on fairly when nobody answers - with the steps and the notices to keep.
keywords: abandoned extension, adopt, maintainer, hand-over, unmaintained, fork, author, contact, transfer, community
type: help
---
# Adopting an abandoned extension

Part 5, chapter 13. Many useful extensions were written years ago and are no longer maintained: the author moved on,
the company closed, the repository was archived. Someone has to look after them if they are to keep working. This
chapter explains how to become that someone without stepping on anyone's toes.

## Is it really abandoned?

Signs, none of which is proof on its own:

| Sign | Where to look |
|---|---|
| No commits for years | the repository's commit history |
| Issues and pull requests unanswered for a long time | the Issues and Pull requests tabs |
| The repository is archived | a banner on GitHub ("This repository has been archived by the owner") |
| The README says so | "no longer maintained", "looking for a maintainer" |
| It does not run on any supported PHP | [Evaluating an old extension](../part-1-from-user-to-developer/evaluating-an-old-extension.md) |

An extension that is simply finished (it does one thing, and does it) may have no commits for years and still be
maintained. Ask before you assume.

## Step 1: ask

Open an issue in the original repository, and if the author lists a contact, write there too:

```text
Title: Is this extension still maintained? Offer to help

Hello, we use this extension with Exponential 6 on PHP 8 and have fixes for PHP 8.0 to 8.5 and for the Content
Security Policy. We would like to send them as pull requests.

If you no longer have time for the extension, we would also be glad to maintain it: as a co-maintainer here, by
transferring the repository, or in a fork that names you as the original author. Whatever you prefer.

Thank you for writing it.
```

Offer the options; let the author choose. Many authors are relieved to be asked.

## Step 2: wait

Give it time: several weeks at least. Write again once, politely. Look for other ways to reach the author
(a profile page, a company site), but respect it if they do not want to be contacted.

## Step 3: the possible outcomes

| Outcome | What you do |
|---|---|
| The author answers and wants to keep it | send your pull requests; offer to review others' |
| The author adds you as a co-maintainer | you maintain it in the original repository; nothing moves |
| The author transfers the repository | GitHub redirects the old address; the history and issues move with it |
| No answer | a fork, done fairly (below) |
| The author says no to a fork | the license still allows it, but think twice and talk; it may be a misunderstanding |

## Step 4: a fair fork, when nobody answers

- Fork on GitHub so the history and the "forked from" note stay ([Forking on GitHub, step by step](forking-on-github.md)).
- Keep the license and every copyright notice; add yours ([Attribution and copyright notices](attribution-and-copyright-notices.md)).
- Use your own package name. Write in the README: "This is a maintained fork of X by Y, which is no longer maintained
  (we asked in issue #N on date D). All credit for the original goes to Y."
- Link the issue where you asked, so anyone can see you did.
- If the author comes back, offer to merge your work into the original.

Several extensions in the se7enxweb organization on GitHub are maintained forks of extensions from the 2000s and
2010s; their repositories show "forked from" under the name, and their READMEs and `composer.json` files name the
original authors. Look at a few before you write your own.

## Step 5: maintain it

Adopting means the work in [The maintainer path](the-maintainer-path.md): answering issues, reviewing pull requests,
releasing. Start by upgrading it ([Part 4](../part-4-upgrading/README.md)) and releasing a version that runs on
current PHP. Then tell the people who use it: on [share.exponential.earth](https://share.exponential.earth) and in the
original repository's issue.

## Related pages

- Previous: [Reviewing other people's work](reviewing-other-peoples-work.md)
- Next: [The maintainer path](the-maintainer-path.md)
- [When to fork, and when not to](when-to-fork.md)
- [Transferring a repository](https://docs.github.com/en/repositories/creating-and-managing-repositories/transferring-a-repository) on GitHub Docs
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
