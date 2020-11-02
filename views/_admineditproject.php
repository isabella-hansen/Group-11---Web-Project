
  
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
<div class="col-md-12 order-md-1 m-auto p-0">
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
    <!--MODAL DELETE-->   
    <div class="modal fade" id ="FeedbackModal" style="color: black">
        <div class="modal-dialog modal-sm p-0">
            <div class="modal-content p-0">
                <div class="modal-header">
                    <h1 class="d-flex justify-content-center" id="modalMsg"></h1>
                    <div class="project_info"></div>
                </div>
                <div class="modal-body" id="deletemsg" style="color:black"></div>
                <div class="modal-footer">                    
                    <button type="button" class="btn btn-primary" id="operation" data-dismiss="modal">Yes</button>
                    <button type="button" class="btn btn-primary" id="close" data-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-2">        
        <div class="col-sm-12">
            <!--OPERATION-->
            <h4 class="float-left">Edit project</h4>        
            <!--DELETE-BTN-->            
            <button class="btn btn-danger float-right" style="display:none;" id="delete_button">Delete project</button>
        </div>
    </div>
    <div class="row mb-3 d-sm-block">
        <!--SEARCH FOR PROJECT TO CHANGE-->
        <div class="d-block col-sm-12">
            <div class="input-group" id="div-search">
                <input class="form-control" type="search" placeholder="Search by title" id="search-input" maxlength="50" name="search_expr">
                <span class="input-group-append">
                    <div class="input-group-text"><i class="fa fa-search"></i></div>
                </span>
            </div>
            <div>
                <div class="list-group position-absolute border border-info"  id ="projects_found"></div>
            </div>
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
                <div class="w-100"><small class="float-right">(<span id="title_counter">150</span> signs left)</small></div>
            </form>
        </div>
    </div>
    
    <div class="row mb-3">
    <!--LEADER-->
        <div  class="col-md-6 col-sm-12 mb-3">
            <form id="leader-form" class="needs-validation" novalidate>
                <div class="input-group">                    
                    <select class="custom-select" id="leader" required="required" name="leader">                        
                            <option selected value=""></option>
                            <?php foreach($members as $member) {?>
                                <option value="<?=$member['id']?>"><?=$member['name']?></option>                                                    
                            <?php } ?>                        
                    </select>                         
                    <div class="input-group-append">
                        <label class="input-group-text" for="leader">Leader</label>
                    </div>                
                    <div class="invalid-feedback">Choose leader</div>       
                </div>
            </form>  
        </div>

        <!--DATE-->
        <div class="col-md-6 col-sm-12 mb-3">
            <form id="date-form">                 
                <div class="input-group" id="div-date-end">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="date_end"><i class="fa fa-lock"></i></label>
                    </div>
                    <input type="text" class="date-picker form-control float-right" id="date_end"  readonly="readonly" name="date_end" style="cursor:pointer; background-color: #FFFFFF">
                    <span class="input-group-append">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </span>                
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
                <div class="w-100"><small class="float-right">(<span id="descr_counter">300</span> signs left)</small></div>
            </form>        
         </div>
    </div>
    
    <div class="row mb-3">
        <!--MEMBERS-->
        <div class="col-sm-12 col-md-6">
            <div class="card p-0 overflow-auto" style="height:13rem; color: black">
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
                <div class="card p-0 overflow-auto" style="height:13rem; color: black">
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
        <div class="col-sm-12" style="color: black">
            <!--FULL DESCRIPTION EDITOR-->
            <form id="full-description-form" class="needs-validation col-12 p-0"  novalidate encrypte="mulipart/form-data">
                <textarea placeholder="Full description..." class ="ckeditor ck form-control w-100" id ="full_description" name="editor_descr" required="required"></textarea>
                <div class="invalid-feedback">Valid description is required.</div>
            </form>              
        </div>     
    </div>
    <div class="row mb-3">    
        <div class="col-sm-12" style="color: black">    
            <!--RESULT EDITOR-->
            <form id="result-form" class="col-12 p-0"  novalidate encrypte="mulipart/form-data">
                <textarea placeholder="Results..." class="ckeditor ck form-control w-100 overflow-auto" id ="result" name="editor_result"></textarea>
            </form>  
        </div> 
    </div>
    <!--CREATE BTN-->
    <div class="d-flex justify-content-center">
        <button class="btn btn-info" id="update_button">Submit <i class='fa fa-check'></i></button>
    </div>
</div>

<script src="javascripts/adminEditProject.js"></script>
<script src="javascripts/adminCommon.js"></script>
