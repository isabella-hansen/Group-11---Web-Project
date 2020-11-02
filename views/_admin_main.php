<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/> 
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.14/dist/css/bootstrap-select.min.css">
<link rel="stylesheet" href="css/admin/admineditor.css"> 


<?php //Isabella has created this page ?>

<?php if(!isset($_SESSION['usr']) || $_SESSION['usr'] != 'admin'): ?>


<!--Page that loads if non-admins try to access the admin-page-->
<div class="container-fluid">
        <div class="row">
            <div class="col-6">
            <br><br>
            <p style="text-align: center; color:white">This page is only accessible for administration staff.</p><br>
            </div>
            <div class="col-6" style="text-align: center">
            <br>
            </div>
        </div>
</div>
<?php elseif($_SESSION['usr'] == 'admin'): ?>

<!--Admin-page-->
<div class="container-fluid" id ="menu_and_content">
    <div class="row">
        
            <div class="d-none d-md-block col-md-3 p-0 m-0 ">
                <div class="w-100"><h4>Projects</h4><br>
                    <button type="button" class="btn btn-info text-white btn-block" id="menu_create" onClick="card_click_content(this.getAttribute('id'));">Create projects</button><br>
                    <button type="button" class="btn btn-info text-white btn-block" id="menu_edit" onClick="card_click_content(this.getAttribute('id'));">Edit projects</button>
                </div><br>
                <hr class="mb-3" id="hr">
                <div class="w-100"><h4>Members</h4><br>
                    <button type="button" class="btn btn-info text-white btn-block" id="menu_accept" onClick="card_click_content(this.getAttribute('id'));">Applications</button><br>
                    <button type="button" class="btn btn-info text-white btn-block" id="menu_delete" onClick="card_click_content(this.getAttribute('id'));">Delete members</button>
                </div>
            </div>
            
            <select name="select_menu" id="select_menu" class="form-control d-block d-md-none col-12 mt-3" onchange="card_click_content(this.options[this.selectedIndex].value);">
	              <option>Go to...</option>	           
	              <option value="menu_create">Create projects</option>
	              <option value="menu_edit">Edit projects</option>
	              <option value="menu_accept">Applications</option>
	              <option value="menu_delete">Delete members</option>
	        </select>
       
        
        <div class="d-flex justify-content-center col-sm-12 col-md-9">
            <br>
             <!--Content that change depending on administrative task-->
            <div class="container" id="content_style">
                <br>
                <div id ="content">
                    <h4>Welcome Admin!</h4><br>
                    <p> What would you like to administrate today?</p>
                </div>
            </div>
        </div>
    </div>
    <br><br>
<?php endif; ?>
</div>

        <script>
        
            function card_click_content(id) { 

                if(id == "menu_create")
                {
                    $("#content").load("Views/_admincreateproject.php");
                    
				}
                if(id == "menu_edit")
                {
                    $("#content").load("Views/_adminEditProject.php");
				}
                if(id == "menu_accept")
                {
                    $("#content").load("Views/_adminAcceptMember.php");
				}
                if(id == "menu_delete")
                {
                    $("#content").load("Views/_adminDeleteMember.php");
				}
                
            } 

        </script>
        

  <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script src="https://cdn.ckeditor.com/ckeditor5/23.0.0/classic/ckeditor.js"></script>
  <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script src="javascripts/adminCommon.js"></script>
  <script src="javascripts/deletememberpage.js"></script>