<?php
namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\VO\EventVO;
use AppBundle\Common\Constants;

class EventValidator
{
	/**
	 * @Assert\NotBlank(
	 * 	message = "Event title cannot be blank."
	 * )
	 */
	private $title;

	/**
	 * @Assert\NotBlank(
	 * 	message = "Event start date cannot be blank."
	 * )
 	 * @Assert\Date(
	 * 	message = "Event start date is not correct date."
 	 * )
	 */
	private $startDate;
	
	/**
	 * @Assert\NotBlank(
	 * 	message = "Event end date cannot be blank."
	 * )
 	 * @Assert\Date(
	 * 	message = "Event end date is not correct date."
 	 * )
	 */
	private $endDate;
	
	/**
	 * @Assert\NotBlank(
	 * 	message = "Event content cannot be blank."
	 * )
	 */
	private $content;
	
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
	 *	mimeTypes = {"application/pdf", "application/zip", "application/x-rar-compressed"},
	 *  mimeTypesMessage = "Please upload a valid attachment (*.pdf, *.zip, *.rar)",
	 * )
	 */
	private $attachment;
	
	public function __construct(EventVO $eventVo, $fileList)
	{
		//var_dump(ini_get("upload_max_filesize"), ini_get("post_max_size"), $fileList);
		$this->title 			= $eventVo->getTitle();
		$this->content			= $eventVo->getContent();
		$this->startDate		= $eventVo->getStartDate();
		$this->endDate			= $eventVo->getEndDate();
		
		$this->thumbnailImage	= isset($fileList[Constants::THUMBNAIL_IMAGE])	? $fileList[Constants::THUMBNAIL_IMAGE] : null;
		$this->mainImage		= isset($fileList[Constants::MAIN_IMAGE]) 		? $fileList[Constants::MAIN_IMAGE] 		: null;
		$this->attachment		= isset($fileList[Constants::ATTACHMENT]) 		? $fileList[Constants::ATTACHMENT] 		: null;
	}
}