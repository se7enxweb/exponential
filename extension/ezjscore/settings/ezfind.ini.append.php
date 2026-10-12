<?php /* #?ini charset="utf-8"?

[SearchSettings]
# The order of the results of the ezjscore search (ezjscServerFunctionsJs::search(),
# the ezjscore call ezjsc::search) when eZ Find is the search engine.
# SortBy[<field>]=asc|desc, several entries for several fields in order.
# The fields are those of eZ Find's sort_by: published, modified, score, name,
# class_identifier, path, section_id, or an attribute (class/attribute).
# Default: newest first, the order this search has always used. The legacy
# search engine is not affected.
# With thanks to Mugo Web (LoveStack, mugoweb/ezpublish-legacy, commits
# 3d2d337b0d and 3af1ec3aee).
SortBy[]
SortBy[published]=desc
#SortBy[score]=desc

*/ ?>
