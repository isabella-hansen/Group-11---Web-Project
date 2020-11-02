 <!-- Created by Mariia Nema -->  
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>   
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css"> 
  
<?php   
    //include 'dbconnection.php';
    $db = OpenCon();

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
        $types = array();

        foreach ($results as $value) {
            $types[] = array(
                'type' => $value["type"],
                'id' => $value["id"]
            );
        }
    }        
    CloseCon($db);
?>        
<div class="row">
    <!--FILTER-->
    <div class="col-lg-3 col-md-4 col-sm-12">
        <div  class="card rounded shadow-sm p-3 mb-2" id="projects_filter">
            <form id="filter">
                <!--SEARCH-->
                <div class="input-group" id="div-search">
                    <input class="form-control py-2 border-right-0 border" type="search" placeholder="Search by title or description" id="search-input" maxlength="50" name="search_expr">
                    <span class="input-group-append">
                        <div class="input-group-text"><i class="fa fa-search"></i></div>
                    </span>
                </div>
                <hr class="mb-3" id="hr">
                <!--RESEARCH AREAS-->
                <label for="div-areas"><h5>Reasearch area</h5></label>                             
                <div class="areas-group-checkbox custom-control custom-checkbox overflow-auto" style="height: 10rem;" id="div-areas">
                    <?php foreach ($types as $value) { ?>
                        <div class="input-group">                            
                            <input type="checkbox" class="checkbox-item-area custom-control-input" id="<?=$value['type']?>" name="<?=$value['type']?>">
                            <label class="custom-control-label" for="<?=$value['type']?>"><?=$value['type']?></label>
                        </div>                        
                    <?php } ?>
                </div>
                <hr class="mb-3" id="hr">
                <!--STATUS-->
                <label for="div-status"><h5>Project status</h5></label>
                <div class="d-block input-group" id="div-status">
                    <div class="custom-control custom-radio">
                        <input id="active" type="radio"class="radio-item-status custom-control-input" name="status" value='active' >
                        <label class="custom-control-label" for="active">Active</label>
                    </div>
          
                    <div class="custom-control custom-radio">
                        <input id="finish" type="radio" class="radio-item-status custom-control-input" name="status"  value='finish' >
                        <label class="custom-control-label" for="finish">Finished</label>
                    </div>

                    <div class="custom-control custom-radio">
                        <input id="all" type="radio" class="radio-item-status custom-control-input"  name="status"  value='null' checked="checked">
                        <label class="custom-control-label" for="all">All</label>
                    </div>
                </div>
                <hr class="mb-3">
                <!--START DATE-->                
                <div class="input-group mb-2" id="div-date-start-picker">       
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="date_start"><i class="fa fa-unlock" style="width: 10pt"></i></label>
                    </div>
                    <input type="text" class="date-picker form-control float-right" id="date-start-picker"  readonly="readonly" style="cursor:pointer; background-color: #FFFFFF" name="start_range">                
                    <span class="input-group-append">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </span>
                </div> 
                <!--END DATE-->                 
                <div class="input-group" id ="div-date-end-picker">    
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="date_end"><i class="fa fa-lock" style="width: 10pt"></i></label>
                    </div>
                    <input type="text" class="date-picker form-control float-right" id="date-end-picker" readonly="readonly" style="cursor:pointer; background-color: #FFFFFF" name="end_range">
                    <span class="input-group-append">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </span>
                </div>
            </form>        
        </div>
    </div>


    <!--PROJECTS-->
    <div class="col-lg-9 col-md-8 col-sm-12">    
        
        <div id="projects-list" class="w-100"></div>        
    
        <!--PAGINATION-->
        <div class="row">
            <nav aria-label="pager" class="col-4 m-auto" id="pager">
                <ul class="pagination d-flex justify-content-center">
                    <li class="page-item" id="li-previous-page" style="cursor:pointer" >
                        <a class="page-link" id="previous-page" value="" onclick="filter(this.value,'prev')" aria-disabled="true">Previous</a>
                    </li>
            
                    <li class="page-item" id="li-next-page" style="cursor:pointer">
                        <a class="page-link" onclick="filter(this.value,'next')" value="" id="next-page">Next</a>
                    </li>
                </ul>
            </nav> 
        </div>
    </div>
</div>             
            
   
  <script src="javascripts/projectspage.js"></script>
  <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/js/bootstrap-select.min.js"></script>  