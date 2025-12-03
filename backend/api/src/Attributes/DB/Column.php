<?php
namespace OpenStudy\Attributes\DB;

use OpenStudy\Attributes\Property;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Column extends Property { }