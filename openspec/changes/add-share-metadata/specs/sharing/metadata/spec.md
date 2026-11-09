# Spec Delta

## Purpose

Every page carries the metadata that decides how its link looks when shared, from one place, and editors see the
result before they publish.

## ADDED Requirements

### Requirement: Metadata of a page
A page of a content object SHALL carry Open Graph and Twitter Card metadata: title, description, image, type,
canonical URL, site name and language. Each value SHALL come from the attributes configured for the content class,
in the configured order of fallbacks, and finally from site defaults.

#### Scenario: Article with a share title
- **WHEN** an article has a share title, a meta title and a name
- **THEN** the shared title is the share title

#### Scenario: Article without a share title
- **WHEN** the share title is empty and the meta title is set
- **THEN** the shared title is the meta title

#### Scenario: Language version
- **WHEN** the English version of an article is shared
- **THEN** title, description and language are those of the English version

### Requirement: Image
The shared image SHALL be the configured image of the content in an image variant large enough for a large card.
When the content has no such image, the site's default share image SHALL be used.

#### Scenario: No image
- **WHEN** a page without an image is shared
- **THEN** the site's default share image is shown

### Requirement: One template for all designs
The metadata SHALL be written by one template that site designs include in their page head; a design that does not
include it SHALL keep its own behavior.

#### Scenario: Bundled designs
- **WHEN** a page is rendered in any of the bundled site designs
- **THEN** the page head contains each metadata tag exactly once, including the image tag

### Requirement: Preview in the administration
admin4 SHALL show, for a content object and language, how its link will look when shared, as a large and a small
card, with a warning for a missing image, a missing description, and a title or description longer than the
networks show.

#### Scenario: Teaser too long
- **WHEN** an editor opens the share preview of an article whose description has 400 characters
- **THEN** the preview shows the shortened description and a warning
