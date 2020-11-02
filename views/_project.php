<?php // Created by Mariia Nema
    //include 'dbconnection.php';
    $db = OpenCon();

    // Getting projects, its leader and areas 
    if (!($statement = $db->prepare(
        "CALL GetProjectByID (?)"))) {
        die("Prepare failed: (" . $db->errno . ")" . $db->error);
    }
    $statement->bind_param("i", $_GET["id"]);

    if(!$statement->execute()) {
        die("Execute failed: (" . $statement->errno . ") " . $statement->error);
    }

    $result = $statement->get_result();
    $statement->close();

    if ($result->num_rows > 0) {
	    $proj=$result->fetch_assoc(); 
    }

    else {
	    header('Location: projects.php');
	    die();
    }
    $result->close();
    $db->next_result();

    // Getting project members 
    if (!($statement = $db->prepare(
        "CALL GetProjectMembers (?)"))) {
        die("Prepare failed: (" . $db->errno . ")" . $db->error);
    }
    $statement->bind_param("i", $_GET["id"]);

    if(!$statement->execute()) {
        die("Execute failed: (" . $statement->errno . ") " . $statement->error);
    }

    $result = $statement->get_result();
    $statement->close();

    if($result->num_rows>0) {
        $results = $result->fetch_all(MYSQLI_ASSOC);
        $members = array();

        foreach ($results as $value) {
            $members[] = array(
                'member_id' => $value['id'],
                'member_fname'=>$value['first_name'],
                'member_lname'=>$value['last_name'],
                'member_title'=>$value['type'],
                'member_photo'=> $value['avatar']
            );
        }
    }            

    $result->close();
    $db->next_result();

    // Getting project publications 
    if (!($statement = $db->prepare(
        "CALL GetProjectPublications (?)"))) {
        die("Prepare failed: (" . $db->errno . ")" . $db->error);
    }
    $statement->bind_param("i", $_GET["id"]);

    if(!$statement->execute()) {
        die("Execute failed: (" . $statement->errno . ") " . $statement->error);
    }

    $result = $statement->get_result();
    $statement->close();

    if($result->num_rows>0) {
        $results = $result->fetch_all(MYSQLI_ASSOC);
        $publications = array();

        foreach ($results as $value) {
            $publications[] = array(
                'publ_id' => $value['id'],
                'abstract'=>$value['abstract'],
                'publ_title'=>$value['title'],
                'publ_type'=>$value['type'],
                'publ_description'=> $value['description'],
                'publ_date'=>$value['date'],
                'publ_file'=>$value['file_name'],
                'publ_authors'=>array()
            );
        }
    
        $result->close();
        $db->next_result();    

        foreach($publications as & $publication) {
            
            // Getting publicationauthors 
            if (!($statement = $db->prepare( "CALL GetPublicationAuthors (?)"))) {
            die("Prepare failed: (" . $db->errno . ")" . $db->error); }

            $statement->bind_param("i", $publication["publ_id"]);

            if(!$statement->execute()) {
                die("Execute failed: (" . $statement->errno . ") " . $statement->error);
            }

            $result = $statement->get_result();
            $statement->close();

            if($result->num_rows>0) {
                $results = $result->fetch_all(MYSQLI_ASSOC);            
                $authors = array();

                foreach ($results as $value) {                
                    $authors[] = array(
                        'member_id' => $value['id'],
                        'author'=>$value['author']
                    );
                }
                $publication['publ_authors']=$authors;             
            }
            $result->close();
            $db->next_result();         
        }       
    }   

    CloseCon($db);
