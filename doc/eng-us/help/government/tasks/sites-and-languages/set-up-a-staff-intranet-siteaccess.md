---
title: Set up a staff intranet siteaccess
audience: government, admin
category: public-sector
order: 180
summary: Task for administrators: create an intranet siteaccess that requires sign-in for every page, with its own folder in a section visitors cannot read and a Staff role, and test it from outside and inside.
keywords: intranet, RequireUserLogin, staff siteaccess, AnonymousAccessList, section, Staff role, internal site
type: help
---
# Set up a staff intranet siteaccess

This task is for administrators. Background: [A staff-only siteaccess](../../building/intranets/a-staff-only-siteaccess.md)
and [Sections and roles for intranet content](../../building/intranets/sections-and-roles-for-intranet-content.md).

## Before you start

- An internal host name (for example `intranet.example.gov`), ideally reachable only from the internal network.
- A user group for staff.

## Steps

1. **Setup > Sections**: create "Intranet". Create the intranet folder and **Assign content** to the section.
2. Create the siteaccess `intranet` like a department site (folder as root node), and add:

   ```ini
   [SiteAccessSettings]
   RequireUserLogin=true
   AnonymousAccessList[]
   AnonymousAccessList[]=user/forgotpassword
   AnonymousAccessList[]=csp/report
   ```

3. Register and match the siteaccess (`AvailableSiteAccessList[]`, `HostMatchMapItems[]`).
4. **Users > Roles and policies**: a role "Staff" with `content/read` on sections Intranet and Standard and
   `user/login` for the intranet siteaccess; assign it to the staff group.
5. Add a `robots.txt` for the intranet host that disallows everything.
6. Clear the INI cache.

## Check

- A private browser window on the intranet host shows the sign-in page for every address.
- A test staff account opens intranet pages.
- On the public site, intranet pages do not open and are not found by search.

## Problems

| Problem | Fix |
|---|---|
| Visitors can register | `user/register` is still in `AnonymousAccessList[]` for the siteaccess |
| Staff see "access denied" | the Staff role lacks the Intranet section, or `user/login` for the siteaccess |

## Related pages

- [Staff intranets with Exponential](../../building/intranets/staff-intranets-with-exponential.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
