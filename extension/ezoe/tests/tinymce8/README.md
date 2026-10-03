# Browser tests of the eZ Online Editor on TinyMCE 8

End to end tests of the TinyMCE 8 editor of ezoe (`ezoe.ini [EditorSettings] EditorEngine=tinymce8`)
against a running installation. A real browser (Firefox or Chrome) is driven with mouse clicks and key
presses through the toolbar, the status bar path, the context menus and the dialogs; the results are
checked in the editor and, with a database, in the stored ezxml.

Every test opens a **new draft** of a configured object, sets its own test content, and discards the
draft at the end. Existing drafts are never edited. Objects created by the upload test are removed again
through the admin interface (without trash).

## Requirements

- Node.js 18 or newer
- Firefox (WebDriver BiDi) or Chrome installed
- An admin login of the installation and its demo content (or matching settings below)
- Optional: the `sqlite3` command line tool and an sqlite database for the ezxml checks

## Run

```sh
cd extension/ezoe/tests/tinymce8
npm install
npm test                  # all tests
npm test -- link table    # only some tests (file names without number and .js)
```

Exit code 0 when all checks pass, 1 when a check failed. Screenshots of failed steps go to
`screenshots/`.

## Tests

| Test | Covers |
|---|---|
| `roundtrip` | objects opened and stored unchanged with TinyMCE 3 and TinyMCE 8 give byte-identical ezxml (needs `EZ_DB`) |
| `embed` | search with class filter, image centered, existing embed by double click (properties only, class, align, preview), path click, switch object via browse, images in table cells, stored ezxml |
| `upload` | invalid file types rejected, chosen file shown and name suggested, image embedded right away with alternative text, PDF via "Upload local file" continuing on properties |
| `link` | external link with title, target and view, edit via path with browse and paging, search with class filter as object link, anchors, link without selection, mailto, context toolbar, stored ezxml |
| `customtag` | inline tag on a word, block tag around a paragraph with attribute, edit via path, remove via context toolbar, empty block tag with placeholder |
| `literal` | multi line code with html characters, paragraph after a new literal, edit via path, plain text paste, remove |
| `table` | insert with size, width, class and custom attributes, th applied to a row, width applied to a column, validation, row dialog, table toolbar |
| `general` | class of paragraph, heading, strong, emphasize, list item (or the notice without configured classes), cancel, kept alignment, context menu |
| `path-and-switch` | ezxml names in the status bar path, path clicks open the dialogs, o2k7 look, engine switch keeping the text (when `EngineSwitch=enabled`) |

## Configuration

Environment variables, the defaults fit the local development installation:

| Variable | Default | Meaning |
|---|---|---|
| `EZ_BASE_URL` | `http://localhost84/exponential6/admin` | admin siteaccess |
| `EZ_USER` | `admin` | login |
| `EZ_PASSWORD` / `EZ_PASSWORD_FILE` | `var/log/initial-admin-password` | password, or a file with a line `Password: …` |
| `EZ_BROWSER` | `firefox` | `firefox` or `chrome` |
| `EZ_BROWSER_PATH` | `/usr/bin/firefox` or `/usr/bin/google-chrome` | browser executable |
| `EZ_HEADLESS` | `1` | `0` shows the browser window |
| `EZ_OBJECT` | `91` | object with an ezxmltext attribute, drafts of it are edited |
| `EZ_LANGUAGE` | `eng-US` | language of the drafts |
| `EZ_ROUNDTRIP_OBJECTS` | `91,92,114,135` | objects of the roundtrip test |
| `EZ_DB` | `var/storage/sqlite3/exponential6.db` if it exists | sqlite database for the ezxml checks; empty to skip them |
| `EZ_IMAGE_SEARCH` / `EZ_IMAGE_CLASS` | `unsplash` / `Image` | search term and class name of images |
| `EZ_OBJECT_SEARCH` / `EZ_OBJECT_CLASS` | `recipe` / `Recipe` | search term and class name of other objects |
| `EZ_EMBED_OBJECT` | `95` | object embedded in the test content |
| `EZ_SCREENSHOTS` | `screenshots/` | directory for screenshots of failed steps |

The tests set the user preference `ezoe_engine` to choose the editor. With `EZ_DB` it is removed again
after the run, so `ezoe.ini [EditorSettings] EditorEngine` applies again; without it the preference stays
on `tinymce8` for the test user.

Tests depending on configuration skip the parts that are not configured, e.g. the general tag dialog
checks classes only for tags with `AvailableClasses`, the engine switch only with `EngineSwitch=enabled`.
