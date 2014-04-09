<? 

/*
This partial is used to generate all of the HTML and layout that comes
after most CRUD forms.  It is used in conjunction with _form_header.
It expects:

	- controller : A string depicting the controller.  This is used in
		generating links.  I.e. 'admin.news'
		
	- item (optional) : The data that is being edited

*/

?>

	<hr/>
	<div class="controls actions">
		<div class="btn-group">
			<? if (app('decoy.auth')->can('update', $controller)): ?>
				<button name="_save" value="save" type="submit" class="btn btn-success save"><i class="icon-file icon-white"></i> Save</button>
			<? endif ?>
			<? if (app('decoy.auth')->can('update', $controller) && app('decoy.auth')->can('create', $controller)): ?>
				<button name="_save" value="new" type="submit" class="btn btn-success save_new">&amp; New</button>
			<? endif ?> 
			<? if (app('decoy.auth')->can('update', $controller)): ?>
				<button name="_save" value="back" type="submit" class="btn btn-success save_back">&amp; Back</button>
			<? endif ?>
		</div>
		
		<? if (!empty($item) && app('decoy.auth')->can('destroy', $controller)): ?>
			<a class="btn btn-danger delete" href="<?=DecoyURL::relative('destroy', $item->id)?>">
				<i class="icon-trash icon-white"></i> Delete
			</a>
		<? endif ?>
		
		<a class="btn back" href="<?=Bkwld\Decoy\Breadcrumbs::smartBack()?>">Back</a>
	</div>

<?= Former::close() ?>
