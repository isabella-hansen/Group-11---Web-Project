<!-- Created by Emelie Wallin -->

<div class="d-flex md-10 justify-content-center">
	<h4>Delete members</h4>
</div>

<div class="container-fluid"> 
	<div class="row">
		<div class="col-sm-12 col-md-4">
			<br>

			<!-- ajax search -->
			<div class="input-group">
				<div class="input-group-prepend">
					<input class="form-control py-2 border-0" type="text" placeholder="Search member" id="search-input" style="max-width:250pt" onkeyup="searchDel(this.value)">
				</div>
				<div class="input-group-append">
					<span class="input-group-text"><i class="fa fa-search"></i></span>
				</div>	
			</div>
			<br>

			<!-- search result will show here -->
			<lable for="list">Members:</lable>	
			<ul id="deleteMembers" class="list-group overflow-auto" style="height:100pt">
				<lable><em>No search objects...</em></lable>		
			</ul>
		</div>

		<!-- default member view -->
		<div class="d-flex justify-content-center flex-column col-md-8">
			<div id="memberInfo">
				<div class="d-flex flex-row flex-wrap">

					<div class="d-flex flex-column col-md-4 p-2">
						
						<!-- first name -->
						<lable for="fname">First Name:</lable>
						<input type="text" value="" class="form-control" id="fname" disabled><br>

						<!-- last name -->
						<lable for="lname">Last Name:</lable>
						<input type="text" value="" class="form-control" id="lname" disabled><br>
					</div>
	
					<div class="d-flex flex-column col-md-4 p-2">
						
						<!-- email -->
						<lable for="email">Email:</lable>
						<input type="text" value="" class="form-control" id="email" disabled><br>

						<!-- phone -->
						<lable for="phone">Phone:</lable>
						<input type="text" value="" class="form-control" id="phone" disabled><br>	
					</div>

					<div class="d-flex flex-column col-md-4 p-2">
						
						<!-- title -->
						<lable for="title">Title:</lable>
						<input type="text" value=""class="form-control" id="title" disabled><br>

						<!-- research area -->
						<lable for="title">Research Area:</lable>
						<input type="text" value="" class="form-control" id="area" disabled><br>
					</div>
				</div>
		
				<div class="d-flex flex-row flex-wrap col-md">

					<!-- biography -->
					<div class="col-md-8 p-2">
						<lable for="biography">Biography:</lable>
						<textarea class="form-control" id="biography" style="height:135pt" disabled></textarea>
					</div>
					
					<!-- avatar -->
					<div class="col-md-4 p-2">
						<div>
							<lable for="img">Avatar:<br></lable>
							<img class="img-thumbnail" id="img" src="uploads/default.jpg" value="" style="width: 125pt; height: 125pt">
						</div>
					</div>
				</div>
			</div>

			<!-- buttons -->
			<div>
				<div class="d-flex flex-row col-md p-2 justify-content-center">
					<div class="p-3">
						<button id="delete" type="button" class="btn btn-info" onclick="deleteMember()">Delete Member <i class="fa fa-times"></i></button>
						<button id="loadingDel" class="btn btn-info" style="display:none;">
						<span class="spinner-border spinner-border-sm"></span>
						Loading..
					</button>
					</div>
				</div>
				<!-- alert-place-->
				<div id="alertMessage"></div>	
			</div>
		</div>
	</div>
</div>
