<?php
namespace OpenStudy\Attributes\DB;

use OpenStudy\Attributes\Proprety;
use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Column extends Proprety { }