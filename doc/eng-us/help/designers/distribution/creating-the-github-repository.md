---
title: "Creating the GitHub repository"
audience: designer
category: theme-distribution
order: 30
summary: Create a public GitHub repository for a theme with the gh command line or in the browser, connect your local repository, and set the description, website, issues and license so GitHub shows them.
keywords: GitHub, gh repo create, repository, remote, origin, public repository, issues, homepage, license detection
type: help
---
# Creating the GitHub repository

This page is for designers who have a theme under Git on their computer and want it on GitHub. Other Git hosts work
much the same; the commands below use the GitHub command line `gh`.

## Before you start

- The theme is a Git repository whose root is the extension folder ([Putting a theme under Git](../extensions/putting-a-theme-under-git.md)).
- `gh auth status` says you are logged in to github.com.
- `LICENSE` and `README.md` are committed.

## Step by step with gh

1. In the theme's folder, create the repository and push in one go:

   ```bash
   gh repo create yourvendor/mytheme --public --source . --remote origin --push \
       --description "A responsive theme for Exponential 6 sites"
   ```

   Expected: the repository's URL, then the output of the push.
2. Set the website and topics (see [Repository name, description and topics](repository-name-description-topics.md)):

   ```bash
   gh repo edit yourvendor/mytheme --homepage https://packagist.org/packages/yourvendor/mytheme \
       --add-topic exponential --add-topic theme --add-topic responsive-design
   ```

3. Check what GitHub detected:

   ```bash
   gh repo view yourvendor/mytheme --json licenseInfo,hasIssuesEnabled,homepageUrl
   ```

   Expected: your license (for the GPL version 2, GitHub reports `GPL-2.0`), issues enabled, the homepage.

## In the browser

1. On github.com, **New repository**: owner, name (the extension name), description, **Public**. Do not let GitHub
   add a README or license; you already have them.
2. Copy the repository URL and connect your local repository:

   ```bash
   git remote add origin https://github.com/yourvendor/mytheme.git
   git push -u origin main
   ```

3. In **Settings**, check that **Issues** is on; in the sidebar's gear, add the website and topics.

## Good practice

- Use an organization account for a theme that a team or company maintains, so it does not depend on one person.
- Turn on two-factor authentication for every account with write access.
- Add `SECURITY.md` before the first release ([A security policy for a theme](security-policy-for-a-theme.md)).

## Related pages

- [The first push and the default branch](first-push-and-default-branch.md)
- [Develop with Git](../../develop-with-git.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
