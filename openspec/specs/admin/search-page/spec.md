# admin/search-page Specification

## Purpose
The administration's search results page: one place to search content, narrow the result with filters, sort it
and act on it, which also takes in the former advanced search.

## Requirements

### Requirement: Filter bar
The search page SHALL offer a filter bar with content classes (several at once, grouped by class group, with the
number of matches per class where the search engine gives it), a class attribute (when exactly one class is
chosen) and sections (several, with numbers where available).

#### Scenario: Narrow by class
- **WHEN** an editor searches "report" and chooses the classes "Article" and "File"
- **THEN** only articles and files that match "report" are listed

#### Scenario: Attribute only for one class
- **WHEN** two classes are chosen
- **THEN** the class attribute filter is not offered

### Requirement: Further filters
The filter bar SHALL also offer a modification period and a publication period (any time, last day, week, month,
year, or a custom range), an owner (by object id, login or e-mail address), a language, a subtree, the search mode
(all words, any word, exact phrase) and the page size.

#### Scenario: Last week
- **WHEN** an editor chooses "modified in the last 7 days"
- **THEN** only results modified in the last seven days are listed

### Requirement: Filter form
The filters SHALL work without scripts as a plain form. With scripts, applying SHALL be possible only after
something changed, and a reset SHALL clear every filter.

#### Scenario: Without scripts
- **WHEN** scripts are disabled and the editor changes a filter and submits the form
- **THEN** the page shows the filtered result

#### Scenario: Apply only after a change
- **WHEN** scripts are enabled and nothing in the filter bar changed
- **THEN** the apply button cannot be used

### Requirement: Only what the engine can do
Filters, sorting and numbers SHALL only be offered where the installation's search engine supports them. With
the built-in search engine the page SHALL NOT offer the modification period, owner, language, the "any word"
mode, the numbers per class and section, or sorting by relevance. A search with filters but without words SHALL
run only where the installation allows an empty search; otherwise the page SHALL ask for words.

#### Scenario: Built-in engine
- **WHEN** the installation uses the built-in search engine
- **THEN** the page offers classes, attribute, sections, publication period, subtree, phrase and page size, and none of the eZ Find-only filters

#### Scenario: Filters without words, empty search not allowed
- **WHEN** an editor chooses a class but types no words and the installation does not allow an empty search
- **THEN** no search runs and the page asks for words

### Requirement: Search modes
"All words" SHALL pass the typed text to the search engine as typed. "Any word" SHALL find items with any of the
words and SHALL keep words the user excluded (with NOT or a leading minus) excluded. "Exact phrase" SHALL find the
words as one phrase.

#### Scenario: Any word keeps an exclusion
- **WHEN** an editor searches "red car NOT bus" with "any word"
- **THEN** items with "red" or "car" are found, but none with "bus"

### Requirement: Active filters and sorting
Every active filter SHALL be shown as a chip above the result; removing a chip SHALL repeat the search without
that one filter. The result SHALL be sortable by relevance (where the engine has it), name ascending and
descending, modification and publication date newest and oldest first. Paging SHALL keep every filter and the
sort order.

#### Scenario: Remove one filter
- **WHEN** a search has the classes "Article" and "File" and the editor removes the chip "File"
- **THEN** the search repeats with "Article" only and every other filter unchanged

#### Scenario: Next page
- **WHEN** the editor opens page 2 of a filtered, sorted result
- **THEN** page 2 has the same filters and sort order

### Requirement: Result table
Each result SHALL show its class icon, its name as a link with its location path and the text around the words
(where the engine gives it), its modification date, its class, its translations (each a link) and an edit action
where the user may edit it. Selecting results for editing SHALL keep working. Where PDF pages are indexed, the
pages of a document that hold the words SHALL be listed under it.

#### Scenario: Translations
- **WHEN** a result exists in German and English
- **THEN** both languages are listed as links to the respective translation

### Requirement: Page states
The page SHALL state the words and the number of results, show a state before the first search and an empty state
with tips, and offer a corrected spelling where the engine suggests one.

#### Scenario: Nothing found
- **WHEN** a search finds no result
- **THEN** the page says so and shows tips for another search

### Requirement: Names only where readable
A subtree or an owner named in the filters or the chips SHALL be shown by name only when the user may read it;
otherwise the subtree SHALL be shown by its number without its path, and the owner SHALL be treated as not found.

#### Scenario: Subtree outside the user's permissions
- **WHEN** an editor opens the search with a subtree they may not read
- **THEN** the chip shows only the subtree's number, not its name or path

#### Scenario: Owner by e-mail address
- **WHEN** an editor enters the e-mail address of a user account they may not read as owner
- **THEN** the owner is not found and no name is shown

### Requirement: Default subtree
Without a subtree in the request, the search SHALL be limited to the tree the siteaccess is restricted to (its path
prefix), as the search was before.

#### Scenario: Restricted siteaccess
- **WHEN** a siteaccess is limited to a subtree and the editor searches without choosing a subtree
- **THEN** only results inside that subtree are listed

### Requirement: Advanced search in the page
The former advanced search SHALL lead to the search page: a posted advanced search form SHALL show the search page
with the equivalent search (its class, class attribute, section, publication date, phrase and page size become the
page's filters), an old advanced search address SHALL open the search page, and every link and form of the page
SHALL lead to the search page.

#### Scenario: Posted advanced search form
- **WHEN** an advanced search form is posted with a class identifier and a phrase
- **THEN** the search page opens with that class chosen and the phrase searched

### Requirement: The search in the address
The search page SHALL keep its search in the address as view parameters (`/(name)/value`), so that a search can be
bookmarked and shared, and SHALL never write or read a query string (`?name=value`). Its forms SHALL be posted and
answered with a redirect to that address.

#### Scenario: Filters in the address
- **WHEN** an editor applies the words "easter" and two content types
- **THEN** the address is the search page followed by `/(text)/easter/(class)/` and the two type ids joined by a comma

#### Scenario: Query string
- **WHEN** an editor opens the search page with `?SearchText=easter`
- **THEN** the query string is ignored and no search runs

### Requirement: One search per page
A search page SHALL run its search once. The search statistics SHALL record the words with the number of results
the page showed, with the filters applied.

#### Scenario: Statistics count the filtered result
- **WHEN** an editor searches "report" with the class "Article" and 4 of 30 matches are articles
- **THEN** the search statistics record "report" with 4 results

### Requirement: Same page with the eZ Find package
With the eZ Find package installed, the classic design admin SHALL show the same header suggestions and the same
search page once the package hands over to the kernel; an older package SHALL keep its own page.

#### Scenario: Classic design with eZ Find
- **WHEN** the classic design admin runs with an eZ Find package that hands over to the kernel
- **THEN** the header field suggests while typing and the search page has the filter bar
