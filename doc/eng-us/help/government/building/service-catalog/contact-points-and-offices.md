---
title: Contact points and offices
audience: government, website-creator, editor
category: public-sector
order: 60
summary: How to model contact points and offices as their own objects in Exponential, relate them from every service they handle, show opening hours and accessibility, and change them in one place.
keywords: contact point, office, desk, opening hours, address, phone, shared mailbox, relation, ezobjectrelation, reverse relations, accessibility of buildings
type: help
---
# Contact points and offices

This page is for website creators and editors who manage contact information for services. When every service page
types its own phone number and opening hours, a change of hours means editing dozens of pages. With one object per
contact point, related from each service, the change is made once. Relations are explained in
[Relations and reuse](../../../creators/website-creators/relations-and-reuse.md).

## Contents

1. [The Contact point class](#the-contact-point-class)
2. [Relating services to contact points](#relating-services-to-contact-points)
3. [Showing the contact on the service page](#showing-the-contact-on-the-service-page)
4. [Changing hours and closures](#changing-hours-and-closures)
5. [Personal data](#personal-data)

## The Contact point class

Create it in **Setup > Classes** ([Content classes for websites](../../../creators/website-creators/content-classes-for-websites.md)):

| Attribute | Identifier | Datatype | Required |
|---|---|---|---|
| Name | `name` | Text line (`ezstring`) | yes |
| Kind | `kind` | Selection (`ezselection`): office, phone line, online only | yes |
| Address | `address` | Text block (`eztext`) | no |
| Opening hours | `opening_hours` | XML block (`ezxmltext`) | no |
| Temporary changes | `changes` | XML block | no |
| Phone | `phone` | Text line | no |
| E-mail | `email` | Email (`ezemail`) | no |
| Contact form | `form` | Object relation (`ezobjectrelation`) to a feedback form | no |
| Accessibility | `access` | XML block | no |
| Location on a map | `location` | Google Maps Location (`ezgmaplocation`), optional | no |

Keep contact points in a folder "Contact points" (or below each office's page). They need not appear in menus.

The map location datatype uses Google Maps, a third-party service, and needs a key
([ezgmaplocation](../../../../features/6.0/extensions/ezgmaplocation.md)); an address in text plus a link to your own
map service may suit your privacy policy better.

## Relating services to contact points

In the Service class, the **Contact** attribute is an Object relation limited to the Contact point class. Editors pick
the contact with **Add existing** ([Creating and editing content](../../../editing-content.md#relations-and-images)).
Several services relate to the same contact point.

To see which services use a contact point, open it and look at its **Relations** tab: **Reverse related objects** lists the objects that
relate to it (reverse relations). Check it before removing a contact point; the reverse relation check warns about
content that still points to an object being removed ([Reverse relation check](../../../../features/6.0/reverse-relation-check.md)).

## Showing the contact on the service page

In the service template:

```text
{if $node.data_map.contact.has_content}
  {def $contact = $node.data_map.contact.content}
  <h2>{'Contact'|i18n( 'design/<your design>' )}</h2>
  <p><strong>{$contact.name|wash}</strong></p>
  {attribute_view_gui attribute=$contact.data_map.opening_hours}
  {if $contact.data_map.changes.has_content}{attribute_view_gui attribute=$contact.data_map.changes}{/if}
  {undef $contact}
{/if}
```

The view cache of a service page is cleared when the service is published. When only the contact point changes, the
service pages that show it must be cleared too; the content cache manager clears related objects' caches according
to `viewcache.ini` settings, so test it: change the hours, then open a service page logged out
([Caches](../../../../guides/caches.md)).

## Changing hours and closures

| Change | Do |
|---|---|
| Permanent new hours | edit **Opening hours**, publish |
| A holiday or a short closure | fill **Temporary changes** ("Closed on <date>"), publish; empty it afterwards |
| An office closes for good | relate services to the new contact point, then hide the old one |

## Personal data

Contact points name desks, teams and shared mailboxes, not individual officials. Shared contact details survive staff
changes and avoid publishing personal data unnecessarily.

## Related pages

- [Building a service catalog](README.md)
- [Modeling services](modeling-services.md)
- [Relations and reuse](../../../creators/website-creators/relations-and-reuse.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
