---
title: "Create a GitHub account and repository"
audience: admin, website-creator, non-technical, non-profit, local-business, individual, student
category: administration
order: 4
summary: For people new to GitHub - make a free account, create the repository your static site goes to, and make the token Exponential pushes with, described screen by screen.
keywords: static publishing, GitHub account, sign up, create repository, new repository, public repository, fine-grained token, personal access token, GitHub Pages, github.io, free hosting, gh-pages, beginners
type: help
---
# Create a GitHub account and repository

GitHub is a service that keeps files in **repositories** (folders with a history). Its **GitHub Pages** serve the
files of a repository as a website, free for public repositories. This page sets up what static publishing needs on
GitHub. The names of GitHub's buttons are given in bold; GitHub changes its screens now and then, so look for the
same words if a screen looks different.

## What you'll have at the end

A GitHub account, an empty public repository for your site, and a token stored in Exponential.

## 1. The account

1. Go to `https://github.com/` and choose **Sign up**.
2. Enter an e-mail address, a password and a **username**. Choose the username with care: your site's free address
   will be `https://<username>.github.io/`.
3. Confirm your e-mail address. The **Free** plan is enough.
4. Switch on two-factor authentication in **Settings > Password and authentication**; GitHub asks for it anyway.

An organization (for a club, a company or a school) is made from your account under **Your organizations > New
organization**; its sites live at `https://<organization>.github.io/`.

## 2. The repository

1. Press **+** at the top, then **New repository**.
2. **Owner**: you or your organization. **Repository name**: short, lower case, for example `my-site`. It becomes
   part of the address: `https://<username>.github.io/my-site/`.
3. Choose **Public**. Free plans serve GitHub Pages only from public repositories.
4. Leave **Add a README** and the other boxes unticked, and press **Create repository**.

The page that follows shows the repository's address, `https://github.com/<username>/my-site.git`. Keep it for the
wizard.

## 3. The token

Exponential signs in with a **fine-grained token** that may only touch this one repository:
[Connect to GitHub: token, deploy key or GitHub App](connect-to-github-token-key-or-app.md#a-fine-grained-token)
lists the clicks and the permissions. Store it in **Setup > Secrets** right away; GitHub shows it only once.

## Good to know

- Everything in a public repository can be seen by anyone, including its history. Publish only what may be public:
  [Keep private content private](keep-private-content-private.md).
- GitHub's terms apply to your site: [GitHub Pages limits and terms](github-pages-limits-and-terms.md).

## Next step

[Make a free website in 15 minutes](make-a-free-website-in-15-minutes.md), step 2.
