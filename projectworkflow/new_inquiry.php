<?php if(!isset($conn)){ include 'db_connect.php'; } ?>
<body>
<div class="col-lg-12">
	<div class="card card-outline card-primary">
		<div class="card-body">
			<form action="" id="manage-inquiry">
			<input type="hidden" name="id" value="<?php echo isset($id) ? $id : ''; ?>">
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="" class="control-label">Name</label>
							<input type="text" class="form-control form-control-sm" name="name" value="<?php echo isset($name) ? $name : '' ?>" REQUIRED>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Inquiry Status</label>
							<select name="inquiry_status" id="inquiry_status" class="custom-select custom-select-sm" REQUIRED>
								<option value="0" <?php echo isset($inquiry_status) && $inquiry_status == 0 ? 'selected' : '' ?>>Serious</option>
								<option value="1" <?php echo isset($inquiry_status) && $inquiry_status == 1 ? 'selected' : '' ?>>Not Serious</option>
								<option value="2" <?php echo isset($inquiry_status) && $inquiry_status == 2 ? 'selected' : '' ?>>Done</option>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="" class="control-label">Contact Info</label>
							<input type="text" class="form-control form-control-sm" name="contact" value="<?php echo isset($contact) ? $contact : '' ?>" REQUIRED>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="">Quotation Status</label>
							<select name="quotation_status" id="quotation_status" class="custom-select custom-select-sm" REQUIRED>
								<option value="0" <?php echo isset($quotation_status) && $quotation_status == 0 ? 'selected' : '' ?>>Quotation Sent</option>
								<option value="1" <?php echo isset($quotation_status) && $quotation_status == 1 ? 'selected' : '' ?>>Quotation Not Sent</option>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="" class="control-label">Business Name</label>
							<input type="text" class="form-control form-control-sm" name="business_name" value="<?php echo isset($business_name) ? $business_name : '' ?>" REQUIRED>
						</div>
					</div>
				</div>
<div class="row">
	<div class="col-md-10">
		<div class="form-group">
			<label for="" class="control-label">Description</label>
			<textarea name="description" cols="30" rows="10" class="summernote form-control" REQUIRED>
<?php echo isset($description) ? trim($description) : '' ?></textarea>
		</div>
	</div>
</div>
			</form>
		</div>
		<div class="card-footer border-top border-info">
			<div class="d-flex w-100 justify-content-center align-items-center">
				<button class="btn btn-flat  bg-gradient-primary mx-2" form="manage-inquiry">Save</button>
				<button class="btn btn-flat bg-gradient-secondary mx-2" type="button" onclick="location.href='index.php?page=inquiry_list'">Cancel</button>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function () {
	$('.summernote').summernote({
		height: 200,
		toolbar: [
			['style', ['style']],
			['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
			['fontname', ['fontname']],
			['fontsize', ['fontsize']],
			['color', ['color']],
			['para', ['ol', 'ul', 'paragraph', 'height']],
			['table', ['table']],
			['view', ['undo', 'redo', 'fullscreen', 'help']]
		],
		placeholder: "Input relevant information such as dimensions, materials, budget, and deadline discussed with client"
	});

	// Initialize Select2
	$('.select2').select2({
		placeholder: "Please select here",
		width: "100%"
	});

	// Custom handling for description box focus/blur
	const descriptionField = $('.note-editable');

	descriptionField.on('focus', function () {
		if (descriptionField.text() === "Input relevant information such as dimensions, materials, budget, and deadline discussed with client") {
			descriptionField.text('');
		}
	});

	descriptionField.on('blur', function () {
		if (descriptionField.text().trim() === '') {
			descriptionField.text("Input relevant information such as dimensions, materials, budget, and deadline discussed with client");
		}
	});

	// Handle form submission
	$('#manage-inquiry').submit(function (e) {
		e.preventDefault();
		start_load();
		$.ajax({
			url: 'ajax.php?action=save_inquiry',
			data: new FormData($(this)[0]),
			cache: false,
			contentType: false,
			processData: false,
			method: 'POST',
			success: function (resp) {
				if (resp == 1) {
					alert_toast('Data successfully saved', "success");
					setTimeout(function () {
						location.reload();
					}, 1500);
				}
			}
		});
	});
});

</script>
<style>

body, .wrapper, .content-wrapper {
    background-color:#f4f1ed !important;
}


</style>
</body>