# ezautosave: drafts that save themselves

This page is for editors who lose work when a browser closes, and for administrators who tune autosave. `ezautosave`
saves the draft of the object you are editing automatically, and adds an inline preview to the admin edit page. It is
based on QH Autosave.

## What it does

- Saves the draft every `Interval` seconds.
- Saves it when you leave a form field after changing something (`TrackUserInput`).
- Hides the **Store draft** button (`HideStoreDraftButton`), which is no longer needed.
- Tries to save when the editor leaves the edit page unexpectedly (back button, closing the browser).
- Gives the admin edit form an inline preview of the draft.

Nothing needs to be done by the editor: edit as usual, and the draft is stored.

## Settings

| File | Block | Key | Default | Meaning |
|---|---|---|---|---|
| `autosave.ini` | `AutosaveSettings` | `Interval` | `180` | Seconds between two automatic saves |
| `autosave.ini` | `AutosaveSettings` | `TrackUserInput` | `enabled` | Save when the user leaves a field that changed; when `disabled`, only the interval saves |
| `autosave.ini` | `AutosaveSettings` | `HideStoreDraftButton` | `enabled` | Hide the **Store draft** button |
| `autosave.ini` | `AutosaveSettings` | `HidePreviewLink` | `disabled` | Hide the preview link |
| `autosave.ini` | `BrowserWorkarounds` | (Internet Explorer 11) | | Autosave is disabled in IE 11 when the form has a password field, because IE 11 cannot post a form to an iframe while a password field has focus |

## What changed in the Exponential 6 releases

| Version | Date | Change |
|---|---|---|
| 6.0.1 to 6.0.4 | | Version, license (`GPL-2.0-or-later` in `composer.json`), German translations; the description names Exponential. |
| 6.0.4, 6.0.5 | 30 September to 1 October 2026 | Autosave and the draft preview run on Exponential UI's `exp::autosave` when `expui` is active, in the admin and in the ezwebin templates, with the YUI version as the fallback. See below. Requires `se7enxweb/expui ^1.0.0.1`. |
| 6.0.6 | 2 October 2026 | The admin edit form's YUI autosave and preview are removed. `design.ini` no longer adds `ezautosubmit.js` and `ezcontentpreview.js` to every admin page (with YUI gone from the admin they stopped the scripts after them in the packed file). The preview link's arrows are drawn in the stylesheet. |
| 6.0.7 | | The front-end YUI autosave is removed too: the ezwebin design's autosave template keeps only its Exponential UI code (`exp::autosave`, with `exp_config()`), and `ezautosubmit.js` is gone. The README documents `Exp.autosave.AutoSubmit`. |

About 6.0.4 and 6.0.5: the same form is posted to the same address with the same fields, the same messages and the
same hidden **Store draft** button. The preview opens, saves first and closes the same way. The rich text editor is
asked to save into the form first, so a draft is no longer saved twice after the editor rewrote its textarea.

## For developers

- `Exp.autosave.AutoSubmit` submits a form at an interval, or when something changed, to an ezjscore call. It
  triggers the events `init`, `beforesave`, `success`, `error` and `abort`.
- The admin's preview pane is `Exp.autosave.Preview`.
- The edit templates (`design/admin` and `design/ezwebin`, `content/edit/autosave.tpl`) show both in use.

See [YUI removal](../../../bc/6.0/yui-removal.md).

## Related pages

- [jQuery 4 and YUI removal](../jquery4-and-yui-removal.md)
- [Online editor on TinyMCE 8](../online-editor-tinymce8.md)
- [Chronicle](../../../history/extensions/ezautosave.md) and [release notes](../../../changelogs/extensions/ezautosave.md)
- [Change ledger](../../../history/ledger/ezautosave.md)
- [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
- Months: [2026-09](../../../history/extensions/months/2026-09.md), [2026-10](../../../history/extensions/months/2026-10.md) (all extensions)
