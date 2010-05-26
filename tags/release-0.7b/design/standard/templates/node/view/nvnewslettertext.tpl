{attribute_view_gui attribute=$node.data_map.plain_text_content}

{* Required stuff *}
{def $view_link = nvnewslettergetviewlink( $node.contentobject_id, $node.contentobject_version )
     $links = nvnewslettergetsitelink( $node.contentobject_id, $node.contentobject_version )
     $nvn_site_url = $links[0] 
     $nvn_file_url = $links[1]
     $nvn_tracker_url = concat( $links[0], '/nvnewsletter/viewlink/', $node.contentobject_id, '/?lnk=' )}
     
{* Use in any template $#site_url or $#file_url *}
{set scope='global' $nvn_site_url=$nvn_site_url}
{set scope='global' $nvn_file_url=$nvn_file_url}
{set scope='global' $nvn_tracker_url=$nvn_tracker_url}
<br /><br />
View newsletter in browser<br />
{$view_link}/<NVN_USER_CODE>
<br /><br />
Unsubscribe<br />
{$nvn_site_url}/nvnewsletter/unsubscribe/{$node.contentobject_id}/<NVN_USER_CODE>