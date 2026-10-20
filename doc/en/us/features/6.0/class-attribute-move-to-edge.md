# Content classes: move an attribute to the top or the bottom

This page is for administrators who edit content classes. Since 29 September 2026 each attribute in the class
editor has **move to the top** and **move to the bottom** buttons next to its up and down arrows, and moving works
reliably again.

## Use it

1. Open **Setup > Classes**, choose a class group and edit a class.
2. Next to the arrows of an attribute, click **move to the top** or **move to the bottom**.
3. The row moves at once. Positions and priorities are renumbered and the server stores the move. If the server
   does not store it, the row is put back and a message says so.
4. Click **Apply** or **OK**. The priority fields follow the rows after every move, so the order you see is the
   order that is stored.

With JavaScript off, the buttons post the form, like the arrows do.

## For developers

- `eZContentClassAttribute::moveToEdge()` puts the attribute first or last and numbers the others 1 to n in their
  old order.
- `class/edit` handles `MoveTop` and `MoveBottom`, both in place and as a form post.
- In-place answers carry the header `X-Class-Attribute-Moved: 1`. The page accepts a move only with that header. A
  server that does not handle the action answers an ordinary page (200), which used to be taken as a stored move.

## Three faults fixed in the same release

| Fault | Cause | Fix |
|---|---|---|
| The arrows reloaded the whole page and only sometimes worked. | The page script called jQuery's `.size()`, removed in jQuery 3 (the admin loads 3.7.1); it threw before the move buttons were set up. It worked only where an older jQuery or jQuery Migrate was loaded. | `.length` instead of `.size()` here, in the translation step of content edit (`edit_languages.tpl`) and in the relations script. |
| The page and the stored order drifted apart. | A move the server did not confirm was not undone. | A refused, failed or unanswered move is put back and the page says so. Position numbers and row colours follow the rows. The request sends the `X-CSRF-Token` header as well as the form token. |
| An attribute did not move, or two others swapped. | Imported or old classes can have duplicate or missing placements; `move()` swapped by placement with nothing to tell ties apart. | The class version's placements are first renumbered 1 to n (by placement, then id) when they are not already, and the move requests load the attribute's id. |

Verified with a class whose attributes had tied and gapped placements: the attribute moved exactly one place, and
the placements were 1 to n afterwards.

## Related pages

- [jQuery 4 and removal of YUI](jquery4-and-yui-removal.md)
- [Behaviour changes, 16 to 30 September 2026](../../bc/6.0/behaviour-changes-2026-09b.md)
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
- [Chronicle, 16 to 30 September 2026](../../history/2026/2026-09b.md)
