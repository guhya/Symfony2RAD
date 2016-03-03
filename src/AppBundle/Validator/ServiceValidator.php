<?php
namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\VO\ServiceVO;
use AppBundle\Common\Constants;

/*
 * This is the validator class for service registration/update page
 * Eventhough it looks similar to ServiceVO, it is actually different.
 * 
 * Here we can add images and files field which is required to be able to complete
 * service operation but the field itself is not in the VO.
 *
 * The main reason this validator is in separate class is because we don't
 * want to pollute simple VO (Plain Old PHP Object) with assertion and annotation
 * VO must be very light weight.
 */
 
class ServiceValidator
{
	/**
	 * @Assert\NotBlank(
	 * 	message = "Service name cannot be blank."
	 * )
	 */
	private $name;

	/**
	 * @Assert\NotBlank(
	 * 	message = "Service description cannot be blank."
	 * )
	 */
	private $description;
	
	/**
	 * @Assert\NotNull(
	 * 	message = "Thumbmail image must be supplied.",
	 *  groups = {"Insert"}
	 * )
	 * @Assert\File(
	 *	mimeTypes = {"image/jpeg", "image/pjpeg", "image/png", "image/gif"},
	 *  mimeTypesMessage = "Please upload a valid Image (*.jpg, *.jpeg, *.png, *.gif)"
	 * )
	 */
	private $thumbnailImage;
	
	/**
	 * @Assert\File(
	 *	mimeTypes = {"image/jpeg", "image/pjpeg", "image/png", "image/gif"},
	 *  mimeTypesMessage = "Please upload a valid Image (*.jpg, *.jpeg, *.png, *.gif)",
	 *  groups = {"Insert"}
	 * )
	 */
	private $mainImage;
	
	/**
	 * @Assert\File(
	 *	mimeTypes = {"image/jpeg", "image/pjpeg", "image/png", "image/gif"},
	 *  mimeTypesMessage = "Please upload a valid Image (*.jpg, *.jpeg, *.png, *.gif)",
	 * )
	 */
	private $image1;
	
	/**
	 * @Assert\File(
	 *	mimeTypes = {"image/jpeg", "image/pjpeg", "image/png", "image/gif"},
	 *  mimeTypesMessage = "Please upload a valid Image (*.jpg, *.jpeg, *.png, *.gif)",
	 * )
	 */
	private $image2;
	
	/**
	 * @Assert\File(
	 *	mimeTypes = {"image/jpeg", "image/pjpeg", "image/png", "image/gif"},
	 *  mimeTypesMessage = "Please upload a valid Image (*.jpg, *.jpeg, *.png, *.gif)",
	 * )
	 */
	private $image3;
	
	/**
	 * @Assert\File(
	 *	mimeTypes = {"application/pdf"},
	 *  mimeTypesMessage = "Please upload a valid brochure in pdf format (*.pdf)",
	 * )
	 */
	private $brochure;
	
	/**
	 * @Assert\File(
	 *	mimeTypes = {"application/pdf", "application/zip", "application/x-rar-compressed"},
	 *  mimeTypesMessage = "Please upload a valid attachment (*.pdf, *.zip, *.rar)",
	 * )
	 */
	private $attachment;
	
	public function __construct(ServiceVO $serviceVo, $fileList)
	{
		//var_dump(ini_get("upload_max_filesize"), ini_get("post_max_size"), $fileList);
		$this->name 				= $serviceVo->getName();
		$this->description			= $serviceVo->getDescription();
		
		$this->thumbnailImage	= isset($fileList[Constants::THUMBNAIL_IMAGE])	? $fileList[Constants::THUMBNAIL_IMAGE] : null;
		$this->mainImage		= isset($fileList[Constants::MAIN_IMAGE]) 		? $fileList[Constants::MAIN_IMAGE] 		: null;
		$this->image1			= isset($fileList[Constants::IMAGE1]) 			? $fileList[Constants::IMAGE1] 			: null;
		$this->image2			= isset($fileList[Constants::IMAGE2]) 			? $fileList[Constants::IMAGE2] 			: null;
		$this->image3			= isset($fileList[Constants::IMAGE3]) 			? $fileList[Constants::IMAGE3] 			: null;
		$this->brochure			= isset($fileList[Constants::BROCHURE]) 		? $fileList[Constants::BROCHURE] 		: null;
		$this->attachment		= isset($fileList[Constants::ATTACHMENT]) 		? $fileList[Constants::ATTACHMENT] 		: null;
	}
}