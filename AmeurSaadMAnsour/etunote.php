<?php
//charge le flux rss dans un objet PHP
$xml = simplexml_load_file('https://picasaweb.google.com/data/feed/base/user/107401320610499259896/albumid/6065229773950541889?alt=rss&kind=photo&authkey=Gv1sRgCNak7e60l-7VlgE&hl=fr');
 {
	
	
	foreach ($xml->channel->item as $tof) {
	echo "<center>";
	echo "<table border ='10'>";
	echo "<tr>";
	echo "<td><img src='".$tof->enclosure["url"]."'/></td>";
	echo"</tr>";
	echo "<tr>";
	echo "<td>".$tof->title."</td>";
	echo"</tr>";
	echo"</table>";
	echo "</center>";}
}	

