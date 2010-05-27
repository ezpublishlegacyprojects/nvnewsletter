<input type="hidden" name="Attribute_array_{$attribute.id}" value="" />
{foreach $attribute.content.options as $key => $option}
  <input type="checkbox" name="Attribute_array_{$attribute.id}[]" value="{$key}"{if eq( $attribute.content.selected.$key, '1')} checked="checked"{/if} /> {$option}<br />
{/foreach}
