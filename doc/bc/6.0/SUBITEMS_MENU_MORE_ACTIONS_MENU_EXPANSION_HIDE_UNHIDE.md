# Subitems More actions expansion for Hide selected and Unhide selected

**Area:** Admin3 subitems list
**Compatibility level:** Backward-compatible behavior expansion
**Status:** Implemented

## What changed

The admin3 subitems list now exposes two additional entries in the `More actions` menu:

* `Hide selected`
* `Unhide selected`

These actions behave like the existing bulk actions in the same menu. They submit the selected node IDs through the legacy `content/action` module flow and redirect back to the parent view after the operation completes.

## Runtime path

The live execution path is the `content` module override provided by `extension/nxc_powercontent`. The kernel `content/action.php` implementation contains the same hide/unhide branch, but the extension override is the path used on this site.

The handler accepts either `HideButton` or `UnhideButton`, inspects `SelectedIDArray`, and applies hide state changes through the legacy content operation layer.

## Verification

Regression coverage was added for the active action path in PHPUnit under the kernel content test suite. The test creates a real node, posts the hide and unhide actions, and verifies both the redirect behavior and the node's hidden state.

## Notes for maintainers

If this menu expansion is modified again, keep the following pieces aligned:

* the admin3 label/template wiring for the menu entries
* the JavaScript post payload for the bulk action buttons
* the active `content/action` handler in `extension/nxc_powercontent`
* the kernel-level action branch for future compatibility
