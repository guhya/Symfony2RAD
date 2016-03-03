<?php
namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\VO\TermVO;
use AppBundle\Common\Constants;

class TermValidator
{
	/**
	 * @Assert\NotBlank(
	 * 	message = "Term name cannot be blank."
	 * )
	 */
	private $name;

	/**
	 * @Assert\NotBlank(
	 * 	message = "Tag / Category cannot be blank."
	 * )
	 */
	private $taxonomy;
	
	/**
	 * @Assert\NotBlank(
	 * 	message = "Parent cannot be blank.",
	 *  groups = {"Category"}
	 * )
	 */
	private $parent;
	
	public function __construct(TermVO $termVo)
	{
		$this->name 		= $termVo->getName();
		$this->description	= $termVo->getDescription();
		$this->taxonomy		= $termVo->getTaxonomy();
		$this->parent		= $termVo->getParent();
	}
}