{let content=$attribute.content
     classContent=$attribute.class_content
     available_options=$classContent.options}
{section show=and(is_set($classContent.db_options),count($classContent.db_options)|gt(0))}
    {set available_options=$classContent.db_options}
{/section}
{set-block scope=root variable=pdf_text}{section var=option loop=$available_options}{section-exclude match=$content|contains($option.item.identifier)|not}{$option.item.name|wash}{delimiter}{cond($classContent.delimiter|ne(""),$classContent.delimiter,", ")}{/delimiter}{/section}{/set-block}
{pdf(text, $pdf_text|wash(pdf))}
{/let}
