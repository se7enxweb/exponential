# Spec Delta

## Purpose

Suggests what the user is looking for while they type in the administration's header search field, so that a
content item, a user, a class or an administration page is one keystroke away instead of a full search.

## ADDED Requirements

### Requirement: Suggestions while typing
When the suggestions are switched on, the header search field of the administration SHALL show a list of
suggestions under the field once the user has typed at least the configured number of characters (three by
default) and paused. A newer input SHALL replace the answer of an older one. The field SHALL stay an ordinary
search field: submitting it SHALL run the full search.

#### Scenario: Suggestions appear after three characters
- **WHEN** a signed-in editor types "exp" into the header search field and pauses
- **THEN** a list of suggestions opens under the field

#### Scenario: Fewer characters show nothing
- **WHEN** the editor has typed two characters
- **THEN** no list is shown and no suggestions are asked for

#### Scenario: Switched off
- **WHEN** the suggestions are switched off for the installation
- **THEN** the field is a plain search field and asking for suggestions directly answers "not found"

### Requirement: Groups
The suggestions SHALL be grouped, in a configurable order and with a configurable limit per group:
content items (name, content class with its icon, location path), pages of documents that hold the words,
user accounts (by login, e-mail address or name), content classes (by name or identifier) and pages of the
administration's menus (named by their menu path, for example "Setup › Cache management").

#### Scenario: Content and a class
- **WHEN** an administrator types "folder"
- **THEN** the content group lists items whose text matches and the class group lists the class "Folder"

### Requirement: Highlighting and all results
The typed words SHALL be highlighted in each suggestion. The list SHALL end with an entry that opens all results,
with their number.

#### Scenario: Highlighted words
- **WHEN** the user types "cache"
- **THEN** the word "Cache" in the suggestion "Setup › Cache management" is highlighted

#### Scenario: All results
- **WHEN** the user chooses the last entry of the list
- **THEN** the search results page opens for the typed words, and the entry shows how many results there are

### Requirement: Only what the user may see
Every suggestion SHALL be something the current user may open: content they may read, user accounts they may
read, content classes only for a user who may open the class list, administration pages only those whose
module and view the user may open. An anonymous user SHALL get no suggestions. A user without permission to
read content SHALL get no suggestion field at all.

#### Scenario: Content outside the user's permissions
- **WHEN** an editor without read access to a subtree types a word that only occurs in that subtree
- **THEN** no item of that subtree is suggested

#### Scenario: No class module access
- **WHEN** an editor who may not open the class list types a class name
- **THEN** no class group is shown

#### Scenario: Anonymous request
- **WHEN** the suggestions are asked for without a signed-in user
- **THEN** the answer is refused and carries no suggestion

### Requirement: Document pages with fallback
Where the search engine indexes the pages of PDF documents, the suggestions SHALL include the pages that hold
the words (document name and page number, opening the document at that page), a limited number per document
and in total. Where it does not, a document SHALL be suggested as a whole among the content items.

#### Scenario: Pages indexed
- **WHEN** the words occur on page 3 of a PDF document and its pages are indexed
- **THEN** a suggestion names the document and page 3 and opens the document there

#### Scenario: Pages not indexed
- **WHEN** the installation does not index PDF pages
- **THEN** the document appears once in the content group and no document page group is shown

### Requirement: Search engine fallback
The suggestions SHALL use eZ Find when it is the installation's search engine and the built-in search engine
otherwise, also when eZ Find is configured but cannot be loaded. They SHALL need no eZ Find feature beyond the
package published upstream.

#### Scenario: Without eZ Find
- **WHEN** the installation uses the built-in search engine
- **THEN** content suggestions come from the built-in search and the other groups work as with eZ Find

### Requirement: Keyboard and screen readers
The field SHALL behave as a combobox: arrow keys move through the suggestions, Enter opens the chosen one (or
searches when none is chosen), Escape and a click outside close the list, and the number of suggestions is
announced. Ctrl+/ (Cmd+/ on a Mac) SHALL move the focus to the field from anywhere, in addition to "/".

#### Scenario: Choose with the keyboard
- **WHEN** the list is open and the user presses Down twice and Enter
- **THEN** the second suggestion opens

#### Scenario: Close with Escape
- **WHEN** the list is open and the user presses Escape
- **THEN** the list closes and the focus stays in the field
