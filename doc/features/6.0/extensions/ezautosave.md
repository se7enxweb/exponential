# ezautosave: drafts that save themselves

`ezautosave` saves the draft of the object you are editing automatically and transparently, and adds an
inline preview from the edit page in the admin. It is based on QH Autosave. Editors stop losing work when the
browser closes, a laptop sleeps or the session dies.

## What it does

* Saves the draft every `Interval` seconds.
* Saves it when you leave a form field after changing something (`TrackUserInput`).
* Hides the **Store draft** button (`HideStoreDraftButton`) because it is no longer needed.
* Tries to save when the editor unexpectedly leaves the edit page (back button, closing the browser).
* Gives the admin edit form an inline preview of the draft.

## Settings (`autosave.ini`)

| Block | Key | Default | Meaning |
|---|---|---|---|
| AutosaveSettings | `Interval` | `180` | Seconds between two automatic saves |
| AutosaveSettings | `TrackUserInput` | `enabled` | Save when the user leaves a field that changed; when `disabled` only the interval saves |
| AutosaveSettings | `HideStoreDraftButton` | `enabled` | Hide the **Store draft** button |
| AutosaveSettings | `HidePreviewLink` | `disabled` | Hide the preview link |
| BrowserWorkarounds | (Internet Explorer 11) | | Autosave is disabled in IE 11 when the form has a password field, because IE 11 cannot post a form to an iframe while a password field has focus |

## What changed in the Exponential 6 releases

* 6.0.4 to 6.0.5 (30 September to 1 October 2026): autosave and the draft preview run on Exponential UI's
  `exp::autosave` when `expui` is active, in the admin and in the ezwebin templates, and keep the YUI version as
  the fallback. The same form is posted to the same address with the same fields, the same messages and the same
  hidden **Store draft** button; the preview opens, saves first and closes the same way. The rich text editor is
  asked to save into the form first, so a draft is no longer saved twice after the editor rewrote its textarea. The
  extension requires `se7enxweb/expui ^1.0.0.1`.
* 6.0.6 (2 October): the admin edit form's YUI autosave and preview are removed; `design.ini` no longer adds
  `ezautosubmit.js` and `ezcontentpreview.js` to every admin page (with YUI gone from the admin they stopped the scripts
  after them in the packed file), and the preview link's arrows are drawn in the stylesheet.
* 6.0.7: the front-end YUI autosave is removed too; the ezwebin design's autosave template keeps only its Exponential
  UI code (`exp::autosave`, with `exp_config()`) and `ezautosubmit.js` is gone. The README documents
  `Exp.autosave.AutoSubmit`.
* 6.0.1 to 6.0.4: version, license (`GPL-2.0-or-later` in `composer.json`), German translations and the description
  name Exponential.

## For developers

`Exp.autosave.AutoSubmit` submits a form at an interval, or when something changed, to an ezjscore call and
triggers the events `init`, `beforesave`, `success`, `error` and `abort`. The admin's preview pane is
`Exp.autosave.Preview`. The edit templates (`design/admin` and `design/ezwebin`, `content/edit/autosave.tpl`) show both in
use. See [YUI removal](../../../bc/6.0/yui-removal.md).

## Related

* [Chronicle](../../../history/extensions/ezautosave.md) and [release notes](../../../changelogs/extensions/ezautosave.md)
* [Change ledger](../../../history/ledger/ezautosave.md)
* [Behaviour changes of the extensions](../../../bc/6.0/extensions-behaviour-changes.md)
* [Month: 2026-09 (all extensions)](../../../history/extensions/months/2026-09.md)
* [Month: 2026-10 (all extensions)](../../../history/extensions/months/2026-10.md)
