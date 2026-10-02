{* Subitems list column "Teaser" (Column_teaser): the first words of the item's intro, or of its
   description or body when it has no intro, without markup. Variables: $node (the row),
   $column (the column's settings: Attributes[] = identifiers to try, Length = characters),
   $key (its key). An XML text block is rendered (its output is HTML, already escaped) and its
   tags stripped; a plain text block is shortened and escaped here.
   Guide: doc/bc/6.0/subitems-table-options.md *}
{def $exp_teaser_map = $node.data_map
     $exp_teaser_text = ''
     $exp_teaser_length = first_set( $column.Length, 120 )
     $exp_teaser_ids = first_set( $column.Attributes, array( 'intro', 'short_description', 'description', 'body' ) )}
{foreach $exp_teaser_ids as $exp_teaser_id}
    {if and( $exp_teaser_text|eq( '' ), is_set( $exp_teaser_map[$exp_teaser_id] ) )}
        {if $exp_teaser_map[$exp_teaser_id].has_content}
            {if $exp_teaser_map[$exp_teaser_id].data_type_string|eq( 'ezxmltext' )}
                {set $exp_teaser_text = $exp_teaser_map[$exp_teaser_id].content.output.output_text|strip_tags|trim|shorten( $exp_teaser_length )}
            {else}
                {set $exp_teaser_text = $exp_teaser_map[$exp_teaser_id].data_text|strip_tags|trim|shorten( $exp_teaser_length )|wash}
            {/if}
        {/if}
    {/if}
{/foreach}
{if $exp_teaser_text|ne( '' )}<span class="exp-subitems-teaser">{$exp_teaser_text}</span>{/if}
{undef $exp_teaser_map $exp_teaser_text $exp_teaser_length $exp_teaser_ids}
