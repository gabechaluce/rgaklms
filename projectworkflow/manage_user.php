<?php 
include('db_connect.php');
session_start();
if(isset($_GET['id'])){
$user = $conn->query("SELECT * FROM users where id =".$_GET['id']);
foreach($user->fetch_array() as $k =>$v){
	$meta[$k] = $v;
}
}
?>
<body>
<div class="container-fluid">
	<div id="msg"></div>
	
	<form action="" id="manage-user" enctype="multipart/form-data">	
		<input type="hidden" name="id" value="<?php echo isset($meta['id']) ? $meta['id']: '' ?>">
		
		<div class="row">
			<div class="col-md-8">
				<div class="card">
					<div class="card-header bg-primary text-white">
						<h4 class="mb-0">User Information</h4>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label for="firstname" class="control-label">First Name <span class="text-danger">*</span></label>
									<input type="text" name="firstname" id="firstname" class="form-control" value="<?php echo isset($meta['firstname']) ? $meta['firstname']: '' ?>" required>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									<label for="lastname" class="control-label">Last Name <span class="text-danger">*</span></label>
									<input type="text" name="lastname" id="lastname" class="form-control" value="<?php echo isset($meta['lastname']) ? $meta['lastname']: '' ?>" required>
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<label for="email" class="control-label">Username <span class="text-danger">*</span></label>
							<input type="text" name="email" id="email" class="form-control" value="<?php echo isset($meta['email']) ? $meta['email']: '' ?>" required autocomplete="off">
						</div>
						
						<div class="form-group">
							<label for="password" class="control-label">Password</label>
							<input type="password" name="password" id="password" class="form-control" value="" autocomplete="off">
							<small class="text-muted"><i>Leave this blank if you don't want to change the password.</i></small>
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-md-4">
				<div class="card">
					<div class="card-header bg-info text-white">
						<h4 class="mb-0">Profile Image</h4>
					</div>
					<div class="card-body text-center">
						<div class="form-group">
							<label for="" class="control-label">Avatar</label>
							<div class="custom-file">
								<input type="file" class="custom-file-input" id="customFile" name="img" onchange="displayImg(this,$(this))">
								<label class="custom-file-label" for="customFile">Choose file</label>
							</div>
						</div>
						<div class="form-group d-flex justify-content-center mt-3">
							<img src="<?php echo isset($meta['avatar']) ? 'assets/uploads/'.$meta['avatar'] : 'assets/uploads/no-image-available.png' ?>" alt="Avatar" id="cimg" class="img-fluid img-thumbnail">
						</div>
					</div>
				</div>
				
				
			</div>
		</div>
	</form>
</div>

<style>
	img#cimg{
		height: 120px;
		width: 120px;
		object-fit: cover;
		border-radius: 50%;
		border: 3px solid #dee2e6;
	}
	.card {
		border: none;
		border-radius: 10px;
		box-shadow: 0 0 15px rgba(0,0,0,0.1);
	}
	.card-header {
		border-radius: 10px 10px 0 0 !important;
	}
	.form-control {
		border-radius: 5px;
	}
	.btn {
		border-radius: 5px;
	}
	body, .wrapper, .content-wrapper {
		background-color: #f4f6f9 !important;
	}
	.required-label::after {
		content: " *";
		color: red;
	}
</style>

<script>
	function displayImg(input,_this) {
	    if (input.files && input.files[0]) {
	        var reader = new FileReader();
	        reader.onload = function (e) {
	        	$('#cimg').attr('src', e.target.result);
	        	_this.siblings('.custom-file-label').html(input.files[0].name);
	        }
	        reader.readAsDataURL(input.files[0]);
	    }
	}
	
	$(document).ready(function(){
		// Update custom file label
		$('.custom-file-input').on('change', function() {
			let fileName = $(this).val().split('\\').pop();
			$(this).siblings('.custom-file-label').addClass("selected").html(fileName);
		});
		
		$('#manage-user').submit(function(e){
			e.preventDefault();
			start_load();
			
			// Basic validation
			var firstname = $('#firstname').val().trim();
			var lastname = $('#lastname').val().trim();
			var email = $('#email').val().trim();
			
			if(!firstname || !lastname || !email) {
				$('#msg').html('<div class="alert alert-danger">Please fill in all required fields</div>');
				end_load();
				return;
			}
			
			$.ajax({
				url: 'ajax.php?action=update_user',
				data: new FormData($(this)[0]),
			    cache: false,
			    contentType: false,
			    processData: false,
			    method: 'POST',
			    type: 'POST',
				success: function(resp){
					if(resp == 1){
						alert_toast("Data successfully saved", 'success');
						setTimeout(function(){
							location.href = 'index.php?page=user_list'; // Redirect to user list
						}, 1500);
					} else {
						$('#msg').html('<div class="alert alert-danger">Error saving data. Please try again.</div>');
						end_load();
					}
				},
				error: function() {
					$('#msg').html('<div class="alert alert-danger">An error occurred. Please try again.</div>');
					end_load();
				}
			});
		});
	});
</script>
</body>