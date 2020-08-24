<?php

$cert = "/root/.config/lxc/client.crt";
$key = "/root/.config/lxc/client.key";

if (isset($_GET['remote']))
  $remote = filter_var(urldecode($_GET['remote']), FILTER_SANITIZE_STRING);
if (isset($_GET['project']))
  $project = filter_var(urldecode($_GET['project']), FILTER_SANITIZE_STRING);
if (isset($_GET['instance']))
  $instance = filter_var(urldecode($_GET['instance']), FILTER_SANITIZE_STRING);


  echo "<thead>";
  echo "<tr>";
  echo "<th style='width:75px'></th>";
  echo "<th>Name</th>";
  echo "<th>Connect</th>";
  echo "<th>Listen</th>";
  echo "<th>Type</th>";
  echo "<th style='width:75px'></th>";
  echo "</tr>";
  echo "</thead>";

  echo "<tbody>";



$db = new SQLite3('/var/lxdware/data/sqlite/lxdware.sqlite');
$db_statement = $db->prepare('SELECT * FROM lxd_hosts WHERE id = :id LIMIT 1;');
$db_statement->bindValue(':id', $remote);
$db_results = $db_statement->execute();

while($row = $db_results->fetchArray()){

  $url = "https://" . $row['host'] . ":" . $row['port'] . "/1.0/instances/" . $instance . "?project=" . $project;
  $remote_data = shell_exec("sudo curl -k -L --cert $cert --key $key -X GET $url");
  $remote_data = json_decode($remote_data, true);
  $device_names = $remote_data['metadata']['expanded_devices'];
  foreach ($device_names as $device_name => $device_data){
    if ($device_data['type'] == "proxy"){
      echo "<tr>";

      echo "<td> <i class='fas fa-exchange-alt fa-lg' style='color:#4e73df'></i> </td>";
      echo "<td>" . htmlentities($device_name) . "</td>";
      echo "<td>" . htmlentities($device_data['connect']) . "</td>";
      echo "<td>" . htmlentities($device_data['listen']) . "</td>";
      echo "<td>" . htmlentities($device_data['type']) . "</td>";
      
    
    
      echo "<td>";
        echo '<div class="dropdown no-arrow">';
        echo '<a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        echo '<i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>';
        echo '</a>';
        echo '<div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink">';
        echo '<div class="dropdown-header">Options:</div>';
        //echo '<a class="dropdown-item" href="#" onclick="detachProfile(' . escapeshellarg($network_name) . ')">Detach</a>';
        echo '</div>';
        echo '</div>';
      echo "</td>";
      
      echo "</tr>";
    }
    else {
      continue;
    }
  }



  
}

echo "</tbody>";


?>