?>
      
    <!--APPLICATION MODAL-->        
    <div class="modal fade" id ="ApplyModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="d-flex justify-content-center">Describe your interest</h1>
                </div>
                <div class="modal-body">
                    <textarea class="w-100"  rows="7"  type="text" id="modalInput" maxlength="300" onkeyup="showCharsLeft(this,'counter',300)"></textarea>
                    <small>(<span id="counter">300</span> signs left)</small>
                    <div class="alert alert-danger fade" role="alert" id="modalError">Something went wrong. Try again later </div>
                    <div class="alert alert-success fade" role="alert" id="modalSuccess">Your application is sent</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" id="SendButton" onclick="sendEmailtoLeader('<?=$proj['email']?>','<?=$proj['title']?>')">Send</button>
                    <button type="button" class="btn btn-info" data-dismiss="modal" onclick="closeModal()">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">         
        <!--PROJECT DESCRIPTION AND RESULTS-->
        <div class="col-sm-12 col-lg-6 m-0 order-lg-2">
            <div class="card p-3 mb-2" id="card-description">
	            <h2 class=" d-flex justify-content-center"><?=$proj['title']?></h2>
	            <ul class="nav nav-tabs m-2" role="tablist">
	                <li class="nav-item">
		                <a class="nav-link text-info active" href="#about" role="tab" data-toggle="tab" id="about-tab" aria-controls="nav-about" aria-selected="true"><strong>About</strong></a>
	                </li>
                    <?php if($proj['result']!=NULL) { ?>
	                    <li class="nav-item">
		                    <a class="nav-link text-info " href="#results"  role="tab" data-toggle="tab" id="results-tab" aria-controls="nav-results" aria-selected="false"><strong>Results</strong></a> 
	                    </li>	  
                    <?php } ?>
	            </ul>	
	            <div class ="tab-content" style="padding: 4mm">
		            <div role = "tabpanel" class="tab-pane fade show active overflow-auto" aria-labelledby="about-tab" id="about">
			            <div><?=$proj['full_description']?></div>
		            </div>
                    <?php if($proj['result']!=NULL) { ?>
		                <div role = "tabpanel" class="tab-pane fade" aria-labelledby="results-tab" id="results">
                            <div><?=$proj['result']?></div>
		                </div>
                    <?php } ?>
	            </div>
            </div>
        </div>        

	    <!--LEADER-->
        <div class="col-sm-12 col-lg-3 order-lg-3">
	        <div id="project-leader" class="card p-2 mb-2">
                <div class="card-header">
                    <h3 class="d-flex justify-content-center">Leader</h3>
                </div>
                <div id ="avatar" class="card-img-top">
                    <div class="d-flex justify-content-center">
                        <img class ="img-thumbnail" src="./uploads/<?=$proj['avatar']?>"/>
                    </div>
                </div>
                <div class="card-body">
                    <h5 class="mb-2">
                        <a class="text-info" href="memberinfo.php?id=<?=$proj['leader']?>"> <?=$proj['first_name']?> <?=$proj['last_name']?></a>
                    </h5>
                    <p class="mb-1"><span class="leader_email"><?=$proj['email']?></span></p>
                    <p class="mb-1"><span class="leader_phone"><?=$proj['phone']?></span></p> 
                </div>
            </div>	 
            
            <!--MEMBERS-->
            <?php if (isset ($members)) { ?>
            <div id="members" class="card p-2 mb-2">
                <div class="card-header">
                    <h3 class="mt-2 d-flex justify-content-center">Participants</h3>
                </div>
                    <div class="overflow-scroll vh-25 mb-4 ">                
                        <ul class="list-group list-unstyled">
                            <?php foreach($members as $member) { ?>
                                <li class="list-group-item b-1 p-1 style="height:3rem;">
                                    <div class="d-inline-block overflow-hidden align-middle" style="height:3rem; width:3rem" >
                                        <img class="img-thumbnail fit-cover border-0" src="./uploads/<?=$member['member_photo']?>"/>
                                    </div>
                                    <div class="media-body d-inline-block align-middle"">
                                        <a class="stretched-link text-info" href="memberinfo.php?id=<?=$member['member_id']?>" >
                                            <?=$member['member_fname']?> <?=$member['member_lname']?>
                                        </a>
                                        <p class="m-0" style="color:black"><?=$member['member_title']?></p>                                  
                                    </div>
                                </li>         
                            <?php } ?>
                        </ul>
                    </div>
                
                </div>     
            <?php } ?>
            <?php if(isset($_SESSION['usr'])) { ?>    
            <?php if ($proj['status']!=='finish') {?>    
                <button data-toggle="modal" data-target="#ApplyModal" class="btn btn-info btn-block mb-2" id="ApplyButton">Apply</button>
            <?php }} ?>           
        </div>

        <!--DETAILS-->
	    <div class="col-sm-12 col-lg-3 order-lg-1">
		    <div class="card p-2 mb-2" id="details">
                <div class="card-header">
                    <h3 class="d-flex justify-content-center">Details</h3>  
                </div>
                <p>
                    <span><i class="fa fa-unlock"></i></span>
                    <span><?=$proj['date_start']?>
                </p>
                <?php if ($proj['status']!='active') {?>
                    <p>
                        <span><i class="fa fa-lock"></i></span>
                        <span><?=$proj['date_end']?>
                    </p>                   
                <?php } ?>               
            </div>        
            
            <!--PUBLICATIONS-->
            <?php if (isset($publications)) { ?>       
                <div id="publications" class="card p-2 mb-2">
                    <div class="card-header">
                        <h3 class="d-flex justify-content-center">Publications</h3>
                    </div>
                        <ul class="list-unstyled list-group">                       
                            <?php foreach($publications as & $publication) { ?>                          
                                <li class="border-bottom mb-1"> 
                                    <h6 class="mt-1"><?=$publication['publ_title']?></h6>
                                    <?php if (file_exists($_SERVER['DOCUMENT_ROOT']."/publications/".$publication['publ_file']) ){ ?>
                                        <span class="float-right">                                            
                                            <a href="../publications/<?=$publication['publ_file']?>" download>
                                                <img src="/css/download.svg" onmouseover="this.src='/css/downloadHover.svg'" onmouseout="this.src='/css/download.svg'"/>
                                            </a>
                                        </span>
                                    <?php } ?>                                
                                
                                    <?php foreach ($publication['publ_authors'] as $author) {?>                                    
                                        <?php if ( $author['member_id']!==null) {?>
                                            <a class="text-info" href="memberinfo.php?id=<?=$author['member_id']?>">
                                                <small><?=$author['author']?></small>
                                            </a>
                                        <?php } else { ?>
                                            <small><?=$author['author']?></small>
                                        <?php } ?> 
                                    <?php } ?>   
                                    <small class="m-0 d-block"><?=$publication['publ_type']?></small>
                                    <small class="d-block"><?=$publication['publ_description']?></small>  
                                </li>                             
                            <?php } ?>                
                        </ul>              
                </div>
            <?php } ?>              
	    </div>
    </div>
    <script src="javascripts/projectpage.js"></script> 
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>


	

	


