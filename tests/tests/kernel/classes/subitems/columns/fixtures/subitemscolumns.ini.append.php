<?php /* #?ini charset="utf-8"?
# The three worked examples of doc/bc/6.0/subitems-table-options.md ("Adding a column in
# 3 minutes"), exactly as the guide prints them. expSubitemsGuideExamplesTest loads this file.

[Column_mydaysonline]
Name=Days online
Group=Custom
Type=number
Handler=expSubitemsColumnHandlers::daysOnline
SortField=published
Align=right
Order=5000
Description=Whole days since the item was first published.

[Column_myreadingtime]
Name=Reading time
Group=Custom
Type=number
Class=expSubitemsReadingTimeColumn
WordsPerMinute=250
Align=right
Order=5010
Description=Minutes a reader needs for the main text.

[Column_mystatus]
Name=Status
Group=Custom
Type=html
Template=design:subitems/columns/statusbadge.tpl
Order=5020
Description=What visitors see of the item.
*/ ?>
