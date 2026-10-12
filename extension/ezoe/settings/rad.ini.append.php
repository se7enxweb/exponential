<?php /* #?ini charset="utf-8"?

# The registry of the Online Editor for the extension point survey (setup/rad, setup/radsurvey/(show)/registries;
# doc/bc/6.0/rad-extension-surface.md "Registries declared in rad.ini"; the format is described in settings/rad.ini).
# Every engine of ezoe.ini [EditorSettings] Engines[<id>]=<class> (ezoe's TinyMCE 3 and 8, and those other
# extensions add, such as exp_oe_tiptap's Tiptap) is a class implementing expOEEditorEngine.
# No empty "Registries[]" line: it would clear the kernel's list.
[RegistrySettings]
Registries[]=ezoeengines

[Registry_ezoeengines]
Title=Online editor engines
Description=The editors the Online Editor offers (ezoe.ini [EditorSettings] Engines[<id>]=<class>), each a class implementing expOEEditorEngine.
Doc=doc/bc/6.0/ezoe-tinymce8.md
Ini=ezoe.ini
Section=EditorSettings
Entries[Engines]=expOEEditorEngine

*/ ?>
