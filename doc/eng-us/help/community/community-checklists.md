---
title: Community checklists
audience: community, advocate, supporter, developer
category: community
order: 8
summary: Checklists for Exponential community members to copy - before posting, before filing a bug, before a pull request, before answering, before publishing an extension, and before running an event.
keywords: checklist, checklists, before posting, bug, pull request, answer, extension, event, community
type: help
---
# Community checklists

Copy the one you need into your notes.

## Contents

1. [Before posting a question](#before-posting-a-question)
2. [Before filing a bug](#before-filing-a-bug)
3. [Before opening a pull request](#before-opening-a-pull-request)
4. [Before answering someone](#before-answering-someone)
5. [Before publishing an extension or theme](#before-publishing-an-extension-or-theme)
6. [Before running an event](#before-running-an-event)

## Before posting a question

```text
[ ] I searched the help, the documentation, eZpedia and the forums
[ ] I describe the goal, not only the error
[ ] Steps, expected and actual result, exact message
[ ] Environment from exp:info --plain or Copy report as text
[ ] No passwords, keys, server addresses or personal data
[ ] It is not a security problem (those go to security@se7enx.com)
```

Page: [Asking a good question](joining/asking-a-good-question.md).

## Before filing a bug

```text
[ ] It is reproducible on a current release or main
[ ] I searched the tracker; no existing issue
[ ] Right tracker: software / Velocity / extension / community
[ ] Title names the problem
[ ] Steps, expected, actual, environment, cleaned log lines
[ ] Nothing private in text or screenshots
```

Page: [Filing a bug](contributing/filing-a-bug.md).

## Before opening a pull request

```text
[ ] One change only
[ ] make hooks installed; hooks pass
[ ] Commit message starts with Added: / Updated: / Fixed: / Removed: / Renamed:
[ ] share/filelist.md5 updated (make manifest-fix)
[ ] Tests added that fail without the change
[ ] Coding standards and naming prefixes followed; no inline styles in admin templates
[ ] Documentation updated if behavior changed
[ ] AI-assisted parts checked and mentioned
[ ] Signed CLA on file (kernel); checkbox ticked
```

Page: [Contributing code](contributing/contributing-code.md).

## Before answering someone

```text
[ ] I understand their goal
[ ] I know the answer, or I say that I am not sure
[ ] I link the source
[ ] I say which version I checked
[ ] No sales pitch
[ ] Security problem? Point to the private channel instead
```

Page: [Answering questions](helping-others/answering-questions.md).

## Before publishing an extension or theme

```text
[ ] No secrets, customer data or site-specific settings
[ ] LICENSE file (GPL 2 or later recommended)
[ ] ezinfo.php and extension.xml carry the same version
[ ] composer.json; Packagist package; webhook
[ ] README: what it does, supported versions, how to install
[ ] Tag chosen from what is published, sorted by version; never moved later
[ ] Listed on Projects; announced in the forums
```

Page: [Sharing extensions and themes](contributing/sharing-extensions-and-themes.md).

## Before running an event

```text
[ ] Announced as community-run, not "official"
[ ] Code of conduct linked; contact person named
[ ] Accessibility checked
[ ] Demo on a test installation with invented data
[ ] Consent for photos, recordings and published names
[ ] Only the attendee data needed; deleted afterwards
```

Page: [Community safety and privacy](local-groups/community-safety-and-privacy.md).

## Related pages

- [Community: start here](community-start-here.md)
- [Advocate checklists](../advocates/advocate-checklists.md)
- [Event checklists](../advocates/event-checklists.md)
- [The release checklist](../extension-development/part-7-releasing/the-release-checklist.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
