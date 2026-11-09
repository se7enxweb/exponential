# Spec Delta

## Purpose

Visitors can share a page to common networks without the page loading anything from those networks.

## ADDED Requirements

### Requirement: Share links without third-party scripts
A design SHALL be able to show share links for e-mail, Mastodon, Bluesky, LinkedIn and copying the link.
The page SHALL load no script, style, image or frame from a network for them; a network SHALL be contacted only
when the visitor clicks its link.

#### Scenario: Page view
- **WHEN** a visitor opens an article with share links
- **THEN** the browser makes no request to any of the networks

#### Scenario: Share to Bluesky
- **WHEN** the visitor clicks "Bluesky"
- **THEN** Bluesky's compose page opens with the title and the link of the article

### Requirement: What each link carries
The e-mail, Mastodon and Bluesky links SHALL carry the title and the address of the page; the LinkedIn link SHALL
carry the address, from which LinkedIn reads the share metadata; copying SHALL put the address on the clipboard.

#### Scenario: Share to LinkedIn
- **WHEN** the visitor clicks "LinkedIn"
- **THEN** LinkedIn's share page opens with the address of the article and shows its title and image from the share metadata

#### Scenario: Copy for a messenger
- **WHEN** the visitor clicks "Copy link"
- **THEN** the address of the article is on the clipboard and the page confirms it

### Requirement: Mastodon instance
The Mastodon link SHALL ask the visitor for their own instance, remember it in the visitor's browser only, and open
the compose page of that instance.

#### Scenario: First share
- **WHEN** a visitor clicks "Mastodon" for the first time and enters "mastodon.social"
- **THEN** the compose page of mastodon.social opens with the title and the link

#### Scenario: Next share
- **WHEN** the same visitor clicks "Mastodon" on another page
- **THEN** the compose page of mastodon.social opens without asking again

### Requirement: Configurable networks
The site SHALL be able to choose which networks are offered and in which order.

#### Scenario: Without LinkedIn
- **WHEN** an administrator removes LinkedIn from the offered networks
- **THEN** no page shows a LinkedIn link
