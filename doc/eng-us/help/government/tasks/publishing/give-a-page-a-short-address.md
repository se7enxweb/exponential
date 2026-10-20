---
title: Give a page a short address
audience: government, editor
category: public-sector
order: 360
summary: For editors - give a page a short, memorable address for print and radio (example.gov/permits) with a URL alias on the node's URL aliases page, choose whether it redirects, and avoid clashes.
keywords: short address, vanity URL, URL alias, Create new alias, URL alias name, Alias should redirect, Place alias on the site root, campaign address, print
type: help
---
# Give a page a short address

This page is for editors who need a short address for a page, to print on a leaflet or say on the radio: `/permits`
instead of `/living/roads-and-parking/parking/resident-parking-permits`. A URL alias on the page does this
([URLs and aliases](../../../../guides/urls-and-aliases.md), [URL aliases and nice URLs](../../../creators/website-creators/url-aliases-and-nice-urls.md)).

## Contents

1. [Before you start](#before-you-start)
2. [Steps](#steps)
3. [Check the result](#check-the-result)
4. [Pitfalls](#pitfalls)

## Before you start

- Choose the short address: a word people remember, lower case, no spaces (`permits`, `vote`, `heat`).
- Check it is free: open `/<word>` on the site; it should answer "not found".
- Your role may manage aliases (the URL aliases page of a node needs edit rights; global aliases need `content/urltranslator`).

## Steps

1. Open the page; open its **URL aliases** page (from the item's menu, or `/content/urlalias/<node id>`). The page
   lists **Generated aliases** and the aliases you made.
2. Under **Create new alias**:
   - **URL alias name**: `permits`;
   - **Language**: the page's language;
   - tick **Place alias on the site root** so the address is `/permits`, not below the page's parent;
   - tick **Alias should redirect to its destination** so visitors (and search engines) end on the real address.
3. Press **Create**.

## Check the result

- [ ] Signed out, `https://<your site>/permits` opens the page (and with redirect on, the address bar shows the full
  address).
- [ ] The alias is listed on the node's URL aliases page; **Remove selected** removes it later.

## Pitfalls

| Pitfall | Avoid it |
|---|---|
| A word that is already a folder's address | the alias clashes; choose another |
| A short address for a temporary campaign | remove it when the campaign ends, or point it to a page that explains |
| Printing the address before testing | test first |
| One address per language | create an alias for each language siteaccess if needed |
| An alias to a module page (search, a form view) | that is a global alias: **Setup > URL translator** |

## Related pages

- [Publishing tasks](README.md)
- [Redirect old addresses after a reorganization](redirect-old-addresses-after-a-reorganization.md)
- [Redirects and moved content](../../../creators/website-creators/redirects-and-moved-content.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
