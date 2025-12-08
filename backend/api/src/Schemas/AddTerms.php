<?php
namespace OpenStudy\Schemas;

use OpenApi\Attributes as OA;
use OpenStudy\Attributes\Validators as VA;
use OpenStudy\Attributes\Property;
use OpenStudy\Models\Term;
#[OA\Schema]
class AddTerms extends BaseSchema {
	/** @var array<Term> */
	public array $terms;

	#[
		OA\Property("terms", type: "array", 
			items: new OA\Items(CreateTerm::class)
		), Property
	]
	private array $_terms;

	public function __construct(array $data) {
		parent::__construct($data);
		foreach ($this->_terms as $key => $term) {
			$this->terms[$key] = new CreateTerm($term);
		}
	}
}