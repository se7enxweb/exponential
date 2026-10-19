---
title: "The package creator wizard, step by step"
audience: designer, admin
category: theme-extensions
order: 380
featured: true
summary: Walk through Setup > Packages > Create new package (/package/create) - choosing a wizard, the pickers, package information, maintainer, changelog, the review and Create package - and what you get at the end.
keywords: package creator wizard, package/create, create new package, content class export, content object export, extension export, site style, package information, maintainer, changelog, review
type: help
---
# The package creator wizard, step by step

This page is for designers who pack classes, content or a theme into an Exponential package in the browser. Nothing
on the site changes while you use the wizard; it only writes a new package into the `local` repository.

## Before you start

- The `package/create` policy (limited by type, creator and role); without it the button is greyed out.
- For an extension export: exporting switched on ([Exporting an extension as a package](extension-export-package.md)).

## Step by step

1. Open **Setup > Packages** (`/package/list`) and press **Create new package** (`/package/create`).
2. Choose a wizard and press **Start the wizard**:

![Create package: the wizards Content class export, Content object export, Extension export and Site style, with Start the wizard](../../images/designers/package-create.webp)

   | Wizard | Exports | Its own steps |
   |---|---|---|
   | Content class export | Class definitions | Content classes to include |
   | Content object export | Nodes or subtrees with content | Content objects to include, Content object limits |
   | Extension export | Files of extensions | Extensions to include |
   | Site style | Two stylesheets, images, a thumbnail | Package thumbnail, CSS files, Image files |

3. Each step shows the list of steps at the top, "Step n of m", its name and what it asks for. Use **Continue**,
   **Back** (keeps what you entered) and **Cancel** (writes nothing). Enter in a field is Continue.
4. Do the wizard's own steps. The class picker has a filter and "n of m selected"; the content picker has
   **Add node**, **Add subtree** and **Remove selected**; the options step sets classes, templates, siteaccesses,
   versions and languages.
5. **Package information**: name (lower case letters, digits and `_`), summary, description, version, format
   (Classic XML or JSON), license ([Choosing a license](package-licenses-in-the-wizard.md)), host and packager.
6. **Package maintainer**: person, email and role. This step is left out when your role may give no maintainer
   role. Use a public contact address.
7. **Package changelog**: who and what. The last step shows the **Review** of the whole package.
8. Press **Create package**. The package's page opens with **Download**, **Install** and the other actions.

When something is wrong, the step stays, a summary at the top lists the errors as links, and each field shows its
error under it.

## After creating

- **Download** writes `<name>-<version>.ezpkg` (or **Download as JSON**).
- **Validate** on the package's page checks it as an upload would be checked
  ([Validating a package](validating-a-package.md)).
- To install it elsewhere, see [Installing your package on a test site](installing-your-package-on-a-test-site.md).

## Related pages

- [The package wizard](../../../features/6.0/package-wizard.md)
- [Packages, section 8](../../../guides/packages.md#8-creating-a-package)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
