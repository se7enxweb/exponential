<?php /* #?ini charset="utf-8"?
# The full and line views of the help classes on the public site (delivery path 1 of the help system plan): one
# template for the four topic types, one for manuals and chapters. A site design with its own templates for these
# classes overrides these rules in its siteaccess's override.ini.

[full_help_concept]
Source=node/view/full.tpl
MatchFile=help/full_topic.tpl
Subdir=templates
Match[class_identifier]=help_concept

[full_help_task]
Source=node/view/full.tpl
MatchFile=help/full_topic.tpl
Subdir=templates
Match[class_identifier]=help_task

[full_help_reference]
Source=node/view/full.tpl
MatchFile=help/full_topic.tpl
Subdir=templates
Match[class_identifier]=help_reference

[full_help_troubleshooting]
Source=node/view/full.tpl
MatchFile=help/full_topic.tpl
Subdir=templates
Match[class_identifier]=help_troubleshooting

[full_help_manual]
Source=node/view/full.tpl
MatchFile=help/full_map.tpl
Subdir=templates
Match[class_identifier]=help_manual

[full_help_section]
Source=node/view/full.tpl
MatchFile=help/full_map.tpl
Subdir=templates
Match[class_identifier]=help_section

[line_help_concept]
Source=node/view/line.tpl
MatchFile=help/line.tpl
Subdir=templates
Match[class_identifier]=help_concept

[line_help_task]
Source=node/view/line.tpl
MatchFile=help/line.tpl
Subdir=templates
Match[class_identifier]=help_task

[line_help_reference]
Source=node/view/line.tpl
MatchFile=help/line.tpl
Subdir=templates
Match[class_identifier]=help_reference

[line_help_troubleshooting]
Source=node/view/line.tpl
MatchFile=help/line.tpl
Subdir=templates
Match[class_identifier]=help_troubleshooting

[line_help_manual]
Source=node/view/line.tpl
MatchFile=help/line.tpl
Subdir=templates
Match[class_identifier]=help_manual

[line_help_section]
Source=node/view/line.tpl
MatchFile=help/line.tpl
Subdir=templates
Match[class_identifier]=help_section
*/ ?>
