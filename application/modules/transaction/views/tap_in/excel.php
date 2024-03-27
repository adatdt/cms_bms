 <?php
 

 
 
 $output='<table border="1" width="100%">
 
      <thead>
 
           <tr>
 
                <th>Judul</th>
 
           </tr>
 
      </thead>
 
      <tbody>';
 
foreach($pri as $data) {
 
          $output .=' <tr>
 
                <td>'.$data->created_on.'</td></tr>';

 			}

      $output.='</tbody></table>';

 
  $output .= '</table>';
  header('Content-Type: application/xlsx');
  header('Content-Disposition: attachment; filename=download.xlsx');
  echo $output;
 ?>