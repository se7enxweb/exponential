# The package wizard

This page is for administrators who create packages in the administration, and for anyone who changes the wizard's
templates. Since October 2026 the package creation wizard (**Setup > Packages > Create new package**,
`/package/create`) is a step wizard drawn with the shared components of the administration: it shows where you are,
asks for each part in a card with labels and help, shows an error at the field it belongs to, and lets you review
the whole package before it is written. What the wizard posts, what the creators read and the package it writes are
unchanged.

## Creating a package

1. **Setup > Packages > Create new package** lists the wizards your role may use, each with what it exports:

   | Wizard | Exports | Steps |
   |---|---|---|
   | Content class export (`ezcontentclass`) | the definitions of content classes, no content | Content classes to include, Package information, Package maintainer, Package changelog |
   | Content object export (`ezcontentobject`) | nodes or subtrees with their content, optionally their classes and templates | Content objects to include, Content object limits, Package information, Package maintainer, Package changelog |
   | Extension export (`ezextension`) | the files of extensions | Extensions to include, Package information, Package maintainer, Package changelog |
   | Site style (`ezstyle`) | two stylesheets, their images and a thumbnail | Package thumbnail, CSS files, Image files, Package information, Package maintainer, Package changelog |

   Choose one and press **Start the wizard**. The Maintainer step is left out when your role may give no maintainer
   role. Extension export needs exporting switched on first (`package.ini [ExtensionExportSettings]`, see
   [Extension list and downloads](extension-list-and-downloads.md)); until then every extension is listed but cannot
   be chosen, with the reason.
2. Each step shows the list of steps at the top (the current one marked, the ones behind you ticked), "Step n of m",
   the step's name and what it asks for. At the bottom: **Back**, the primary action and **Cancel**.
   - **Continue** checks the step and goes on. When something is wrong the step stays, a summary at the top lists the
     errors (each a link to its field), and the field shows its error under it.
   - **Back** keeps what you entered (when it is valid) and returns one step; from the first step it returns to the
     choice of wizard.
   - **Cancel** returns to the package list; nothing is written.
   - Enter in a field is Continue, never Back or a step's own button.
3. The last step (Changelog) shows the **Review**: the package's name, summary, version, format, license, host,
   packager and maintainer, and what it carries (the classes, the nodes with the options, the extensions or the
   files). **Create package** writes it into the `local` repository and opens its page, which has **Download**,
   **Install** and the other actions.

The pickers:

- **Content classes**: a table of every class with a checkbox, its identifier and groups, and "n of m selected". A
  filter above it narrows the list by name or identifier (a script; without it the whole list shows). A class hidden
  by the filter keeps its tick.
- **Content objects**: the chosen nodes and subtrees in a table, each with its class and URL. **Add node** and
  **Add subtree** open the content browser and return with the choice; **Remove selected** takes the ticked rows out
  (with nothing ticked it removes nothing).
- **Options** (content object export): class definitions and templates (with the siteaccesses whose templates go
  with it, as chips), the versions, the languages (chips) and how node assignments and related objects are kept.
- **Extensions**: a table with version, last change and details (a disclosure per extension); one that may not be
  exported is shown disabled with its reason.
- **Image files** (site style): **Add image** adds the chosen file and stays on the step; **Continue** without a file
  goes on.

## The templates

Every step is one template of `design/<design>/templates/package/`, the same file in `design/admin4` (admin4 and
admin4l) and `design/admin` (the classic administration):

| Template | Holds |
|---|---|
| `create.tpl` | the choice of wizard |
| `create/wizard_head.tpl` | the hidden fields every step posts and the page title |
| `create/wizard_progress.tpl` | the steps (`ol.a4-steps`, `aria-current="step"`), "Step n of m", the step's heading and intro, the error summary; parameters `intro`, `field_ids`, `field_labels` |
| `create/wizard_actions.tpl` | the review on the last step, Back, Continue or Create package, Cancel |
| `create/wizard_review.tpl` | the review (reads `$persistent_data`) |
| `create/wizard_pick_script.tpl` | the count and filter of the pickers |
| `create/info.tpl`, `maintainer.tpl`, `changelog.tpl`, `thumbnail.tpl` | the steps every creator shares |
| `creators/<creator>/*.tpl` | the creators' own steps, and the content browser's head (`browse_node.tpl`, `browse_subtree.tpl`) |

A step template opens the frame (`form.a4-page.a4-package-wizard` > `.context-block`), includes `wizard_head.tpl`,
opens `box-bc`, includes `wizard_progress.tpl`, draws its cards and includes `wizard_actions.tpl`; every template
closes the frames it opens. An error a creator reports names its field (`'field' => ezpI18n::tr( 'kernel/package',
'Package name' )`); the step maps that name to the id of its control through `field_ids`, so the summary links to
the control, and the control gets `aria-invalid="true"` and `aria-describedby` with the error's id.

The installers still use `package/install_header.tpl` and `package/navigator.tpl`; `package/header.tpl` and
`create/error.tpl` stay for overrides that include them.

### What is posted

Nothing changed. The names, as the creators read them:

| Step | Names |
|---|---|
| every step | `CreatorItemID`, `CreatorStepID`, `PackageStep`, `NextStepButton`, `PreviousStepButton` |
| choice of wizard | `CreatorItemID`, `CreatePackageButton` |
| Package information | `PackageName`, `PackageSummary`, `PackageDescription`, `PackageVersion`, `PackageFormat`, `PackageLicence`, `PackageHost`, `PackagePackager` |
| Maintainer | `PackageMaintainerPerson`, `PackageMaintainerEmail`, `PackageMaintainerRole` |
| Changelog | `PackageChangelogPerson`, `PackageChangelogEmail`, `PackageChangelogText` |
| Thumbnail | `PackageThumbnail` (file) |
| Content classes | `ClassList[]` (checkboxes now; a multiple select before, the same list) |
| Content objects | `AddNode`, `AddSubtree`, `RemoveSelected`, `DeleteIDArray[]` |
| Options | `IncludeClasses`, `IncludeTemplates`, `SiteAccesses[]`, `VersionExport`, `Languages[]`, `NodeAssignment`, `RelatedObjects` (siteaccesses and languages are checkboxes now; the same lists) |
| Extensions | `PackageExtensionNames[]` |
| CSS files | `PackageSiteCSSFile`, `PackageClassesCSSFile` (files) |
| Image files | `PackageImageFile` (file); **Add image** posts `NextStepButton` like Continue |

The required fields carry `aria-required`, not `required`: the server checks them and its messages are the ones
shown, in the interface language, and Back is never stopped by the browser.

### Styles

The shared components (`.a4-card`, `.a4-steps`, `.a4-field`, `.a4-table`, `.a4-filterbar`, `.a4-chips`, `.a4-radios`,
`.a4-toolbar`, `.a4-facts`, `.a4-notice`) and, for what they cannot do, the `.a4-package-*` rules of
`stylesheets/modules/structure.css` (the package section; identical in both designs): the step list scrolls in one row
below 600px and shows only the current step's name, fields stand two by two from 640px, Cancel sits at the end of
the action bar, the review's two groups stand side by side. Tokens only, no inline CSS
([Styling the administration](../../bc/6.0/admin4-styling.md)).

`tests/tests/design/AdminPackageWizardTest.php` checks that the two designs carry the same files, that every step
uses the head, progress and actions, and that every name of the table above is still posted.
