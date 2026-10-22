<?php /* #?ini charset="utf-8"?

[EditorSettings]
# The types the editor may store. A raster image is saved in the type it had
# when that type is on the list, otherwise as PNG; an SVG stays an SVG. Each
# type must also be on image.ini [ImageUploadSettings] AllowedMIMETypes[].
OutputMIMETypes[]
OutputMIMETypes[]=image/jpeg
OutputMIMETypes[]=image/png
OutputMIMETypes[]=image/webp
OutputMIMETypes[]=image/svg+xml
# Quality of a saved JPEG or WebP, 0.1 to 1
Quality=0.92
# The size of a new drawing, <width>x<height> in pixels: an image attribute
# without an image offers "New SVG drawing" (needs image/svg+xml here and in
# image.ini AllowedMIMETypes[])
NewDrawingSize=800x600
# Aspect ratios the crop tool offers, <width>:<height>, after those of the
# image aliases with a fixed shape (geometry/scaleexact or geometry/crop)
AspectRatios[]
AspectRatios[]=1:1
AspectRatios[]=4:3
AspectRatios[]=3:2
AspectRatios[]=16:9
# Images the watermark tool offers besides a text, as <label>;<design path>
# (resolved like ezdesign, a PNG with transparency works best).
# Example: Watermarks[]=Logo;images/watermarks/logo.png
Watermarks[]
# Fonts the text tools offer besides the system font (the watermark text of
# the raster editor, the text tool of the SVG editor), as
#   <label>;<font-family>;<design path>[;<outline path>]
# The design path (resolved like ezdesign, on this site) is the file the
# browser loads, .woff2, .woff, .ttf or .otf; the editor loads it with the
# FontFace API when it opens. <font-family> is the name the text carries in
# the drawing (letters, digits, spaces and hyphens).
# "Save text in added fonts as paths" (SVG editor, see TextToPaths) reads the
# glyph outlines with opentype.js, which reads .woff, .ttf and .otf but not
# .woff2: the outline path names such a file of the same font. Without it, a
# .woff/.ttf/.otf design path is used; a font given only as .woff2 is offered
# all the same, but its text stays text in the saved SVG.
# Sample font shipped with the extension (Lora Regular, Latin, SIL Open Font
# License 1.1, see fonts/expimageeditor/LICENSE):
# Fonts[]=Lora;Lora;fonts/expimageeditor/lora-latin-400-normal.woff2;fonts/expimageeditor/lora-latin-400-normal.woff
Fonts[]
# Whether "Save text in added fonts as paths" is ticked when the SVG editor
# opens (it is only offered when a font of Fonts[] can be converted). Ticked,
# a text in such a font is saved as its outlines (<path>, with the words as
# role="img", aria-label and <title>), so it looks the same where the font is
# not installed; the text stays text in the editor until saving. Texts in
# other fonts are never converted. enabled or disabled.
TextToPaths=enabled

*/ ?>
