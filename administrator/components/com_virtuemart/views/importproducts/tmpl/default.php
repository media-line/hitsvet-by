<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
AdminUIHelper::startAdminArea();
/* Get the component name */
$option = JRequest::getWord('option');
?>

<script type="text/javascript" src="/administrator/components/com_virtuemart/assets/fancyupload/source/Fx.ProgressBar.js"></script>
<script type="text/javascript" src="/administrator/components/com_virtuemart/assets/fancyupload/source/Swiff.Uploader.js"></script>
<script type="text/javascript" src="/administrator/components/com_virtuemart/assets/fancyupload/source/FancyUpload3.Attach.js"></script>

<script type="text/javascript">
	
/**
 * FancyUpload Showcase
 *
 * @license		MIT License
 * @author		Harald Kirschner <mail [at] digitarald [dot] de>
 * @copyright	Authors
 */

window.addEvent('domready', function() {

	/**
	 * Uploader instance
	 */
	var upZip = new FancyUpload3.Attach('zip-list', '#zip-attach, #zip-attach-2', {
		path: 'components/com_virtuemart/assets/fancyupload/source/Swiff.Uploader.swf',
		url: 'components/com_virtuemart/assets/fancyupload/showcase/script.php',
		typeFilter: {
			'ZIP-архивы (*.zip)': '*zip'
		},
		fileSizeMax: 30 * 1024 * 1024,
		
		verbose: true,
		
		onSelectFail: function(files) {
			files.each(function(file) {
				new Element('li', {
					'class': 'file-invalid',
					events: {
						click: function() {
							this.destroy();
						}
					}
				}).adopt(
					new Element('span', {html: file.validationErrorMessage || file.validationError})
				).inject(this.list, 'bottom');
			}, this);	
		},
		
		onFileSuccess: function(file) {
			new Element('input', {type: 'checkbox', 'checked': true}).inject(file.ui.element, 'top');
			file.ui.element.highlight('#e6efc2');
			document.getElementById('zipFile').value = file.name;
		},
		
		onFileError: function(file) {
			file.ui.cancel.set('html', 'Retry').removeEvents().addEvent('click', function() {
				file.requeue();
				return false;
			});
			
			new Element('span', {
				html: file.errorMessage,
				'class': 'file-error'
			}).inject(file.ui.cancel, 'after');
		},
		
		onFileRequeue: function(file) {
			file.ui.element.getElement('.file-error').destroy();
			
			file.ui.cancel.set('html', 'Cancel').removeEvents().addEvent('click', function() {
				file.remove();
				return false;
			});
			
			this.start();
		}
		
	});
	
	
	var upXls = new FancyUpload3.Attach('xls-list', '#xls-attach, #xls-attach-2', {
		path: 'components/com_virtuemart/assets/fancyupload/source/Swiff.Uploader.swf',
		url: 'components/com_virtuemart/assets/fancyupload/showcase/script.php',
		typeFilter: {
			'XLS-файлы (*.xls)': '*xls'
		},
		fileSizeMax: 2 * 1024 * 1024,
		
		verbose: true,
		
		onSelectFail: function(files) {
			files.each(function(file) {
				new Element('li', {
					'class': 'file-invalid',
					events: {
						click: function() {
							this.destroy();
						}
					}
				}).adopt(
					new Element('span', {html: file.validationErrorMessage || file.validationError})
				).inject(this.list, 'bottom');
			}, this);	
		},
		
		onFileSuccess: function(file) {
			new Element('input', {type: 'checkbox', 'checked': true}).inject(file.ui.element, 'top');
			file.ui.element.highlight('#e6efc2');
			document.getElementById('xlsFile').value = file.name;
		},
		
		onFileError: function(file) {
			file.ui.cancel.set('html', 'Retry').removeEvents().addEvent('click', function() {
				file.requeue();
				return false;
			});
			
			new Element('span', {
				html: file.errorMessage,
				'class': 'file-error'
			}).inject(file.ui.cancel, 'after');
		},
		
		onFileRequeue: function(file) {
			file.ui.element.getElement('.file-error').destroy();
			
			file.ui.cancel.set('html', 'Cancel').removeEvents().addEvent('click', function() {
				file.remove();
				return false;
			});
			
			this.start();
		}
		
	});

});
</script>


<style>
	.toolbar-list,
	.icon-48-vm_importproducts_48 small,
	#toolbar-edit,
	#toolbar-unpublish,
	#toolbar-publish{display:none;}
</style>
<form action="index.php?task=import" method="post" name="adminForm" id="adminForm">
	<?
	/*
	<div class="ImportProducts">
		<a href="#" id="zip-attach">Загрузить архив с файлами</a>
		<ul id="zip-list"></ul>
		<a href="#" id="zip-attach-2" style="display: none;">Attach another file</a>
	</div>
	*/
	?>
		
	<div class="ImportProducts">
		<a href="#" id="xls-attach">Загрузить файл c данными</a>
		<ul id="xls-list"></ul>
		<a href="#" id="xls-attach-2" style="display: none;">Attach another file</a>
	</div>
	
	<div class="ImportProducts">
		<button id="ImportData" onclick="Joomla.submitbutton('import')">Импортировать данные</button>
		<input type="hidden" name="zipFile" id="zipFile" value="" />
		<input type="hidden" name="xlsFile" id="xlsFile" value="" />
	</div>
		

	<!-- Hidden Fields -->
	<?php echo $this->addStandardHiddenToForm(); ?>
</form>
<?php AdminUIHelper::endAdminArea(); ?>

