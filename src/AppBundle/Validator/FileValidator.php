<?php
namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraints as Assert;
 
class FileValidator
{
	
	/**
	 * @Assert\NotNull(
	 * 	message = "Thumbmail image must be supplied.",
	 *  groups = {"Product"}
	 * )
	 * @Assert\File(
	 *	mimeTypes = {"image/jpeg", "image/pjpeg", "image/png", "image/gif"},
	 *  mimeTypesMessage = "Please upload a valid Image (*.jpg, *.jpeg, *.png, *.gif)"
	 * )
	 */
	private $thumbnailImage;
	
	public function __construct($thumbnailImage = null)
	{
		$this->thumbnailImage		= $thumbnailImage;
	}
}