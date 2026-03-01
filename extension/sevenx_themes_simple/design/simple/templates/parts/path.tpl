  <!-- Path content: START -->
  {if $path_array|count|ge(2)}
  <p>
  {foreach $path_array as $path}
  {if $path.url}
    <a href={cond( is_set( $path.url_alias ), $path.url_alias,
                                        $path.url )|ezurl}>{$path.text|wash}</a>
  {else}
    <span class="path-text">{$path.text|wash}</span>
  {/if}
  {delimiter}<span class="path-separator">/</span>{/delimiter}
  {/foreach}
  </p>
  {/if}
  <!-- Path content: END -->
