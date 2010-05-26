{def $attribute_count = $attribute.content.options|count
     $url = array()}

{if gt( $attribute_count, 1 )}
<select id="selectSite" name="Attribute_{$attribute.id}">
{foreach $attribute.content.options as $key => $option}
    {set $url = $key|explode(';')}
    {set $url = $url[0]}
  <option value="{$key}"{if eq( $attribute.content.selected, $key)} selected="selected"{/if}>{$option}: {$url}</option>
{/foreach}
</select>
{else}
{foreach $attribute.content.options as $key => $option}
    {set $url = $key|explode(';')}
    {set $url = $url[0]}
  <p>{$option}: {$url}</p>
  <input type="hidden" value="{$key}" name="Attribute_{$attribute.id}" />
{/foreach}
{/if}