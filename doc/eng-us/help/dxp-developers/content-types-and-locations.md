---
title: Content, content types and locations
audience: dxp-developer
category: dxp-development
order: 17
summary: The content model of the DXP for developers - content types and fields, content items, versions, translations, locations in the tree, sections, object states, users, roles and policies - with the repository services that handle each and the Exponential 6 names for comparison.
keywords: content model, content type, field definition, content item, version, draft, translation, location, tree, section, object state, role, policy, repository, ContentService, LocationService
type: help
---
# Content, content types and locations

This page is for developers who model content on the DXP. It names the parts of the content model, the repository
service that handles each part in PHP, and the Exponential 6 term for readers who know that kernel. The services
listed are the interfaces in `Ibexa\Contracts\Core\Repository` of the installed core
(`se7enxweb/exponential-platform-dxp-core`).

## Contents

1. [The parts of the model](#the-parts-of-the-model)
2. [Versions and translations](#versions-and-translations)
3. [The tree](#the-tree)
4. [Access](#access)
5. [Working with the repository in PHP](#working-with-the-repository-in-php)
6. [Modeling advice](#modeling-advice)

## The parts of the model

| Part | What it is | Service | In Exponential 6 |
|---|---|---|---|
| Content type | A definition with fields, grouped in content type groups | `ContentTypeService` | content class |
| Field definition | One field of a content type, with a field type and settings | `ContentTypeService` | class attribute |
| Content item | One piece of content of a content type | `ContentService` | content object |
| Field | A value of a field definition on a content item | `ContentService` | object attribute |
| Location | A place of a content item in the tree | `LocationService` | node |
| Section | A label for access and design rules | `SectionService` | section |
| Object state | A state in a state group (for example locked, unlocked) | `ObjectStateService` | object state |
| URL alias | A readable URL for a location | `URLAliasService` | URL alias |

The field types available are on [The field types it ships](the-field-types-it-ships.md).

## Versions and translations

Every change of a content item creates a version. A version is a draft until it is published; the previously
published version is archived. `exponential:content:cleanup-versions` prunes old drafts and archived versions
([Console commands](console-commands-and-renames.md)).

Each version holds one or more translations. The languages of the repository are managed with `LanguageService`;
which languages a siteaccess shows is its `languages` setting ([Siteaccesses](siteaccesses-site-and-admin.md)).
The admin configuration in an installed project notes that content and content types start in `eng-GB`.

## The tree

Content items are placed in a tree of locations. A content item can have several locations; one is its main
location. Moving, copying, hiding and trashing work on locations (`LocationService`, `TrashService`).
`exponential:copy-subtree <source> <target>` copies a subtree from the command line.

## Access

Users and user groups are content items (`UserService`). Roles are sets of policies (module and function, with
limitations), assigned to users or groups, optionally limited to a subtree or a section (`RoleService`,
`PermissionResolver`). Console commands take a `--user` option and act with that user's permissions.

## Working with the repository in PHP

Inject the services into your own service in `src/`, never fetch them from the container by hand:

```php
<?php

namespace App\Service;

use Ibexa\Contracts\Core\Repository\ContentService;
use Ibexa\Contracts\Core\Repository\LocationService;

final class ArticleReader
{
    public function __construct(
        private readonly ContentService $contentService,
        private readonly LocationService $locationService,
    ) {
    }

    public function titleOfLocation(int $locationId): string
    {
        $location = $this->locationService->loadLocation($locationId);
        $content = $this->contentService->loadContent($location->getContentId());

        return (string) $content->getFieldValue('title');
    }
}
```

The interfaces are upstream API: their full documentation is the upstream line's public API reference, which the DXP
keeps compatible because the forks keep the namespaces ([The se7enxweb forks](the-se7enxweb-forks-and-replace.md#namespaces-stay-the-same)).

## Modeling advice

- Model by meaning, not by page: an "Event" type with a date field, not a "Right column box".
- Prefer relations to copying: a relation field to an author content item instead of a text field with a name.
- Keep identifiers stable; templates, GraphQL types and API clients use them. The GraphQL schema is generated from
  your content types, so regenerate it after every change ([GraphQL](graphql-and-graphiql.md)).
- After a content type change, reindex if search uses the changed fields ([Reindexing](reindexing.md)).

## Related pages

- [The field types it ships](the-field-types-it-ships.md)
- [Twig functions you use daily](twig-functions-you-use-daily.md)
- [Content classes for builders (Exponential 6)](../extension-development/part-2-builder/content-classes-for-builders.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
