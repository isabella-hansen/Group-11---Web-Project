
  
 <?php  //Created By Mariia Nema 
   include '../dbconnection.php';
    $db = OpenCon();

    // Getting members 
    if (!($statement = $db->prepare('CALL AdminGetMembers()'))) {
        die("Prepare failed: (" . $db->errno . ")" . $db->error);
    }

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
                'name' => $value["name"],
                'id' => $value["id"]
            );
        }
    }     
     $result->close();
    $db->next_result();

    // Getting areas     
    if (!($statement = $db->prepare('CALL GetAreas()'))) {
        die("Prepare failed: (" . $db->errno . ")" . $db->error);
    }

    if(!$statement->execute()) {
        die("Execute failed: (" . $statement->errno . ") " . $statement->error);
    }

    $result = $statement->get_result();
    $statement->close();

    if($result->num_rows>0) {
        $results = $result->fetch_all(MYSQLI_ASSOC);
        $areas = array();

        foreach ($results as $value) {
            $areas[] = array(
            'type' => $value["type"],
            'id' => $value["id"]
            );
        }
    }        
    CloseCon($db);
?>      

<!--Edit a project-->
<div class="col-md-12 order-md-1 m-auto p-0" style="color: black">
     <!--ALERT-->   
     <div class="modal fade" id ="alert">
        <div class="modal-dialog modal-sm p-0">
            <div class="modal-content">                                  
                <div class="modal-body p-0">  
                    <div class="alert m-0" role="alert" id="modalAlert"></div>
                </div>
            </div>
        </div>
    </div>    
    
    <div class="row mb-2">
        <!--OPERATION-->
        <div class="col-sm-6">
            <h4 class="float-left" style="color: white">Create project</h4>
        </div>      
    </div>
    
    <!--TITLE-->
    <div class="row mb-3">
        <div class="col-sm-12">
            <form id="title-form" class="needs-validation" novalidate >   
                <div class="input-group" id="div-title">                        
                    <input type="text" class="form-control" id="title" required="required" maxlength="150" onkeyup="showCharsLeft(this,'title_counter',150)" name="title">
                    <span class="input-group-prepend">
                        <div class="input-group-text">Title</div>
                    </span>                
                    <div class="invalid-feedback"> Title is required.</div>                    
                </div>
                <div class="w-100" style="color:white"><small class="float-right">(<span id="title_counter">150</span> signs left)</small></div>
            </form>
        </div>
    </div>
    
    <div class="row mb-3">
    <!--LEADER-->
        <div  class="col-md-6 col-sm-12 mb-3">
            <form id="leader-form" class="needs-validation" novalidate>
                <div class="input-group">
                    <select class="custom-select" id="leader_admin" required="required" name="leader">
                        <option selected value=""></option>
                        <?php foreach($members as $member) {?>
                            <option value="<?=$member['id']?>"><?=$member['name']?></option>                                                    
                        <?php } ?>
                    </select>
                    <div class="input-group-append">
                        <label class="input-group-text" for="leader_admin">Leader</label>
                    </div>                
                    <div class="invalid-feedback">Choose leader</div>       
                </div>
            </form>  
        </div>

        <!--DATE-->
        <div class="col-md-6 col-sm-12 mb-3">
            <form id="date-form" class="needs-validation" novalidate>                 
                <div class="input-group" id="div-date-start">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="date_start"><i class="fa fa-unlock"></i></label>
                    </div>
                    <input type="text" class="date-picker form-control float-right readonly" id="date_start"   required="required" name="date_start" style="cursor:pointer; background-color: #FFFFFF">
                    <span class="input-group-append">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </span> 
                    <div class="invalid-feedback">Choose start date</div>       
                </div>            
            </form>
        </div>
    </div>

    <div class="row mb-3">
        <!--SHORT DESCRIPTION-->
        <div  class="col-sm-12" rows="5" >
            <form id="short-description-form">
                <div class="input-group w-100">                        
                    <textarea placeholder="Short description..." class="form-control" id="short_description" maxlength="300" required="required" onkeyup="showCharsLeft(this,'descr_counter',300)" name="short_description"></textarea>
                </div>
                <div class="w-100" style="color: white"><small class="float-right">(<span id="descr_counter">300</span> signs left)</small></div>
            </form>        
         </div>
    </div>
    
    <div class="row mb-3">
        <!--MEMBERS-->
        <div class="col-sm-12 col-md-6">
            <div class="card p-0 overflow-auto" style="height:13rem">
                <div class="card-header">Participants</div> 
                <select class="custom-select" id="add_participant" name="new_participant" onchange="addParticipant(this)">
                    <option selected disabled="disabled" value="">Add participant</option>
                    <?php foreach($members as $member) {?>
                        <option value="<?=$member['id']?>"><?=$member['name']?></option>                                                    
                    <?php } ?>
                </select>
                <div class="list-group" id ="participants"></div>                 
             </div>    
             <hr class="mb-3 mt-0 d-md-none" style="color:transparent">      
        </div>
    
        <!--RESEARCH AREAS-->
        <div class="col-md-6 col-sm-12">
            <form id="areas-form">
                <div class="card p-0 overflow-auto" style="height:13rem">
                    <div class="card-header">Research area</div>
                    <div class="areas-group-checkbox custom-control custom-checkbox m-2" id="div-areas">
                        <?php foreach ($areas as $value) { ?>
                            <div class="input-group">                            
                                <input type="checkbox" class="checkbox-item-area custom-control-input" id="<?=$value['id']?>" name="<?=$value['type']?>">
                                <label class="custom-control-label" for="<?=$value['id']?>"><?=$value['type']?></label>
                            </div>                        
                        <?php } ?> 
                    </div>
                </div>                
            </form>
        </div>
    </div>        

    <div class="row mb-3"> 
        <div class="col-sm-12">
            <!--FULL DESCRIPTION EDITOR-->
            <form id="full-description-form" class="needs-validation col-12 p-0"  novalidate encrypte="mulipart/form-data">
                <textarea placeholder="Full description..." class ="ckeditor ck form-control w-100" id ="full_description" name="editor_descr" required="required"></textarea>
                <div class="invalid-feedback">Valid description is required.</div>
            </form>  
        </div>           
    </div>
    <div class="d-flex justify-content-center">
        <button class="btn btn-info" id="create_button" onclick="Submit()">Create <i class='fa fa-check'></i></button>
    </div>
</div>

<script src="javascripts/adminCreateProject.js"></script>
<script src="javascripts/adminCommon.js"></script>