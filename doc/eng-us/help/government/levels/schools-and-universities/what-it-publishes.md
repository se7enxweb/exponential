---
title: What schools and universities publish
audience: government, project-manager, website-creator
category: public-sector
order: 20
summary: The content public schools, school districts, colleges and universities typically publish - admissions, programs, calendars, news, policies, public profiles, staff and student information - and what each needs from Exponential.
keywords: school website, university website, admissions, programs, courses, academic calendar, policies, staff directory, student information, intranet, scope
type: help
---
# What schools and universities publish

This page is for project managers and website creators at a school, school district, college or university who define
the scope of a site. It lists what education institutions typically publish (general public-sector practice) and what
each kind of content needs.

## Contents

1. [Public content](#public-content)
2. [Content for staff and students](#content-for-staff-and-students)
3. [What the CMS must do](#what-the-cms-must-do)
4. [What belongs in other systems](#what-belongs-in-other-systems)
5. [Questions to settle](#questions-to-settle)

## Public content

| Content | Audience | Rhythm |
|---|---|---|
| Admissions: how to apply, deadlines, requirements, open days | prospective students, parents | yearly cycle with fixed deadlines |
| Programs and courses: what is taught, entry requirements, outcomes | prospective students | yearly |
| Departments, faculties, research groups | prospective students, researchers, partners | changes with staff |
| Academic calendar: terms, holidays, exams | everyone | yearly |
| News and events | community, press | weekly |
| Policies: admissions, conduct, complaints, safeguarding, privacy | parents, students, regulators | reviewed yearly |
| Reports: inspection results, performance information, annual report | parents, oversight bodies | yearly |
| People: public role profiles of staff (for universities, researchers) | the public | with staff changes |
| Jobs | applicants | continuous |

## Content for staff and students

| Content | Audience |
|---|---|
| Timetables and room changes | students, staff (often from another system) |
| Internal news and notices | staff, students |
| Forms and procedures (leave, expenses, IT requests) | staff |
| Handbooks: student handbook, staff handbook | students, staff |
| Department internal pages | department members |

## What the CMS must do

| Need | Exponential | Read |
|---|---|---|
| Department subsites with their own look | siteaccesses and designs on one installation | [Intranets and department subsites](intranets-and-department-subsites.md) |
| Restricted pages for staff and students | sections that Anonymous may not read; roles for members | [Sections, 13.1](../../../../guides/sections.md#131-a-members-area) |
| Deadlines that drive lists | date attributes and fetches | [Choosing datatypes](../../../creators/website-creators/choosing-datatypes.md) |
| Events with a calendar | the Event and Event calendar classes | [Content model](content-model.md) |
| Staff profiles in one place, reused | a Person class (`ng_person` ships with the default package) and relations | [Relations and reuse](../../../creators/website-creators/relations-and-reuse.md) |
| Search across many departments | built-in search or eZ Find with Solr | [Search with eZ Find and Solr](../../../creators/website-creators/search-with-ezfind-and-solr.md) |
| Handbooks in several levels | the Documentation page class (`documentation_page`) | [sevenx_classes](../../../site-packages/sevenx-multisite/sevenx-classes.md) |

## What belongs in other systems

| System | The website's part |
|---|---|
| Student information system (enrolment, grades) | links only |
| Learning management system (courses, assignments) | links only |
| Timetabling | links, or a feed if the system offers one |
| Library catalog | links ([Events and catalogs linked rather than built](../public-libraries/events-and-linked-catalogs.md)) |
| Admissions application | the program page links to it |

Exponential can authenticate against other systems with login handlers, including OAuth social login skeletons in
`sevenx_authentication_2fa` ([sevenx_authentication_2fa](../../../../features/6.0/extensions/sevenx_authentication_2fa.md));
connecting to an institution's identity provider is a development task, not a setting.

## Questions to settle

- [ ] How many departments want their own site, and who maintains each?
- [ ] Which content is for staff and students only, and how will they sign in?
- [ ] Where are photos of pupils allowed, and how is consent recorded?
- [ ] Which deadlines of the year drive the site (admissions, exams)?

## Related pages

- [Schools and universities](README.md)
- [Content model for schools and universities](content-model.md)
- [Planning your website](../../../creators/website-creators/planning-your-website.md)
- [Free documentation](../../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../../partners/partners-start-here.md) and [How to apply](../../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
