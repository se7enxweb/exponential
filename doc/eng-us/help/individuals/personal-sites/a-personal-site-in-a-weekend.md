---
title: A personal site in a weekend
audience: individual, website-creator
category: website-creation
order: 1602
summary: A step-by-step weekend plan for one person to build a small personal site on Exponential - install on your own computer with SQLite, create the pages, add images and a contact form, adjust the look, and prepare the move to a host.
keywords: weekend, step by step, personal site, first site, install, SQLite, exp:install, pages, images, contact form, theme, publish, go live
type: help
---
# A personal site in a weekend

This page is for individuals who have a plan ([Planning a personal site](planning-a-personal-site.md)) and a free
weekend. It builds the site on your own computer first, where mistakes cost nothing, and moves it to a host at the
end. The detailed technical walk-through is
[Your first site step by step](../../creators/website-creators/your-first-site-step-by-step.md); this page is the
same work cut into a weekend.

## Contents

1. [Before Saturday](#before-saturday)
2. [Saturday morning: install](#saturday-morning-install)
3. [Saturday afternoon: pages](#saturday-afternoon-pages)
4. [Sunday morning: images, contact and look](#sunday-morning-images-contact-and-look)
5. [Sunday afternoon: check and prepare to go live](#sunday-afternoon-check-and-prepare-to-go-live)
6. [If you run out of time](#if-you-run-out-of-time)

## Before Saturday

- [ ] A computer with PHP 8.0 or later and Composer, or access to a host with the setup wizard
  ([Requirements at a glance](../../hosting/basics/requirements-at-a-glance.md)).
- [ ] Your page list and texts in a document, even rough ones.
- [ ] Five to ten images, already chosen.
- [ ] An address where contact messages should go.

## Saturday morning: install

1. Get the code ([Getting the code](../../../install/03-getting-the-code.md)).
2. Install with the console and its defaults: SQLite and the default site package. On your own computer that is the
   quickest way ([Choosing an install method](../setup/choosing-an-install-method.md)):

   ```bash
   php bin/php/console exp:install --dry-run
   php bin/php/console exp:install
   ```

   For a real site later, the clean package without demo content is the better start
   ([Choosing a site package and theme](../setup/choosing-a-site-package-and-theme.md)).
3. Serve it, with PHP's built-in server or Velocity ([Serving the site](../../../install/08-serving-the-site.md)).
4. Sign in to the administration ([Logging into the admin](../../logging-into-the-admin.md)) and change the default
   password at once ([Change the admin password](../../change-the-admin-password.md)).
5. Take the [Admin tour](../../admin-tour.md).

## Saturday afternoon: pages

1. Delete or hide the demo content you do not want (or start from the clean package).
2. Create the folders of your page list in the content tree ([Editing content](../../editing-content.md)).
3. Write the About page as an *Article*: a title, a short intro, the body.
4. Create one item for each project or talk.
5. Publish each page and look at it on the public site. If you change your mind, every publish is a new version
   you can go back to ([Publishing and versions](../../publishing-and-versions.md)).

> **Tip**
> Write the intro of every page as if it were the only sentence anyone reads. It appears in lists, search results
> and feeds.

## Sunday morning: images, contact and look

1. **Images**: upload them into the media library or directly into the articles
   ([Media and uploads](../../media-and-uploads.md)); crop or rotate in the browser with `ezie`
   ([Editing images with ezie](../portfolios/editing-images-with-ezie.md)). Give every image alternative text.
2. **Contact**: create a *Feedback form* object and set its recipient
   ([About page and contact form](about-page-and-contact-form.md)).
3. **Look**: change the site name, the logo and colors the theme lets you change
   ([Branding your site](../../creators/website-creators/branding-your-site.md)). Deeper changes are template
   overrides, which can wait.

## Sunday afternoon: check and prepare to go live

| Check | How |
|---|---|
| Every page opens, every link works | Click through the menu; [Link and content checks](../../creators/website-creators/link-and-content-checks.md) |
| It reads well on a phone | Narrow the browser window |
| The contact form sends | Send yourself a message |
| Titles and descriptions | [SEO basics](../../creators/website-creators/seo-basics.md) |
| Images have alternative text | Open each image's edit form |

Then decide where it will live ([Running on a small budget](../setup/running-on-a-small-budget.md)) and work through
the [launch checklist](../checklists/launch-checklist-for-individuals.md). Moving from your computer to a host means
copying the database file and `var/storage` together with the code, then setting the real address
([Going live: hosts, HTTPS and siteaccesses](../../creators/website-creators/going-live-hosts-https.md)).

## If you run out of time

Publish less, not worse. Home, About and Contact are a complete personal site. Work, writing and the rest can
follow one page at a time.

## Related pages

- [Personal sites](README.md)
- [Your first site step by step](../../creators/website-creators/your-first-site-step-by-step.md)
- [Launch checklist for individuals](../checklists/launch-checklist-for-individuals.md)
- [Getting started](../../getting-started.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
