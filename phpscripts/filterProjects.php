<?php // Created by Mariia Nema 
  $data = json_decode(file_get_contents('php://input'), true);
    
    include '../dbconnection.php';
    $db = OpenCon();
    
    if (!($statement = $db->prepare(
        'CALL GetFilteredProjects(?,?,?,?,?,?,?,?,?,?)'))) {
        
        die("Prepare failed: (" . $db->errno . ")" . $db->error);
    }
    
    $statement->bind_param('ssssssiiss',
    $data['status'], 
    $data['search_expr'], 
    $data['project_start_from'], 
    $data['project_start_to'],
    $data['project_end_from'],
    $data['project_end_to'],
    $data['limit'], 
    $data['start_id'],
    $data['areas_array'],
    $data['direction']);  
  
    if(!$statement->execute()) {    
        die("Execute failed: (" . $statement->errno . ") " . $statement->error);
    }
    
    $result = $statement->get_result();   
   
    $statement->close();
    
    $answer= array();   
    $rows_number=$result->num_rows;    
    $answer[]=array('num_rows'=>$rows_number);

    if($rows_number>0) {
        
        $projects = $result->fetch_all(MYSQLI_ASSOC);       
        $output = array();

        foreach ($projects as $value) {
            $output[] = array(
                'id' => $value['id'],
                'title'=>$value['title'],
                'short_description'=>$value['short_description'],
                'status'=>$value['status'],
                'date_start'=>$value['date_start'],
                'date_end'=>$value['date_end'],
                'all_areas'=>$value['all_areas'],
                'leader'=>$value['leader'],
                'first_name'=>$value['first_name'],
                'last_name'=>$value['last_name'],
                'type'=>$value['type']
            );
        }
        $answer[]=array('proj'=>$output);         
    }   
             
    echo json_encode($answer);
    CloseCon($db); 

?>
