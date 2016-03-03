<?php
namespace AppBundle\Validator;

use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\VO\UserVO;
use AppBundle\Common\Constants;
 
class UserValidator
{
	/**
	 * @Assert\NotBlank(
	 * 	message = "Username cannot be blank."
	 * )
	 */
	private $username;

	/**
	 * @Assert\NotBlank(
	 * 	message = "Password cannot be blank."
	 * )
	 */
	private $password;
	
	/**
	 * @Assert\NotBlank(
	 * 	message = "Old Password cannot be blank."
	 * )
	 */
	private $oldPassword;
	
	/**
	 * @Assert\Email(
	 * 	message = "The email you've entered is not a valid email."
	 * )
	 */
	private $email;
	
	/**
	 * @Assert\File(
	 *	mimeTypes = {"image/jpeg", "image/pjpeg", "image/png", "image/gif"},
	 *  mimeTypesMessage = "Please upload a valid Image (*.jpg, *.jpeg, *.png, *.gif)"
	 * )
	 */
	private $thumbnailImage;
	
	/**
	 * @Assert\IsTrue(message = "Old password does not match.")
	 */
	public function isOldPassword()
	{
		return $this->dbPassword == $this->oldPassword;
	}
	
	private $userService;	
	private $dbPassword;
	
	public function __construct(UserVO $userVo, $fileList, $userService)
	{
		$this->userService	= $userService;
		
		$r = $this->userService->getByUsername($userVo);
		$dbPassword = "";
		if($r){
			$dbPassword = $r["password"];
		}
		$this->dbPassword 	= $dbPassword;
		$this->oldPassword	= $userVo->getOldPassword();
		$this->username		= $userVo->getUsername();
		$this->password		= $userVo->getPassword();
		$this->email		= $userVo->getEmail();
		
		$this->thumbnailImage	= isset($fileList[Constants::THUMBNAIL_IMAGE])	? $fileList[Constants::THUMBNAIL_IMAGE] : null;
	}
}