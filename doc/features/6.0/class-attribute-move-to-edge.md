# Content classes: move an attribute to the top or the bottom

In the class editor each attribute already had up and down arrows. Since 29
September 2026 each also has **move to the top** and **move to the bottom**
buttons, and moving works reliably again.

## Use it

1. Open **Setup > Classes**, choose a class group and edit a class.
2. Next to the arrows of an attribute click **move to the top** or **move to the
   bottom**.
3. The row moves at once; positions and priorities are renumbered, the server
   stores the move, and a move the server did not store is put back with a
   message. With JavaScript off the buttons post the form like the arrows do.
4. After every move the priority fields follow the rows, so **Apply** and
   **OK** store exactly the order you see.

For developers: `eZContentClassAttribute::moveToEdge()` puts the attribute first
or last and numbers the others 1 to n in their old order; `class/edit` handles
`MoveTop` and `MoveBottom` both in place and as a form post. The in-place answers
carry `X-Class-Attribute-Moved: 1` and the page accepts a move only with that
header: a server that does not handle the action answers an ordinary page (200),
which used to be taken as a stored move.

## Three faults fixed in the same release

| Fault | Cause | Fix |
|---|---|---|
| Arrows reloaded the whole page, only sometimes worked. | The page script called jQuery's `.size()`, removed in jQuery 3 (the admin loads 3.7.1); it threw before the move buttons were set up. It worked only where an older jQuery or jQuery Migrate was loaded. | `.length` instead of `.size()` here, in the translation step of content edit (`edit_languages.tpl`) and in the relations script. |
| Page and stored order drifted apart. | A move the server did not confirm was not undone. | A refused, failed or unanswered move is put back and the page says so. Position numbers and row colours follow the rows. The request sends the `X-CSRF-Token` header as well as the form token. |
| An attribute did not move, or two others swapped. | Imported or old classes can have duplicate or missing placements; `move()` swapped by placement with nothing to tell ties apart. | The class version's placements are renumbered 1 to n (by placement, then id) first when they are not already, and the move requests load the attribute's id. |

Verified with a class whose attributes had tied and gapped placements: the
attribute moved exactly one place and the placements were 1 to n afterwards.

## Related pages

- [jQuery 4 and removal of YUI](jquery4-and-yui-removal.md)
- [Chronicle, 16 to 30 September 2026](../../history/2026/2026-09b.md)
