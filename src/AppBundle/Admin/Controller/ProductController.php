<?php

namespace AppBundle\Admin\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\VO\ProductVO;
use AppBundle\VO\FileVO;
use AppBundle\Common\Constants;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Validator\ProductValidator;
use AppBundle\Admin\Listener\IAuthenticationListener;
use AppBundle\Validator\FileValidator;
use Symfony\Component\HttpFoundation\FileBag;
use AppBundle\Common\VOHelper;

class ProductController extends Controller implements IAuthenticationListener
{
	/**
	 * @Route("/admin/product/list", name="adminProductList")
	 */
	public function listAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		/* Create object for search criteria based on parameters */
		$productVo	= new ProductVO();
		$productVo->setSearchCondition($a["c"]);
		$productVo->setSearchKeyword($a["k"]);
		$productVo->setPageSize(Constants::PAGE_SIZE_VAL);
		$productVo->setStartRow(($a["p"] - 1) * $productVo->getPageSize());
		
		/* Counting rows for pagination, outputing rows only within criteria */
		$totalRows 	= $this->get("my.admin.productService")->countList($productVo);		
		$result 	= $this->get("my.admin.productService")->getList($productVo);
		
		/* Build pagination */
		$paging = $this->get("my.admin.pagingService")->buildPage($totalRows, $productVo->getPageSize(), $a["p"], "", $a["param"]);
		
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Product Management",
				Constants::CONTENT_MENU 		=> Constants::PRODUCT,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::PRODUCT				=> $result,
				Constants::CONTENT_PAGING		=> array(
													Constants::CONTENT_PAGING				=> $paging, 
													Constants::CURRENT						=> $a["p"],
													Constants::PAGE_SIZE					=> $productVo->getPageSize(), 
													Constants::TOTAL_ROWS					=> $totalRows
												)						
		);
		
		return $this->render("admin/product/productList.html.twig", $content);
	}

	/**
	 * @Route("/admin/product/detail", name="adminProductDetail")
	 */
	public function detailAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		$productVo	= new ProductVO();
		$productVo->setSeq($request->get("seq"));
		
		$result 	= $this->get("my.admin.productService")->getById($productVo);
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Product Management",
				Constants::CONTENT_MENU 		=> Constants::PRODUCT,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::PRODUCT				=> $result,				
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])
		);
		
		return $this->render("admin/product/productDetail.html.twig", $content);
	}
	
	/**
	 * @Route("/admin/product/write", name="adminProductWrite")
	 */
	public function writeAction(Request $request)
	{
		/* Check if it is from submitted form */
		$errors 	= "";
		$product	= "";
		if($request->isMethod("POST")){
			$a	= $this->buildPaginationUrl($request);
				
			$product		= $this->prepareVo($request);
			$categories		= $request->get("categories");
			$tags			= $request->get("tags");			
			$fileList		= $request->files->all();
			
			$errors		= $this->validate($product, $fileList);
			
			if(count($errors) == 0) {
				$r = $this->get("my.admin.productService")->insert($product, $fileList, $categories, $tags);
				if($r){
					return $this->redirect(Constants::WEBROOT."admin/product/list", 301);
				}
			}
		}else{
			$a	= $this->buildPaginationUrl($request);
		}
		
		$cat	= $this->get("my.admin.termService")->getCategories();
		$tag	= $this->get("my.admin.termService")->getTags();
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Product Management",
				Constants::CONTENT_MENU 		=> Constants::PRODUCT,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::PRODUCT				=> $product,
				Constants::CATEGORY				=> $cat,
				Constants::TAG					=> $tag,
				Constants::ERRORS				=> $errors,
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])	
		);
	
		return $this->render("admin/product/productForm.html.twig", $content);
	}
	
	/**
	 * @Route("/admin/product/edit", name="adminProductEdit")
	 */
	public function editAction(Request $request)
	{	
		$productSeq		= $request->get("seq");		
		
		$productVo	= new ProductVO();
		$productVo->setSeq($productSeq);
		
		/* Check if it is from submitted form */
		$errors 	= "";
		$product	= "";
		
		if($request->isMethod("POST")){
			$a	= $this->buildPaginationUrl($request);
				
			$product		= $this->prepareVo($request);
			$deletedFiles 	= $request->get("deletedFiles");
			$categories		= $request->get("categories");
			$tags			= $request->get("tags");			
			$fileList		= $request->files->all();
			
			$errors		= $this->validate($product, $fileList);
			
			if(count($errors) == 0) {
				$r = $this->deleteFile($deletedFiles, $productVo->getSeq());
				$r = $this->get("my.admin.productService")->update($product, $fileList, $categories, $tags);
				if($r){
					return $this->redirect(Constants::WEBROOT."admin/product/detail?seq=".$productVo->getSeq()."&".Constants::PARAMETER_PAGE."=".$a["p"].$a["param"], 301);
				}
			}
			
		}else{
			$a	= $this->buildPaginationUrl($request);				
			$product 	= $this->get("my.admin.productService")->getById($productVo);
		}		
	
		$cat	= $this->get("my.admin.termService")->getCategories();		
		$tag	= $this->get("my.admin.termService")->getTags();
		
		$content = array(
				Constants::CONTENT_PAGE_TITLE 	=> "Product Management",
				Constants::CONTENT_MENU 		=> Constants::PRODUCT,
				Constants::CONTENT_PARAM		=> array(
													Constants::PARAMETER_CONDITION			=> $a["c"], 
													Constants::PARAMETER_KEYWORD			=> $a["k"], 
													Constants::PARAMETER 					=> Constants::PARAMETER_PAGE."=".$a["p"].$a["param"]
												),
				Constants::PRODUCT				=> $product,
				Constants::CATEGORY				=> $cat,
				Constants::TAG					=> $tag,
				Constants::ERRORS				=> $errors,
				Constants::CONTENT_PAGING		=> array(Constants::CURRENT	=> $a["p"])
				
		);
	
		return $this->render("admin/product/productForm.html.twig", $content);
	}	

	/**
	 * @Route("/admin/product/delete", name="adminProductDelete")
	 */
	public function deleteAction(Request $request)
	{
		$a	= $this->buildPaginationUrl($request);
		
		$productVo	= $this->prepareVo($request);
		$result = $this->get("my.admin.productService")->delete($productVo);
	
		return $this->redirect(Constants::WEBROOT."admin/product/list?".Constants::PARAMETER_PAGE."=".$a["p"].$a["param"], 301);
	}
	
	private function buildPaginationUrl(Request $request)
	{
		$p = $request->get(Constants::PARAMETER_PAGE) ? $request->get(Constants::PARAMETER_PAGE) : 1;
		$c = $request->get(Constants::PARAMETER_CONDITION);
		$k = $request->get(Constants::PARAMETER_KEYWORD);
		
		$parameter 	= "&".Constants::PARAMETER_CONDITION."=".$c."&".Constants::PARAMETER_KEYWORD."=".$k;
		
		$a = array("p" => $p, "c" => $c, "k" => $k, "param" => $parameter); 
		
		return $a;
	}
	
	private function prepareVo(Request $request)
	{
		$productVo	= new ProductVO();
		$productVo->setSeq($request->request->get("seq"));
		$productVo->setName($request->request->get("name"));
		$productVo->setDescription($request->request->get("description"));
		
		$productVo->setRegIp($_SERVER["REMOTE_ADDR"]);
		$productVo->setModIp($_SERVER["REMOTE_ADDR"]);
		$productVo->setRegId($this->get("session")->get("userSession")["username"]);
		$productVo->setModId($this->get("session")->get("userSession")["username"]);
		
		return $productVo;		
	}
	
	private function validate(ProductVO $productVo, $fileList)
	{
		$validator 	= $this->get("validator");
		$productValidator = new ProductValidator($productVo, $fileList);		
		$errors	= $validator->validate($productValidator, null, array("Default"));	
		
		return $errors;
	}
	
	private function deleteFile($deletedFiles, $ownerSeq)
	{
		/* Delete file if told so */
		if($deletedFiles != ""){
			$arrDeletedFiles = split(",", $deletedFiles);
			foreach ($arrDeletedFiles as $deletedSeq){		
				$fileVo 	= VOHelper::prepareFileVo($_SERVER["REMOTE_ADDR"], $this->get("session")->get("userSession")["username"]);
				$fileVo->setSeq($deletedSeq);
				$fileVo->setOwnerSeq($ownerSeq);
				
				$this->get("my.common.fileService")->delete($fileVo);
			}
		}
	}

	
}
