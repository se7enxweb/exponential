<?php /* #?ini charset="utf-8"?

# The online editor's engine settings in the Exp Debug bar (settings/debugbar.ini)

[DebugBarSettings]
Settings[]=ezoe_editor_engine
Settings[]=ezoe_engine_switch

[Setting_ezoe_editor_engine]
File=ezoe.ini
Block=EditorSettings
Variable=EditorEngine
Type=enum
Values[]
Values[]=tinymce3
Values[]=tinymce8
Label=Online editor engine
Group=extensions
Help=The editor of ezxmltext fields for every user without a preference of their own (a user can choose with ezoe/engine).

[Setting_ezoe_engine_switch]
File=ezoe.ini
Block=EditorSettings
Variable=EngineSwitch
Type=bool
Label=Editor engine switch
Group=extensions
Help=A button below the editor lets editors switch between the registered engines.

*/ ?>
