<?php namespace Bkwld\Decoy\Fields;

// Dependencies
use Bkwld\Decoy\Input\ManyToManyChecklist as ManyToManyChecklistHandler;
use Former\Form\Fields\Checkbox;
use HtmlObject\Input as HtmlInput;
use Illuminate\Container\Container;
use Str;

/**
 * Render a list of checkboxes to represent a related many-to-many table.  The underlying
 * Former field type is a checkbox.  The relationship names is stored in the name. 
 * The relationship instance that is being represented is stored in the value.
 */
class ManyToManyChecklist extends Checkbox {
	use Traits\CaptureLabel, Traits\Scopable, Traits\Helpers;

	/**
	 * Prints out the field, wrapped in its group.  This is the opportunity
	 * to tack additional stuff onto the control group
	 * 
	 * @return string
	 */
	public function wrapAndRender() {
		$this->addGroupClass('many-to-many-checklist');
		return parent::wrapAndRender();

	}

	/**
	 * Prints out the current tag, appending an extra hidden field onto it
	 * for the storing of the foreign key
	 *
	 * @return string An input tag
	 */
	public function render() {

		// Get an array of formatted data for Former checkboxes 
		$boxes = array();
		foreach($this->getRelations() as $row) {
			$boxes[$this->generateBoxLabel($row)] = $this->generateBox($row);
		}

		// Render the checkboxes, adding a hidden field after the set so that if
		// all boxes are un-checked, an empty value will be sent
		if (count($boxes)) {
			$this->checkboxes($boxes);
			return parent::render().HtmlInput::hidden($this->boxName());

		// There are no relations yet, show a message to that effect
		} else {
			return '<i class="icon-info-sign"></i> 
				You have not <a href="/admin/'.Str::snake($this->name,'-').'">created</a> 
				any <b>'.ucfirst($this->label_text).'</b>.';
		}
	}

	/**
	 * Get all the options in the related table.  Each will become a checkbox.
	 *
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	public function getRelations() {
		$query = call_user_func(ucfirst(Str::singular($this->name)).'::ordered');
		if ($this->scope) call_user_func($this->scope, $query);
		return $query->get();
	}

	/**
	 * Generate the checkbox name using a special prefix that tells
	 * Decoy to treat it has a many to many checkbox
	 */
	protected function boxName() {
		return ManyToManyChecklistHandler::PREFIX.$this->name.'[]';
	}

	/**
	 * Take a model instance and generate a checkbox for it
	 *
	 * @param Illuminate\Database\Eloquent\Model $row
	 * @return array Configuration that is used by Former for a checkbox
	 */
	protected function generateBox($row) {
		return array(
			'name' => $this->boxName(),
			'value' => $row->getKey(),
			'checked' => ($children = $this->children()) && $children->contains($row->getKey()),

			// Former is giving these a class of "form-control" which isn't correct
			'class' => false,
		);
	}

	/**
	 * Create the HTML label for a checkbox
	 *
	 * @param Illuminate\Database\Eloquent\Model $row
	 * @return string HTML
	 */
	protected function generateBoxLabel($row) {
		$url = Str::snake($this->name,'-').'/'.$row->getKey().'/edit';
		$html = '<a href="/admin/'.$url.'">'.$row->title().'</a>';

		// The str_replace fixes Former's auto conversion of underscores into spaces. 
		$html = str_replace('_', '&#95;', $html);
		return $html;
	}

	/**
	 * Get a collection of all the children that are already associated with the parent
	 *
	 * @return Illuminate\Database\Eloquent\Collection
	 */
	protected function children() {
		if (($item = $this->model())
			&& is_a($item, 'Illuminate\Database\Eloquent\Model')
			&& method_exists($item, $this->name)) return $item->{$this->name};
	}

}