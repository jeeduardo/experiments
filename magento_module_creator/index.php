<!DOCTYPE html>
<html>
<body>
	<?php require_once 'lib.php'; ?>
	<div class="wrapper">
		<div class="container">
			<div class="form-div">
				<h1>Magento Module Creator Experiment</h1>
				<form method="POST" action="<?php echo get_action_url('action.php'); ?>">
					<div class="form-label">
						<label for="namespace">Namespace</label>
					</div>
					<div class="form-input">
						<input type="text" name="namespace" id="namespace">
					</div>

					<div class="form-label">
						<label for="module">Module</label>
					</div>
					<div class="form-input">
						<input type="text" name="module" id="module">
					</div>

					<div class="form-label">
						<label for="directories">Directories</label>
					</div>
					<div class="form-input">
						<input type="text" name="directories" id="directories">
					</div>

					<div class="form-label">
						<label for="version">Version Number:</label>
					</div>
					<div class="form-input">
						<input type="text" name="version" id="version">
					</div>

					<div class="form-submit">
						<button type="submit" name="create_module" id="create_module" value="create_module">
							Create Module</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</body>
</html>